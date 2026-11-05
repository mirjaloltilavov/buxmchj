<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstagramPostResource\Pages;
use App\Filament\Support\Icons;
use App\Filament\Support\Tr;
use App\Models\InstagramPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstagramPostResource extends Resource
{
    protected static ?string $model = InstagramPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationGroup = 'Bosh sahifa';
    protected static ?string $modelLabel = 'post';
    protected static ?string $pluralModelLabel = 'Instagram postlar';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\FileUpload::make('image')->label('Rasm (kvadrat)')->image()->disk('public')->directory('instagram')->imageEditor()->imageCropAspectRatio('1:1')->required(),
                Forms\Components\TextInput::make('url')->label('Post havolasi')->url(),
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
            Tables\Columns\ImageColumn::make('image')->label('')->disk('public')->height(64),
            Tables\Columns\TextColumn::make('url')->label('Havola')->limit(50),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish'),
            Tables\Actions\EditAction::make()->label('Tahrirlash'), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstagramPosts::route('/'),
            'create' => Pages\CreateInstagramPost::route('/create'),
            'view' => Pages\ViewInstagramPost::route('/{record}'),
            'edit' => Pages\EditInstagramPost::route('/{record}/edit'),
        ];
    }
}
