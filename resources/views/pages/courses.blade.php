@extends('layouts.app')
@section('popup', 'course')
@section('title', hs('courses_page.title', 'Buxgalteriya kurslari').' — Buxgalter MChJ')
@section('description', hs('courses_page.lead', 'Noldan boshlab buxgalteriya hisobi, 1C va soliq hisobotlarini o‘rganing. Toshkentda oflayn hamda onlayn formatda.'))

@section('content')
    {{-- Sahifa konstruktori: admin → Sozlamalar → Sahifa tuzilishi --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('courses'), 'ctx' => 'courses'])
@endsection
