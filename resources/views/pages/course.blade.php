@extends('layouts.app')
@section('title', ($course->tr('seo_title') ?: $course->tr('title')).' — Buxgalter MChJ')
@section('description', $course->tr('seo_description') ?: $course->tr('excerpt'))
@section('og_image', media_url($course->image))

@php
    $m = $course->meta ?? [];
    $plans = $course->plans ?? [];
    $onRequest = ! empty($m['plans_on_request']) || empty($plans);
@endphp

@section('content')
    {{-- Figma: Kurs — Hero --}}
    @component('partials.p-hero', [
        'title' => $course->tr('title'),
        'lead' => $course->tr('excerpt'),
        'crumbs' => [__('Kurslar') => lroute('courses'), $course->tr('title') => null],
        'image' => $course->image,
        'crumbsRoot' => false,
        'badge' => tr($m['badge'] ?? null),
    ])
        <div class="hero-ctas">
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult><span class="label">{{ __('Kursga yozilish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            <a href="#plans" class="btn-pill btn-pill--white btn-pill--l"><span class="label">{{ __('Tariflarni ko‘rish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        </div>
        @if(! empty($m['stats']))
            <div class="stats">
                @foreach($m['stats'] as $st)
                    <div><b>{{ tr($st['value'] ?? null) }}</b><span>@if(! empty($st['icon'])){{ icon($st['icon']) }}@endif {{ tr($st['label'] ?? null) }}</span></div>
                @endforeach
            </div>
        @endif
        @slot('cards')
            <div class="info-card info-card--dark" style="left:-40px;top:40px">
                <span class="t"><small>{{ tr($m['dark_card_sub'] ?? null) ?: __('Toshkent · onlayn') }}</small><b>{{ tr($m['dark_card_title'] ?? null) ?: __('Yangi guruhga qabul ochiq') }}</b></span>
                <span class="round">{{ icon('arrow-right') }}</span>
            </div>
            @if($course->price)
                <div class="info-card" style="left:-40px;top:376px">
                    <span class="icon-tile">{{ icon('monitor') }}</span>
                    <span class="t"><b>{{ price_from($course->price) }}</b><small>{{ tr($m['price_caption'] ?? null) ?: __('bir martada to‘lansa') }}</small></span>
                </div>
            @endif
        @endslot
    @endcomponent

    {{-- Figma: Kurs kimlar uchun --}}
    @if($course->audience)
        <section class="section" id="audience">
            <div class="container">
                @include('partials.section-head', ['title' => __('Kurs kimlar uchun'), 'lead' => tr($m['audience_lead'] ?? null), 'lg' => true])
                <div class="aud" data-snap data-stagger="80">
                    @foreach($course->audience as $i => $a)
                        @php $variant = $i === 0 ? 'acc' : ($i === count($course->audience) - 1 && count($course->audience) > 2 ? 'dark' : 'plain'); @endphp
                        <div class="aud__tile aud__tile--{{ $variant }}" data-reveal>
                            <span @class(['icon-tile', 'icon-tile--white' => $variant === 'acc', 'icon-tile--blue2' => $variant === 'dark'])>{{ icon($a['icon'] ?? 'user') }}</span>
                            <div><h3>{{ tr($a['title'] ?? null) }}</h3><p>{{ tr($a['text'] ?? null) }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Figma: Kurs dasturi — sticky scroll --}}
    @if($course->program)
        <section class="section" id="program">
            <div class="container program" data-program>
                <div class="program__left">
                    <span class="badge-y" data-reveal>{{ $course->tr('title') }}</span>
                    <div class="program__text" data-reveal style="--d:120ms">
                        <h2 class="h2">{{ __('Kurs dasturi') }}</h2>
                        @if(tr($m['program_lead'] ?? null))<p>{{ tr($m['program_lead']) }}</p>@endif
                    </div>
                    <nav class="program__nav" data-reveal style="--d:240ms">
                        <span class="program__ind" aria-hidden="true"></span>
                        @foreach($course->program as $i => $mod)
                            <a href="#module-{{ $i + 1 }}" @class(['is-active' => $i === 0]) data-module-link="{{ $i }}"><i></i><b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b><span>{{ tr($mod['title'] ?? null) }}</span></a>
                        @endforeach
                    </nav>
                </div>
                <div class="program__mods">
                    @foreach($course->program as $i => $mod)
                        <article class="module" id="module-{{ $i + 1 }}" data-module="{{ $i }}" data-reveal>
                            <div class="module__img">@if(! empty($mod['image']))<img src="{{ media_url($mod['image']) }}" alt="" loading="lazy" data-parallax>@endif</div>
                            <div class="module__body">
                                <div class="module__top"><span>{{ __('Modul') }} {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="icon-tile icon-tile--40">{{ icon($mod['icon'] ?? 'book') }}</span></div>
                                <div><h3>{{ tr($mod['title'] ?? null) }}</h3>@if(tr($mod['text'] ?? null))<p>{{ tr($mod['text']) }}</p>@endif</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Figma: Kurs yakunida --}}
    @if($course->outcomes)
        <section class="section section--soft" id="outcomes">
            <div class="container outcomes">
                <div class="outcomes__img" data-reveal="clip-up">@if(! empty($m['outcomes_image']))<img src="{{ media_url($m['outcomes_image']) }}" alt="" loading="lazy">@endif</div>
                <div class="outcomes__text">
                    <div class="outcomes__head" data-reveal>
                        <h2 class="h2 h2--56">{{ __('Kurs yakunida siz') }}</h2>
                        @if(tr($m['outcomes_lead'] ?? null))<p>{{ tr($m['outcomes_lead']) }}</p>@endif
                    </div>
                    <ul class="outcomes__list">
                        @foreach($course->outcomes as $i => $o)
                            <li class="draw" data-reveal="fade" style="--d: {{ 100 + $i * 60 }}ms"><span class="icon-tile icon-tile--green32">{{ icon('tick-circle') }}</span>{{ tr($o['text'] ?? $o) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

    {{-- Figma: Tariflar --}}
    <section class="section section--soft" id="plans" @if($course->outcomes) style="padding-top:0" @endif>
        <div class="container">
            @if($onRequest)
                <div class="section-head"><h2 class="h2" data-reveal>{{ __('Tariflar') }}</h2></div>
                <div class="on-request" data-reveal>
                    <span class="icon-tile">{{ icon('receipt-text') }}</span>
                    <div class="t"><b>{{ __('Narxi so‘rov bo‘yicha') }}</b><p>{{ __('Narx guruh va o‘qish formatiga qarab belgilanadi. Bepul konsultatsiyada aniq narx va to‘lov shartlarini aytamiz.') }}</p></div>
                    <a href="#consultation" class="btn-pill btn-pill--yellow btn-pill--l shine" data-consult><span class="label">{{ __('Narxni bilish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                </div>
            @else
                <div class="plans-head">
                    <h2 class="h2" data-reveal>{{ __('Tariflar') }}</h2>
                    <div class="plans-head__right" data-reveal style="--d:120ms">
                        @if(tr($m['plans_lead'] ?? null))<p>{{ tr($m['plans_lead']) }}</p>@endif
                        <div class="segmented segmented--sm" data-pay-toggle>
                            <label><input type="radio" name="pay" value="once" checked><span>{{ icon('book') }} {{ __('Bir martada −20%') }}</span></label>
                            <label><input type="radio" name="pay" value="split"><span>{{ icon('briefcase') }} {{ __('Bo‘lib to‘lash') }}</span></label>
                        </div>
                    </div>
                </div>
                <div class="pcards" data-snap>
                    @foreach($plans as $i => $p)
                        @php $hl = ! empty($p['highlight']); @endphp
                        <div @class(['pcard', 'pcard--hl' => $hl]) data-reveal="up40" style="--d: {{ $i * 80 + ($hl ? 120 : 0) }}ms">
                            <div class="pcard__head"><h3>{{ tr($p['name'] ?? null) }}</h3><p>{{ tr($p['subtitle'] ?? null) }}</p></div>
                            <div class="pcard__price" data-price data-once="{{ (int) ($p['price_once'] ?? 0) }}" data-split="{{ (int) ($p['price_split'] ?? 0) }}">
                                <small data-price-label data-once-label="{{ __('Bir martada to‘lansa') }}" data-split-label="{{ __('Bo‘lib to‘lansa') }}">{{ __('Bir martada to‘lansa') }}</small>
                                <b><span data-price-value>{{ money_uz((int) ($p['price_once'] ?? 0)) }}</span> {{ __('so‘m') }}</b>
                                @if(! empty($p['price_split']))
                                    <div class="pcard__full"><span data-price-alt><s>{{ __('Bo‘lib to‘lansa') }}: {{ money_uz((int) $p['price_split']) }} {{ __('so‘m') }}</s></span><em class="badge-y @if($hl) badge-y--dark @endif" data-price-badge>−20%</em></div>
                                @endif
                            </div>
                            <a href="#consultation" class="btn-pill btn-pill--l {{ $hl ? 'btn-pill--ink' : 'btn-pill--outline' }} pcard__btn" data-consult><span class="label">{{ __('Tarifni tanlash') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                            <hr>
                            <ul class="pcard__features">
                                @foreach(($p['features'] ?? []) as $f)<li>{{ icon('tick-circle') }}<span>{{ tr($f['text'] ?? $f) }}</span></li>@endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
            @if(tr($m['plans_note'] ?? null) && ! $onRequest)<p class="note">{{ tr($m['plans_note']) }}</p>@endif
        </div>
    </section>

    @include('sections.teachers', ['soft' => false])
    @include('sections.faq')
    @include('sections.consultation', ['interest' => 'course'])
@endsection
