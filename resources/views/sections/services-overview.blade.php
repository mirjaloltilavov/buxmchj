{{-- Figma: Services overview --}}
<section class="section section--soft" id="services">
    <div class="container services-ov">
        <div class="left">
            <h2 class="h2">{{ hs('services.title', 'Tadbirkorlar uchun buxgalteriya xizmati') }}</h2>
            <p>{{ hs('services.lead', 'Buxgalteriyani tajribali jamoaga topshiring: hisob yuritish, hisobotlar va soliq organi bilan yozishmalar bizning zimmamizda. Tariflar va to‘liq narxlar Xizmat sahifasida.') }}</p>
            <div class="facts">
                <div class="fact"><span class="k">{{ icon('profile-2user') }} {{ hs('services.kimlar_uchun', 'Kimlar uchun') }}</span><span class="v">{{ hs('services.for', 'YaTT va MChJ') }}</span></div>
                <div class="fact"><span class="k">{{ icon('wallet') }} {{ __('Narx') }}</span><span class="v">{{ hs('services.price_text', '1 500 000 so‘mdan / oy') }}</span></div>
            </div>
            <div class="btn-row">
                <a href="{{ lroute('services') }}" class="btn btn--blue">{{ hs('services.xizmat_va_narxlar', 'Xizmat va narxlar') }} {{ icon('arrow-right') }}</a>
                <a href="{{ lroute('services') }}#calculator" class="btn btn--ghost">{{ hs('services.narxni_hisoblash', 'Narxni hisoblash') }}</a>
            </div>
        </div>
        <div class="right">
            <div class="services-media">
                <img src="{{ media_url(setting('texts.services.image') ?: 'img/services.jpg') }}" alt="" loading="lazy">
                <div class="price-float">
                    <span class="fc-sub" style="font-weight:500">{{ hs('services.oylik_xizmat', 'Oylik xizmat') }}</span>
                    <span class="n">{{ hs('services.price_big', '1 500 000') }}<small>{{ __('so‘mdan') }}</small></span>
                    <span class="s">{{ hs('services.for_long', 'YaTT va MChJ uchun') }}</span>
                </div>
            </div>
            <div class="service-cards">
                @foreach($services as $i => $service)
                    <div class="service-card">
                        <div class="top"><span class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="icon-tile icon-tile--44">{{ icon($service->icon) }}</span></div>
                        <div><h3>{{ $service->tr('title') }}</h3><p>{{ $service->tr('description') }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
