<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Graduate;
use App\Models\HeroSlide;
use App\Models\InstagramPost;
use App\Models\Service;
use App\Models\ServicePlan;
use App\Models\Setting;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Boshlang'ich kontent (eski Framer saytidan olingan matnlar).
 * Faqat bo'sh bazaga yoziladi — admin kiritgan ma'lumotlarni o'chirmaydi.
 */
class ContentSeeder extends Seeder
{
    private function t(string $uz, ?string $ru = null): array
    {
        return ['uz' => $uz, 'ru' => $ru];
    }

    /** public/img dagi standart rasmni public/uploads ga ko'chiradi (admin paneldan almashtirish mumkin bo'lishi uchun). */
    private function img(string $src, string $dir): string
    {
        $from = public_path($src);
        $name = basename($src);
        $to = public_path("uploads/{$dir}/{$name}");
        if (! is_file($to) && is_file($from)) {
            File::ensureDirectoryExists(dirname($to));
            File::copy($from, $to);
        }

        return "{$dir}/{$name}";
    }

    public function run(): void
    {
        $this->settings();
        $this->heroSlides();
        $this->courses();
        (new CourseContentSeeder)->run();
        $this->services();
        $this->articles();
        $this->teachers();
        $this->graduates();
        $this->faqs();
        $this->instagram();
        $this->branches();
    }

    private function settings(): void
    {
        $defaults = [
            'site_name' => 'Buxgalter MChJ',
            'phone' => '+998 55 500 37 37',
            'telegram_url' => 'https://t.me/buxgalter_mchj',
            'telegram_handle' => '@buxgalter_mchj',
            'instagram_url' => 'https://instagram.com/buxgalter_mchj',
            'instagram_handle' => '@buxgalter_mchj',
            'youtube_url' => null,
            'work_hours' => $this->t('Du–Sha, 9:00–18:00', 'Пн–Сб, 9:00–18:00'),
            'hero_autoplay' => 6,
            'seo_title' => $this->t('Buxgalter MChJ — buxgalteriya kurslari va xizmati', 'Buxgalter — курсы бухгалтерии и бухгалтерские услуги'),
            'seo_description' => $this->t(
                'Noldan buxgalteriya, 1C va soliq kurslari hamda tadbirkorlar uchun buxgalteriya xizmati. Toshkent, Chilonzor.',
                'Курсы бухгалтерии, 1С и налогов с нуля и бухгалтерские услуги для предпринимателей. Ташкент, Чиланзар.'
            ),
        ];
        foreach ($defaults as $key => $value) {
            if (! Setting::where('key', $key)->exists()) {
                Setting::put($key, $value);
            }
        }
    }

    private function heroSlides(): void
    {
        if (HeroSlide::exists()) {
            return;
        }
        HeroSlide::create([
            'theme' => 'courses', 'sort' => 1,
            'tab_label' => $this->t('Buxgalteriya kurslari', 'Курсы бухгалтерии'),
            'chip' => $this->t('Yangi guruhga qabul ochiq', 'Открыт набор в новую группу'),
            'title' => $this->t('Buxgalteriyani noldan o‘rganing va kasb egasi bo‘ling', 'Освойте бухгалтерию с нуля и получите профессию'),
            'lead' => $this->t('Amaliyotga asoslangan buxgalteriya, 1C va soliq kurslari. Tadbirkorlar uchun — to‘liq buxgalteriya xizmati.', 'Курсы бухгалтерии, 1С и налогов с упором на практику. Для предпринимателей — полный комплекс бухгалтерских услуг.'),
            'primary_label' => $this->t('Kursga yozilish', 'Записаться на курс'), 'primary_url' => '#consultation',
            'secondary_label' => $this->t('Kurs narxlari', 'Цены на курсы'), 'secondary_url' => '/courses#formats',
            'image' => $this->img('img/hero-portrait.png', 'hero'),
            'cards' => [
                'badge_icon' => 'monitor',
                'badge_title' => $this->t('1C dasturi bepul', 'Программа 1С бесплатно'),
                'badge_sub' => $this->t('noutbukingizga o‘rnatiladi', 'устанавливается на ваш ноутбук'),
                'list_title' => $this->t('Kurs dasturi', 'Программа курса'),
                'list_counter' => $this->t('2 / 4', '2 / 4'),
                'list_items' => [
                    ['text' => $this->t('Buxgalteriya asoslari', 'Основы бухгалтерии')],
                    ['text' => $this->t('1C dasturi', 'Программа 1С')],
                    ['text' => $this->t('Soliq va my.soliq.uz', 'Налоги и my.soliq.uz')],
                ],
                'dark_sub' => $this->t('Toshkent · onlayn', 'Ташкент · онлайн'),
                'dark_title' => $this->t('Offline va onlayn guruhlar', 'Офлайн- и онлайн-группы'),
                'dark_url' => '/courses',
            ],
        ]);
        HeroSlide::create([
            'theme' => 'services', 'sort' => 2,
            'tab_label' => $this->t('Buxgalteriya xizmati', 'Бухгалтерские услуги'),
            'chip' => $this->t('YaTT va MChJ uchun', 'Для ИП и ООО'),
            'title' => $this->t('Buxgalteriyangizni tajribali jamoaga topshiring', 'Доверьте бухгалтерию опытной команде'),
            'lead' => $this->t('Hisob yuritish, soliq hisobotlari, ish haqi va bank operatsiyalari bizning zimmamizda. Oyiga 1 500 000 so‘mdan.', 'Учёт, налоговую отчётность, зарплату и банковские операции мы берём на себя. От 1 500 000 сум в месяц.'),
            'primary_label' => $this->t('Bepul konsultatsiya', 'Бесплатная консультация'), 'primary_url' => '#consultation',
            'secondary_label' => $this->t('Xizmat narxlari', 'Цены на услуги'), 'secondary_url' => '/services#plans',
            'image' => $this->img('img/hero-portrait-2.png', 'hero'),
            'cards' => [
                'badge_icon' => 'document-text',
                'badge_title' => $this->t('Hisobotlar o‘z vaqtida', 'Отчётность вовремя'),
                'badge_sub' => $this->t('soliq organiga topshiriladi', 'сдаётся в налоговый орган'),
                'list_title' => $this->t('Oylik hisobotlar', 'Ежемесячная отчётность'),
                'list_counter' => $this->t('3 / 4', '3 / 4'),
                'list_items' => [
                    ['text' => $this->t('QQS hisoboti', 'Отчёт по НДС')],
                    ['text' => $this->t('Ish haqi va JShDS', 'Зарплата и НДФЛ')],
                    ['text' => $this->t('Statistika hisoboti', 'Статистический отчёт')],
                ],
                'dark_sub' => $this->t('YaTT va MChJ uchun', 'Для ИП и ООО'),
                'dark_title' => $this->t('Biriktirilgan bosh buxgalter', 'Закреплённый главный бухгалтер'),
                'dark_url' => '/services',
            ],
        ]);
    }

    private function courses(): void
    {
        if (Course::exists()) {
            return;
        }
        $courses = [
            [
                'slug' => 'buxgalteriya-noldan', 'sort' => 1, 'is_featured' => true, 'price' => 1512000,
                'title' => $this->t('Buxgalteriya noldan', 'Бухгалтерия с нуля'),
                'excerpt' => $this->t('Buxgalteriya hisobi, 1C, soliq va real korxonada amaliyot. Kasbni noldan o‘rganmoqchi bo‘lganlar uchun.', 'Бухучёт, 1С, налоги и практика на реальном предприятии. Для тех, кто осваивает профессию с нуля.'),
                'image' => $this->img('img/course-basic.png', 'courses'),
                'tags' => [
                    ['icon' => 'calendar', 'text' => $this->t('4 oydan 1 yilgacha', 'от 4 месяцев до 1 года'), 'accent' => true],
                    ['icon' => 'location', 'text' => $this->t('Offline · onlayn', 'Офлайн · онлайн'), 'accent' => false],
                ],
                'program' => [
                    ['title' => $this->t('Buxgalteriya asoslari', 'Основы бухгалтерии'), 'text' => $this->t('Hisob tushunchalari, schyotlar rejasi, provodkalar va birlamchi hujjatlar.', 'Понятия учёта, план счетов, проводки и первичные документы.'), 'topics' => []],
                    ['title' => $this->t('1C dasturi', 'Программа 1С'), 'text' => $this->t('Kirim-chiqim, kassa, bank, ish haqi va hisobotlarni 1C’da yuritish.', 'Поступления, реализация, касса, банк, зарплата и отчёты в 1С.'), 'topics' => []],
                    ['title' => $this->t('Soliq va my.soliq.uz', 'Налоги и my.soliq.uz'), 'text' => $this->t('Soliq turlari, hisobotlar va my.soliq.uz orqali topshirish.', 'Виды налогов, отчётность и сдача через my.soliq.uz.'), 'topics' => []],
                    ['title' => $this->t('Real korxonada amaliyot', 'Практика на реальном предприятии'), 'text' => $this->t('Korxonaning bir yillik buxgalteriyasini tiklaysiz.', 'Восстановите годовой учёт компании.'), 'topics' => []],
                ],
            ],
            [
                'slug' => 'mukammal-1c-kursi', 'sort' => 2, 'price' => 1200000,
                'title' => $this->t('Mukammal 1C kursi', 'Продвинутый курс 1С'),
                'excerpt' => $this->t('Buxgalteriyadan boshlang‘ich bilimi borlar uchun 1C dasturi bo‘yicha chuqur kurs.', 'Углублённый курс по 1С для тех, у кого есть базовые знания бухгалтерии.'),
                'price_note' => $this->t('Narxi kurs sahifasida', 'Цена на странице курса'),
                'image' => $this->img('img/course-1c.jpg', 'courses'),
                'tags' => [
                    ['icon' => 'calendar', 'text' => $this->t('7 hafta', '7 недель'), 'accent' => false],
                    ['icon' => 'clock', 'text' => $this->t('Du, Chor, Ju · 10:00–12:30', 'Пн, Ср, Пт · 10:00–12:30'), 'accent' => false],
                ],
            ],
            [
                'slug' => 'onlayn-kurs', 'sort' => 3, 'price' => 720000,
                'title' => $this->t('Onlayn kurs', 'Онлайн-курс'),
                'excerpt' => $this->t('Platformada video darslar, test va topshiriqlar. Instruktor savollaringizga javob beradi.', 'Видеоуроки, тесты и задания на платформе. Инструктор отвечает на ваши вопросы.'),
                'price_note' => $this->t('Narxi kurs sahifasida', 'Цена на странице курса'),
                'image' => $this->img('img/course-online.jpg', 'courses'),
                'tags' => [
                    ['icon' => 'clock', 'text' => $this->t('Istalgan vaqtda', 'В любое время'), 'accent' => false],
                    ['icon' => 'monitor', 'text' => $this->t('Platformaga kirish', 'Доступ к платформе'), 'accent' => false],
                ],
            ],
        ];
        foreach ($courses as $c) {
            Course::create($c + ['is_active' => true]);
        }
    }

    private function services(): void
    {
        if (! Service::exists()) {
            $items = [
                ['calculator', 'Buxgalteriya hisobi', 'Birlamchi hujjatlar, provodkalar va 1C bazasini yuritish.', 'Бухгалтерский учёт', 'Первичные документы, проводки и ведение базы 1С.'],
                ['receipt-text', 'Soliq hisobotlari', 'Soliq va statistika hisobotlarini o‘z vaqtida topshirish.', 'Налоговая отчётность', 'Своевременная сдача налоговой и статистической отчётности.'],
                ['people', 'Ish haqi va kadrlar', 'Oylik hisoblash, ish haqi soliqlari va kadr hujjatlari.', 'Зарплата и кадры', 'Расчёт зарплаты, зарплатные налоги и кадровые документы.'],
                ['card', 'Bank-klient va to‘lovlar', 'To‘lov topshiriqnomalari va bank operatsiyalarini yuritish.', 'Банк-клиент и платежи', 'Платёжные поручения и банковские операции.'],
                ['refresh', 'Hisobni tiklash', 'O‘tgan davrlar hisobini tartibga keltirish va qayta topshirish.', 'Восстановление учёта', 'Приведение в порядок учёта прошлых периодов и повторная сдача.'],
                ['message-question', 'Konsultatsiya', 'Soliq va buxgalteriya bo‘yicha savollaringizga javob.', 'Консультации', 'Ответы на вопросы по налогам и бухгалтерии.'],
            ];
            foreach ($items as $i => [$icon, $uz, $uzd, $ru, $rud]) {
                Service::create(['icon' => $icon, 'title' => $this->t($uz, $ru), 'description' => $this->t($uzd, $rud), 'sort' => $i + 1]);
            }
        }

        // Figma bento: 7-xizmat (faqat Xizmat sahifasida)
        if (! Service::where('icon', 'user')->exists()) {
            Service::where('sort', '>=', 4)->increment('sort');
            Service::create(['icon' => 'user', 'title' => $this->t('Biriktirilgan bosh buxgalter', 'Закреплённый главный бухгалтер'), 'description' => $this->t('Korxonangiz hisobini bitta mas’ul mutaxassis yuritadi va shartnoma asosida javob beradi.', 'Учёт вашей компании ведёт один ответственный специалист, который отвечает по договору.'), 'sort' => 4, 'on_home' => false]);
        }

        if (! ServicePlan::exists()) {
            $f = fn (array $list) => array_map(fn ($p) => ['text' => $this->t($p[0], $p[1])], $list);
            $base = [['Buxgalteriya hisobi va 1C bazasi', 'Бухучёт и база 1С'], ['Soliq va statistika hisobotlari', 'Налоговая и статистическая отчётность'], ['Bank-klient va to‘lovlar', 'Банк-клиент и платежи']];
            $plans = [
                ['YaTT', 'ЯТТ / ИП', 'Yakka tartibdagi tadbirkorlar uchun', 'Для индивидуальных предпринимателей', 1500000, false, [...$base, ['Konsultatsiya', 'Консультации']]],
                ['MChJ', 'ООО', 'QQS to‘lovchisi bo‘lmagan korxonalar', 'Компании — не плательщики НДС', 3000000, false, [...$base, ['Ish haqi va kadrlar hujjatlari', 'Зарплата и кадровые документы'], ['Konsultatsiya', 'Консультации']]],
                ['MChJ + QQS', 'ООО + НДС', 'QQS to‘lovchisi bo‘lgan korxonalar', 'Компании — плательщики НДС', 5000000, true, [...$base, ['QQS hisobi va hisobotlari', 'Учёт и отчётность по НДС'], ['Ish haqi va kadrlar hujjatlari', 'Зарплата и кадровые документы'], ['Biriktirilgan bosh buxgalter', 'Закреплённый главный бухгалтер']]],
                ['Import-eksport', 'Импорт-экспорт', 'Tashqi savdo bilan shug‘ullanuvchi korxonalar', 'Компании, ведущие внешнюю торговлю', 8000000, false, [['MChJ + QQS tarifidagi barcha xizmatlar', 'Все услуги тарифа ООО + НДС'], ['Import-eksport operatsiyalari hisobi', 'Учёт импортно-экспортных операций'], ['Bojxona hujjatlari bo‘yicha hisob', 'Учёт по таможенным документам']]],
            ];
            foreach ($plans as $i => [$n, $nr, $s, $sr, $price, $hl, $features]) {
                ServicePlan::create(['name' => $this->t($n, $nr), 'subtitle' => $this->t($s, $sr), 'price' => $price, 'is_highlighted' => $hl, 'features' => $f($features), 'sort' => $i + 1]);
            }
        }
    }

    private function articles(): void
    {
        if (Article::exists()) {
            // Avval o'rnatilgan saytda: yangi maydonlar (teglar, muallif) bo'sh bo'lsa to'ldiriladi
            foreach (json_decode(file_get_contents(database_path('seeders/data/articles.json')), true) as $a) {
                Article::where('slug', $a['slug'])->whereNull('tags')->update([
                    'tags' => json_encode($a['tags'] ?? null, JSON_UNESCAPED_UNICODE),
                    'author' => json_encode($a['author'] ?? null, JSON_UNESCAPED_UNICODE),
                ]);
                // Ruscha matn bo'sh bo'lsa — tarjima qo'shiladi (o'zbekcha matnga tegilmaydi)
                $article = Article::where('slug', $a['slug'])->first();
                if ($article && empty($article->body['ru']) && ! empty($a['body']['ru'])) {
                    $article->update(['body' => array_merge($article->body ?? [], ['ru' => $a['body']['ru']])]);
                }
                foreach (['title', 'excerpt'] as $f) {
                    if ($article && empty($article->{$f}['ru']) && ! empty($a[$f]['ru'])) {
                        $article->update([$f => array_merge($article->{$f} ?? [], ['ru' => $a[$f]['ru']])]);
                    }
                }
            }

            return;
        }
        $cats = [
            'soliq' => ['Soliq', 'Налоги'], '1c' => ['1C', '1С'], 'buxgalteriya' => ['Buxgalteriya', 'Бухгалтерия'],
            'kasb' => ['Kasb', 'Профессия'], 'biznes' => ['Biznes', 'Бизнес'],
        ];
        $ids = [];
        $sort = 0;
        foreach ($cats as $slug => [$uz, $ru]) {
            $ids[$slug] = ArticleCategory::firstOrCreate(['slug' => $slug], ['name' => $this->t($uz, $ru), 'sort' => ++$sort])->id;
        }
        $data = json_decode(file_get_contents(database_path('seeders/data/articles.json')), true);
        foreach ($data as $a) {
            Article::create([
                'slug' => $a['slug'],
                'article_category_id' => $ids[$a['category']] ?? null,
                'title' => $a['title'], 'excerpt' => $a['excerpt'], 'body' => $a['body'],
                'tags' => $a['tags'] ?? null, 'author' => $a['author'] ?? null,
                'cover' => $this->img('img/'.$a['cover'], 'articles'),
                'read_minutes' => $a['read_minutes'],
                'published_at' => $a['published_at'],
                'is_featured' => $a['is_featured'],
                'is_active' => true,
            ]);
        }
    }

    private function teachers(): void
    {
        if (Teacher::exists()) {
            return;
        }
        $list = [
            ['Oxunjon Asrorov', 'Охунжон Асроров', 'Asoschi, bosh ustoz', 'Основатель, главный преподаватель', 'img/teachers/oxunjon.jpg'],
            ['Hulkar Madalxonova', 'Хулкар Мадалхонова', 'Buxgalteriya hisobi', 'Бухгалтерский учёт', 'img/teachers/hulkar.jpg'],
            ['Abdusaid Xolmamatov', 'Абдусаид Холмаматов', '1C va soliq', '1С и налоги', 'img/teachers/abdusaid.jpg'],
        ];
        foreach ($list as $i => [$n, $nr, $r, $rr, $photo]) {
            Teacher::create(['name' => $this->t($n, $nr), 'role' => $this->t($r, $rr), 'photo' => $this->img($photo, 'teachers'), 'sort' => $i + 1]);
        }
    }

    private function graduates(): void
    {
        if (Graduate::exists()) {
            return;
        }
        $list = [
            ['Dilnoza Karimova', 'Дилноза Каримова', 'BH-28 guruhi · buxgalter, «Orient Trade» MChJ', 'Группа BH-28 · бухгалтер, ООО «Orient Trade»', 'g1'],
            ['Jasur Toshmatov', 'Жасур Тошматов', 'BH-32 guruhi · bosh buxgalter yordamchisi, «Nur Logistic»', 'Группа BH-32 · помощник главбуха, «Nur Logistic»', 'g2'],
            ['Malika Yusupova', 'Малика Юсупова', 'BH-36 guruhi · YaTT buxgalteri, o‘z mijozlari bilan', 'Группа BH-36 · бухгалтер ИП, ведёт своих клиентов', 'g3'],
            ['Sardor Rahimov', 'Сардор Рахимов', 'BH-40 guruhi · 1C mutaxassisi, «Smart Build» MChJ', 'Группа BH-40 · специалист 1С, ООО «Smart Build»', 'g4'],
            ['Nigora Aliyeva', 'Нигора Алиева', 'BH-41 guruhi · buxgalter, «Green Food» MChJ', 'Группа BH-41 · бухгалтер, ООО «Green Food»', 'g5'],
            ['Bekzod Ergashev', 'Бекзод Эргашев', 'BH-36 guruhi · soliq bo‘yicha mutaxassis', 'Группа BH-36 · специалист по налогам', 'g6'],
        ];
        foreach ($list as $i => [$n, $nr, $s, $sr, $photo]) {
            Graduate::create(['name' => $this->t($n, $nr), 'subtitle' => $this->t($s, $sr), 'photo' => $this->img("img/graduates/{$photo}.jpg", 'graduates'), 'sort' => $i + 1]);
        }
    }

    private function faqs(): void
    {
        if (Faq::exists()) {
            return;
        }
        $data = json_decode(file_get_contents(database_path('seeders/data/faqs.json')), true);
        foreach ($data as $i => $f) {
            Faq::create(['question' => $f['question'], 'answer' => $f['answer'], 'pages' => $f['pages'], 'sort' => $i + 1]);
        }
    }

    private function instagram(): void
    {
        if (InstagramPost::exists()) {
            return;
        }
        foreach (range(1, 7) as $i) {
            InstagramPost::create(['image' => $this->img("img/insta/post-{$i}.jpg", 'instagram'), 'sort' => $i]);
        }
    }

    private function branches(): void
    {
        if (Branch::exists()) {
            return;
        }
        Branch::create([
            'name' => $this->t('Toshkent filiali', 'Ташкентский филиал'),
            'address' => $this->t('Toshkent sh., Chilonzor tumani, 9-kvartal, 14/1', 'г. Ташкент, Чиланзарский район, 9-квартал, 14/1'),
            'hours' => $this->t('Dushanba–Shanba, 9:00–18:00', 'Понедельник–суббота, 9:00–18:00'),
            'phone' => '+998 55 500 37 37',
            'map_url' => 'https://yandex.uz/maps/?text=Toshkent%2C%20Chilonzor%2C%209-kvartal%2C%2014%2F1',
        ]);
    }
}
