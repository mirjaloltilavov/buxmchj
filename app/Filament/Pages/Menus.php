<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasSettingBlocks;
use App\Filament\Support\Tr;
use App\Support\Menu;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

/** Header va footer menyulari, tugma va footer matnlari */
class Menus extends Page implements HasForms, HasActions
{
    use HasSettingBlocks;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Sahifalar';
    protected static ?string $navigationLabel = 'Menyular';
    protected static ?string $title = 'Menyular (header va footer)';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'menus';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin', 'admin', 'editor');
    }

    protected function hint(): ?string
    {
        return 'Saytning yuqori menyusi (header), pastki qismi (footer) havolalari va matnlari. Havolani sudrab tartibini o‘zgartirasiz. Yangi sahifa avval “Sahifalar” bo‘limida yaratiladi, keyin shu yerda menyuga qo‘shiladi.';
    }

    protected function groupIcons(): array
    {
        return ['Header (yuqori menyu)' => 'heroicon-o-bars-3', 'Footer (pastki qism)' => 'heroicon-o-bars-3-bottom-left'];
    }

    protected function defaultFor(string $name): mixed
    {
        if (str_starts_with($name, 'texts.')) {
            return \App\Support\TextDefaults::for($name);
        }

        return Menu::defaults()[$name] ?? null;
    }

    /** Bitta havola: nomi, qayerga, (boshqa havola), #bo'lim, yangi oynada */
    public static function linkSchema(): array
    {
        return [
            Tr::text('label', 'Nomi', true),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('link')->label('Qayerga olib boradi')->options(fn () => Menu::linkOptions())->required()->live(),
                Forms\Components\TextInput::make('anchor')->label('Sahifadagi bo‘lim (ixtiyoriy)')->prefix('#')->placeholder('plans')
                    ->helperText('Masalan, xizmat sahifasidagi tariflarga: plans'),
            ]),
            Forms\Components\TextInput::make('url')->label('Havola')->placeholder('https://… yoki /sahifa')->required()
                ->visible(fn (Forms\Get $get) => $get('link') === 'url'),
            Forms\Components\Toggle::make('new_tab')->label('Yangi oynada ochilsin'),
        ];
    }

    private static function links(string $name, string $label, string $add = 'Havola qo‘shish'): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)->label($label)->schema(self::linkSchema())
            ->addActionLabel($add)->reorderableWithDragAndDrop()->collapsible()->collapsed()->cloneable()
            ->itemLabel(fn (array $state) => ($state['label']['uz'] ?? null) ?: 'Yangi havola')
            ->defaultItems(0);
    }

    protected function blocks(): array
    {
        return [
            'Header (yuqori menyu)' => [
                'header' => ['title' => 'Header menyusi', 'icon' => 'heroicon-o-bars-3', 'description' => 'Saytning yuqorisidagi havolalar (telefonda — ochiladigan menyu).', 'paths' => ['menu_header'], 'schema' => fn () => [
                    self::links('menu_header', 'Havolalar')->minItems(1)->maxItems(8)->helperText('Kompyuterda 5–6 tadan oshmagani ma’qul — sig‘masa, telefon menyusiga o‘tadi.'),
                ]],
                'header_texts' => ['title' => 'Header tugmalari', 'icon' => 'heroicon-o-cursor-arrow-rays', 'paths' => ['texts.header'], 'schema' => fn () => [
                    Tr::text('texts.header.cta', 'Asosiy tugma (kompyuterda)'),
                    Tr::text('texts.header.mobile_cta', 'Asosiy tugma (telefon menyusida)'),
                ]],
            ],
            'Footer (pastki qism)' => [
                'footer_cols' => ['title' => 'Footer ustunlari', 'icon' => 'heroicon-o-view-columns', 'description' => 'Sarlavhali havola ustunlari. “Avtomatik” — kurslar yoki ijtimoiy tarmoqlar o‘zi qo‘shiladi.', 'paths' => ['menu_footer'], 'schema' => fn () => [
                    Forms\Components\Repeater::make('menu_footer')->label('Ustunlar')->maxItems(5)->reorderableWithDragAndDrop()->collapsible()->addActionLabel('Ustun qo‘shish')
                        ->itemLabel(fn (array $state) => ($state['title']['uz'] ?? null) ?: 'Yangi ustun')
                        ->schema([
                            Tr::text('title', 'Ustun sarlavhasi', true),
                            Forms\Components\Select::make('auto')->label('Avtomatik qo‘shilsin')->placeholder('Yo‘q — faqat quyidagi havolalar')
                                ->options(['courses' => 'Barcha faol kurslar', 'socials' => 'Ijtimoiy tarmoqlar (sozlamalardan)']),
                            self::links('links', 'Havolalar'),
                        ]),
                ]],
                'footer_bottom' => ['title' => 'Eng pastki havolalar', 'icon' => 'heroicon-o-minus', 'description' => 'Mualliflik huquqi yonidagi havolalar.', 'paths' => ['menu_bottom'], 'schema' => fn () => [
                    self::links('menu_bottom', 'Havolalar'),
                ]],
                'footer_texts' => ['title' => 'Footer matnlari', 'icon' => 'heroicon-o-chat-bubble-bottom-center-text', 'paths' => ['texts.footer'], 'schema' => fn () => [
                    Tr::text('texts.footer.title1', 'Katta sarlavha (1-qator)'),
                    Tr::text('texts.footer.title2', 'Katta sarlavha (2-qator)'),
                    Tr::textarea('texts.footer.lead', 'Sarlavha ostidagi matn', false, 2),
                    Tr::text('texts.footer.about', 'Logotip ostidagi matn'),
                    Tr::text('texts.footer.copyright', 'Mualliflik huquqi matni'),
                ]],
            ],
        ];
    }
}
