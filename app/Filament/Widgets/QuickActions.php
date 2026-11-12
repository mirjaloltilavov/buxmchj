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
            'links' => array_values(array_filter([
                \App\Filament\Resources\LeadResource::canViewAny() ? ['Arizalar', 'heroicon-o-inbox-arrow-down', \App\Filament\Resources\LeadResource::getUrl('index')] : null,
                \App\Filament\Resources\ArticleResource::canViewAny() ? ['Maqolalar', 'heroicon-o-document-plus', \App\Filament\Resources\ArticleResource::getUrl('index')] : null,
                \App\Filament\Resources\CourseResource::canViewAny() ? ['Kurslar', 'heroicon-o-academic-cap', \App\Filament\Resources\CourseResource::getUrl('index')] : null,
                \App\Filament\Resources\PageResource::canViewAny() ? ['Sahifalar', 'heroicon-o-rectangle-group', \App\Filament\Resources\PageResource::getUrl('index')] : null,
                \App\Filament\Pages\SiteSettings::canAccess() ? ['Sayt sozlamalari', 'heroicon-o-cog-6-tooth', \App\Filament\Pages\SiteSettings::getUrl()] : null,
                \App\Filament\Resources\UserResource::canViewAny() ? ['Foydalanuvchilar', 'heroicon-o-users', \App\Filament\Resources\UserResource::getUrl('index')] : null,
                ['Saytni ochish', 'heroicon-o-arrow-top-right-on-square', url('/'), true],
            ])),
            'role' => auth()->user()?->roleLabel(),
        ];
    }
}
