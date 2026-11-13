<?php

namespace App\Services;

use App\Exceptions\ProductCatalogPendingException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use RuntimeException;

class H2hService
{
    private const CATALOG_BATCH_SIZE = 10;

    private const CATALOG_REQUESTS_PER_MINUTE = 90;

    public function __construct(public readonly IntegrationSettings $settings) {}

    public function client(): PendingRequest
    {
        $key = $this->settings->credential('h2h_api_key');
        if ($key === '') {
            throw new RuntimeException('API key H2H belum diatur di CMS.');
        }

        return Http::baseUrl(rtrim($this->settings->value('h2h_base_url'), '/'))
            ->withHeaders(['X-API-Key' => $key])->acceptJson()->connectTimeout(5)->timeout(15);
    }

    public function products(bool $refresh = false): array
    {
        $catalog = $this->catalog($refresh);
        if (! $catalog['complete']) {
            throw new ProductCatalogPendingException;
        }

        return $catalog['products'];
    }

    /**
     * @return array{products: array, complete: bool, count: int, retry_after: int}
     */
    public function catalog(bool $refresh = false): array
    {
        $fingerprint = hash('sha256', json_encode([
            rtrim($this->settings->value('h2h_base_url'), '/'),
            $this->settings->credential('h2h_api_key'),
        ]));
        $cacheKey = 'h2h:catalog:'.$fingerprint;
        $rateKey = 'h2h:catalog-requests:'.$fingerprint;
        $initial = ['products' => [], 'next_page' => 1, 'complete' => false, 'retry_at' => 0];
        $state = Cache::get($cacheKey, $initial);
        if ($state['complete'] && ! $refresh) {
            return $this->presentCatalog($state);
        }

        $updated = Cache::lock($cacheKey.':lock', 60)->get(function () use ($cacheKey, $rateKey, $initial, $refresh) {
            $state = Cache::get($cacheKey, $initial);
            if ($refresh && $state['complete']) {
                $state = $initial;
            }
            if ($state['complete'] || $state['retry_at'] > now()->timestamp) {
                return $state;
            }

            $startedAt = microtime(true);
            for ($batch = 0; $batch < self::CATALOG_BATCH_SIZE; $batch++) {
                if ($batch > 0 && microtime(true) - $startedAt >= 5) {
                    break;
                }
                if (RateLimiter::tooManyAttempts($rateKey, self::CATALOG_REQUESTS_PER_MINUTE)) {
                    $state['retry_at'] = now()->addSeconds(max(1, RateLimiter::availableIn($rateKey)))->timestamp;
                    Cache::put($cacheKey, $state, 3600);

                    break;
                }

                $client = $this->client();
                RateLimiter::hit($rateKey, 60);
                $response = $client->get('/api/v1/products', ['page' => $state['next_page']]);
                if ($response->status() === 429) {
                    $retryAfter = filter_var($response->header('Retry-After'), FILTER_VALIDATE_INT);
                    $state['retry_at'] = now()->addSeconds(max(1, $retryAfter === false ? 60 : $retryAfter))->timestamp;
                    Cache::put($cacheKey, $state, 3600);

                    break;
                }
                $data = $response->throw()->json('data');
                if (! is_array($data) || ! array_is_list($data)) {
                    throw new RuntimeException('Respons katalog H2H tidak valid.');
                }
                foreach ($data as $product) {
                    if (($product['billingType'] ?? 'prepaid') === 'prepaid' && ($product['apiPrice'] ?? 0) > 0) {
                        $state['products'][$product['id']] = [
                            'sku' => $product['sku'], 'name' => $product['name'],
                            'category' => $product['category'], 'brand' => $product['operator'],
                            'price' => $product['apiPrice'], 'provider_product_id' => $product['id'],
                            'status' => ($product['active'] ?? false) && ($product['published'] ?? true),
                            'fields' => $product['fields'] ?? [],
                            'username_service' => $product['usernameService'] ?? null,
                        ];
                    }
                }
                $state['next_page']++;
                $state['complete'] = count($data) < 50;
                $state['retry_at'] = 0;
                Cache::put($cacheKey, $state, $state['complete'] ? 300 : 3600);
                if ($state['complete']) {
                    break;
                }
                if ($state['next_page'] > 10000) {
                    throw new RuntimeException('Katalog H2H melebihi batas halaman API.');
                }
            }

            return $state;
        });

        return $this->presentCatalog($updated === false ? Cache::get($cacheKey, $initial) : $updated);
    }

    /**
     * @param  array{products: array, next_page: int, complete: bool, retry_at: int}  $state
     * @return array{products: array, complete: bool, count: int, retry_after: int}
     */
    private function presentCatalog(array $state): array
    {
        return [
            'products' => array_values($state['products']),
            'complete' => $state['complete'],
            'count' => count($state['products']),
            'retry_after' => max(0, $state['retry_at'] - now()->timestamp),
        ];
    }

    public function checkUsername(string $game, string $uid, string $server, ?string $serviceCode = null): array
    {
        $fingerprint = hash('sha256', $this->settings->credential('h2h_api_key'));
        $services = Cache::remember('h2h:username-services:'.$fingerprint, 300,
            fn () => $this->client()->get('/api/v1/username-services')->throw()->json('data') ?? []);
        $service = collect($services)->first(fn ($service) => ($service['active'] ?? false) && ($serviceCode
                ? $service['service'] === $serviceCode : UsernameService::gameForBrand($service['game']) === $game));
        if (! $service) {
            return ['status' => false, 'message' => 'Cek username belum tersedia untuk game ini.'];
        }

        $cacheKey = 'h2h:username-request:'.$fingerprint.':'.hash('sha256', json_encode([$service['service'], $uid, $server]));
        $request = Cache::remember($cacheKey, now()->addDay(), fn () => ['key' => (string) Str::uuid()]);
        if (isset($request['id'])) {
            $data = $this->client()->get('/api/v1/username-checks/'.$request['id'])->throw()->json('data');
        } else {
            $data = $this->client()->withHeaders(['Idempotency-Key' => $request['key']])
                ->post('/api/v1/username-checks', [
                    'service' => $service['service'], 'destination' => $uid, 'serverId' => $server,
                ])->throw()->json('data');
            if (isset($data['id'])) {
                Cache::put($cacheKey, [...$request, 'id' => $data['id']], now()->addDay());
            }
        }

        if (($data['status'] ?? '') === 'failed') {
            Cache::put($cacheKey, $request, 60);
        }

        return [
            'status' => ($data['status'] ?? '') === 'success' && ! empty($data['username']),
            'pending' => ($data['status'] ?? '') === 'pending',
            'message' => ($data['status'] ?? '') === 'pending'
                ? 'Username sedang diperiksa. Coba lagi sebentar.' : 'Game ID tidak ditemukan.',
            'data' => ['username' => $data['username'] ?? null],
        ];
    }
}
