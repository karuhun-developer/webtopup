<?php

use App\Http\Controllers\Cms\Management\MenuController;
use App\Http\Controllers\Cms\Management\MenuSubController;
use App\Http\Controllers\Cms\Management\PermissionController;
use App\Http\Controllers\Cms\Management\RoleController;
use App\Http\Controllers\Cms\Management\RolePermissionController;
use App\Http\Controllers\Cms\Management\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'management',
    'as' => 'management.',
], function () {
    Route::resources([
        'permissions' => PermissionController::class,
        'roles' => RoleController::class,
        'menus' => MenuController::class,
        'menus.sub-menus' => MenuSubController::class,
        'users' => UserController::class,
    ]);

    // Role Permissions Route
    Route::get('roles/{role}/permissions', [RolePermissionController::class, 'index'])->name('roles.permissions');
    Route::put('roles/{role}/check-permissions', [RolePermissionController::class, 'checkPermissions'])->name('roles.check-permissions');
    Route::put('roles/{role}/uncheck-permissions', [RolePermissionController::class, 'uncheckPermissions'])->name('roles.uncheck-permissions');
    Route::put('roles/{role}/check-all-permissions', [RolePermissionController::class, 'checkAllPermissions'])->name('roles.check-all-permissions');
    Route::put('roles/{role}/uncheck-all-permissions', [RolePermissionController::class, 'uncheckAllPermissions'])->name('roles.uncheck-all-permissions');

    // Users Password Route
    Route::get('users/{user}/edit-password', [UserController::class, 'editPassword'])->name('users.edit-password');
    Route::put('users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');
});
