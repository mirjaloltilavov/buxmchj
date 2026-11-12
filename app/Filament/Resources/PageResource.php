<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\Info;
use App\Filament\Support\Tr;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components as I;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/** Qo'shimcha sahifalar: "Biz haqimizda", "Vakansiyalar" va h.k. Menyuga "Menyular" bo'limidan qo'shiladi. */
class PageResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin', 'editor'];

    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'sahifa';
    protected static ?string $pluralModelLabel = 'Sahifalar';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Sahifa')->schema([
                Tr::text('title', 'Sarlavha', true),
                Forms\Components\TextInput::make('slug')->label('Manzil (URL)')->required()->maxLength(120)
                    ->prefix(fn () => rtrim(url('/'), '/').'/')
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->notIn(Page::RESERVED)
                    ->validationMessages([
                        'regex' => 'Faqat kichik lotin harflari, raqamlar va chiziqcha (-). Masalan: biz-haqimizda',
                        'unique' => 'Bu manzil boshqa sahifada band.',
                        'not_in' => 'Bu manzil saytning o‘z sahifasi uchun band. Boshqasini tanlang.',
                    ])
                    ->helperText('Masalan: biz-haqimizda. Bo‘sh joy va katta harf avtomatik tuzatiladi.')
                    // brauzerda tuzatiladi: "Biz Haqimizda" -> "biz-haqimizda" (serverga so'rovsiz)
                    ->extraInputAttributes(['x-on:blur' => "\$el.value = \$el.value.toLowerCase().replace(/[‘’ʻʼ'`]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+\$/g, ''); \$el.dispatchEvent(new Event('input'))"]),
            ]),
            Forms\Components\Section::make('Tuzilishi (seksiyalar)')->icon('heroicon-o-rectangle-group')
                ->description('Sahifa shu seksiyalardan yig‘iladi: saytdagi tayyor seksiyalar (FAQ, ariza formasi, ustozlar, tariflar…) yoki erkin bloklar (matn, rasm + matn, kartochkalar, raqamlar, qadamlar, CTA). Sudrab tartibini o‘zgartiring.')
                ->schema([\App\Support\Blocks::builder('sections', 'page')->default(Page::starterSections())]),
            Forms\Components\Section::make('SEO')->description('Bo‘sh qolsa, sarlavha va qisqa izoh ishlatiladi.')->collapsible()->collapsed()->schema([
                Tr::text('seo_title', 'Google sarlavhasi (title)'),
                Tr::textarea('seo_description', 'Google tavsifi (description)', false, 2),
            ]),
            Forms\Components\Section::make('Holat')->columns(2)->schema([
                Forms\Components\Toggle::make('is_active')->label('Saytda ko‘rinadi')->default(true),
                Forms\Components\TextInput::make('sort')->label('Tartib')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->reorderable('sort')->defaultSort('sort')
            ->emptyStateHeading('Hali qo‘shimcha sahifa yo‘q')
            ->emptyStateDescription('Masalan, “Biz haqimizda” yoki “Vakansiyalar” sahifasini yarating, keyin uni “Menyular” bo‘limidan header yoki footerga qo‘shing.')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Sarlavha')->formatStateUsing(fn ($record) => $record->tr('title', 'uz'))->weight('bold')
                    ->description(fn ($record) => '/'.$record->slug)->searchable(query: fn ($query, $search) => $query->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")),
                Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
                Tables\Columns\TextColumn::make('updated_at')->label('O‘zgartirilgan')->since()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')->label('Saytda ochish')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (Page $record) => $record->url('uz'), true),
                Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl'),
                Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('5xl'),
                Tables\Actions\DeleteAction::make()->label('O‘chirish'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Sahifa')->schema([
                Info::tr('title', 'Sarlavha'),
                I\TextEntry::make('slug')->label('Manzil')->formatStateUsing(fn (Page $record) => $record->url('uz'))->url(fn (Page $record) => $record->url('uz'), true)->color('primary')->icon('heroicon-o-link'),
                I\TextEntry::make('sections')->label('Seksiyalar')->listWithLineBreaks()->bulleted()->placeholder('—')
                    ->state(fn (Page $record) => collect($record->sections ?? [])->map(fn ($s) => \App\Support\Blocks::label($s['type'] ?? '').(tr($s['data']['title'] ?? null) ? ' — '.tr($s['data']['title']) : ''))->values()->all()),
            ]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPages::route('/')];
    }
}
