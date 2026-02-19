<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Main\HomeController::class, 'index'])->name('home');
Route::get('/privacy-policy', [App\Http\Controllers\Main\ContentController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [App\Http\Controllers\Main\ContentController::class, 'terms'])->name('terms');
Route::get('/brand/{brand}', [App\Http\Controllers\Main\BrandController::class, 'show'])->name('product.show');

Route::post('/checkout', [App\Http\Controllers\Main\TransactionController::class, 'store'])->name('checkout.store');
Route::get('/transaction/{order}', [App\Http\Controllers\Main\TransactionController::class, 'show'])->name('transaction.show');
Route::get('/transaction', [App\Http\Controllers\Main\TransactionController::class, 'check'])->name('transaction.check');
Route::put('/transaction/{order}', [App\Http\Controllers\Main\TransactionController::class, 'update'])->name('transaction.update');
Route::post('/check-game-account', [App\Http\Controllers\Main\CheckGameAccountController::class, 'check'])->name('check-game-account');
Route::post('/check-voucher', [App\Http\Controllers\Main\TransactionController::class, 'checkVoucher'])->name('check-voucher');

Route::get('/profile', [App\Http\Controllers\Main\ProfileController::class, 'index'])->name('main.profile.index')->middleware('auth');
Route::patch('/profile', [App\Http\Controllers\Main\ProfileController::class, 'update'])->name('main.profile.update')->middleware('auth');
Route::patch('/password', [App\Http\Controllers\Main\PasswordController::class, 'update'])->name('main.password.update')->middleware('auth');

// After login
Route::get('/after-login', function () {
    if (auth()->user()->hasRole(['superadmin', 'admin'])) {
        return to_route('cms.dashboard');
    } else {
        return to_route('home');
    }
})->name('after-login')->middleware('auth');
