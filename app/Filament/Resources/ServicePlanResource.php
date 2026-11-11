<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\ServicePlanResource\Pages;
use App\Filament\Support\Tr;
use App\Filament\Forms\CardsField;
use App\Filament\Forms\IconPicker;
use App\Models\ServicePlan;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServicePlanResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin', 'editor'];

    protected static ?string $model = ServicePlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'tarif';
    protected static ?string $pluralModelLabel = 'Xizmat tariflari';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Tr::text('name', 'Nomi', true),
                Tr::text('subtitle', 'Kimlar uchun'),
                Forms\Components\TextInput::make('price')->label('Oylik narx (so‘m)')->numeric(),
                Forms\Components\Toggle::make('is_highlighted')->label('Ajratib ko‘rsatish (qora kartochka)'),
                CardsField::make('features')->label('Nimalar kiradi')->kind('feature')->cols(2)->itemName('band')->addLabel('Band qo‘shish')->drawerWidth('md')->itemSchema(fn () => [Tr::text('text', 'Band', true)]),
            ]),
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('sort')->label('Tartib')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Faol')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->reorderable('sort')->defaultSort('sort')->columns([
            Tables\Columns\TextColumn::make('name')->label('Nomi')->formatStateUsing(fn ($record) => $record->name['uz'] ?? ''),
            Tables\Columns\TextColumn::make('price')->label('Narx')->numeric(thousandsSeparator: ' ')->suffix(' so‘m'),
            Tables\Columns\IconColumn::make('is_highlighted')->label('Ajratilgan')->boolean(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl'),
            Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('3xl'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Tarif')->schema([
                Info::tr('name', 'Nomi'), Info::tr('subtitle', 'Kimlar uchun'),
                I\Grid::make(2)->schema([
                    I\TextEntry::make('price')->label('Oylik narx')->money('UZS', locale: 'uz')->formatStateUsing(fn ($state) => number_format((int) $state, 0, '', ' ').' so‘m'),
                    Info::bool('is_highlighted', 'Ajratib ko‘rsatilgan (qora kartochka)'),
                ]),
            ]),
            I\Section::make('Nimalar kiradi')->schema([Info::cards('features', 'Bandlar', 'feature', 2)]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListServicePlans::route('/')];
    }
}
