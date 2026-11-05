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
        $svg = $cache[$name] ??= (is_file($f = resource_path("icons/{$name}.svg")) ? file_get_contents($f) : '');
        $style = $size ? ' style="--s:'.(is_numeric($size) ? $size.'px' : $size).'"' : '';

        return new \Illuminate\Support\HtmlString('<span class="icon '.e($class).'"'.$style.' aria-hidden="true">'.$svg.'</span>');
    }
}

if (! function_exists('hs')) {
    /**
     * Sahifa matni: admin paneldagi "Sahifa matnlari"dan oladi (setting "texts"),
     * bo'sh bo'lsa standart matnni tarjima qilib qaytaradi.
     */
    function hs(string $key, string $default = ''): string
    {
        $value = tr(data_get(setting('texts', []), $key));

        return filled($value) ? $value : __($default);
    }
}

if (! function_exists('hs_list')) {
    /** Ro'yxat ko'rinishidagi matnlar (masalan, belgilar ro'yxati). */
    function hs_list(string $key, array $default = []): array
    {
        $items = data_get(setting('texts', []), $key);
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
