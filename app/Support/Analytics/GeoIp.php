<?php

namespace App\Support\Analytics;

use Illuminate\Support\Facades\Http;

/**
 * IP → davlat (ISO kodi). Baza: DB-IP "IP to Country Lite" (CC BY 4.0, https://db-ip.com),
 * oyda bir yangilanadi. Ixcham ikkilik faylga aylantiriladi: har yozuv = boshi(16) + oxiri(16) + kod(2) bayt.
 * IP saqlanmaydi — faqat aniqlangan davlat kodi.
 */
class GeoIp
{
    private const REC = 34;

    public static function path(): string
    {
        return storage_path('app/geoip/country.bin');
    }

    public static function available(): bool
    {
        return is_file(self::path()) && filesize(self::path()) > self::REC;
    }

    public static function country(?string $ip): ?string
    {
        if (! $ip || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || ! self::available()) {
            return null;
        }
        $key = self::key($ip);
        $fh = fopen(self::path(), 'rb');
        $lo = 0;
        $hi = intdiv(filesize(self::path()), self::REC) - 1;
        $found = null;
        while ($lo <= $hi) {
            $mid = intdiv($lo + $hi, 2);
            fseek($fh, $mid * self::REC);
            $rec = fread($fh, self::REC);
            $start = substr($rec, 0, 16);
            if (strcmp($key, $start) < 0) {
                $hi = $mid - 1;
            } else {
                if (strcmp($key, substr($rec, 16, 16)) <= 0) {
                    $found = substr($rec, 32, 2);
                    break;
                }
                $lo = $mid + 1;
            }
        }
        fclose($fh);

        return $found && $found !== 'ZZ' ? $found : null;
    }

    /** IPv4 ham IPv6 ko'rinishiga keltiriladi (::ffff:a.b.c.d), 16 bayt */
    private static function key(string $ip): string
    {
        $b = inet_pton($ip);

        return strlen($b) === 4 ? str_repeat("\0", 10)."\xff\xff".$b : $b;
    }

    /** DB-IP bazasini yuklab, ikkilik faylga aylantiradi. @return array{0: bool, 1: string} */
    public static function update(): array
    {
        $tmp = storage_path('app/geoip/dbip.csv.gz');
        @mkdir(dirname($tmp), 0775, true);
        $ok = false;
        foreach ([now(), now()->subMonthNoOverflow()] as $d) {
            try {
                $r = Http::timeout(120)->withOptions(['sink' => $tmp])->get('https://download.db-ip.com/free/dbip-country-lite-'.$d->format('Y-m').'.csv.gz');
                if ($r->ok() && filesize($tmp) > 100000) {
                    $ok = true;
                    break;
                }
            } catch (\Throwable $e) {
                $err = $e->getMessage();
            }
        }
        if (! $ok) {
            return [false, 'Davlatlar bazasini yuklab bo‘lmadi'.(isset($err) ? ': '.$err : '.')];
        }

        return self::build($tmp);
    }

    /** CSV (gz yoki oddiy): start,end,cc → ikkilik fayl */
    public static function build(string $csv): array
    {
        $in = str_ends_with($csv, '.gz') ? gzopen($csv, 'rb') : fopen($csv, 'rb');
        $rows = [];
        $v4lo = str_repeat("\0", 10)."\xff\xff\0\0\0\0";
        $v4hi = str_repeat("\0", 10).str_repeat("\xff", 6);
        while (($line = str_ends_with($csv, '.gz') ? gzgets($in) : fgets($in)) !== false) {
            $p = str_getcsv(trim($line));
            if (count($p) < 3 || ! filter_var($p[0], FILTER_VALIDATE_IP)) {
                continue;
            }
            $start = self::key($p[0]);
            $end = self::key($p[1]);
            // IPv6 yozuvi IPv4 ko'rinishidagi oraliqni (::ffff:0:0/96) qoplasa — o'tkaziladi (IPv4 yozuvlari bilan chalkashmasin)
            if (str_contains($p[0], ':') && strcmp($start, $v4hi) <= 0 && strcmp($end, $v4lo) >= 0) {
                continue;
            }
            $rows[] = $start.$end.substr(strtoupper($p[2]).'ZZ', 0, 2);
        }
        str_ends_with($csv, '.gz') ? gzclose($in) : fclose($in);
        if (count($rows) < 1) {
            return [false, 'Bazada yozuv topilmadi.'];
        }
        sort($rows, SORT_STRING);
        @mkdir(dirname(self::path()), 0775, true);
        $out = self::path().'.tmp';
        if (file_put_contents($out, implode('', $rows)) === false || ! rename($out, self::path())) {
            return [false, 'Faylni yozib bo‘lmadi: '.self::path()];
        }
        @unlink(storage_path('app/geoip/dbip.csv.gz'));

        return [true, number_format(count($rows), 0, '.', ' ').' ta IP oraliq yuklandi.'];
    }
}
