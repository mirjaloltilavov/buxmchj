<section @class(['section', 'section--soft' => ($b['bg'] ?? '') === 'soft'])>
    <div class="container">
        @if(tr($b['title'] ?? null))<h2 class="h2 bstats__title" data-reveal>{{ tr($b['title']) }}</h2>@endif
        <div class="bstats" data-stagger="80">
            @foreach(($b['items'] ?? []) as $it)
                <div class="bstats__i" data-reveal="up"><b>{{ tr($it['value'] ?? null) }}</b>@if(tr($it['label'] ?? null))<span>{{ tr($it['label']) }}</span>@endif</div>
            @endforeach
        </div>
    </div>
</section>
