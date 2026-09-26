<?php

use App\Enums\PaymentStatusEnum;
use App\Jobs\SubmitOrderToProvider;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config([
        'inertia.ssr.enabled' => false,
        'midtrans.server_key' => 'test-server-key',
    ]);

    $this->seed(SettingSeeder::class);

    Queue::fake();
    Mail::fake();
});

function midtransSignature(string $orderId, string $statusCode, string $grossAmount, string $serverKey): string
{
    return hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
}

it('settles a paid order idempotently and submits to the supplier once', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'midtrans']]);
    $payment = $order->payment;

    $payload = [
        'order_id' => $payment->order_id,
        'status_code' => '200',
        'gross_amount' => (string) $payment->amount,
        'transaction_status' => 'settlement',
        'signature_key' => midtransSignature(
            $payment->order_id,
            '200',
            (string) $payment->amount,
            'test-server-key',
        ),
    ];

    $this->postJson(route('api.v1.midtrans.callback'), $payload)->assertOk();
    // Midtrans retries the same notification - it must be a no-op.
    $this->postJson(route('api.v1.midtrans.callback'), $payload)->assertOk();

    expect($order->fresh()->payment_status)->toBe(PaymentStatusEnum::SETTLEMENT)
        ->and($payment->fresh()->paid_at)->not->toBeNull();

    Queue::assertPushed(SubmitOrderToProvider::class, 1);
});

it('rejects a midtrans callback with an invalid signature', function () {
    $order = makeTestOrder(['payment' => ['driver' => 'midtrans']]);
    $payment = $order->payment;

    $this->postJson(route('api.v1.midtrans.callback'), [
        'order_id' => $payment->order_id,
        'status_code' => '200',
        'gross_amount' => (string) $payment->amount,
        'transaction_status' => 'settlement',
        'signature_key' => 'invalid',
    ])->assertStatus(400);

    expect($payment->fresh()->paid_at)->toBeNull();
    Queue::assertNothingPushed();
});

it('does not crash on a digiflazz callback with an array payload', function () {
    $order = makeTestOrder();

    $response = $this->postJson(route('api.v1.digiflazz.callback'), [
        'data' => [
            'ref_id' => $order->reference,
            'status' => 'Sukses',
            'rc' => '00',
            'sn' => 'SN-123',
        ],
    ]);

    expect($response->status())->not->toBe(500);
});

it('does not crash on a digiflazz callback with a json string payload', function () {
    $order = makeTestOrder();

    $response = $this->postJson(route('api.v1.digiflazz.callback'), [
        'data' => json_encode([
            'ref_id' => $order->reference,
            'status' => 'Gagal',
            'rc' => '01',
        ]),
    ]);

    expect($response->status())->not->toBe(500);
});
