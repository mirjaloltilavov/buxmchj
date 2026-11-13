<?php

namespace App\Filament\Widgets\Traffic;

use App\Support\Analytics\Stats;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

/**
 * Reyting ro'yxati: nom — chiziq (ulush) — son. Qo'shimcha ustunlar ($cols) bo'lishi mumkin.
 * rows(): [['label' => ..., 'value' => int, 'icon' => '🇺🇿', 'hint' => '/path', 'extra' => [..]], ...]
 */
abstract class RankWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.rank';
    protected static bool $isDiscovered = false;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 1;

    protected string $heading = '';
    protected ?string $description = null;
    protected string $icon = 'heroicon-o-chart-bar';
    protected string $valueLabel = 'Tashrif';
    protected int $limit = 10;
    /** @var array<int, string> qo'shimcha ustun sarlavhalari */
    protected array $cols = [];

    abstract protected function rows(array $range): array;

    protected function getViewData(): array
    {
        $rows = collect($this->rows(Stats::range($this->filters)))->filter(fn ($r) => $r['value'] > 0)->values();
        $total = max(1, $rows->sum('value'));
        $max = max(1, $rows->max('value') ?? 1);

        return [
            'heading' => $this->heading, 'description' => $this->description, 'icon' => $this->icon, 'valueLabel' => $this->valueLabel, 'cols' => $this->cols,
            'rows' => $rows->take($this->limit)->map(fn ($r) => $r + ['share' => round($r['value'] / $total * 100, 1), 'bar' => round($r['value'] / $max * 100)])->all(),
            'more' => max(0, $rows->count() - $this->limit),
        ];
    }
}
