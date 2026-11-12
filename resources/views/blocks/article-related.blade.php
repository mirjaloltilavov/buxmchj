@php $related ??= \App\Models\Article::published()->take(3)->get(); @endphp
@if($related->isNotEmpty())
    <section class="section section--soft a-related">
        <div class="container">
            <div class="a-related__head">
                <h2 class="h2 h2--56" data-reveal>{{ hs('article.oxshash_maqolalar', 'O‘xshash maqolalar') }}</h2>
                <a href="{{ lroute('blog') }}" class="btn-pill btn-pill--outline btn-pill--l" data-reveal style="--d:120ms"><span class="label">{{ hs('article.barcha_maqolalar', 'Barcha maqolalar') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            </div>
            <div class="agrid agrid--row" data-snap>
                @foreach($related as $i => $item)
                    @include('partials.acard', ['article' => $item, 'i' => $i + 1])
                @endforeach
            </div>
        </div>
    </section>
@endif
