@extends('layouts.app')
@section('popup', 'guide')
@php
    $activeCat = $activeCategory ? $categories->firstWhere('slug', $activeCategory) : null;
    $pageNo = (int) request('page') > 1 ? (int) request('page') : null;
@endphp
@section('title', ($activeCat ? $activeCat->tr('name').' — ' : '').__('Maqolalar').($pageNo ? ' — '.__(':n-sahifa', ['n' => $pageNo]) : '').' — Buxgalter MChJ')
@section('description', hs('blog.lead', 'Soliq, 1C va buxgalteriya bo‘yicha amaliy maslahatlar — tadbirkorlar, buxgalterlar va o‘quvchilar uchun.'))

@php
    $shown = $articles->total() + ($featured ? 1 : 0);
    $countLabel = fn ($n) => $n.' '.__('ta maqola');
    $hints = app()->getLocale() === 'ru'
        ? ['1С: с чего начать', 'Налоги для ИП', 'Сдача отчётов', 'Зарплата и НДФЛ']
        : ['1C dasturi bo‘yicha', 'YaTT uchun soliq', 'Hisobot topshirish', 'Ish haqi va JShDS'];
@endphp

@section('content')
    {{-- Sahifa konstruktori: admin → Sozlamalar → Sahifa tuzilishi --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('blog'), 'ctx' => 'blog'])
@endsection
