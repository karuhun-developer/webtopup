<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\PaymentStatusEnum;
use App\Models\Order\Order;
use App\Services\IntegrationSettings;
use RuntimeException;

class HandleH2hCallbackAction
{
    public function __construct(public readonly IntegrationSettings $settings, public readonly UpdateProviderOrderAction $update) {}

    public function handle(string $body, string $timestamp, string $signature, string $eventId): void
    {
        $secret = $this->settings->credential('h2h_webhook_secret');
        if ($secret === '' || ! ctype_digit($timestamp) || abs(now()->timestamp - (int) $timestamp) > 300
            || ! hash_equals(hash_hmac('sha256', $timestamp.'.'.$body, $secret), $signature)) {
            throw new RuntimeException('Invalid H2H signature', 403);
        }
        $event = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        if (empty($eventId) || ($event['eventId'] ?? null) !== $eventId
            || ! in_array($event['type'] ?? null, ['order.success', 'order.failed'], true)
            || ! is_array($event['order'] ?? null) || empty($event['order']['reference']) || empty($event['order']['id'])) {
            throw new RuntimeException('Invalid H2H event', 422);
        }
        $data = $event['order'];
        $order = Order::where('provider', 'h2h')->where('reference', $data['reference'])
            ->where('payment_status', PaymentStatusEnum::SETTLEMENT)->firstOrFail();
        if ($order->provider_order_id && $order->provider_order_id !== $data['id']) {
            throw new RuntimeException('H2H order does not match', 422);
        }
        $order->update(['provider_order_id' => $data['id']]);
        $this->update->handle($order->id, $event['type'] === 'order.success' ? 'success' : 'failed', $data['serial'] ?? null);
    }
}
