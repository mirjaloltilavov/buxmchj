{{-- Figma: Maqola kartasi (rasm 464/290, meta, sarlavha, tavsif, "Maqolani o‘qish") --}}
@php $url = lroute('article', ['article' => $article->slug]); @endphp
<article class="acard" data-reveal style="--d: {{ ($i ?? 0) * 60 }}ms">
    <a href="{{ $url }}" class="acard__img" tabindex="-1" aria-hidden="true"><img src="{{ media_url($article->cover) }}" alt="" loading="lazy"></a>
    <div class="acard__body">
        @include('partials.article-meta', ['article' => $article, 'withCategory' => true])
        <div class="acard__text">
            <h3><a href="{{ $url }}">{{ $article->tr('title') }}</a></h3>
            @if($article->tr('excerpt'))<p>{{ $article->tr('excerpt') }}</p>@endif
        </div>
        <a href="{{ $url }}" class="read-link">{{ __('Maqolani o‘qish') }} {{ icon('arrow-right') }}</a>
    </div>
</article>
