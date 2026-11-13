<?php

namespace App\Actions\Cms\PPOB\ImportProduct;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\PPOB\PPOBProduct;
use App\Services\ProductProviderService;
use App\Traits\WithVersionedCache;
use Illuminate\Support\Facades\DB;

class SyncPPOBProductAction
{
    use WithVersionedCache;

    public function __construct(public readonly ProductProviderService $providers) {}

    public function handle(string $provider): void
    {
        $products = $this->providers->products($provider);
        DB::transaction(function () use ($products, $provider) {
            foreach ($products as $data) {
                $category = PPOBCategory::firstOrCreate(['name' => $data['category']], ['status' => true]);
                $fields = collect($data['fields'] ?? []);
                $server = $fields->firstWhere('name', 'server_id');
                $brand = PPOBBrand::firstOrCreate([
                    'p_p_o_b_category_id' => $category->id, 'name' => $data['brand'],
                ], [
                    'provider' => $provider, 'featured' => false, 'status' => true,
                    'order' => (PPOBBrand::max('order') ?? 0) + 1,
                    'settings' => [
                        'type' => ($server['required'] ?? false) ? 'id+server' : 'id',
                        'label_id' => $fields->firstWhere('name', 'tujuan')['label'] ?? 'ID',
                        'label_server' => $server['label'] ?? 'Server',
                        'servers' => $server['options'] ?? [],
                        'username_service' => $data['username_service'] ?? null,
                    ],
                ]);
                if ($provider === 'h2h') {
                    $settings = $brand->settings ?? [];
                    $settings['username_service'] = $data['username_service'] ?? null;
                    if ($server['required'] ?? false) {
                        $settings['type'] = 'id+server';
                        $settings['label_server'] ??= $server['label'] ?? 'Server';
                    }
                    if (empty($settings['servers'])) {
                        $settings['servers'] = $server['options'] ?? [];
                    }
                    $brand->update(['settings' => $settings]);
                }
                $product = PPOBProduct::firstOrNew(['provider' => $provider, 'sku' => $data['sku']]);
                $product->fill([
                    'p_p_o_b_brand_id' => $brand->id, 'name' => $data['name'],
                    'provider_product_id' => $data['provider_product_id'] ?? null,
                    'buy_price' => $data['price'], 'status' => $data['status'],
                ]);
                if (! $product->exists) {
                    $product->sell_price = (int) ceil($data['price'] * 1.1);
                }
                $product->save();
            }
        });
        $this->flushCacheGroup(CacheGroupEnum::BRANDS, CacheGroupEnum::CATEGORIES);
    }
}
