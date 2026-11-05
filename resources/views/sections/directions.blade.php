{{-- Figma: Directions --}}
<section class="section" id="directions">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">{{ hs('directions.title', 'Kurslar va buxgalteriya xizmati') }}</h2>
            <p>{{ hs('directions.lead', 'Buxgalter bo‘lmoqchi bo‘lganlar uchun o‘quv kurslari, tadbirkorlar uchun esa buxgalteriyani to‘liq yuritish xizmati.') }}</p>
        </div>
        <div class="directions">
            <article class="direction direction--courses">
                <div class="direction-img"><img src="{{ media_url(setting('texts.directions.courses_image') ?: 'img/dir-courses.jpg') }}" alt="" loading="lazy"></div>
                <div class="direction-body">
                    <h3>{{ hs('directions.courses_title', 'Buxgalteriya kurslari') }}</h3>
                    <p>{{ hs('directions.courses_text', 'Noldan boshlab buxgalteriya hisobi, 1C va soliq hisobotlarini o‘rganasiz. Amaliy mashg‘ulotlar birinchi oydan boshlanadi.') }}</p>
                    <ul class="checks">
                        @foreach(hs_list('directions.courses_checks', ['Buxgalteriya, 1C va soliq bir dasturda', 'Real korxonada amaliyot', 'Diplom va sertifikat']) as $item)
                            <li>{{ icon('tick-circle') }} {{ $item }}</li>
                        @endforeach
                    </ul>
                    <div class="direction-foot">
                        <div><p class="price-label">{{ __('Narxi') }}</p><p class="price-value">{{ hs('directions.courses_price', '1 512 000 so‘mdan') }}</p></div>
                        <a href="{{ lroute('courses') }}" class="btn btn--acc">{{ __('Kurslar haqida') }} {{ icon('arrow-right') }}</a>
                    </div>
                </div>
            </article>
            <article class="direction direction--services">
                <div class="direction-img"><img src="{{ media_url(setting('texts.directions.services_image') ?: 'img/dir-services.jpg') }}" alt="" loading="lazy"></div>
                <div class="direction-body">
                    <h3>{{ hs('directions.services_title', 'Buxgalteriya xizmati') }}</h3>
                    <p>{{ hs('directions.services_text', 'YaTT va MChJ buxgalteriyasini to‘liq yuritamiz: hisobotlar, soliqlar, kadrlar va bank operatsiyalari.') }}</p>
                    <ul class="checks">
                        @foreach(hs_list('directions.services_checks', ['Biriktirilgan bosh buxgalter', 'Hisobotlar o‘z vaqtida topshiriladi', 'Shartnoma asosida javobgarlik']) as $item)
                            <li>{{ icon('tick-circle') }} {{ $item }}</li>
                        @endforeach
                    </ul>
                    <div class="direction-foot">
                        <div><p class="price-label">{{ __('Oylik narx') }}</p><p class="price-value">{{ hs('directions.services_price', '1 500 000 so‘mdan') }}</p></div>
                        <a href="{{ lroute('services') }}" class="btn btn--blue">{{ __('Xizmat haqida') }} {{ icon('arrow-right') }}</a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>
