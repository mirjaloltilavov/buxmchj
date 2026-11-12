{{-- O'qish qanday boshlanadi --}}
<section class="section section--soft" id="start">
    <div class="container">
        @include('partials.section-head', ['title' => hs('courses_page.oqish_qanday_boshlanadi', 'O‘qish qanday boshlanadi'), 'lead' => hs('courses_page.ariza_qoldirganingizdan_keyin_mutaxassis', 'Ariza qoldirganingizdan keyin mutaxassis sizga mos tarif va formatni tanlashga yordam beradi.'), 'lg' => true])
        <div class="features" data-snap>
            @foreach([
                ['document-text', '1. Ariza qoldirasiz', 'Ism va telefon raqamingiz kifoya.'],
                ['call', '2. Mutaxassis qo‘ng‘iroq qiladi', 'Bir ish kuni ichida bog‘lanamiz.'],
                ['calendar', '3. Tarifni tanlaysiz', 'Guruhga yozilasiz yoki platformaga kirasiz.'],
                ['award', '4. O‘qish va amaliyot', 'Darslar, topshiriqlar, imtihon va hujjat.'],
            ] as $i => [$ic, $t, $d])
                <div class="feature" data-reveal="up" style="--d: {{ $i * 120 }}ms">
                    <span class="icon-tile" data-reveal="pop" style="--d: {{ 100 + $i * 120 }}ms">{{ icon($ic) }}</span>
                    <div><h3>{{ __($t) }}</h3><p>{{ __($d) }}</p></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
