<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\Icons;
use App\Filament\Support\Tr;
use App\Filament\Forms\CardsField;
use App\Filament\Forms\IconPicker;
use App\Models\Service;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'xizmat';
    protected static ?string $pluralModelLabel = 'Xizmatlar';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                IconPicker::make('icon')->label('Ikonka')->required()->default('calculator'),
                Tr::text('title', 'Nomi', true),
                Tr::textarea('description', 'Tavsif', false, 2),
                Forms\Components\Toggle::make('on_home')->label('Bosh sahifada ko‘rsatish')->default(true),
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
            Tables\Columns\TextColumn::make('title')->label('Nomi')->formatStateUsing(fn ($record) => $record->title['uz'] ?? ''),
            Tables\Columns\TextColumn::make('icon')->label('Ikonka')->badge(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish'),
            Tables\Actions\EditAction::make()->label('Tahrirlash'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Xizmat')->schema([
                Info::tr('title', 'Nomi'), Info::tr('description', 'Tavsif', true),
                I\Grid::make(2)->schema([Info::text('icon', 'Ikonka'), Info::bool('on_home', 'Bosh sahifada ko‘rsatiladi')]),
            ]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
