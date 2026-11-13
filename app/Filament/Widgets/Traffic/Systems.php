<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Systems extends RankWidget
{
    protected static ?int $sort = 10;
    protected string $heading = 'Operatsion tizimlar';
    protected string $icon = 'heroicon-o-cpu-chip';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('os', DB::raw('COUNT(*) as n'))->groupBy('os')->orderByDesc('n')->get()
            ->map(fn ($r) => ['label' => 'os' === 'device' ? Stats::deviceLabel($r->os) : ($r->os ?: 'Boshqa'), 'icon' => 'os' === 'device' ? (['mobile' => '📱', 'tablet' => '📟', 'desktop' => '💻'][$r->os] ?? '•') : null, 'value' => (int) $r->n])->all();
    }
}
