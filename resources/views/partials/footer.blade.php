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
                <h2>{{ __('Savolingiz bormi?') }}<br>{{ __('Biz bilan bog‘laning') }}</h2>
                <p>{{ __('Kurs yoki buxgalteriya xizmati bo‘yicha mutaxassis bepul maslahat beradi.') }}</p>
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
                <p>{{ tr(setting('footer_about')) ?: __('Buxgalteriya kurslari va tadbirkorlar uchun buxgalteriya xizmati. 10 yildan beri.') }}</p>
                @if($branch)
                    <div class="branch">
                        <span class="icon-tile icon-tile--ink">{{ icon('location') }}</span>
                        <span class="t"><b>{{ $branch->tr('name') }}</b><small>{{ $branch->tr('address') }}</small></span>
                        @if($branch->map_url)<a href="{{ $branch->map_url }}" target="_blank" rel="noopener">{{ __('Xarita') }}</a>@endif
                    </div>
                @endif
            </div>
            <div class="f-cols">
                <div class="f-col">
                    <h4>{{ __('Kurslar') }}</h4>
                    <ul>
                        @foreach(\App\Models\Course::active()->get(['slug', 'title']) as $c)
                            <li><a href="{{ lroute('course', ['course' => $c->slug]) }}">{{ $c->tr('title') }}</a></li>
                        @endforeach
                        <li><a href="{{ lroute('courses') }}#formats">{{ __('Kurs narxlari') }}</a></li>
                    </ul>
                </div>
                <div class="f-col">
                    <h4>{{ __('Buxgalteriya xizmati') }}</h4>
                    <ul>
                        <li><a href="{{ lroute('services') }}#plans">{{ __('YaTT uchun') }}</a></li>
                        <li><a href="{{ lroute('services') }}#plans">{{ __('MChJ uchun') }}</a></li>
                        <li><a href="{{ lroute('services') }}">{{ __('Hisobni tiklash') }}</a></li>
                        <li><a href="{{ lroute('services') }}#plans">{{ __('Xizmat narxlari') }}</a></li>
                    </ul>
                </div>
                <div class="f-col">
                    <h4>{{ __('Kompaniya') }}</h4>
                    <ul>
                        <li><a href="{{ lroute('courses') }}#teachers">{{ __('Ustozlar') }}</a></li>
                        <li><a href="{{ lroute('blog') }}">{{ __('Maqolalar') }}</a></li>
                        <li><a href="{{ lroute('home') }}#faq">{{ __('Ko‘p so‘raladigan savollar') }}</a></li>
                        <li><a href="{{ lroute('contact') }}">{{ __('Aloqa') }}</a></li>
                    </ul>
                </div>
                <div class="f-col">
                    <h4>{{ __('Ijtimoiy tarmoqlar') }}</h4>
                    <ul>
                        <li><a href="{{ $instagram }}" target="_blank" rel="noopener">Instagram</a></li>
                        <li><a href="{{ $telegram }}" target="_blank" rel="noopener">Telegram</a></li>
                        @if($youtube)<li><a href="{{ $youtube }}" target="_blank" rel="noopener">YouTube</a></li>@endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="f-divider"></div>

        <div class="f-bottom">
            <p class="c">© {{ date('Y') }} Buxgalter MChJ. {{ __('Barcha huquqlar himoyalangan.') }}</p>
            <a href="{{ lroute('privacy') }}">{{ __('Maxfiylik siyosati') }}</a>
            <a href="{{ lroute('offer') }}">{{ __('Ommaviy oferta') }}</a>
            <a href="#top" class="glass-btn" aria-label="{{ __('Yuqoriga') }}" data-to-top>{{ icon('arrow-up') }}</a>
        </div>
    </div>
    <div class="wordmark" aria-hidden="true"><span>Buxgalter</span></div>
</footer>
