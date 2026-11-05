@php
    $locale = app()->getLocale();
    $current = preg_replace('/^ru\./', '', request()->route()?->getName() ?? '');
    $nav = [
        ['home', __('Asosiy'), ['home']],
        ['courses', __('Kurslar'), ['courses', 'course']],
        ['services', __('Buxgalteriya xizmati'), ['services']],
        ['blog', __('Maqolalar'), ['blog', 'article']],
        ['contact', __('Aloqa'), ['contact']],
    ];
    $phone = setting('phone', '+998 55 500 37 37');
    $phoneHref = 'tel:'.preg_replace('/[^0-9+]/', '', $phone);
@endphp
<header class="site-header {{ $solid ? 'is-solid' : '' }}" data-header>
    <div class="container header-bar">
        <a href="{{ lroute('home') }}" class="logo" aria-label="Buxgalter MChJ">{{ icon('logo') }}</a>

        <nav class="nav" aria-label="{{ __('Asosiy menyu') }}">
            @foreach($nav as [$name, $label, $active])
                <a href="{{ lroute($name) }}" @class(['is-active' => in_array($current, $active)])>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="header-actions">
            <details class="lang">
                <summary class="lang-trigger" aria-label="{{ __('Tilni tanlash') }}">
                    <span class="flag">{{ icon('flag-'.$locale) }}</span>
                    {{ strtoupper($locale) }}
                    <svg class="chev" viewBox="0 0 14 14" fill="none"><path d="M3.5 5.25 7 8.75l3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </summary>
                <div class="lang-menu">
                    <a href="{{ $altUrl('uz') }}" @class(['is-active' => $locale === 'uz']) hreflang="uz"><span class="flag">{{ icon('flag-uz', 28) }}</span> O‘zbekcha</a>
                    <a href="{{ $altUrl('ru') }}" @class(['is-active' => $locale === 'ru']) hreflang="ru"><span class="flag">{{ icon('flag-ru', 28) }}</span> Русский</a>
                </div>
            </details>
            <a href="{{ $phoneHref }}" class="header-phone">{{ icon('call') }} {{ $phone }}</a>
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--m" data-consult>
                {{ __('Konsultatsiya') }}
                <span class="arrow">{{ icon('arrow-diag') }}</span>
            </a>
            <button class="burger" type="button" aria-label="{{ __('Menyu') }}" aria-expanded="false" data-burger><span></span></button>
        </div>
    </div>
</header>

<div class="mobile-menu" data-mobile-menu aria-hidden="true">
    <div class="mm-top">
        <a href="{{ lroute('home') }}" class="logo">{{ icon('logo') }}</a>
        <button class="mm-close" type="button" aria-label="{{ __('Yopish') }}" data-burger-close>×</button>
    </div>
    <nav>
        @foreach($nav as [$name, $label])
            <a href="{{ lroute($name) }}">{{ $label }}</a>
        @endforeach
    </nav>
    <div class="mm-langs">
        <a href="{{ $altUrl('uz') }}" @class(['is-active' => $locale === 'uz'])>UZ</a>
        <a href="{{ $altUrl('ru') }}" @class(['is-active' => $locale === 'ru'])>RU</a>
    </div>
    <a href="{{ $phoneHref }}" class="header-phone">{{ icon('call') }} {{ $phone }}</a>
    <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult data-burger-close>
        {{ __('Konsultatsiya') }} <span class="arrow">{{ icon('arrow-diag') }}</span>
    </a>
</div>
