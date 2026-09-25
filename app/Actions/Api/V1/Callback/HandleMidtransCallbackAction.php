<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\PaymentStatusEnum;
use App\Jobs\SubmitOrderToProvider;
use App\Mail\PaymentFailed;
use App\Mail\PaymentSuccess;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Services\MidtransService;
use App\Services\VodaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HandleMidtransCallbackAction
{
    public function __construct(
        public readonly MidtransService $midtransService,
        public readonly VodaService $vodaService,
    ) {}

    public function handle(array $payload)
    {
        $this->assertPayload($payload);

        // Validate signature key
        if (! $this->midtransService->validateSignature(
            $payload['order_id'],
            $payload['status_code'],
            $payload['gross_amount'],
            $payload['signature_key'],
        )) {
            throw new \Exception('Invalid signature key', 403);
        }

        Log::info('Midtrans Callback Received', [
            'order_id' => $payload['order_id'],
            'transaction_status' => $payload['transaction_status'],
        ]);

        // Get the order ID without the suffix
        $orderId = explode('-', $payload['order_id'])[0];

        $outcome = null;
        $order = null;

        $payment = DB::transaction(function () use ($payload, $orderId, &$outcome, &$order) {
            $payment = Payment::where('order_id', $orderId)->lockForUpdate()->first();

            if (! $payment) {
                throw new \Exception('Transaction not found', 404);
            }

            // Idempotent: a repeated settlement/capture callback is acknowledged
            // without reprocessing, so Midtrans retries never cause double work.
            if ($payment->paid_at) {
                return $payment;
            }

            if ($payment->expired_at && $payment->expired_at->isPast()) {
                throw new \Exception('Transaction already expired', 400);
            }

            $transactionStatus = $payload['transaction_status'];

            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                $payment->update(['paid_at' => now()]);

                $order = $this->resolveOrder($payment);
                $order?->update(['payment_status' => PaymentStatusEnum::SETTLEMENT]);

                $outcome = 'settled';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'], true)) {
                $payment->update(['expired_at' => now()]);

                $order = $this->resolveOrder($payment);
                $order?->update(['payment_status' => PaymentStatusEnum::DENY]);

                $outcome = 'denied';
            }

            return $payment;
        });

        // Side effects run after the transaction has committed.
        if ($order instanceof Order) {
            if ($outcome === 'settled') {
                $this->sendOrderNotification($order, true);

                // Send to the supplier outside the HTTP request so a slow or
                // failing upstream never blocks the callback.
                SubmitOrderToProvider::dispatch($order->id);
            } elseif ($outcome === 'denied') {
                $this->sendOrderNotification($order, false);
            }
        }

        return $payment;
    }

    protected function assertPayload(array $payload): void
    {
        $required = ['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'];

        foreach ($required as $key) {
            if (! array_key_exists($key, $payload)) {
                throw new \Exception('Invalid callback payload', 400);
            }
        }
    }

    protected function resolveOrder(Payment $payment): ?Order
    {
        if ($payment->payable_type !== Order::class) {
            return null;
        }

        return $payment->payable;
    }

    protected function sendOrderNotification(Order $order, bool $isSuccess): void
    {
        if ($isSuccess) {
            $message = getSetting('template_payment_confirmation') ?? '';
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{app_name}', config('app.name'), $message);
            $message = str_replace('{link}', transactionUrl($order), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);
        } else {
            $message = getSetting('template_payment_rejected') ?? '';
            $message = str_replace('{customer_name}', $order->name, $message);
            $message = str_replace('{order_id}', $order->reference, $message);
            $message = str_replace('{app_name}', config('app.name'), $message);
            $message = str_replace('{link}', transactionUrl($order), $message);
            $message = str_replace('{cs_link}', getSetting('cs'), $message);
        }

        // Send message via email
        Mail::to($order->email)->send(
            $isSuccess ? new PaymentSuccess($order) : new PaymentFailed($order)
        );

        $order->notifications()->create([
            'provider' => 'email',
            'title' => 'Payment '.($isSuccess ? 'Confirmed' : 'Rejected'),
            'content' => $message,
            'error' => false,
        ]);
    }
}
