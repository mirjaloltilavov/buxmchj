@extends('layouts.app')
@section('popup', 'course')
@push('schema'){!! \App\Support\Seo::graph(\App\Support\Seo::organization(), \App\Support\Seo::website()) !!}@endpush

@section('content')
    {{-- Sahifa konstruktori: admin → Sahifalar → shu sahifa → Tahrirlash --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('home'), 'ctx' => 'home'])
@endsection
