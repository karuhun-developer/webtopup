<?php

namespace App\Actions\Cms\PPOB\PPOBCategory;

use App\Models\PPOB\PPOBCategory;

class DeletePPOBCategoryAction
{
    /**
     * Handle the action.
     */
    public function handle(PPOBCategory $category): ?bool
    {
        return $category->delete();
    }
}
