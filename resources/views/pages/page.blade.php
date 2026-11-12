@extends('layouts.app')
@php
    $sections = $page->sections ?? [];
    $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags(collect($sections)->map(fn ($s) => tr($s['data']['body'] ?? null).' '.tr($s['data']['lead'] ?? null).' '.tr($s['data']['text'] ?? null))->implode(' ').' '.$page->tr('content'))));
@endphp
@section('title', ($page->tr('seo_title') ?: $page->tr('title')).' — '.setting('site_name', 'Buxgalter MChJ'))
@section('description', $page->tr('seo_description') ?: $page->tr('lead') ?: \Illuminate\Support\Str::limit($plainText, 155))
@php $headImg = collect($sections)->firstWhere('type', 'page_head')['data']['image'] ?? $page->image; @endphp
@if($headImg)@section('og_image', media_url($headImg))@endif
@section('content')
    @if($sections)
        {{-- Sahifa konstruktori: admin → Kontent → Sahifalar → Tuzilishi --}}
        @include('blocks.render', ['items' => $sections, 'ctx' => 'page', 'pageTitle' => $page->tr('title')])
    @else
        {{-- eski (konstruktorsiz) sahifalar --}}
        @include('partials.page-hero', ['title' => $page->tr('title'), 'lead' => $page->tr('lead'), 'crumbs' => [$page->tr('title') => null]])
        <section class="section"><div class="container"><div class="prose">{!! $page->tr('content') !!}</div></div></section>
        @if($page->show_consult)@include('sections.consultation', ['interest' => 'course'])@endif
    @endif
@endsection
