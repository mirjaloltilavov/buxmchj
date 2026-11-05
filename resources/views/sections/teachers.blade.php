{{-- Figma: Teachers --}}
@if(($teachers ?? collect())->isNotEmpty())
<section @class(['section', 'section--soft' => $soft ?? false]) id="teachers">
    <div class="container">
        @include('partials.section-head', ['title' => __('Ustozlarimiz'), 'lead' => __('Darslarni amaliyotchi buxgalterlar olib boradi. Ular bugun ham korxonalar hisobini yuritadi.')])
        <div class="teachers" data-snap data-stagger="80">
            @foreach($teachers as $t)
                <div class="teacher" data-reveal="up40">
                    <div class="zoom">@if($t->photo)<img src="{{ media_url($t->photo) }}" alt="{{ $t->tr('name') }}" loading="lazy">@endif</div>
                    <div class="info"><b>{{ $t->tr('name') }}</b><span>{{ icon('teacher') }} {{ $t->tr('role') }}</span></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
