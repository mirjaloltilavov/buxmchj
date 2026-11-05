@extends('layouts.app')
@section('title', __('Maqolalar').' — Buxgalter MChJ')

@section('content')
    @include('partials.page-hero', ['title' => __('Maqolalar'), 'lead' => hs('blog.lead', 'Soliq, 1C va buxgalteriya bo‘yicha amaliy maslahatlar — tadbirkorlar, buxgalterlar va o‘quvchilar uchun.'), 'crumbs' => [__('Maqolalar') => null]])

    <section class="section">
        <div class="container">
            <div class="blog-tools">
                <div class="cats">
                    <a href="{{ lroute('blog') }}" @class(['is-active' => ! $activeCategory])>{{ __('Barchasi') }} <small>{{ $total }}</small></a>
                    @foreach($categories as $cat)
                        <a href="{{ lroute('blog', ['category' => $cat->slug]) }}" @class(['is-active' => $activeCategory === $cat->slug])>{{ $cat->tr('name') }} <small>{{ $cat->articles_count }}</small></a>
                    @endforeach
                </div>
                <form class="search" method="get" action="{{ lroute('blog') }}" role="search">
                    {{ icon('search-normal') }}
                    @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
                    <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Qidiruv') }}" aria-label="{{ __('Qidiruv') }}">
                </form>
            </div>

            @if($articles->isEmpty())
                <p class="note">{{ __('Hech narsa topilmadi.') }}</p>
            @else
                <div class="cards-grid">
                    @foreach($articles as $article)
                        @include('partials.article-card', ['article' => $article])
                    @endforeach
                </div>
                @if($articles->hasPages())
                    <nav class="pager" aria-label="pagination">
                        @foreach($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                            @if($page === $articles->currentPage())<span class="is-current">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
                        @endforeach
                    </nav>
                @endif
            @endif
        </div>
    </section>

    @include('sections.consultation')
@endsection
