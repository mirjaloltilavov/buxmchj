<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    public function getTabs(): array
    {
        $count = fn (string $status) => Lead::where('status', $status)->count();

        return [
            'all' => Tab::make('Hammasi')->badge(Lead::count()),
            'new' => Tab::make('Yangi')->icon('heroicon-o-bell-alert')->badge($count('new') ?: null)->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'new')),
            'contacted' => Tab::make('Bog‘lanildi')->icon('heroicon-o-phone-arrow-up-right')->badge($count('contacted') ?: null)->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'contacted')),
            'done' => Tab::make('Yakunlandi')->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'done')),
            'spam' => Tab::make('Spam')->icon('heroicon-o-no-symbol')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'spam')),
        ];
    }
}
