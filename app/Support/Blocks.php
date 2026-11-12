<?php

namespace App\Support;

use App\Filament\Support\Tr;
use Filament\Forms;
use Filament\Forms\Components\Builder;

/**
 * Sahifa konstruktori: saytdagi tayyor seksiyalar va erkin bloklar ro'yxati.
 * Har bir sahifa (bosh, kurslar, xizmat, aloqa, blog, kurs, maqola va qo'shimcha sahifalar)
 * shu bloklardan yig'iladi. Tartib: admin → Sahifalar → (sahifa) → Tahrirlash.
 */
class Blocks
{
    /** Tizim sahifalari: kalit => nomi */
    public const PAGES = [
        'home' => 'Bosh sahifa', 'courses' => 'Kurslar sahifasi', 'services' => 'Xizmat sahifasi', 'contact' => 'Aloqa sahifasi',
        'blog' => 'Maqolalar (blog)', 'course' => 'Kurs sahifasi (har bir kurs)', 'article' => 'Maqola sahifasi (har bir maqola)',
    ];

    /** Har bir sahifaning hozirgi (dizayndagi) tuzilishi */
    public const DEFAULTS = [
        'home' => ['hero', 'directions', 'why', 'courses_overview', 'services_overview', 'faq', 'articles', ['consultation', ['interest' => 'course']], 'instagram'],
        'courses' => ['courses_hero', 'courses_formats', 'why', 'courses_career', ['teachers', ['soft' => true]], 'graduates', 'courses_start', 'faq', ['consultation', ['interest' => 'course']]],
        'services' => ['services_hero', 'services_bento', 'services_plans', 'services_calc', 'services_start', 'faq', ['consultation', ['interest' => 'service']]],
        'contact' => ['contact_hero', 'contact_form', 'contact_branches', 'faq', 'instagram'],
        'blog' => ['blog_main', 'blog_telegram'],
        'course' => ['course_main', ['teachers', ['soft' => false]], 'faq', ['consultation', ['interest' => 'course']]],
        'article' => ['article_main', 'article_related'],
        'page' => [],
    ];

    /**
     * Bloklar: kalit => [nomi, guruh, ikonka, view, izoh, (faqat shu sahifalarda)].
     * Tayyor seksiyalarning matnlari seksiyaning o‘zida ("Matnlar" bo‘limi).
     */
    public static function all(): array
    {
        return [
            // ---- Erkin bloklar (matni shu yerning o'zida yoziladi) ----
            'page_head' => ['Sarlavha (sahifa boshi)', 'Erkin bloklar', 'heroicon-o-h1', 'blocks.free.page-head', 'Sariq fonli sarlavha, izoh, rasm va tugmalar.'],
            'text' => ['Matn', 'Erkin bloklar', 'heroicon-o-document-text', 'blocks.free.text', 'Formatlangan matn (sarlavhalar, ro‘yxatlar, havolalar, rasmlar).'],
            'image_text' => ['Rasm + matn', 'Erkin bloklar', 'heroicon-o-photo', 'blocks.free.image-text', 'Bir tomonda rasm, ikkinchi tomonda sarlavha, matn va tugma.'],
            'cards' => ['Kartochkalar', 'Erkin bloklar', 'heroicon-o-squares-2x2', 'blocks.free.cards', 'Ikonkali kartochkalar to‘plami (afzalliklar, xizmatlar…).'],
            'stats' => ['Raqamlar', 'Erkin bloklar', 'heroicon-o-chart-bar', 'blocks.free.stats', 'Katta raqamlar va izohlari (10 yil, 36+ guruh…).'],
            'steps' => ['Qadamlar', 'Erkin bloklar', 'heroicon-o-list-bullet', 'blocks.free.steps', 'Raqamlangan bosqichlar (qanday ishlaymiz).'],
            'cta' => ['Chaqiruv (CTA) banneri', 'Erkin bloklar', 'heroicon-o-megaphone', 'blocks.free.cta', 'Sariq banner: sarlavha, matn va tugma.'],
            // ---- Saytning tayyor seksiyalari (matnlari seksiyaning o'zida — "Matnlar" bo'limi) ----
            'hero' => ['Bosh slayder (hero)', 'Bosh sahifa seksiyalari', 'heroicon-o-photo', 'blocks.sec.hero', 'Slaydlar: Bosh sahifa → Hero slaydlar.'],
            'directions' => ['Yo‘nalishlar (kurs / xizmat)', 'Bosh sahifa seksiyalari', 'heroicon-o-squares-2x2', 'blocks.sec.directions', ''],
            'why' => ['Nega biz', 'Bosh sahifa seksiyalari', 'heroicon-o-star', 'blocks.sec.why', ''],
            'courses_overview' => ['Kurslar ro‘yxati', 'Bosh sahifa seksiyalari', 'heroicon-o-academic-cap', 'blocks.sec.courses-overview', 'Kurslar: Kontent → Kurslar.'],
            'services_overview' => ['Xizmatlar (qisqa)', 'Bosh sahifa seksiyalari', 'heroicon-o-briefcase', 'blocks.sec.services-overview', 'Xizmatlar: Kontent → Xizmatlar (“bosh sahifada” belgilanganlari).'],
            'faq' => ['Savol-javoblar (FAQ)', 'Umumiy seksiyalar', 'heroicon-o-question-mark-circle', 'blocks.sec.faq', 'Savollar: Kontent → Savol-javoblar.'],
            'articles' => ['So‘nggi maqolalar', 'Umumiy seksiyalar', 'heroicon-o-newspaper', 'blocks.sec.articles', 'Maqolalar: Kontent → Maqolalar.'],
            'consultation' => ['Ariza formasi (konsultatsiya)', 'Umumiy seksiyalar', 'heroicon-o-chat-bubble-left-right', 'blocks.sec.consultation', ''],
            'instagram' => ['Instagram lentasi', 'Umumiy seksiyalar', 'heroicon-o-camera', 'blocks.sec.instagram', 'Postlar: Bosh sahifa → Instagram postlar.'],
            'teachers' => ['Ustozlar', 'Umumiy seksiyalar', 'heroicon-o-users', 'blocks.sec.teachers', 'Ustozlar: Kontent → Ustozlar.'],
            'graduates' => ['Bitiruvchilar fikri', 'Umumiy seksiyalar', 'heroicon-o-user-group', 'blocks.sec.graduates', 'Videolar: Kontent → Bitiruvchilar fikri.'],
            'courses_hero' => ['Kurslar: sarlavha', 'Kurslar sahifasi seksiyalari', 'heroicon-o-academic-cap', 'blocks.courses-hero', ''],
            'courses_formats' => ['Kurs formatlari (kartochkalar)', 'Kurslar sahifasi seksiyalari', 'heroicon-o-rectangle-stack', 'blocks.courses-formats', 'Kurslar: Kontent → Kurslar.'],
            'courses_career' => ['Kursdan keyingi yo‘l (karyera)', 'Kurslar sahifasi seksiyalari', 'heroicon-o-arrow-trending-up', 'blocks.courses-career', ''],
            'courses_start' => ['O‘qish qanday boshlanadi', 'Kurslar sahifasi seksiyalari', 'heroicon-o-list-bullet', 'blocks.courses-start', ''],
            'services_hero' => ['Xizmat: sarlavha', 'Xizmat sahifasi seksiyalari', 'heroicon-o-briefcase', 'blocks.services-hero', ''],
            'services_bento' => ['Nimalarni o‘z zimmamizga olamiz', 'Xizmat sahifasi seksiyalari', 'heroicon-o-squares-plus', 'blocks.services-bento', 'Xizmatlar: Kontent → Xizmatlar.'],
            'services_plans' => ['Xizmat tariflari', 'Xizmat sahifasi seksiyalari', 'heroicon-o-banknotes', 'blocks.services-plans', 'Tariflar: Kontent → Xizmat tariflari.'],
            'services_calc' => ['Narx kalkulyatori', 'Xizmat sahifasi seksiyalari', 'heroicon-o-calculator', 'blocks.services-calc', 'Qoidalar: Sayt sozlamalari → Xizmat kalkulyatori.'],
            'services_start' => ['Ishni qanday boshlaymiz', 'Xizmat sahifasi seksiyalari', 'heroicon-o-list-bullet', 'blocks.services-start', ''],
            'contact_hero' => ['Aloqa: sarlavha va kontaktlar', 'Aloqa sahifasi seksiyalari', 'heroicon-o-phone', 'blocks.contact-hero', 'Telefon va tarmoqlar: Sayt sozlamalari.'],
            'contact_form' => ['Aloqa: ariza formasi', 'Aloqa sahifasi seksiyalari', 'heroicon-o-pencil-square', 'blocks.contact-form', ''],
            'contact_branches' => ['Manzil va xarita', 'Aloqa sahifasi seksiyalari', 'heroicon-o-map-pin', 'blocks.contact-branches', 'Filiallar: Kontent → Filiallar.'],
            'blog_telegram' => ['Telegram kanalga chaqiruv', 'Umumiy seksiyalar', 'heroicon-o-paper-airplane', 'blocks.blog-telegram', ''],
            // ---- Asosiy (faqat o'z sahifasida, olib tashlanmaydi) ----
            'blog_main' => ['Maqolalar ro‘yxati (asosiy)', 'Asosiy qism', 'heroicon-o-lock-closed', 'blocks.blog-main', 'Blog sahifasining asosiy qismi: qidiruv, kategoriyalar, maqolalar.', ['blog']],
            'course_main' => ['Kurs ma’lumoti (asosiy)', 'Asosiy qism', 'heroicon-o-lock-closed', 'blocks.course-main', 'Kurs sahifasining asosiy qismi: Kontent → Kurslar’dan.', ['course']],
            'article_main' => ['Maqola matni (asosiy)', 'Asosiy qism', 'heroicon-o-lock-closed', 'blocks.article-main', 'Maqola sahifasining asosiy qismi.', ['article']],
            'article_related' => ['O‘xshash maqolalar', 'Asosiy qism', 'heroicon-o-newspaper', 'blocks.article-related', '', ['article']],
        ];
    }

    public static function get(string $type): ?array
    {
        return self::all()[$type] ?? null;
    }

    public static function label(string $type): string
    {
        return self::all()[$type][0] ?? $type;
    }

    /** Sahifaning tuzilishi: admin'da saqlangan, bo'lmasa standart */
    public static function layout(string $page): array
    {
        $saved = \App\Models\Page::forSystem($page)?->sections;

        return is_array($saved) && $saved !== [] ? array_values($saved) : self::defaultLayout($page);
    }

    public static function defaultLayout(string $page): array
    {
        return array_map(fn ($i) => is_array($i) ? ['type' => $i[0], 'data' => $i[1]] : ['type' => $i, 'data' => []], self::DEFAULTS[$page] ?? []);
    }

    /** Kalkulyator va tariflar uchun asosiy narxlar */
    public static function serviceBase($plans): array
    {
        $l = collect($plans)->values();

        return [
            'yatt' => (int) ($l[0]->price ?? 1500000),
            'llc' => (int) ($l[1]->price ?? 3000000),
            'llc_vat' => (int) ($l[2]->price ?? 5000000),
            'import' => (int) ($l[3]->price ?? 8000000),
            'emp_free' => (int) (setting('calc.emp_free') ?: 10),
            'emp_step' => (int) (setting('calc.emp_step') ?: 60000),
            'ops_free' => (int) (setting('calc.ops_free') ?: 150),
            'ops_step' => (int) (setting('calc.ops_step') ?: 60000),
        ];
    }

    // ===================== Admin: Filament Builder =====================

    /** @param  string|null  $page  tizim sahifasi kaliti (asosiy bloklar faqat o'z sahifasida chiqadi) */
    public static function builder(string $name, ?string $page = null): Builder
    {
        $blocks = [];
        foreach (self::all() as $type => $def) {
            [$label, $group, $icon, , $hint] = $def;
            $only = $def[5] ?? null;
            if ($only && ! in_array($page, $only, true)) {
                continue;
            }
            $blocks[] = Builder\Block::make($type)->label(fn (?array $state) => self::itemLabel($type, $state))->icon($icon)
                ->schema(self::fields($type, $hint))->columns(1);
        }

        return Builder::make($name)->label('Seksiyalar')->hiddenLabel()->blocks($blocks)->blockNumbers(false)
            ->afterStateHydrated(fn (Builder $component, $state) => $component->state(self::hydrateTexts($state)))
            ->addActionLabel('Seksiya qo‘shish')->addBetweenActionLabel('Shu yerga qo‘shish')
            ->blockPickerColumns(['md' => 2])->blockPickerWidth('3xl')
            ->reorderableWithDragAndDrop()->reorderableWithButtons()->collapsible()->collapsed()->cloneable()
            ->deleteAction(fn ($action) => $action->requiresConfirmation()->modalHeading('Seksiyani o‘chirish')->modalDescription('Seksiya sahifadan olib tashlanadi. Uning matnlari o‘chmaydi.'));
    }

    private static function itemLabel(string $type, ?array $state): string
    {
        $base = self::label($type);
        $t = $state['title']['uz'] ?? null;
        $hidden = ! empty($state['hidden']) ? '  · yashirilgan' : '';

        return ($t ? $base.': '.$t : $base).$hidden;
    }

    /** Har bir blokning sozlamalari */
    private static function fields(string $type, string $hint): array
    {
        $note = $hint ? [Forms\Components\Placeholder::make('hint')->hiddenLabel()->content($hint)] : [];
        $common = [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('anchor')->label('Havola uchun nom (ixtiyoriy)')->prefix('#')->placeholder('masalan: narxlar')
                    ->helperText('Menyudan shu seksiyaga olib boruvchi havola uchun.')->regex('/^[a-z0-9-]*$/'),
                Forms\Components\Toggle::make('hidden')->label('Vaqtincha yashirish')->helperText('Saytda ko‘rinmaydi, tuzilishda qoladi.')->inline(false),
            ]),
        ];
        $bg = Forms\Components\Select::make('bg')->label('Fon')->options(['' => 'Oq', 'soft' => 'Kulrang (yumshoq)'])->default('')->selectablePlaceholder(false);
        $btn = fn (string $p, string $l) => Forms\Components\Fieldset::make($l)->columns(2)->schema([
            Tr::text("{$p}_label", 'Tugma matni'),
            Forms\Components\TextInput::make("{$p}_url")->label('Havola')->placeholder('#consultation, /contact yoki https://…')->columnSpanFull(),
        ]);

        $own = match ($type) {
            'page_head' => [
                Tr::text('title', 'Sarlavha (bo‘sh qolsa — sahifa nomi)'), Tr::textarea('lead', 'Izoh', false, 2),
                Forms\Components\FileUpload::make('image')->label('Rasm (o‘ng tomonda, ixtiyoriy)')->image()->disk('public')->directory('blocks')->imageEditor(),
                $btn('btn1', '1-tugma (qora)'), $btn('btn2', '2-tugma (oq)'),
            ],
            'text' => [Tr::text('title', 'Sarlavha (ixtiyoriy)'), Tr::rich('body', 'Matn', 'blocks'), $bg],
            'image_text' => [
                Tr::text('title', 'Sarlavha', true), Tr::textarea('text', 'Matn', false, 4),
                Forms\Components\FileUpload::make('image')->label('Rasm')->image()->disk('public')->directory('blocks')->imageEditor()->required(),
                Forms\Components\Radio::make('side')->label('Rasm qayerda')->options(['left' => 'Chapda', 'right' => 'O‘ngda'])->default('left')->inline(),
                $btn('btn', 'Tugma (ixtiyoriy)'), $bg,
            ],
            'cards' => [
                Tr::text('title', 'Sarlavha'), Tr::textarea('lead', 'Izoh', false, 2),
                Forms\Components\Repeater::make('items')->label('Kartochkalar')->schema([
                    \App\Filament\Forms\IconPicker::make('icon')->label('Ikonka'),
                    Tr::text('title', 'Sarlavha', true), Tr::textarea('text', 'Matn', false, 2),
                ])->addActionLabel('Kartochka qo‘shish')->collapsible()->reorderableWithDragAndDrop()->itemLabel(fn (array $state) => $state['title']['uz'] ?? null)->minItems(1),
                $bg,
            ],
            'stats' => [
                Tr::text('title', 'Sarlavha (ixtiyoriy)'),
                Forms\Components\Repeater::make('items')->label('Raqamlar')->schema([
                    Forms\Components\Grid::make(2)->schema([Tr::text('value', 'Raqam', true), Tr::text('label', 'Izoh')]),
                ])->addActionLabel('Raqam qo‘shish')->collapsible()->reorderableWithDragAndDrop()->itemLabel(fn (array $state) => $state['value']['uz'] ?? null)->maxItems(6),
                $bg,
            ],
            'steps' => [
                Tr::text('title', 'Sarlavha'), Tr::textarea('lead', 'Izoh', false, 2),
                Forms\Components\Repeater::make('items')->label('Qadamlar')->schema([Tr::text('title', 'Qadam', true), Tr::textarea('text', 'Izoh', false, 2)])
                    ->addActionLabel('Qadam qo‘shish')->collapsible()->reorderableWithDragAndDrop()->itemLabel(fn (array $state) => $state['title']['uz'] ?? null),
                $bg,
            ],
            'cta' => [Tr::text('title', 'Sarlavha', true), Tr::textarea('text', 'Matn', false, 2), $btn('btn', 'Tugma')],
            'consultation' => [Forms\Components\Radio::make('interest')->label('Formada oldindan tanlangan')->options(['course' => 'Kurslar', 'service' => 'Buxgalteriya xizmati'])->default('course')->inline()],
            'teachers' => [Forms\Components\Toggle::make('soft')->label('Kulrang fon')],
            'faq' => [Forms\Components\Select::make('set')->label('Qaysi savollar')->placeholder('Shu sahifaga belgilanganlari')->options(\App\Filament\Resources\FaqResource::PAGES)],
            default => [],
        };

        $texts = self::textFields($type);

        return [...$note, ...$own, ...$texts, ...$common];
    }

    /** Seksiya shablonida ishlatilgan matn kalitlari (why.title, …) */
    public static function textKeys(string $type): array
    {
        static $cache = [];
        $def = self::get($type);

        return $cache[$type] ??= $def ? \App\Support\TextDefaults::keysInView($def[3]) : [];
    }

    /** Seksiyaning "Matnlar" bo'limi: har bir matn — shu seksiyaning o'zida tahrirlanadi */
    private static function textFields(string $type): array
    {
        $keys = self::textKeys($type);
        if ($keys === []) {
            return [];
        }
        static $labels;
        $labels ??= require __DIR__.'/text-labels.php';
        $fields = [];
        foreach ($keys as $k) {
            $d = \App\Support\TextDefaults::for($k);
            $name = 'texts.'.$k;
            $uz = is_array($d) && ! array_is_list($d) ? (string) ($d['uz'] ?? '') : '';
            $label = $labels[$k] ?? ($uz !== '' ? '«'.\Illuminate\Support\Str::limit($uz, 48).'»' : str_replace('_', ' ', \Illuminate\Support\Str::afterLast($k, '.')));
            if (is_string($d) && str_starts_with($d, 'img/')) {
                $fields[] = Forms\Components\FileUpload::make($name)->label($label)->image()->disk('public')->directory('pages')->imageEditor()
                    ->helperText('Bo‘sh qolsa — dizayndagi standart rasm.');
            } elseif (is_array($d) && array_is_list($d)) {
                $fields[] = Forms\Components\Repeater::make($name)->label($label)->schema([Tr::text('text', 'Band', true)])
                    ->addActionLabel('Band qo‘shish')->reorderableWithDragAndDrop()->collapsible()->itemLabel(fn (array $state) => $state['text']['uz'] ?? null)->defaultItems(0);
            } else {
                $fields[] = mb_strlen($uz) > 70 ? Tr::textarea($name, $label, false, 2) : Tr::text($name, $label);
            }
        }

        return [Forms\Components\Section::make('Matnlar')->icon('heroicon-o-language')->description('Shu seksiyadagi barcha yozuvlar. Saytda hozir turgan matn bilan to‘ldirilgan.')->collapsible()->compact()->schema($fields)];
    }

    /** Matnning amaldagi qiymati: umumiy (eski) sozlama yoki shablondagi standart */
    public static function effectiveText(string $key): mixed
    {
        $v = data_get(setting('texts', []), $key);
        if (filled($v)) {
            return $v;
        }
        $d = \App\Support\TextDefaults::for($key);

        return is_string($d) && str_starts_with($d, 'img/') ? null : $d;
    }

    /** Konstruktor ochilganda: seksiyalardagi bo'sh matnlarni amaldagi qiymat bilan to'ldirish */
    public static function hydrateTexts(?array $state): ?array
    {
        if (! is_array($state)) {
            return $state;
        }
        foreach ($state as $uuid => $item) {
            foreach (self::textKeys($item['type'] ?? '') as $k) {
                if (blank(data_get($item, 'data.texts.'.$k))) {
                    $v = self::effectiveText($k);
                    if (filled($v)) {
                        data_set($state[$uuid], 'data.texts.'.$k, $v);
                    }
                }
            }
        }

        return $state;
    }
}
