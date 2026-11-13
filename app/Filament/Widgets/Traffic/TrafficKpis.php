<?php

namespace App\Filament\Widgets\Traffic;

use App\Models\Lead;
use App\Support\Analytics\Stats;
use App\Support\LeadPeriod;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrafficKpis extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;
    protected static bool $isDiscovered = false;

    protected function getColumns(): int
    {
        return 4;
    }

    private function calc(array $range): array
    {
        $v = Stats::visits($range);
        $visits = (clone $v)->count();
        $people = (clone $v)->distinct()->count('visitor');
        $views = Stats::views($range)->count();
        $bounce = (clone $v)->where('pageviews', '<=', 1)->count();
        $avg = (clone $v)->avg('duration');
        $newP = (clone $v)->where('is_new', true)->distinct()->count('visitor');
        $leads = Lead::whereBetween('created_at', $range)->where('status', '!=', 'spam')->count();

        return compact('visits', 'people', 'views', 'bounce', 'avg', 'newP', 'leads');
    }

    private function diff(float $cur, float $prev, bool $lowerIsBetter = false): array
    {
        if ($prev <= 0) {
            return ['Oldingi davr bilan solishtirib bo‘lmaydi', null, 'gray'];
        }
        $d = round(($cur - $prev) / $prev * 100);
        $good = $lowerIsBetter ? $d <= 0 : $d >= 0;

        return [($d >= 0 ? '+' : '').$d.'% oldingi davrga nisbatan', $d >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down', $good ? 'success' : 'danger'];
    }

    protected function getStats(): array
    {
        $range = Stats::range($this->filters);
        $c = $this->calc($range);
        $p = $this->calc(LeadPeriod::previous($range));
        $fmt = fn ($n) => number_format($n, 0, '.', ' ');
        $pct = fn ($a, $b) => $b ? round($a / $b * 100, 1) : 0;
        $stat = function (string $label, string $value, float $cur, float $prev, ?string $help = null, bool $lower = false) {
            [$desc, $icon, $color] = $this->diff($cur, $prev, $lower);

            return Stat::make($label, $value)->description($help ? $help.' · '.$desc : $desc)->descriptionIcon($icon)->color($color);
        };

        return [
            $stat('Tashrif buyuruvchilar', $fmt($c['people']), $c['people'], $p['people'], 'alohida odamlar'),
            $stat('Tashriflar', $fmt($c['visits']), $c['visits'], $p['visits'], 'sessiyalar'),
            $stat('Sahifa ko‘rishlar', $fmt($c['views']), $c['views'], $p['views'], $c['visits'] ? round($c['views'] / $c['visits'], 1).' sahifa / tashrif' : null),
            $stat('O‘rtacha tashrif vaqti', Stats::duration($c['avg']), (float) $c['avg'], (float) $p['avg']),
            $stat('Bitta sahifada chiqib ketish', $pct($c['bounce'], $c['visits']).'%', $pct($c['bounce'], $c['visits']), $pct($p['bounce'], $p['visits']), 'bounce rate', true),
            $stat('Yangi tashrif buyuruvchilar', $pct($c['newP'], $c['people']).'%', $pct($c['newP'], $c['people']), $pct($p['newP'], $p['people']), $fmt($c['newP']).' kishi'),
            $stat('Arizalar', $fmt($c['leads']), $c['leads'], $p['leads'], 'spamsiz'),
            $stat('Arizaga aylanish', $pct($c['leads'], $c['people']).'%', $pct($c['leads'], $c['people']), $pct($p['leads'], $p['people']), 'ariza / tashrif buyuruvchi'),
        ];
    }
}
