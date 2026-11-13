<?php

namespace App\Filament\Widgets\Traffic;

use App\Models\Visit;
use App\Support\Analytics\Stats;
use Filament\Widgets\Widget;

/** Hozir saytda: oxirgi daqiqalarda faol bo'lgan tashriflar (har 15 soniyada yangilanadi) */
class LiveNow extends Widget
{
    protected static string $view = 'filament.widgets.live-now';
    protected static ?int $sort = 1;
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $since = now()->subMinutes(Stats::LIVE_MINUTES);
        $live = Visit::where('last_seen_at', '>=', $since)->with(['views' => fn ($q) => $q->latest('id')->limit(1)])->latest('last_seen_at')->limit(30)->get();
        $today = Visit::where('started_at', '>=', now()->startOfDay());

        return [
            'count' => Visit::where('last_seen_at', '>=', $since)->count(),
            'live' => $live,
            'last30' => Visit::where('last_seen_at', '>=', now()->subMinutes(30))->count(),
            'todayPeople' => (clone $today)->distinct()->count('visitor'),
            'todayViews' => \App\Models\Pageview::where('created_at', '>=', now()->startOfDay())->count(),
        ];
    }
}
