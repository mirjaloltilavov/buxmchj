<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Ikki tilli (UZ/RU) maydonlar. Qiymat JSON ustunda {"uz": "...", "ru": "..."} bo'lib saqlanadi.
 */
class Tr
{
    public static function text(string $name, string $label, bool $required = false, ?int $max = 255): Grid
    {
        return Grid::make(2)->schema([
            TextInput::make("{$name}.uz")->label("{$label} (UZ)")->required($required)->maxLength($max),
            TextInput::make("{$name}.ru")->label("{$label} (RU)")->maxLength($max),
        ]);
    }

    public static function textarea(string $name, string $label, bool $required = false, int $rows = 3): Grid
    {
        return Grid::make(2)->schema([
            Textarea::make("{$name}.uz")->label("{$label} (UZ)")->required($required)->rows($rows),
            Textarea::make("{$name}.ru")->label("{$label} (RU)")->rows($rows),
        ]);
    }

    public static function rich(string $name, string $label, ?string $uploadDir = 'editor'): Tabs
    {
        $editor = fn (string $lang) => RichEditor::make("{$name}.{$lang}")
            ->label($label)
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsDirectory($uploadDir)
            ->toolbarButtons(['h2', 'h3', 'bold', 'italic', 'underline', 'link', 'bulletList', 'orderedList', 'blockquote', 'attachFiles', 'undo', 'redo']);

        return Tabs::make($label)->tabs([
            Tabs\Tab::make('O‘zbekcha')->schema([$editor('uz')]),
            Tabs\Tab::make('Русский')->schema([$editor('ru')]),
        ])->columnSpanFull();
    }

    /** Jadvalda ko'rsatish uchun o'zbekcha qiymat. */
    public static function col(?array $value): ?string
    {
        return $value['uz'] ?? null;
    }
}
