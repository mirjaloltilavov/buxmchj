{{-- Figma: Courses overview --}}
@if($courses->isNotEmpty())
@php $first = $courses->firstWhere('is_featured', true) ?? $courses->first(); $rest = $courses->reject(fn ($c) => $c->is($first))->take(2); @endphp
<section class="section" id="courses">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">{{ hs('courses.title', 'Kurslarimiz') }}</h2>
            <p>{{ hs('courses.lead', 'O‘zingizga mos formatni tanlang. Batafsil dastur, jadval va narxlar Kurslar sahifasida.') }}</p>
        </div>
        <div class="courses-grid">
            @include('partials.course-card', ['course' => $first])
            @if($rest->isNotEmpty())
                <div class="course-stack">
                    @foreach($rest as $course)
                        @include('partials.course-card', ['course' => $course, 'row' => true])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif
