<?php

namespace App\Actions\Cms\Web\Slider;

use App\Models\Web\Slider;
use App\Traits\WithMediaCollection;
use Illuminate\Http\UploadedFile;

class StoreSliderAction
{
    use WithMediaCollection;

    /**
     * Handle the action.
     */
    public function handle(array $data): Slider
    {
        $slider = Slider::create($data);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $slider,
                file: $data['image'],
                collection: 'image',
            );
        }

        return $slider;
    }
}
