<?php

namespace App\Http\Controllers\Cms\Management;

use App\Actions\Cms\Management\RolePermission\UpdateRolePermissionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\Management\RolePermission\UpdateRolePermissionRequest;
use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role as SpatieRole;

class RolePermissionController extends Controller
{
    protected string $resource = Role::class;

    public function index(SpatieRole $role)
    {
        Gate::authorize('validate'.$this->resource);

        return inertia('cms/management/role/Permission', [
            'role' => $role,
            'permissions' => inertia()->defer(fn () => $this->getPermissions($role)),
        ]);
    }

    /**
     * Get all permissions.
     */
    private function getPermissions(SpatieRole $role)
    {
        $permissionSources = Permission::all();
        $permissions = [];

        // Get all permission sources
        foreach ($permissionSources as $perm) {
            $perm = explode('App\\', $perm->name);
            $model = 'App\\'.$perm[1];
            $permission = $perm[0];

            $permissions[$model][$permission] = false;
        }

        // Check if role has permissions
        foreach ($role->permissions->pluck('name') as $perm) {
            $perm = explode('App\\', $perm);
            $model = 'App\\'.$perm[1];
            $permission = $perm[0];

            $permissions[$model][$permission] = true;
        }

        return $permissions;
    }

    public function checkAllPermissions(SpatieRole $role, UpdateRolePermissionsAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->assignAll($role);

        return back();
    }

    public function uncheckAllPermissions(SpatieRole $role, UpdateRolePermissionsAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->revokeAll($role);

        return back();
    }

    public function checkPermissions(UpdateRolePermissionRequest $request, SpatieRole $role, UpdateRolePermissionsAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->assign($role, $request->permission);

        return back();
    }

    public function uncheckPermissions(UpdateRolePermissionRequest $request, SpatieRole $role, UpdateRolePermissionsAction $action)
    {
        Gate::authorize('update'.$this->resource);

        $action->revoke($role, $request->permission);

        return back();
    }
}
