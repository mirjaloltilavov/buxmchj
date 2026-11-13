<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Illuminate\Support\Facades\DB;

class Devices extends RankWidget
{
    protected static ?int $sort = 8;
    protected string $heading = 'Qurilmalar';
    protected string $icon = 'heroicon-o-device-phone-mobile';

    protected function rows(array $range): array
    {
        return Stats::visits($range)->select('device', DB::raw('COUNT(*) as n'))->groupBy('device')->orderByDesc('n')->get()
            ->map(fn ($r) => ['label' => 'device' === 'device' ? Stats::deviceLabel($r->device) : ($r->device ?: 'Boshqa'), 'icon' => 'device' === 'device' ? (['mobile' => '📱', 'tablet' => '📟', 'desktop' => '💻'][$r->device] ?? '•') : null, 'value' => (int) $r->n])->all();
    }
}
