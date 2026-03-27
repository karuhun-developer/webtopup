<?php

use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
    'middleware' => ['auth:api'],
], function () {
    // Users Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.change-password');
    Route::put('/users/{user}/email', [UserController::class, 'validateEmail'])->name('users.validate-email');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
