<?php

namespace App\Actions\Cms\PPOB\PPOBProduct;

use App\Models\PPOB\PPOBProduct;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class UpdatePPOBProductAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(PPOBProduct $product, array $data): bool
    {
        $data['buy_price'] = currencyToNumber($data['buy_price']);
        $data['sell_price'] = currencyToNumber($data['sell_price']);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $product,
                file: $data['image'],
                collection: 'image',
            );
        }

        return $product->update($data);
    }
}
