<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Infolists\Components as I;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/** Admin paneldagi o'zgarishlar jurnali (faqat o'qish). */
class ActivityLogResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin'];

    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Tizim';
    protected static ?string $navigationLabel = 'Faollik jurnali';
    protected static ?string $modelLabel = 'yozuv';
    protected static ?string $pluralModelLabel = 'Faollik jurnali';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    /** Maydon nomlari jurnalda o'zbekcha chiqsin */
    private const FIELDS = [
        'title' => 'Nomi / sarlavha', 'name' => 'Nomi', 'question' => 'Savol', 'answer' => 'Javob', 'excerpt' => 'Qisqa tavsif', 'body' => 'Matn',
        'description' => 'Tavsif', 'status' => 'Holat', 'note' => 'Izoh', 'assigned_to' => 'Mas’ul xodim', 'price' => 'Narx', 'image' => 'Rasm',
        'cover' => 'Muqova', 'photo' => 'Rasm', 'is_active' => 'Faol', 'is_featured' => 'Tanlangan', 'sort' => 'Tartib', 'slug' => 'URL',
        'role' => 'Rol', 'email' => 'Email', 'phone' => 'Telefon', 'password' => 'Parol', 'address' => 'Manzil', 'hours' => 'Ish vaqti',
        'plans' => 'Tariflar', 'program' => 'Dastur', 'audience' => 'Kimlar uchun', 'outcomes' => 'Natijalar', 'tags' => 'Teglar', 'meta' => 'Qo‘shimcha',
        'features' => 'Imkoniyatlar', 'subtitle' => 'Izoh', 'role_label' => 'Rol', 'icon' => 'Ikonka', 'url' => 'Havola', 'video_url' => 'Video',
    ];

    private const COLORS = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', 'login' => 'gray', 'settings' => 'info'];

    protected static function fmt($v): string
    {
        if ($v === null || $v === '' || $v === []) {
            return '—';
        }
        if (is_bool($v)) {
            return $v ? 'Ha' : 'Yo‘q';
        }
        if (is_array($v)) {
            if (isset($v['uz']) || isset($v['ru'])) {
                return 'UZ: '.static::fmt($v['uz'] ?? null).' · RU: '.static::fmt($v['ru'] ?? null);
            }

            return Str::limit(json_encode($v, JSON_UNESCAPED_UNICODE), 300);
        }

        return Str::limit(strip_tags((string) $v), 300);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make()->columns(3)->schema([
                I\TextEntry::make('user.name')->label('Kim')->placeholder('Tizim')->icon('heroicon-o-user'),
                I\TextEntry::make('event')->label('Amal')->badge()->formatStateUsing(fn ($state) => ActivityLog::EVENTS[$state] ?? $state)->color(fn ($state) => self::COLORS[$state] ?? 'gray'),
                I\TextEntry::make('created_at')->label('Qachon')->dateTime('d.m.Y H:i:s'),
                I\TextEntry::make('subject')->label('Nima')->state(fn (ActivityLog $r) => ($r->subject_type && $r->event !== 'login' ? $r->subjectName().': ' : '').$r->subject_label)->columnSpan(2),
                I\TextEntry::make('ip')->label('IP manzil')->placeholder('—'),
            ]),
            I\Section::make('O‘zgarishlar')->visible(fn (ActivityLog $r) => filled($r->changes))->schema([
                I\TextEntry::make('changes')->hiddenLabel()->html()->state(function (ActivityLog $r) {
                    $rows = collect($r->changes ?? [])->map(fn ($c, $k) => '<tr><td style="padding:.4rem .6rem;font-weight:600;vertical-align:top">'.e(self::FIELDS[$k] ?? $k).'</td>'
                        .'<td style="padding:.4rem .6rem;color:#b42318;vertical-align:top"><s>'.e(static::fmt($c['old'] ?? null)).'</s></td>'
                        .'<td style="padding:.4rem .6rem;color:#1f9d5c;vertical-align:top">'.e(static::fmt($c['new'] ?? null)).'</td></tr>')->implode('');

                    return new HtmlString('<table style="width:100%;font-size:.85rem;border-collapse:collapse"><thead><tr style="opacity:.6;text-align:left"><th style="padding:.4rem .6rem">Maydon</th><th style="padding:.4rem .6rem">Avval</th><th style="padding:.4rem .6rem">Keyin</th></tr></thead><tbody>'.$rows.'</tbody></table>');
                }),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->defaultSort('created_at', 'desc')->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Qachon')->formatStateUsing(fn ($state) => ago($state))->description(fn (ActivityLog $r) => $r->created_at?->format('d.m.Y H:i'))->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Kim')->placeholder('Tizim')->searchable(),
                Tables\Columns\TextColumn::make('event')->label('Amal')->badge()->formatStateUsing(fn ($state) => ActivityLog::EVENTS[$state] ?? $state)->color(fn ($state) => self::COLORS[$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('subject_label')->label('Nima')->searchable()->wrap()
                    ->description(fn (ActivityLog $r) => $r->subject_type && $r->event !== 'login' ? $r->subjectName() : null),
                Tables\Columns\TextColumn::make('changes')->label('Maydonlar')->state(fn (ActivityLog $r) => $r->changes ? implode(', ', array_map(fn ($k) => self::FIELDS[$k] ?? $k, array_slice(array_keys($r->changes), 0, 4))) : null)->placeholder('—')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')->label('Kim')->options(fn () => User::orderBy('name')->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('event')->label('Amal')->options(ActivityLog::EVENTS),
                Tables\Filters\SelectFilter::make('subject_type')->label('Bo‘lim')->options(ActivityLog::SUBJECTS),
            ])
            ->actions([Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl')->modalHeading('Jurnal yozuvi')])
            ->emptyStateHeading('Hozircha yozuv yo‘q')->emptyStateDescription('Admin panelda qilingan har bir o‘zgarish shu yerda ko‘rinadi.');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListActivityLogs::route('/')];
    }
}
