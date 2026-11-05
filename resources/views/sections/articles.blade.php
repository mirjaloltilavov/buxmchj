{{-- Figma: Maqolalar --}}
@if($featured)
@php $list = $articles->reject(fn ($a) => $a->is($featured))->take(4); @endphp
<section class="section section--soft" id="articles">
    <div class="container">
        <div class="articles-head">
            <div class="t">
                <h2 class="h2">{{ hs('articles.title', 'Foydali maqolalar') }}</h2>
                <p>{{ hs('articles.lead', 'Soliq, 1C va buxgalteriya bo‘yicha amaliy maslahatlar — tadbirkorlar va o‘quvchilar uchun.') }}</p>
            </div>
            <a href="{{ lroute('blog') }}" class="btn-pill btn-pill--outline btn-pill--l">{{ __('Barcha maqolalar') }} <span class="arrow">{{ icon('arrow-right') }}</span></a>
        </div>
        <div class="articles-grid">
            @php $url = lroute('article', ['article' => $featured->slug]); @endphp
            <article class="article-feature">
                <a href="{{ $url }}" class="img"><img src="{{ media_url($featured->cover) }}" alt="{{ $featured->tr('title') }}" loading="lazy"></a>
                <div style="display:flex;flex-direction:column;gap:12px">
                    @include('partials.article-meta', ['article' => $featured, 'withCategory' => true])
                    <h3><a href="{{ $url }}">{{ $featured->tr('title') }}</a></h3>
                    <p class="ex">{{ $featured->tr('excerpt') }}</p>
                    <a href="{{ $url }}" class="read-link">{{ __('Maqolani o‘qish') }} {{ icon('arrow-right') }}</a>
                </div>
            </article>
            <div class="article-list">
                @foreach($list as $article)
                    <a href="{{ lroute('article', ['article' => $article->slug]) }}" class="article-row">
                        <span class="img"><img src="{{ media_url($article->cover) }}" alt="" loading="lazy"></span>
                        <span class="b">@include('partials.article-meta', ['article' => $article])<h4>{{ $article->tr('title') }}</h4></span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
