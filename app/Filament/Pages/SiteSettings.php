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
        'seo_title', 'seo_description', 'legal_privacy', 'legal_offer', 'head_code', 'body_code', 'popups',
    ];

    public ?array $data = [];

    public function mount(): void
    {
        $data = collect(self::KEYS)->mapWithKeys(fn ($k) => [$k => Setting::get($k)])->all();
        // Popuplar birinchi marta ochilganda — yoqilgan holatda ko'rsatiladi (saytdagi standart bilan bir xil)
        $popups = is_array($data['popups'] ?? null) ? $data['popups'] : [];
        $popups['delay'] ??= 15;
        foreach (['course', 'service', 'guide'] as $type) {
            $popups[$type]['enabled'] ??= true;
        }
        $data['popups'] = $popups;
        $this->form->fill($data);
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
                Forms\Components\Tabs\Tab::make('Popuplar')->icon('heroicon-o-gift')->schema([
                    Forms\Components\Placeholder::make('popups_help')->label('')->content('Popup sahifada 15 soniyadan keyin yoki foydalanuvchi sahifani tark etmoqchi bo‘lganda chiqadi — bir sessiyada bir marta. Kurs chegirmasi: bosh sahifa va kurs sahifalarida; xizmat chegirmasi: xizmat sahifasida; qo‘llanma: maqolalarda.'),
                    Forms\Components\TextInput::make('popups.delay')->label('Necha soniyadan keyin chiqsin')->numeric()->default(15),
                    Forms\Components\Section::make('Kurs chegirma')->collapsible()->columns(2)->schema([
                        Forms\Components\Toggle::make('popups.course.enabled')->label('Yoqilgan')->default(true)->columnSpanFull(),
                        Forms\Components\TextInput::make('popups.course.title.uz')->label('Sarlavha (UZ)')->placeholder('Kursga hozir yoziling — 20 % chegirma'),
                        Forms\Components\TextInput::make('popups.course.title.ru')->label('Sarlavha (RU)'),
                        Forms\Components\Textarea::make('popups.course.text.uz')->label('Matn (UZ)')->rows(2),
                        Forms\Components\Textarea::make('popups.course.text.ru')->label('Matn (RU)')->rows(2),
                        Forms\Components\TextInput::make('popups.course.price_new')->label('Yangi narx (so‘m)')->numeric()->placeholder('2160000'),
                        Forms\Components\TextInput::make('popups.course.price_old')->label('Eski narx (so‘m)')->numeric()->placeholder('2700000'),
                        Forms\Components\TextInput::make('popups.course.caption.uz')->label('Narx izohi (UZ)')->placeholder('Premium tarif · bir martalik to‘lovda'),
                        Forms\Components\TextInput::make('popups.course.caption.ru')->label('Narx izohi (RU)'),
                        Forms\Components\DatePicker::make('popups.course.deadline')->label('Taklif tugash sanasi')->helperText('Bo‘sh qolsa — joriy oy oxirigacha.')->native(false),
                        Forms\Components\FileUpload::make('popups.course.image')->label('Rasm')->image()->disk('public')->directory('popups'),
                    ]),
                    Forms\Components\Section::make('Xizmat chegirma')->collapsible()->columns(2)->schema([
                        Forms\Components\Toggle::make('popups.service.enabled')->label('Yoqilgan')->default(true)->columnSpanFull(),
                        Forms\Components\TextInput::make('popups.service.title.uz')->label('Sarlavha (UZ)')->placeholder('Birinchi oy buxgalteriya xizmati — 50 % chegirma'),
                        Forms\Components\TextInput::make('popups.service.title.ru')->label('Sarlavha (RU)'),
                        Forms\Components\Textarea::make('popups.service.text.uz')->label('Matn (UZ)')->rows(2),
                        Forms\Components\Textarea::make('popups.service.text.ru')->label('Matn (RU)')->rows(2),
                        Forms\Components\Textarea::make('popups.service.points.uz')->label('Afzalliklar (UZ, har biri yangi qatorda)')->rows(3),
                        Forms\Components\Textarea::make('popups.service.points.ru')->label('Afzalliklar (RU, har biri yangi qatorda)')->rows(3),
                        Forms\Components\DatePicker::make('popups.service.deadline')->label('Taklif tugash sanasi')->helperText('Bo‘sh qolsa — joriy oy oxirigacha.')->native(false),
                    ]),
                    Forms\Components\Section::make('Bepul qo‘llanma')->collapsible()->columns(2)->schema([
                        Forms\Components\Toggle::make('popups.guide.enabled')->label('Yoqilgan')->default(true)->columnSpanFull(),
                        Forms\Components\TextInput::make('popups.guide.title.uz')->label('Sarlavha (UZ)')->placeholder('YaTT va MChJ uchun soliq kalendari 2027'),
                        Forms\Components\TextInput::make('popups.guide.title.ru')->label('Sarlavha (RU)'),
                        Forms\Components\Textarea::make('popups.guide.text.uz')->label('Matn (UZ)')->rows(2),
                        Forms\Components\Textarea::make('popups.guide.text.ru')->label('Matn (RU)')->rows(2),
                        Forms\Components\FileUpload::make('popups.guide.image')->label('Rasm')->image()->disk('public')->directory('popups'),
                    ]),
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
