@extends('layouts.app')
@section('popup', 'course')
@section('title', ($course->tr('seo_title') ?: $course->tr('title')).' — Buxgalter MChJ')
@section('description', $course->tr('seo_description') ?: $course->tr('excerpt'))
@section('og_image', media_url($course->image))
@push('schema'){!! \App\Support\Seo::graph(\App\Support\Seo::course($course)) !!}@endpush

@php
    $m = $course->meta ?? [];
    $plans = $course->plans ?? [];
    $onRequest = ! empty($m['plans_on_request']) || empty($plans);
@endphp

@section('content')
    {{-- Sahifa konstruktori: admin → Sahifalar → shu sahifa → Tahrirlash --}}
    @include('blocks.render', ['items' => \App\Support\Blocks::layout('course'), 'ctx' => 'course'])
@endsection
