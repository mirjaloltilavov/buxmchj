<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduateResource\Pages;
use App\Filament\Support\Tr;
use App\Models\Graduate;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GraduateResource extends Resource
{
    protected static ?string $model = Graduate::class;
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'bitiruvchi';
    protected static ?string $pluralModelLabel = 'Bitiruvchilar fikri';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Tr::text('name', 'Ism familiya', true),
                Tr::text('subtitle', 'Guruh va ish joyi'),
                Forms\Components\FileUpload::make('photo')->label('Rasm (vertikal, 360×600)')->image()->disk('public')->directory('graduates')->imageEditor(),
                Forms\Components\TextInput::make('video_url')->label('Video havolasi (YouTube)')->url(),
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
            Tables\Columns\ImageColumn::make('photo')->label('')->disk('public')->height(56),
            Tables\Columns\TextColumn::make('name')->label('Ism')->formatStateUsing(fn ($record) => $record->name['uz'] ?? ''),
            Tables\Columns\TextColumn::make('subtitle')->label('Guruh / ish joyi')->formatStateUsing(fn ($record) => $record->subtitle['uz'] ?? ''),
            Tables\Columns\IconColumn::make('video_url')->label('Video')->boolean(fn ($state) => filled($state)),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl'),
            Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('3xl'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Bitiruvchi')->columns(['default' => 1, 'lg' => 3])->schema([
                Info::img('photo', 'Rasm', 260),
                I\Group::make([Info::tr('name', 'Ism familiya'), Info::tr('subtitle', 'Guruh va ish joyi'), Info::link('video_url', 'Video havolasi')])->columnSpan(['lg' => 2]),
            ]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListGraduates::route('/')];
    }
}
