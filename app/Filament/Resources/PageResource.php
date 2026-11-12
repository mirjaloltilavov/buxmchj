<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\Tr;
use App\Models\Page;
use App\Support\Blocks;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components as I;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Saytning BARCHA sahifalari bitta joyda: tizim sahifalari (Bosh, Kurslar, Xizmat, Aloqa, Blog,
 * kurs va maqola shablonlari) va admin yaratgan qo'shimcha sahifalar. Har biri seksiyalardan yig'iladi.
 */
class PageResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin', 'editor'];

    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationGroup = 'Sahifalar';
    protected static ?string $navigationLabel = 'Sahifalar';
    protected static ?string $modelLabel = 'sahifa';
    protected static ?string $pluralModelLabel = 'Sahifalar';
    protected static ?int $navigationSort = 1;

    public static function canDelete($record): bool
    {
        return ! $record->isSystem() && static::roleAllowed();
    }

    public static function form(Form $form): Form
    {
        $sys = fn (?Page $record) => (bool) $record?->isSystem();

        return $form->schema([
            Forms\Components\Section::make('Sahifa')->schema([
                Forms\Components\Placeholder::make('sys_note')->hiddenLabel()->visible($sys)
                    ->content(fn (?Page $record) => new \Illuminate\Support\HtmlString('<b>Tizim sahifasi.</b> Manzili o‘zgarmaydi va o‘chirilmaydi. '
                        .($record?->publicUrl('uz') ? 'Saytda: <a href="'.e($record->publicUrl('uz')).'" target="_blank" style="color:#d48806">'.e($record->publicUrl('uz')).'</a>' : 'Bu shablon — har bir '.($record?->system === 'course' ? 'kurs' : 'maqola').' sahifasi shu tuzilishda chiqadi.'))),
                Tr::text('title', 'Nomi', true),
                Forms\Components\TextInput::make('slug')->label('Manzil (URL)')->maxLength(120)
                    ->hidden($sys)->required(fn (?Page $record) => ! $record?->isSystem())
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
                ->description('Sahifa shu seksiyalardan yig‘iladi. “Seksiya qo‘shish” — saytdagi tayyor seksiyalar (FAQ, ariza formasi, ustozlar, tariflar…) yoki erkin bloklar (matn, rasm + matn, kartochkalar, raqamlar, qadamlar, CTA). Sudrab tartibini o‘zgartiring.')
                ->schema(fn (?Page $record) => [
                    Blocks::builder('sections', $record?->system ?: 'page')->default($record ? null : Page::starterSections()),
                ]),
            Forms\Components\Section::make('SEO (Google)')->description('Bo‘sh qolsa, sahifa nomi va matnidan olinadi.')->collapsible()->collapsed()
                ->hidden(fn (?Page $record) => in_array($record?->system, ['course', 'article'], true))
                ->schema([
                    Tr::text('seo_title', 'Google sarlavhasi (title)'),
                    Tr::textarea('seo_description', 'Google tavsifi (description)', false, 2),
                ]),
            Forms\Components\Section::make('Holat')->columns(2)->hidden($sys)->schema([
                Forms\Components\Toggle::make('is_active')->label('Saytda ko‘rinadi')->default(true),
                Forms\Components\TextInput::make('sort')->label('Tartib')->numeric()->default(0),
            ]),
        ]);
    }

    /** Tahrirlashda: tizim sahifasining tuzilishi saqlanmagan bo'lsa — hozirgi (standart) tuzilish */
    public static function fillDefaults(array $data, Page $record): array
    {
        if ($record->isSystem() && empty($data['sections'])) {
            $data['sections'] = Blocks::defaultLayout($record->system);
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\EditAction::class)->defaultSort('sort')
            ->modifyQueryUsing(fn ($query) => $query->orderByRaw('system is null'))
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Sahifa')->formatStateUsing(fn (Page $record) => $record->tr('title', 'uz'))->weight('bold')
                    ->icon(fn (Page $record) => ['home' => 'heroicon-o-home', 'courses' => 'heroicon-o-academic-cap', 'services' => 'heroicon-o-briefcase', 'contact' => 'heroicon-o-phone', 'blog' => 'heroicon-o-newspaper', 'course' => 'heroicon-o-book-open', 'article' => 'heroicon-o-document-text'][$record->system] ?? 'heroicon-o-document-plus')
                    ->description(fn (Page $record) => $record->publicUrl('uz') ? parse_url($record->publicUrl('uz'), PHP_URL_PATH) ?: '/' : 'Shablon: har bir '.($record->system === 'course' ? 'kurs' : 'maqola').' uchun')
                    ->searchable(query: fn ($query, $search) => $query->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('system')->label('Turi')->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Tizim sahifasi' : 'Qo‘shimcha')->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->state(fn (Page $record) => $record->system ?: '0'),
                Tables\Columns\TextColumn::make('sections')->label('Seksiyalar')
                    ->state(fn (Page $record) => count($record->sections ?: ($record->isSystem() ? Blocks::defaultLayout($record->system) : [])).' ta'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Faol')->disabled(fn (Page $record) => $record->isSystem()),
                Tables\Columns\TextColumn::make('updated_at')->label('O‘zgartirilgan')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('system')->label('Turi')->nullable()->placeholder('Hammasi')
                    ->trueLabel('Tizim sahifalari')->falseLabel('Qo‘shimcha sahifalar')
                    ->queries(true: fn ($q) => $q->whereNotNull('system'), false: fn ($q) => $q->whereNull('system')),
            ])
            ->actions([
                Tables\Actions\Action::make('open')->label('Saytda ochish')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (Page $record) => $record->publicUrl('uz'), true)->visible(fn (Page $record) => (bool) $record->publicUrl('uz')),
                Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('5xl')
                    ->mutateRecordDataUsing(fn (array $data, Page $record) => static::fillDefaults($data, $record)),
                Tables\Actions\Action::make('reset')->label('Standartga qaytarish')->icon('heroicon-o-arrow-uturn-left')->color('gray')
                    ->visible(fn (Page $record) => $record->isSystem() && ! empty($record->sections))
                    ->requiresConfirmation()->modalDescription('Sahifa tuzilishi dastlabki (dizayndagi) holatga qaytadi. Seksiyalarning matnlari o‘chmaydi.')
                    ->action(fn (Page $record) => $record->update(['sections' => null])),
                Tables\Actions\DeleteAction::make()->label('O‘chirish'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Sahifa')->schema([
                I\TextEntry::make('title.uz')->label('Nomi'),
                I\TextEntry::make('sections')->label('Seksiyalar')->listWithLineBreaks()->bulleted()->placeholder('—')
                    ->state(fn (Page $record) => collect($record->sections ?: ($record->isSystem() ? Blocks::defaultLayout($record->system) : []))
                        ->map(fn ($s) => Blocks::label($s['type'] ?? '').(tr($s['data']['title'] ?? null) ? ' — '.tr($s['data']['title']) : ''))->values()->all()),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPages::route('/')];
    }
}
