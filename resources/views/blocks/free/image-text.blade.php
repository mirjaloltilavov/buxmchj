<section @class(['section', 'section--soft' => ($b['bg'] ?? '') === 'soft'])>
    <div @class(['container', 'itxt', 'itxt--right' => ($b['side'] ?? 'left') === 'right'])>
        @if(! empty($b['image']))<div class="itxt__img" data-reveal="card"><img src="{{ media_url($b['image']) }}" alt="{{ tr($b['title'] ?? null) }}" loading="lazy"></div>@endif
        <div class="itxt__body" data-reveal>
            <h2 class="h2">{{ tr($b['title'] ?? null) }}</h2>
            @if(tr($b['text'] ?? null))<p>{!! nl2br(e(tr($b['text']))) !!}</p>@endif
            @include('blocks.free._btn', ['p' => 'btn', 'cls' => 'btn-pill--ink'])
        </div>
    </div>
</section>
