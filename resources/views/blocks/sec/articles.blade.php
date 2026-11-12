@php
    $articles = \App\Models\Article::published()->with('category')->take(5)->get();
    $featured = $articles->firstWhere('is_featured', true) ?? $articles->first();
@endphp
@include('sections.articles')
