<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class EntryPages extends RankWidget
{
    protected static ?int $sort = 11;
    protected string $heading = 'Kirish sahifalari';
    protected ?string $description = 'Saytga birinchi qaysi sahifadan kirishgan';
    protected string $icon = 'heroicon-o-arrow-down-on-square';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('landing', DB::raw('COUNT(*) as n'))->groupBy('landing')->orderByDesc('n')->limit(40)->get()
            ->map(fn ($r) => ['label' => Stats::pageLabel((string) $r->landing), 'hint' => $r->landing, 'value' => (int) $r->n])->all();
    }
}
