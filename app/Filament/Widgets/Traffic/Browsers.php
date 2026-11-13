<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Browsers extends RankWidget
{
    protected static ?int $sort = 9;
    protected string $heading = 'Brauzerlar';
    protected string $icon = 'heroicon-o-window';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('browser', DB::raw('COUNT(*) as n'))->groupBy('browser')->orderByDesc('n')->get()
            ->map(fn ($r) => ['label' => 'browser' === 'device' ? Stats::deviceLabel($r->browser) : ($r->browser ?: 'Boshqa'), 'icon' => 'browser' === 'device' ? (['mobile' => '📱', 'tablet' => '📟', 'desktop' => '💻'][$r->browser] ?? '•') : null, 'value' => (int) $r->n])->all();
    }
}
