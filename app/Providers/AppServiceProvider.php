<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Exclude large catalog payloads before Debugbar registers its Inertia listeners.
        config()->set('debugbar.except', array_values(array_unique([
            ...config('debugbar.except', []),
            'cms/ppob/import-product',
        ])));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
