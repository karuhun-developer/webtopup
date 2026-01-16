<?php

namespace App\Actions\Cms\Management\Permission;

use App\Models\Spatie\Permission;

class StorePermissionAction
{
    /**
     * Handle the action.
     */
    public function handle(array $data): Permission
    {
        return Permission::create($data);
    }
}
