<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Admin memakai Bootstrap 4; hindari pagination Tailwind bawaan
        // (ikon SVG-nya tampil raksasa tanpa Tailwind).
        Paginator::useBootstrapFour();
    }
}
