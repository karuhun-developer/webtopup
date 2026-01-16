<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'management',
    'as' => 'management.',
], function () {
    Route::resources([
        'permissions' => \App\Http\Controllers\Cms\Management\PermissionController::class,
        'roles' => \App\Http\Controllers\Cms\Management\RoleController::class,
        'menus' => \App\Http\Controllers\Cms\Management\MenuController::class,
        'menus.sub-menus' => \App\Http\Controllers\Cms\Management\MenuSubController::class,
        'users' => \App\Http\Controllers\Cms\Management\UserController::class,
    ]);

    // Role Permissions Route
    Route::get('roles/{role}/permissions', [\App\Http\Controllers\Cms\Management\RolePermissionController::class, 'index'])->name('roles.permissions');
    Route::put('roles/{role}/check-permissions', [\App\Http\Controllers\Cms\Management\RolePermissionController::class, 'checkPermissions'])->name('roles.check-permissions');
    Route::put('roles/{role}/uncheck-permissions', [\App\Http\Controllers\Cms\Management\RolePermissionController::class, 'uncheckPermissions'])->name('roles.uncheck-permissions');
    Route::put('roles/{role}/check-all-permissions', [\App\Http\Controllers\Cms\Management\RolePermissionController::class, 'checkAllPermissions'])->name('roles.check-all-permissions');
    Route::put('roles/{role}/uncheck-all-permissions', [\App\Http\Controllers\Cms\Management\RolePermissionController::class, 'uncheckAllPermissions'])->name('roles.uncheck-all-permissions');

    // Users Password Route
    Route::get('users/{user}/edit-password', [\App\Http\Controllers\Cms\Management\UserController::class, 'editPassword'])->name('users.edit-password');
    Route::put('users/{user}/update-password', [\App\Http\Controllers\Cms\Management\UserController::class, 'updatePassword'])->name('users.update-password');
});
