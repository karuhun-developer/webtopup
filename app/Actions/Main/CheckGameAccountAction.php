<?php

namespace App\Actions\Main;

use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBProduct;
use App\Services\UsernameService;

class CheckGameAccountAction
{
    public function __construct(protected UsernameService $username) {}

    public function handle(array $data): array
    {
        $brand = isset($data['product_id'])
            ? PPOBProduct::with('brand')->findOrFail($data['product_id'])->brand
            : PPOBBrand::where('slug', $data['slug'])->firstOrFail();

        return $this->username->resolveAccount(
            UsernameService::gameForBrand($brand->name), $data['account_id'], $data['server_id'] ?? null,
            serviceCode: $brand->settings['username_service'] ?? null,
        );
    }
}
