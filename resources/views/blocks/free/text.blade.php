<section @class(['section', 'section--soft' => ($b['bg'] ?? '') === 'soft'])>
    <div class="container btext">
        @if(tr($b['title'] ?? null))<h2 class="h2" data-reveal>{{ tr($b['title']) }}</h2>@endif
        <div class="prose" data-reveal>{!! tr($b['body'] ?? null) !!}</div>
    </div>
</section>
