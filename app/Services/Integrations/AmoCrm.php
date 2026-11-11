<?php

namespace App\Services\Integrations;

use App\Models\Lead;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * amoCRM: har bir arizadan sdelka + kontakt (telefon bilan) + izoh yaratadi.
 * Kirish: uzoq muddatli token (amoCRM → Sozlamalar → Integratsiyalar → Integratsiya yaratish → Kalitlar va dostuplar).
 */
class AmoCrm
{
    public function __construct(private ?string $domain = null, private ?string $token = null)
    {
        $this->domain = self::normalizeDomain($domain ?? (string) setting('amo_domain'));
        $this->token = trim((string) ($token ?? setting('amo_token')));
    }

    public static function normalizeDomain(string $d): string
    {
        $d = strtolower(trim($d));
        $d = preg_replace('~^https?://~', '', $d);
        $d = rtrim(explode('/', $d)[0], '.');
        if ($d !== '' && ! str_contains($d, '.')) {
            $d .= '.amocrm.ru';
        }

        return $d;
    }

    public function ready(): bool
    {
        return (bool) preg_match('~^[a-z0-9-]+\.(amocrm\.(ru|com)|kommo\.com)$~', $this->domain) && strlen($this->token) > 20;
    }

    public function enabled(): bool
    {
        $v = setting('amo_enabled');

        return $v !== false && $v !== 0 && $v !== '0' && $this->ready();
    }

    public function leadUrl(int $id): string
    {
        return "https://{$this->domain}/leads/detail/{$id}";
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl("https://{$this->domain}/api/v4/")->withToken($this->token)->acceptJson()->timeout(15);
    }

    private function error($r): string
    {
        $j = $r->json();
        $msg = $j['detail'] ?? $j['title'] ?? $j['hint'] ?? null;
        if ($r->status() === 401) {
            return 'Token noto‘g‘ri yoki muddati tugagan (401).';
        }
        if (! empty($j['validation-errors'])) {
            $msg = json_encode($j['validation-errors'], JSON_UNESCAPED_UNICODE);
        }

        return 'amoCRM xatosi '.$r->status().($msg ? ': '.$msg : '');
    }

    /** @return array<int, array{name: string, statuses: array<int, string>}> */
    public function pipelines(): array
    {
        if (! $this->ready()) {
            return [];
        }

        return Cache::remember('amo:pipelines:'.md5($this->domain.$this->token), 600, function () {
            try {
                $r = $this->http()->get('leads/pipelines');
            } catch (\Throwable) {
                return [];
            }
            if (! $r->ok()) {
                return [];
            }
            $out = [];
            foreach ($r->json('_embedded.pipelines', []) as $p) {
                $st = [];
                foreach ($p['_embedded']['statuses'] ?? [] as $s) {
                    if (! in_array($s['id'], [142, 143], true)) { // "Muvaffaqiyatli" va "Yopilgan" bosqichlariga tushirilmaydi
                        $st[$s['id']] = $s['name'];
                    }
                }
                $out[$p['id']] = ['name' => $p['name'].(! empty($p['is_main']) ? ' (asosiy)' : ''), 'statuses' => $st];
            }

            return $out;
        }) ?: [];
    }

    /** @return array<int, string> */
    public function users(): array
    {
        if (! $this->ready()) {
            return [];
        }

        return Cache::remember('amo:users:'.md5($this->domain.$this->token), 600, function () {
            try {
                $r = $this->http()->get('users', ['limit' => 250]);
            } catch (\Throwable) {
                return [];
            }

            return $r->ok() ? collect($r->json('_embedded.users', []))->mapWithKeys(fn ($u) => [$u['id'] => $u['name']])->all() : [];
        }) ?: [];
    }

    /** @return array{0: bool, 1: string} */
    public function test(): array
    {
        if (! $this->ready()) {
            return [false, 'amoCRM manzili yoki token kiritilmagan.'];
        }
        try {
            $r = $this->http()->get('account');
            if (! $r->ok()) {
                return [false, $this->error($r)];
            }
            Cache::forget('amo:pipelines:'.md5($this->domain.$this->token));
            Cache::forget('amo:users:'.md5($this->domain.$this->token));

            return [true, 'Ulanish ishlayapti: “'.$r->json('name').'” akkaunti. Voronkalar soni: '.count($this->pipelines()).'.'];
        } catch (\Throwable $e) {
            return [false, 'amoCRM bilan bog‘lanib bo‘lmadi: '.$e->getMessage()];
        }
    }

    /** @return array{0: bool, 1: string} */
    public function push(Lead $l): array
    {
        if (! $this->ready()) {
            return [false, 'amoCRM sozlanmagan.'];
        }
        if ($l->amo_lead_id) {
            return [true, 'Allaqachon yuborilgan.'];
        }
        $interest = Lead::INTERESTS[$l->interest] ?? null;
        $deal = array_filter([
            'name' => 'Sayt: '.($interest ? $interest.' — ' : '').$l->name,
            'pipeline_id' => (int) setting('amo_pipeline_id') ?: null,
            'status_id' => (int) setting('amo_status_id') ?: null,
            'responsible_user_id' => (int) setting('amo_responsible_id') ?: null,
            '_embedded' => [
                'tags' => collect(['Sayt', $interest, $l->channel()])->filter()->unique()->map(fn ($t) => ['name' => mb_substr($t, 0, 50)])->values()->all(),
                'contacts' => [array_filter([
                    'name' => $l->name && $l->name !== '—' ? $l->name : $l->phone,
                    'responsible_user_id' => (int) setting('amo_responsible_id') ?: null,
                    'custom_fields_values' => [['field_code' => 'PHONE', 'values' => [['value' => $l->phone, 'enum_code' => 'WORK']]]],
                ])],
            ],
        ]);
        try {
            $r = $this->http()->post('leads/complex', [$deal]);
            if (! $r->successful()) {
                return [false, $this->error($r)];
            }
            $id = (int) $r->json('0.id');
            $note = collect([
                $l->message ? 'Xabar: '.$l->message : null,
                'Qiziqish: '.($interest ?? '—'),
                'Sahifa: '.($l->source ?: '—'),
                'Manba: '.$l->channel().($l->utm_campaign ? ' / kampaniya: '.$l->utm_campaign : ''),
                $l->referrer ? 'Qaysi saytdan: '.$l->referrer : null,
                'Saytdagi ariza #'.$l->id.' · '.($l->created_at ?? now())->format('d.m.Y H:i'),
            ])->filter()->implode("\n");
            $this->http()->post("leads/{$id}/notes", [['note_type' => 'common', 'params' => ['text' => $note]]]);
            if ($l->exists) {
                $l->forceFill(['amo_lead_id' => $id])->saveQuietly();
            }

            return [true, 'amoCRM’da sdelka yaratildi (#'.$id.').'];
        } catch (\Throwable $e) {
            return [false, 'amoCRM bilan bog‘lanib bo‘lmadi: '.$e->getMessage()];
        }
    }
}
