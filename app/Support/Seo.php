<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Branch;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;

/**
 * SEO yordamchilari: JSON-LD (schema.org) va sitemap ma'lumotlari.
 * Faqat saytda/adminda haqiqatan bor ma'lumot ishlatiladi; yo'q maydon chiqarilmaydi.
 */
class Seo
{
    /** <script type="application/ld+json"> — matn ichidagi "</script>" sahifani buzmasligi uchun xavfsiz kodlanadi */
    public static function jsonLd(array $data): string
    {
        $json = json_encode(self::clean($data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);

        return '<script type="application/ld+json">'.$json.'</script>';
    }

    /** Bo'sh qiymatlarni olib tashlaydi (schema'da bo'sh maydon bo'lmasin) */
    private static function clean(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = self::clean($v);
            }
            if ($v === null || $v === '' || $v === []) {
                continue;
            }
            $out[$k] = $v;
        }

        return $out;
    }

    public static function orgId(): string
    {
        return url('/').'#organization';
    }

    /** Tashkilot: buxgalteriya xizmati + o'quv markazi. Manzil, telefon, koordinata — Filiallar va sozlamalardan */
    public static function organization(): array
    {
        $branch = Cache::remember('seo:branch:'.app()->getLocale(), 600, fn () => Branch::where('is_active', true)->orderBy('sort')->first());
        $phone = setting('phone') ?: $branch?->phone;

        return [
            '@type' => 'AccountingService',
            '@id' => self::orgId(),
            'name' => setting('site_name', 'Buxgalter MChJ'),
            'url' => url('/'),
            'logo' => asset('img/apple-touch-icon.png'),
            'image' => brand_url('og_image', 'img/og.jpg'),
            'description' => tr(setting('seo_description')),
            'telephone' => $phone,
            'address' => $branch ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $branch->tr('address'),
                'addressCountry' => 'UZ',
            ] : null,
            'geo' => $branch && $branch->lat && $branch->lng ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $branch->lat, 'longitude' => (float) $branch->lng] : null,
            'hasMap' => $branch?->map_url,
            'sameAs' => array_values(array_filter([setting('telegram_url'), setting('instagram_url'), setting('youtube_url')])),
        ];
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => setting('site_name', 'Buxgalter MChJ'),
            'url' => url('/'),
            'inLanguage' => app()->getLocale(),
            'publisher' => ['@id' => self::orgId()],
        ];
    }

    /** @param  array<string, string|null>  $crumbs  [nom => havola|null(joriy sahifa)] */
    public static function breadcrumbs(array $crumbs): array
    {
        $items = [['name' => __('Asosiy'), 'url' => lroute('home')]];
        foreach ($crumbs as $name => $url) {
            $items[] = ['name' => (string) $name, 'url' => $url ?: url()->current()];
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($c, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['url']])->all(),
        ];
    }

    public static function course(Course $c): array
    {
        return [
            '@type' => 'Course',
            'name' => $c->tr('title'),
            'description' => $c->tr('seo_description') ?: $c->tr('excerpt') ?: \Illuminate\Support\Str::limit(strip_tags((string) $c->tr('description')), 300),
            'url' => url()->current(),
            'image' => media_url($c->image),
            'inLanguage' => app()->getLocale(),
            'provider' => ['@id' => self::orgId()],
        ];
    }

    public static function article(Article $a): array
    {
        return [
            '@type' => 'BlogPosting',
            'headline' => \Illuminate\Support\Str::limit((string) $a->tr('title'), 110, ''),
            'description' => $a->tr('seo_description') ?: $a->tr('excerpt'),
            'image' => media_url($a->cover),
            'datePublished' => $a->published_at?->toIso8601String(),
            'dateModified' => ($a->updated_at && $a->published_at && $a->updated_at->gt($a->published_at) ? $a->updated_at : $a->published_at)?->toIso8601String(),
            'inLanguage' => app()->getLocale(),
            'mainEntityOfPage' => url()->current(),
            'articleSection' => $a->category?->tr('name'),
            'author' => ['@id' => self::orgId()],
            'publisher' => ['@id' => self::orgId()],
        ];
    }

    /** Bir nechta tugunni bitta @graph bilan chiqarish */
    public static function graph(array ...$nodes): string
    {
        return self::jsonLd(['@context' => 'https://schema.org', '@graph' => array_values(array_filter($nodes))]);
    }
}
