{{-- Figma: Why us (bento) --}}
<section class="section section--soft" id="why">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">{{ hs('why.title', 'Nega aynan Buxgalter') }}</h2>
            <p>{{ hs('why.lead', 'O‘quvchilarimiz kursni nazariya bilan emas, real ish ko‘nikmasi bilan tugatadi.') }}</p>
        </div>
        <div class="bento">
            <div class="bento-row1">
                <div class="bento-photo">
                    <img src="{{ media_url(setting('texts.why.image') ?: 'img/why.jpg') }}" alt="" loading="lazy">
                    <div class="bento-caption">
                        <span class="icon-tile icon-tile--52">{{ icon('location') }}</span>
                        <span><span class="fc-title" style="display:block">{{ hs('why.place_title', 'Toshkentda') }}</span><span class="fc-sub">{{ hs('why.place_sub', 'Oflayn guruhlar va onlayn format') }}</span></span>
                    </div>
                </div>
                <div class="tile tile--accent">
                    <span class="icon-tile icon-tile--52 icon-tile--light">{{ icon('document-text') }}</span>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <p class="big">{{ hs('why.big', '1 yil') }}</p>
                        <h3>{{ hs('why.big_title', 'Real korxonada amaliyot') }}</h3>
                        <p>{{ hs('why.big_text', 'Kurs davomida korxonaning bir yillik buxgalteriyasini tiklaysiz.') }}</p>
                    </div>
                </div>
                <div class="tile-stack">
                    <div class="tile">
                        <span class="icon-tile icon-tile--52">{{ icon('task-square') }}</span>
                        <div><h3>{{ hs('why.t1_title', 'Amaliyot birinchi oydan') }}</h3><p>{{ hs('why.t1_text', 'Darslar real hujjatlar va vazifalar asosida o‘tadi.') }}</p></div>
                    </div>
                    <div class="tile">
                        <span class="icon-tile icon-tile--52">{{ icon('teacher') }}</span>
                        <div><h3>{{ hs('why.t2_title', 'Ustoz nazorati') }}</h3><p>{{ hs('why.t2_text', 'Har bir topshiriq tekshiriladi va izoh beriladi.') }}</p></div>
                    </div>
                </div>
            </div>
            <div class="bento-row2">
                <div class="tile">
                    <span class="icon-tile icon-tile--52">{{ icon('monitor') }}</span>
                    <div><h3>{{ hs('why.t3_title', '1C bepul o‘rnatiladi') }}</h3><p>{{ hs('why.t3_text', 'Dasturni noutbukingizga o‘zimiz o‘rnatib beramiz.') }}</p></div>
                </div>
                <div class="tile">
                    <span class="icon-tile icon-tile--52 icon-tile--green">{{ icon('award') }}</span>
                    <div><h3>{{ hs('why.t4_title', 'Diplom va sertifikat') }}</h3><p>{{ hs('why.t4_text', 'Kurs yakunida imtihon topshirasiz va hujjat olasiz.') }}</p></div>
                </div>
                <div class="tile tile--dark">
                    <div class="col">
                        <span class="icon-tile icon-tile--52 icon-tile--blue">{{ icon('bank') }}</span>
                        <h3>{{ hs('why.t5_title', 'my.soliq.uz va internet-banking') }}</h3>
                        <p>{{ hs('why.t5_text', 'Soliq sayti va bank tizimida hisobot topshirish hamda to‘lov qilishni amalda o‘rganasiz.') }}</p>
                    </div>
                    <div class="dark-chips">
                        @foreach(hs_list('why.t5_chips', ['my.soliq.uz', 'Internet-banking', '1C:Buxgalteriya']) as $chip)
                            <span class="dark-chip"><i></i>{{ $chip }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
