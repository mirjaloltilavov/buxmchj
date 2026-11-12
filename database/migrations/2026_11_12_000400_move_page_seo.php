<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Sayt sozlamalaridagi "SEO: bosh sahifa" va "SEO: ichki sahifalar" qiymatlari tegishli sahifaning o'ziga ko'chiriladi (endi SEO bitta joyda) */
return new class extends Migration
{
    /** settings.value — {"v": qiymat} ko'rinishida saqlanadi */
    private function setting(string $key): mixed
    {
        $raw = DB::table('settings')->where('key', $key)->value('value');
        $d = $raw ? json_decode($raw, true) : null;

        return is_array($d) && array_key_exists('v', $d) ? $d['v'] : $d;
    }

    public function up(): void
    {
        // Bosh sahifa: "SEO: bosh sahifa" sozlamasi
        $home = DB::table('pages')->where('system', 'home')->first();
        if ($home) {
            foreach (['seo_title', 'seo_description'] as $k) {
                $v = $this->setting($k);
                $v = is_array($v) && array_filter($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : null;
                if ($v && empty(array_filter((array) json_decode((string) $home->{$k}, true)))) {
                    DB::table('pages')->where('id', $home->id)->update([$k => $v]);
                }
            }
        }
        $seo = $this->setting('seo_pages') ?: [];
        foreach ((array) $seo as $route => $v) {
            $page = DB::table('pages')->where('system', $route)->first();
            if (! $page || ! is_array($v)) {
                continue;
            }
            $upd = [];
            if (empty(array_filter((array) json_decode((string) $page->seo_title, true))) && ! empty(array_filter((array) ($v['title'] ?? [])))) {
                $upd['seo_title'] = json_encode($v['title'], JSON_UNESCAPED_UNICODE);
            }
            if (empty(array_filter((array) json_decode((string) $page->seo_description, true))) && ! empty(array_filter((array) ($v['description'] ?? [])))) {
                $upd['seo_description'] = json_encode($v['description'], JSON_UNESCAPED_UNICODE);
            }
            if ($upd) {
                DB::table('pages')->where('id', $page->id)->update($upd);
            }
        }
    }

    public function down(): void
    {
    }
};
