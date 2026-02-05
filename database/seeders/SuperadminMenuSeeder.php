<?php

namespace Database\Seeders;

use App\Models\Menu\Menu;
use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SuperadminMenuSeeder extends Seeder
{
    public $role;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->role = Role::where('name', 'superadmin')->first();
        Menu::where('role_id', $this->role->id)->delete();

        // Clear cache
        Cache::forget('menus:role:'.$this->role->id);

        // Create menu
        $this->dashboardMenu();
        $this->orderMenu();
        $this->ppobMenu();
        $this->webMenu();
        $this->managementMenu();
    }

    public function dashboardMenu()
    {
        Menu::create([
            'role_id' => $this->role->id,
            'name' => 'Dashboard',
            'url' => '/cms/dashboard',
            'icon' => 'LayoutGrid',
            'order' => 1,
            'active_pattern' => '/cms/dashboard',
            'status' => 1,
        ]);
    }

    public function orderMenu()
    {
        Menu::create([
            'role_id' => $this->role->id,
            'name' => 'Orders',
            'url' => '/cms/order/orders',
            'icon' => 'ShoppingCart',
            'order' => 5,
            'active_pattern' => '/cms/order/orders',
            'status' => 1,
        ]);
    }

    public function ppobMenu()
    {
        $ppob = Menu::create([
            'role_id' => $this->role->id,
            'name' => 'PPOB',
            'url' => '#',
            'icon' => 'Box',
            'order' => 10,
            'active_pattern' => '/cms/ppob',
            'status' => 1,
        ]);
        $ppob->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Categories',
            'url' => '/cms/ppob/categories',
            'order' => 1,
            'active_pattern' => '/cms/ppob/categories',
            'status' => 1,
        ]);
        $ppob->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Brands',
            'url' => '/cms/ppob/brands',
            'order' => 2,
            'active_pattern' => '/cms/ppob/brands',
            'status' => 1,
        ]);
        $ppob->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Products',
            'url' => '/cms/ppob/products',
            'order' => 3,
            'active_pattern' => '/cms/ppob/products',
            'status' => 1,
        ]);
        $ppob->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Migrate Products',
            'url' => '/cms/ppob/import-digiflazz',
            'order' => 4,
            'active_pattern' => '/cms/ppob/import-digiflazz',
            'status' => 1,
        ]);
    }

    public function webMenu()
    {
        $web = Menu::create([
            'role_id' => $this->role->id,
            'name' => 'Web',
            'url' => '#',
            'icon' => 'Globe',
            'order' => 20,
            'active_pattern' => '/cms/web',
            'status' => 1,
        ]);
        $web->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Sliders',
            'url' => '/cms/web/sliders',
            'order' => 1,
            'active_pattern' => '/cms/web/sliders',
            'status' => 1,
        ]);
        $web->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'FAQs',
            'url' => '/cms/web/faqs',
            'order' => 2,
            'active_pattern' => '/cms/web/faqs',
            'status' => 1,
        ]);
    }

    public function managementMenu()
    {
        $management = Menu::create([
            'role_id' => $this->role->id,
            'name' => 'Managements',
            'url' => '#',
            'icon' => 'Folder',
            'order' => 999,
            'active_pattern' => '/cms/management',
            'status' => 1,
        ]);
        $management->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Permission',
            'url' => '/cms/management/permissions',
            'order' => 1,
            'active_pattern' => '/cms/management/permissions',
            'status' => 1,
        ]);
        $management->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Role',
            'url' => '/cms/management/roles',
            'order' => 2,
            'active_pattern' => '/cms/management/roles',
            'status' => 1,
        ]);
        $management->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'Menu',
            'url' => '/cms/management/menus',
            'order' => 3,
            'active_pattern' => '/cms/management/menus',
            'status' => 1,
        ]);
        $management->subMenu()->create([
            'role_id' => $this->role->id,
            'name' => 'User',
            'url' => '/cms/management/users',
            'order' => 4,
            'active_pattern' => '/cms/management/users',
            'status' => 1,
        ]);
    }
}
