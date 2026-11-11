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
    // Admin → SEO: sahifa bo'yicha sarlavha va tavsif (bo'sh bo'lsa sahifaning o'zi beradi)
    $seoBase = $route && $route->getName() ? preg_replace('/^ru\./', '', $route->getName()) : null;
    $seoPage = $seoBase ? setting('seo_pages.'.$seoBase) : null;
    if (is_array($seoPage)) {
        $pageTitle = tr($seoPage['title'] ?? null) ?: $pageTitle;
        $pageDesc = tr($seoPage['description'] ?? null) ?: $pageDesc;
    }
    $ogImage = trim($__env->yieldContent('og_image')) ?: brand_url('og_image', 'img/og.jpg');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    @if($pageDesc)<meta name="description" content="{{ $pageDesc }}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
    @if(setting('seo_noindex'))<meta name="robots" content="noindex, nofollow">@endif
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    @if($pageDesc)<meta property="og:description" content="{{ $pageDesc }}">@endif
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $locale === 'ru' ? 'ru_RU' : 'uz_UZ' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <link rel="alternate" hreflang="uz" href="{{ $altUrl('uz') }}">
    <link rel="alternate" hreflang="ru" href="{{ $altUrl('ru') }}">
    <link rel="alternate" hreflang="x-default" href="{{ $altUrl('uz') }}">
    {{-- Favicon: yorug' va qorong'i rejim uchun alohida (admin → SEO va brending) --}}
    <link rel="icon" href="{{ brand_url('favicon_light', 'img/favicon-light.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" href="{{ brand_url('favicon_dark', 'img/favicon-dark.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="preload" href="{{ asset('fonts/golos-text-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(filled(setting('gsc_meta')))<meta name="google-site-verification" content="{{ setting('gsc_meta') }}">@endif
    @php $gaId = trim((string) setting('ga_id')); @endphp
    @if(preg_match('/^G-[A-Z0-9]{4,}$/', $gaId))
        {{-- Google Analytics 4 (admin panel → Sayt sozlamalari → Google) --}}
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
    @endif
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
