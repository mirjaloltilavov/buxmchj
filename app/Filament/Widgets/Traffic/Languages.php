<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Languages extends RankWidget
{
    protected static ?int $sort = 14;
    protected string $heading = 'Sayt tili';
    protected ?string $description = 'Qaysi tildagi sahifadan kirishgan';
    protected string $icon = 'heroicon-o-language';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('lang', DB::raw('COUNT(*) as n'))->groupBy('lang')->orderByDesc('n')->get()
            ->map(fn ($r) => ['label' => $r->lang === 'ru' ? 'Ruscha' : 'O‘zbekcha', 'icon' => $r->lang === 'ru' ? '🇷🇺' : '🇺🇿', 'value' => (int) $r->n])->all();
    }
}
