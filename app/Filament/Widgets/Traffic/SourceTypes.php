<?php

namespace App\Filament\Widgets\Traffic;

use App\Models\Visit;
use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class SourceTypes extends RankWidget
{
    protected static ?int $sort = 5;
    protected string $heading = 'Qayerdan kelishdi (turi)';
    protected string $icon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected function rows(array $range): array
    {
        $icons = ['search' => '🔎', 'social' => '💬', 'ads' => '📣', 'referral' => '🔗', 'direct' => '⌨️'];

        return Stats::visits($range)->select('source_type', DB::raw('COUNT(*) as n'))->groupBy('source_type')->orderByDesc('n')->get()
            ->map(fn ($r) => ['label' => Visit::SOURCE_TYPES[$r->source_type] ?? $r->source_type, 'icon' => $icons[$r->source_type] ?? '•', 'value' => (int) $r->n])->all();
    }
}
