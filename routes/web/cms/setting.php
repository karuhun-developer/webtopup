<?php

use App\Http\Controllers\Cms\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'setting',
    'as' => 'setting.',
], function () {
    Route::get('settings', [SettingController::class, 'index'])->name('index');
    Route::put('settings', [SettingController::class, 'save'])->name('save');
});
