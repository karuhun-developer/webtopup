<?php

namespace App\Actions\Cms\Order\Order;

use App\Enums\PaymentStatusEnum;
use App\Models\Order\Order;
use App\Services\VodaService;
use Illuminate\Support\Facades\Log;
use Triyatna\Digiflazz\Digiflazz;

class ValidatePaymentAction
{
    public function __construct(
        public readonly VodaService $vodaService,
    ) {}

    /**
     * Handle the action.
     */
    public function handle(Order $order, int $status): void
    {
        $payment = $order->payment;

        if ($payment->paid_at) {
            throw new \Exception('Payment already validated');
        }

        $isApproved = $status === PaymentStatusEnum::SETTLEMENT->value;

        if ($isApproved) {
            // Approve payment
            $payment->update([
                'paid_at' => now(),
            ]);

            $order->update([
                'payment_status' => PaymentStatusEnum::SETTLEMENT,
            ]);

            // Proccess Transaction
            $accountId = $order->submited['account_id'] ?? '';
            $serverId = $order->submited['server_id'] ?? '';
            $customer = $accountId.$serverId;

            // If the provider is Digiflazz, create transaction to Digiflazz
            if ($order->product->provider === 'digiflazz') {
                // Digiflazz::createPrepaidTransaction(
                //     productCode: $order->product->sku,
                //     customerNo: $customer,
                //     refId: $order->reference,
                // );
            }

            // Send success notification
            $this->sendNotification($order, true);
        } else {
            // Reject payment
            $order->update([
                'payment_status' => $status,
            ]);

            // Send rejection notification
            $this->sendNotification($order, false);
        }

        // Log activity
        activity()
            ->performedOn($order)
            ->withProperties([
                'status' => $status,
            ])
            ->log('Payment '.($isApproved ? 'approved' : 'rejected'));
    }

    protected function sendNotification(Order $order, bool $isSuccess): void
    {
        if ($isSuccess) {
            $message = getSetting('template_payment_confirmation');
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{app_name}', config('app.name'), $message);
            $message = str_replace('{link}', route('transaction.show', [
                'order' => $order,
            ]), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);
        } else {
            $message = getSetting('template_payment_rejected');
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{link}', route('transaction.show', [
                'order' => $order,
            ]), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);
        }

        // Send message via Voda
        $isNotificationError = false;
        try {
            $this->vodaService->sendMessage(
                phone: $order->phone,
                message: $message,
                linkPreview: true,
            );
        } catch (\Exception $e) {
            Log::error('Failed to send Voda message: '.$e->getMessage());
            $isNotificationError = true;
        }

        // Create notification record
        $order->notifications()->create([
            'provider' => 'voda',
            'title' => 'Payment '.($isSuccess ? 'Confirmed' : 'Rejected'),
            'content' => $message,
            'error' => $isNotificationError,
        ]);
    }
}
