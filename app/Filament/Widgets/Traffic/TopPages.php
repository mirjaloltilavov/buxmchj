<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class TopPages extends RankWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected string $heading = 'Eng ko‘p ko‘rilgan sahifalar';
    protected ?string $description = 'Sahifa ko‘rishlar, alohida tashrif buyuruvchilar va sahifada o‘rtacha o‘tirilgan vaqt';
    protected string $icon = 'heroicon-o-document-text';
    protected string $valueLabel = 'Ko‘rish';
    protected array $cols = ['Odam', 'O‘rt. vaqt'];
    protected int $limit = 15;

    protected function rows(array $range): array
    {
        return Stats::views($range)->join('visits', 'visits.id', '=', 'pageviews.visit_id')
            ->select('pageviews.path', DB::raw('COUNT(*) as n'), DB::raw('COUNT(DISTINCT visits.visitor) as u'), DB::raw('AVG(NULLIF(pageviews.duration, 0)) as t'))
            ->groupBy('pageviews.path')->orderByDesc('n')->limit(50)->get()
            ->map(fn ($r) => ['label' => Stats::pageLabel($r->path), 'hint' => $r->path, 'value' => (int) $r->n, 'extra' => [number_format($r->u, 0, '.', ' '), Stats::duration($r->t)]])->all();
    }
}
