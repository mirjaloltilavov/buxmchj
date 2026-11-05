<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $modelLabel = 'ariza';
    protected static ?string $pluralModelLabel = 'Arizalar';
    protected static ?int $navigationSort = -1;

    public static function getNavigationBadge(): ?string
    {
        $n = Lead::where('status', 'new')->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')->label('Ism')->required(),
                Forms\Components\TextInput::make('phone')->label('Telefon')->required(),
                Forms\Components\Select::make('interest')->label('Qiziqish')->options(Lead::INTERESTS),
                Forms\Components\Select::make('status')->label('Holat')->options(Lead::STATUSES)->required(),
                Forms\Components\Textarea::make('message')->label('Xabar')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('note')->label('Izoh (faqat admin uchun)')->columnSpanFull(),
                Forms\Components\Placeholder::make('source')->label('Sahifa')->content(fn ($record) => $record?->source ?: '—'),
                Forms\Components\Placeholder::make('created_at')->label('Yuborilgan')->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('created_at')->label('Sana')->dateTime('d.m.Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('name')->label('Ism')->searchable(),
            Tables\Columns\TextColumn::make('phone')->label('Telefon')->searchable()->url(fn ($record) => 'tel:'.preg_replace('/[^0-9+]/', '', $record->phone)),
            Tables\Columns\TextColumn::make('interest')->label('Qiziqish')->formatStateUsing(fn ($state) => Lead::INTERESTS[$state] ?? '—')->badge(),
            Tables\Columns\TextColumn::make('message')->label('Xabar')->limit(40)->toggleable(),
            Tables\Columns\TextColumn::make('source')->label('Sahifa')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\SelectColumn::make('status')->label('Holat')->options(Lead::STATUSES)->selectablePlaceholder(false),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->label('Holat')->options(Lead::STATUSES),
            Tables\Filters\SelectFilter::make('interest')->label('Qiziqish')->options(Lead::INTERESTS),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
