<?php

namespace App\Http\Controllers;

use App\Models\Pageview;
use App\Models\Visit;
use App\Support\Analytics\Agent;
use App\Support\Analytics\GeoIp;
use App\Support\Analytics\Source;
use Illuminate\Http\Request;

/**
 * Sayt faolligi: sahifa ko'rish (pv) va sahifada qolish vaqti (ping).
 * Botlar va tizimga kirgan xodimlar hisobga olinmaydi; IP saqlanmaydi.
 */
class TrackController extends Controller
{
    public function pv(Request $request)
    {
        $ua = (string) $request->userAgent();
        if (Agent::isBot($ua) || auth()->check()) {
            return response()->noContent();
        }
        $d = $request->validate([
            'vid' => ['required', 'uuid'], 'sid' => ['required', 'uuid'], 'new' => ['nullable', 'boolean'],
            'path' => ['required', 'string', 'max:255'], 'title' => ['nullable', 'string', 'max:255'],
            'ref' => ['nullable', 'string', 'max:500'], 'lang' => ['nullable', 'string', 'max:5'], 'screen' => ['nullable', 'string', 'max:12'],
            'utm_source' => ['nullable', 'string', 'max:120'], 'utm_medium' => ['nullable', 'string', 'max:120'], 'utm_campaign' => ['nullable', 'string', 'max:160'],
        ]);
        $path = '/'.ltrim(parse_url($d['path'], PHP_URL_PATH) ?: '/', '/');
        if (str_starts_with($path, '/admin') || str_starts_with($path, '/livewire')) {
            return response()->noContent();
        }
        $now = now();
        $visit = Visit::where('uuid', $d['sid'])->first();
        if (! $visit) {
            $refHost = null;
            if (! empty($d['ref'])) {
                $h = strtolower((string) parse_url($d['ref'], PHP_URL_HOST));
                $own = strtolower((string) $request->getHost());
                $refHost = $h && $h !== $own && ! str_ends_with($h, '.'.$own) ? preg_replace('/^(www|m|l|lm)\./', '', $h) : null;
            }
            [$type, $name] = Source::classify($d['utm_source'] ?? null, $d['utm_medium'] ?? null, $refHost);
            $agent = Agent::parse($ua);
            $country = strtoupper((string) $request->header('CF-IPCountry')) ?: null;
            $country = $country && preg_match('/^[A-Z]{2}$/', $country) && $country !== 'XX' ? $country : GeoIp::country($request->ip());
            $visit = Visit::create([
                'uuid' => $d['sid'], 'visitor' => $d['vid'], 'is_new' => (bool) ($d['new'] ?? false),
                'started_at' => $now, 'last_seen_at' => $now, 'landing' => $path,
                'source_type' => $type, 'source' => $name, 'referrer_host' => $refHost,
                'utm_source' => $d['utm_source'] ?? null, 'utm_medium' => $d['utm_medium'] ?? null, 'utm_campaign' => $d['utm_campaign'] ?? null,
                'country' => $country, 'device' => $agent['device'], 'browser' => $agent['browser'], 'os' => $agent['os'],
                'lang' => str_starts_with($path, '/ru') ? 'ru' : 'uz', 'screen' => $d['screen'] ?? null,
            ]);
        }
        $pv = Pageview::create(['visit_id' => $visit->id, 'path' => $path, 'title' => $d['title'] ?? null, 'created_at' => $now]);
        $visit->forceFill([
            'pageviews' => $visit->pageviews + 1, 'exit_path' => $path, 'last_seen_at' => $now,
            'duration' => min(86400, (int) $visit->started_at->diffInSeconds($now, true)),
        ])->save();

        return response()->json(['pv' => $pv->id]);
    }

    public function ping(Request $request)
    {
        $d = $request->validate(['pv' => ['required', 'integer'], 'sid' => ['required', 'uuid'], 'sec' => ['required', 'integer', 'min:0']]);
        $pv = Pageview::with('visit')->find($d['pv']);
        if (! $pv || $pv->visit?->uuid !== $d['sid']) {
            return response()->noContent();
        }
        $now = now();
        $pv->update(['duration' => max($pv->duration, min(3600, (int) $d['sec']))]);
        $pv->visit->forceFill(['last_seen_at' => $now, 'duration' => min(86400, (int) $pv->visit->started_at->diffInSeconds($now, true))])->save();

        return response()->noContent();
    }
}
