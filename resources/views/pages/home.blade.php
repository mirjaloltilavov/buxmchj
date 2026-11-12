@extends('layouts.app')
@section('popup', 'course')
@push('schema'){!! \App\Support\Seo::graph(\App\Support\Seo::organization(), \App\Support\Seo::website()) !!}@endpush

@section('content')
    {{-- Sahifa konstruktori: admin → Sozlamalar → Sahifa tuzilishi --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('home'), 'ctx' => 'home'])
@endsection
