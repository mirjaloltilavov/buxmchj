{{-- Kursdan keyingi yo'l --}}
<section class="section" id="career">
    <div class="container">
        @include('partials.section-head', ['title' => hs('courses_page.kursdan_keyingi_yol', 'Kursdan keyingi yo‘l'), 'lead' => hs('courses_page.bitiruvchilarimiz_yordamchi_buxgalterdan', 'Bitiruvchilarimiz yordamchi buxgalterdan boshlab, tajriba oshgani sari o‘z firmasini ochishgacha boradi.')])
        <div class="career" data-career data-reveal="card">
            <div class="career__line" aria-hidden="true"><i></i></div>
            @foreach([
                ['user', '1-bosqich', '250–350 $', 'Yordamchi buxgalter', 'Kursdan keyin boshlanadigan lavozim'],
                ['briefcase', '2-bosqich', '500–1000 $', 'Buxgalter', '2–3 yillik tajriba bilan'],
                ['building', '3-bosqich', '2000 $+', 'O‘z buxgalteriya firmangiz', 'Bir nechta korxonaga xizmat'],
            ] as $i => [$ic, $lbl, $sum, $role, $note])
                <div class="career__lvl" data-reveal="up" style="--d: {{ 200 + $i * 200 }}ms">
                    <span class="career__lbl"><i class="node"></i>{{ icon($ic) }} {{ __($lbl) }}</span>
                    <b>{{ hs('career.l'.($i + 1).'_sum', $sum) }}</b>
                    <strong>{{ hs('career.l'.($i + 1).'_role', $role) }}</strong>
                    <small>{{ hs('career.l'.($i + 1).'_note', $note) }}</small>
                </div>
            @endforeach
        </div>
        <p class="note">{{ hs('courses_page.maosh_miqdorlari_bitiruvchilar_tajribasi', 'Maosh miqdorlari bitiruvchilar tajribasiga asoslangan va ish joyiga qarab farq qiladi.') }}</p>
    </div>
</section>
