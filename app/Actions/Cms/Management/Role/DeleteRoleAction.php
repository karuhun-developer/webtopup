<?php

namespace App\Actions\Cms\Management\Role;

use App\Models\Spatie\Role;

class DeleteRoleAction
{
    /**
     * Handle the action.
     */
    public function handle(Role $role): ?bool
    {
        return $role->delete();
    }
}
