<?php

use Illuminate\Support\Facades\Route;

// Callback URL for Midtrans
Route::post('/midtrans/callback', [App\Http\Controllers\Api\V1\Callback\MidtransController::class, 'callback'])->name('midtrans.callback');

// Callback URL for Digiflazz
Route::post('/digiflazz/callback', [App\Http\Controllers\Api\V1\Callback\DigiflazzController::class, 'callback'])->name('digiflazz.callback');
