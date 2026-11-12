@component('partials.p-hero', [
    'title' => hs('courses_page.title', 'Buxgalteriya kurslari'),
    'lead' => hs('courses_page.lead', 'Noldan boshlab buxgalteriya hisobi, 1C va soliq hisobotlarini o‘rganing. Toshkentda oflayn hamda onlayn formatda.'),
    'crumbs' => [__('Kurslar') => null],
    'image' => hs_img('courses_page.image', 'img/courses-hero.jpg'),
])
    <div class="hero-ctas">
        <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult><span class="label">{{ hs('courses_page.kursga_yozilish', 'Kursga yozilish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        <a href="#formats" class="btn-pill btn-pill--white btn-pill--l"><span class="label">{{ hs('courses_page.kurslarni_korish', 'Kurslarni ko‘rish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
    </div>
    <div class="stats">
        <div><b>{{ hs('courses_page.stat1', '10 yil') }}</b><span>{{ icon('calendar') }} {{ hs('courses_page.stat1_label', 'tajriba') }}</span></div>
        <div><b>{{ hs('courses_page.stat2', '36+') }}</b><span>{{ icon('people') }} {{ hs('courses_page.stat2_label', 'bitirgan guruh') }}</span></div>
        <div><b>{{ hs('courses_page.stat3', '3 ta') }}</b><span>{{ icon('book') }} {{ hs('courses_page.stat3_label', 'kurs formati') }}</span></div>
    </div>
    @slot('cards')
        <div class="info-card info-card--dark" style="right:-20px;top:32px">
            <span class="t"><small>{{ hs('courses_page.toshkent_onlayn', 'Toshkent · onlayn') }}</small><b>{{ hs('courses_page.yangi_guruhga_qabul_ochiq', 'Yangi guruhga qabul ochiq') }}</b></span>
            <span class="round">{{ icon('arrow-right') }}</span>
        </div>
        <div class="info-card info-card--tl" style="left:-40px;top:376px">
            <span class="icon-tile">{{ icon('monitor') }}</span>
            <span class="t"><b>{{ hs('courses_page.1c_dasturi_bepul', '1C dasturi bepul') }}</b><small>{{ hs('courses_page.noutbukingizga_ornatiladi', 'noutbukingizga o‘rnatiladi') }}</small></span>
        </div>
    @endslot
@endcomponent
