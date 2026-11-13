<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class TrafficTrend extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Tashriflar dinamikasi';
    protected static ?int $sort = 3;
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        [$from, $to] = Stats::range($this->filters);
        $hourly = $from->diffInHours($to) <= 48;
        $unit = $hourly ? 'hour' : 'day';
        $bv = Stats::bucket('started_at', $unit);
        $bp = Stats::bucket('created_at', $unit);
        $people = Stats::visits([$from, $to])->select(DB::raw("$bv as b"), DB::raw('COUNT(DISTINCT visitor) as n'))->groupBy('b')->pluck('n', 'b');
        $views = Stats::views([$from, $to])->select(DB::raw("$bp as b"), DB::raw('COUNT(*) as n'))->groupBy('b')->pluck('n', 'b');
        $labels = [];
        $a = [];
        $c = [];
        if ($hourly) {
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $a[] = (int) ($people[$h] ?? 0);
                $c[] = (int) ($views[$h] ?? 0);
            }
        } else {
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $k = $d->toDateString();
                $labels[] = $d->format('d.m');
                $a[] = (int) ($people[$k] ?? 0);
                $c[] = (int) ($views[$k] ?? 0);
            }
        }

        return [
            'datasets' => [
                ['label' => 'Tashrif buyuruvchilar', 'data' => $a, 'borderColor' => '#f5a524', 'backgroundColor' => 'rgba(245,165,36,.15)', 'fill' => true, 'tension' => .35],
                ['label' => 'Sahifa ko‘rishlar', 'data' => $c, 'borderColor' => '#2f6fe4', 'backgroundColor' => 'transparent', 'tension' => .35],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
