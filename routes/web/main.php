<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Main\HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [App\Http\Controllers\Main\ProductController::class, 'show'])->name('product.show');
Route::get('/transaction-check', [App\Http\Controllers\Main\TransactionController::class, 'check'])->name('transaction.check');
Route::get('/profile', [App\Http\Controllers\Main\ProfileController::class, 'index'])->name('profile');
