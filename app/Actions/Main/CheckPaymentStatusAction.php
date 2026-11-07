<?php

namespace App\Actions\Main;

use App\Actions\Api\V1\Callback\SettlePaymentAction;
use App\Enums\PaymentStatusEnum;
use App\Jobs\CreateGatewayPayment;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckPaymentStatusAction
{
    public function __construct(public readonly PaymentGatewayService $gateways, public readonly SettlePaymentAction $settle) {}

    public function handle(Payment $payment): void
    {
        if ($payment->paid_at || ! in_array($payment->driver, ['midtrans', 'karuhun_qris', 'paywuz'], true)) {
            return;
        }

        Cache::lock('check-payment:'.$payment->id, 30)->get(function () use ($payment) {
            $payment->refresh()->load('payable');
            if ($payment->paid_at || ! $payment->payable instanceof Order
                || $payment->payable->payment_status !== PaymentStatusEnum::PENDING) {
                return;
            }
            if (! $payment->transaction_id && ! $payment->gateway_token) {
                if ($payment->expired_at?->isFuture()) {
                    CreateGatewayPayment::dispatch($payment->id);
                } else {
                    $this->expireIfDue($payment);
                }

                return;
            }
            if ($payment->driver === 'midtrans') {
                $this->expireIfDue($payment);

                return;
            }
            if (! Cache::add('payment-last-check:'.$payment->id, true, 5)) {
                return;
            }

            try {
                if ($result = $this->gateways->check($payment)) {
                    $this->settle->handle($payment->driver, $payment->order_id, $result['status'], $result['amount']);
                }
                $this->expireIfDue($payment);
            } catch (Throwable $e) {
                Log::warning('Gateway status check failed', ['payment_id' => $payment->id]);
            }
        });
    }

    private function expireIfDue(Payment $payment): void
    {
        if ($payment->expired_at?->isPast()) {
            $payment->payable->newQuery()->whereKey($payment->payable_id)
                ->where('payment_status', PaymentStatusEnum::PENDING)
                ->update(['payment_status' => PaymentStatusEnum::EXPIRED]);
        }
    }
}
