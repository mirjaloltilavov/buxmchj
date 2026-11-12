@extends('layouts.app')
@section('popup', 'service')
@section('title', __('Buxgalteriya xizmati').' — Buxgalter MChJ')
@section('description', hs('services_page.lead', 'Hisob yuritish, soliq hisobotlari, ish haqi va bank operatsiyalari — tajribali jamoaga topshiring, o‘zingiz biznesingizga e’tibor bering.'))


@section('content')
    {{-- Sahifa konstruktori: admin → Sahifalar → shu sahifa → Tahrirlash --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('services'), 'ctx' => 'services'])
@endsection
