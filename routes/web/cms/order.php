<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'order',
    'as' => 'order.',
], function () {
    Route::get('orders', [\App\Http\Controllers\Cms\Order\OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('orders/create', [\App\Http\Controllers\Cms\Order\OrderController::class, 'create'])
        ->name('orders.create');

    Route::post('orders', [\App\Http\Controllers\Cms\Order\OrderController::class, 'store'])
        ->name('orders.store');

    Route::get('orders/{order}', [\App\Http\Controllers\Cms\Order\OrderController::class, 'show'])
        ->name('orders.show');

    Route::put('orders/{order}/validate-payment', [\App\Http\Controllers\Cms\Order\OrderController::class, 'validatePayment'])
        ->name('orders.validate-payment');
});
