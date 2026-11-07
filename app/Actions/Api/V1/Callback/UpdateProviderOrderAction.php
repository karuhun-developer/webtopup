<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\DigiflazzStatusEnum;
use App\Mail\TopupFailed;
use App\Mail\TopupSuccess;
use App\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateProviderOrderAction
{
    public function handle(int $orderId, string $status, ?string $serial = null): void
    {
        $order = DB::transaction(function () use ($orderId, $status, $serial) {
            $order = Order::lockForUpdate()->findOrFail($orderId);
            if (in_array($order->topup_status, [DigiflazzStatusEnum::SUCCESS, DigiflazzStatusEnum::FAILED], true)) {
                return null;
            }
            $state = match ($status) {
                'success' => DigiflazzStatusEnum::SUCCESS,
                'failed' => DigiflazzStatusEnum::FAILED,
                default => DigiflazzStatusEnum::ON_PROGRESS,
            };
            $order->update(['topup_status' => $state, 'sn' => $serial]);

            return in_array($status, ['success', 'failed'], true) ? $order : null;
        });
        if ($order?->email) {
            Mail::to($order->email)->queue($status === 'success' ? new TopupSuccess($order) : new TopupFailed($order));
            $order->notifications()->create([
                'provider' => 'email', 'title' => $status === 'success' ? 'Order Completed' : 'Order Failed',
                'content' => strtr(getSetting($status === 'success' ? 'template_order_completed' : 'template_order_failed') ?? '', [
                    '{customer_name}' => $order->name, '{order_id}' => $order->reference, '{app_name}' => config('app.name'),
                    '{link}' => transactionUrl($order), '{cs_link}' => getSetting('cs') ?? '',
                ]), 'error' => false,
            ]);
        }
    }
}
