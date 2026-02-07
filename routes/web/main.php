<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Main\HomeController::class, 'index'])->name('home');
Route::get('/brand/{brand}', [App\Http\Controllers\Main\BrandController::class, 'show'])->name('product.show');

Route::post('/checkout', [App\Http\Controllers\Main\TransactionController::class, 'store'])->name('checkout.store');
Route::get('/transaction/{order}', [App\Http\Controllers\Main\TransactionController::class, 'show'])->name('transaction.show');
Route::get('/transaction', [App\Http\Controllers\Main\TransactionController::class, 'check'])->name('transaction.check');
Route::put('/transaction/{order}', [App\Http\Controllers\Main\TransactionController::class, 'update'])->name('transaction.update');

Route::get('/profile', [App\Http\Controllers\Main\ProfileController::class, 'index'])->name('main.profile.index')->middleware('auth');
Route::patch('/profile', [App\Http\Controllers\Main\ProfileController::class, 'update'])->name('main.profile.update')->middleware('auth');
Route::patch('/password', [App\Http\Controllers\Main\PasswordController::class, 'update'])->name('main.password.update')->middleware('auth');

// After login
Route::get('/after-login', function () {
    if (auth()->user()->hasRole('superadmin')) {
        return to_route('cms.dashboard');
    } else {
        return to_route('home');
    }
})->name('after-login')->middleware('auth');
