<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\UserResource\Pages;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components as I;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin'];

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Tizim';
    protected static ?string $navigationLabel = 'Foydalanuvchilar';
    protected static ?string $modelLabel = 'foydalanuvchi';
    protected static ?string $pluralModelLabel = 'Foydalanuvchilar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'username', 'email', 'phone'];
    }

    /** Oxirgi faol bosh adminni o'chirish / bloklash / pasaytirish mumkin emas */
    public static function isLastSuperAdmin(User $u): bool
    {
        return $u->role === 'super_admin' && User::where('role', 'super_admin')->where('is_active', true)->whereKeyNot($u->id)->doesntExist();
    }

    public static function canDelete($record): bool
    {
        return static::roleAllowed() && $record && $record->id !== auth()->id() && ! static::isLastSuperAdmin($record);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Shaxsiy ma’lumotlar')->columns(2)->schema([
                Forms\Components\FileUpload::make('avatar')->label('Rasm')->image()->avatar()->disk('public')->directory('avatars')->imageEditor()->columnSpanFull(),
                Forms\Components\TextInput::make('name')->label('Ism familiya')->required()->maxLength(120),
                Forms\Components\TextInput::make('phone')->label('Telefon')->tel()->maxLength(40)->placeholder('+998 90 123 45 67'),
                Forms\Components\TextInput::make('username')->label('Login (panelga kirish uchun)')->required()->maxLength(60)->unique(ignoreRecord: true)
                    ->regex('/^[A-Za-z0-9._-]+$/')->validationMessages(['regex' => 'Faqat lotin harflari, raqamlar va . _ - belgilari.', 'unique' => 'Bu login band.'])
                    ->placeholder('OxunjonAsrorov')->helperText('Bo‘sh joysiz, lotin harflarida. Email bilan ham kirish mumkin.'),
                Forms\Components\TextInput::make('email')->label('Email (ixtiyoriy)')->email()->maxLength(190)->unique(ignoreRecord: true)
                    ->validationMessages(['unique' => 'Bu email boshqa foydalanuvchiga biriktirilgan.']),
            ]),
            Forms\Components\Section::make('Rol va huquqlar')->schema([
                Forms\Components\Radio::make('role')->label('Rol')->options(User::ROLES)->descriptions(User::ROLE_HINTS)->required()->default('editor')
                    ->disabled(fn (?User $record) => $record && ($record->id === auth()->id() || static::isLastSuperAdmin($record)))
                    ->helperText(fn (?User $record) => $record && $record->id === auth()->id() ? 'O‘zingizning rolingizni o‘zgartira olmaysiz.' : null),
                Forms\Components\Toggle::make('is_active')->label('Faol (panelga kira oladi)')->default(true)
                    ->disabled(fn (?User $record) => $record && ($record->id === auth()->id() || static::isLastSuperAdmin($record))),
            ]),
            Forms\Components\Section::make('Parol')->columns(2)->schema([
                Forms\Components\TextInput::make('password')->label(fn (?User $record) => $record ? 'Yangi parol (o‘zgartirmasangiz bo‘sh qoldiring)' : 'Parol')
                    ->password()->revealable()->minLength(8)->required(fn (?User $record) => ! $record)
                    ->dehydrated(fn ($state) => filled($state))->dehydrateStateUsing(fn ($state) => Hash::make($state))->confirmed(),
                Forms\Components\TextInput::make('password_confirmation')->label('Parolni takrorlang')->password()->revealable()->dehydrated(false)
                    ->required(fn (?User $record, Forms\Get $get) => ! $record || filled($get('password'))),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            I\Section::make()->columns(['default' => 1, 'md' => 4])->schema([
                I\ImageEntry::make('avatar')->hiddenLabel()->disk('public')->circular()->height(96)
                    ->defaultImageUrl(fn (User $r) => 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96"><rect width="96" height="96" rx="48" fill="#ffd877"/><text x="48" y="60" font-family="Arial" font-size="38" font-weight="700" text-anchor="middle" fill="#16171a">'.e(mb_strtoupper(mb_substr($r->name, 0, 1))).'</text></svg>')),
                I\Group::make([
                    I\TextEntry::make('name')->hiddenLabel()->weight('bold')->size('lg'),
                    I\TextEntry::make('username')->hiddenLabel()->icon('heroicon-o-user')->prefix('@')->copyable()->placeholder('Login yo‘q'),
                    I\TextEntry::make('email')->hiddenLabel()->icon('heroicon-o-envelope')->copyable()->placeholder('Email yo‘q'),
                    I\TextEntry::make('phone')->hiddenLabel()->icon('heroicon-o-phone')->placeholder('Telefon kiritilmagan'),
                ])->columnSpan(['md' => 2]),
                I\Group::make([
                    I\TextEntry::make('role')->hiddenLabel()->badge()->formatStateUsing(fn ($state) => User::ROLES[$state] ?? $state)->color(fn ($state) => User::ROLE_COLORS[$state] ?? 'gray'),
                    I\TextEntry::make('is_active')->hiddenLabel()->badge()->formatStateUsing(fn ($state) => $state ? 'Faol' : 'Bloklangan')->color(fn ($state) => $state ? 'success' : 'gray'),
                ]),
            ]),
            I\Section::make('Huquqlari')->schema([
                I\TextEntry::make('role')->hiddenLabel()->formatStateUsing(fn ($state) => User::ROLE_HINTS[$state] ?? '—'),
            ]),
            I\Section::make('Faollik')->columns(3)->schema([
                I\TextEntry::make('last_login_at')->label('Oxirgi kirish')->formatStateUsing(fn ($state) => $state ? ago($state).' ('.$state->format('d.m.Y H:i').')' : 'Hali kirmagan')->placeholder('Hali kirmagan'),
                I\TextEntry::make('assigned')->label('Biriktirilgan arizalar')->state(fn (User $r) => $r->assignedLeads()->count().' ta ('.$r->assignedLeads()->where('status', 'new')->count().' tasi yangi)'),
                I\TextEntry::make('created_at')->label('Qo‘shilgan')->dateTime('d.m.Y'),
                I\TextEntry::make('recent')->label('Oxirgi harakatlari')->columnSpanFull()->html()
                    ->state(function (User $r) {
                        $rows = ActivityLog::where('user_id', $r->id)->latest('created_at')->limit(8)->get();
                        if ($rows->isEmpty()) {
                            return '<span style="opacity:.6">Hali harakat yo‘q</span>';
                        }

                        return '<ul style="display:flex;flex-direction:column;gap:.35rem">'.$rows->map(fn ($l) => '<li><b>'.e(ActivityLog::EVENTS[$l->event] ?? $l->event).'</b> · '.e($l->subject_type && $l->event !== 'login' ? $l->subjectName().': ' : '').e((string) $l->subject_label).' <span style="opacity:.55">— '.e(ago($l->created_at)).'</span></li>')->implode('').'</ul>';
                    }),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->defaultSort('id')
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->label('')->disk('public')->circular()->height(40)
                    ->defaultImageUrl(fn (User $r) => 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><rect width="40" height="40" rx="20" fill="#ffd877"/><text x="20" y="26" font-family="Arial" font-size="16" font-weight="700" text-anchor="middle" fill="#16171a">'.e(mb_strtoupper(mb_substr($r->name, 0, 1))).'</text></svg>')),
                Tables\Columns\TextColumn::make('name')->label('Ism')->searchable()->weight('bold')->description(fn (User $r) => collect(['@'.$r->username, $r->email])->filter(fn ($v) => $v && $v !== '@')->implode(' · ')),
                Tables\Columns\TextColumn::make('role')->label('Rol')->badge()->formatStateUsing(fn ($state) => User::ROLES[$state] ?? $state)->color(fn ($state) => User::ROLE_COLORS[$state] ?? 'gray')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Faol')->boolean(),
                Tables\Columns\TextColumn::make('last_login_at')->label('Oxirgi kirish')->formatStateUsing(fn ($state) => ago($state))->placeholder('Hali kirmagan')->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->label('Rol')->options(User::ROLES),
                Tables\Filters\TernaryFilter::make('is_active')->label('Holat')->trueLabel('Faol')->falseLabel('Bloklangan'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('3xl'),
                    Tables\Actions\Action::make('resetPassword')->label('Parolni tiklash')->icon('heroicon-o-key')->color('warning')
                        ->requiresConfirmation()->modalHeading('Yangi parol yaratilsinmi?')
                        ->modalDescription('Tasodifiy yangi parol yaratiladi va bir marta ko‘rsatiladi. Uni foydalanuvchiga xavfsiz yo‘l bilan yetkazing.')
                        ->action(function (User $record) {
                            $pass = Str::password(12, symbols: false);
                            $record->update(['password' => $pass]);
                            Notification::make()->title('Yangi parol: '.$pass)->body(($record->username ?: $record->email).' uchun. Bu oyna yopilgach parol qayta ko‘rsatilmaydi.')->warning()->persistent()->send();
                        }),
                    Tables\Actions\Action::make('toggleActive')
                        ->label(fn (User $r) => $r->is_active ? 'Bloklash' : 'Faollashtirish')
                        ->icon(fn (User $r) => $r->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                        ->color(fn (User $r) => $r->is_active ? 'danger' : 'success')
                        ->visible(fn (User $r) => $r->id !== auth()->id() && ! static::isLastSuperAdmin($r))
                        ->requiresConfirmation()
                        ->action(fn (User $r) => $r->update(['is_active' => ! $r->is_active])),
                    Tables\Actions\DeleteAction::make()->label('O‘chirish'),
                ]),
            ])
            ->emptyStateHeading('Foydalanuvchi yo‘q');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListUsers::route('/')];
    }
}
