<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->rename('/cms/ppob/import-digiflazz', '/cms/ppob/import-product', 'Import Products');
    }

    public function down(): void
    {
        $this->rename('/cms/ppob/import-product', '/cms/ppob/import-digiflazz', 'Migrate Products');
    }

    private function rename(string $from, string $to, string $name): void
    {
        foreach (['menus', 'menu_subs'] as $table) {
            $roles = DB::table($table)->where('url', $from)->pluck('role_id');
            DB::table($table)->where('url', $from)->update(['url' => $to, 'active_pattern' => $to, 'name' => $name]);
            foreach ($roles as $role) {
                Cache::forget('menus:role:'.$role);
            }
        }
    }
};
