<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\PaymentStatusEnum;
use App\Jobs\SubmitOrderToProvider;
use App\Mail\PaymentFailed;
use App\Mail\PaymentSuccess;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SettlePaymentAction
{
    public function handle(string $driver, string $orderId, string $status, int $amount): Payment
    {
        return DB::transaction(function () use ($driver, $orderId, $status, $amount) {
            $payment = Payment::where('driver', $driver)->where('order_id', $orderId)->lockForUpdate()->firstOrFail();
            if ((int) $payment->amount !== $amount) {
                throw new RuntimeException('Nominal pembayaran tidak sesuai.');
            }
            if ($payment->paid_at || $status === 'pending') {
                return $payment;
            }
            $order = $payment->payable;
            if (! $order instanceof Order || $order->payment_status === PaymentStatusEnum::DENY) {
                return $payment;
            }
            $success = $status === 'settled';
            $payment->update($success ? ['paid_at' => now()] : ['expired_at' => now()]);
            $order->update(['payment_status' => $success ? PaymentStatusEnum::SETTLEMENT : PaymentStatusEnum::DENY]);
            if ($success) {
                SubmitOrderToProvider::dispatch($order->id)->afterCommit();
            }
            DB::afterCommit(function () use ($order, $success) {
                if ($order->email) {
                    Mail::to($order->email)->queue($success ? new PaymentSuccess($order) : new PaymentFailed($order));
                    $order->notifications()->create([
                        'provider' => 'email', 'title' => 'Payment '.($success ? 'Confirmed' : 'Rejected'),
                        'content' => strtr(getSetting($success ? 'template_payment_confirmation' : 'template_payment_rejected') ?? '', [
                            '{customer_name}' => $order->name, '{order_id}' => $order->reference, '{app_name}' => config('app.name'),
                            '{link}' => transactionUrl($order), '{cs_link}' => getSetting('cs') ?? '',
                        ]), 'error' => false,
                    ]);
                }
            });

            return $payment;
        });
    }
}
