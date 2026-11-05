<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Support\Tr;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Kontent';
    protected static ?string $modelLabel = 'ustoz';
    protected static ?string $pluralModelLabel = 'Ustozlar';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Tr::text('name', 'Ism familiya', true),
                Tr::text('role', 'Lavozim / yo‘nalish'),
                Tr::textarea('bio', 'Qisqacha', false, 3),
                Forms\Components\FileUpload::make('photo')->label('Rasm')->image()->disk('public')->directory('teachers')->imageEditor(),
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
            Tables\Columns\ImageColumn::make('photo')->label('')->disk('public')->circular(),
            Tables\Columns\TextColumn::make('name')->label('Ism')->formatStateUsing(fn ($record) => $record->name['uz'] ?? ''),
            Tables\Columns\TextColumn::make('role')->label('Lavozim')->formatStateUsing(fn ($record) => $record->role['uz'] ?? ''),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish'),
            Tables\Actions\EditAction::make()->label('Tahrirlash'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Ustoz')->columns(['default' => 1, 'lg' => 3])->schema([
                Info::img('photo', 'Rasm', 220),
                I\Group::make([Info::tr('name', 'Ism familiya'), Info::tr('role', 'Lavozim / yo‘nalish'), Info::tr('bio', 'Qisqacha', true)])->columnSpan(['lg' => 2]),
            ]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
