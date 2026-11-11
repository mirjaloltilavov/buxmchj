<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Support\Tr;
use App\Models\Branch;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as I;
use App\Filament\Support\Info;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin', 'editor'];

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
            ]),
            Forms\Components\Section::make('Xaritadagi joy (pin)')->icon('heroicon-o-map-pin')->schema([
                Forms\Components\TextInput::make('map_url')->label('Xarita havolasi (Yandex/Google)')->url()->live(onBlur: true)
                    ->helperText('“Yo‘l ko‘rsatish” tugmasi shu havolani ochadi. Havolada koordinata bo‘lsa, pin ham o‘zi o‘sha joyga qo‘yiladi.')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($c = static::coordsFrom((string) $state)) {
                            $set('lat', $c[0]);
                            $set('lng', $c[1]);
                        }
                    }),
                Forms\Components\ViewField::make('map_picker')->hiddenLabel()->view('filament.forms.map-picker')->dehydrated(false),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('lat')->label('Kenglik (lat)')->numeric()->placeholder('41.2756')->rules(['nullable', 'numeric', 'between:-90,90']),
                    Forms\Components\TextInput::make('lng')->label('Uzunlik (lng)')->numeric()->placeholder('69.2037')->rules(['nullable', 'numeric', 'between:-180,180']),
                ]),
            ]),
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('sort')->label('Tartib')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Faol')->default(true),
            ]),
        ]);
    }

    /** Google/Yandex havolasidan yoki "41.27, 69.20" matnidan koordinata ajratib oladi */
    public static function coordsFrom(string $text): ?array
    {
        $t = urldecode($text);
        $pairs = [
            '~!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)~' => false,       // Google: aniq nuqta
            '~@(-?\d+\.\d+),(-?\d+\.\d+)~' => false,           // Google: xarita markazi
            '~[?&](?:q|query|destination)=(-?\d+\.\d+),\s*(-?\d+\.\d+)~' => false,
            '~[?&]pt=(-?\d+\.\d+),(-?\d+\.\d+)~' => true,      // Yandex: lng,lat
            '~[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)~' => true,      // Yandex: lng,lat
            '~^\s*(-?\d+\.\d+)\s*[,; ]\s*(-?\d+\.\d+)\s*$~' => false,
        ];
        foreach ($pairs as $re => $swap) {
            if (preg_match($re, $t, $m)) {
                [$lat, $lng] = $swap ? [(float) $m[2], (float) $m[1]] : [(float) $m[1], (float) $m[2]];
                if (abs($lat) <= 90 && abs($lng) <= 180) {
                    return [round($lat, 6), round($lng, 6)];
                }
            }
        }

        // Qisqa havola (maps.app.goo.gl, goo.gl/maps, yandex …/-/…): ochib, oxirgi manzildan olinadi
        if (preg_match('~^https://(maps\.app\.goo\.gl|goo\.gl/maps|yandex\.(uz|ru|com)/maps/-/)~', trim($text))) {
            try {
                $final = null;
                \Illuminate\Support\Facades\Http::timeout(8)->withOptions(['allow_redirects' => ['track_redirects' => true], 'on_stats' => function ($st) use (&$final) {
                    $final = (string) $st->getEffectiveUri();
                }])->get(trim($text));
                if ($final && $final !== trim($text)) {
                    return static::coordsFrom($final);
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->reorderable('sort')->defaultSort('sort')->columns([
            Tables\Columns\TextColumn::make('name')->label('Nomi')->formatStateUsing(fn ($record) => $record->name['uz'] ?? ''),
            Tables\Columns\TextColumn::make('address')->label('Manzil')->formatStateUsing(fn ($record) => $record->address['uz'] ?? ''),
            Tables\Columns\ToggleColumn::make('is_active')->label('Faol'),
        ])->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl'),
            Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('3xl'), Tables\Actions\DeleteAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make('Filial')->schema([
                Info::tr('name', 'Nomi'), Info::tr('address', 'Manzil'), Info::tr('hours', 'Ish vaqti'),
                I\Grid::make(3)->schema([Info::text('phone', 'Telefon', 'heroicon-o-phone'), Info::text('lat', 'Kenglik (lat)'), Info::text('lng', 'Uzunlik (lng)')]),
                Info::link('map_url', 'Xarita havolasi'),
            ]),
            Info::status(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListBranchs::route('/')];
    }
}
