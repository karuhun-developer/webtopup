<?php

use App\Http\Controllers\Api\V1\Callback\DigiflazzController;
use App\Http\Controllers\Api\V1\Callback\MidtransController;
use Illuminate\Support\Facades\Route;

// Callback URL for Midtrans
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');

// Callback URL for Digiflazz
Route::post('/digiflazz/callback', [DigiflazzController::class, 'callback'])->name('digiflazz.callback');
