<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Observers\ActivityObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Production'da barcha havolalar (canonical, sitemap, hreflang) https bilan — APP_URL https bo'lsa
        if ($this->app->environment('production') && str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Statistika sahifasi vidjetlari (panelda avto-topilmaydi) — lazy yuklanishi uchun Livewire'ga ro'yxatdan o'tkaziladi
        foreach (glob(app_path('Filament/Widgets/Stats/*.php')) as $file) {
            $class = 'App\\Filament\\Widgets\\Stats\\'.basename($file, '.php');
            \Livewire\Livewire::component('lead-stats.'.\Illuminate\Support\Str::kebab(class_basename($class)), $class);
        }
        // Sayt faolligi vidjetlari
        foreach (glob(app_path('Filament/Widgets/Traffic/*.php')) as $file) {
            $class = 'App\\Filament\\Widgets\\Traffic\\'.basename($file, '.php');
            if (! (new \ReflectionClass($class))->isAbstract()) {
                \Livewire\Livewire::component('site-activity.'.\Illuminate\Support\Str::kebab(class_basename($class)), $class);
            }
        }

        // "5 soat avval" kabi vaqtlar lotin alifbosida chiqsin: til o'zgargan har safar qayta o'rnatiladi
        Event::listen(LocaleUpdated::class, function ($e) {
            if ($e->locale === 'uz') {
                Carbon::setLocale('uz_Latn');
            }
        });
        if (app()->getLocale() === 'uz') {
            Carbon::setLocale('uz_Latn');
        }

        // Admin paneldagi o'zgarishlar jurnali
        foreach ([
            \App\Models\Course::class, \App\Models\Article::class, \App\Models\ArticleCategory::class, \App\Models\Service::class,
            \App\Models\ServicePlan::class, \App\Models\Teacher::class, \App\Models\Faq::class, \App\Models\Graduate::class,
            \App\Models\HeroSlide::class, \App\Models\InstagramPost::class, \App\Models\Branch::class, \App\Models\Lead::class,
            User::class, \App\Models\Icon::class,
        ] as $model) {
            $model::observe(ActivityObserver::class);
        }

        // Tizimga kirish: oxirgi kirish vaqti va jurnal
        Event::listen(Login::class, function (Login $e) {
            if ($e->user instanceof User) {
                $e->user->forceFill(['last_login_at' => now()])->saveQuietly();
                ActivityLog::create([
                    'user_id' => $e->user->id, 'event' => 'login', 'subject_type' => User::class,
                    'subject_id' => $e->user->id, 'subject_label' => $e->user->name, 'ip' => request()?->ip(),
                ]);
            }
        });
    }
}
