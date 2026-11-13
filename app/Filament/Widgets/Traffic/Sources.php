<?php

namespace App\Filament\Widgets\Traffic;

use App\Models\Visit;
use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Sources extends RankWidget
{
    protected static ?int $sort = 6;
    protected string $heading = 'Manbalar';
    protected ?string $description = 'Google, Instagram, Telegram, boshqa saytlar…';
    protected string $icon = 'heroicon-o-globe-alt';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('source_type', 'source', DB::raw('COUNT(*) as n'))->groupBy('source_type', 'source')->orderByDesc('n')->limit(40)->get()
            ->map(fn ($r) => ['label' => $r->source ?: 'To‘g‘ridan-to‘g‘ri (havolasiz)', 'hint' => Visit::SOURCE_TYPES[$r->source_type] ?? null, 'value' => (int) $r->n])->all();
    }
}
