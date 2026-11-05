<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Support\Icons;
use App\Filament\Support\Tr;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'savol';
    protected static ?string $pluralModelLabel = 'Savol-javoblar';
    protected static ?int $navigationSort = 7;

    public const PAGES = ['home' => 'Bosh sahifa', 'courses' => 'Kurslar', 'services' => 'Buxgalteriya xizmati', 'contact' => 'Aloqa'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\CheckboxList::make('pages')->label('Qaysi sahifalarda chiqadi')->options(self::PAGES)->columns(4)->required()->default(['home']),
                Tr::text('question', 'Savol', true),
                Tr::textarea('answer', 'Javob', true, 4),
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
            Tables\Columns\TextColumn::make('question')->label('Savol')->formatStateUsing(fn ($record) => $record->question['uz'] ?? '')->wrap(),
            Tables\Columns\TextColumn::make('pages')->label('Sahifalar')->badge()->formatStateUsing(fn ($state) => self::PAGES[$state] ?? $state),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
