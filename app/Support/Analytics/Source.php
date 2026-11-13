<?php

namespace App\Support\Analytics;

/** Tashrif manbasi: turi (qidiruv, ijtimoiy, reklama, boshqa sayt, to'g'ridan-to'g'ri) va nomi */
class Source
{
    private const SEARCH = ['google' => 'Google', 'yandex' => 'Yandex', 'bing' => 'Bing', 'yahoo' => 'Yahoo', 'duckduckgo' => 'DuckDuckGo', 'mail.ru' => 'Mail.ru', 'baidu' => 'Baidu', 'ecosia' => 'Ecosia'];

    private const SOCIAL = ['instagram' => 'Instagram', 't.me' => 'Telegram', 'telegram' => 'Telegram', 'facebook' => 'Facebook', 'fb.' => 'Facebook', 'youtube' => 'YouTube', 'youtu.be' => 'YouTube', 'tiktok' => 'TikTok', 'vk.com' => 'VK', 'ok.ru' => 'Odnoklassniki', 'linkedin' => 'LinkedIn', 'twitter' => 'X (Twitter)', 'x.com' => 'X (Twitter)', 'threads' => 'Threads', 'whatsapp' => 'WhatsApp'];

    /** @return array{0: string, 1: ?string} [tur, nom] */
    public static function classify(?string $utmSource, ?string $utmMedium, ?string $refHost): array
    {
        if ($utmSource) {
            $name = self::nameFor($utmSource) ?? $utmSource;
            $isAds = preg_match('/cpc|ppc|paid|ads?|banner|cpm|target/i', (string) $utmMedium);

            return [$isAds ? 'ads' : (self::isSocial($utmSource) ? 'social' : (self::isSearch($utmSource) ? 'search' : 'ads')), $name];
        }
        if ($refHost) {
            if (self::isSearch($refHost)) {
                return ['search', self::nameFor($refHost)];
            }
            if (self::isSocial($refHost)) {
                return ['social', self::nameFor($refHost)];
            }

            return ['referral', $refHost];
        }

        return ['direct', null];
    }

    private static function isSearch(string $s): bool
    {
        foreach (array_keys(self::SEARCH) as $k) {
            if (str_contains(strtolower($s), $k)) {
                return true;
            }
        }

        return false;
    }

    private static function isSocial(string $s): bool
    {
        foreach (array_keys(self::SOCIAL) as $k) {
            if (str_contains(strtolower($s), $k)) {
                return true;
            }
        }

        return false;
    }

    private static function nameFor(string $s): ?string
    {
        foreach (self::SEARCH + self::SOCIAL as $k => $name) {
            if (str_contains(strtolower($s), $k)) {
                return $name;
            }
        }

        return null;
    }
}
