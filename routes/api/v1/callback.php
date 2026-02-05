<?php

use Illuminate\Support\Facades\Route;

// Callback URL for Midtrans
Route::post('/midtrans/callback', [App\Http\Controllers\Api\V1\Callback\MidtransController::class, 'callback'])->name('midtrans.callback');
