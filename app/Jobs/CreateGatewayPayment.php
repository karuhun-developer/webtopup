<?php

namespace App\Jobs;

use App\Models\Payment\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateGatewayPayment implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $uniqueFor = 300;

    public array $backoff = [10, 30, 60, 120];

    public function __construct(public readonly int $paymentId) {}

    public function uniqueId(): string
    {
        return (string) $this->paymentId;
    }

    public function handle(PaymentGatewayService $gateway): void
    {
        $payment = Payment::find($this->paymentId);
        if ($payment && ! $payment->paid_at && $payment->expired_at?->isFuture() && $payment->driver !== 'manual') {
            $gateway->create($payment);
        }
    }

    public function failed(?Throwable $e): void
    {
        Log::warning('Payment creation requires reconciliation', ['payment_id' => $this->paymentId]);
    }
}
