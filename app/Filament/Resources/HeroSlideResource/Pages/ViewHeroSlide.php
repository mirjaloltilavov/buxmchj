<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use App\Filament\Resources\HeroSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHeroSlide extends ViewRecord
{
    protected static string $resource = HeroSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Tahrirlash')->icon('heroicon-o-pencil-square'),
        ];
    }
}
