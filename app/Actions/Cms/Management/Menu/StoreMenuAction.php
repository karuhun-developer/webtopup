<?php

namespace App\Actions\Cms\Management\Menu;

use App\Models\Menu\Menu;

class StoreMenuAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): Menu
    {
        return Menu::create($data);
    }
}
