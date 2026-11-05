<?php

namespace App\Filament\Support;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

/** Faqat o'qish uchun ko'rinish (Ko'rish sahifasi) yordamchilari. */
class Info
{
    /** Ikki tilli matn: UZ va RU yonma-yon */
    public static function tr(string $name, string $label, bool $long = false): Grid
    {
        return Grid::make(2)->schema([
            TextEntry::make("{$name}.uz")->label("{$label} (UZ)")->placeholder('—')->prose($long),
            TextEntry::make("{$name}.ru")->label("{$label} (RU)")->placeholder('—')->prose($long),
        ]);
    }

    public static function text(string $name, string $label, ?string $icon = null): TextEntry
    {
        return TextEntry::make($name)->label($label)->placeholder('—')->icon($icon);
    }

    public static function link(string $name, string $label): TextEntry
    {
        return TextEntry::make($name)->label($label)->placeholder('—')->url(fn ($state) => $state ?: null, true)->color('primary')->icon('heroicon-o-link');
    }

    public static function bool(string $name, string $label): IconEntry
    {
        return IconEntry::make($name)->label($label)->boolean();
    }

    public static function img(string $name, string $label, int $h = 160): ImageEntry
    {
        return ImageEntry::make($name)->label($label)->disk('public')->height($h)->extraImgAttributes(['style' => 'border-radius:12px;object-fit:cover'])
            ->defaultImageUrl(fn ($record) => null);
    }

    /** Standart "Holat" bloki: tartib + faol */
    public static function status(bool $active = true): Section
    {
        return Section::make('Holat')->columns(4)->schema(array_filter([
            self::text('sort', 'Tartib raqami', 'heroicon-o-arrows-up-down'),
            $active ? self::bool('is_active', 'Saytda ko‘rinadi') : null,
            TextEntry::make('updated_at')->label('Oxirgi o‘zgarish')->formatStateUsing(fn ($state) => ago($state))->tooltip(fn ($record) => $record->updated_at?->format('d.m.Y H:i')),
        ]));
    }

    public static function list(string $name, string $label, array $schema, int $cols = 1): RepeatableEntry
    {
        return RepeatableEntry::make($name)->label($label)->columns($cols)->schema($schema)->placeholder('Kiritilmagan');
    }
}
