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
                    <div class="c-map__canvas" data-leaflet data-lat="{{ $first['lat'] }}" data-lng="{{ $first['lng'] }}" @if(filled(setting('map_tiles'))) data-tiles="{{ setting('map_tiles') }}" @endif role="application" aria-label="{{ __('Xarita') }}"></div>
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
                            <div class="c-line" @if(! $first['hours']) hidden @endif><span class="icon-tile icon-tile--line">{{ icon('clock', 20) }}</span><div><small>{{ hs('contact.ish_vaqti', 'Ish vaqti') }}</small><b data-b="hours">{{ $first['hours'] }}</b></div></div>
                            <div class="c-line"><span class="icon-tile icon-tile--line">{{ icon('call', 20) }}</span><div><small>{{ __('Telefon') }}</small><b data-b="phone">{{ $first['phone'] }}</b></div></div>
                        </div>
                        <div class="c-branch__btns">
                            <a href="{{ $first['route'] }}" target="_blank" rel="noopener" class="btn-pill btn-pill--yellow btn-pill--m btn-pill--block" data-b-href="route"><span class="label">{{ hs('contact.yol_korsatish', 'Yo‘l ko‘rsatish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                            <a href="{{ $first['tel'] }}" class="icon-btn icon-btn--line" aria-label="{{ hs('contact.qongiroq_qilish', 'Qo‘ng‘iroq qilish') }}" data-b-href="tel">{{ icon('call') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
