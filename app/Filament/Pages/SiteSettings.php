<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasSettingBlocks;
use App\Filament\Support\Tr;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

/** Umumiy sozlamalar: kartochkalar ko'rinishida, tahrirlash drawer'da. */
class SiteSettings extends Page implements HasForms, HasActions
{
    use HasSettingBlocks;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Sozlamalar';
    protected static ?string $navigationLabel = 'Sayt sozlamalari';
    protected static ?string $title = 'Sayt sozlamalari';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $u = auth()->user();

        return $u && in_array($u->role, ['super_admin', 'admin'], true);
    }

    protected function hint(): ?string
    {
        return 'Har bir blok yonidagi “Tahrirlash” tugmasi o‘ng tomondan oyna ochadi. Popuplarni kartochkaning o‘zidagi tugma bilan yoqish yoki o‘chirish mumkin.';
    }

    protected function blocks(): array
    {
        $popup = fn (string $key, string $title, string $icon, string $desc, \Closure $schema) => [
            'title' => $title, 'icon' => $icon, 'description' => $desc, 'paths' => ["popups.{$key}"], 'toggle' => "popups.{$key}.enabled", 'schema' => $schema,
        ];

        return [
            'Sayt' => [
                'contacts' => ['title' => 'Kontaktlar va asosiy', 'icon' => 'heroicon-o-phone', 'paths' => ['site_name', 'phone', 'work_hours', 'footer_about', 'map_embed', 'hero_autoplay'], 'schema' => fn () => [
                    Forms\Components\TextInput::make('site_name')->label('Sayt nomi')->default('Buxgalter MChJ'),
                    Forms\Components\TextInput::make('phone')->label('Telefon')->placeholder('+998 55 500 37 37'),
                    Tr::text('work_hours', 'Ish vaqti'),
                    Tr::textarea('footer_about', 'Footerdagi qisqa matn', false, 2),
                    Forms\Components\Textarea::make('map_embed')->label('Xarita (iframe kodi)')->rows(3)->helperText('Yandex yoki Google xaritadan “Встроить / Embed” kodini qo‘ying.'),
                    Forms\Components\TextInput::make('hero_autoplay')->label('Hero slayd almashish vaqti (soniya)')->numeric()->default(6),
                ]],
                'social' => ['title' => 'Ijtimoiy tarmoqlar', 'icon' => 'heroicon-o-share', 'paths' => ['telegram_url', 'telegram_handle', 'instagram_url', 'instagram_handle', 'youtube_url'], 'schema' => fn () => [
                    Forms\Components\TextInput::make('telegram_url')->label('Telegram havolasi')->url(),
                    Forms\Components\TextInput::make('telegram_handle')->label('Telegram nomi')->placeholder('@buxgalter_mchj'),
                    Forms\Components\TextInput::make('instagram_url')->label('Instagram havolasi')->url(),
                    Forms\Components\TextInput::make('instagram_handle')->label('Instagram nomi')->placeholder('@buxgalter_mchj'),
                    Forms\Components\TextInput::make('youtube_url')->label('YouTube havolasi')->url(),
                ]],
                'bot' => ['title' => 'Telegram bot (ariza xabarlari)', 'icon' => 'heroicon-o-bell-alert', 'description' => 'Yangi ariza kelganda guruhga xabar yuboriladi.', 'paths' => ['telegram_bot_token', 'telegram_chat_id'], 'schema' => fn () => [
                    Forms\Components\Placeholder::make('tg_help')->label('')->content('@BotFather orqali bot yarating, botni guruhga qo‘shing va guruh chat ID sini kiriting.'),
                    Forms\Components\TextInput::make('telegram_bot_token')->label('Bot token')->password()->revealable(),
                    Forms\Components\TextInput::make('telegram_chat_id')->label('Chat ID')->placeholder('-1001234567890'),
                ]],
                'seo' => ['title' => 'SEO (bosh sahifa)', 'icon' => 'heroicon-o-magnifying-glass', 'paths' => ['seo_title', 'seo_description'], 'schema' => fn () => [
                    Tr::text('seo_title', 'Bosh sahifa sarlavhasi (title)'),
                    Tr::textarea('seo_description', 'Bosh sahifa tavsifi (description)', false, 2),
                ]],
            ],
            'Huquqiy matnlar' => [
                'privacy' => ['title' => 'Maxfiylik siyosati', 'icon' => 'heroicon-o-shield-check', 'paths' => ['legal_privacy'], 'schema' => fn () => [Tr::rich('legal_privacy', 'Maxfiylik siyosati', 'legal')]],
                'offer' => ['title' => 'Ommaviy oferta', 'icon' => 'heroicon-o-scale', 'paths' => ['legal_offer'], 'schema' => fn () => [Tr::rich('legal_offer', 'Ommaviy oferta', 'legal')]],
            ],
            'Popuplar (maxsus takliflar)' => [
                'popup_general' => ['title' => 'Popup vaqti', 'icon' => 'heroicon-o-clock', 'description' => 'Sahifa ochilgach necha soniyadan keyin chiqadi (yoki foydalanuvchi sahifani tark etmoqchi bo‘lganda).', 'paths' => ['popups.delay'], 'schema' => fn () => [
                    Forms\Components\TextInput::make('popups.delay')->label('Necha soniyadan keyin chiqsin')->numeric()->default(15),
                ]],
                'popup_course' => $popup('course', 'Kurs chegirmasi', 'heroicon-o-academic-cap', 'Bosh sahifa, Kurslar va kurs sahifalarida', fn () => [
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
                'popup_service' => $popup('service', 'Xizmat chegirmasi', 'heroicon-o-gift', 'Xizmatlar sahifasida', fn () => [
                    Forms\Components\TextInput::make('popups.service.title.uz')->label('Sarlavha (UZ)')->placeholder('Birinchi oy buxgalteriya xizmati — 50 % chegirma'),
                    Forms\Components\TextInput::make('popups.service.title.ru')->label('Sarlavha (RU)'),
                    Forms\Components\Textarea::make('popups.service.text.uz')->label('Matn (UZ)')->rows(2),
                    Forms\Components\Textarea::make('popups.service.text.ru')->label('Matn (RU)')->rows(2),
                    Forms\Components\Textarea::make('popups.service.points.uz')->label('Afzalliklar (UZ, har biri yangi qatorda)')->rows(3),
                    Forms\Components\Textarea::make('popups.service.points.ru')->label('Afzalliklar (RU, har biri yangi qatorda)')->rows(3),
                    Forms\Components\DatePicker::make('popups.service.deadline')->label('Taklif tugash sanasi')->helperText('Bo‘sh qolsa — joriy oy oxirigacha.')->native(false),
                ]),
                'popup_guide' => $popup('guide', 'Bepul qo‘llanma', 'heroicon-o-document-arrow-down', 'Maqolalar sahifasi va maqolalarda', fn () => [
                    Forms\Components\TextInput::make('popups.guide.title.uz')->label('Sarlavha (UZ)')->placeholder('YaTT va MChJ uchun soliq kalendari 2027'),
                    Forms\Components\TextInput::make('popups.guide.title.ru')->label('Sarlavha (RU)'),
                    Forms\Components\Textarea::make('popups.guide.text.uz')->label('Matn (UZ)')->rows(2),
                    Forms\Components\Textarea::make('popups.guide.text.ru')->label('Matn (RU)')->rows(2),
                    Forms\Components\FileUpload::make('popups.guide.image')->label('Rasm')->image()->disk('public')->directory('popups'),
                ]),
            ],
            'Qo‘shimcha' => [
                'code' => ['title' => 'Qo‘shimcha kod (Analytics, Pixel)', 'icon' => 'heroicon-o-code-bracket', 'paths' => ['head_code', 'body_code'], 'schema' => fn () => [
                    Forms\Components\Textarea::make('head_code')->label('<head> ichiga (Google Analytics, Meta Pixel...)')->rows(5),
                    Forms\Components\Textarea::make('body_code')->label('</body> oldidan')->rows(5),
                ]],
            ],
        ];
    }
}
