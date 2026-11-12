<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Page;

/**
 * Header va footer menyulari (admin → Menyular). Saqlanmagan bo'lsa — standart (dizayndagi) menyu.
 * Havola formati: "route:courses" | "page:5" | "course:slug" | "url" (+ alohida url maydoni), ixtiyoriy #anchor.
 */
class Menu
{
    /** Sayt sahifalari: route nomi => [nomi, faol bo'ladigan route'lar] */
    public const ROUTES = [
        'home' => ['Asosiy', ['home']],
        'courses' => ['Kurslar', ['courses', 'course']],
        'services' => ['Buxgalteriya xizmati', ['services']],
        'blog' => ['Maqolalar', ['blog', 'article']],
        'contact' => ['Aloqa', ['contact']],
        'privacy' => ['Maxfiylik siyosati', ['privacy']],
        'offer' => ['Ommaviy oferta', ['offer']],
    ];

    private static function item(string $uz, string $link, ?string $anchor = null): array
    {
        return ['label' => ['uz' => $uz, 'ru' => self::ru($uz)], 'link' => $link, 'url' => null, 'anchor' => $anchor, 'new_tab' => false];
    }

    private static function ru(string $uz): string
    {
        $t = app('translator')->get($uz, [], 'ru');

        return is_string($t) ? $t : $uz;
    }

    /** Standart menyular — hozirgi dizayndagidek */
    public static function defaults(): array
    {
        return [
            'menu_header' => [
                self::item('Asosiy', 'route:home'),
                self::item('Kurslar', 'route:courses'),
                self::item('Buxgalteriya xizmati', 'route:services'),
                self::item('Maqolalar', 'route:blog'),
                self::item('Aloqa', 'route:contact'),
            ],
            'menu_footer' => [
                ['title' => ['uz' => 'Kurslar', 'ru' => self::ru('Kurslar')], 'auto' => 'courses', 'links' => [self::item('Kurs narxlari', 'route:courses', 'formats')]],
                ['title' => ['uz' => 'Buxgalteriya xizmati', 'ru' => self::ru('Buxgalteriya xizmati')], 'auto' => null, 'links' => [
                    self::item('YaTT uchun', 'route:services', 'plans'),
                    self::item('MChJ uchun', 'route:services', 'plans'),
                    self::item('Hisobni tiklash', 'route:services'),
                    self::item('Xizmat narxlari', 'route:services', 'plans'),
                ]],
                ['title' => ['uz' => 'Kompaniya', 'ru' => self::ru('Kompaniya')], 'auto' => null, 'links' => [
                    self::item('Ustozlar', 'route:courses', 'teachers'),
                    self::item('Maqolalar', 'route:blog'),
                    self::item('Ko‘p so‘raladigan savollar', 'route:home', 'faq'),
                    self::item('Aloqa', 'route:contact'),
                ]],
                ['title' => ['uz' => 'Ijtimoiy tarmoqlar', 'ru' => self::ru('Ijtimoiy tarmoqlar')], 'auto' => 'socials', 'links' => []],
            ],
            'menu_bottom' => [
                self::item('Maxfiylik siyosati', 'route:privacy'),
                self::item('Ommaviy oferta', 'route:offer'),
            ],
        ];
    }

    public static function get(string $key): array
    {
        $v = setting($key);

        return is_array($v) && $v !== [] ? array_values($v) : (self::defaults()[$key] ?? []);
    }

    /** Admin'dagi "Qayerga olib boradi" ro'yxati */
    public static function linkOptions(): array
    {
        $opts = ['Sayt sahifalari' => collect(self::ROUTES)->mapWithKeys(fn ($r, $name) => ['route:'.$name => $r[0]])->all()];
        $pages = Page::custom()->orderBy('sort')->get(['id', 'title', 'slug']);
        if ($pages->isNotEmpty()) {
            $opts['Qo‘shimcha sahifalar'] = $pages->mapWithKeys(fn ($p) => ['page:'.$p->id => $p->tr('title', 'uz').'  (/'.$p->slug.')'])->all();
        }
        $courses = Course::orderBy('sort')->get(['slug', 'title']);
        if ($courses->isNotEmpty()) {
            $opts['Kurslar'] = $courses->mapWithKeys(fn ($c) => ['course:'.$c->slug => $c->tr('title', 'uz')])->all();
        }
        $opts['Boshqa'] = ['url' => 'Boshqa havola (qo‘lda yoziladi)'];

        return $opts;
    }

    /**
     * Saytda chizish uchun: [['href','label','active','new_tab'], ...]. Topilmagan (o'chirilgan) sahifa tushib qoladi.
     */
    public static function resolve(array $items): array
    {
        $current = preg_replace('/^ru\./', '', request()->route()?->getName() ?? '');
        $currentPage = $current === 'page' ? (string) request()->route('page') : null;
        $out = [];
        foreach ($items as $it) {
            $link = (string) ($it['link'] ?? '');
            $label = tr($it['label'] ?? null);
            $href = null;
            $active = false;
            if (str_starts_with($link, 'route:')) {
                $name = substr($link, 6);
                if (! isset(self::ROUTES[$name])) {
                    continue;
                }
                $href = lroute($name);
                $active = in_array($current, self::ROUTES[$name][1], true) && blank($it['anchor'] ?? null);
                $label = $label ?: __(self::ROUTES[$name][0]);
            } elseif (str_starts_with($link, 'page:')) {
                $page = self::pages()[(int) substr($link, 5)] ?? null;
                if (! $page) {
                    continue;
                }
                $href = $page->url();
                $active = $currentPage === $page->slug;
                $label = $label ?: $page->tr('title');
            } elseif (str_starts_with($link, 'course:')) {
                $slug = substr($link, 7);
                $href = lroute('course', ['course' => $slug]);
                $active = $current === 'course' && (string) request()->route('course') === $slug;
            } elseif ($link === 'url' && filled($it['url'] ?? null)) {
                $href = $it['url'];
            }
            if (! $href || blank($label)) {
                continue;
            }
            if (filled($it['anchor'] ?? null)) {
                $href .= '#'.ltrim($it['anchor'], '#');
            }
            $out[] = ['href' => $href, 'label' => $label, 'active' => $active, 'new_tab' => (bool) ($it['new_tab'] ?? false)];
        }

        return $out;
    }

    private static function pages(): array
    {
        static $cache;

        return $cache ??= Page::custom()->where('is_active', true)->get()->keyBy('id')->all();
    }
}
