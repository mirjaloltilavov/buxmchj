<?php

use App\Models\Setting;
use Illuminate\Support\Str;

if (! function_exists('tr')) {
    /** {"uz":..,"ru":..} massividan joriy tildagi qiymatni oladi. */
    function tr(mixed $value, ?string $locale = null): ?string
    {
        if (is_string($value) || $value === null) {
            return $value;
        }
        $locale ??= app()->getLocale();

        return filled($value[$locale] ?? null) ? $value[$locale] : ($value['uz'] ?? null);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('lroute')) {
    /** Joriy til prefiksi bilan route URL. */
    function lroute(string $name, array $params = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return route(($locale === 'ru' ? 'ru.' : '').$name, $params);
    }
}

if (! function_exists('money_uz')) {
    /** 1512000 -> "1 512 000" */
    function money_uz(?int $amount): string
    {
        return $amount === null ? '' : number_format($amount, 0, '.', ' ');
    }
}

if (! function_exists('media_url')) {
    /** Admin yuklagan fayl yoki public/ ichidagi standart rasm. */
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }
        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }
        if (Str::startsWith($path, 'img/')) {
            return asset($path);
        }

        return asset('uploads/'.ltrim($path, '/'));
    }
}

if (! function_exists('icon')) {
    /** resources/icons/{name}.svg faylini inline chiqaradi (rangi currentColor). */
    function icon(string $name, int|string|null $size = null, string $class = ''): \Illuminate\Support\HtmlString
    {
        static $cache = [];
        $svg = $cache[$name] ??= (\App\Models\Icon::map()[$name] ?? (is_file($f = resource_path("icons/{$name}.svg")) ? file_get_contents($f) : ''));
        $style = $size ? ' style="--s:'.(is_numeric($size) ? $size.'px' : $size).'"' : '';

        return new \Illuminate\Support\HtmlString('<span class="icon '.e($class).'"'.$style.' aria-hidden="true">'.$svg.'</span>');
    }
}

if (! function_exists('hs')) {
    /**
     * Seksiya matni: konstruktordagi seksiyaning o'zidan, so'ng umumiy sozlamadan (setting "texts"),
     * bo'sh bo'lsa standart matnni tarjima qilib qaytaradi.
     */
    function hs(string $key, string $default = ''): string
    {
        // 1) joriy seksiyaning o'z matni (konstruktor), 2) umumiy matn (eski sozlama), 3) shablondagi standart
        $value = tr(\App\Support\TextScope::get($key)) ?: tr(data_get(setting('texts', []), $key));

        return filled($value) ? $value : __($default);
    }
}

if (! function_exists('hs_img')) {
    /** Seksiya rasmi: seksiyaning o'zidan, umumiy sozlamadan yoki standart rasm */
    function hs_img(string $key, string $default): string
    {
        $v = \App\Support\TextScope::get($key) ?: data_get(setting('texts', []), $key);

        return is_string($v) && $v !== '' ? $v : $default;
    }
}

if (! function_exists('hs_list')) {
    /** Ro'yxat ko'rinishidagi matnlar (masalan, belgilar ro'yxati). */
    function hs_list(string $key, array $default = []): array
    {
        $items = \App\Support\TextScope::get($key);
        $items = is_array($items) && $items !== [] ? $items : data_get(setting('texts', []), $key);
        if (is_array($items) && $items !== []) {
            return array_values(array_filter(array_map(fn ($i) => tr($i['text'] ?? $i), $items)));
        }

        return array_map(fn ($d) => __($d), $default);
    }
}

if (! function_exists('price_from')) {
    /** "1 512 000 so‘mdan" / "от 1 512 000 сум" */
    function price_from(?int $amount): string
    {
        if ($amount === null) {
            return '';
        }

        return app()->getLocale() === 'ru' ? 'от '.money_uz($amount).' сум' : money_uz($amount).' so‘mdan';
    }
}

if (! function_exists('article_html')) {
    /**
     * Admin rich-editor HTML'ini Figma "Article body" ko'rinishiga moslaydi:
     *  - "Muhim:" / "Важно:" bilan boshlangan paragraf → sariq callout (info ikonka bilan)
     *  - <ul> elementlari → yashil ✓ ikonka bilan
     *  - Trix rasm biriktirmalari (<figure> ichidagi <a>) tozalanadi
     */
    function article_html(?string $html): string
    {
        $html = (string) $html;
        if ($html === '') {
            return '';
        }

        $info = icon('info-circle');
        $html = preg_replace_callback(
            '~<p>\s*(?:<strong>)?\s*(Muhim|Diqqat|Eslatma|Важно|Внимание)\s*:?\s*(?:</strong>)?\s*:?\s*(.*?)</p>~su',
            fn ($m) => '<aside class="callout"><span class="callout__icon">'.$info.'</span><div><b>'.e($m[1]).'</b><p>'.ucfirst(trim($m[2])).'</p></div></aside>',
            $html
        );

        $tick = icon('tick-circle');
        $html = preg_replace_callback('~<ul>(.*?)</ul>~s', function ($m) use ($tick) {
            return '<ul class="ticks">'.preg_replace('~<li>~', '<li>'.$tick.'<span>', str_replace('</li>', '</span></li>', $m[1])).'</ul>';
        }, $html);

        // Trix: <figure ...><a href=..><img></a><figcaption>…</figcaption></figure>
        $html = preg_replace('~<figure[^>]*data-trix-attachment[^>]*>~', '<figure>', $html);
        $html = preg_replace('~<figure>\s*<a [^>]*>(\s*<img[^>]*>)\s*</a>~', '<figure>$1', $html);
        $html = preg_replace('~<img(?![^>]*loading=)~', '<img loading="lazy"', $html);

        return $html;
    }
}

if (! function_exists('ago')) {
    /** "5 soat avval" — o'zbekcha lotin yozuvida (Carbon'ning "uz" tili kirilcha) */
    function ago($date): ?string
    {
        if (! $date) {
            return null;
        }
        $loc = app()->getLocale();

        return \Illuminate\Support\Carbon::parse($date)->locale($loc === 'uz' ? 'uz_Latn' : $loc)->diffForHumans();
    }
}

if (! function_exists('brand_url')) {
    /** Admin'da yuklangan brend fayli (favicon, OG rasm) yoki standart fayl manzili */
    function brand_url(string $key, string $default): string
    {
        $v = setting($key);
        if (is_array($v)) {
            $v = reset($v) ?: null;
        }

        return filled($v) ? media_url($v) : asset($default);
    }
}
