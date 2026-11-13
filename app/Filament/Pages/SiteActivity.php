<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Traffic;
use App\Support\Analytics\GeoIp;
use App\Support\LeadPeriod;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

/** Sayt faolligi: kim, qayerdan, qaysi sahifalarni ko'rdi, qaysi davlatdan, hozir saytda nechta odam */
class SiteActivity extends Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'site-activity';
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'Mijozlar';
    protected static ?string $navigationLabel = 'Sayt faolligi';
    protected static ?string $title = 'Sayt faolligi';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin', 'admin', 'manager');
    }

    public function getSubheading(): ?string
    {
        return GeoIp::available() ? null : 'Davlatlar bazasi hali yuklanmagan — “Davlatlar bazasini yangilash” tugmasini bir marta bosing.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('geoip')->label('Davlatlar bazasini yangilash')->icon('heroicon-o-globe-europe-africa')->color('gray')
                ->visible(fn () => auth()->user()?->hasRole('super_admin', 'admin'))
                ->requiresConfirmation()->modalHeading('Davlatlar bazasini yangilash')
                ->modalDescription('DB-IP (db-ip.com) bepul “IP → davlat” bazasi yuklab olinadi. Bir necha soniya davom etadi. Oyiga bir marta yangilash kifoya.')
                ->action(function () {
                    @set_time_limit(300);
                    @ini_set('memory_limit', '512M');
                    [$ok, $msg] = GeoIp::update();
                    $n = Notification::make()->title($ok ? 'Baza yangilandi' : 'Yangilanmadi')->body($msg);
                    ($ok ? $n->success() : $n->danger()->persistent())->send();
                }),
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(3)->schema([
                Forms\Components\Select::make('period')->label('Davr')->options(LeadPeriod::OPTIONS)->default('7')->selectablePlaceholder(false)->live(),
                Forms\Components\DatePicker::make('from')->label('Dan')->native(false)->visible(fn (Forms\Get $get) => $get('period') === 'custom'),
                Forms\Components\DatePicker::make('to')->label('Gacha')->native(false)->visible(fn (Forms\Get $get) => $get('period') === 'custom'),
            ]),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            Traffic\LiveNow::class, Traffic\TrafficKpis::class, Traffic\TrafficTrend::class, Traffic\TopPages::class,
            Traffic\SourceTypes::class, Traffic\Sources::class, Traffic\Countries::class,
            Traffic\Devices::class, Traffic\Browsers::class, Traffic\Systems::class,
            Traffic\EntryPages::class, Traffic\ExitPages::class, Traffic\Campaigns::class,
            Traffic\Languages::class, Traffic\ActivityHours::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return ['default' => 1, 'md' => 2, 'xl' => 3];
    }
}
