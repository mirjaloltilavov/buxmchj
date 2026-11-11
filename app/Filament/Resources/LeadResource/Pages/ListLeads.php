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
            Actions\Action::make('exportCsv')->label('Excel (CSV) yuklab olish')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->action(fn () => \App\Support\LeadExport::download($this->getFilteredSortedTableQuery()->with('assignee')->get())),
            Actions\ActionGroup::make([
                Actions\Action::make('syncSheets')->label('Yuborilmaganlarni Google Sheets’ga')->icon('heroicon-o-table-cells')
                    ->visible(fn () => app(\App\Services\Integrations\GoogleSheets::class)->ready())
                    ->requiresConfirmation()->modalDescription(fn () => 'Jadvalga hali yozilmagan '.Lead::whereNull('sheets_synced_at')->count().' ta ariza yuboriladi (bir urinishda ko‘pi bilan '.\App\Support\LeadSync::BATCH * 10 .' ta).')
                    ->action(fn () => \App\Support\LeadSync::toSheets(Lead::whereNull('sheets_synced_at')->orderBy('id')->limit(\App\Support\LeadSync::BATCH * 10)->get())),
                Actions\Action::make('syncAmo')->label('Yuborilmaganlarni amoCRM’ga')->icon('heroicon-o-briefcase')
                    ->visible(fn () => (new \App\Services\Integrations\AmoCrm)->ready())
                    ->requiresConfirmation()->modalDescription(fn () => 'amoCRM’ga hali yuborilmagan '.Lead::whereNull('amo_lead_id')->where('status', '!=', 'spam')->count().' ta ariza (spamsiz) yuboriladi. Bir urinishda ko‘pi bilan '.\App\Support\LeadSync::BATCH.' ta.')
                    ->action(fn () => \App\Support\LeadSync::toAmo(Lead::whereNull('amo_lead_id')->where('status', '!=', 'spam')->orderBy('id')->limit(\App\Support\LeadSync::BATCH)->get())),
            ])->label('Integratsiyalar')->icon('heroicon-m-arrows-right-left')->button()->color('gray')
                ->visible(fn () => app(\App\Services\Integrations\GoogleSheets::class)->ready() || (new \App\Services\Integrations\AmoCrm)->ready()),
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
