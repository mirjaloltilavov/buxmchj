@extends('layouts.app')
@section('title', __('Buxgalteriya kurslari').' — Buxgalter MChJ')

@section('content')
    @component('partials.page-hero', ['title' => hs('courses_page.title', 'Buxgalteriya kurslari'), 'lead' => hs('courses_page.lead', 'Noldan boshlab buxgalteriya hisobi, 1C va soliq hisobotlarini o‘rganing. Toshkentda oflayn hamda onlayn formatda.'), 'crumbs' => [__('Kurslar') => null]])
        <div class="hero-ctas">
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--xl" data-consult>{{ __('Kursga yozilish') }} <span class="arrow">{{ icon('arrow-diag') }}</span></a>
            <a href="#formats" class="btn btn--white">{{ __('Kurslarni ko‘rish') }}</a>
        </div>
        <div class="stats">
            <div><b>{{ hs('courses_page.stat1', '10 yil') }}</b><small>{{ hs('courses_page.stat1_label', 'tajriba') }}</small></div>
            <div><b>{{ hs('courses_page.stat2', '36+') }}</b><small>{{ hs('courses_page.stat2_label', 'bitirgan guruh') }}</small></div>
            <div><b>{{ hs('courses_page.stat3', '3 ta') }}</b><small>{{ hs('courses_page.stat3_label', 'kurs formati') }}</small></div>
        </div>
    @endcomponent

    <section class="section" id="formats">
        <div class="container">
            <div class="section-head reveal">
                <h2 class="h2">{{ __('Kurs formatlari') }}</h2>
                <p>{{ __('Boshlang‘ich darajangiz va vaqtingizga mos formatni tanlang.') }}</p>
            </div>
            <div class="cards-grid">
                @foreach($courses as $course)
                    @include('partials.course-card', ['course' => $course])
                @endforeach
            </div>
        </div>
    </section>

    @include('sections.why')

    @if($teachers->isNotEmpty())
        <section class="section" id="teachers">
            <div class="container">
                <div class="section-head reveal">
                    <h2 class="h2">{{ __('Ustozlarimiz') }}</h2>
                    <p>{{ __('Darslarni amaliyotchi buxgalterlar olib boradi. Ular bugun ham korxonalar hisobini yuritadi.') }}</p>
                </div>
                <div class="teachers">
                    @foreach($teachers as $t)
                        <div class="teacher reveal">
                            @if($t->photo)<img src="{{ media_url($t->photo) }}" alt="{{ $t->tr('name') }}" loading="lazy">@endif
                            <div class="b"><b>{{ $t->tr('name') }}</b><small>{{ $t->tr('role') }}</small></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.faq')
    @include('sections.consultation', ['interest' => 'course'])
@endsection
