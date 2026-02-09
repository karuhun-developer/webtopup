<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\PaymentStatusEnum;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Services\MidtransService;
use App\Services\VodaService;
use Illuminate\Support\Facades\Log;
use Triyatna\Digiflazz\Digiflazz;

class HandleMidtransCallbackAction
{
    public function __construct(
        public readonly MidtransService $midtransService,
        public readonly VodaService $vodaService,
    ) {}

    public function handle(array $payload)
    {
        Log::info('Midtrans Callback Received', [
            'request' => $payload,
        ]);

        // Validate signature key
        if (! $this->midtransService->validateSignature(
            $payload['order_id'],
            $payload['status_code'],
            $payload['gross_amount'],
            $payload['signature_key'],
        )) {
            throw new \Exception('Invalid signature key', 403);
        }

        // Get the order ID without the suffix
        $orderId = $payload['order_id'];
        $orderId = explode('-', $orderId)[0];

        $payment = Payment::where('order_id', $orderId)->first();

        if (! $payment) {
            throw new \Exception('Transaction not found', 404);
        }

        if ($payment->paid_at || ($payment->expires_at && $payment->expires_at?->isPast())) {
            throw new \Exception('Transaction already paid or expired', 400);
        }

        $this->handlePaymentStatus($payload['transaction_status'], $payment);

        return $payment;
    }

    protected function handlePaymentStatus($transactionStatus, Payment $payment)
    {
        switch ($transactionStatus) {
            case 'capture':
                $payment->update([
                    'paid_at' => now(),
                ]);
                $this->capture($payment);
                break;
            case 'settlement':
                $payment->update([
                    'paid_at' => now(),
                ]);
                $this->capture($payment);
                break;
            case 'pending':
                break;
            case 'deny':
                $payment->update([
                    'expires_at' => now(),
                ]);
                $this->deny($payment);
                break;
            case 'expire':
                $payment->update([
                    'expires_at' => now(),
                ]);
                $this->deny($payment);
                break;
            case 'cancel':
                $payment->update([
                    'expires_at' => now(),
                ]);
                $this->deny($payment);
                break;
        }
    }

    protected function capture(Payment $payment)
    {
        if ($payment->payable_type === Order::class) {
            $this->handleAfterOrderPaid($payment->payable);
        }
    }

    protected function handleAfterOrderPaid(Order $order)
    {
        $order->update([
            'payment_status' => PaymentStatusEnum::SETTLEMENT,
        ]);

        $this->sendOrderNotification($order, true);
    }

    protected function deny(Payment $payment)
    {
        if ($payment->payable_type === Order::class) {
            $order = $payment->payable;
            $order->update([
                'payment_status' => PaymentStatusEnum::DENY,
            ]);

            $this->sendOrderNotification($order, false);
        }
    }

    protected function sendOrderNotification(Order $order, bool $isSuccess)
    {
        if ($isSuccess) {
            // Proccess Transaction
            $accountId = $order->submited['account_id'] ?? '';
            $serverId = $order->submited['server_id'] ?? '';
            $customer = $accountId.$serverId;

            // If the provider is Digiflazz, create transaction to Digiflazz
            if ($order->brand->provider === 'digiflazz') {
                // Digiflazz::createPrepaidTransaction(
                //     productCode: $order->product->sku,
                //     customerNo: $customer,
                //     refId: $order->reference,
                // );
            }

            // Send notification to user
            $message = getSetting('template_payment_confirmation');
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{app_name}', config('app.name'), $message);
            $message = str_replace('{link}', route('transaction.show', [
                'order' => $order,
            ]), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);

            // Send message via Voda
            $this->vodaService->sendMessage(
                phone: $order->phone,
                message: $message,
                linkPreview: true,
            );
        } else {
            // Send notification to user
            $message = getSetting('template_payment_rejected');
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{app_name}', config('app.name'), $message);
            $message = str_replace('{link}', route('transaction.show', [
                'order' => $order,
            ]), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);

            // Send message via Voda
            $this->vodaService->sendMessage(
                phone: $order->phone,
                message: $message,
                linkPreview: true,
            );
        }
    }
}
