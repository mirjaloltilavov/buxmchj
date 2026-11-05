<?php

namespace App\Support;

/** Yuklangan SVG'ni xavfsiz va sayt uslubiga mos (currentColor) holatga keltiradi. */
class IconSvg
{
    public static function clean(?string $svg): string
    {
        $svg = trim((string) $svg);
        if ($svg === '') {
            return '';
        }
        // faqat <svg> elementini olamiz
        if (preg_match('~<svg\b.*?</svg>~is', $svg, $m)) {
            $svg = $m[0];
        }
        // xavfli narsalar
        $svg = preg_replace('~<(script|foreignObject|iframe|object|embed)\b.*?</\1>~is', '', $svg);
        $svg = preg_replace('~<(script|foreignObject|iframe|object|embed)\b[^>]*/?>~is', '', $svg);
        $svg = preg_replace('~\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\')~i', '', $svg);
        $svg = preg_replace('~(href|xlink:href)\s*=\s*("|\')\s*javascript:[^"\']*\2~i', '', $svg);
        // o'lcham saytdagi --s o'zgaruvchisidan olinadi
        $svg = preg_replace('~(<svg\b[^>]*?)\s(width|height)\s*=\s*("[^"]*"|\'[^\']*\')~i', '$1', $svg);
        $svg = preg_replace('~(<svg\b[^>]*?)\s(width|height)\s*=\s*("[^"]*"|\'[^\']*\')~i', '$1', $svg);
        // ranglar currentColor bo'lsin (none va url(#...) saqlanadi)
        $svg = preg_replace('~\b(fill|stroke)\s*=\s*("|\')(?!none|currentColor|url\()[^"\']+\2~i', '$1=$2currentColor$2', $svg);
        $svg = preg_replace('~\b(fill|stroke)\s*:\s*(?!none|currentColor|url\()#?[0-9a-z(),.% ]+~i', '$1:currentColor', $svg);
        // viewBox bo'lmasa — qo'shamiz
        if (! preg_match('~<svg\b[^>]*viewBox~i', $svg)) {
            $svg = preg_replace('~<svg\b~i', '<svg viewBox="0 0 24 24"', $svg, 1);
        }
        if (! preg_match('~<svg\b[^>]*xmlns=~i', $svg)) {
            $svg = preg_replace('~<svg\b~i', '<svg xmlns="http://www.w3.org/2000/svg"', $svg, 1);
        }

        return $svg;
    }
}
