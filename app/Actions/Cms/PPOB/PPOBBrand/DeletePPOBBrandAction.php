<?php

namespace App\Actions\Cms\PPOB\PPOBBrand;

use App\Models\PPOB\PPOBBrand;

class DeletePPOBBrandAction
{
    /**
     * Handle the action.
     */
    public function handle(PPOBBrand $brand): ?bool
    {
        return $brand->delete();
    }
}
