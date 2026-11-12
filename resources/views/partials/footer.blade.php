@php
    $phone = setting('phone', '+998 55 500 37 37');
    $telegram = setting('telegram_url', 'https://t.me/buxgalter_mchj');
    $instagram = setting('instagram_url', 'https://instagram.com/buxgalter_mchj');
    $instagramHandle = setting('instagram_handle', '@buxgalter_mchj');
    $youtube = setting('youtube_url');
    $branch = \App\Models\Branch::active()->first();
@endphp
<footer class="site-footer">
    <div class="container">
        <div class="f-top">
            <div class="h">
                <h2>{{ hs('footer.title1', 'Savolingiz bormi?') }}<br>{{ hs('footer.title2', 'Biz bilan bog‘laning') }}</h2>
                <p>{{ hs('footer.lead', 'Kurs yoki buxgalteriya xizmati bo‘yicha mutaxassis bepul maslahat beradi.') }}</p>
            </div>
            <div class="f-contacts">
                <a class="contact-tile contact-tile--acc" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">
                    <span class="top">{{ icon('call') }} {{ icon('arrow-diag', 20, 'arr') }}</span>
                    <span><small>{{ __('Qo‘ng‘iroq qiling') }}</small><b>{{ $phone }}</b></span>
                </a>
                <a class="contact-tile" href="{{ $telegram }}" target="_blank" rel="noopener">
                    <span class="top">{{ icon('send-2') }} {{ icon('arrow-diag', 20, 'arr') }}</span>
                    <span><small>{{ __('Telegram’da yozing') }}</small><b>Buxgalter MChJ</b></span>
                </a>
                <a class="contact-tile" href="{{ $instagram }}" target="_blank" rel="noopener">
                    <span class="top">{{ icon('instagram') }} {{ icon('arrow-diag', 20, 'arr') }}</span>
                    <span><small>Instagram</small><b>{{ $instagramHandle }}</b></span>
                </a>
            </div>
        </div>

        <div class="f-divider"></div>

        <div class="f-middle">
            <div class="f-brand">
                <a href="{{ lroute('home') }}" class="logo">{{ icon('logo-white') }}</a>
                <p>{{ tr(setting('footer_about')) ?: hs('footer.about', 'Buxgalteriya kurslari va tadbirkorlar uchun buxgalteriya xizmati. 10 yildan beri.') }}</p>
                @if($branch)
                    <div class="branch">
                        <span class="icon-tile icon-tile--ink">{{ icon('location') }}</span>
                        <span class="t"><b>{{ $branch->tr('name') }}</b><small>{{ $branch->tr('address') }}</small></span>
                        @if($branch->map_url)<a href="{{ $branch->map_url }}" target="_blank" rel="noopener">{{ __('Xarita') }}</a>@endif
                    </div>
                @endif
            </div>
            {{-- Admin → Menyular → Footer ustunlari --}}
            <div class="f-cols">
                @foreach(\App\Support\Menu::get('menu_footer') as $col)
                    @php
                        $links = \App\Support\Menu::resolve($col['links'] ?? []);
                        $auto = $col['auto'] ?? null;
                    @endphp
                    <div class="f-col">
                        <h4>{{ tr($col['title'] ?? null) }}</h4>
                        <ul>
                            @if($auto === 'courses')
                                @foreach(\App\Models\Course::active()->get(['slug', 'title']) as $c)
                                    <li><a href="{{ lroute('course', ['course' => $c->slug]) }}">{{ $c->tr('title') }}</a></li>
                                @endforeach
                            @endif
                            @foreach($links as $l)
                                <li><a href="{{ $l['href'] }}" @if($l['new_tab']) target="_blank" rel="noopener" @endif>{{ $l['label'] }}</a></li>
                            @endforeach
                            @if($auto === 'socials')
                                <li><a href="{{ $instagram }}" target="_blank" rel="noopener">Instagram</a></li>
                                <li><a href="{{ $telegram }}" target="_blank" rel="noopener">Telegram</a></li>
                                @if($youtube)<li><a href="{{ $youtube }}" target="_blank" rel="noopener">YouTube</a></li>@endif
                            @endif
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="f-divider"></div>

        <div class="f-bottom">
            <p class="c">© {{ date('Y') }} {{ setting('site_name', 'Buxgalter MChJ') }}. {{ hs('footer.copyright', 'Barcha huquqlar himoyalangan.') }}</p>
            @foreach(\App\Support\Menu::resolve(\App\Support\Menu::get('menu_bottom')) as $l)
                <a href="{{ $l['href'] }}" @if($l['new_tab']) target="_blank" rel="noopener" @endif>{{ $l['label'] }}</a>
            @endforeach
            {{-- Ishlab chiquvchi --}}
            <a href="https://7design.uz" target="_blank" rel="noopener" class="f-credit" title="Seven Design Studio — 7design.uz">
                <span>{{ __('Sayt “Seven Design Studio” tomonidan ishlab chiqilgan') }}</span>
                @include('partials.seven-logo', ['w' => 96])
            </a>
            <a href="#top" class="glass-btn" aria-label="{{ __('Yuqoriga') }}" data-to-top>{{ icon('arrow-up') }}</a>
        </div>
    </div>
    <div class="wordmark" aria-hidden="true"><span>Buxgalter</span></div>
</footer>
