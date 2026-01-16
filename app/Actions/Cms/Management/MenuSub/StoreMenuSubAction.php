<?php

namespace App\Actions\Cms\Management\MenuSub;

use App\Models\Menu\MenuSub;

class StoreMenuSubAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): MenuSub
    {
        return MenuSub::create($data);
    }
}
