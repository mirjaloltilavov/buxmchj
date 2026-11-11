<?php

namespace App\Filament\Widgets\Stats;

use App\Models\Lead;
use App\Support\LeadPeriod;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class StaffPerformance extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Xodimlar natijasi (davr bo‘yicha)';
    protected static ?int $sort = 7;
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $range = LeadPeriod::range($this->filters);
        $leads = fn ($u) => Lead::where('assigned_to', $u->id)->whereBetween('created_at', $range);

        return $table->query(Lead::staff())->paginated(false)
            ->emptyStateHeading('Arizalar bilan ishlaydigan xodim yo‘q')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Xodim')->weight('bold')->description(fn ($record) => $record->roleLabel()),
                Tables\Columns\TextColumn::make('assigned')->label('Biriktirilgan')->state(fn ($record) => $leads($record)->count()),
                Tables\Columns\TextColumn::make('open')->label('Ochiq')->state(fn ($record) => $leads($record)->whereIn('status', ['new', 'contacted'])->count())->color('warning'),
                Tables\Columns\TextColumn::make('done')->label('Yakunlangan')->state(fn ($record) => $leads($record)->where('status', 'done')->count())->color('success'),
                Tables\Columns\TextColumn::make('conv')->label('Konversiya')->state(function ($record) use ($leads) {
                    $all = $leads($record)->where('status', '!=', 'spam')->count();

                    return $all ? round($leads($record)->where('status', 'done')->count() / $all * 100).'%' : '—';
                }),
                Tables\Columns\TextColumn::make('resp')->label('O‘rtacha javob')->state(function ($record) use ($leads) {
                    $m = $leads($record)->whereNotNull('contacted_at')->get(['created_at', 'contacted_at'])->map(fn ($l) => $l->created_at->diffInMinutes($l->contacted_at));

                    return LeadPeriod::humanMinutes($m->count() ? $m->avg() : null);
                }),
            ]);
    }
}
