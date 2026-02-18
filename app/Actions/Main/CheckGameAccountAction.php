<?php

namespace App\Actions\Main;

use App\Models\PPOB\PPOBProduct;
use App\Services\GameProService;
use Illuminate\Support\Str;

class CheckGameAccountAction
{
    public function __construct(
        protected GameProService $gameProService,
    ) {}

    /**
     * Handle the action.
     */
    public function handle(array $data): array
    {
        $brandName = '';

        if (isset($data['product_id'])) {
            $product = PPOBProduct::find($data['product_id']);
            $brandName = $product->brand->name;
        } elseif (isset($data['slug'])) {
            $brand = \App\Models\PPOB\PPOBBrand::where('slug', $data['slug'])->firstOrFail();
            $brandName = $brand->name;
        }

        // Currently only Mobile Legends is supported for check
        if (!Str::contains(strtolower($brandName), 'mobile legend')) {
            return [
                'status' => false,
                'message' => 'Game validation not supported for this product',
            ];
        }

        return $this->gameProService->resolveAccount(
            game: 'mobilelegend',
            uid: $data['account_id'],
            server: $data['server_id'] ?? null,
        );
    }
}
