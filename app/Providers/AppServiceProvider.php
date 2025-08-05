<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Prakerin;
use App\Observers\PrakerinObserver;

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
        // Daftarkan observer untuk Prakerin
        Prakerin::observe(PrakerinObserver::class);
    }
}
