<section class="section">
    <div class="container">
        <div class="bcta" data-reveal="card">
            <div class="bcta__t">
                <h2>{{ tr($b['title'] ?? null) }}</h2>
                @if(tr($b['text'] ?? null))<p>{{ tr($b['text']) }}</p>@endif
            </div>
            @include('blocks.free._btn', ['p' => 'btn', 'cls' => 'btn-pill--ink'])
        </div>
    </div>
</section>
