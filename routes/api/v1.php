<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function () {
    // Welcome Route
    Route::get('/', function () {
        return response()->json([
            'message' => 'V1 API',
        ]);
    })->name('welcome');

    // Callback Routes
    require 'v1/callback.php';
});
