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

    /** Bo'sh maydonda saytda amalda ishlayotgan qiymat */
    protected function defaultFor(string $name): mixed
    {
        return ['calc.emp_free' => '10', 'calc.emp_step' => '60000', 'calc.ops_free' => '150', 'calc.ops_step' => '60000', 'site_name' => 'Buxgalter MChJ', 'hero_autoplay' => '6', 'mail_method' => 'server'][$name] ?? null;
    }

    protected function groupIcons(): array
    {
        return [
            'Sayt' => 'heroicon-o-globe-alt',
            'Arizalar xabarnomasi' => 'heroicon-o-bell-alert',
            'SEO va brending' => 'heroicon-o-magnifying-glass',
            'Analytics va tasdiqlash' => 'heroicon-o-chart-bar',
            'Huquqiy matnlar' => 'heroicon-o-scale',
            'Popuplar (maxsus takliflar)' => 'heroicon-o-megaphone',
            'Xizmat kalkulyatori' => 'heroicon-o-calculator',
            'Qo‘shimcha' => 'heroicon-o-adjustments-horizontal',
        ];
    }

    protected function blocks(): array
    {
        $popup = fn (string $key, string $title, string $icon, string $desc, \Closure $schema) => [
            'title' => $title, 'icon' => $icon, 'description' => $desc, 'paths' => ["popups.{$key}"], 'toggle' => "popups.{$key}.enabled", 'schema' => $schema,
        ];

        return [
            'Sayt' => [
                'contacts' => ['title' => 'Kontaktlar va asosiy', 'icon' => 'heroicon-o-phone', 'paths' => ['site_name', 'phone', 'work_hours', 'map_embed', 'map_tiles', 'hero_autoplay'], 'schema' => fn () => [
                    Forms\Components\TextInput::make('site_name')->label('Sayt nomi')->default('Buxgalter MChJ'),
                    Forms\Components\TextInput::make('phone')->label('Telefon')->placeholder('+998 55 500 37 37'),
                    Tr::text('work_hours', 'Ish vaqti'),
                    Forms\Components\TextInput::make('map_tiles')->label('Xarita fon qatlami (ixtiyoriy)')->placeholder('Standart: OpenStreetMap')
                        ->helperText('Odatda bo‘sh qoldiring. Boshqa xarita uslubi kerak bo‘lsa, {z}/{x}/{y} li tile havolasini kiriting (masalan, kalitli Stadia yoki MapTiler).'),
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
            ],
            'Arizalar xabarnomasi' => [
                'bot' => ['title' => 'Telegram bot', 'icon' => 'heroicon-o-paper-airplane', 'description' => 'Yangi ariza kelishi bilan Telegram guruh yoki shaxsiy chatga xabar keladi.', 'toggle' => 'notify_tg_enabled', 'paths' => ['telegram_bot_token', 'telegram_chat_id'],
                    'test' => ['label' => 'Sinov xabari', 'run' => fn () => app(\App\Services\LeadNotifier::class)->testTelegram()],
                    'schema' => fn () => [
                        Forms\Components\Placeholder::make('tg_help')->label('Qanday ulanadi')->content(new \Illuminate\Support\HtmlString('1) Telegram’da <b>@BotFather</b> → /newbot orqali bot yarating va tokenni nusxalang.<br>2) Botni guruhga qo‘shing (yoki botga o‘zingiz /start yozing).<br>3) Chat ID ni <b>@userinfobot</b> yoki <b>@getidsbot</b> orqali bilib oling. Guruh ID si odatda <code>-100</code> bilan boshlanadi.<br>4) Saqlang va “Sinov xabari” tugmasini bosing.')),
                        Forms\Components\TextInput::make('telegram_bot_token')->label('Bot token')->password()->revealable()->placeholder('123456789:AA...'),
                        Forms\Components\TextInput::make('telegram_chat_id')->label('Chat ID')->placeholder('-1001234567890')->helperText('Bir nechta chat bo‘lsa vergul bilan ajrating.'),
                    ]],
                'mail' => ['title' => 'Email xabarnoma', 'icon' => 'heroicon-o-envelope', 'description' => 'Har bir yangi ariza ko‘rsatilgan pochtaga xat bo‘lib keladi.', 'toggle' => 'notify_mail_enabled', 'paths' => ['notify_mail_to', 'mail_method', 'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password', 'mail_from_address', 'mail_from_name'],
                    'test' => ['label' => 'Sinov xati', 'run' => fn () => app(\App\Services\LeadNotifier::class)->testMail()],
                    'schema' => fn () => [
                        Forms\Components\TextInput::make('notify_mail_to')->label('Qabul qiluvchilar')->placeholder('info@buxmchj.uz, manager@buxmchj.uz')->helperText('Bir nechta manzil bo‘lsa vergul bilan ajrating.'),
                        Forms\Components\Radio::make('mail_method')->label('Yuborish usuli')->inline()->default('server')->live()
                            ->options(['server' => 'Server pochtasi (Plesk)', 'smtp' => 'SMTP (Gmail, Yandex, Plesk pochta)'])
                            ->helperText('SMTP ishonchliroq: xatlar “Spam”ga kamroq tushadi.'),
                        Forms\Components\Grid::make(2)->visible(fn (Forms\Get $get) => $get('mail_method') === 'smtp')->schema([
                            Forms\Components\TextInput::make('smtp_host')->label('SMTP server')->placeholder('smtp.gmail.com'),
                            Forms\Components\TextInput::make('smtp_port')->label('Port')->numeric()->placeholder('587'),
                            Forms\Components\Select::make('smtp_encryption')->label('Shifrlash')->options(['tls' => 'TLS (587)', 'ssl' => 'SSL (465)', 'none' => 'Yo‘q'])->default('tls'),
                            Forms\Components\TextInput::make('smtp_username')->label('Login')->placeholder('info@buxmchj.uz'),
                            Forms\Components\TextInput::make('smtp_password')->label('Parol')->password()->revealable()->helperText('Gmail uchun “App password” (ilova paroli) kerak.')->columnSpanFull(),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('mail_from_address')->label('Kimdan (email)')->email()->placeholder('noreply@buxmchj.uz')->helperText('SMTP’da odatda login bilan bir xil bo‘lishi kerak.'),
                            Forms\Components\TextInput::make('mail_from_name')->label('Kimdan (nom)')->placeholder('Buxgalter MChJ'),
                        ]),
                    ]],
                'sheets' => ['title' => 'Google Sheets', 'icon' => 'heroicon-o-table-cells', 'description' => 'Har bir yangi ariza Google jadvalga yangi qator bo‘lib yoziladi.', 'toggle' => 'sheets_enabled', 'paths' => ['sheets_url', 'sheets_tab'],
                    'test' => ['label' => 'Sinov qatori', 'run' => fn () => app(\App\Services\Integrations\GoogleSheets::class)->test()],
                    'schema' => fn () => [
                        Forms\Components\Placeholder::make('sheets_help')->label('Qanday ulanadi (bir marta, 3 daqiqa)')->content(fn () => new \Illuminate\Support\HtmlString(
                            '<ol style="list-style:decimal;padding-left:1.2rem;line-height:1.7">'
                            .'<li>Google Sheets’da yangi jadval oching → <b>Kengaytmalar → Apps Script</b>.</li>'
                            .'<li>U yerdagi kodni o‘chirib, pastdagi kodni to‘liq qo‘ying va saqlang (💾).</li>'
                            .'<li><b>Joylashtirish (Deploy) → Yangi joylashtirish</b> → turi: <b>Veb-ilova</b>. “Kim kira oladi”: <b>Hamma (Anyone)</b> → Joylashtirish → Google ruxsat so‘rasa, ruxsat bering.</li>'
                            .'<li>Chiqqan <b>Veb-ilova havolasini</b> (…/exec) quyidagi maydonga qo‘ying, saqlang va “Sinov qatori”ni bosing.</li></ol>'
                        )),
                        Forms\Components\Placeholder::make('sheets_code')->label('Apps Script kodi')->content(fn () => new \Illuminate\Support\HtmlString(
                            '<div x-data="{ok:false}" style="position:relative">'
                            .'<button type="button" x-on:click="navigator.clipboard.writeText($refs.code.innerText); ok=true; setTimeout(()=>ok=false,1500)" style="position:absolute;top:.5rem;right:.5rem;padding:.3rem .7rem;border-radius:.5rem;background:#f5a524;color:#16171a;font-size:.75rem;font-weight:600" x-text="ok ? \'Nusxalandi ✓\' : \'Nusxalash\'"></button>'
                            .'<pre x-ref="code" style="max-height:260px;overflow:auto;padding:1rem;border-radius:.75rem;background:#16171a;color:#e8e8ea;font-size:.72rem;line-height:1.5;white-space:pre">'.e(\App\Services\Integrations\GoogleSheets::script()).'</pre></div>'
                        )),
                        Forms\Components\TextInput::make('sheets_url')->label('Veb-ilova havolasi')->url()->placeholder('https://script.google.com/macros/s/…/exec')
                            ->regex('~^https://script\.google\.com/macros/s/[^/]+/exec~')->validationMessages(['regex' => 'Havola https://script.google.com/macros/s/…/exec ko‘rinishida bo‘lishi kerak.']),
                        Forms\Components\TextInput::make('sheets_tab')->label('Varaq (list) nomi')->placeholder('Arizalar')->helperText('Bo‘lmasa avtomatik yaratiladi.'),
                    ]],
                'amo' => ['title' => 'amoCRM', 'icon' => 'heroicon-o-briefcase', 'description' => 'Har bir arizadan amoCRM’da sdelka va kontakt yaratiladi.', 'toggle' => 'amo_enabled', 'paths' => ['amo_domain', 'amo_token', 'amo_pipeline_id', 'amo_status_id', 'amo_responsible_id'],
                    'test' => ['label' => 'Ulanishni tekshirish', 'run' => fn () => (new \App\Services\Integrations\AmoCrm)->test()],
                    'schema' => fn () => [
                        Forms\Components\Placeholder::make('amo_help')->label('Token qayerdan olinadi')->content(new \Illuminate\Support\HtmlString(
                            '<ol style="list-style:decimal;padding-left:1.2rem;line-height:1.7">'
                            .'<li>amoCRM → <b>Sozlamalar (Настройки) → Integratsiyalar (Интеграции)</b> → o‘ng yuqorida <b>Integratsiya yaratish (Создать интеграцию)</b> → “Внешняя интеграция”.</li>'
                            .'<li>Hamma ruxsatlarni belgilang, nom bering (masalan, “Sayt”) va saqlang.</li>'
                            .'<li>Integratsiyaning <b>Ключи и доступы</b> bo‘limida <b>Долгосрочный токен</b> yarating (muddati: 5 yil) va nusxalang.</li>'
                            .'<li>Manzil va tokenni kiriting — shundan keyin voronka va bosqich ro‘yxati chiqadi.</li></ol>'
                        )),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('amo_domain')->label('amoCRM manzili')->placeholder('buxmchj.amocrm.ru')->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ? \App\Services\Integrations\AmoCrm::normalizeDomain($state) : null),
                            Forms\Components\TextInput::make('amo_token')->label('Uzoq muddatli token')->password()->revealable()->live(onBlur: true),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('amo_pipeline_id')->label('Voronka')->live()->placeholder('Asosiy voronka')
                                ->options(fn (Forms\Get $get) => collect((new \App\Services\Integrations\AmoCrm($get('amo_domain'), $get('amo_token')))->pipelines())->map(fn ($p) => $p['name'])->all())
                                ->afterStateUpdated(fn (Forms\Set $set) => $set('amo_status_id', null)),
                            Forms\Components\Select::make('amo_status_id')->label('Bosqich')->placeholder('Voronkaning birinchi bosqichi')
                                ->options(fn (Forms\Get $get) => (new \App\Services\Integrations\AmoCrm($get('amo_domain'), $get('amo_token')))->pipelines()[(int) $get('amo_pipeline_id')]['statuses'] ?? []),
                            Forms\Components\Select::make('amo_responsible_id')->label('Mas’ul xodim')->placeholder('Token egasi')->searchable()
                                ->options(fn (Forms\Get $get) => (new \App\Services\Integrations\AmoCrm($get('amo_domain'), $get('amo_token')))->users())
                                ->helperText('Ro‘yxat bo‘sh bo‘lsa — manzil yoki token xato, yoki hali kiritilmagan.')->columnSpanFull(),
                        ]),
                    ]],
            ],
            'SEO va brending' => [
                'og' => ['title' => 'Ulashish rasmi (OG)', 'icon' => 'heroicon-o-photo', 'description' => 'Telegram, Facebook va boshqalarda havola ulashilganda chiqadigan rasm.', 'paths' => ['og_image'], 'schema' => fn () => [
                    Forms\Components\FileUpload::make('og_image')->label('OG rasm (1200×630 px, JPG yoki PNG)')->image()->disk('public')->directory('seo')->imageEditor()
                        ->imageEditorAspectRatios(['1.91:1'])->maxSize(4096)->helperText('Kurs va maqola sahifalarida ularning o‘z rasmi ishlatiladi.'),
                ]],
                'favicon' => ['title' => 'Favicon (brauzer belgisi)', 'icon' => 'heroicon-o-sparkles', 'description' => 'Yorug‘ va qorong‘i rejim uchun alohida. Kvadrat PNG yoki SVG, kamida 192×192.', 'paths' => ['favicon_light', 'favicon_dark'], 'schema' => fn () => [
                    Forms\Components\FileUpload::make('favicon_light')->label('Yorug‘ rejim uchun')->image()->disk('public')->directory('seo')->imageEditor()->imageEditorAspectRatios(['1:1'])
                        ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/jpeg', 'image/webp'])->maxSize(1024),
                    Forms\Components\FileUpload::make('favicon_dark')->label('Qorong‘i rejim uchun')->image()->disk('public')->directory('seo')->imageEditor()->imageEditorAspectRatios(['1:1'])
                        ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/jpeg', 'image/webp'])->maxSize(1024),
                ]],
                'indexing' => ['title' => 'Qidiruv tizimlari', 'icon' => 'heroicon-o-eye-slash', 'description' => 'Sayt tayyor bo‘lmasa, Google’da ko‘rinmasligi uchun yoqing.', 'paths' => ['seo_noindex'], 'schema' => fn () => [
                    Forms\Components\Toggle::make('seo_noindex')->label('Qidiruv tizimlaridan yashirish (noindex)')->helperText('Odatda O‘CHIQ bo‘lishi kerak. Yoqilsa, sayt Google natijalaridan chiqib ketadi.'),
                ]],
            ],
            'Analytics va tasdiqlash' => [
                'google' => ['title' => 'Analytics va saytni tasdiqlash', 'icon' => 'heroicon-o-chart-bar', 'description' => 'Tashrif statistikasi va Google, Yandex, Bing’da saytga egalikni tasdiqlash.', 'paths' => ['ga_id', 'gsc_meta', 'yandex_meta', 'bing_meta', 'verify_files'], 'schema' => fn () => [
                    Forms\Components\TextInput::make('ga_id')->label('Google Analytics o‘lchov ID')->placeholder('G-XXXXXXXXXX')
                        ->regex('/^G-[A-Z0-9]{4,}$/')->validationMessages(['regex' => 'ID “G-” bilan boshlanadi, masalan G-AB12CD34EF.'])
                        ->helperText('Analytics → Administrator → Ma’lumotlar oqimlari → veb-oqim → “O‘lchov ID”. Kiritilsa, kod hamma sahifaga avtomatik qo‘shiladi.'),
                    Forms\Components\TextInput::make('gsc_meta')->label('Search Console meta-teg (ixtiyoriy)')->placeholder('masalan: abc123XYZ...')
                        ->helperText('Search Console “HTML-teg” usulini bersa, content="..." ichidagi qiymatni shu yerga qo‘ying.'),
                    Forms\Components\TextInput::make('yandex_meta')->label('Yandex Webmaster meta-teg (ixtiyoriy)')->placeholder('masalan: 1a2b3c4d5e6f7a8b')
                        ->helperText('Yandex Webmaster → “Meta-teg” usuli: <meta name="yandex-verification" content="..."> ichidagi qiymat. O‘zbekistonda Yandex ham ko‘p ishlatiladi.'),
                    Forms\Components\TextInput::make('bing_meta')->label('Bing Webmaster meta-teg (ixtiyoriy)')->placeholder('masalan: 0123ABCD...')
                        ->helperText('Bing Webmaster Tools → “HTML Meta Tag”: msvalidate.01 ichidagi qiymat.'),
                    Forms\Components\FileUpload::make('verify_files')->label('Tasdiqlash fayllari (Google, Yandex, Bing)')->multiple()->disk('local')->directory('verification')
                        ->preserveFilenames()->acceptedFileTypes(['text/html', 'text/plain', 'application/xml', 'text/xml'])->maxSize(64)->downloadable()
                        ->helperText('Search Console bergan google....html faylini shu yerga yuklang. U https://buxmchj.uz/fayl-nomi.html manzilida ochiladi, keyin Search Console’da “Tasdiqlash”ni bosing.'),
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
            'Xizmat kalkulyatori' => [
                'calc' => ['title' => 'Narx kalkulyatori qoidalari', 'icon' => 'heroicon-o-calculator', 'description' => 'Asosiy narxlar “Xizmat tariflari”dan olinadi. Bu yerda — qo‘shimcha xodim va operatsiyalar uchun ustama.', 'paths' => ['calc'], 'schema' => fn () => [
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('calc.emp_free')->label('Narxga kiradigan xodimlar soni')->numeric()->minValue(0)->placeholder('10')->suffix('kishi'),
                        Forms\Components\TextInput::make('calc.emp_step')->label('Har bir qo‘shimcha xodim uchun')->numeric()->minValue(0)->placeholder('60000')->suffix('so‘m'),
                        Forms\Components\TextInput::make('calc.ops_free')->label('Narxga kiradigan oylik operatsiyalar')->numeric()->minValue(0)->placeholder('150')->suffix('ta'),
                        Forms\Components\TextInput::make('calc.ops_step')->label('Har 10 ta qo‘shimcha operatsiya uchun')->numeric()->minValue(0)->placeholder('60000')->suffix('so‘m'),
                    ]),
                ]],
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
