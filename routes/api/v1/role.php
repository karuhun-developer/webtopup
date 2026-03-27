<?php

use App\Http\Controllers\Api\V1\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'role',
    'as' => 'role.',
    'middleware' => ['auth:api'],
], function () {
    // Roles Routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});
