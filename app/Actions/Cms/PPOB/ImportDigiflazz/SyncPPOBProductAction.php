<?php

namespace App\Actions\Cms\PPOB\ImportDigiflazz;

use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\PPOB\PPOBProduct;
use App\Services\DigiflazzService;

class SyncPPOBProductAction
{
    public function __construct(
        public readonly DigiflazzService $digiflazzService,
    ) {}

    /**
     * Handle the action.
     */
    public function handle(): void
    {
        $products = $this->digiflazzService->getPrepaidProducts();

        foreach ($products as $productData) {
            $category = PPOBCategory::firstOrCreate(
                ['name' => $productData['category']],
                [
                    'description' => 'Imported from Digiflazz',
                    'status' => true,
                ],
            );
            $brand = PPOBBrand::firstOrCreate(
                [
                    'p_p_o_b_category_id' => $category->id,
                    'name' => $productData['brand'],
                ],
                [
                    'description' => 'Imported from Digiflazz',
                    'featured' => false,
                    'order' => PPOBBrand::max('order') + 1,
                    'settings' => [
                        'type' => 'id',
                        'label_id' => 'uid',
                    ],
                    'status' => true,
                ],
            );
            PPOBProduct::updateOrCreate(
                [
                    'p_p_o_b_brand_id' => $brand->id,
                    'sku' => $productData['buyer_sku_code'],
                ],
                [
                    'name' => $productData['product_name'],
                    'description' => 'Imported from Digiflazz',
                    'buy_price' => $productData['price'],
                    'sell_price' => $productData['price'] * 1.1, // 10% markup
                    'status' => true,
                ],
            );
        }
    }
}
