<?php

namespace App\Services\Integrations;

use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Arizalarni Google Sheets jadvaliga yozish.
 * Jadvalga Apps Script "Web app" o'rnatiladi (kodi admin panelda), sayt unga JSON yuboradi. Google API kalitlari kerak emas.
 */
class GoogleSheets
{
    public const HEADERS = ['ID', 'Sana', 'Ism', 'Telefon', 'Qiziqish', 'Xabar', 'Holat', 'Sahifa', 'Manba', 'Kampaniya', 'Til'];

    public function ready(): bool
    {
        return (bool) preg_match('~^https://script\.google\.com/macros/s/[^/]+/exec~', (string) setting('sheets_url'));
    }

    public function enabled(): bool
    {
        return setting('sheets_enabled') !== false && setting('sheets_enabled') !== 0 && setting('sheets_enabled') !== '0' && $this->ready();
    }

    /** Skript va sayt o'rtasidagi maxfiy kalit (birinchi so'ralganda yaratiladi) */
    public static function secret(): string
    {
        $s = setting('sheets_secret');
        if (! $s) {
            $s = Str::random(32);
            Setting::put('sheets_secret', $s);
        }

        return $s;
    }

    public static function row(Lead $l): array
    {
        return array_map([self::class, 'cell'], self::rawRow($l));
    }

    /**
     * Sheets "+998…", "=…", "-…", "@…" bilan boshlangan matnni formula deb tushunadi (#ERROR!).
     * Boshiga ' qo'yiladi — jadvalda ko'rinmaydi, qiymat matn bo'lib qoladi (formula kiritishdan ham himoya).
     */
    public static function cell(mixed $v): mixed
    {
        return is_string($v) && $v !== '' && in_array($v[0], ['=', '+', '-', '@'], true) ? "'".$v : $v;
    }

    private static function rawRow(Lead $l): array
    {
        return [
            $l->id, ($l->created_at ?? now())->format('d.m.Y H:i'), $l->name, $l->phone,
            Lead::INTERESTS[$l->interest] ?? '', (string) $l->message, Lead::STATUSES[$l->status] ?? (string) $l->status,
            (string) $l->source, $l->channel(), (string) $l->utm_campaign, strtoupper((string) ($l->locale ?: 'uz')),
        ];
    }

    /**
     * @param  iterable<Lead>  $leads
     * @return array{0: bool, 1: string}
     */
    public function send(iterable $leads, bool $test = false): array
    {
        if (! $this->ready()) {
            return [false, 'Web app havolasi kiritilmagan yoki noto‘g‘ri (https://script.google.com/macros/s/.../exec bo‘lishi kerak).'];
        }
        $leads = collect($leads);
        $rows = $leads->map(fn (Lead $l) => self::row($l))->values()->all();
        try {
            $r = Http::timeout(20)->acceptJson()->post(trim((string) setting('sheets_url')), [
                'secret' => self::secret(), 'sheet' => setting('sheets_tab') ?: 'Arizalar', 'headers' => self::HEADERS, 'rows' => $rows,
            ]);
            $json = $r->json();
            if (! $r->ok() || ! is_array($json) || empty($json['ok'])) {
                $err = is_array($json) ? ($json['error'] ?? 'noma’lum javob') : 'Skript JSON qaytarmadi (Web app “Anyone” uchun ochiqligini tekshiring)';

                return [false, $err === 'secret' ? 'Maxfiy kalit mos emas: admin paneldagi skript kodini qaytadan nusxalab qo‘ying.' : 'Google Sheets: '.$err];
            }
        } catch (\Throwable $e) {
            return [false, 'Google Sheets bilan bog‘lanib bo‘lmadi: '.$e->getMessage()];
        }
        if (! $test) {
            Lead::whereKey($leads->pluck('id')->filter())->update(['sheets_synced_at' => now()]);
        }

        return [true, $test ? 'Jadvalga sinov qatori yozildi.' : count($rows).' ta ariza jadvalga yozildi.'];
    }

    public function test(): array
    {
        $l = new Lead(['name' => 'Sinov Mijoz', 'phone' => '+998 90 000 00 00', 'interest' => 'course', 'message' => 'Sinov qatori — o‘chirib yuborishingiz mumkin', 'status' => 'new', 'source' => '/admin']);
        $l->created_at = now();

        return $this->send([$l], true);
    }

    /** Admin panelda ko'rsatiladigan Apps Script kodi */
    public static function script(): string
    {
        $secret = self::secret();

        return <<<JS
// Buxgalter sayti → Google Sheets (arizalar)
const SECRET = '{$secret}';

function doPost(e) {
  const out = (o) => ContentService.createTextOutput(JSON.stringify(o)).setMimeType(ContentService.MimeType.JSON);
  try {
    const data = JSON.parse(e.postData.contents);
    if (data.secret !== SECRET) return out({ ok: false, error: 'secret' });
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName(data.sheet) || ss.insertSheet(data.sheet);
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(data.headers);
      sheet.getRange(1, 1, 1, data.headers.length).setFontWeight('bold').setBackground('#ffd877');
      sheet.setFrozenRows(1);
    }
    const lock = LockService.getScriptLock();
    lock.waitLock(20000);
    data.rows.forEach((r) => sheet.appendRow(r));
    lock.releaseLock();
    return out({ ok: true, added: data.rows.length });
  } catch (err) {
    return out({ ok: false, error: String(err) });
  }
}
JS;
    }
}
