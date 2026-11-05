{{-- Figma: Course card (Kurslar sahifasi) --}}
@php
    $url = lroute('course', ['course' => $course->slug]);
    $priceText = $course->price ? price_from($course->price) : ($course->tr('price_note') ?: __('Narxi so‘rov bo‘yicha'));
@endphp
<article class="ccard card-hover" data-reveal="up40">
    <a href="{{ $url }}" class="zoom"><img src="{{ media_url($course->image) }}" alt="{{ $course->tr('title') }}" loading="lazy"></a>
    <div class="ccard__body">
        <div class="ccard__top">
            @if($course->tags)
                <div class="tags">
                    @foreach($course->tags as $tag)
                        <span @class(['tag', 'tag--acc' => ! empty($tag['accent'])])>@if(! empty($tag['icon'])){{ icon($tag['icon']) }}@endif {{ tr($tag['text'] ?? null) }}</span>
                    @endforeach
                </div>
            @endif
            <div style="display:flex;flex-direction:column;gap:8px">
                <h3><a href="{{ $url }}">{{ $course->tr('title') }}</a></h3>
                <p>{{ $course->tr('excerpt') }}</p>
            </div>
        </div>
        <div class="ccard__foot">
            <b>{{ $priceText }}</b>
            <a href="{{ $url }}" class="btn-pill btn-pill--m btn-pill--outline"><span class="label">{{ __('Batafsil') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        </div>
    </div>
</article>
