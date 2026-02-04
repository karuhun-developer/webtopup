<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Main\HomeController::class, 'index'])->name('home');
Route::get('/brand/{brand}', [App\Http\Controllers\Main\BrandController::class, 'show'])->name('product.show');
Route::post('/checkout', [App\Http\Controllers\Main\TransactionController::class, 'store'])->name('checkout.store');
Route::get('/transaction/{order}', [App\Http\Controllers\Main\TransactionController::class, 'show'])->name('transaction.show');
Route::get('/transaction-check', [App\Http\Controllers\Main\TransactionController::class, 'check'])->name('transaction.check');
Route::get('/profile', [App\Http\Controllers\Main\ProfileController::class, 'index'])->name('profile');
