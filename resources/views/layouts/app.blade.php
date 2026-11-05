@php
    $locale = app()->getLocale();
    $siteName = setting('site_name', 'Buxgalter MChJ');
    $pageTitle = trim($__env->yieldContent('title')) ?: tr(setting('seo_title')) ?: $siteName;
    $pageDesc = trim($__env->yieldContent('description')) ?: tr(setting('seo_description'));
    // Joriy sahifaning boshqa tildagi manzili
    $route = request()->route();
    $altUrl = function (string $to) use ($route) {
        if (! $route || ! $route->getName()) {
            return $to === 'ru' ? url('/ru') : url('/');
        }
        $base = preg_replace('/^ru\./', '', $route->getName());

        return route(($to === 'ru' ? 'ru.' : '').$base, $route->parameters());
    };
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    @if($pageDesc)<meta name="description" content="{{ $pageDesc }}">@endif
    <meta property="og:title" content="{{ $pageTitle }}">
    @if($pageDesc)<meta property="og:description" content="{{ $pageDesc }}">@endif
    <meta property="og:image" content="{{ trim($__env->yieldContent('og_image')) ?: asset('img/og.jpg') }}">
    <meta property="og:type" content="website">
    <link rel="alternate" hreflang="uz" href="{{ $altUrl('uz') }}">
    <link rel="alternate" hreflang="ru" href="{{ $altUrl('ru') }}">
    <link rel="icon" href="{{ asset('img/logo-mark.jpg') }}">
    <link rel="preload" href="{{ asset('fonts/golos-text-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! setting('head_code') !!}
</head>
<body class="@yield('body_class')">
    @include('partials.header', ['altUrl' => $altUrl, 'theme' => trim($__env->yieldContent('header_theme')) ?: 'yellow'])

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @hasSection('popup')
        @include('partials.promo-popup', ['type' => trim($__env->yieldContent('popup'))])
    @endif

    <script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}" defer></script>
    {!! setting('body_code') !!}
</body>
</html>
