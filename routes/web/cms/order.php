<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'order',
    'as' => 'order.',
], function () {
    // Topup Orders
    Route::get('topup-orders', [\App\Http\Controllers\Cms\Order\OrderController::class, 'index'])->name('orders.index');
    Route::get('topup-orders/create', [\App\Http\Controllers\Cms\Order\OrderController::class, 'create'])->name('orders.create');
    Route::post('topup-orders', [\App\Http\Controllers\Cms\Order\OrderController::class, 'store'])->name('orders.store');
    Route::get('topup-orders/{order}', [\App\Http\Controllers\Cms\Order\OrderController::class, 'show'])->name('orders.show');
    Route::put('topup-orders/{order}/validate-payment', [\App\Http\Controllers\Cms\Order\OrderController::class, 'validatePayment'])->name('orders.validate-payment');
    Route::post('topup-orders/archive-all', [\App\Http\Controllers\Cms\Order\OrderController::class, 'archiveAll'])->name('orders.archive-all');

    // Gift Orders
    Route::get('gift-orders', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'index'])->name('gift-orders.index');
    Route::get('gift-orders/create', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'create'])->name('gift-orders.create');
    Route::post('gift-orders', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'store'])->name('gift-orders.store');
    Route::get('gift-orders/{order}', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'show'])->name('gift-orders.show');
    Route::put('gift-orders/{order}', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'save'])->name('gift-orders.save');
    Route::put('gift-orders/{order}/notify', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'notify'])->name('gift-orders.notify');
    Route::get('gift-orders/{order}/validate', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'validatePaymentView'])->name('gift-orders.validate');
    Route::put('gift-orders/{order}/validate-payment', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'validatePayment'])->name('gift-orders.validate-payment');
    Route::post('gift-orders/archive-all', [\App\Http\Controllers\Cms\Order\GiftOrderController::class, 'archiveAll'])->name('gift-orders.archive-all');

    // Manual Topup Orders
    Route::get('manual-topup-orders', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'index'])->name('manual-topup-orders.index');
    Route::get('manual-topup-orders/create', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'create'])->name('manual-topup-orders.create');
    Route::post('manual-topup-orders', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'store'])->name('manual-topup-orders.store');
    Route::get('manual-topup-orders/{order}', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'show'])->name('manual-topup-orders.show');
    Route::put('manual-topup-orders/{order}', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'save'])->name('manual-topup-orders.save');
    Route::put('manual-topup-orders/{order}/notify', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'notify'])->name('manual-topup-orders.notify');
    Route::post('manual-topup-orders/archive-all', [\App\Http\Controllers\Cms\Order\ManualTopupOrderController::class, 'archiveAll'])->name('manual-topup-orders.archive-all');

    // Archive
    Route::get('archives', [\App\Http\Controllers\Cms\Order\ArchiveOrderController::class, 'index'])->name('archives.index');
    Route::post('archives', [\App\Http\Controllers\Cms\Order\OrderController::class, 'archive'])->name('archives.archive');
    Route::post('archives/unarchive', [\App\Http\Controllers\Cms\Order\ArchiveOrderController::class, 'unarchive'])->name('archives.unarchive');
    Route::post('archives/unarchive-all', [\App\Http\Controllers\Cms\Order\ArchiveOrderController::class, 'unarchiveAll'])->name('archives.unarchive-all');
});
