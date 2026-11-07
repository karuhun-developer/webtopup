<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class H2hService
{
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
        $key = 'h2h:products:'.hash('sha256', $this->settings->credential('h2h_api_key'));
        if ($refresh) {
            Cache::forget($key);
        }

        return Cache::remember($key, 300, function () {
            $products = [];
            for ($page = 1; $page <= 10000; $page++) {
                $data = $this->client()->get('/api/v1/products', ['page' => $page])->throw()->json('data');
                if (! is_array($data)) {
                    throw new RuntimeException('Respons katalog H2H tidak valid.');
                }
                foreach ($data as $product) {
                    if (($product['billingType'] ?? 'prepaid') === 'prepaid' && ($product['apiPrice'] ?? 0) > 0) {
                        $products[] = [
                            'sku' => $product['sku'], 'name' => $product['name'],
                            'category' => $product['category'], 'brand' => $product['operator'],
                            'price' => $product['apiPrice'], 'provider_product_id' => $product['id'],
                            'status' => ($product['active'] ?? false) && ($product['published'] ?? true),
                            'fields' => $product['fields'] ?? [],
                            'username_service' => $product['usernameService'] ?? null,
                        ];
                    }
                }
                if (count($data) < 50) {
                    break;
                }
            }

            return $products;
        });
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
