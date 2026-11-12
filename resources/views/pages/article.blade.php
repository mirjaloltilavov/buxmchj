@extends('layouts.app')
@section('popup', 'guide')
@section('title', ($article->tr('seo_title') ?: $article->tr('title')).' — Buxgalter MChJ')
@section('description', $article->tr('seo_description') ?: $article->tr('excerpt'))
@section('og_image', media_url($article->cover))
@section('og_type', 'article')
@push('schema'){!! \App\Support\Seo::graph(\App\Support\Seo::article($article)) !!}@endpush

@php
    $shareUrl = url()->current();
    $shareTitle = $article->tr('title');
    $tags = $article->tags[app()->getLocale()] ?? ($article->tags['uz'] ?? []);
    $author = $article->tr('author') ?: __('Buxgalter jamoasi');
@endphp

@section('content')
    {{-- Sahifa konstruktori: admin → Sahifalar → shu sahifa → Tahrirlash --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('article'), 'ctx' => 'article'])
@endsection
