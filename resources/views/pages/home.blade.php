@extends('layouts.app')
@section('popup', 'course')
@push('schema'){!! \App\Support\Seo::graph(\App\Support\Seo::organization(), \App\Support\Seo::website()) !!}@endpush

@section('content')
    @include('sections.hero')
    @include('sections.directions')
    @include('sections.why')
    @include('sections.courses-overview')
    @include('sections.services-overview')
    @include('sections.faq')
    @include('sections.articles')
    @include('sections.consultation')
    @include('sections.instagram')
@endsection
