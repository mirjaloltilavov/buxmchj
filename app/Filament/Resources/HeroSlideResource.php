<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Filament\Support\Icons;
use App\Filament\Support\Tr;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Bosh sahifa';
    protected static ?string $modelLabel = 'slayd';
    protected static ?string $pluralModelLabel = 'Hero slaydlar';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Asosiy')->schema([
                Forms\Components\Select::make('theme')->label('Mavzu')->options(['courses' => 'Kurslar (yashil nuqta)', 'services' => 'Xizmat (ko‘k nuqta)'])->default('courses')->required(),
                Tr::text('tab_label', 'Pastdagi tab nomi', true),
                Tr::text('chip', 'Yuqoridagi belgi (chip)'),
                Tr::textarea('title', 'Sarlavha', true, 2),
                Tr::textarea('lead', 'Qisqa matn', false, 2),
                Forms\Components\FileUpload::make('image')->label('Portret rasm (600×840)')->image()->disk('public')->directory('hero')->imageEditor(),
            ]),
            Forms\Components\Section::make('Tugmalar')->columns(2)->schema([
                Tr::text('primary_label', 'Asosiy tugma'),
                Forms\Components\TextInput::make('primary_url')->label('Asosiy tugma havolasi')->placeholder('#consultation — ariza formasiga'),
                Tr::text('secondary_label', 'Ikkinchi tugma'),
                Forms\Components\TextInput::make('secondary_url')->label('Ikkinchi tugma havolasi')->placeholder('/courses'),
            ]),
            Forms\Components\Section::make('Suzuvchi kartochkalar')->collapsible()->schema([
                Forms\Components\Fieldset::make('Oq kartochka (yuqorida)')->schema([
                    Forms\Components\Select::make('cards.badge_icon')->label('Ikonka')->options(Icons::options())->default('monitor'),
                    Tr::text('cards.badge_title', 'Sarlavha'),
                    Tr::text('cards.badge_sub', 'Izoh'),
                ])->columns(1),
                Forms\Components\Fieldset::make('Ro‘yxatli kartochka')->schema([
                    Tr::text('cards.list_title', 'Sarlavha'),
                    Tr::text('cards.list_counter', 'Hisoblagich (masalan 2 / 4)'),
                    Forms\Components\Repeater::make('cards.list_items')->label('Bandlar (oxirgisi kulrang ko‘rinadi)')->schema([Tr::text('text', 'Band')])->defaultItems(0)->collapsible(),
                ])->columns(1),
                Forms\Components\Fieldset::make('Qora kartochka (pastda)')->schema([
                    Tr::text('cards.dark_sub', 'Kichik matn'),
                    Tr::text('cards.dark_title', 'Sarlavha'),
                    Forms\Components\TextInput::make('cards.dark_url')->label('Havola'),
                ])->columns(1),
            ]),
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('sort')->label('Tartib')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Faol')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort')->defaultSort('sort')->columns([
            Tables\Columns\ImageColumn::make('image')->label('')->disk('public')->height(48),
            Tables\Columns\TextColumn::make('tab_label')->label('Slayd')->formatStateUsing(fn ($record) => $record->tab_label['uz'] ?? ''),
            Tables\Columns\TextColumn::make('title')->label('Sarlavha')->formatStateUsing(fn ($record) => $record->title['uz'] ?? '')->wrap()->limit(60),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
