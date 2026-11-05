<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Forms\CardsField;
use App\Filament\Forms\IconPicker;
use App\Filament\Support\Tr;
use App\Models\Course;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
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
                    CardsField::make('tags')->label('Teglar (kartochkadagi)')->kind('tag')->cols(4)->itemName('teg')->addLabel('Teg qo‘shish')->drawerWidth('md')->itemSchema(fn () => [
                        IconPicker::make('icon')->label('Ikonka'),
                        Tr::text('text', 'Matn', true),
                        Forms\Components\Toggle::make('accent')->label('Sariq fon'),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Hero')->schema([
                    Tr::text('meta.badge', 'Yuqoridagi belgi (masalan: Offline · onlayn · 4 oydan 1 yilgacha)'),
                    CardsField::make('meta.stats')->label('Raqamlar (hero pastida)')->kind('stat')->cols(4)->max(4)->itemName('raqam')->addLabel('Raqam qo‘shish')->drawerWidth('md')->itemSchema(fn () => [
                        IconPicker::make('icon')->label('Ikonka'),
                        Tr::text('value', 'Qiymat (26–40, 1C, Diplom...)'),
                        Tr::text('label', 'Izoh'),
                    ]),
                    Tr::text('meta.price_caption', 'Narx kartochkasidagi izoh (bir martada to‘lansa)'),
                    Tr::text('meta.dark_card_sub', 'Qora kartochka: kichik matn (bo‘sh — “Toshkent · onlayn”)'),
                    Tr::text('meta.dark_card_title', 'Qora kartochka: sarlavha (bo‘sh — “Yangi guruhga qabul ochiq”)'),
                ]),
                Forms\Components\Tabs\Tab::make('Kimlar uchun')->schema([
                    Tr::textarea('meta.audience_lead', 'Blok izohi', false, 2),
                    CardsField::make('audience')->label('Kartochkalar (1-si sariq, oxirgisi qora)')->kind('audience')->cols(4)->itemName('kartochka')->addLabel('Kartochka qo‘shish')->drawerWidth('lg')->itemSchema(fn () => [
                        IconPicker::make('icon')->label('Ikonka'),
                        Tr::text('title', 'Sarlavha', true),
                        Tr::textarea('text', 'Matn', false, 3),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Dastur')->schema([
                    Tr::textarea('meta.program_lead', 'Dastur izohi', false, 2),
                    CardsField::make('program')->label('Modullar')->kind('program')->cols(3)->itemName('modul')->addLabel('Modul qo‘shish')->drawerWidth('xl')->itemSchema(fn () => [
                        IconPicker::make('icon')->label('Ikonka'),
                        Forms\Components\FileUpload::make('image')->label('Rasm')->image()->disk('public')->directory('courses')->imageEditor(),
                        Tr::text('title', 'Modul nomi', true),
                        Tr::textarea('text', 'Izoh', false, 3),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Natijalar')->schema([
                    Tr::textarea('meta.outcomes_lead', '“Kurs yakunida siz” izohi', false, 2),
                    Forms\Components\FileUpload::make('meta.outcomes_image')->label('Rasm')->image()->disk('public')->directory('courses')->imageEditor(),
                    CardsField::make('outcomes')->label('Natijalar')->kind('outcome')->cols(2)->itemName('band')->addLabel('Natija qo‘shish')->drawerWidth('md')->itemSchema(fn () => [Tr::text('text', 'Band', true)]),
                    Tr::rich('description', 'Qo‘shimcha matn (ixtiyoriy)', 'courses'),
                ]),
                Forms\Components\Tabs\Tab::make('Tariflar')->schema([
                    Forms\Components\Toggle::make('meta.plans_on_request')->label('Tariflar o‘rniga “Narxi so‘rov bo‘yicha” blokini ko‘rsatish'),
                    Tr::text('meta.plans_lead', 'Izoh (masalan: bir martada to‘lasangiz — 20% chegirma)'),
                    CardsField::make('plans')->label('Kurs tariflari')->kind('plan')->cols(3)->itemName('tarif')->addLabel('Tarif qo‘shish')->drawerWidth('xl')->itemSchema(fn () => [
                        Tr::text('name', 'Nomi', true),
                        Tr::text('subtitle', 'Izoh'),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('price_once')->label('Bir martada (so‘m)')->numeric(),
                            Forms\Components\TextInput::make('price_split')->label('Bo‘lib to‘lansa (so‘m)')->numeric(),
                        ]),
                        Forms\Components\Toggle::make('highlight')->label('Ajratib ko‘rsatish (sariq)'),
                        CardsField::make('features')->label('Imkoniyatlar')->kind('feature')->cols(1)->itemName('band')->addLabel('Imkoniyat qo‘shish')->drawerWidth('md')->itemSchema(fn () => [Tr::text('text', 'Band', true)]),
                    ]),
                    Tr::text('meta.plans_note', 'Pastdagi izoh (* ...)'),
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
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish'),
            Tables\Actions\EditAction::make()->label('Tahrirlash'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Kurs')->columns(['default' => 1, 'lg' => 3])->schema([
                Info::img('image', 'Rasm', 220),
                I\Group::make([
                    Info::tr('title', 'Nomi'), Info::tr('excerpt', 'Qisqa tavsif', true),
                    I\Grid::make(3)->schema([
                        I\TextEntry::make('price')->label('Narxi')->formatStateUsing(fn ($state) => $state ? number_format((int) $state, 0, '', ' ').' so‘mdan' : '—'),
                        Info::text('slug', 'URL (slug)', 'heroicon-o-link'),
                        Info::bool('is_featured', 'Bosh sahifada katta karta'),
                    ]),
                ])->columnSpan(['lg' => 2]),
            ]),
            I\Section::make('Hero raqamlari')->collapsible()->schema([Info::cards('meta.stats', 'Raqamlar', 'stat', 4)]),
            I\Section::make('Kimlar uchun')->collapsible()->collapsed()->schema([Info::cards('audience', 'Kartochkalar', 'audience', 4)]),
            I\Section::make('Dasturi (modullar)')->collapsible()->collapsed()->schema([Info::cards('program', 'Modullar', 'program', 3)]),
            I\Section::make('Natijalar')->collapsible()->collapsed()->schema([Info::cards('outcomes', 'Natijalar', 'outcome', 2)]),
            I\Section::make('Tariflar')->collapsible()->schema([
                Info::bool('meta.plans_on_request', 'Tariflar o‘rniga “Narxi so‘rov bo‘yicha” ko‘rsatiladi'),
                Info::cards('plans', 'Kurs tariflari', 'plan', 3),
            ]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
