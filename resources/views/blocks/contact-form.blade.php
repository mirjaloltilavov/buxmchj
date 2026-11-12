{{-- Figma: Aloqa — Ariza --}}
<section class="section c-form" id="form">
    <div class="container c-form__grid">
        <div class="c-form__left" data-stagger="80">
            <div class="c-form__head" data-reveal>
                <h2 class="h2 h2--56">{{ hs('contact.form_title', 'Savolingizni qoldiring — o‘zimiz qo‘ng‘iroq qilamiz') }}</h2>
                <p>{{ hs('contact.form_lead', 'Formani to‘ldiring: mutaxassisimiz siz bilan bog‘lanib, mos kurs yoki xizmat turini tanlashga yordam beradi.') }}</p>
            </div>
            <div class="c-points">
                @foreach([
                    ['Tez javob', 'Ish vaqtida 15 daqiqa ichida qayta qo‘ng‘iroq qilamiz.', 'clock', ''],
                    ['Aniq maslahat', 'Kurs, tarif yoki xizmat narxi bo‘yicha savollarga javob beramiz.', 'message-question', 'icon-tile--blue2'],
                    ['Majburiyatsiz', 'Konsultatsiya bepul, hech narsaga majbur qilmaydi.', 'tick-circle', 'icon-tile--green'],
                ] as $n => [$t, $d, $i, $cls])
                    <div class="c-point" data-reveal>
                        <span class="icon-tile {{ $cls }}">{{ icon($i) }}</span>
                        <div><b>{{ hs('contact.point'.($n + 1).'_title', $t) }}</b><p>{{ hs('contact.point'.($n + 1).'_text', $d) }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
        <div data-reveal="card" style="--d:150ms">
            @include('partials.lead-form', ['variant' => 'contact'])
        </div>
    </div>
</section>
