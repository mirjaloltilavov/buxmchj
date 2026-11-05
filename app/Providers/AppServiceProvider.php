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
        // "5 soat avval" kabi vaqtlar lotin alifbosida chiqsin (admin va sayt)
        if (app()->getLocale() === 'uz') {
            \Illuminate\Support\Carbon::setLocale('uz_Latn');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
