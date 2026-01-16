<?php

use App\Actions\Cms\Management\RolePermission\UpdateRolePermissionsAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('assign all permissions action assigns all permissions', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    Permission::create(['name' => 'perm1', 'guard_name' => 'api']);
    Permission::create(['name' => 'perm2', 'guard_name' => 'api']);

    $action = new UpdateRolePermissionsAction;
    $action->assignAll($role);

    expect($role->refresh()->permissions->count())->toBe(2);
    expect($role->hasPermissionTo('perm1'))->toBeTrue();
    expect($role->hasPermissionTo('perm2'))->toBeTrue();
});

test('revoke all permissions action revokes all permissions', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $p1 = Permission::create(['name' => 'perm1', 'guard_name' => 'api']);
    $role->givePermissionTo($p1);

    $action = new UpdateRolePermissionsAction;
    $action->revokeAll($role);

    expect($role->refresh()->permissions->count())->toBe(0);
});

test('assign permission action assigns specific permission', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    Permission::create(['name' => 'perm1', 'guard_name' => 'api']);

    $action = new UpdateRolePermissionsAction;
    $action->assign($role, 'perm1');

    expect($role->refresh()->hasPermissionTo('perm1'))->toBeTrue();
});

test('revoke permission action revokes specific permission', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $p1 = Permission::create(['name' => 'perm1', 'guard_name' => 'api']);
    $role->givePermissionTo($p1);

    $action = new UpdateRolePermissionsAction;
    $action->revoke($role, 'perm1');

    expect($role->refresh()->hasPermissionTo('perm1'))->toBeFalse();
});
