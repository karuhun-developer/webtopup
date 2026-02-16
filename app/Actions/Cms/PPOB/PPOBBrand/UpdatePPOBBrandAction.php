<?php

namespace App\Actions\Cms\PPOB\PPOBBrand;

use App\Models\PPOB\PPOBBrand;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class UpdatePPOBBrandAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(PPOBBrand $brand, array $data): bool
    {
        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $brand,
                file: $data['image'],
                collection: 'image',
            );
        }

        if ($data['banner'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $brand,
                file: $data['banner'],
                collection: 'banner',
            );
        }

        if ($data['default_product_image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $brand,
                file: $data['default_product_image'],
                collection: 'default_product_image',
            );
        }

        $brand->products()->where('provider', $brand->provider)->update([
            'provider' => $data['provider'] ?? $brand->provider,
        ]);

        return $brand->update($data);
    }
}
