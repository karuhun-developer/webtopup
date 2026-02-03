<?php

namespace App\Actions\Cms\Web\Slider;

use App\Models\Web\Slider;

class DeleteSliderAction
{
    /**
     * Handle the action.
     */
    public function handle(Slider $slider): ?bool
    {
        return $slider->delete();
    }
}
