<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Login
Route::post('/login', [AuthenticatedController::class, 'store'])->name('login');

// Register
Route::post('/register', [RegisterController::class, 'store'])->name('register');

// Reset Password
Route::post('/password/reset', [PasswordResetController::class, 'store'])->middleware('throttle:5,1')->name('password.reset');

// Must be authenticated
Route::middleware('auth:api')->group(function () {
    // Me
    Route::get('/me', [AuthenticatedController::class, 'me'])->name('me');

    // Update profile
    Route::put('/me', [AuthenticatedController::class, 'update'])->name('me.update');

    // Resend verification email
    Route::post('/email/resend', [AuthenticatedController::class, 'resend'])->name('email.resend')->middleware('throttle:6,1');

    // Logout
    Route::post('/logout', [AuthenticatedController::class, 'logout'])->name('logout');
});
