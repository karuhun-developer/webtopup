<?php

namespace App\Actions\Cms\Management\RolePermission;

use App\Models\Spatie\Permission;
use Spatie\Permission\Models\Role;

class UpdateRolePermissionsAction
{
    /**
     * Assign all permissions to the role.
     */
    public function assignAll(Role $role): void
    {
        $role->syncPermissions(Permission::all()->pluck('name')->toArray());
    }

    /**
     * Revoke all permissions from the role.
     */
    public function revokeAll(Role $role): void
    {
        $role->syncPermissions([]);
    }

    /**
     * Assign a specific permission to the role.
     */
    public function assign(Role $role, string $permissionName): void
    {
        $role->givePermissionTo($permissionName);
    }

    /**
     * Revoke a specific permission from the role.
     */
    public function revoke(Role $role, string $permissionName): void
    {
        $role->revokePermissionTo($permissionName);
    }
}
