<?php

namespace App\Actions\Cms\PPOB\PPOBProduct;

use App\Models\PPOB\PPOBProduct;

class DeletePPOBProductAction
{
    /**
     * Handle the action.
     */
    public function handle(PPOBProduct $product): ?bool
    {
        return $product->delete();
    }
}
