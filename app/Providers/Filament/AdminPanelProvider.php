<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class, isSimple: false)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandName('Buxgalter MChJ')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('img/logo-mark.jpg'))
            ->font('Golos Text')
            ->colors([
                'primary' => Color::hex('#f5a524'),
                'gray' => Color::Zinc,
            ])
            ->navigationGroups([
                NavigationGroup::make('Mijozlar'),
                NavigationGroup::make('Kontent'),
                NavigationGroup::make('Bosh sahifa'),
                NavigationGroup::make('Sozlamalar'),
                NavigationGroup::make('Tizim'),
            ])
            ->navigationItems([
                NavigationItem::make('Saytni ochish')->url('/', shouldOpenInNewTab: true)->icon('heroicon-o-arrow-top-right-on-square')->sort(100),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
                        ->widgets([
                \App\Filament\Widgets\QuickActions::class,
                \App\Filament\Widgets\LeadStats::class,
                \App\Filament\Widgets\LeadsChart::class,
                \App\Filament\Widgets\LatestLeads::class,
            ])
            ->renderHook(\Filament\View\PanelsRenderHook::HEAD_END, fn () => view('filament.admin-styles'))
            ->darkMode(true)
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth('full')
            ->userMenuItems([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
