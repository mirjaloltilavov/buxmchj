@php
    // Blok ma'lumoti o'zi yuklanadi — istalgan sahifaga qo'yish mumkin
    $svcPlans = \App\Models\ServicePlan::active()->get();
    $base = \App\Support\Blocks::serviceBase($svcPlans);
@endphp
{{-- Figma: Xizmat — Hero --}}
@component('partials.p-hero', [
    'title' => hs('services_page.buxgalteriya_xizmati', 'Buxgalteriya xizmati'),
    'lead' => hs('services_page.lead', 'Hisob yuritish, soliq hisobotlari, ish haqi va bank operatsiyalari — tajribali jamoaga topshiring, o‘zingiz biznesingizga e’tibor bering.'),
    'crumbs' => [__('Buxgalteriya xizmati') => null],
    'image' => hs_img('services_page.image', 'img/dir-services.jpg'),
    'imgTall' => true,
])
    <ul class="checks checks--blue">
        @foreach(hs_list('services_page.checks', ['Biriktirilgan bosh buxgalter', 'Hisobotlar o‘z vaqtida topshiriladi', 'Shartnoma asosida javobgarlik']) as $c)
            <li>{{ icon('tick-circle') }} {{ $c }}</li>
        @endforeach
    </ul>
    <div class="hero-ctas">
        <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult><span class="label">{{ hs('services_page.bepul_konsultatsiya', 'Bepul konsultatsiya') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        <a href="#calculator" class="btn-pill btn-pill--white btn-pill--l"><span class="label">{{ hs('services_page.narxni_hisoblash', 'Narxni hisoblash') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
    </div>
    @slot('cards')
        <div class="info-card info-card--dark" style="right:-20px;top:32px">
            <span class="t"><small>{{ hs('services_page.oylik_xizmat', 'Oylik xizmat') }}</small><b>{{ price_from($base['yatt']) }}</b></span>
            <span class="round">{{ icon('arrow-right') }}</span>
        </div>
        <div class="info-card" style="left:-40px;top:416px">
            <span class="icon-tile">{{ icon('document-text') }}</span>
            <span class="t"><b>{{ hs('services_page.hisobotlar_oz_vaqtida', 'Hisobotlar o‘z vaqtida') }}</b><small>{{ hs('services_page.soliq_organiga_topshiriladi', 'soliq organiga topshiriladi') }}</small></span>
        </div>
    @endslot
@endcomponent
