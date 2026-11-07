<?php

namespace App\Actions\Api\V1\Callback;

use App\Models\Payment\Payment;
use App\Services\MidtransService;
use RuntimeException;

class HandleMidtransCallbackAction
{
    public function __construct(public readonly MidtransService $midtransService, public readonly SettlePaymentAction $settle) {}

    public function handle(array $payload): Payment
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'] as $key) {
            if (! isset($payload[$key]) || ! is_scalar($payload[$key])) {
                throw new RuntimeException('Invalid callback payload');
            }
        }
        if (! $this->midtransService->validateSignature($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            throw new RuntimeException('Invalid signature key');
        }
        $status = match ($payload['transaction_status']) {
            'settlement' => 'settled',
            'capture' => ($payload['fraud_status'] ?? 'accept') === 'accept' ? 'settled' : 'pending',
            'deny', 'expire', 'cancel' => 'denied',
            default => 'pending',
        };

        return $this->settle->handle('midtrans', explode('-', $payload['order_id'])[0], $status, (int) $payload['gross_amount']);
    }
}
