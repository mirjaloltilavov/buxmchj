@extends('layouts.app')
@section('title', ($course->tr('seo_title') ?: $course->tr('title')).' — Buxgalter MChJ')
@section('description', $course->tr('seo_description') ?: $course->tr('excerpt'))
@section('og_image', media_url($course->image))

@section('content')
    @component('partials.page-hero', ['title' => $course->tr('title'), 'lead' => $course->tr('excerpt'), 'crumbs' => [__('Kurslar') => lroute('courses'), $course->tr('title') => null]])
        @if($course->tags)
            <div class="tags" style="margin-top:24px">
                @foreach($course->tags as $tag)
                    <span class="tag" style="background:rgba(255,255,255,.6);color:var(--ink)">@if(! empty($tag['icon'])){{ icon($tag['icon']) }}@endif {{ tr($tag['text'] ?? null) }}</span>
                @endforeach
            </div>
        @endif
        <div class="hero-ctas">
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--xl" data-consult>{{ __('Kursga yozilish') }} <span class="arrow">{{ icon('arrow-diag') }}</span></a>
            @if($course->price)<span class="btn btn--white">{{ price_from($course->price) }}</span>@endif
        </div>
    @endcomponent

    @if($course->tr('description'))
        <section class="section"><div class="container"><div class="prose">{!! $course->tr('description') !!}</div></div></section>
    @endif

    @if($course->audience)
        <section class="section section--soft">
            <div class="container">
                <div class="section-head"><h2 class="h2">{{ __('Kurs kimlar uchun') }}</h2></div>
                <div class="service-cards" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr))">
                    @foreach($course->audience as $item)
                        <div class="service-card"><span class="icon-tile icon-tile--44">{{ icon($item['icon'] ?? 'user') }}</span><div><h3>{{ tr($item['title'] ?? null) }}</h3><p>{{ tr($item['text'] ?? null) }}</p></div></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($course->program)
        <section class="section">
            <div class="container">
                <div class="section-head"><h2 class="h2">{{ __('Kurs dasturi') }}</h2></div>
                <div class="program">
                    @foreach($course->program as $i => $module)
                        <div class="module">
                            <span class="num">{{ __('Modul') }} {{ $i + 1 }}</span>
                            <h3>{{ tr($module['title'] ?? null) }}</h3>
                            @if(tr($module['text'] ?? null))<p style="color:var(--text)">{{ tr($module['text']) }}</p>@endif
                            @if(! empty($module['topics']))
                                <ul>@foreach($module['topics'] as $topic)<li>{{ icon('tick-circle') }} {{ tr($topic['text'] ?? $topic) }}</li>@endforeach</ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($course->plans)
        <section class="section section--soft" id="plans">
            <div class="container">
                <div class="section-head"><h2 class="h2">{{ __('Tariflar') }}</h2></div>
                <div class="plans">
                    @foreach($course->plans as $plan)
                        <div @class(['plan', 'is-hl' => ! empty($plan['highlight'])])>
                            <div><h3>{{ tr($plan['name'] ?? null) }}</h3><p class="sub">{{ tr($plan['subtitle'] ?? null) }}</p></div>
                            <p class="price"><small>{{ tr($plan['price_label'] ?? null) ?: __('Narxi') }}</small>{{ tr($plan['price'] ?? null) }}</p>
                            <ul>@foreach(($plan['features'] ?? []) as $f)<li>{{ icon('tick-circle') }} {{ tr($f['text'] ?? $f) }}</li>@endforeach</ul>
                            <a href="#consultation" class="btn {{ ! empty($plan['highlight']) ? 'btn--acc' : 'btn--ghost' }}" data-consult>{{ __('Kursga yozilish') }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.faq')
    @include('sections.consultation', ['interest' => 'course'])
@endsection
