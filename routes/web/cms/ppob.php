<?php

use App\Http\Controllers\Cms\PPOB\ImportProductController;
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
    Route::get('import-product', [ImportProductController::class, 'index'])->name('import-product.index');
    Route::post('import-product', [ImportProductController::class, 'sync'])->name('import-product.sync');
    Route::redirect('import-digiflazz', '/cms/ppob/import-product');
});
