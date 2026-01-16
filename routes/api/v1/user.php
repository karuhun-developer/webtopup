<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
    'middleware' => ['auth:api'],
], function () {
    // Users Routes
    Route::get('/users', [App\Http\Controllers\Api\V1\User\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Api\V1\User\UserController::class, 'show'])->name('users.show');
    Route::post('/users', [App\Http\Controllers\Api\V1\User\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [App\Http\Controllers\Api\V1\User\UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/password', [App\Http\Controllers\Api\V1\User\UserController::class, 'updatePassword'])->name('users.change-password');
    Route::put('/users/{user}/email', [App\Http\Controllers\Api\V1\User\UserController::class, 'validateEmail'])->name('users.validate-email');
    Route::delete('/users/{user}', [App\Http\Controllers\Api\V1\User\UserController::class, 'destroy'])->name('users.destroy');
});
