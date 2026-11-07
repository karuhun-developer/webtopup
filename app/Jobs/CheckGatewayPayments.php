<?php

namespace App\Jobs;

use App\Actions\Main\CheckPaymentStatusAction;
use App\Enums\PaymentStatusEnum;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckGatewayPayments implements ShouldQueue
{
    use Queueable;

    public function handle(CheckPaymentStatusAction $check): void
    {
        Payment::whereIn('driver', ['midtrans', 'karuhun_qris', 'paywuz'])->whereNull('paid_at')
            ->whereHasMorph('payable', [Order::class], fn ($query) => $query->where('payment_status', PaymentStatusEnum::PENDING))
            ->chunkById(100, function ($payments) use ($check) {
                foreach ($payments as $payment) {
                    try {
                        $check->handle($payment);
                    } catch (Throwable $e) {
                        Log::warning('Gateway status check failed', ['payment_id' => $payment->id]);
                    }
                }
            });
    }

    public function failed(?Throwable $e): void
    {
        Log::error('Gateway payment polling failed', ['error' => $e?->getMessage()]);
    }
}
