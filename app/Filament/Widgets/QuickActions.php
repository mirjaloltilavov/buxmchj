<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $name = auth()->user()?->name;
        $h = (int) now()->format('H');

        return [
            'greeting' => ($h < 12 ? 'Xayrli tong' : ($h < 18 ? 'Xayrli kun' : 'Xayrli kech')).($name ? ', '.$name : ''),
            'links' => [
                ['Maqola qo‘shish', 'heroicon-o-document-plus', \App\Filament\Resources\ArticleResource::getUrl('create')],
                ['Kurslar', 'heroicon-o-academic-cap', \App\Filament\Resources\CourseResource::getUrl('index')],
                ['Sahifa matnlari', 'heroicon-o-language', \App\Filament\Pages\PageTexts::getUrl()],
                ['Sayt sozlamalari', 'heroicon-o-cog-6-tooth', \App\Filament\Pages\SiteSettings::getUrl()],
                ['Saytni ochish', 'heroicon-o-arrow-top-right-on-square', url('/'), true],
            ],
        ];
    }
}
