<?php

namespace App\Filament\Pages;

use App\Filament\Forms\CardsField;
use App\Filament\Pages\Concerns\HasSettingBlocks;
use App\Filament\Support\Tr;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

/**
 * Sahifalardagi sarlavha va matnlar (kartochkalar ko'rinishida, tahrirlash drawer'da).
 * Bo'sh qoldirilgan maydon o'rniga standart (dizayndagi) matn chiqadi.
 */
class PageTexts extends Page implements HasForms, HasActions
{
    use HasSettingBlocks;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Bosh sahifa';
    protected static ?string $navigationLabel = 'Sahifa matnlari';
    protected static ?string $title = 'Sahifa matnlari';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $u = auth()->user();

        return $u && in_array($u->role, ['super_admin', 'admin', 'editor'], true);
    }

    protected function hint(): ?string
    {
        return 'Bo‘sh qoldirilgan maydonlarda dizayndagi standart matn ko‘rinadi. Tahrirlash uchun blok yonidagi tugmani bosing.';
    }

    protected function emptyLabel(): string
    {
        return '— standart matn';
    }

    private static function list(string $name, string $label): CardsField
    {
        return CardsField::make($name)->label($label)->kind('feature')->cols(2)->itemName('band')->addLabel('Band qo‘shish')->drawerWidth('md')
            ->itemSchema(fn () => [Tr::text('text', 'Band', true)]);
    }

    private static function image(string $name, string $label): Forms\Components\FileUpload
    {
        return Forms\Components\FileUpload::make($name)->label($label)->image()->disk('public')->directory('pages')->imageEditor();
    }

    /** Bo'sh maydonda saytda hozir ko'rinib turgan matn (shablondagi standart) */
    protected function defaultFor(string $name): mixed
    {
        return \App\Support\TextDefaults::for($name);
    }

    protected function groupIcons(): array
    {
        return [
            'Bosh sahifa' => 'heroicon-o-home', 'Kurslar sahifasi' => 'heroicon-o-academic-cap', 'Kurs sahifasi' => 'heroicon-o-book-open',
            'Xizmat sahifasi' => 'heroicon-o-briefcase', 'Maqolalar sahifasi' => 'heroicon-o-newspaper', 'Maqola sahifasi' => 'heroicon-o-document-text',
            'Aloqa sahifasi' => 'heroicon-o-phone',
        ];
    }

    /** Matn kaliti prefiksi => [bo'lim, yangi blok nomi, ikonka] (qo'lda yozilmagan matnlar shu yerga avtomatik tushadi) */
    private const AUTO = [
        'hero' => ['Bosh sahifa', 'Hero (birinchi ekran)', 'heroicon-o-photo'],
        'graduates' => ['Bosh sahifa', 'Bitiruvchilar bloki', 'heroicon-o-user-group'],
        'teachers' => ['Bosh sahifa', 'Ustozlar bloki', 'heroicon-o-users'],
        'instagram' => ['Bosh sahifa', 'Instagram bloki', 'heroicon-o-camera'],
        'home' => ['Bosh sahifa', 'Boshqa matnlar', 'heroicon-o-ellipsis-horizontal-circle'],
        'courses_page' => ['Kurslar sahifasi', 'Kurslar sahifasi', 'heroicon-o-academic-cap'],
        'career' => ['Kurslar sahifasi', 'Karyera bloki', 'heroicon-o-briefcase'],
        'course' => ['Kurs sahifasi', 'Kurs sahifasi matnlari', 'heroicon-o-book-open'],
        'services_page' => ['Xizmat sahifasi', 'Xizmat sahifasi', 'heroicon-o-briefcase'],
        'blog' => ['Maqolalar sahifasi', 'Maqolalar sahifasi', 'heroicon-o-newspaper'],
        'article' => ['Maqola sahifasi', 'Maqola sahifasi matnlari', 'heroicon-o-document-text'],
        'contact' => ['Aloqa sahifasi', 'Aloqa sahifasi', 'heroicon-o-phone'],
    ];

    protected function blocks(): array
    {
        static $built;

        return $built ??= $this->withAutoFields($this->manualBlocks());
    }

    /**
     * Shablonlardagi hs('...') matnlaridan qo'lda yozilmaganlari avtomatik maydon bo'ladi:
     * shu prefiksli blok bo'lsa — uning "Boshqa matnlar" qismiga, bo'lmasa — yangi blok.
     */
    private function withAutoFields(array $groups): array
    {
        $covered = [];
        $owner = [];
        foreach ($groups as $g => $blocks) {
            foreach ($blocks as $key => $b) {
                foreach ($this->makeForm()->schema(($b['schema'])())->getFlatComponents(withHidden: true) as $c) {
                    if ($c instanceof Forms\Components\Field) {
                        $covered[preg_replace('~\.(uz|ru)$~', '', $c->getName())] = true;
                    }
                }
                foreach ($b['paths'] as $path) {
                    $owner[preg_replace('/^texts\./', '', $path)] ??= [$g, $key];
                }
            }
        }
        $extra = [];
        foreach (\App\Support\TextDefaults::all() as $k => $default) {
            $prefix = strtok($k, '.');
            if (isset($covered['texts.'.$k]) || in_array($prefix, ['header', 'footer'], true)) {
                continue;
            }
            $extra[$prefix][$k] = $default;
        }
        foreach ($extra as $prefix => $items) {
            $fields = fn () => collect($items)->map(fn ($d, $k) => self::autoField('texts.'.$k, $d))->values()->all();
            if (isset($owner[$prefix])) {
                [$g, $key] = $owner[$prefix];
                $old = $groups[$g][$key]['schema'];
                $groups[$g][$key]['schema'] = fn () => [...$old(), Forms\Components\Section::make('Boshqa matnlar')->schema($fields())];

                continue;
            }
            [$g, $title, $icon] = self::AUTO[$prefix] ?? ['Bosh sahifa', ucfirst(str_replace('_', ' ', $prefix)), 'heroicon-o-document-text'];
            $groups[$g]['auto_'.$prefix] = ['title' => $title, 'icon' => $icon, 'paths' => ['texts.'.$prefix], 'schema' => $fields];
        }
        // bo'limlar tartibi
        $order = array_keys($this->groupIcons());

        return collect($groups)->sortBy(fn ($b, $g) => array_search($g, $order, true) === false ? 99 : array_search($g, $order, true))->all();
    }

    /** Avtomatik maydon: yorlig'i — saytdagi matnning o'zi (qaysi matn ekani darhol tushunarli) */
    private static function autoField(string $name, mixed $default): Forms\Components\Component
    {
        if (is_string($default) && str_starts_with($default, 'img/')) {
            return self::image($name, 'Rasm');
        }
        if (is_array($default) && array_is_list($default)) {
            return self::list($name, 'Ro‘yxat');
        }
        $uz = is_array($default) ? (string) ($default['uz'] ?? '') : '';
        $label = '«'.\Illuminate\Support\Str::limit($uz, 48).'»';

        return mb_strlen($uz) > 70 ? Tr::textarea($name, $label, false, 2) : Tr::text($name, $label);
    }

    private function manualBlocks(): array
    {
        return [
            'Bosh sahifa' => [
                'directions' => ['title' => 'Yo‘nalishlar', 'icon' => 'heroicon-o-squares-2x2', 'paths' => ['texts.directions'], 'schema' => fn () => [
                    Tr::text('texts.directions.title', 'Sarlavha'),
                    Tr::textarea('texts.directions.lead', 'Izoh', false, 2),
                    Forms\Components\Fieldset::make('Kurslar kartochkasi')->columns(1)->schema([
                        self::image('texts.directions.courses_image', 'Rasm'),
                        Tr::text('texts.directions.courses_title', 'Sarlavha'),
                        Tr::textarea('texts.directions.courses_text', 'Matn', false, 2),
                        self::list('texts.directions.courses_checks', 'Belgilar'),
                        Tr::text('texts.directions.courses_price', 'Narx matni'),
                    ]),
                    Forms\Components\Fieldset::make('Xizmat kartochkasi')->columns(1)->schema([
                        self::image('texts.directions.services_image', 'Rasm'),
                        Tr::text('texts.directions.services_title', 'Sarlavha'),
                        Tr::textarea('texts.directions.services_text', 'Matn', false, 2),
                        self::list('texts.directions.services_checks', 'Belgilar'),
                        Tr::text('texts.directions.services_price', 'Narx matni'),
                    ]),
                ]],
                'why' => ['title' => 'Nega biz', 'icon' => 'heroicon-o-star', 'paths' => ['texts.why'], 'schema' => fn () => [
                    Tr::text('texts.why.title', 'Sarlavha'),
                    Tr::textarea('texts.why.lead', 'Izoh', false, 2),
                    self::image('texts.why.image', 'Katta rasm'),
                    Tr::text('texts.why.place_title', 'Rasm ustidagi yozuv'),
                    Tr::text('texts.why.place_sub', 'Rasm ustidagi izoh'),
                    Tr::text('texts.why.big', 'Katta raqam (1 yil)'),
                    Tr::text('texts.why.big_title', 'Sariq blok sarlavhasi'),
                    Tr::textarea('texts.why.big_text', 'Sariq blok matni', false, 2),
                    ...collect(range(1, 5))->flatMap(fn ($i) => [
                        Tr::text("texts.why.t{$i}_title", "Blok {$i} sarlavhasi"),
                        Tr::textarea("texts.why.t{$i}_text", "Blok {$i} matni", false, 2),
                    ])->all(),
                    self::list('texts.why.t5_chips', 'Qora blokdagi teglar'),
                ]],
                'blocks' => ['title' => 'Kurslar va xizmat bloklari', 'icon' => 'heroicon-o-rectangle-group', 'paths' => ['texts.courses', 'texts.services'], 'schema' => fn () => [
                    Tr::text('texts.courses.title', 'Kurslar bloki sarlavhasi'),
                    Tr::textarea('texts.courses.lead', 'Kurslar bloki izohi', false, 2),
                    Tr::text('texts.services.title', 'Xizmat bloki sarlavhasi'),
                    Tr::textarea('texts.services.lead', 'Xizmat bloki izohi', false, 3),
                    self::image('texts.services.image', 'Xizmat bloki rasmi'),
                    Tr::text('texts.services.for', 'Kimlar uchun'),
                    Tr::text('texts.services.price_text', 'Narx (qatorda)'),
                    Tr::text('texts.services.price_big', 'Narx (rasm ustida)'),
                    Tr::text('texts.services.for_long', 'Rasm ustidagi izoh'),
                ]],
                'faq' => ['title' => 'FAQ, maqolalar va ariza bloklari', 'icon' => 'heroicon-o-chat-bubble-left-right', 'paths' => ['texts.faq', 'texts.articles', 'texts.consult'], 'schema' => fn () => [
                    Tr::text('texts.faq.title', 'FAQ sarlavhasi'),
                    Tr::textarea('texts.faq.lead', 'FAQ izohi', false, 2),
                    Tr::text('texts.articles.title', 'Maqolalar bloki sarlavhasi'),
                    Tr::textarea('texts.articles.lead', 'Maqolalar bloki izohi', false, 2),
                    Tr::text('texts.consult.title', 'Ariza bloki sarlavhasi'),
                    Tr::textarea('texts.consult.lead', 'Ariza bloki izohi', false, 2),
                    ...collect(range(1, 3))->flatMap(fn ($i) => [
                        Tr::text("texts.consult.step{$i}_title", "{$i}-qadam"),
                        Tr::text("texts.consult.step{$i}_text", "{$i}-qadam izohi"),
                    ])->all(),
                ]],
            ],
            'Kurslar sahifasi' => [
                'courses_page' => ['title' => 'Sarlavha, izoh va raqamlar', 'icon' => 'heroicon-o-academic-cap', 'paths' => ['texts.courses_page'], 'schema' => fn () => [
                    Tr::text('texts.courses_page.title', 'Sarlavha'),
                    Tr::textarea('texts.courses_page.lead', 'Izoh', false, 2),
                    Forms\Components\Fieldset::make('Raqamlar')->columns(1)->schema([
                        Tr::text('texts.courses_page.stat1', 'Raqam 1'), Tr::text('texts.courses_page.stat1_label', 'Raqam 1 izohi'),
                        Tr::text('texts.courses_page.stat2', 'Raqam 2'), Tr::text('texts.courses_page.stat2_label', 'Raqam 2 izohi'),
                        Tr::text('texts.courses_page.stat3', 'Raqam 3'), Tr::text('texts.courses_page.stat3_label', 'Raqam 3 izohi'),
                    ]),
                ]],
            ],
            'Xizmat sahifasi' => [
                'services_page' => ['title' => 'Xizmat sahifasi', 'icon' => 'heroicon-o-briefcase', 'paths' => ['texts.services_page'], 'schema' => fn () => [
                    Tr::textarea('texts.services_page.lead', 'Sarlavha ostidagi izoh', false, 2),
                ]],
            ],
            'Maqolalar sahifasi' => [
                'blog' => ['title' => 'Maqolalar sahifasi', 'icon' => 'heroicon-o-newspaper', 'paths' => ['texts.blog'], 'schema' => fn () => [
                    Tr::textarea('texts.blog.lead', 'Sarlavha ostidagi izoh', false, 2),
                ]],
            ],
            'Aloqa sahifasi' => [
                'contact' => ['title' => 'Aloqa sahifasi', 'icon' => 'heroicon-o-phone', 'paths' => ['texts.contact'], 'schema' => fn () => [
                    Tr::textarea('texts.contact.lead', 'Sarlavha ostidagi izoh', false, 2),
                ]],
            ],
        ];
    }
}
