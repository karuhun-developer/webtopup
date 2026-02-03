<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'web',
    'as' => 'web.',
], function () {
    Route::resources([
        'sliders' => \App\Http\Controllers\Cms\Web\SliderController::class,
        'faqs' => \App\Http\Controllers\Cms\Web\FaqController::class,
    ]);
});
