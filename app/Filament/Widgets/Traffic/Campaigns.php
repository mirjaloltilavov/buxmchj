<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Campaigns extends RankWidget
{
    protected static ?int $sort = 13;
    protected string $heading = 'Reklama kampaniyalari (UTM)';
    protected ?string $description = 'utm_campaign belgisi bilan kelganlar';
    protected string $icon = 'heroicon-o-megaphone';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->whereNotNull('utm_campaign')->select('utm_campaign', 'utm_source', DB::raw('COUNT(*) as n'))
            ->groupBy('utm_campaign', 'utm_source')->orderByDesc('n')->limit(40)->get()
            ->map(fn ($r) => ['label' => $r->utm_campaign, 'hint' => $r->utm_source, 'value' => (int) $r->n])->all();
    }
}
