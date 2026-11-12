<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasSettingBlocks;
use App\Support\Blocks;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

/** Sahifa konstruktori: har bir sayt sahifasi qaysi seksiyalardan, qaysi tartibda yig'ilishi */
class PageLayouts extends Page implements HasForms, HasActions
{
    use HasSettingBlocks;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationGroup = 'Bosh sahifa';
    protected static ?string $navigationLabel = 'Sahifa tuzilishi';
    protected static ?string $title = 'Sahifa tuzilishi (konstruktor)';
    protected static ?int $navigationSort = 0;
    protected static ?string $slug = 'page-layouts';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin', 'admin', 'editor');
    }

    protected function hint(): ?string
    {
        return 'Har bir sahifa seksiyalardan yig‘iladi. “Tahrirlash” → seksiya qo‘shish, sudrab tartibini o‘zgartirish, yashirish yoki o‘chirish. Tayyor seksiyalarning matnlari “Sahifa matnlari”da, erkin bloklarning matni shu yerning o‘zida yoziladi.';
    }

    protected function groupIcons(): array
    {
        return ['Sayt sahifalari' => 'heroicon-o-rectangle-group'];
    }

    protected function emptyLabel(): string
    {
        return 'Seksiya yo‘q';
    }

    protected function defaultFor(string $name): mixed
    {
        return str_starts_with($name, 'layouts.') ? Blocks::defaultLayout(substr($name, 8)) : null;
    }

    protected function blocks(): array
    {
        $icons = ['home' => 'heroicon-o-home', 'courses' => 'heroicon-o-academic-cap', 'services' => 'heroicon-o-briefcase', 'contact' => 'heroicon-o-phone',
            'blog' => 'heroicon-o-newspaper', 'course' => 'heroicon-o-book-open', 'article' => 'heroicon-o-document-text'];
        $urls = ['home' => '/', 'courses' => '/courses', 'services' => '/services', 'contact' => '/contact', 'blog' => '/blog', 'course' => '/courses/…', 'article' => '/articles/…'];
        $out = [];
        foreach (Blocks::PAGES as $key => $title) {
            $out[$key] = [
                'title' => $title, 'icon' => $icons[$key] ?? 'heroicon-o-document', 'description' => 'Manzil: '.$urls[$key],
                'paths' => ['layouts.'.$key], 'width' => '5xl',
                'schema' => fn () => [Blocks::builder('layouts.'.$key, $key)],
            ];
        }

        return ['Sayt sahifalari' => $out];
    }
}
