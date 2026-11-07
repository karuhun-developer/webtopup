<?php

namespace App\Jobs;

use App\Enums\DigiflazzStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckProviderOrders implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Order::where('provider', 'h2h')->where('payment_status', PaymentStatusEnum::SETTLEMENT)
            ->whereIn('topup_status', [DigiflazzStatusEnum::PENDING, DigiflazzStatusEnum::ON_PROGRESS])
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    SubmitOrderToProvider::dispatch($order->id);
                }
            });
    }

    public function failed(?Throwable $e): void
    {
        Log::error('Provider status polling failed', ['error' => $e?->getMessage()]);
    }
}
