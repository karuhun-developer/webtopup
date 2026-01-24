<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'ppob',
    'as' => 'ppob.',
], function () {
    Route::resources([
        'categories' => \App\Http\Controllers\Cms\PPOB\PPOBCategoryController::class,
        'brands' => \App\Http\Controllers\Cms\PPOB\PPOBBrandController::class,
    ]);
});
