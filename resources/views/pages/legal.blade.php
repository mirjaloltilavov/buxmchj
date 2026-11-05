@extends('layouts.app')
@php $title = $key === 'privacy' ? __('Maxfiylik siyosati') : __('Ommaviy oferta'); @endphp
@section('title', $title.' — Buxgalter MChJ')
@section('content')
    @include('partials.page-hero', ['title' => $title, 'crumbs' => [$title => null]])
    <section class="section"><div class="container"><div class="prose">{!! tr(setting('legal_'.$key)) ?: '<p>'.e(__('Matn tez orada qo‘shiladi.')).'</p>' !!}</div></div></section>
@endsection
