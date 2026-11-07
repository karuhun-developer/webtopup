<?php

namespace App\Actions\Api\V1\Callback;

use App\Models\Payment\Payment;
use App\Services\IntegrationSettings;
use RuntimeException;

class HandlePaywuzCallbackAction
{
    public function __construct(public readonly IntegrationSettings $settings, public readonly SettlePaymentAction $settle) {}

    public function handle(string $body, string $signature): void
    {
        $key = $this->settings->credential('paywuz_api_key');
        if ($key === '' || ! hash_equals('sha256='.hash_hmac('sha256', $body, $key), $signature)) {
            throw new RuntimeException('Invalid Paywuz signature', 403);
        }
        $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $data = $payload['data'] ?? [];
        if (empty($data['orderId']) || empty($data['id']) || ! is_numeric($data['totalPayment'] ?? null)) {
            throw new RuntimeException('Invalid Paywuz payload', 422);
        }
        Payment::where('driver', 'paywuz')->where('order_id', $data['orderId'])->where('transaction_id', $data['id'])->firstOrFail();
        $status = match ($payload['event'] ?? '') {
            'transaction.paid' => 'settled',
            'transaction.failed', 'transaction.cancelled' => 'denied',
            default => 'pending',
        };
        $this->settle->handle('paywuz', $data['orderId'], $status, (int) $data['totalPayment']);
    }
}
