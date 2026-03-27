<?php

use App\Http\Controllers\Cms\PPOB\ImportDigiflazzController;
use App\Http\Controllers\Cms\PPOB\PPOBBrandController;
use App\Http\Controllers\Cms\PPOB\PPOBCategoryController;
use App\Http\Controllers\Cms\PPOB\PPOBProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'ppob',
    'as' => 'ppob.',
], function () {
    Route::resources([
        'categories' => PPOBCategoryController::class,
        'brands' => PPOBBrandController::class,
        'products' => PPOBProductController::class,
    ]);

    // Additional Routes
    Route::post('brands/json-all', [PPOBBrandController::class, 'jsonAll'])->name('brands.json-all');
    Route::get('import-digiflazz', [ImportDigiflazzController::class, 'index'])->name('import-digiflazz.index');
    Route::post('import-digiflazz', [ImportDigiflazzController::class, 'sync'])->name('import-digiflazz.sync');
});
