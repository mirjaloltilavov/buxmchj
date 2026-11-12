@php
    $phone = setting('phone', '+998 55 500 37 37');
    $tel = fn ($p) => 'tel:'.preg_replace('/[^0-9+]/', '', (string) $p);
    $tiles = [
        ['call', __('Telefon'), $phone, $tel($phone), false],
        ['send-2', 'Telegram', setting('telegram_handle', '@buxgalter_mchj'), setting('telegram_url', 'https://t.me/buxgalter_mchj'), true],
        ['instagram', 'Instagram', setting('instagram_handle', '@buxgalter_mchj'), setting('instagram_url', 'https://instagram.com/buxgalter_mchj'), true],
        ['clock', __('Ish vaqti'), tr(setting('work_hours')) ?: __('Du–Sha, 9:00–18:00'), null, false],
    ];
    $mapSrc = fn ($b) => 'https://maps.google.com/maps?q='.urlencode($b->tr('address')).'&z=15&hl='.app()->getLocale().'&output=embed';
    $branchData = \App\Models\Branch::active()->get()->map(fn ($b) => [
        'name' => $b->tr('name'),
        'address' => $b->tr('address'),
        'hours' => $b->tr('hours'),
        'phone' => $b->phone ?: $phone,
        'tel' => $tel($b->phone ?: $phone),
        'map' => $mapSrc($b),
        'lat' => $b->lat ? (float) $b->lat : null,
        'lng' => $b->lng ? (float) $b->lng : null,
        'route' => $b->map_url ?: 'https://maps.google.com/maps?daddr='.urlencode($b->tr('address')),
    ])->values();
    $first = $branchData->first();
@endphp
{{-- Figma: Aloqa — Hero + Contacts --}}
<section class="c-hero" data-c-hero>
    <div class="hero-clip" aria-hidden="true"><div class="hero-deco">{{ icon('hero-deco') }}</div></div>
    <div class="container c-hero__body">
        <nav class="crumbs" aria-label="breadcrumb"><a href="{{ lroute('home') }}">{{ __('Asosiy') }}</a>{{ icon('arrow-right') }}<b>{{ __('Aloqa') }}</b></nav>
        <h1>{{ hs('contact.title', 'Biz bilan bog‘laning') }}</h1>
        <p class="c-hero__lead">{{ hs('contact.lead', 'Kurslar yoki buxgalteriya xizmati bo‘yicha savolingiz bo‘lsa — qo‘ng‘iroq qiling, yozing yoki ofisimizga keling. Ish vaqtida 15 daqiqa ichida javob beramiz.') }}</p>
    </div>
    <div class="container">
        <div class="c-tiles">
            @foreach($tiles as $k => [$ic, $label, $value, $href, $blank])
                @php $tag = $href ? 'a' : 'div'; @endphp
                <{{ $tag }} class="c-tile" @if($href) href="{{ $href }}" @if($blank) target="_blank" rel="noopener" @endif @endif style="--d: {{ 300 + $k * 80 }}ms">
                    <span class="c-tile__top"><span class="c-tile__ic">{{ icon($ic, 24) }}</span>@if($href)<span class="c-tile__go">{{ icon('arrow-right', 20) }}</span>@endif</span>
                    <span class="c-tile__t"><small>{{ $label }}</small><b>{{ $value }}</b></span>
                </{{ $tag }}>
            @endforeach
        </div>
    </div>
</section>
