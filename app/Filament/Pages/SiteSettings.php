<?php

namespace App\Filament\Pages;

use App\Filament\Support\Tr;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/** Umumiy sozlamalar: kontaktlar, ijtimoiy tarmoqlar, Telegram bot, SEO, huquqiy matnlar. */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Sozlamalar';
    protected static ?string $navigationLabel = 'Sayt sozlamalari';
    protected static ?string $title = 'Sayt sozlamalari';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.settings-form';

    /** Bu sahifada tahrirlanadigan kalitlar. */
    public const KEYS = [
        'site_name', 'phone', 'telegram_url', 'telegram_handle', 'instagram_url', 'instagram_handle', 'youtube_url',
        'work_hours', 'footer_about', 'map_embed', 'hero_autoplay',
        'telegram_bot_token', 'telegram_chat_id',
        'seo_title', 'seo_description', 'legal_privacy', 'legal_offer', 'head_code', 'body_code',
    ];

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(collect(self::KEYS)->mapWithKeys(fn ($k) => [$k => Setting::get($k)])->all());
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Tabs::make()->tabs([
                Forms\Components\Tabs\Tab::make('Kontaktlar')->icon('heroicon-o-phone')->schema([
                    Forms\Components\TextInput::make('site_name')->label('Sayt nomi')->default('Buxgalter MChJ'),
                    Forms\Components\TextInput::make('phone')->label('Telefon')->placeholder('+998 55 500 37 37'),
                    Tr::text('work_hours', 'Ish vaqti'),
                    Tr::textarea('footer_about', 'Footerdagi qisqa matn', false, 2),
                    Forms\Components\Textarea::make('map_embed')->label('Xarita (iframe kodi)')->rows(3)->helperText('Yandex yoki Google xaritadan “Встроить / Embed” kodini qo‘ying.'),
                    Forms\Components\TextInput::make('hero_autoplay')->label('Hero slayd almashish vaqti (soniya)')->numeric()->default(6),
                ]),
                Forms\Components\Tabs\Tab::make('Ijtimoiy tarmoqlar')->icon('heroicon-o-share')->schema([
                    Forms\Components\TextInput::make('telegram_url')->label('Telegram havolasi')->url(),
                    Forms\Components\TextInput::make('telegram_handle')->label('Telegram nomi')->placeholder('@buxgalter_mchj'),
                    Forms\Components\TextInput::make('instagram_url')->label('Instagram havolasi')->url(),
                    Forms\Components\TextInput::make('instagram_handle')->label('Instagram nomi')->placeholder('@buxgalter_mchj'),
                    Forms\Components\TextInput::make('youtube_url')->label('YouTube havolasi')->url(),
                ]),
                Forms\Components\Tabs\Tab::make('Telegram bot')->icon('heroicon-o-bell-alert')->schema([
                    Forms\Components\Placeholder::make('tg_help')->label('')->content('Yangi ariza kelganda Telegram guruhga xabar yuboriladi. @BotFather orqali bot yarating, botni guruhga qo‘shing va guruh chat ID sini kiriting.'),
                    Forms\Components\TextInput::make('telegram_bot_token')->label('Bot token')->password()->revealable(),
                    Forms\Components\TextInput::make('telegram_chat_id')->label('Chat ID')->placeholder('-1001234567890'),
                ]),
                Forms\Components\Tabs\Tab::make('SEO')->icon('heroicon-o-magnifying-glass')->schema([
                    Tr::text('seo_title', 'Bosh sahifa sarlavhasi (title)'),
                    Tr::textarea('seo_description', 'Bosh sahifa tavsifi (description)', false, 2),
                ]),
                Forms\Components\Tabs\Tab::make('Huquqiy matnlar')->icon('heroicon-o-scale')->schema([
                    Tr::rich('legal_privacy', 'Maxfiylik siyosati', 'legal'),
                    Tr::rich('legal_offer', 'Ommaviy oferta', 'legal'),
                ]),
                Forms\Components\Tabs\Tab::make('Qo‘shimcha kod')->icon('heroicon-o-code-bracket')->schema([
                    Forms\Components\Textarea::make('head_code')->label('<head> ichiga (Google Analytics, Meta Pixel...)')->rows(5),
                    Forms\Components\Textarea::make('body_code')->label('</body> oldidan')->rows(5),
                ]),
            ])->persistTabInQueryString(),
        ]);
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            if (in_array($key, self::KEYS, true)) {
                Setting::put($key, $value);
            }
        }
        Notification::make()->title('Saqlandi')->success()->send();
    }
}
