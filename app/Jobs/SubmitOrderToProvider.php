<?php

namespace App\Jobs;

use App\Enums\DigiflazzStatusEnum;
use App\Mail\TopupFailed;
use App\Models\Order\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Triyatna\Digiflazz\Digiflazz;

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
    public function handle(): void
    {
        $order = Order::with('product')->find($this->orderId);

        if (! $order || $order->product?->provider !== 'digiflazz') {
            return;
        }

        // Already fulfilled - never call the supplier twice.
        if ($order->topup_status === DigiflazzStatusEnum::SUCCESS) {
            return;
        }

        $accountId = $order->submited['account_id'] ?? '';
        $serverId = $order->submited['server_id'] ?? '';

        Digiflazz::createPrepaidTransaction(
            productCode: $order->product->sku,
            customerNo: $accountId.$serverId,
            refId: $order->reference,
        );
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

        $order->update([
            'topup_status' => DigiflazzStatusEnum::FAILED,
        ]);

        Log::error('Failed to submit order to Digiflazz', [
            'reference' => $order->reference,
            'error' => $e?->getMessage(),
        ]);

        if ($order->email) {
            Mail::to($order->email)->send(new TopupFailed($order));
        }
    }
}
