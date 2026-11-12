<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ListPages extends ManageRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('menus')->label('Menyuga qo‘shish')->icon('heroicon-o-bars-3')->color('gray')->url(fn () => \App\Filament\Pages\Menus::getUrl()),
            Actions\CreateAction::make()->label('Yangi sahifa')->icon('heroicon-o-plus')->slideOver()->modalWidth('5xl')
                ->mutateFormDataUsing(function (array $data) {
                    $data['slug'] = Str::slug($data['slug'] ?: ($data['title']['uz'] ?? ''));

                    return $data;
                }),
        ];
    }
}
