<?php

use App\Http\Controllers\Main\BrandController;
use App\Http\Controllers\Main\CheckGameAccountController;
use App\Http\Controllers\Main\ContentController;
use App\Http\Controllers\Main\HomeController;
use App\Http\Controllers\Main\PasswordController;
use App\Http\Controllers\Main\ProfileController;
use App\Http\Controllers\Main\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy-policy', [ContentController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [ContentController::class, 'terms'])->name('terms');
Route::get('/brand/{brand}', [BrandController::class, 'show'])->name('product.show');

Route::post('/checkout', [TransactionController::class, 'store'])->name('checkout.store')->middleware('throttle:20,1');
Route::get('/transaction/{order}', [TransactionController::class, 'show'])->name('transaction.show')->middleware(['signed', 'throttle:60,1']);
Route::get('/transaction', [TransactionController::class, 'check'])->name('transaction.check')->middleware('throttle:30,1');
Route::put('/transaction/{order}', [TransactionController::class, 'update'])->name('transaction.update')->middleware(['signed', 'throttle:30,1']);
Route::post('/check-game-account', [CheckGameAccountController::class, 'check'])->name('check-game-account')->middleware('throttle:30,1');
Route::post('/check-voucher', [TransactionController::class, 'checkVoucher'])->name('check-voucher')->middleware('throttle:30,1');

Route::get('/profile', [ProfileController::class, 'index'])->name('main.profile.index')->middleware('auth');
Route::patch('/profile', [ProfileController::class, 'update'])->name('main.profile.update')->middleware('auth');
Route::patch('/password', [PasswordController::class, 'update'])->name('main.password.update')->middleware('auth');

// After login
Route::get('/after-login', function () {
    if (auth()->user()->hasRole(['superadmin', 'admin'])) {
        return to_route('cms.dashboard');
    } else {
        return to_route('home');
    }
})->name('after-login')->middleware('auth');
