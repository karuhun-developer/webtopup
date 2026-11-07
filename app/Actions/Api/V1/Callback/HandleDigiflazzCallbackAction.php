<?php

namespace App\Actions\Api\V1\Callback;

use App\Enums\PaymentStatusEnum;
use App\Models\Order\Order;
use App\Services\IntegrationSettings;
use RuntimeException;
use Triyatna\Digiflazz\Helpers\Webhook;

class HandleDigiflazzCallbackAction
{
    public function __construct(public readonly IntegrationSettings $settings, public readonly UpdateProviderOrderAction $update) {}

    public function handle(?string $signature, string $payload, array|string $data): void
    {
        if (! is_array($data)) {
            $data = json_decode($data, true) ?? [];
        }
        if (! is_array($data) || empty($data['ref_id']) || empty($data['status'])) {
            throw new RuntimeException('Invalid callback payload');
        }
        $secret = $this->settings->credential('digiflazz_webhook_secret');
        if ($secret === '' || ! Webhook::validate($signature ?? '', $payload, $secret)) {
            throw new RuntimeException('Invalid signature');
        }
        $order = Order::where('reference', $data['ref_id'])
            ->fromProviders(['digiflazz'])->where('payment_status', PaymentStatusEnum::SETTLEMENT)->firstOrFail();
        $status = match (true) {
            $data['status'] === 'Sukses' && ($data['rc'] ?? '') === '00' => 'success',
            $data['status'] === 'Pending' || in_array($data['rc'] ?? '', ['03', '99'], true) => 'pending',
            default => 'failed',
        };
        $this->update->handle($order->id, $status, $data['sn'] ?? null);
    }
}
