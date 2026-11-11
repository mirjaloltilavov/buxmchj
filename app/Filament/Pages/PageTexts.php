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

    protected function blocks(): array
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
            'Ichki sahifalar' => [
                'pages' => ['title' => 'Sahifa sarlavhalari va izohlari', 'icon' => 'heroicon-o-document-duplicate', 'paths' => ['texts.courses_page', 'texts.services_page', 'texts.blog', 'texts.contact'], 'schema' => fn () => [
                    Tr::text('texts.courses_page.title', 'Kurslar sahifasi sarlavhasi'),
                    Tr::textarea('texts.courses_page.lead', 'Kurslar sahifasi izohi', false, 2),
                    Forms\Components\Grid::make(3)->schema([
                        Tr::text('texts.courses_page.stat1', 'Raqam 1'), Tr::text('texts.courses_page.stat1_label', 'Raqam 1 izohi'),
                        Tr::text('texts.courses_page.stat2', 'Raqam 2'), Tr::text('texts.courses_page.stat2_label', 'Raqam 2 izohi'),
                        Tr::text('texts.courses_page.stat3', 'Raqam 3'), Tr::text('texts.courses_page.stat3_label', 'Raqam 3 izohi'),
                    ]),
                    Tr::textarea('texts.services_page.lead', 'Xizmat sahifasi izohi', false, 2),
                    Tr::textarea('texts.blog.lead', 'Maqolalar sahifasi izohi', false, 2),
                    Tr::textarea('texts.contact.lead', 'Aloqa sahifasi izohi', false, 2),
                ]],
            ],
        ];
    }
}
