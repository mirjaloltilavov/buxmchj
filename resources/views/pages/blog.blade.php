@extends('layouts.app')
@section('popup', 'guide')
@section('title', __('Maqolalar').' — Buxgalter MChJ')
@section('description', hs('blog.lead', 'Soliq, 1C va buxgalteriya bo‘yicha amaliy maslahatlar — tadbirkorlar, buxgalterlar va o‘quvchilar uchun.'))

@php
    $shown = $articles->total() + ($featured ? 1 : 0);
    $countLabel = fn ($n) => $n.' '.__('ta maqola');
    $hints = app()->getLocale() === 'ru'
        ? ['1С: с чего начать', 'Налоги для ИП', 'Сдача отчётов', 'Зарплата и НДФЛ']
        : ['1C dasturi bo‘yicha', 'YaTT uchun soliq', 'Hisobot topshirish', 'Ish haqi va JShDS'];
@endphp

@section('content')
    {{-- Figma: Maqolalar — Hero (qidiruv o‘ng tomonda) --}}
    <section class="p-hero p-hero--blog" data-page-hero>
        <div class="hero-deco" aria-hidden="true">{{ icon('hero-deco') }}</div>
        <div class="container p-hero__body p-hero__body--end">
            <div class="p-hero__left">
                <nav class="crumbs" aria-label="breadcrumb">
                    <a href="{{ lroute('home') }}">{{ __('Asosiy') }}</a>{{ icon('arrow-right') }}<b>{{ __('Maqolalar') }}</b>
                </nav>
                <div class="p-hero__text">
                    <h1>{{ __('Maqolalar') }}</h1>
                    <p class="p-hero__lead">{{ hs('blog.lead', 'Soliq, 1C va buxgalteriya bo‘yicha amaliy maslahatlar — tadbirkorlar, buxgalterlar va o‘quvchilar uchun.') }}</p>
                </div>
            </div>
            <form class="bsearch" method="get" action="{{ lroute('blog') }}" role="search" data-bsearch>
                <label for="bsearch-q">{{ __('Qidiruv') }}</label>
                <div class="bsearch__field">
                    {{ icon('search-normal') }}
                    @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
                    <input id="bsearch-q" type="search" name="q" value="{{ $search }}" autocomplete="off"
                           placeholder="{{ __('Maqola nomi yoki mavzu') }}" data-hints='@json(array_map(fn ($h) => $h.'…', $hints))'>
                    <span class="bsearch__ph" aria-hidden="true"></span>
                </div>
            </form>
        </div>
    </section>

    {{-- Figma: Tanlangan maqola --}}
    @if($featured)
        @php $furl = lroute('article', ['article' => $featured->slug]); @endphp
        <section class="section bfeat-sec">
            <div class="container">
                <article class="bfeat" data-bfeat>
                    <a href="{{ $furl }}" class="bfeat__img" tabindex="-1" aria-hidden="true"><img src="{{ media_url($featured->cover) }}" alt="" fetchpriority="high"></a>
                    <div class="bfeat__body">
                        <div class="bfeat__top">
                            <div class="meta">
                                @if($featured->category)<span class="cat cat--y">{{ $featured->category->tr('name') }}</span>@endif
                                <span>{{ icon('calendar') }} {{ $featured->dateLabel() }}</span>
                                <span>{{ icon('clock') }} {{ $featured->read_minutes }} {{ __('daqiqa') }}</span>
                            </div>
                            <h2><a href="{{ $furl }}">{{ $featured->tr('title') }}</a></h2>
                            @if($featured->tr('excerpt'))<p>{{ $featured->tr('excerpt') }}</p>@endif
                        </div>
                        <a href="{{ $furl }}" class="read-link">{{ __('Maqolani o‘qish') }} {{ icon('arrow-right') }}</a>
                    </div>
                </article>
            </div>
        </section>
    @endif

    {{-- Figma: Barcha maqolalar — filtr + grid --}}
    <section class="section ball" id="all" data-blog>
        <div class="container ball__in">
            <div class="bfilter" data-bfilter>
                <div class="bfilter__chips" role="tablist">
                    <a href="{{ lroute('blog', array_filter(['q' => $search ?: null])) }}#all" @class(['fchip', 'is-active' => ! $activeCategory]) data-fchip>{{ __('Barchasi') }} <small>{{ $total }}</small></a>
                    @foreach($categories as $cat)
                        @continue(! $cat->articles_count)
                        <a href="{{ lroute('blog', array_filter(['category' => $cat->slug, 'q' => $search ?: null])) }}#all" @class(['fchip', 'is-active' => $activeCategory === $cat->slug]) data-fchip>{{ $cat->tr('name') }} <small>{{ $cat->articles_count }}</small></a>
                    @endforeach
                </div>
                <span class="bfilter__count" data-bcount>{{ $countLabel($shown) }}</span>
            </div>

            <div data-bresults>
                @if($search !== '')
                    <p class="bsearch-note">{{ __('Qidiruv natijalari') }}: «{{ $search }}» · <a href="{{ lroute('blog', array_filter(['category' => $activeCategory])) }}#all">{{ __('Tozalash') }}</a></p>
                @endif
                @if($articles->isEmpty())
                    <div class="bempty">
                        <span class="icon-tile">{{ icon('search-normal') }}</span>
                        <b>{{ __('Hech narsa topilmadi.') }}</b>
                        <p>{{ __('Boshqa so‘z bilan qidirib ko‘ring yoki barcha maqolalarni ko‘ring.') }}</p>
                    </div>
                @else
                    <div class="agrid" data-bgrid>
                        @foreach($articles as $i => $article)
                            @include('partials.acard', ['article' => $article, 'i' => $i % 3])
                        @endforeach
                    </div>
                @endif
                @if($articles->hasMorePages())
                    <div class="bmore">
                        <a href="{{ $articles->nextPageUrl() }}#all" class="btn-pill btn-pill--outline btn-pill--l" data-bmore><span class="label">{{ __('Ko‘proq maqolalar') }}</span><span class="arrow">{{ icon('arrow-diag') }}</span></a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Figma: Telegram CTA --}}
    <section class="section tg-sec">
        <div class="container">
            <div class="tg-cta" data-reveal="card">
                <span class="icon-tile icon-tile--blue2 tg-cta__icon" data-tg-plane>{{ icon('send-2') }}</span>
                <div class="tg-cta__t">
                    <h2>{{ hs('blog.tg_title', 'Yangi maqolalarni Telegram’da o‘qing') }}</h2>
                    <p>{{ hs('blog.tg_text', 'Soliq yangiliklari va amaliy maslahatlar — haftasiga bir necha marta.') }}</p>
                </div>
                <a href="{{ setting('telegram_url', 'https://t.me/buxgalter_mchj') }}" target="_blank" rel="noopener" class="btn-pill btn-pill--blue btn-pill--l"><span class="label">{{ __('Kanalga obuna bo‘lish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            </div>
        </div>
    </section>
@endsection
