@extends('layouts.app')
@section('popup', 'course')
@section('title', hs('courses_page.title', 'Buxgalteriya kurslari').' — Buxgalter MChJ')
@section('description', hs('courses_page.lead', 'Noldan boshlab buxgalteriya hisobi, 1C va soliq hisobotlarini o‘rganing. Toshkentda oflayn hamda onlayn formatda.'))

@section('content')
    @component('partials.p-hero', [
        'title' => hs('courses_page.title', 'Buxgalteriya kurslari'),
        'lead' => hs('courses_page.lead', 'Noldan boshlab buxgalteriya hisobi, 1C va soliq hisobotlarini o‘rganing. Toshkentda oflayn hamda onlayn formatda.'),
        'crumbs' => [__('Kurslar') => null],
        'image' => setting('texts.courses_page.image') ?: 'img/courses-hero.jpg',
    ])
        <div class="hero-ctas">
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult><span class="label">{{ __('Kursga yozilish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            <a href="#formats" class="btn-pill btn-pill--white btn-pill--l"><span class="label">{{ __('Kurslarni ko‘rish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        </div>
        <div class="stats">
            <div><b>{{ hs('courses_page.stat1', '10 yil') }}</b><span>{{ icon('calendar') }} {{ hs('courses_page.stat1_label', 'tajriba') }}</span></div>
            <div><b>{{ hs('courses_page.stat2', '36+') }}</b><span>{{ icon('people') }} {{ hs('courses_page.stat2_label', 'bitirgan guruh') }}</span></div>
            <div><b>{{ hs('courses_page.stat3', '3 ta') }}</b><span>{{ icon('book') }} {{ hs('courses_page.stat3_label', 'kurs formati') }}</span></div>
        </div>
        @slot('cards')
            <div class="info-card info-card--dark" style="right:-20px;top:32px">
                <span class="t"><small>{{ __('Toshkent · onlayn') }}</small><b>{{ __('Yangi guruhga qabul ochiq') }}</b></span>
                <span class="round">{{ icon('arrow-right') }}</span>
            </div>
            <div class="info-card info-card--tl" style="left:-40px;top:376px">
                <span class="icon-tile">{{ icon('monitor') }}</span>
                <span class="t"><b>{{ __('1C dasturi bepul') }}</b><small>{{ __('noutbukingizga o‘rnatiladi') }}</small></span>
            </div>
        @endslot
    @endcomponent

    {{-- Kurs formatlari --}}
    <section class="section" id="formats">
        <div class="container">
            @include('partials.section-head', ['title' => __('Kurs formatlari'), 'lead' => __('Boshlang‘ich darajangiz va vaqtingizga mos formatni tanlang.'), 'lg' => true])
            <div class="cards-3" data-stagger="80">
                @foreach($courses as $course)
                    @include('partials.ccard', ['course' => $course])
                @endforeach
            </div>
        </div>
    </section>

    @include('sections.why')

    {{-- Kursdan keyingi yo'l --}}
    <section class="section" id="career">
        <div class="container">
            @include('partials.section-head', ['title' => __('Kursdan keyingi yo‘l'), 'lead' => __('Bitiruvchilarimiz yordamchi buxgalterdan boshlab, tajriba oshgani sari o‘z firmasini ochishgacha boradi.')])
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
            <p class="note">{{ __('Maosh miqdorlari bitiruvchilar tajribasiga asoslangan va ish joyiga qarab farq qiladi.') }}</p>
        </div>
    </section>

    @include('sections.teachers', ['soft' => true])
    @include('sections.graduates')

    {{-- O'qish qanday boshlanadi --}}
    <section class="section section--soft" id="start">
        <div class="container">
            @include('partials.section-head', ['title' => __('O‘qish qanday boshlanadi'), 'lead' => __('Ariza qoldirganingizdan keyin mutaxassis sizga mos tarif va formatni tanlashga yordam beradi.'), 'lg' => true])
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

    @include('sections.faq')
    @include('sections.consultation', ['interest' => 'course'])
@endsection
