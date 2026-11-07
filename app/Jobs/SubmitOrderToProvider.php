<?php

namespace App\Jobs;

use App\Enums\DigiflazzStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Mail\TopupFailed;
use App\Models\Order\Order;
use App\Services\ProductProviderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubmitOrderToProvider implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60, 120, 300];

    public function __construct(public readonly int $orderId) {}

    /**
     * Submit a paid order to the upstream supplier.
     */
    public function handle(ProductProviderService $providers): void
    {
        $order = Order::with('product')->find($this->orderId);

        if (! $order || $order->payment_status !== PaymentStatusEnum::SETTLEMENT
            || ! in_array($order->provider ?? $order->product?->provider, ['digiflazz', 'h2h'], true)) {
            return;
        }

        // Already fulfilled - never call the supplier twice.
        if (in_array($order->topup_status, [DigiflazzStatusEnum::SUCCESS, DigiflazzStatusEnum::FAILED], true)) {
            return;
        }

        Cache::lock('submit-order:'.$order->id, 30)->block(5, function () use ($order, $providers) {
            $order->refresh();
            if (! in_array($order->topup_status, [DigiflazzStatusEnum::SUCCESS, DigiflazzStatusEnum::FAILED], true)) {
                $providers->submit($order);
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $e): void
    {
        $order = Order::with('product')->find($this->orderId);

        if (! $order || $order->topup_status === DigiflazzStatusEnum::SUCCESS) {
            return;
        }

        if (($order->provider ?? $order->product?->provider) === 'h2h') {
            $order->update(['topup_status' => DigiflazzStatusEnum::ON_PROGRESS]);
            Log::warning('H2H submission outcome requires reconciliation', ['reference' => $order->reference]);

            return;
        }

        $order->update([
            'topup_status' => DigiflazzStatusEnum::FAILED,
        ]);

        Log::error('Failed to submit order to provider', [
            'reference' => $order->reference,
            'error' => $e?->getMessage(),
        ]);

        if ($order->email) {
            Mail::to($order->email)->send(new TopupFailed($order));
        }
    }
}
