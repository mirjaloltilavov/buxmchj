<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class ExitPages extends RankWidget
{
    protected static ?int $sort = 12;
    protected string $heading = 'Chiqib ketish sahifalari';
    protected ?string $description = 'Tashrif qaysi sahifada tugagan';
    protected string $icon = 'heroicon-o-arrow-up-on-square';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('exit_path', DB::raw('COUNT(*) as n'))->groupBy('exit_path')->orderByDesc('n')->limit(40)->get()
            ->map(fn ($r) => ['label' => Stats::pageLabel((string) $r->exit_path), 'hint' => $r->exit_path, 'value' => (int) $r->n])->all();
    }
}
