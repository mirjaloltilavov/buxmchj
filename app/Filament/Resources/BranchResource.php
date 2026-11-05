<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Support\Icons;
use App\Filament\Support\Tr;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'filial';
    protected static ?string $pluralModelLabel = 'Filiallar';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Tr::text('name', 'Nomi', true),
                Tr::text('address', 'Manzil', true),
                Tr::text('hours', 'Ish vaqti'),
                Forms\Components\TextInput::make('phone')->label('Telefon'),
                Forms\Components\TextInput::make('map_url')->label('Xarita havolasi (Yandex/Google)')->url(),
                Forms\Components\TextInput::make('lat')->label('Kenglik (lat)')->numeric()->placeholder('41.2756')->helperText('Google Maps’da joyni o‘ng tugma bilan bosing — birinchi raqam shu.'),
                Forms\Components\TextInput::make('lng')->label('Uzunlik (lng)')->numeric()->placeholder('69.2037')->helperText('Shu yerdagi ikkinchi raqam. Xaritadagi nuqta shu koordinataga biriktiriladi.'),
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
            Tables\Columns\TextColumn::make('name')->label('Nomi')->formatStateUsing(fn ($record) => $record->name['uz'] ?? ''),
            Tables\Columns\TextColumn::make('address')->label('Manzil')->formatStateUsing(fn ($record) => $record->address['uz'] ?? ''),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish'),
            Tables\Actions\EditAction::make()->label('Tahrirlash'), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchs::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'view' => Pages\ViewBranch::route('/{record}'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
