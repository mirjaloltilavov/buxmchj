<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsChart extends ChartWidget
{
    protected static ?string $heading = 'Arizalar dinamikasi';
    protected static ?string $description = 'Oxirgi 30 kun';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 1];
    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($d) => now()->subDays($d));

        return [
            'datasets' => [[
                'label' => 'Arizalar',
                'data' => $days->map(fn ($d) => Lead::whereDate('created_at', $d)->count())->all(),
                'borderColor' => '#f5a524',
                'backgroundColor' => 'rgba(245,165,36,.18)',
                'fill' => true,
                'tension' => .35,
            ]],
            'labels' => $days->map(fn ($d) => $d->format('d.m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return ['plugins' => ['legend' => ['display' => false]], 'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]];
    }

    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin', 'admin', 'manager');
    }
}
