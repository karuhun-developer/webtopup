<?php

use App\Http\Controllers\Api\V1\Callback\DigiflazzController;
use App\Http\Controllers\Api\V1\Callback\H2hController;
use App\Http\Controllers\Api\V1\Callback\MidtransController;
use App\Http\Controllers\Api\V1\Callback\PaywuzController;
use Illuminate\Support\Facades\Route;

// Callback URL for Midtrans
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');

// Callback URL for Digiflazz
Route::post('/digiflazz/callback', [DigiflazzController::class, 'callback'])->name('digiflazz.callback');
Route::post('/h2h/callback', [H2hController::class, 'callback'])->name('h2h.callback');
Route::post('/paywuz/callback', [PaywuzController::class, 'callback'])->name('paywuz.callback');
