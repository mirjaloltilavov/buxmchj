<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\ArticleCategoryResource\Pages;
use App\Filament\Support\Tr;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleCategoryResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin', 'editor'];

    protected static ?string $model = ArticleCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'kategoriya';
    protected static ?string $pluralModelLabel = 'Maqola kategoriyalari';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Tr::text('name', 'Nomi', true),
                Forms\Components\TextInput::make('slug')->label('URL (slug)')->required()->unique(ignoreRecord: true)->alphaDash(),
                Forms\Components\TextInput::make('sort')->label('Tartib')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->reorderable('sort')->defaultSort('sort')->columns([
            Tables\Columns\TextColumn::make('name')->label('Nomi')->formatStateUsing(fn ($record) => $record->name['uz'] ?? ''),
            Tables\Columns\TextColumn::make('slug'),
            Tables\Columns\TextColumn::make('articles_count')->label('Maqolalar')->counts('articles'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl'),
            Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('3xl'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Kategoriya')->schema([
                Info::tr('name', 'Nomi'), Info::text('slug', 'URL (slug)', 'heroicon-o-link'),
            ]),
            Info::status(false),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListArticleCategories::route('/')];
    }
}
