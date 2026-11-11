<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RoleAccess;
use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    use RoleAccess;

    protected static array $roles = ['super_admin', 'admin', 'manager'];

    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $modelLabel = 'ariza';
    protected static ?string $pluralModelLabel = 'Arizalar';
    protected static ?int $navigationSort = -1;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationGroup = 'Mijozlar';
    protected static ?string $navigationLabel = 'Arizalar';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'message'];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $color = ['new' => 'warning', 'contacted' => 'info', 'done' => 'success', 'spam' => 'gray'];

        return $infolist->schema([
            Infolists\Components\Section::make('Mijoz')->icon('heroicon-o-user')->columns(4)->schema([
                Infolists\Components\TextEntry::make('name')->label('Ism')->weight('bold')->size('lg')->icon('heroicon-o-user'),
                Infolists\Components\TextEntry::make('phone')->label('Telefon')->icon('heroicon-o-phone')->copyable()->copyMessage('Nusxalandi')
                    ->url(fn ($record) => 'tel:'.preg_replace('/[^0-9+]/', '', $record->phone))->color('primary')->weight('bold')->size('lg'),
                Infolists\Components\TextEntry::make('assignee.name')->label('Mas’ul xodim')->placeholder('Biriktirilmagan')->icon('heroicon-o-user-circle'),
                Infolists\Components\TextEntry::make('status')->label('Holat')->badge()
                    ->formatStateUsing(fn ($state) => Lead::STATUSES[$state] ?? $state)->color(fn ($state) => $color[$state] ?? 'gray'),
            ]),
            Infolists\Components\Section::make('Ariza')->icon('heroicon-o-chat-bubble-left-ellipsis')->columns(3)->schema([
                Infolists\Components\TextEntry::make('interest')->label('Qiziqish')->badge()->formatStateUsing(fn ($state) => Lead::INTERESTS[$state] ?? '—'),
                Infolists\Components\TextEntry::make('created_at')->label('Yuborilgan')->dateTime('d.m.Y H:i')->icon('heroicon-o-clock')
                    ->helperText(fn ($record) => ago($record->created_at)),
                Infolists\Components\TextEntry::make('source')->label('Qaysi sahifadan')->placeholder('—')->icon('heroicon-o-globe-alt'),
                Infolists\Components\TextEntry::make('message')->label('Xabar')->placeholder('Xabar qoldirilmagan')->columnSpanFull()->prose(),
            ]),
            Infolists\Components\Section::make('Manba va vaqtlar')->icon('heroicon-o-chart-bar')->columns(3)->collapsible()->schema([
                Infolists\Components\TextEntry::make('channel')->label('Manba')->state(fn ($record) => $record->channel())->badge()->color('info'),
                Infolists\Components\TextEntry::make('utm_campaign')->label('Reklama kampaniyasi')->placeholder('—'),
                Infolists\Components\TextEntry::make('referrer')->label('Qaysi saytdan kelgan')->placeholder('—')->limit(60),
                Infolists\Components\TextEntry::make('contacted_at')->label('Bog‘lanilgan')->placeholder('Hali bog‘lanilmagan')
                    ->formatStateUsing(fn ($state, $record) => $state ? $state->format('d.m.Y H:i').' (javob: '.\App\Support\LeadPeriod::humanMinutes($record->created_at->diffInMinutes($state)).')' : null),
                Infolists\Components\TextEntry::make('closed_at')->label('Yopilgan')->placeholder('—')->dateTime('d.m.Y H:i'),
                Infolists\Components\TextEntry::make('ip')->label('IP manzil')->placeholder('—'),
            ]),
            Infolists\Components\Section::make('Admin izohi')->icon('heroicon-o-pencil')->schema([
                Infolists\Components\TextEntry::make('note')->hiddenLabel()->placeholder('Izoh yo‘q. “Tahrirlash” orqali yozishingiz mumkin.')->prose(),
            ]),
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $n = Lead::where('status', 'new')->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /** Arizani faqat admin o'chira oladi (menejer — yo'q) */
    public static function canDelete($record): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin', 'admin');
    }

    public static function canDeleteAny(): bool
    {
        return static::canDelete(null);
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
                Forms\Components\Select::make('assigned_to')->label('Mas’ul xodim')->options(fn () => Lead::staff()->pluck('name', 'id'))->searchable()->placeholder('Biriktirilmagan'),
                Forms\Components\Textarea::make('message')->label('Xabar')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('note')->label('Izoh (faqat admin uchun)')->columnSpanFull(),
                Forms\Components\Placeholder::make('source')->label('Sahifa')->content(fn ($record) => $record?->source ?: '—'),
                Forms\Components\Placeholder::make('created_at')->label('Yuborilgan')->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->recordUrl(null)->recordAction(Tables\Actions\ViewAction::class)->defaultSort('created_at', 'desc')->poll('30s')->striped()->emptyStateHeading('Hozircha arizalar yo‘q')
            ->emptyStateDescription('Saytdagi formalardan kelgan arizalar shu yerda ko‘rinadi.')->emptyStateIcon('heroicon-o-inbox')->columns([
            Tables\Columns\TextColumn::make('created_at')->label('Sana')->dateTime('d.m.Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('name')->label('Ism')->searchable(),
            Tables\Columns\TextColumn::make('phone')->label('Telefon')->searchable()->icon('heroicon-o-phone')->copyable()->weight('bold')->url(fn ($record) => 'tel:'.preg_replace('/[^0-9+]/', '', $record->phone)),
            Tables\Columns\TextColumn::make('interest')->label('Qiziqish')->formatStateUsing(fn ($state) => Lead::INTERESTS[$state] ?? '—')->badge()->color('gray'),
            Tables\Columns\TextColumn::make('message')->label('Xabar')->limit(40)->toggleable(),
            Tables\Columns\TextColumn::make('channel')->label('Manba')->state(fn ($record) => $record->channel())->badge()->color('gray')->toggleable(),
            Tables\Columns\TextColumn::make('source')->label('Sahifa')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\SelectColumn::make('status')->label('Holat')->options(Lead::STATUSES)->selectablePlaceholder(false),
            Tables\Columns\TextColumn::make('assignee.name')->label('Mas’ul')->placeholder('—')->icon('heroicon-o-user-circle')->toggleable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->label('Holat')->options(Lead::STATUSES),
            Tables\Filters\SelectFilter::make('interest')->label('Qiziqish')->options(Lead::INTERESTS),
            Tables\Filters\SelectFilter::make('assigned_to')->label('Mas’ul')->options(fn () => Lead::staff()->pluck('name', 'id')),
        ])->actions([
            Tables\Actions\ViewAction::make()->label('Ko‘rish')->slideOver()->modalWidth('3xl')->extraModalFooterActions(fn ($record) => [
                Tables\Actions\Action::make('call')->label('Qo‘ng‘iroq qilish')->icon('heroicon-o-phone')->color('primary')->url('tel:'.preg_replace('/[^0-9+]/', '', (string) $record->phone)),
                Tables\Actions\Action::make('contacted')->label('Bog‘lanildi')->icon('heroicon-o-phone-arrow-up-right')->color('info')
                    ->visible($record->status === 'new')->action(fn () => $record->update(['status' => 'contacted']))->cancelParentActions(),
                Tables\Actions\Action::make('done')->label('Yakunlandi')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(! in_array($record->status, ['done', 'spam'], true))->action(fn () => $record->update(['status' => 'done']))->cancelParentActions(),
            ]),
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('contacted')->label('Bog‘lanildi')->icon('heroicon-o-phone-arrow-up-right')->color('info')
                    ->visible(fn ($record) => $record->status === 'new')
                    ->action(fn ($record) => $record->update(['status' => 'contacted'])),
                Tables\Actions\Action::make('done')->label('Yakunlandi')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => ! in_array($record->status, ['done', 'spam'], true))
                    ->action(fn ($record) => $record->update(['status' => 'done'])),
                Tables\Actions\Action::make('takeMine')->label('O‘zimga olish')->icon('heroicon-o-hand-raised')->color('primary')
                    ->visible(fn ($record) => $record->assigned_to !== auth()->id())
                    ->action(fn ($record) => $record->update(['assigned_to' => auth()->id()])),
                Tables\Actions\Action::make('assign')->label('Xodimga biriktirish')->icon('heroicon-o-user-plus')->color('gray')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin', 'admin'))
                    ->form([Forms\Components\Select::make('assigned_to')->label('Mas’ul xodim')->options(fn () => Lead::staff()->pluck('name', 'id'))->required()])
                    ->fillForm(fn ($record) => ['assigned_to' => $record->assigned_to])
                    ->action(fn ($record, array $data) => $record->update($data)),
                Tables\Actions\EditAction::make()->label('Tahrirlash')->slideOver()->modalWidth('3xl'),
                Tables\Actions\DeleteAction::make()->label('O‘chirish'),
            ]),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\BulkAction::make('contacted')->label('“Bog‘lanildi” deb belgilash')->icon('heroicon-o-phone-arrow-up-right')
                    ->action(fn ($records) => $records->each->update(['status' => 'contacted']))->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('done')->label('“Yakunlandi” deb belgilash')->icon('heroicon-o-check-circle')
                    ->action(fn ($records) => $records->each->update(['status' => 'done']))->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('exportCsv')->label('Excel (CSV) yuklab olish')->icon('heroicon-o-arrow-down-tray')
                    ->action(fn ($records) => \App\Support\LeadExport::download($records)),
                Tables\Actions\DeleteBulkAction::make()->label('O‘chirish'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListLeads::route('/')];
    }
}
