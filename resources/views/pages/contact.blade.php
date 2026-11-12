@extends('layouts.app')
@push('schema'){!! \App\Support\Seo::graph(\App\Support\Seo::organization()) !!}@endpush
@section('title', __('Aloqa').' — Buxgalter MChJ')
@section('description', hs('contact.lead', 'Kurslar yoki buxgalteriya xizmati bo‘yicha savolingiz bo‘lsa — qo‘ng‘iroq qiling, yozing yoki ofisimizga keling. Ish vaqtida 15 daqiqa ichida javob beramiz.'))


@section('content')
    {{-- Sahifa konstruktori: admin → Sozlamalar → Sahifa tuzilishi --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('contact'), 'ctx' => 'contact'])
@endsection
