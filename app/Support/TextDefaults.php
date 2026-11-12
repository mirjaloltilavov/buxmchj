<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Sahifa shablonlaridagi standart matnlar: hs('kalit', 'matn'), hs_list('kalit', [...]),
 * setting('texts.kalit') ?: 'img/...'. Admin "Sahifa matnlari"da bo'sh maydon o'rniga
 * saytda hozir ko'rinib turgan matn chiqishi uchun ishlatiladi.
 */
class TextDefaults
{
    /** @return array<string, array{uz: mixed, ru: mixed}|string> kalit (texts. siz) => qiymat */
    public static function all(): array
    {
        $files = glob(resource_path('views/{,*/,*/*/}*.blade.php'), GLOB_BRACE) ?: [];
        $sig = md5(implode('|', array_map(fn ($f) => $f.filemtime($f), $files)));

        return Cache::rememberForever('text-defaults:'.$sig, function () use ($files) {
            $out = [];
            $str = "'((?:[^'\\\\]|\\\\.)*)'";
            foreach ($files as $f) {
                $src = file_get_contents($f);
                // hs('kalit', 'matn')
                if (preg_match_all("~\\bhs\\(\\s*'([^']+)'\\s*,\\s*{$str}\\s*\\)~u", $src, $m, PREG_SET_ORDER)) {
                    foreach ($m as $x) {
                        $uz = stripcslashes($x[2]);
                        $out[$x[1]] ??= ['uz' => $uz, 'ru' => self::ru($uz)];
                    }
                }
                // hs_list('kalit', ['a', 'b'])
                if (preg_match_all("~\\bhs_list\\(\\s*'([^']+)'\\s*,\\s*\\[(.*?)\\]\\s*\\)~su", $src, $m, PREG_SET_ORDER)) {
                    foreach ($m as $x) {
                        preg_match_all("~{$str}~u", $x[2], $items);
                        $out[$x[1]] ??= array_map(fn ($s) => ['text' => ['uz' => stripcslashes($s), 'ru' => self::ru(stripcslashes($s))]], $items[1]);
                    }
                }
                // setting('texts.kalit') ?: 'img/...'
                if (preg_match_all("~setting\\('texts\\.([^']+)'\\)\\s*\\?:\\s*'([^']+)'~u", $src, $m, PREG_SET_ORDER)) {
                    foreach ($m as $x) {
                        $out[$x[1]] ??= $x[2];
                    }
                }
            }

            return $out;
        });
    }

    private static function ru(string $uz): string
    {
        $t = app('translator')->get($uz, [], 'ru');

        return is_string($t) ? $t : $uz;
    }

    /** "texts.why.title" => standart qiymat (yoki null) */
    public static function for(string $path): mixed
    {
        return self::all()[preg_replace('/^texts\./', '', $path)] ?? null;
    }
}
