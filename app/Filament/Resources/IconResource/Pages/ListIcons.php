<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;




class ListIcons extends ManageRecords
{
    protected static string $resource = IconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Qo‘shish')->icon('heroicon-o-plus')->slideOver()->modalWidth('3xl')
            ->mutateFormDataUsing(function (array $data): array {
                $data['group'] = 'custom';
                unset($data['svg_file']);

                return $data;
            }),
        ];
    }
}
