<section @class(['section', 'section--soft' => ($b['bg'] ?? '') === 'soft'])>
    <div class="container">
        @if(tr($b['title'] ?? null))@include('partials.section-head', ['title' => tr($b['title']), 'lead' => tr($b['lead'] ?? null), 'lg' => true])@endif
        <div class="features bcards" data-snap style="--n: {{ max(1, min(4, count($b['items'] ?? []))) }}">
            @foreach(($b['items'] ?? []) as $i => $it)
                <div class="feature" data-reveal="up" style="--d: {{ $i * 100 }}ms">
                    @if(! empty($it['icon']))<span class="icon-tile">{{ icon($it['icon']) }}</span>@endif
                    <div><h3>{{ tr($it['title'] ?? null) }}</h3>@if(tr($it['text'] ?? null))<p>{{ tr($it['text']) }}</p>@endif</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
