<?php

namespace App\Filament\Resources\GraduateResource\Pages;

use App\Filament\Resources\GraduateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;




class ListGraduates extends ManageRecords
{
    protected static string $resource = GraduateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Qo‘shish')->icon('heroicon-o-plus')->slideOver()->modalWidth('3xl'),
        ];
    }
}
