<?php

namespace App\Actions\Cms\PPOB\PPOBBrand;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBBrand;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class UpdatePPOBBrandAction
{
    use WithMediaCollection, WithVersionedCache;

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

        $result = $brand->update($data);

        $this->flushCacheGroup(CacheGroupEnum::BRANDS, CacheGroupEnum::CATEGORIES);

        return $result;
    }
}
