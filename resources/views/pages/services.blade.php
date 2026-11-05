@extends('layouts.app')
@section('popup', 'service')
@section('title', __('Buxgalteriya xizmati').' — Buxgalter MChJ')

@php
    // Bento uyachalari (Figma: Xizmatlar — bento): katta qora, sariq, oq, ko'k (keng), oq, qora, oq
    $slots = ['big', 'acc', 'plain', 'blue', 'plain', 'dark', 'plain'];
    $planList = $plans->values();
    $base = [
        'yatt' => (int) ($planList[0]->price ?? 1500000),
        'llc' => (int) ($planList[1]->price ?? 3000000),
        'llc_vat' => (int) ($planList[2]->price ?? 5000000),
        'import' => (int) ($planList[3]->price ?? 8000000),
    ];
@endphp

@section('content')
    {{-- Figma: Xizmat — Hero --}}
    @component('partials.p-hero', [
        'title' => __('Buxgalteriya xizmati'),
        'lead' => hs('services_page.lead', 'Hisob yuritish, soliq hisobotlari, ish haqi va bank operatsiyalari — tajribali jamoaga topshiring, o‘zingiz biznesingizga e’tibor bering.'),
        'crumbs' => [__('Buxgalteriya xizmati') => null],
        'image' => setting('texts.services_page.image') ?: 'img/dir-services.jpg',
        'imgTall' => true,
    ])
        <ul class="checks checks--blue">
            @foreach(hs_list('services_page.checks', ['Biriktirilgan bosh buxgalter', 'Hisobotlar o‘z vaqtida topshiriladi', 'Shartnoma asosida javobgarlik']) as $c)
                <li>{{ icon('tick-circle') }} {{ $c }}</li>
            @endforeach
        </ul>
        <div class="hero-ctas">
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult><span class="label">{{ __('Bepul konsultatsiya') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            <a href="#calculator" class="btn-pill btn-pill--white btn-pill--l"><span class="label">{{ __('Narxni hisoblash') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        </div>
        @slot('cards')
            <div class="info-card info-card--dark" style="right:-20px;top:32px">
                <span class="t"><small>{{ __('Oylik xizmat') }}</small><b>{{ price_from($base['yatt']) }}</b></span>
                <span class="round">{{ icon('arrow-right') }}</span>
            </div>
            <div class="info-card" style="left:-40px;top:416px">
                <span class="icon-tile">{{ icon('document-text') }}</span>
                <span class="t"><b>{{ __('Hisobotlar o‘z vaqtida') }}</b><small>{{ __('soliq organiga topshiriladi') }}</small></span>
            </div>
        @endslot
    @endcomponent

    {{-- Figma: Xizmatlar — bento --}}
    <section class="section" id="what">
        <div class="container">
            @include('partials.section-head', ['title' => __('Nimalarni o‘z zimmamizga olamiz'), 'lead' => __('Buxgalteriyaning barcha yo‘nalishlarini bitta jamoa yuritadi — siz faqat natijani ko‘rasiz.'), 'lg' => true])
            <div class="sbento" data-stagger="80">
                @foreach($services->take(7) as $i => $service)
                    @php $sl = $slots[$i] ?? 'plain'; @endphp
                    <div class="sb sb--{{ $sl }} sb--n{{ $i + 1 }}" data-reveal="scale">
                        @if($sl === 'big')
                            <div class="sb__img"><img src="{{ media_url(setting('texts.services_page.bento_image') ?: 'img/services-bento.jpg') }}" alt="" loading="lazy"></div>
                            <div class="sb__body">
                                <div class="sb__head"><span class="icon-tile">{{ icon($service->icon) }}</span><h3>{{ $service->tr('title') }}</h3></div>
                                <p>{{ $service->tr('description') }}</p>
                            </div>
                        @else
                            <span @class(['icon-tile', 'icon-tile--white' => $sl === 'acc', 'icon-tile--blue2' => $sl === 'blue'])>{{ icon($service->icon) }}</span>
                            <div><h3>{{ $service->tr('title') }}</h3><p>{{ $service->tr('description') }}</p></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Figma: Xizmat tariflari --}}
    @if($plans->isNotEmpty())
        <section class="section section--soft" id="plans">
            <div class="container">
                @include('partials.section-head', ['title' => __('Xizmat tariflari'), 'lead' => __('Narx tashkilot shakli, QQS va faoliyat turiga bog‘liq. Aniq narxni konsultatsiyada aytamiz.'), 'lg' => true])
                <div class="pcards pcards--4" data-snap style="--n: {{ max(1, min(4, $plans->count())) }}">
                    @foreach($plans as $i => $plan)
                        <div @class(['pcard', 'pcard--sm', 'pcard--hl' => $plan->is_highlighted]) data-reveal="up40" style="--d: {{ $i * 80 + ($plan->is_highlighted ? 120 : 0) }}ms">
                            <div class="pcard__head"><h3>{{ $plan->tr('name') }}</h3><p>{{ $plan->tr('subtitle') }}</p></div>
                            <div class="pcard__price"><small>{{ __('Oyiga') }}</small><b>{{ money_uz($plan->price) }} {{ __('so‘m') }}</b></div>
                            <a href="#consultation" class="btn-pill btn-pill--l {{ $plan->is_highlighted ? 'btn-pill--ink' : 'btn-pill--outline' }} pcard__btn" data-consult><span class="label">{{ __('Ariza qoldirish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                            <hr>
                            <ul class="pcard__features">@foreach(($plan->features ?? []) as $f)<li>{{ icon('tick-circle') }}<span>{{ tr($f['text'] ?? $f) }}</span></li>@endforeach</ul>
                        </div>
                    @endforeach
                </div>
                <p class="note">{{ __('Narxlar taxminiy. Korxona hajmi va oylik operatsiyalar soniga qarab o‘zgarishi mumkin.') }}</p>
            </div>
        </section>
    @endif

    {{-- Figma: Narx kalkulyatori --}}
    <section class="section" id="calculator">
        <div class="container">
            @include('partials.section-head', ['title' => __('Narxni hisoblang'), 'lead' => __('Bir necha savolga javob bering — taxminiy oylik narxni ko‘rasiz.'), 'lg' => true])
            <div class="calc" data-calc data-base='@json($base)' data-som="{{ __('so‘m') }}">
                <div class="calc__q" data-reveal>
                    <div class="calc__row">
                        <p class="calc__lbl"><i>1</i>{{ __('Tashkilot shakli') }}</p>
                        <div class="segmented"><label><input type="radio" name="org" value="yatt"><span>{{ __('YaTT') }}</span></label><label><input type="radio" name="org" value="llc" checked><span>{{ __('MChJ') }}</span></label></div>
                    </div>
                    <div class="calc__row">
                        <p class="calc__lbl"><i>2</i>{{ __('QQS to‘lovchisimisiz?') }}</p>
                        <div class="segmented"><label><input type="radio" name="vat" value="0"><span>{{ __('Yo‘q') }}</span></label><label><input type="radio" name="vat" value="1" checked><span>{{ __('Ha') }}</span></label></div>
                    </div>
                    <div class="calc__row">
                        <p class="calc__lbl"><i>3</i>{{ __('Faoliyat turi') }}</p>
                        <div class="segmented"><label><input type="radio" name="kind" value="local" checked><span>{{ __('Ichki savdo va xizmat') }}</span></label><label><input type="radio" name="kind" value="import"><span>{{ __('Import-eksport') }}</span></label></div>
                    </div>
                    <div class="calc__row">
                        <p class="calc__lbl"><i>4</i>{{ __('Korxona hajmi') }}</p>
                        <label class="range">
                            <span class="range__top"><b>{{ __('Xodimlar soni') }}</b><em data-out="emp">10 {{ __('kishi') }}</em></span>
                            <input type="range" name="emp" min="1" max="50" value="10" step="1">
                            <span class="range__minmax"><span>1</span><span>50+</span></span>
                        </label>
                        <label class="range">
                            <span class="range__top"><b>{{ __('Oylik operatsiyalar soni') }}</b><em data-out="ops">~120 {{ __('ta') }}</em></span>
                            <input type="range" name="ops" min="0" max="500" value="120" step="10">
                            <span class="range__minmax"><span>0</span><span>500+</span></span>
                        </label>
                    </div>
                </div>
                <div class="calc__res" data-reveal="card" style="--d:120ms">
                    <div class="calc__top">
                        <span class="badge-n" data-res="badge">{{ __('MChJ') }} · {{ __('QQS to‘lovchisi') }}</span>
                        <p class="calc__cap">{{ __('Taxminiy oylik narx') }}</p>
                        <p class="calc__sum"><span data-res="sum">{{ money_uz($base['llc_vat']) }}</span> {{ __('so‘m') }}</p>
                        <p class="calc__hint">{{ __('Aniq narx korxonangiz holatini o‘rgangach aytiladi.') }}</p>
                        <dl class="calc__sum-list">
                            <div data-row="org"><dt>{{ __('Tashkilot') }}</dt><dd data-res="org">{{ __('MChJ') }}</dd></div>
                            <div data-row="vat"><dt>{{ __('QQS') }}</dt><dd data-res="vat">{{ __('Ha') }}</dd></div>
                            <div data-row="kind"><dt>{{ __('Faoliyat') }}</dt><dd data-res="kind">{{ __('Ichki savdo va xizmat') }}</dd></div>
                            <div data-row="emp"><dt>{{ __('Xodimlar') }}</dt><dd data-res="emp">10 {{ __('kishi') }}</dd></div>
                            <div data-row="ops"><dt>{{ __('Operatsiyalar') }}</dt><dd data-res="ops">~120 {{ __('ta / oy') }}</dd></div>
                        </dl>
                    </div>
                    <a href="#consultation" class="btn-pill btn-pill--yellow btn-pill--l calc__btn" data-consult><span class="label">{{ __('Maxsus taklif oling') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                </div>
            </div>
        </div>
    </section>

    {{-- Figma: Ishni qanday boshlaymiz — sticky --}}
    <section class="section section--soft" id="start">
        <div class="container sticky2" data-steps>
            <div class="sticky2__left">
                <div class="program__text" data-reveal>
                    <h2 class="h2">{{ __('Ishni qanday boshlaymiz') }}</h2>
                    <p>{{ __('Buxgalteriyani bizga topshirish to‘rt bosqichda bo‘ladi. Birinchi konsultatsiya bepul.') }}</p>
                </div>
                <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult data-reveal style="--d:120ms"><span class="label">{{ __('Ariza qoldirish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            </div>
            <div class="sticky2__steps">
                <div class="steps-line" aria-hidden="true"><i></i></div>
                @foreach([
                    ['document-text', 'Ariza qoldirasiz', 'Ism va telefon raqamingiz kifoya. Bir ish kuni ichida bog‘lanamiz.'],
                    ['search-normal', 'Holatni o‘rganamiz', 'Bepul konsultatsiyada hisob va hisobotlaringiz holatini ko‘rib chiqamiz.'],
                    ['task-square', 'Shartnoma tuzamiz', 'Tarif, xizmatlar ro‘yxati va javobgarlik shartnomada belgilanadi.'],
                    ['calculator', 'Buxgalteriyani yuritamiz', 'Bosh buxgalter biriktiriladi, hisobotlar o‘z vaqtida topshiriladi.'],
                ] as $i => [$ic, $t, $d])
                    <div class="sstep" data-step data-reveal>
                        <div class="sstep__top"><span class="n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="sstep__done">{{ icon('tick-circle') }}</span><span class="icon-tile icon-tile--40">{{ icon($ic) }}</span></div>
                        <div><h3>{{ __($t) }}</h3><p>{{ __($d) }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('sections.faq')
    @include('sections.consultation', ['interest' => 'service'])
@endsection
