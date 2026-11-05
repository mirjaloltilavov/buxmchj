<?php

namespace App\Filament\Resources\ArticleCategoryResource\Pages;

use App\Filament\Resources\ArticleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewArticleCategory extends ViewRecord
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Tahrirlash')->icon('heroicon-o-pencil-square'),
        ];
    }
}
