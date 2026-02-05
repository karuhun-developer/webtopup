<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'cms',
    'as' => 'cms.',
    'middleware' => ['auth', 'verified'],
], function () {
    // Auto redirect to dashboard
    Route::get('/', fn () => to_route('cms.dashboard'))->name('home');

    // Dashboard Route
    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');

    // Web Routes
    require 'cms/web.php';

    // PPOB Routes
    require 'cms/ppob.php';

    // Management Routes
    require 'cms/management.php';

    // Logs
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('logs')->middleware('auth', 'role:superadmin');
});
