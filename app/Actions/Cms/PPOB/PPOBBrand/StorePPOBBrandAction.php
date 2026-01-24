<?php

namespace App\Actions\Cms\PPOB\PPOBBrand;

use App\Models\PPOB\PPOBBrand;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class StorePPOBBrandAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(array $data): PPOBBrand
    {
        $brand = PPOBBrand::create($data);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $brand,
                file: $data['image'],
                collection: 'image',
            );
        }

        return $brand;
    }
}
