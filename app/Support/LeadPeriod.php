<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/** Statistika davri (sahifa filtri) */
class LeadPeriod
{
    public const OPTIONS = [
        'today' => 'Bugun', 'yesterday' => 'Kecha', '7' => 'Oxirgi 7 kun', '30' => 'Oxirgi 30 kun', '90' => 'Oxirgi 90 kun',
        'month' => 'Shu oy', 'prev_month' => 'O‘tgan oy', 'year' => 'Shu yil', 'custom' => 'Tanlangan oraliq',
    ];

    /** @return array{0: Carbon, 1: Carbon} */
    public static function range(?array $f): array
    {
        $p = $f['period'] ?? '30';
        $now = now();

        return match ($p) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            '7', '30', '90' => [$now->copy()->subDays((int) $p - 1)->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            'prev_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            'custom' => [
                Carbon::parse($f['from'] ?? $now->copy()->subDays(29))->startOfDay(),
                Carbon::parse($f['to'] ?? $now)->endOfDay(),
            ],
            default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /** Xuddi shu uzunlikdagi oldingi davr */
    public static function previous(array $range): array
    {
        [$from, $to] = $range;
        $len = $from->diffInSeconds($to);

        return [$from->copy()->subSeconds($len + 1), $from->copy()->subSecond()];
    }

    public static function humanMinutes(?float $min): string
    {
        if ($min === null) {
            return '—';
        }
        if ($min < 60) {
            return round($min).' daqiqa';
        }
        if ($min < 60 * 24) {
            return round($min / 60, 1).' soat';
        }

        return round($min / 1440, 1).' kun';
    }
}
