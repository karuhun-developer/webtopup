<?php

namespace App\Services;

use App\Actions\Api\V1\Callback\UpdateProviderOrderAction;
use App\Models\Order\Order;
use RuntimeException;
use Triyatna\Digiflazz\Services\DigiflazzClient;

class ProductProviderService
{
    public function __construct(public readonly IntegrationSettings $settings, public readonly H2hService $h2h) {}

    public function digiflazz(): DigiflazzClient
    {
        return new DigiflazzClient($this->settings->credential('digiflazz_username'), $this->settings->credential('digiflazz_api_key'));
    }

    public function products(string $provider, bool $refresh = false): array
    {
        if ($provider === 'h2h') {
            return $this->h2h->products($refresh);
        }
        if ($provider !== 'digiflazz') {
            throw new RuntimeException('Provider produk tidak valid.');
        }
        $response = $this->digiflazz()->getPriceList('prepaid');
        if (! $response->isSuccess()) {
            throw new RuntimeException('Gagal mengambil katalog Digiflazz.');
        }

        return array_map(fn ($product) => [
            'sku' => $product['buyer_sku_code'], 'name' => $product['product_name'],
            'category' => $product['category'], 'brand' => $product['brand'], 'price' => $product['price'],
            'status' => ($product['buyer_product_status'] ?? false) && ($product['seller_product_status'] ?? false),
        ], $response->data());
    }

    /**
     * @return array{products: array, complete: bool, count: int, retry_after: int}
     */
    public function catalog(string $provider): array
    {
        if ($provider === 'h2h') {
            return $this->h2h->catalog();
        }
        $products = $this->products($provider);

        return ['products' => $products, 'complete' => true, 'count' => count($products), 'retry_after' => 0];
    }

    public function submit(Order $order): void
    {
        $provider = $order->provider ?? $order->product?->provider;
        if ($provider === 'digiflazz') {
            $response = $this->digiflazz()->createPrepaidTransaction(
                productCode: $order->provider_payload['sku'] ?? $order->product->sku,
                customerNo: ($order->submited['account_id'] ?? '').($order->submited['server_id'] ?? ''),
                refId: $order->reference,
            );
            if (! $response->transactionCreated()) {
                throw new RuntimeException('Gagal mengirim pesanan ke Digiflazz.');
            }
            app(UpdateProviderOrderAction::class)->handle($order->id,
                $response->isSuccess() ? 'success' : ($response->isPending() ? 'pending' : 'failed'), $response->get('sn'));

            return;
        }
        if ($provider !== 'h2h') {
            return;
        }

        if ($order->provider_order_id) {
            $data = $this->h2h->client()->get('/api/v1/orders/'.$order->provider_order_id)->throw()->json('data');
        } else {
            $payload = $order->provider_payload;
            if (! $payload) {
                $payload = [
                    'productId' => $order->product->provider_product_id,
                    'destination' => (string) ($order->submited['account_id'] ?? ''),
                    'serverId' => (string) ($order->submited['server_id'] ?? ''),
                    'expectedPrice' => (int) $order->product->buy_price,
                    'payment' => 'wallet', 'reference' => $order->reference,
                ];
                $order->update(['provider' => 'h2h', 'provider_payload' => $payload]);
            }
            if (empty($payload['productId'])) {
                throw new RuntimeException('ID produk H2H belum diatur. Import ulang produk dari H2H.');
            }
            $data = $this->h2h->client()->withHeaders(['Idempotency-Key' => $order->reference])
                ->post('/api/v1/orders', $payload)->throw()->json('data.order');
            $order->update(['provider_order_id' => $data['id']]);
        }

        app(UpdateProviderOrderAction::class)->handle($order->id, $data['status'], $data['serial'] ?? null);
    }
}
