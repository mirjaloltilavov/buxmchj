<?php

namespace App\Filament\Widgets\Stats;

use App\Models\Lead;
use App\Support\LeadPeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LeadTrend extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Arizalar dinamikasi';
    protected static ?int $sort = 2;
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        [$from, $to] = LeadPeriod::range($this->filters);
        $rows = Lead::whereBetween('created_at', [$from, $to])->get(['created_at', 'status']);
        $byWeek = $from->diffInDays($to) > 92;
        $labels = $all = $done = [];
        for ($d = $from->copy(); $d->lte($to); $byWeek ? $d->addWeek() : $d->addDay()) {
            $end = $byWeek ? $d->copy()->addDays(6)->endOfDay() : $d->copy()->endOfDay();
            $set = $rows->filter(fn ($l) => $l->created_at->between($d, $end));
            $labels[] = $d->format($byWeek ? 'd.m' : 'd.m');
            $all[] = $set->count();
            $done[] = $set->where('status', 'done')->count();
        }

        return [
            'datasets' => [
                ['label' => 'Hamma arizalar', 'data' => $all, 'borderColor' => '#f5a524', 'backgroundColor' => 'rgba(245,165,36,.15)', 'fill' => true, 'tension' => .35],
                ['label' => 'Yakunlangan', 'data' => $done, 'borderColor' => '#1f9d5c', 'backgroundColor' => 'rgba(31,157,92,.1)', 'fill' => false, 'tension' => .35],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return ['scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]]];
    }
}
