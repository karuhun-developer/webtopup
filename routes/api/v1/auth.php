<?php

use Illuminate\Support\Facades\Route;

// Login
Route::post('/login', [App\Http\Controllers\Api\V1\Auth\AuthenticatedController::class, 'store'])->name('login');

// Register
Route::post('/register', [App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'store'])->name('register');

// Reset Password
Route::post('/password/reset', [App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'store'])->middleware('throttle:5,1')->name('password.reset');

// Must be authenticated
Route::middleware('auth:api')->group(function () {
    // Me
    Route::get('/me', [App\Http\Controllers\Api\V1\Auth\AuthenticatedController::class, 'me'])->name('me');

    // Update profile
    Route::put('/me', [App\Http\Controllers\Api\V1\Auth\AuthenticatedController::class, 'update'])->name('me.update');

    // Resend verification email
    Route::post('/email/resend', [App\Http\Controllers\Api\V1\Auth\AuthenticatedController::class, 'resend'])->name('email.resend')->middleware('throttle:6,1');

    // Logout
    Route::post('/logout', [App\Http\Controllers\Api\V1\Auth\AuthenticatedController::class, 'logout'])->name('logout');
});
