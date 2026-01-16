<?php

use App\Actions\Cms\Management\MenuSub\DeleteMenuSubAction;
use App\Actions\Cms\Management\MenuSub\StoreMenuSubAction;
use App\Actions\Cms\Management\MenuSub\UpdateMenuSubAction;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuSub;
use App\Models\Spatie\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('store menu sub action creates a sub menu', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $menu = Menu::create([
        'name' => 'Test Menu',
        'url' => '#',
        'icon' => 'home',
        'active_pattern' => 'home*',
        'order' => 1,
        'role_id' => $role->id,
    ]);

    $action = new StoreMenuSubAction;
    $data = [
        'name' => 'Test Sub Menu',
        'url' => '#',
        'icon' => 'circle',
        'active_pattern' => 'home/sub',
        'order' => 1,
        'menu_id' => $menu->id,
        'role_id' => $role->id,
    ];

    $menuSub = $action->handle($data);

    expect($menuSub)->toBeInstanceOf(MenuSub::class);
    $this->assertDatabaseHas('menu_subs', $data);
});

test('update menu sub action updates a sub menu', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $menu = Menu::create([
        'name' => 'Test Menu',
        'url' => '#',
        'icon' => 'home',
        'active_pattern' => 'home*',
        'order' => 1,
        'role_id' => $role->id,
    ]);
    $menuSub = MenuSub::create([
        'name' => 'Old Name',
        'url' => '#',
        'icon' => 'circle',
        'active_pattern' => 'home/sub',
        'order' => 1,
        'menu_id' => $menu->id,
        'role_id' => $role->id,
    ]);

    $action = new UpdateMenuSubAction;
    $data = [
        'name' => 'New Name',
        'url' => '/new',
        'icon' => 'square',
        'active_pattern' => 'new*',
        'order' => 2,
        'menu_id' => $menu->id,
        'role_id' => $role->id,
    ];

    $result = $action->handle($menuSub, $data);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('menu_subs', ['id' => $menuSub->id, 'name' => 'New Name']);
});

test('delete menu sub action deletes a sub menu', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $menu = Menu::create([
        'name' => 'Test Menu',
        'url' => '#',
        'icon' => 'home',
        'active_pattern' => 'home*',
        'order' => 1,
        'role_id' => $role->id,
    ]);
    $menuSub = MenuSub::create([
        'name' => 'Delete Me',
        'url' => '#',
        'icon' => 'circle',
        'active_pattern' => 'home/sub',
        'order' => 1,
        'menu_id' => $menu->id,
        'role_id' => $role->id,
    ]);

    $action = new DeleteMenuSubAction;

    $result = $action->handle($menuSub);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('menu_subs', ['id' => $menuSub->id]);
});
