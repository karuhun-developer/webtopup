<?php

namespace App\Actions\Cms\Management\Permission;

use App\Models\Spatie\Permission;

class DeletePermissionAction
{
    /**
     * Handle the action.
     */
    public function handle(Permission $permission): ?bool
    {
        return $permission->delete();
    }
}
