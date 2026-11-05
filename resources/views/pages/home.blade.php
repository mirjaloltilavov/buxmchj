@extends('layouts.app')
@section('popup', 'course')

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
