<?php

namespace App\Actions\Cms\Management\MenuSub;

use App\Models\Menu\MenuSub;

class UpdateMenuSubAction
{
    /**
     * Handle the action.
     */
    public function handle(MenuSub $menuSub, array $data): bool
    {
        return $menuSub->update($data);
    }
}
