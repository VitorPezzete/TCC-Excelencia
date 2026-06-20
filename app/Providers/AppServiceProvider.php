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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $is_open = \App\Models\Setting::where('key', 'is_store_open')->value('value') ?? '1';
                $view->with('storeIsOpen', $is_open === '1');
            } catch (\Exception $e) {
                $view->with('storeIsOpen', true);
            }
        });
    }
}
