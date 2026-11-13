<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class ActivityHours extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Qaysi soatlarda faol';
    protected static ?string $description = 'Tashriflar kun soatlari bo‘yicha (Toshkent vaqti)';
    protected static ?int $sort = 15;
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 2];
    protected static ?string $maxHeight = '240px';

    protected function getData(): array
    {
        $b = Stats::bucket('started_at', 'hour');
        $rows = Stats::visits(Stats::range($this->filters))->select(DB::raw("$b as h"), DB::raw('COUNT(*) as n'))->groupBy('h')->pluck('n', 'h');

        return [
            'datasets' => [['label' => 'Tashriflar', 'data' => array_map(fn ($h) => (int) ($rows[$h] ?? 0), range(0, 23)), 'backgroundColor' => '#f5a524', 'borderRadius' => 4]],
            'labels' => array_map(fn ($h) => sprintf('%02d', $h), range(0, 23)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return ['plugins' => ['legend' => ['display' => false]]];
    }
}
