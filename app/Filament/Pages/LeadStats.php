<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Stats;
use App\Support\LeadPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

/** Arizalar statistikasi: davr filtri bilan grafiklar va jadvallar */
class LeadStats extends Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'lead-stats';
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Mijozlar';
    protected static ?string $navigationLabel = 'Statistika';
    protected static ?string $title = 'Arizalar statistikasi';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin', 'admin', 'manager');
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(3)->schema([
                Forms\Components\Select::make('period')->label('Davr')->options(LeadPeriod::OPTIONS)->default('30')->selectablePlaceholder(false)->live(),
                Forms\Components\DatePicker::make('from')->label('Dan')->native(false)->visible(fn (Forms\Get $get) => $get('period') === 'custom'),
                Forms\Components\DatePicker::make('to')->label('Gacha')->native(false)->visible(fn (Forms\Get $get) => $get('period') === 'custom'),
            ]),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            Stats\LeadKpis::class, Stats\LeadTrend::class, Stats\LeadByInterest::class, Stats\LeadByStatus::class,
            Stats\LeadByChannel::class, Stats\LeadByPage::class, Stats\StaffPerformance::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return ['default' => 1, 'md' => 2];
    }
}
