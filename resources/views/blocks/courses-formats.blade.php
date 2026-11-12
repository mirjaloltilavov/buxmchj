@php $courses = \App\Models\Course::active()->get(); @endphp
{{-- Kurs formatlari --}}
<section class="section" id="formats">
    <div class="container">
        @include('partials.section-head', ['title' => hs('courses_page.kurs_formatlari', 'Kurs formatlari'), 'lead' => hs('courses_page.boshlangich_darajangiz_va_vaqtingizga_mo', 'Boshlang‘ich darajangiz va vaqtingizga mos formatni tanlang.'), 'lg' => true])
        <div class="cards-3" data-stagger="80">
            @foreach($courses as $course)
                @include('partials.ccard', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>
