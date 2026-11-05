<?php

namespace App\Filament\Resources\InstagramPostResource\Pages;

use App\Filament\Resources\InstagramPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInstagramPost extends ViewRecord
{
    protected static string $resource = InstagramPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Tahrirlash')->icon('heroicon-o-pencil-square'),
        ];
    }
}
