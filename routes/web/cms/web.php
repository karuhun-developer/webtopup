<?php

use App\Http\Controllers\Cms\Web\FaqController;
use App\Http\Controllers\Cms\Web\SliderController;
use App\Http\Controllers\Cms\Web\VoucherController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'web',
    'as' => 'web.',
], function () {
    Route::resources([
        'sliders' => SliderController::class,
        'faqs' => FaqController::class,
        'vouchers' => VoucherController::class,
    ]);
});
