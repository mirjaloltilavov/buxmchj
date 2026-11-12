{{-- Figma: Graduates — video kartalar karuseli: drag + snap, ekran chetigacha --}}
@php $graduates = \App\Models\Graduate::active()->get(); @endphp
@if($graduates->isNotEmpty())
<section class="section" id="graduates" data-carousel>
    <div class="container">
        <div class="grads-head">
            <h2 class="h2" data-reveal>{{ hs('graduates.bitiruvchilar_fikri', 'Bitiruvchilar fikri') }}</h2>
            <p data-reveal style="--d:120ms">{{ hs('graduates.kursni_tugatganlar_qayerda_ishlayotgani', 'Kursni tugatganlar qayerda ishlayotgani va kurs ularga nima berganini o‘zlari aytib beradi.') }}</p>
            <div class="car-arrows" data-reveal style="--d:240ms">
                <button type="button" class="prev" aria-label="{{ __('Oldingi') }}" data-car-prev>{{ icon('arrow-left') }}</button>
                <button type="button" class="next" aria-label="{{ __('Keyingi') }}" data-car-next>{{ icon('arrow-right') }}</button>
            </div>
        </div>
    </div>
    <div class="grads-track" data-track data-stagger="80">
        @foreach($graduates as $g)
            <div @class(['grad', 'grad--video' => $g->video_url]) data-reveal="up40">
                <a href="{{ $g->video_url ?: '#' }}" class="zoom" @if($g->video_url) data-video="{{ $g->video_url }}" @else onclick="return false" @endif>
                    @if($g->photo)<img src="{{ media_url($g->photo) }}" alt="{{ $g->tr('name') }}" loading="lazy" draggable="false">@endif
                    @if($g->video_url)<span class="play" aria-hidden="true">{{ icon('play') }}</span>@endif
                </a>
                <b>{{ $g->tr('name') }}</b>
                <span>{{ icon('briefcase') }} {{ $g->tr('subtitle') }}</span>
            </div>
        @endforeach
    </div>
</section>
@endif
