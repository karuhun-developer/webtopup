<?php

use App\Actions\Cms\Management\Menu\DeleteMenuAction;
use App\Actions\Cms\Management\Menu\StoreMenuAction;
use App\Actions\Cms\Management\Menu\UpdateMenuAction;
use App\Models\Menu\Menu;
use App\Models\Spatie\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('store menu action creates a menu', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $action = new StoreMenuAction;
    $data = [
        'name' => 'Test Menu',
        'url' => '#',
        'icon' => 'home',
        'active_pattern' => 'home*',
        'order' => 1,
        'role_id' => $role->id,
    ];

    $menu = $action->handle($data);

    expect($menu)->toBeInstanceOf(Menu::class);
    $this->assertDatabaseHas('menus', $data);
});

test('update menu action updates a menu', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $menu = Menu::create([
        'name' => 'Old Name',
        'url' => '#',
        'icon' => 'home',
        'active_pattern' => 'home*',
        'order' => 1,
        'role_id' => $role->id,
    ]);
    $action = new UpdateMenuAction;
    $data = [
        'name' => 'New Name',
        'url' => '/new',
        'icon' => 'user',
        'active_pattern' => 'new*',
        'order' => 2,
        'role_id' => $role->id,
    ];

    $result = $action->handle($menu, $data);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'New Name']);
});

test('delete menu action deletes a menu', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $menu = Menu::create([
        'name' => 'Delete Me',
        'url' => '#',
        'icon' => 'home',
        'active_pattern' => 'home*',
        'order' => 1,
        'role_id' => $role->id,
    ]);
    $action = new DeleteMenuAction;

    $result = $action->handle($menu);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
});
