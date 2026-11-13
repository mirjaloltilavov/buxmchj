<?php

namespace App\Support\Analytics;

use App\Models\Pageview;
use App\Models\Visit;
use App\Support\LeadPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/** Sayt faolligi hisob-kitoblari (SQLite va MySQL'da bir xil ishlaydi) */
class Stats
{
    /** Hozir saytda: oxirgi shuncha daqiqada faol bo'lganlar */
    public const LIVE_MINUTES = 3;

    public static function range(?array $filters): array
    {
        return LeadPeriod::range($filters ?? []);
    }

    public static function visits(array $range): Builder
    {
        return Visit::query()->whereBetween('started_at', $range);
    }

    public static function views(array $range): Builder
    {
        return Pageview::query()->whereBetween('created_at', $range);
    }

    /** Sana/soat bo'yicha guruhlash ifodasi */
    public static function bucket(string $col, string $unit): string
    {
        $mysql = DB::connection()->getDriverName() === 'mysql';

        return match ($unit) {
            'hour' => $mysql ? "HOUR($col)" : "CAST(strftime('%H', $col) AS INTEGER)",
            'weekday' => $mysql ? "(DAYOFWEEK($col) + 5) % 7" : "(CAST(strftime('%w', $col) AS INTEGER) + 6) % 7", // 0 = dushanba
            default => "DATE($col)",
        };
    }

    public static function duration(?float $sec): string
    {
        if ($sec === null) {
            return '—';
        }
        $sec = (int) round($sec);
        if ($sec < 60) {
            return $sec.' s';
        }
        $m = intdiv($sec, 60);

        return $m < 60 ? $m.' daq '.($sec % 60).' s' : intdiv($m, 60).' soat '.($m % 60).' daq';
    }

    public static function flag(?string $cc): string
    {
        if (! $cc || ! preg_match('/^[A-Z]{2}$/', $cc)) {
            return '🌐';
        }

        return mb_chr(0x1F1E6 + ord($cc[0]) - 65).mb_chr(0x1F1E6 + ord($cc[1]) - 65);
    }

    public static function country(?string $cc): string
    {
        $n = [
            'UZ' => 'O‘zbekiston', 'RU' => 'Rossiya', 'KZ' => 'Qozog‘iston', 'KG' => 'Qirg‘iziston', 'TJ' => 'Tojikiston', 'TM' => 'Turkmaniston',
            'AF' => 'Afg‘oniston', 'TR' => 'Turkiya', 'US' => 'AQSH', 'DE' => 'Germaniya', 'GB' => 'Buyuk Britaniya', 'KR' => 'Janubiy Koreya',
            'AE' => 'BAA', 'CN' => 'Xitoy', 'UA' => 'Ukraina', 'BY' => 'Belarus', 'AZ' => 'Ozarbayjon', 'GE' => 'Gruziya', 'PL' => 'Polsha',
            'FR' => 'Fransiya', 'IT' => 'Italiya', 'ES' => 'Ispaniya', 'NL' => 'Niderlandiya', 'CA' => 'Kanada', 'JP' => 'Yaponiya', 'IN' => 'Hindiston',
            'PK' => 'Pokiston', 'IR' => 'Eron', 'SA' => 'Saudiya Arabistoni', 'CZ' => 'Chexiya', 'LT' => 'Litva', 'LV' => 'Latviya', 'EE' => 'Estoniya',
            'FI' => 'Finlyandiya', 'SE' => 'Shvetsiya', 'AU' => 'Avstraliya', 'IL' => 'Isroil', 'EG' => 'Misr', 'SG' => 'Singapur', 'HK' => 'Gonkong',
        ];

        return $cc ? ($n[$cc] ?? $cc) : 'Aniqlanmagan';
    }

    public static function deviceLabel(?string $d): string
    {
        return ['mobile' => 'Telefon', 'tablet' => 'Planshet', 'desktop' => 'Kompyuter'][$d] ?? 'Boshqa';
    }

    /** Sahifa manzili → tushunarli nom */
    public static function pageLabel(string $path): string
    {
        $p = preg_replace('~^/ru(?=/|$)~', '', $path) ?: '/';
        $base = match (true) {
            $p === '/' => 'Bosh sahifa',
            $p === '/courses' => 'Kurslar',
            str_starts_with($p, '/courses/') => 'Kurs: '.substr($p, 9),
            $p === '/services' => 'Buxgalteriya xizmati',
            $p === '/blog' => 'Maqolalar',
            str_starts_with($p, '/articles/') => 'Maqola: '.substr($p, 10),
            $p === '/contact' => 'Aloqa',
            $p === '/maxfiylik-siyosati' => 'Maxfiylik siyosati',
            $p === '/ommaviy-oferta' => 'Ommaviy oferta',
            default => $p,
        };

        return $base.(str_starts_with($path, '/ru') ? ' (RU)' : '');
    }
}
