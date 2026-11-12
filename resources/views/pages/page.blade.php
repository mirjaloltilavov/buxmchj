@extends('layouts.app')
@section('title', ($page->tr('seo_title') ?: $page->tr('title')).' — '.setting('site_name', 'Buxgalter MChJ'))
@section('description', $page->tr('seo_description') ?: $page->tr('lead') ?: \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $page->tr('content')))), 155))
@if($page->image)@section('og_image', media_url($page->image))@endif
@section('content')
    @include('partials.page-hero', ['title' => $page->tr('title'), 'lead' => $page->tr('lead'), 'crumbs' => [$page->tr('title') => null]])
    <section class="section">
        <div class="container">
            @if($page->image)<img class="cpage__img" src="{{ media_url($page->image) }}" alt="{{ $page->tr('title') }}" loading="lazy">@endif
            <div class="prose">{!! $page->tr('content') !!}</div>
        </div>
    </section>
    @if($page->show_consult)
        @include('sections.consultation', ['interest' => 'course'])
    @endif
@endsection
