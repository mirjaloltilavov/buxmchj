@php $url = lroute('article', ['article' => $article->slug]); @endphp
<article class="article-feature">
    <a href="{{ $url }}" class="img"><img src="{{ media_url($article->cover) }}" alt="{{ $article->tr('title') }}" loading="lazy"></a>
    <div style="display:flex;flex-direction:column;gap:12px">
        @include('partials.article-meta', ['article' => $article, 'withCategory' => true])
        <h3 style="font-size:22px;line-height:1.4"><a href="{{ $url }}">{{ $article->tr('title') }}</a></h3>
        <p class="ex">{{ $article->tr('excerpt') }}</p>
        <a href="{{ $url }}" class="read-link">{{ __('Maqolani o‘qish') }} {{ icon('arrow-right') }}</a>
    </div>
</article>
