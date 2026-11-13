<?php

namespace App\Support\Analytics;

/** User-Agent'dan qurilma, brauzer va OS (soddalashtirilgan, tashqi kutubxonasiz) */
class Agent
{
    public static function isBot(?string $ua): bool
    {
        return $ua === null || $ua === '' || (bool) preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|embedly|quora|pinterest|vkshare|whatsapp|telegrambot|lighthouse|pagespeed|headless|phantom|python|curl|wget|httpclient|java\//i', $ua);
    }

    /** @return array{device: string, browser: string, os: string} */
    public static function parse(string $ua): array
    {
        $device = preg_match('/iPad|Tablet|PlayBook|Silk|(Android(?!.*Mobile))/i', $ua) ? 'tablet' : (preg_match('/Mobi|iPhone|iPod|Android.*Mobile|Windows Phone/i', $ua) ? 'mobile' : 'desktop');
        $browser = match (true) {
            (bool) preg_match('/YaBrowser/i', $ua) => 'Yandex Browser',
            (bool) preg_match('/Edg\//i', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/SamsungBrowser/i', $ua) => 'Samsung Internet',
            (bool) preg_match('/Instagram/i', $ua) => 'Instagram (ilova ichida)',
            (bool) preg_match('/Telegram/i', $ua) => 'Telegram (ilova ichida)',
            (bool) preg_match('/FBAN|FBAV/i', $ua) => 'Facebook (ilova ichida)',
            (bool) preg_match('/Firefox|FxiOS/i', $ua) => 'Firefox',
            (bool) preg_match('/Chrome|CriOS/i', $ua) => 'Chrome',
            (bool) preg_match('/Safari/i', $ua) => 'Safari',
            default => 'Boshqa',
        };
        $os = match (true) {
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/iPhone|iPad|iPod/i', $ua) => 'iOS',
            (bool) preg_match('/Mac OS X|Macintosh/i', $ua) => 'macOS',
            (bool) preg_match('/CrOS/i', $ua) => 'ChromeOS',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => 'Boshqa',
        };

        return compact('device', 'browser', 'os');
    }
}
