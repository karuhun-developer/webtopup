<?php

namespace App\Actions\Cms\PPOB\PPOBProduct;

use App\Models\PPOB\PPOBProduct;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class StorePPOBProductAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(array $data): PPOBProduct
    {
        $data['buy_price'] = currencyToNumber($data['buy_price']);
        $data['sell_price'] = currencyToNumber($data['sell_price']);

        $product = PPOBProduct::create($data);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $product,
                file: $data['image'],
                collection: 'image',
            );
        }

        return $product;
    }
}
