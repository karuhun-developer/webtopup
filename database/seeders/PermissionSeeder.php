<?php

namespace Database\Seeders;

use App\Models\Order\Order;
use App\Models\User;
use App\Models\Voucher\Voucher;
use App\Models\Web\Faq;
use App\Models\Web\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    // Define the prefix for permissions
    // This will be used to create permissions for each model
    private $prefixPermission = [
        'view',
        'show',
        'create',
        'update',
        'delete',
        'restore',
        'forceDelete',
        'export',
        'import',
        'validate',
    ];

    // Guard name for the permissions
    private $guardName = 'api';

    // Superadmin can't do
    private $superAdminExcludePermission = [
    ];

    // List user permissions
    private $adminPermissions = [
        'view'.User::class,
        'show'.User::class,
        'create'.User::class,
        'update'.User::class,
        'view'.Order::class,
        'show'.Order::class,
        'create'.Order::class,
        'update'.Order::class,
        'delete'.Order::class,
        'view'.Slider::class,
        'show'.Slider::class,
        'create'.Slider::class,
        'update'.Slider::class,
        'delete'.Slider::class,
        'view'.Faq::class,
        'show'.Faq::class,
        'create'.Faq::class,
        'update'.Faq::class,
        'create'.Faq::class,
        'update'.Faq::class,
        'delete'.Faq::class,
        'view'.Voucher::class,
        'show'.Voucher::class,
        'create'.Voucher::class,
        'update'.Voucher::class,
        'delete'.Voucher::class,
    ];

    // List user permissions
    private $userPermissions = [
        'view'.User::class,
        'update'.User::class,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Read all models exists
        $models = $this->getModelLists();

        // Create roles
        $roleSuperAdmin = Role::findOrCreate('superadmin', $this->guardName);
        $roleAdmin = Role::findOrCreate('admin', $this->guardName);
        $roleUser = Role::findOrCreate('user', $this->guardName);

        // Loop through each model and create permissions
        foreach ($this->prefixPermission as $permission) {
            foreach ($models as $model) {
                $permissionName = $permission.$model;
                Permission::query()
                    ->where('name', $permissionName)
                    ->where('guard_name', $this->guardName)
                    ->firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => $this->guardName,
                    ]);

                // Assign permissions to roles
                if (in_array($permissionName, $this->userPermissions)) {
                    $roleUser->givePermissionTo($permissionName);
                }

                // Assign permissions to admin role
                if (in_array($permissionName, $this->adminPermissions)) {
                    $roleAdmin->givePermissionTo($permissionName);
                }

                // Exclude superadmin permissions
                if (! in_array($permissionName, $this->superAdminExcludePermission)) {
                    $roleSuperAdmin->givePermissionTo($permissionName);
                }
            }
        }
    }

    /**
     * Get the list of models from the app directory.
     */
    private function getModelLists(): array
    {
        return collect(File::allFiles(app_path('Models')))
            ->filter(function ($file) {
                return $file->getExtension() === 'php';
            })
            ->map(function ($file) {
                $className = 'App\\Models\\'.str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

                return class_exists($className) ? $className : null;
            })
            ->filter()
            ->toArray();
    }
}
