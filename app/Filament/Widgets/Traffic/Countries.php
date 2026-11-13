<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Countries extends RankWidget
{
    protected static ?int $sort = 7;
    protected string $heading = 'Davlatlar';
    protected string $icon = 'heroicon-o-flag';
    protected string $valueLabel = 'Odam';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('country', DB::raw('COUNT(DISTINCT visitor) as u'), DB::raw('COUNT(*) as n'))->groupBy('country')->orderByDesc('u')->get()
            ->map(fn ($r) => ['label' => Stats::country($r->country), 'icon' => Stats::flag($r->country), 'value' => (int) $r->u, 'hint' => number_format($r->n, 0, '.', ' ').' tashrif'])->all();
    }
}
