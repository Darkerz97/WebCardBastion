<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\CartService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.public', function ($view): void {
            $view->with('cartItemCount', app(CartService::class)->count());
        });

        View::composer(['layouts.public', 'layouts.auth', 'store.*', 'account.*'], function ($view): void {
            $view->with('siteSettings', Schema::hasTable('site_settings') ? SiteSetting::current() : null);
        });
    }
}
