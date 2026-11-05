<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Support\Tr;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'maqola';
    protected static ?string $pluralModelLabel = 'Maqolalar';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->columns(3)->schema([
            Forms\Components\Group::make()->columnSpan(2)->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('title.uz')->label('Sarlavha (UZ)')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get, ?Article $record) => $record ? null : $set('slug', Str::slug(str_replace(['‘', '’', "'"], '', (string) $state)))),
                    Forms\Components\TextInput::make('title.ru')->label('Sarlavha (RU)')->maxLength(255),
                    Forms\Components\TextInput::make('slug')->label('URL (slug)')->required()->unique(ignoreRecord: true)->alphaDash(),
                    Tr::textarea('excerpt', 'Qisqa tavsif', false, 2),
                ]),
                Tr::rich('body', 'Maqola matni', 'articles'),
                Forms\Components\Section::make('SEO')->collapsed()->schema([
                    Tr::text('seo_title', 'SEO sarlavha'),
                    Tr::textarea('seo_description', 'SEO tavsif', false, 2),
                ]),
            ]),
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\FileUpload::make('cover')->label('Muqova rasmi')->image()->disk('public')->directory('articles')->imageEditor(),
                    Forms\Components\Select::make('article_category_id')->label('Kategoriya')->relationship('category', 'slug')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['uz'] ?? $record->slug)->preload(),
                    Forms\Components\DateTimePicker::make('published_at')->label('Chop etilgan sana')->default(now())->native(false),
                    Forms\Components\TextInput::make('read_minutes')->label('O‘qish vaqti (daqiqa)')->numeric()->default(5),
                    Forms\Components\TextInput::make('author.uz')->label('Muallif (UZ)')->placeholder('Buxgalter jamoasi'),
                    Forms\Components\TextInput::make('author.ru')->label('Muallif (RU)')->placeholder('Команда Buxgalter'),
                    Forms\Components\TagsInput::make('tags.uz')->label('Teglar (UZ)')->placeholder('YaTT, Soliq…'),
                    Forms\Components\TagsInput::make('tags.ru')->label('Teglar (RU)'),
                    Forms\Components\Toggle::make('is_featured')->label('Tanlangan maqola'),
                    Forms\Components\Toggle::make('is_active')->label('Faol')->default(true),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('published_at', 'desc')->columns([
            Tables\Columns\ImageColumn::make('cover')->label('')->disk('public')->height(48),
            Tables\Columns\TextColumn::make('title')->label('Sarlavha')->formatStateUsing(fn ($record) => $record->title['uz'] ?? '')->wrap()
                ->searchable(query: fn ($query, $search) => $query->where('title', 'like', "%{$search}%")),
            Tables\Columns\TextColumn::make('category.name')->label('Kategoriya')->formatStateUsing(fn ($record) => $record->category?->name['uz'] ?? '—')->badge(),
            Tables\Columns\TextColumn::make('published_at')->label('Sana')->date('d.m.Y')->sortable(),
            Tables\Columns\IconColumn::make('is_featured')->label('Tanlangan')->boolean(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->filters([
            Tables\Filters\SelectFilter::make('article_category_id')->label('Kategoriya')->relationship('category', 'slug')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['uz'] ?? $record->slug),
        ])->actions([
            Tables\Actions\Action::make('view')->label('Ko‘rish')->icon('heroicon-o-eye')->url(fn ($record) => route('article', $record->slug))->openUrlInNewTab(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
