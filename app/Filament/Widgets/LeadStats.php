<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Course;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $perDay = collect(range(6, 0))->map(fn ($d) => Lead::whereDate('created_at', now()->subDays($d))->count())->all();
        $new = Lead::where('status', 'new')->count();

        return [
            Stat::make('Yangi arizalar', $new)->description($new ? 'Bog‘lanishni kutmoqda' : 'Hammasi ko‘rib chiqilgan')
                ->descriptionIcon($new ? 'heroicon-m-bell-alert' : 'heroicon-m-check-circle')->color($new ? 'warning' : 'success')
                ->url(\App\Filament\Resources\LeadResource::getUrl('index')),
            Stat::make('Bugun', Lead::whereDate('created_at', today())->count())->description('Oxirgi 7 kun grafigi')->chart($perDay)->color('primary'),
            Stat::make('Shu oy', Lead::where('created_at', '>=', now()->startOfMonth())->count())->description('Jami: '.Lead::count().' ta ariza')->color('info'),
            Stat::make('Saytdagi kontent', Course::count().' kurs · '.Article::published()->count().' maqola')->description('Chop etilgan')->color('gray'),
        ];
    }
}
