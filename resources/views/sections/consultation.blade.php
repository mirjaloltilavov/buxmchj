{{-- Figma: Consultation --}}
<section class="section" id="consultation">
    <div class="container">
        <div class="consult">
            <div class="left">
                <h2 class="h2">{{ hs('consult.title', 'Bepul konsultatsiyaga yoziling') }}</h2>
                <p>{{ hs('consult.lead', '15 daqiqalik suhbatda sizga mos kurs yoki xizmat tarifini tanlab beramiz.') }}</p>
                <div class="steps">
                    <div class="step"><span class="n">1</span><span><b>{{ hs('consult.step1_title', 'Ariza qoldirasiz') }}</b><small>{{ hs('consult.step1_text', 'Ism va telefon raqamingiz kifoya.') }}</small></span></div>
                    <div class="step"><span class="n">2</span><span><b>{{ hs('consult.step2_title', 'Mutaxassis qo‘ng‘iroq qiladi') }}</b><small>{{ hs('consult.step2_text', 'Bir ish kuni ichida.') }}</small></span></div>
                    <div class="step"><span class="n">3</span><span><b>{{ hs('consult.step3_title', 'Kurs yoki tarifni tanlaysiz') }}</b><small>{{ hs('consult.step3_text', 'Guruhga yoziling yoki shartnoma tuzing.') }}</small></span></div>
                </div>
            </div>
            @include('partials.lead-form', ['interest' => $interest ?? 'course'])
        </div>
    </div>
</section>
