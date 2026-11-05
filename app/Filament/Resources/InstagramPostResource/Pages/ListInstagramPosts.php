<?php

namespace App\Filament\Resources\InstagramPostResource\Pages;

use App\Filament\Resources\InstagramPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;




class ListInstagramPosts extends ManageRecords
{
    protected static string $resource = InstagramPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Qo‘shish')->icon('heroicon-o-plus')->slideOver()->modalWidth('3xl'),
        ];
    }
}
