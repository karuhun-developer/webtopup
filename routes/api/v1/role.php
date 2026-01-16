<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'role',
    'as' => 'role.',
    'middleware' => ['auth:api'],
], function () {
    // Roles Routes
    Route::get('/roles', [\App\Http\Controllers\Api\V1\Role\RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}', [\App\Http\Controllers\Api\V1\Role\RoleController::class, 'show'])->name('roles.show');
    Route::post('/roles', [\App\Http\Controllers\Api\V1\Role\RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [\App\Http\Controllers\Api\V1\Role\RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [\App\Http\Controllers\Api\V1\Role\RoleController::class, 'destroy'])->name('roles.destroy');
});
