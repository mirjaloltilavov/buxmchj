<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Support\Icons;
use App\Filament\Support\Tr;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'kurs';
    protected static ?string $pluralModelLabel = 'Kurslar';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->columnSpanFull()->tabs([
                Forms\Components\Tabs\Tab::make('Asosiy')->schema([
                    Tr::text('title', 'Nomi', true),
                    Forms\Components\TextInput::make('slug')->label('URL (slug)')->required()->unique(ignoreRecord: true)->helperText('Masalan: buxgalteriya-noldan')->alphaDash(),
                    Tr::textarea('excerpt', 'Qisqa tavsif'),
                    Forms\Components\FileUpload::make('image')->label('Rasm')->image()->disk('public')->directory('courses')->imageEditor(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('price')->label('Narxi (so‘m, “dan”)')->numeric()->helperText('Bo‘sh qolsa pastdagi izoh chiqadi'),
                        Forms\Components\Toggle::make('is_featured')->label('Bosh sahifada katta kartochka')->inline(false),
                    ]),
                    Tr::text('price_note', 'Narx o‘rniga izoh', false),
                    Forms\Components\Repeater::make('tags')->label('Teglar (kartochkadagi)')->schema([
                        Forms\Components\Select::make('icon')->label('Ikonka')->options(Icons::options()),
                        Tr::text('text', 'Matn', true),
                        Forms\Components\Toggle::make('accent')->label('Sariq fon'),
                    ])->defaultItems(0)->collapsible()->itemLabel(fn (array $state) => $state['text']['uz'] ?? null),
                ]),
                Forms\Components\Tabs\Tab::make('Sahifa matni')->schema([
                    Tr::rich('description', 'Kurs haqida batafsil', 'courses'),
                    Forms\Components\Repeater::make('audience')->label('Kurs kimlar uchun')->schema([
                        Forms\Components\Select::make('icon')->label('Ikonka')->options(Icons::options()),
                        Tr::text('title', 'Sarlavha'),
                        Tr::textarea('text', 'Matn', false, 2),
                    ])->defaultItems(0)->collapsible()->itemLabel(fn (array $state) => $state['title']['uz'] ?? null),
                ]),
                Forms\Components\Tabs\Tab::make('Dastur')->schema([
                    Forms\Components\Repeater::make('program')->label('Modullar')->schema([
                        Tr::text('title', 'Modul nomi', true),
                        Tr::textarea('text', 'Izoh', false, 2),
                        Forms\Components\Repeater::make('topics')->label('Mavzular')->schema([Tr::text('text', 'Mavzu')])->defaultItems(0)->collapsible(),
                    ])->defaultItems(0)->collapsible()->itemLabel(fn (array $state) => $state['title']['uz'] ?? null),
                ]),
                Forms\Components\Tabs\Tab::make('Tariflar')->schema([
                    Forms\Components\Repeater::make('plans')->label('Kurs tariflari')->schema([
                        Tr::text('name', 'Nomi', true),
                        Tr::text('subtitle', 'Izoh'),
                        Tr::text('price_label', 'Narx ustidagi yozuv'),
                        Tr::text('price', 'Narx (matn)'),
                        Forms\Components\Toggle::make('highlight')->label('Ajratib ko‘rsatish'),
                        Forms\Components\Repeater::make('features')->label('Imkoniyatlar')->schema([Tr::text('text', 'Band')])->defaultItems(0)->collapsible(),
                    ])->defaultItems(0)->collapsible()->itemLabel(fn (array $state) => $state['name']['uz'] ?? null),
                ]),
                Forms\Components\Tabs\Tab::make('SEO')->schema([
                    Tr::text('seo_title', 'SEO sarlavha'),
                    Tr::textarea('seo_description', 'SEO tavsif', false, 2),
                ]),
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
            Tables\Columns\TextColumn::make('title')->label('Nomi')->formatStateUsing(fn ($record) => $record->title['uz'] ?? ''),
            Tables\Columns\TextColumn::make('price')->label('Narx')->numeric(thousandsSeparator: ' ')->suffix(' so‘m'),
            Tables\Columns\IconColumn::make('is_featured')->label('Asosiy')->boolean(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
