<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Filament\Resources\IconResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIcon extends CreateRecord
{
    protected static string $resource = IconResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['group'] = 'custom';
        unset($data['svg_file']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
