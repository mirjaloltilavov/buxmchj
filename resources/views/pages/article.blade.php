@extends('layouts.app')
@section('popup', 'guide')
@section('title', ($article->tr('seo_title') ?: $article->tr('title')).' — Buxgalter MChJ')
@section('description', $article->tr('seo_description') ?: $article->tr('excerpt'))
@section('og_image', media_url($article->cover))

@php
    $shareUrl = url()->current();
    $shareTitle = $article->tr('title');
    $tags = $article->tags[app()->getLocale()] ?? ($article->tags['uz'] ?? []);
    $author = $article->tr('author') ?: __('Buxgalter jamoasi');
@endphp

@section('content')
    <div class="read-progress" data-read-progress aria-hidden="true"><i></i></div>

    {{-- Figma: Maqola — Hero + Cover --}}
    <section class="a-hero" data-a-hero>
        <div class="container a-hero__body">
            <nav class="crumbs" aria-label="breadcrumb">
                <a href="{{ lroute('home') }}">{{ __('Asosiy') }}</a>{{ icon('arrow-right') }}
                <a href="{{ lroute('blog') }}">{{ __('Maqolalar') }}</a>
                @if($article->category){{ icon('arrow-right') }}<a href="{{ lroute('blog', ['category' => $article->category->slug]) }}#all"><b>{{ $article->category->tr('name') }}</b></a>@endif
            </nav>
            <h1 class="a-hero__title">@foreach(preg_split('/\s+/u', trim($shareTitle)) as $wi => $w)<span class="w" style="--i:{{ $wi }}">{{ $w }}</span> @endforeach</h1>
            <div class="a-meta">
                @if($article->category)<span class="tag">{{ $article->category->tr('name') }}</span>@endif
                <span>{{ icon('user') }} {{ $author }}</span>
                <span>{{ icon('calendar') }} {{ $article->dateLabel() }}</span>
                <span>{{ icon('clock') }} {{ $article->read_minutes }} {{ __('daqiqa o‘qish') }}</span>
            </div>
        </div>
        @if($article->cover)
            <div class="container">
                <div class="a-cover"><img src="{{ media_url($article->cover) }}" alt="{{ $shareTitle }}" fetchpriority="high" data-a-parallax></div>
            </div>
        @endif
    </section>

    {{-- Figma: Article body — chapda sticky ulashish, o‘ngda sticky CTA --}}
    <section class="a-body">
        <div class="container a-body__grid">
            <aside class="a-share">
                <div class="a-share__in">
                    <span class="a-share__label">{{ __('Ulashish') }}</span>
                    <div class="a-share__btns">
                        <a class="icon-btn icon-btn--line" href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}" target="_blank" rel="noopener" aria-label="Telegram">{{ icon('send-2') }}</a>
                        <a class="icon-btn icon-btn--line" href="{{ setting('instagram_url', 'https://instagram.com/buxgalter_mchj') }}" target="_blank" rel="noopener" aria-label="Instagram" data-share-native>{{ icon('instagram') }}</a>
                        <a class="icon-btn icon-btn--line" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" aria-label="Facebook">{{ icon('facebook') }}</a>
                        <button class="icon-btn icon-btn--line" type="button" data-copy="{{ $shareUrl }}" aria-label="{{ __('Havolani nusxalash') }}">{{ icon('link') }}<span class="tip" role="status">{{ __('Nusxalandi') }}</span></button>
                    </div>
                </div>
            </aside>

            <div class="a-content">
                <div class="prose prose--article" data-prose>{!! article_html($article->tr('body')) !!}</div>
                <div class="a-foot">
                    @if($tags)
                        <div class="a-tags">@foreach($tags as $t)<span class="tag">#{{ $t }}</span>@endforeach</div>
                    @endif
                    <div class="a-author" data-reveal>
                        <span class="icon-tile icon-tile--ink">{{ icon('profile-2user') }}</span>
                        <div><b>{{ $author }}</b><p>{{ hs('article.author_bio', 'Amaliyotchi buxgalterlar va ustozlar tayyorlagan maqola.') }}</p></div>
                    </div>
                </div>
            </div>

            <aside class="a-cta-wrap">
                <div class="a-cta" data-reveal="card">
                    <span class="icon-tile">{{ icon('calculator') }}</span>
                    <div class="a-cta__t">
                        <b>{{ hs('article.cta_title', 'Buxgalteriyani bizga topshiring') }}</b>
                        <p>{{ hs('article.cta_text', 'YaTT uchun oylik xizmat — 1 500 000 so‘mdan.') }}</p>
                    </div>
                    <a href="#consultation" class="btn-pill btn-pill--yellow btn-pill--m btn-pill--block" data-consult><span class="label">{{ __('Konsultatsiya') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                </div>
            </aside>
        </div>
    </section>

    {{-- Figma: O‘xshash maqolalar --}}
    @if($related->isNotEmpty())
        <section class="section section--soft a-related">
            <div class="container">
                <div class="a-related__head">
                    <h2 class="h2 h2--56" data-reveal>{{ __('O‘xshash maqolalar') }}</h2>
                    <a href="{{ lroute('blog') }}" class="btn-pill btn-pill--outline btn-pill--l" data-reveal style="--d:120ms"><span class="label">{{ __('Barcha maqolalar') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                </div>
                <div class="agrid agrid--row" data-snap>
                    @foreach($related as $i => $item)
                        @include('partials.acard', ['article' => $item, 'i' => $i + 1])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
