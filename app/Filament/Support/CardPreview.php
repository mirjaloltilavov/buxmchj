<?php

namespace App\Filament\Support;

use Illuminate\Support\HtmlString;

/** Admin'da kartochkalarni saytdagiga o'xshash ko'rsatish. */
class CardPreview
{
    protected static function t($v): string
    {
        $s = is_array($v) ? ($v['uz'] ?? '') : (string) $v;

        return e($s);
    }

    protected static function ic(?string $name, string $cls = ''): string
    {
        return '<span class="bx-tile '.$cls.'">'.(icon($name ?: 'user')).'</span>';
    }

    protected static function img(?string $path): string
    {
        return $path ? '<img src="'.e(media_url($path)).'" alt="" class="bx-thumb">' : '<span class="bx-thumb bx-thumb--empty">Rasm yo‘q</span>';
    }

    public static function render(string $kind, array $it, int $i = 0, int $count = 1): HtmlString
    {
        $h = match ($kind) {
            'audience' => '<div class="bx-aud '.($i === 0 ? 'is-acc' : ($i === $count - 1 && $count > 2 ? 'is-dark' : '')).'">'
                .static::ic($it['icon'] ?? 'user').'<b>'.static::t($it['title'] ?? '').'</b><p>'.static::t($it['text'] ?? '').'</p></div>',
            'program' => '<div class="bx-mod">'.static::img($it['image'] ?? null).'<div class="bx-mod__b"><div class="bx-mod__top"><small>Modul '.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT).'</small>'.static::ic($it['icon'] ?? 'book', 'bx-tile--s').'</div><b>'.static::t($it['title'] ?? '').'</b><p>'.static::t($it['text'] ?? '').'</p></div></div>',
            'stat' => '<div class="bx-stat"><b>'.static::t($it['value'] ?? '').'</b><span>'.($it['icon'] ?? null ? '<span class="bx-ic bx-ic--s">'.icon($it['icon']).'</span> ' : '').static::t($it['label'] ?? '').'</span></div>',
            'outcome' => '<div class="bx-out"><span class="bx-tick">'.icon('tick-circle').'</span><span>'.static::t($it['text'] ?? '').'</span></div>',
            'tag' => '<span class="bx-tag '.(! empty($it['accent']) ? 'is-acc' : '').'">'.(($it['icon'] ?? null) ? '<span class="bx-ic bx-ic--s">'.icon($it['icon']).'</span>' : '').static::t($it['text'] ?? '').'</span>',
            'plan' => '<div class="bx-plan '.(! empty($it['highlight']) ? 'is-hl' : '').'"><b>'.static::t($it['name'] ?? '').'</b><small>'.static::t($it['subtitle'] ?? '').'</small>'
                .'<div class="bx-plan__price">'.number_format((int) ($it['price_once'] ?? 0), 0, '', ' ').' so‘m'
                .(! empty($it['price_split']) ? ' <s>'.number_format((int) $it['price_split'], 0, '', ' ').'</s>' : '').'</div>'
                .'<ul>'.collect($it['features'] ?? [])->take(4)->map(fn ($f) => '<li><span class="bx-tick">'.icon('tick-circle').'</span>'.static::t($f['text'] ?? '').'</li>')->implode('').'</ul></div>',
            'feature' => '<div class="bx-out"><span class="bx-tick">'.icon('tick-circle').'</span><span>'.static::t($it['text'] ?? '').'</span></div>',
            default => '<div class="bx-out"><span>'.static::t($it['title'] ?? $it['text'] ?? $it['name'] ?? '—').'</span></div>',
        };

        return new HtmlString($h);
    }
}
