{{-- Figma: Consultation --}}
<section class="section" id="consultation">
    <div class="container">
        <div class="consult reveal">
            <div class="left">
                <h2 class="h2">{{ hs('consult.title', 'Bepul konsultatsiyaga yoziling') }}</h2>
                <p>{{ hs('consult.lead', '15 daqiqalik suhbatda sizga mos kurs yoki xizmat tarifini tanlab beramiz.') }}</p>
                <div class="steps">
                    @foreach([
                        ['Ariza qoldirasiz', 'Ism va telefon raqamingiz kifoya.'],
                        ['Mutaxassis qo‘ng‘iroq qiladi', 'Bir ish kuni ichida.'],
                        ['Kurs yoki tarifni tanlaysiz', 'Guruhga yoziling yoki shartnoma tuzing.'],
                    ] as $n => [$t, $s])
                        <div class="step"><span class="n">{{ $n + 1 }}</span><span><b>{{ hs('consult.step'.($n + 1).'_title', $t) }}</b><small>{{ hs('consult.step'.($n + 1).'_text', $s) }}</small></span></div>
                    @endforeach
                </div>
            </div>
            @include('partials.lead-form', ['interest' => $interest ?? 'course'])
        </div>
    </div>
</section>
