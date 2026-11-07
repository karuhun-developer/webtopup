<?php

namespace App\Services;

use App\Models\Payment\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class PaymentGatewayService
{
    public function __construct(public readonly IntegrationSettings $settings, public readonly MidtransService $midtrans) {}

    public function driver(): string
    {
        return $this->settings->value('payment_gateway');
    }

    public function client(string $driver): PendingRequest
    {
        $key = $this->settings->credential($driver === 'karuhun_qris' ? 'qris_api_key' : 'paywuz_api_key');
        if ($key === '') {
            throw new RuntimeException('API key payment gateway belum diatur di CMS.');
        }
        $client = Http::baseUrl(rtrim($this->settings->value($driver === 'karuhun_qris' ? 'qris_base_url' : 'paywuz_base_url'), '/'))
            ->acceptJson()->connectTimeout(5)->timeout(15);

        return $driver === 'karuhun_qris' ? $client->withHeaders(['x-api-key' => $key]) : $client->withToken($key);
    }

    public function methods(): array
    {
        $driver = $this->driver();
        if ($driver === 'karuhun_qris') {
            return [['id' => 'qris', 'name' => 'QRIS', 'fee' => 0, 'percent' => 0, 'action' => 'add', 'img' => '/images/QRIS.svg']];
        }
        if ($driver === 'paywuz') {
            try {
                $key = 'paywuz:methods:'.hash('sha256', $this->settings->credential('paywuz_api_key'));

                return Cache::remember($key, 300, function () {
                    $data = $this->client('paywuz')->get('/v1/payment-methods')->throw()->json('data');

                    return collect($data)->filter(fn ($method) => in_array($method['type'], ['qris', 'virtual_account'], true))
                        ->map(fn ($method) => [
                            'id' => $method['code'], 'name' => $method['name'],
                            'fee' => $method['fee']['flatIdr'], 'percent' => $method['fee']['percentBps'] / 10000,
                            'action' => 'add', 'img' => $method['type'] === 'qris' ? '/images/QRIS.svg' : '/images/BCA.svg',
                            'type' => $method['type'], 'min' => $method['limits']['minIdr'], 'max' => $method['limits']['maxIdr'],
                        ])->values()->all();
                });
            } catch (Throwable $e) {
                report($e);

                return [];
            }
        }
        if ($driver !== 'midtrans') {
            return [];
        }

        return collect(['qris', 'bca', 'bni', 'bri'])->map(fn ($id) => [
            'id' => $id, 'name' => $id === 'qris' ? 'QRIS' : 'Virtual Account '.strtoupper($id),
            'fee' => $id === 'qris' ? 0 : 4000, 'percent' => $id === 'qris' ? 0.007 : 0,
            'action' => 'add', 'img' => '/images/'.strtoupper($id).'.svg',
        ])->all();
    }

    public function fee(string $method, int $amount): int
    {
        $option = collect($this->methods())->firstWhere('id', $method);
        if (! $option) {
            throw new RuntimeException('Metode pembayaran tidak tersedia pada gateway yang dipilih.');
        }
        if ($amount < ($option['min'] ?? 1) || $amount > ($option['max'] ?? PHP_INT_MAX)) {
            throw new RuntimeException('Nominal di luar batas metode pembayaran.');
        }

        return (int) ceil($option['fee'] + $amount * $option['percent']);
    }

    public function create(Payment $payment): void
    {
        Cache::lock('create-payment:'.$payment->id, 60)->block(5, function () use ($payment) {
            $payment->refresh();
            if (! $payment->transaction_id && ! $payment->paid_at) {
                $this->createTransaction($payment);
            }
        });
    }

    private function createTransaction(Payment $payment): void
    {
        if ($payment->driver === 'midtrans') {
            $data = $payment->channel === 'qris'
                ? $this->midtrans->createQris($payment->order_id, $payment->amount)
                : $this->midtrans->createBankTransfer($payment->order_id, $payment->amount, $payment->channel);
            if (! $data['successful'] || empty($data['transaction_id']) || empty($data['account'])) {
                throw new RuntimeException('Gagal membuat pembayaran Midtrans.');
            }
            $payment->update([
                'transaction_id' => $data['transaction_id'], 'account_number' => $data['account'],
                'account_code' => $data['code'] ?? null,
            ]);

            return;
        }
        if ($payment->driver === 'karuhun_qris') {
            $params = $payment->gateway_payload;
            if (! $params) {
                $params = ['amount' => (int) $payment->amount];
                if ($selector = $this->settings->value('qris_provider')) {
                    $params['provider'] = $selector;
                }
                $payment->update(['gateway_payload' => $params]);
            }
            $response = $this->client('karuhun_qris')->withHeaders(['Idempotency-Key' => $payment->order_id])
                ->post('/v1/qris/create', $params)->throw();
            $data = $response->json('data');
            if (! $response->json('success') || empty($data['id']) || empty($data['token']) || empty($data['qris_url']) || empty($data['expires_at']) || ! is_numeric($data['amount'] ?? null)) {
                throw new RuntimeException('Respons QRIS Karuhun tidak valid.');
            }
            $unique = (int) $data['amount'] - (int) $payment->amount;
            if ($unique < 0) {
                throw new RuntimeException('Nominal QRIS Karuhun tidak valid.');
            }
            DB::transaction(function () use ($payment, $data, $unique) {
                $payment->update([
                    'transaction_id' => $data['id'], 'gateway_token' => $data['token'],
                    'account_number' => $data['qris_url'], 'expired_at' => $data['expires_at'],
                    'unique_amount' => $unique, 'amount' => $data['amount'],
                ]);
                $payment->payable->increment('total_amount', $unique);
            });

            return;
        }
        if ($payment->driver !== 'paywuz') {
            throw new RuntimeException('Payment gateway tidak valid.');
        }
        $order = $payment->payable;
        $params = $payment->gateway_payload ?? [
            'orderId' => $payment->order_id,
            'amount' => (int) ($payment->amount - $order->fee),
            'paymentMethod' => $payment->channel, 'feeByMerchant' => false,
            'redirectUrl' => transactionUrl($order),
        ];
        $payment->update(['gateway_payload' => $params]);
        $data = $this->client('paywuz')->post('/v1/transactions', $params)->throw()->json('data');
        if (empty($data['id']) || empty($data['paymentUrl']) || empty($data['expiresAt']) || ! is_numeric($data['totalPayment'] ?? null) || $data['totalPayment'] < $params['amount']) {
            throw new RuntimeException('Respons Paywuz tidak valid.');
        }
        DB::transaction(function () use ($payment, $order, $data) {
            $payment->update([
                'transaction_id' => $data['id'], 'payment_url' => $data['paymentUrl'],
                'account_number' => $payment->payment_type === 'qris' ? '' : ($data['paymentNumber'] ?? ''), 'amount' => $data['totalPayment'],
                'expired_at' => $data['expiresAt'],
            ]);
            $order->update(['total_amount' => $payment->amount, 'fee' => $payment->amount - $order->amount + $order->discount_amount]);
        });
    }

    public function check(Payment $payment): ?array
    {
        if ($payment->driver === 'karuhun_qris' && $payment->gateway_token) {
            $data = $this->client('karuhun_qris')->post('/v1/payment/check', [
                'token' => $payment->gateway_token, 'wait' => false,
            ])->throw()->json();
            if (($data['success'] ?? null) !== true || ! is_bool($data['paid'] ?? null)) {
                throw new RuntimeException('Respons status QRIS Karuhun tidak valid.');
            }
            if ($data['paid'] && (($data['data']['token'] ?? null) !== $payment->gateway_token
                || (int) ($data['data']['amount'] ?? 0) !== (int) $payment->amount)) {
                throw new RuntimeException('Pembayaran QRIS Karuhun tidak sesuai.');
            }

            return ['status' => $data['paid'] ? 'settled' : 'pending', 'amount' => (int) $payment->amount];
        } elseif ($payment->driver === 'paywuz') {
            $data = $this->client('paywuz')->get('/v1/transactions/'.rawurlencode($payment->order_id))->throw()->json('data');
            if (($data['id'] ?? null) !== $payment->transaction_id || ($data['orderId'] ?? null) !== $payment->order_id
                || ! is_numeric($data['totalPayment'] ?? null)
                || ! in_array($data['status'] ?? null, ['pending', 'settlement', 'success', 'failed', 'cancelled'], true)) {
                throw new RuntimeException('Respons status Paywuz tidak valid.');
            }

            return [
                'status' => match ($data['status']) {
                    'success' => 'settled', 'failed', 'cancelled' => 'denied', default => 'pending'
                },
                'amount' => (int) $data['totalPayment'],
            ];
        }

        return null;
    }
}
