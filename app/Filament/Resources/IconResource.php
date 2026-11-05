<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IconResource\Pages;
use App\Models\Icon;
use App\Support\IconSvg;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class IconResource extends Resource
{
    protected static ?string $model = Icon::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Sozlamalar';
    protected static ?string $navigationLabel = 'Ikonkalar kutubxonasi';
    protected static ?string $modelLabel = 'ikonka';
    protected static ?string $pluralModelLabel = 'Ikonkalar';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Yangi ikonka')->description('SVG kodini qo‘ying yoki .svg fayl yuklang. Ranglar avtomatik saytdagi rangga moslanadi.')->columns(2)->schema([
                Forms\Components\TextInput::make('label')->label('Nomi (o‘zingiz uchun)')->required()->maxLength(60)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => $get('name') ? null : $set('name', 'my-'.Str::slug((string) $state))),
                Forms\Components\TextInput::make('name')->label('Kod nomi')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(60)
                    ->helperText('Lotin harflar va tire. Keyin o‘zgartirmang — saytda shu nom bilan ishlatiladi.')
                    ->disabled(fn (?Icon $record) => $record?->is_system)->dehydrated(),
                Forms\Components\FileUpload::make('svg_file')->label('.svg fayl yuklash')->acceptedFileTypes(['image/svg+xml'])->disk('local')->directory('tmp-icons')
                    ->visibility('private')->columnSpanFull()->dehydrated(false)
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $file = is_array($state) ? reset($state) : $state;
                        if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $set('svg', IconSvg::clean(file_get_contents($file->getRealPath())));
                        }
                    })->live(),
                Forms\Components\Textarea::make('svg')->label('SVG kodi')->required()->rows(7)->columnSpanFull()->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('svg', IconSvg::clean($state)))
                    ->helperText('Masalan: <svg viewBox="0 0 24 24">…</svg>'),
                Forms\Components\Placeholder::make('preview')->label('Ko‘rinishi')->columnSpanFull()->content(fn (Forms\Get $get) => new HtmlString(
                    '<div style="display:flex;gap:16px;align-items:center"><span style="width:56px;height:56px;border-radius:14px;background:linear-gradient(180deg,#ffd877,#f5a524);display:grid;place-items:center;color:#16171a">'.
                    '<span style="width:28px;height:28px;display:block" class="bx-ic">'.($get('svg') ?: '').'</span></span>'.
                    '<span style="width:56px;height:56px;border-radius:14px;background:#16171a;display:grid;place-items:center;color:#ffd877"><span style="width:28px;height:28px;display:block" class="bx-ic">'.($get('svg') ?: '').'</span></span></div>'
                )),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->orderByRaw("case \"group\" when 'custom' then 0 when 'site' then 1 else 2 end")->orderBy('name'))->striped()->paginated([48, 96, 200])->defaultPaginationPageOption(48)
            ->columns([
                Tables\Columns\ViewColumn::make('svg')->label('')->view('filament.tables.icon-preview'),
                Tables\Columns\TextColumn::make('label')->label('Nomi')->searchable()->weight(FontWeight::SemiBold),
                Tables\Columns\TextColumn::make('name')->label('Kod nomi')->searchable()->copyable()->fontFamily('mono')->size('xs'),
                Tables\Columns\TextColumn::make('group')->label('Guruh')->badge()
                    ->formatStateUsing(fn ($state) => ['site' => 'Sayt', 'heroicons' => 'Qo‘shimcha', 'custom' => 'Mening'][$state] ?? $state)
                    ->color(fn ($state) => $state === 'custom' ? 'primary' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')->label('Guruh')->options(['site' => 'Sayt ikonkalari', 'heroicons' => 'Qo‘shimcha', 'custom' => 'Mening ikonkalarim']),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Tahrirlash')->visible(fn (Icon $r) => ! $r->is_system),
                Tables\Actions\DeleteAction::make()->label('O‘chirish')->visible(fn (Icon $r) => ! $r->is_system),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->label('O‘chirish')])])
            ->checkIfRecordIsSelectableUsing(fn (Icon $r) => ! $r->is_system)
            ->emptyStateHeading('Ikonka yo‘q');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIcons::route('/'),
            'create' => Pages\CreateIcon::route('/create'),
            'edit' => Pages\EditIcon::route('/{record}/edit'),
        ];
    }
}
