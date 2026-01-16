<?php

namespace App\Actions\Cms\Management\Permission;

use App\Models\Spatie\Permission;

class UpdatePermissionAction
{
    /**
     * Handle the action.
     */
    public function handle(Permission $permission, array $data): bool
    {
        return $permission->update($data);
    }
}
