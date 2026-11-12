<section @class(['section', 'section--soft' => ($b['bg'] ?? '') === 'soft'])>
    <div class="container">
        @if(tr($b['title'] ?? null))@include('partials.section-head', ['title' => tr($b['title']), 'lead' => tr($b['lead'] ?? null), 'lg' => true])@endif
        <ol class="bsteps">
            @foreach(($b['items'] ?? []) as $i => $it)
                <li class="bsteps__i" data-reveal="up" style="--d: {{ $i * 100 }}ms">
                    <span class="bsteps__n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div><h3>{{ tr($it['title'] ?? null) }}</h3>@if(tr($it['text'] ?? null))<p>{{ tr($it['text']) }}</p>@endif</div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
