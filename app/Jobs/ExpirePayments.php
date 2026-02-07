<?php

namespace App\Jobs;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpirePayments implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Payment::query()
            ->where('expired_at', '<=', now())
            ->whereHasMorph('payable', '*', function ($query) {
                $query->where('payment_status', PaymentStatusEnum::PENDING);
            })
            ->with('payable')
            ->chunkById(100, function ($payments) {
                foreach ($payments as $payment) {
                    if ($payment->payable && $payment->payable->payment_status === PaymentStatusEnum::PENDING) {
                        $payment->payable->update([
                            'payment_status' => PaymentStatusEnum::EXPIRED,
                        ]);
                    }
                }
            });
    }
}
