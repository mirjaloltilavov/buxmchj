@php
    $locale = app()->getLocale();
    $current = preg_replace('/^ru\./', '', request()->route()?->getName() ?? '');
    // Admin → Menyular → Header menyusi
    $nav = \App\Support\Menu::resolve(\App\Support\Menu::get('menu_header'));
    $phone = setting('phone', '+998 55 500 37 37');
    $phoneHref = 'tel:'.preg_replace('/[^0-9+]/', '', $phone);
@endphp
{{-- Figma: Header (Theme Yellow/Light, State Top/Fixed). Fixed holat va yashirinish — site.js --}}
<header class="site-header" data-header data-theme="{{ $theme ?? 'yellow' }}">
    <div class="container">
        <div class="header-bar">
            <a href="{{ lroute('home') }}" class="logo" aria-label="Buxgalter MChJ">{{ icon('logo', null, 'logo-dark') }}{{ icon('logo-color', null, 'logo-light') }}</a>

            <nav class="nav" aria-label="{{ __('Asosiy menyu') }}" data-nav>
                <span class="nav-pill" aria-hidden="true"></span>
                @foreach($nav as $m)
                    <a href="{{ $m['href'] }}" @class(['is-active' => $m['active']]) @if($m['active']) aria-current="page" @endif @if($m['new_tab']) target="_blank" rel="noopener" @endif>{{ $m['label'] }}</a>
                @endforeach
            </nav>

            <div class="header-actions">
                <details class="lang">
                    <summary class="lang-trigger" aria-label="{{ __('Tilni tanlash') }}">
                        <span class="flag">{{ icon('flag-'.$locale) }}</span>
                        <span class="lang-code">{{ strtoupper($locale) }}</span>
                        <span class="chev">{{ icon('chevron-down', 14) }}</span>
                    </summary>
                    <div class="lang-menu">
                        <a href="{{ $altUrl('uz') }}" @class(['is-active' => $locale === 'uz']) hreflang="uz"><span class="flag">{{ icon('flag-uz') }}</span> O‘zbekcha</a>
                        <a href="{{ $altUrl('ru') }}" @class(['is-active' => $locale === 'ru']) hreflang="ru"><span class="flag">{{ icon('flag-ru') }}</span> Русский</a>
                    </div>
                </details>
                <a href="{{ $phoneHref }}" class="header-phone">{{ icon('call') }} {{ $phone }}</a>
                <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--m header-cta" data-consult>
                    <span class="label">{{ hs('header.cta', 'Konsultatsiya') }}</span>
                    <span class="arrow">{{ icon('arrow-right') }}</span>
                </a>
                <div class="mobile-actions">
                    <a href="{{ $phoneHref }}" class="icon-btn" aria-label="{{ __('Qo‘ng‘iroq qiling') }}">{{ icon('call') }}</a>
                    <button class="icon-btn icon-btn--ink" type="button" aria-label="{{ __('Menyu') }}" aria-expanded="false" data-burger>{{ icon('menu') }}</button>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Figma: Mobile menu (open) --}}
<div class="mobile-menu" data-mobile-menu aria-hidden="true">
    <div class="mm-top">
        <a href="{{ lroute('home') }}" class="logo">{{ icon('logo-color', null, 'logo-light') }}</a>
        <div class="mm-actions">
            <a href="{{ $altUrl($locale === 'uz' ? 'ru' : 'uz') }}" class="icon-btn mm-lang" aria-label="{{ $locale === 'uz' ? 'Русский' : 'O‘zbekcha' }}"><span class="flag">{{ icon('flag-'.$locale) }}</span></a>
            <a href="{{ $phoneHref }}" class="icon-btn" aria-label="{{ __('Qo‘ng‘iroq qiling') }}">{{ icon('call') }}</a>
            <button class="icon-btn icon-btn--ink mm-close" type="button" aria-label="{{ __('Yopish') }}" data-burger-close>{{ icon('close-circle') }}</button>
        </div>
    </div>
    <nav class="mm-links">
        @foreach($nav as $i => $m)
            <a href="{{ $m['href'] }}" @class(['is-active' => $m['active']]) style="--d: {{ 80 + $i * 60 }}ms" @if($m['new_tab']) target="_blank" rel="noopener" @endif>
                <span class="l">@if($m['active'])<i class="dot"></i>@endif{{ $m['label'] }}</span>
                {{ icon('arrow-right') }}
            </a>
        @endforeach
    </nav>
    <div class="mm-bottom">
        <a class="contact-tile contact-tile--acc" href="{{ $phoneHref }}">
            <span class="top">{{ icon('call') }} {{ icon('arrow-diag', 20, 'arr') }}</span>
            <span><small>{{ __('Qo‘ng‘iroq qiling') }}</small><b>{{ $phone }}</b></span>
        </a>
        <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l mm-cta" data-consult data-burger-close>
            <span class="label">{{ hs('header.mobile_cta', 'Bepul konsultatsiya') }}</span><span class="arrow">{{ icon('arrow-right') }}</span>
        </a>
        <div class="mm-socials">
            <a class="icon-btn" href="{{ setting('instagram_url', 'https://instagram.com/buxgalter_mchj') }}" target="_blank" rel="noopener" aria-label="Instagram">{{ icon('instagram') }}</a>
            <a class="icon-btn" href="{{ setting('telegram_url', 'https://t.me/buxgalter_mchj') }}" target="_blank" rel="noopener" aria-label="Telegram">{{ icon('send-2') }}</a>
            @if(setting('youtube_url'))<a class="icon-btn" href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" aria-label="YouTube">{{ icon('youtube') }}</a>@endif
        </div>
    </div>
</div>
