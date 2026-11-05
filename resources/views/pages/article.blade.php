@extends('layouts.app')
@section('title', ($article->tr('seo_title') ?: $article->tr('title')).' — Buxgalter MChJ')
@section('description', $article->tr('seo_description') ?: $article->tr('excerpt'))
@section('og_image', media_url($article->cover))

@section('content')
    @component('partials.page-hero', ['title' => $article->tr('title'), 'lead' => $article->tr('excerpt'), 'crumbs' => [__('Maqolalar') => lroute('blog'), ($article->category?->tr('name') ?? '') => $article->category ? lroute('blog', ['category' => $article->category->slug]) : null]])
        <div style="margin-top:24px">@include('partials.article-meta', ['article' => $article])</div>
    @endcomponent

    @if($article->cover)
        <div class="container"><div class="article-cover"><img src="{{ media_url($article->cover) }}" alt=""></div></div>
    @endif

    <section class="section" style="padding-top:0">
        <div class="container"><div class="prose">{!! $article->tr('body') !!}</div></div>
    </section>

    @if($related->isNotEmpty())
        <section class="section section--soft">
            <div class="container">
                <div class="section-head reveal"><h2 class="h2">{{ __('O‘xshash maqolalar') }}</h2></div>
                <div class="cards-grid">
                    @foreach($related as $item)
                        @include('partials.article-card', ['article' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.consultation')
@endsection
