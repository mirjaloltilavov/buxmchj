@extends('layouts.app')
@section('title', __('Aloqa').' — Buxgalter MChJ')
@section('description', hs('contact.lead', 'Kurslar yoki buxgalteriya xizmati bo‘yicha savolingiz bo‘lsa — qo‘ng‘iroq qiling, yozing yoki ofisimizga keling. Ish vaqtida 15 daqiqa ichida javob beramiz.'))

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
    $branchData = $branches->map(fn ($b) => [
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

@section('content')
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

    {{-- Figma: Aloqa — Ariza --}}
    <section class="section c-form" id="form">
        <div class="container c-form__grid">
            <div class="c-form__left" data-stagger="80">
                <span class="chip chip--grey" data-reveal><i class="dot"></i>{{ __('Bepul konsultatsiya') }}</span>
                <div class="c-form__head" data-reveal>
                    <h2 class="h2 h2--56">{{ hs('contact.form_title', 'Savolingizni qoldiring — o‘zimiz qo‘ng‘iroq qilamiz') }}</h2>
                    <p>{{ hs('contact.form_lead', 'Formani to‘ldiring: mutaxassisimiz siz bilan bog‘lanib, mos kurs yoki xizmat turini tanlashga yordam beradi.') }}</p>
                </div>
                <div class="c-points">
                    @foreach([
                        ['Tez javob', 'Ish vaqtida 15 daqiqa ichida qayta qo‘ng‘iroq qilamiz.', 'clock', ''],
                        ['Aniq maslahat', 'Kurs, tarif yoki xizmat narxi bo‘yicha savollarga javob beramiz.', 'message-question', 'icon-tile--blue2'],
                        ['Majburiyatsiz', 'Konsultatsiya bepul, hech narsaga majbur qilmaydi.', 'tick-circle', 'icon-tile--green'],
                    ] as $n => [$t, $d, $i, $cls])
                        <div class="c-point" data-reveal>
                            <span class="icon-tile {{ $cls }}">{{ icon($i) }}</span>
                            <div><b>{{ hs('contact.point'.($n + 1).'_title', $t) }}</b><p>{{ hs('contact.point'.($n + 1).'_text', $d) }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div data-reveal="card" style="--d:150ms">
                @include('partials.lead-form', ['variant' => 'contact'])
            </div>
        </div>
    </section>

    {{-- Figma: Filiallar (xarita + filial kartasi) --}}
    @if($first)
        <section class="section section--soft c-branches" data-branches='@json($branchData)'>
            <div class="container">
                <div class="c-branches__head">
                    <h2 class="h2 h2--56" data-reveal>{{ hs('contact.branches_title', 'Manzilimiz') }}</h2>
                    <p data-reveal style="--d:120ms">{{ hs('contact.branches_lead', 'Offline kurslar Toshkentdagi o‘quv markazimizda o‘tadi.') }}</p>
                </div>
                <div class="c-map" data-reveal="card">
                    @if($first['lat'] && $first['lng'])
                        <div class="c-map__canvas" data-leaflet data-lat="{{ $first['lat'] }}" data-lng="{{ $first['lng'] }}" role="application" aria-label="{{ __('Xarita') }}"></div>
                    @else
                        <iframe class="c-map__frame" src="{{ $first['map'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ __('Xarita') }}" data-map-frame></iframe>
                        <span class="c-pin" aria-hidden="true" data-pin><i></i>{{ icon('location', 28) }}</span>
                    @endif
                    <template class="c-pin-src">{{ icon('location', 28) }}</template>
                    <div class="c-branch">
                        @if($branchData->count() > 1)
                            <div class="c-branch__tabs" role="tablist">
                                @foreach($branchData as $bi => $b)
                                    <button type="button" role="tab" @class(['fchip', 'is-active' => $bi === 0]) data-branch-tab="{{ $bi }}">{{ $b['name'] }}</button>
                                @endforeach
                            </div>
                        @endif
                        <div class="c-branch__in" data-branch-body>
                            <span class="badge-y"><i class="dot"></i><span data-b="name">{{ $first['name'] }}</span></span>
                            <div class="c-branch__lines">
                                <div class="c-line"><span class="icon-tile icon-tile--line">{{ icon('location', 20) }}</span><div><small>{{ __('Manzil') }}</small><b data-b="address">{{ $first['address'] }}</b></div></div>
                                <div class="c-line" @if(! $first['hours']) hidden @endif><span class="icon-tile icon-tile--line">{{ icon('clock', 20) }}</span><div><small>{{ __('Ish vaqti') }}</small><b data-b="hours">{{ $first['hours'] }}</b></div></div>
                                <div class="c-line"><span class="icon-tile icon-tile--line">{{ icon('call', 20) }}</span><div><small>{{ __('Telefon') }}</small><b data-b="phone">{{ $first['phone'] }}</b></div></div>
                            </div>
                            <div class="c-branch__btns">
                                <a href="{{ $first['route'] }}" target="_blank" rel="noopener" class="btn-pill btn-pill--yellow btn-pill--m btn-pill--block" data-b-href="route"><span class="label">{{ __('Yo‘l ko‘rsatish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                                <a href="{{ $first['tel'] }}" class="icon-btn icon-btn--line" aria-label="{{ __('Qo‘ng‘iroq qilish') }}" data-b-href="tel">{{ icon('call') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @include('sections.faq')
    @include('sections.instagram')
@endsection
