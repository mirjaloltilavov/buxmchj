<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Lead;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ManageRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Qo‘shish')->icon('heroicon-o-plus')->slideOver()->modalWidth('3xl'),
        ];
    }

    public function getTabs(): array
    {
        $count = fn (string $status) => Lead::where('status', $status)->count();

        return [
            'all' => Tab::make('Hammasi')->badge(Lead::count()),
            'mine' => Tab::make('Menga biriktirilgan')->icon('heroicon-o-user-circle')->badge(Lead::where('assigned_to', auth()->id())->whereIn('status', ['new', 'contacted'])->count() ?: null)->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', auth()->id())),
            'new' => Tab::make('Yangi')->icon('heroicon-o-bell-alert')->badge($count('new') ?: null)->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'new')),
            'contacted' => Tab::make('Bog‘lanildi')->icon('heroicon-o-phone-arrow-up-right')->badge($count('contacted') ?: null)->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'contacted')),
            'done' => Tab::make('Yakunlandi')->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'done')),
            'spam' => Tab::make('Spam')->icon('heroicon-o-no-symbol')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'spam')),
        ];
    }
}
