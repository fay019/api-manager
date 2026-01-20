<?php

namespace App\Providers;

use App\Models\Promo;
use App\Observers\PromoObserver;
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
        Promo::observe(PromoObserver::class);
    }
}
