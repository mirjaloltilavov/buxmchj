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
        // "5 soat avval" kabi vaqtlar lotin alifbosida chiqsin: til o'zgargan har safar qayta o'rnatiladi
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Foundation\Events\LocaleUpdated::class, function ($e) {
            if ($e->locale === 'uz') {
                \Illuminate\Support\Carbon::setLocale('uz_Latn');
            }
        });
        if (app()->getLocale() === 'uz') {
            \Illuminate\Support\Carbon::setLocale('uz_Latn');
        }
    }
}
