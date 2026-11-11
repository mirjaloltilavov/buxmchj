<?php

namespace App\Filament\Widgets\Stats;

use App\Models\Lead;
use App\Support\LeadPeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LeadByPage extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Qaysi sahifadan yuborilgan';
    protected static ?int $sort = 6;
    protected static bool $isDiscovered = false;
    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $rows = Lead::whereBetween('created_at', LeadPeriod::range($this->filters))->get();
        $counts = $rows->groupBy(fn ($l) => '/'.ltrim(preg_replace('/\\s*·.*$/u', '', (string) $l->source), '/'))->map->count()->sortDesc()->take(8);

        return [
            'datasets' => [['label' => 'Arizalar', 'data' => $counts->values()->all(), 'backgroundColor' => ['#f5a524', '#2f6fe4', '#1f9d5c', '#9aa0a6', '#e5484d', '#8e4ec6', '#12a594', '#f76b15'], 'borderWidth' => 0]],
            'labels' => $counts->keys()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return ['indexAxis' => 'y', 'plugins' => ['legend' => ['display' => false]], 'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]];
    }
}
