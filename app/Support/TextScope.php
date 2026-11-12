<?php

namespace App\Support;

/**
 * Seksiya ichidagi matnlar: konstruktordagi har bir seksiya o'z matnlarini saqlaydi.
 * Seksiya chizilayotganda uning matnlari "doira"ga qo'yiladi va hs() avval shu yerdan oladi.
 */
class TextScope
{
    private static array $stack = [];

    public static function push(?array $texts): void
    {
        self::$stack[] = is_array($texts) ? $texts : [];
    }

    public static function pop(): void
    {
        array_pop(self::$stack);
    }

    /** Joriy seksiyadagi qiymat ("why.title" kabi kalit) yoki null */
    public static function get(string $key): mixed
    {
        $top = end(self::$stack);

        return $top ? data_get($top, $key) : null;
    }
}
