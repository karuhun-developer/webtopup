<?php

use App\Http\Controllers\Cms\Order\ArchiveOrderController;
use App\Http\Controllers\Cms\Order\GiftOrderController;
use App\Http\Controllers\Cms\Order\ManualTopupOrderController;
use App\Http\Controllers\Cms\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'order',
    'as' => 'order.',
], function () {
    // Topup Orders
    Route::get('topup-orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('topup-orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('topup-orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('topup-orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('topup-orders/{order}/validate-payment', [OrderController::class, 'validatePayment'])->name('orders.validate-payment');
    Route::post('topup-orders/archive-all', [OrderController::class, 'archiveAll'])->name('orders.archive-all');

    // Gift Orders
    Route::get('gift-orders', [GiftOrderController::class, 'index'])->name('gift-orders.index');
    Route::get('gift-orders/create', [GiftOrderController::class, 'create'])->name('gift-orders.create');
    Route::post('gift-orders', [GiftOrderController::class, 'store'])->name('gift-orders.store');
    Route::get('gift-orders/{order}', [GiftOrderController::class, 'show'])->name('gift-orders.show');
    Route::put('gift-orders/{order}', [GiftOrderController::class, 'save'])->name('gift-orders.save');
    Route::put('gift-orders/{order}/notify', [GiftOrderController::class, 'notify'])->name('gift-orders.notify');
    Route::get('gift-orders/{order}/validate', [GiftOrderController::class, 'validatePaymentView'])->name('gift-orders.validate');
    Route::put('gift-orders/{order}/validate-payment', [GiftOrderController::class, 'validatePayment'])->name('gift-orders.validate-payment');
    Route::post('gift-orders/archive-all', [GiftOrderController::class, 'archiveAll'])->name('gift-orders.archive-all');

    // Manual Topup Orders
    Route::get('manual-topup-orders', [ManualTopupOrderController::class, 'index'])->name('manual-topup-orders.index');
    Route::get('manual-topup-orders/create', [ManualTopupOrderController::class, 'create'])->name('manual-topup-orders.create');
    Route::post('manual-topup-orders', [ManualTopupOrderController::class, 'store'])->name('manual-topup-orders.store');
    Route::get('manual-topup-orders/{order}', [ManualTopupOrderController::class, 'show'])->name('manual-topup-orders.show');
    Route::put('manual-topup-orders/{order}', [ManualTopupOrderController::class, 'save'])->name('manual-topup-orders.save');
    Route::put('manual-topup-orders/{order}/notify', [ManualTopupOrderController::class, 'notify'])->name('manual-topup-orders.notify');
    Route::post('manual-topup-orders/archive-all', [ManualTopupOrderController::class, 'archiveAll'])->name('manual-topup-orders.archive-all');

    // Archive
    Route::get('archives', [ArchiveOrderController::class, 'index'])->name('archives.index');
    Route::post('archives', [OrderController::class, 'archive'])->name('archives.archive');
    Route::post('archives/unarchive', [ArchiveOrderController::class, 'unarchive'])->name('archives.unarchive');
    Route::post('archives/unarchive-all', [ArchiveOrderController::class, 'unarchiveAll'])->name('archives.unarchive-all');
});
