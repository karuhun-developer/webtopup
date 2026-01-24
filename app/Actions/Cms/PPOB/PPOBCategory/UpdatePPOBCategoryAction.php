<?php

namespace App\Actions\Cms\PPOB\PPOBCategory;

use App\Models\PPOB\PPOBCategory;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class UpdatePPOBCategoryAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(PPOBCategory $category, array $data): bool
    {
        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $category,
                file: $data['image'],
                collection: 'image',
            );
        }

        return $category->update($data);
    }
}
