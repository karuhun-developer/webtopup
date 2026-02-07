<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'setting',
    'as' => 'setting.',
], function () {
    Route::get('settings', [\App\Http\Controllers\Cms\Setting\SettingController::class, 'index'])->name('index');
    Route::put('settings', [\App\Http\Controllers\Cms\Setting\SettingController::class, 'save'])->name('save');
});
