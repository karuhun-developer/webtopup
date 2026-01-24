<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'cms',
    'as' => 'cms.',
    'middleware' => ['auth', 'verified'],
], function () {
    // Dashboard Route
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');

    // PPOB Routes
    require 'cms/ppob.php';

    // Management Routes
    require 'cms/management.php';
});
