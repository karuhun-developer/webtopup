<?php

namespace Database\Seeders;

use App\Models\Menu\Menu;
use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class AdminMenuSeeder extends Seeder
{
    public $role;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->role = Role::where('name', 'admin')->first();
        Menu::where('role_id', $this->role->id)->delete();

        // Clear cache
        Cache::forget('menus:role:'.$this->role->id);

        // Create menu
        $this->dashboardMenu();
        $this->orderMenu();
        $this->webMenu();
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
            'name' => 'Topup Orders',
            'url' => '/cms/order/topup-orders',
            'icon' => 'ShoppingCart',
            'order' => 5,
            'active_pattern' => '/cms/order/topup-orders',
            'status' => 1,
        ]);
        Menu::create([
            'role_id' => $this->role->id,
            'name' => 'Gift Orders',
            'url' => '/cms/order/gift-orders',
            'icon' => 'Gift',
            'order' => 6,
            'active_pattern' => '/cms/order/gift-orders',
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
            'name' => 'Banner',
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
}
