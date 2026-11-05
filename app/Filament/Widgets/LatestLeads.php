<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeads extends TableWidget
{
    protected static ?string $heading = 'Oxirgi arizalar';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 1];
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        $color = ['new' => 'warning', 'contacted' => 'info', 'done' => 'success', 'spam' => 'gray'];

        return $table->query(Lead::query()->latest()->limit(6))->paginated(false)
            ->emptyStateHeading('Hozircha arizalar yo‘q')->emptyStateIcon('heroicon-o-inbox')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Ism')->weight('bold')->description(fn ($record) => ago($record->created_at)),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->icon('heroicon-o-phone')->copyable(),
                Tables\Columns\TextColumn::make('status')->label('Holat')->badge()->formatStateUsing(fn ($state) => Lead::STATUSES[$state] ?? $state)->color(fn ($state) => $color[$state] ?? 'gray'),
            ])
            ->recordUrl(fn ($record) => LeadResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Tables\Actions\Action::make('all')->label('Hammasi')->icon('heroicon-o-arrow-right')->url(LeadResource::getUrl('index'))->color('gray'),
            ]);
    }
}
