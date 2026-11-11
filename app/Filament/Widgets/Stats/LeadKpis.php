<?php

namespace App\Filament\Widgets\Stats;

use App\Models\Lead;
use App\Support\LeadPeriod;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadKpis extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $range = LeadPeriod::range($this->filters);
        $prev = LeadPeriod::previous($range);
        $cur = Lead::whereBetween('created_at', $range)->get(['status', 'created_at', 'contacted_at']);
        $total = $cur->count();
        $before = Lead::whereBetween('created_at', $prev)->count();
        $diff = $before ? round(($total - $before) / $before * 100) : null;
        $real = $cur->where('status', '!=', 'spam');
        $done = $real->where('status', 'done')->count();
        $conv = $real->count() ? round($done / $real->count() * 100) : 0;
        $resp = $cur->filter(fn ($l) => $l->contacted_at)->map(fn ($l) => $l->created_at->diffInMinutes($l->contacted_at));
        $avg = $resp->count() ? $resp->avg() : null;
        $spam = $cur->where('status', 'spam')->count();
        $open = Lead::whereIn('status', ['new', 'contacted'])->count();

        $days = collect();
        for ($d = $range[0]->copy(); $d->lte($range[1]) && $days->count() < 120; $d->addDay()) {
            $days->push($cur->filter(fn ($l) => $l->created_at->isSameDay($d))->count());
        }

        return [
            Stat::make('Arizalar (davr)', $total)
                ->description($diff === null ? 'Oldingi davr bilan solishtirib bo‘lmaydi' : ($diff >= 0 ? "+{$diff}% oldingi davrga nisbatan" : "{$diff}% oldingi davrga nisbatan"))
                ->descriptionIcon($diff !== null && $diff < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($diff !== null && $diff < 0 ? 'danger' : 'success')->chart($days->all()),
            Stat::make('Konversiya', $conv.'%')->description($done.' ta yakunlangan (spamsiz)')->descriptionIcon('heroicon-m-check-badge')->color('primary'),
            Stat::make('O‘rtacha javob vaqti', LeadPeriod::humanMinutes($avg))->description('Ariza kelgandan bog‘lanishgacha')->descriptionIcon('heroicon-m-clock')->color($avg !== null && $avg > 60 ? 'warning' : 'success'),
            Stat::make('Ochiq arizalar', $open)->description('Yangi va bog‘lanilgan (hozir)')->descriptionIcon('heroicon-m-inbox')->color($open ? 'warning' : 'gray'),
            Stat::make('Spam', $spam)->description($total ? round($spam / $total * 100).'% davrdagi arizalardan' : '—')->color('gray'),
        ];
    }

    protected function getColumns(): int
    {
        return 5;
    }
}
