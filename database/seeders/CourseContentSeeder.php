<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Kurs sahifalarining to'liq kontenti (Figma + eski sayt matnlari).
 * Faqat meta bo'sh bo'lgan kurslarni to'ldiradi — admin tahrirlarini o'chirmaydi.
 */
class CourseContentSeeder extends Seeder
{
    private function t(string $uz, ?string $ru = null): array
    {
        return ['uz' => $uz, 'ru' => $ru];
    }

    private function list(array $items): array
    {
        return array_map(fn ($i) => ['text' => $this->t($i[0], $i[1] ?? null)], $items);
    }

    private function img(string $src, string $dir): string
    {
        $from = public_path($src);
        $to = public_path("uploads/{$dir}/".basename($src));
        if (! is_file($to) && is_file($from)) {
            File::ensureDirectoryExists(dirname($to));
            File::copy($from, $to);
        }

        return "{$dir}/".basename($src);
    }

    private function modules(array $rows): array
    {
        $out = [];
        foreach ($rows as [$icon, $img, $uz, $ru, $tuz, $tru]) {
            $out[] = ['icon' => $icon, 'image' => $this->img("img/{$img}", 'courses'), 'title' => $this->t($uz, $ru), 'text' => $this->t($tuz, $tru), 'topics' => []];
        }

        return $out;
    }

    private function plan(string $name, string $sub, string $subRu, int $once, int $split, bool $hl, array $features): array
    {
        return [
            'name' => $this->t($name, $name === 'Standart' ? 'Стандарт' : ($name === 'Premium' ? 'Премиум' : $name)),
            'subtitle' => $this->t($sub, $subRu),
            'price_once' => $once,
            'price_split' => $split,
            'highlight' => $hl,
            'features' => $this->list($features),
        ];
    }

    public function run(): void
    {
        $data = [
            'buxgalteriya-noldan' => [
                'meta' => [
                    'badge' => $this->t('Offline · onlayn · 4 oydan 1 yilgacha', 'Офлайн · онлайн · от 4 месяцев до 1 года'),
                    'stats' => [
                        ['icon' => 'book', 'value' => $this->t('26–40', '26–40'), 'label' => $this->t('ta dars', 'занятий')],
                        ['icon' => 'monitor', 'value' => $this->t('1C', '1С'), 'label' => $this->t('bepul o‘rnatiladi', 'установим бесплатно')],
                        ['icon' => 'award', 'value' => $this->t('Diplom', 'Диплом'), 'label' => $this->t('va sertifikat', 'и сертификат')],
                    ],
                    'price_caption' => $this->t('bir martada to‘lansa', 'при оплате сразу'),
                    'audience_lead' => $this->t('Oldindan bilim talab qilinmaydi — hammasini asosidan boshlaymiz.', 'Предварительные знания не нужны — начинаем с основ.'),
                    'program_lead' => $this->t('Standart — 26 ta dars, Premium va VIP — 40 ta dars. Har bir mavzu real hujjatlar bilan mustahkamlanadi.', 'Стандарт — 26 занятий, Премиум и VIP — 40 занятий. Каждая тема закрепляется на реальных документах.'),
                    'outcomes_lead' => $this->t('Har bir modul amaliy topshiriq bilan yakunlanadi — bilim darhol ishda qo‘llaniladi.', 'Каждый модуль завершается практическим заданием — знания сразу применяются в работе.'),
                    'outcomes_image' => $this->img('img/graduates/g3.jpg', 'courses'),
                    'plans_lead' => $this->t('Kursni bir martada to‘lasangiz — 20% chegirma.', 'При оплате курса сразу — скидка 20%.'),
                    'plans_note' => $this->t('* Litsenziyali 1C kurs oxirigacha ishlatiladi. Litsenziyasiz 1C cheklovsiz ishlaydi.', '* Лицензионная 1С используется до конца курса. Нелицензионная работает без ограничений.'),
                ],
                'audience' => [
                    ['icon' => 'user', 'title' => $this->t('Noldan boshlovchilar', 'Начинающие с нуля'), 'text' => $this->t('Buxgalteriya bo‘yicha bilimingiz bo‘lmasa ham, hammasini asosidan tushuntiramiz.', 'Даже если вы не знакомы с бухгалтерией, объясним всё с основ.')],
                    ['icon' => 'refresh', 'title' => $this->t('Kasbini o‘zgartirmoqchilar', 'Меняющие профессию'), 'text' => $this->t('Barqaror va talab yuqori bo‘lgan yangi kasbni egallash uchun.', 'Чтобы освоить стабильную и востребованную профессию.')],
                    ['icon' => 'briefcase', 'title' => $this->t('Tadbirkorlar', 'Предприниматели'), 'text' => $this->t('O‘z biznesingiz hisobini tushunish va nazorat qilish uchun.', 'Чтобы понимать и контролировать учёт своего бизнеса.')],
                    ['icon' => 'book', 'title' => $this->t('Talabalar', 'Студенты'), 'text' => $this->t('O‘qish bilan birga amaliy ko‘nikma va hujjat olish uchun.', 'Чтобы параллельно с учёбой получить практику и документ.')],
                ],
                'program' => $this->modules([
                    ['calculator', 'course-basic.png', 'Buxgalteriya hisobi asoslari', 'Основы бухгалтерского учёта', 'Asosiy tushunchalar, birlamchi hujjatlar va provodkalar.', 'Основные понятия, первичные документы и проводки.'],
                    ['monitor', 'course-1c.jpg', '1C dasturida ishlash', 'Работа в 1С', 'Hujjatlarni kiritish va hisobotlarni shakllantirish. Dastur noutbukingizga bepul o‘rnatiladi.', 'Ввод документов и формирование отчётов. Программу бесплатно установим на ваш ноутбук.'],
                    ['receipt-text', 'why.jpg', 'Soliq hisobotlari', 'Налоговая отчётность', 'Hisobotlarni tayyorlash va my.soliq.uz orqali topshirish.', 'Подготовка отчётов и сдача через my.soliq.uz.'],
                    ['card', 'services.jpg', 'Bank va to‘lovlar', 'Банк и платежи', 'Internet-banking orqali to‘lovlar va bank operatsiyalari.', 'Платежи и банковские операции через интернет-банкинг.'],
                    ['briefcase', 'dir-courses.jpg', 'Real korxonada amaliyot', 'Практика на реальном предприятии', 'Korxonaning bir yillik buxgalteriyasini tiklaysiz (Premium va VIP).', 'Восстановите годовой учёт компании (Премиум и VIP).'],
                    ['award', 'graduates/g3.jpg', 'Imtihon va hujjat', 'Экзамен и документ', 'Kurs yakunida imtihon. Standart’da sertifikat, Premium’da diplom va sertifikat.', 'Экзамен в конце курса. В Стандарте — сертификат, в Премиуме — диплом и сертификат.'],
                ]),
                'outcomes' => $this->list([
                    ['Birlamchi hujjatlar va provodkalarni mustaqil yuritasiz', 'Самостоятельно ведёте первичные документы и проводки'],
                    ['1C dasturida hujjat kiritib, hisobot shakllantirasiz', 'Вводите документы в 1С и формируете отчёты'],
                    ['Soliq hisobotlarini my.soliq.uz orqali topshirasiz', 'Сдаёте налоговую отчётность через my.soliq.uz'],
                    ['Internet-banking orqali to‘lovlarni amalga oshirasiz', 'Проводите платежи через интернет-банкинг'],
                    ['Real korxona buxgalteriyasini tiklash tajribasiga ega bo‘lasiz', 'Получаете опыт восстановления учёта реальной компании'],
                    ['Sertifikat yoki diplom bilan ish izlashni boshlaysiz', 'Начинаете поиск работы с сертификатом или дипломом'],
                ]),
                'plans' => [
                    $this->plan('Standart', 'Online platformaga 4 oyga kirish', 'Доступ к онлайн-платформе на 4 месяца', 1512000, 1890000, false, [
                        ['Buxgalteriya + 1C (litsenziyasiz)', 'Бухгалтерия + 1С (без лицензии)'],
                        ['26 ta dars, test, topshiriq va imtihon', '26 занятий, тесты, задания и экзамен'],
                        ['Instruktor biriktiriladi', 'Закрепляется инструктор'],
                        ['Sertifikat', 'Сертификат'],
                    ]),
                    $this->plan('Premium', 'Online platformaga 1 yilga kirish', 'Доступ к онлайн-платформе на 1 год', 2160000, 2700000, true, [
                        ['Buxgalteriya + 1C (litsenziyali* va litsenziyasiz) + Soliq + Amaliyot', 'Бухгалтерия + 1С (лицензионная* и без лицензии) + Налоги + Практика'],
                        ['40 ta dars, test, topshiriq va imtihon', '40 занятий, тесты, задания и экзамен'],
                        ['Diplom va sertifikat', 'Диплом и сертификат'],
                        ['Real firma bilan amaliyot — 1 yillik buxgalteriyani tiklash', 'Практика с реальной фирмой — восстановление годового учёта'],
                        ['Real korxona kaliti bilan ishlash', 'Работа с ключом реального предприятия'],
                        ['Topshiriqlar tekshiriladi, ustoz nazorati', 'Проверка заданий, контроль преподавателя'],
                        ['Markazga istalgan vaqtda (12/6) kelib shug‘ullanish va maslahat', 'Занятия и консультации в центре в любое время (12/6)'],
                    ]),
                    $this->plan('VIP', 'Online platformaga 3 yilga kirish', 'Доступ к онлайн-платформе на 3 года', 3600000, 4500000, false, [
                        ['Premium tarifidagi barcha imkoniyatlar', 'Все возможности тарифа Премиум'],
                        ['Markaz rahbaridan o‘z korxonangiz bo‘yicha maslahat — oyiga 1 marta, jami 3 marta, 1 soatdan', 'Консультации руководителя центра по вашей компании — раз в месяц, всего 3 раза по 1 часу'],
                        ['Markaz rahbarining haftalik shaxsiy nazorati', 'Еженедельный личный контроль руководителя центра'],
                    ]),
                ],
            ],
            'mukammal-1c-kursi' => [
                'meta' => [
                    'badge' => $this->t('7 hafta · Du, Chor, Ju · 10:00–12:30', '7 недель · Пн, Ср, Пт · 10:00–12:30'),
                    'stats' => [
                        ['icon' => 'book', 'value' => $this->t('6', '6'), 'label' => $this->t('ta modul', 'модулей')],
                        ['icon' => 'monitor', 'value' => $this->t('1C', '1С'), 'label' => $this->t('real bazada', 'на реальной базе')],
                        ['icon' => 'award', 'value' => $this->t('Sertifikat', 'Сертификат'), 'label' => $this->t('kurs yakunida', 'по окончании')],
                    ],
                    'price_caption' => $this->t('bir martada to‘lansa', 'при оплате сразу'),
                    'audience_lead' => $this->t('Buxgalteriya asoslarini biladiganlar uchun mo‘ljallangan.', 'Для тех, кто знает основы бухгалтерии.'),
                    'program_lead' => $this->t('Kurs 6 ta moduldan iborat. Har bir mavzu 1C bazasida real misollar bilan mustahkamlanadi.', 'Курс состоит из 6 модулей. Каждая тема закрепляется на реальных примерах в базе 1С.'),
                    'outcomes_lead' => $this->t('Har bir modul amaliy topshiriq bilan yakunlanadi — bilim darhol ishda qo‘llaniladi.', 'Каждый модуль завершается практическим заданием — знания сразу применяются в работе.'),
                    'outcomes_image' => $this->img('img/course-1c.jpg', 'courses'),
                    'plans_on_request' => true,
                    'plans_note' => $this->t('* Litsenziyali 1C kurs oxirigacha ishlatiladi. Litsenziyasiz 1C cheklovsiz ishlaydi.', '* Лицензионная 1С используется до конца курса. Нелицензионная работает без ограничений.'),
                ],
                'audience' => [
                    ['icon' => 'briefcase', 'title' => $this->t('Buxgalterlar', 'Бухгалтеры'), 'text' => $this->t('Ishda 1C dan to‘liq foydalanishni o‘rganmoqchi bo‘lganlar uchun.', 'Для тех, кто хочет полноценно использовать 1С в работе.')],
                    ['icon' => 'award', 'title' => $this->t('Bitiruvchilar', 'Выпускники'), 'text' => $this->t('Asosiy kursdan keyin bilimni chuqurlashtirish uchun.', 'Чтобы углубить знания после базового курса.')],
                    ['icon' => 'building', 'title' => $this->t('Tadbirkorlar', 'Предприниматели'), 'text' => $this->t('Hisobni 1C da o‘zi nazorat qilmoqchi bo‘lganlar uchun.', 'Для тех, кто хочет сам контролировать учёт в 1С.')],
                    ['icon' => 'calculator', 'title' => $this->t('Hisobchilar', 'Счетоводы'), 'text' => $this->t('Qo‘lda yuritilgan hisobni dasturga o‘tkazish uchun.', 'Чтобы перенести ручной учёт в программу.')],
                ],
                'program' => $this->modules([
                    ['monitor', 'course-1c.jpg', '1C ga kirish va sozlash', 'Введение и настройка 1С', 'Dastur interfeysi, tashkilot ma’lumotlari va hisob siyosatini sozlash.', 'Интерфейс программы, данные организации и учётная политика.'],
                    ['building', 'dir-services.jpg', 'Ombor va savdo', 'Склад и торговля', 'Tovarlar kirim-chiqimi, sotuv va xarid hujjatlari.', 'Поступление и выбытие товаров, документы продаж и закупок.'],
                    ['card', 'services.jpg', 'Kassa va bank', 'Касса и банк', 'Kassa orderlari, bank ko‘chirmalari va to‘lov topshiriqnomalari.', 'Кассовые ордера, банковские выписки и платёжные поручения.'],
                    ['people', 'why.jpg', 'Ish haqi va kadrlar', 'Зарплата и кадры', 'Xodimlarni qabul qilish, ish haqi hisoblash va ushlab qolishlar.', 'Приём сотрудников, расчёт зарплаты и удержания.'],
                    ['receipt-text', 'dir-courses.jpg', 'Soliqlar va hisobotlar', 'Налоги и отчётность', 'QQS, foyda solig‘i va moliyaviy hisobotlarni 1C da shakllantirish.', 'Формирование НДС, налога на прибыль и финансовой отчётности в 1С.'],
                    ['task-square', 'course-basic.png', 'Davrni yopish va tahlil', 'Закрытие периода и анализ', 'Oy va yil yakunini yopish, xatolarni topish va tuzatish.', 'Закрытие месяца и года, поиск и исправление ошибок.'],
                ]),
                'outcomes' => $this->list([
                    ['1C da tashkilotni noldan sozlaysiz', 'Настраиваете организацию в 1С с нуля'],
                    ['Ombor, savdo va xarid hujjatlarini yuritasiz', 'Ведёте складские, торговые и закупочные документы'],
                    ['Kassa va bank operatsiyalarini kiritasiz', 'Вводите кассовые и банковские операции'],
                    ['Ish haqini hisoblab, kadrlar hujjatlarini yuritasiz', 'Рассчитываете зарплату и ведёте кадровые документы'],
                    ['Soliq va moliyaviy hisobotlarni shakllantirasiz', 'Формируете налоговую и финансовую отчётность'],
                    ['Davrni mustaqil yopib, xatolarni tuzatasiz', 'Самостоятельно закрываете период и исправляете ошибки'],
                ]),
                'plans' => [],
            ],
            'onlayn-kurs' => [
                'meta' => [
                    'badge' => $this->t('Istalgan vaqtda · platformada', 'В любое время · на платформе'),
                    'stats' => [
                        ['icon' => 'play', 'value' => $this->t('Video', 'Видео'), 'label' => $this->t('darslar', 'уроки')],
                        ['icon' => 'task-square', 'value' => $this->t('Test', 'Тесты'), 'label' => $this->t('va topshiriqlar', 'и задания')],
                        ['icon' => 'award', 'value' => $this->t('Sertifikat', 'Сертификат'), 'label' => $this->t('kurs yakunida', 'по окончании')],
                    ],
                    'price_caption' => $this->t('bir martada to‘lansa', 'при оплате сразу'),
                    'dark_card_sub' => $this->t('O‘zingizga qulay vaqtda', 'В удобное для вас время'),
                    'dark_card_title' => $this->t('Instruktor savollarga javob beradi', 'Инструктор отвечает на вопросы'),
                    'audience_lead' => $this->t('Kompyuter va internet bo‘lsa yetarli.', 'Достаточно компьютера и интернета.'),
                    'program_lead' => $this->t('Video darslar, testlar va amaliy topshiriqlar. Har bir modul yakunida instruktor ishingizni tekshiradi.', 'Видеоуроки, тесты и практические задания. В конце каждого модуля инструктор проверяет вашу работу.'),
                    'outcomes_lead' => $this->t('Har bir modul amaliy topshiriq bilan yakunlanadi — bilim darhol ishda qo‘llaniladi.', 'Каждый модуль завершается практическим заданием — знания сразу применяются в работе.'),
                    'outcomes_image' => $this->img('img/course-online.jpg', 'courses'),
                    'plans_on_request' => true,
                    'plans_note' => $this->t('* Litsenziyali 1C kurs oxirigacha ishlatiladi. Litsenziyasiz 1C cheklovsiz ishlaydi.', '* Лицензионная 1С используется до конца курса. Нелицензионная работает без ограничений.'),
                ],
                'audience' => [
                    ['icon' => 'location', 'title' => $this->t('Boshqa shahardagilar', 'Из других городов'), 'text' => $this->t('Toshkentga kela olmaydiganlar uchun.', 'Для тех, кто не может приехать в Ташкент.')],
                    ['icon' => 'clock', 'title' => $this->t('Band insonlar', 'Занятые люди'), 'text' => $this->t('Ish bilan birga qulay vaqtda o‘qish uchun.', 'Чтобы учиться в удобное время параллельно с работой.')],
                    ['icon' => 'user', 'title' => $this->t('Noldan boshlovchilar', 'Начинающие с нуля'), 'text' => $this->t('Buxgalteriyani bosqichma-bosqich o‘rganish uchun.', 'Чтобы изучать бухгалтерию шаг за шагом.')],
                    ['icon' => 'book', 'title' => $this->t('Talabalar', 'Студенты'), 'text' => $this->t('O‘qish bilan birga amaliy ko‘nikma olish uchun.', 'Чтобы получить практические навыки параллельно с учёбой.')],
                ],
                'program' => $this->modules([
                    ['monitor', 'course-online.jpg', 'Platforma va o‘qish tartibi', 'Платформа и порядок обучения', 'Shaxsiy kabinet, darslar ketma-ketligi va instruktor bilan aloqa.', 'Личный кабинет, порядок уроков и связь с инструктором.'],
                    ['calculator', 'course-basic.png', 'Buxgalteriya hisobi asoslari', 'Основы бухгалтерского учёта', 'Asosiy tushunchalar, birlamchi hujjatlar va provodkalar.', 'Основные понятия, первичные документы и проводки.'],
                    ['monitor', 'course-1c.jpg', '1C dasturida ishlash', 'Работа в 1С', 'Video darslar asosida hujjat kiritish va hisobot shakllantirish.', 'Ввод документов и формирование отчётов по видеоурокам.'],
                    ['receipt-text', 'why.jpg', 'Soliq hisobotlari', 'Налоговая отчётность', 'Hisobotlarni tayyorlash va my.soliq.uz orqali topshirish.', 'Подготовка отчётов и сдача через my.soliq.uz.'],
                    ['card', 'services.jpg', 'Bank va to‘lovlar', 'Банк и платежи', 'Internet-banking orqali to‘lovlar va bank operatsiyalari.', 'Платежи и банковские операции через интернет-банкинг.'],
                    ['award', 'graduates/g3.jpg', 'Yakuniy test va sertifikat', 'Итоговый тест и сертификат', 'Barcha modullar bo‘yicha test topshirib, sertifikat olasiz.', 'Сдаёте тест по всем модулям и получаете сертификат.'],
                ]),
                'outcomes' => $this->list([
                    ['Buxgalteriya hisobining asosiy tushunchalarini bilasiz', 'Знаете основные понятия бухгалтерского учёта'],
                    ['Birlamchi hujjatlar va provodkalarni yuritasiz', 'Ведёте первичные документы и проводки'],
                    ['1C dasturida hujjat kiritib, hisobot shakllantirasiz', 'Вводите документы в 1С и формируете отчёты'],
                    ['Soliq hisobotlarini my.soliq.uz orqali topshirasiz', 'Сдаёте налоговую отчётность через my.soliq.uz'],
                    ['Internet-banking orqali to‘lovlarni amalga oshirasiz', 'Проводите платежи через интернет-банкинг'],
                    ['Yakuniy testdan o‘tib, sertifikat olasiz', 'Проходите итоговый тест и получаете сертификат'],
                ]),
                'plans' => [],
            ],
        ];

        foreach ($data as $slug => $fields) {
            $course = Course::where('slug', $slug)->first();
            if (! $course || ! empty($course->meta)) {
                continue;
            }
            $course->update($fields);
        }
    }
}
