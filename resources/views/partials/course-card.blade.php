@php
    $priceText = $course->price ? price_from($course->price) : ($course->tr('price_note') ?: __('Narxi kurs sahifasida'));
    $url = lroute('course', ['course' => $course->slug]);
@endphp
<article @class(['course-card', 'course-card--row' => ($row ?? false)])>
    <a href="{{ $url }}" class="img"><img src="{{ media_url($course->image) }}" alt="{{ $course->tr('title') }}" loading="lazy"></a>
    <div class="body">
        <div style="display:flex;flex-direction:column;gap:12px">
            @if($course->tags)
                <div class="tags">
                    @foreach($course->tags as $tag)
                        <span @class(['tag', 'tag--acc' => ! empty($tag['accent'])])>@if(! empty($tag['icon'])){{ icon($tag['icon']) }}@endif {{ tr($tag['text'] ?? null) }}</span>
                    @endforeach
                </div>
            @endif
            <h3><a href="{{ $url }}">{{ $course->tr('title') }}</a></h3>
            <p class="desc">{{ $course->tr('excerpt') }}</p>
        </div>
        <div class="card-foot">
            <p class="p">{{ ($row ?? false) && ! $course->is_featured ? ($course->tr('price_note') ?: $priceText) : $priceText }}</p>
            <a href="{{ $url }}" @class(['btn', 'btn--acc' => ! ($row ?? false), 'btn--ghost' => ($row ?? false)])>{{ __('Batafsil') }} {{ icon('arrow-right') }}</a>
        </div>
    </div>
</article>
