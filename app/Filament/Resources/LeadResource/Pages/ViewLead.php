<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('call')->label('Qo‘ng‘iroq qilish')->icon('heroicon-o-phone')->color('primary')
                ->url(fn () => 'tel:'.preg_replace('/[^0-9+]/', '', $this->record->phone)),
            Actions\Action::make('contacted')->label('Bog‘lanildi')->icon('heroicon-o-phone-arrow-up-right')->color('info')
                ->visible(fn () => $this->record->status === 'new')
                ->action(function () { $this->record->update(['status' => 'contacted']); $this->refreshFormData(['status']); Notification::make()->title('“Bog‘lanildi” deb belgilandi')->success()->send(); }),
            Actions\Action::make('done')->label('Yakunlandi')->icon('heroicon-o-check-circle')->color('success')
                ->visible(fn () => ! in_array($this->record->status, ['done', 'spam'], true))
                ->action(function () { $this->record->update(['status' => 'done']); $this->refreshFormData(['status']); Notification::make()->title('Ariza yakunlandi')->success()->send(); }),
            Actions\EditAction::make()->label('Izoh / tahrirlash')->icon('heroicon-o-pencil-square')->color('gray'),
        ];
    }
}
