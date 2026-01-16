<?php

use App\Actions\Cms\Management\Role\DeleteRoleAction;
use App\Actions\Cms\Management\Role\StoreRoleAction;
use App\Actions\Cms\Management\Role\UpdateRoleAction;
use App\Models\Spatie\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('store role action creates a role', function () {
    $action = new StoreRoleAction;
    $data = ['name' => 'Test Role', 'guard_name' => 'api'];

    $role = $action->handle($data);

    expect($role)->toBeInstanceOf(Role::class);
    $this->assertDatabaseHas('roles', $data);
});

test('update role action updates a role', function () {
    $role = Role::create(['name' => 'Old Name', 'guard_name' => 'api']);
    $action = new UpdateRoleAction;
    $data = ['name' => 'New Name', 'guard_name' => 'api'];

    $result = $action->handle($role, $data);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'New Name']);
});

test('delete role action deletes a role', function () {
    $role = Role::create(['name' => 'Delete Me', 'guard_name' => 'api']);
    $action = new DeleteRoleAction;

    $result = $action->handle($role);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});
