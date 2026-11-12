@php $headTitle = tr($b['title'] ?? null) ?: ($pageTitle ?? ''); @endphp
@component('partials.p-hero', [
    'title' => $headTitle,
    'lead' => tr($b['lead'] ?? null),
    'crumbs' => [$headTitle => null],
    'image' => $b['image'] ?? null,
])
    @if(tr($b['btn1_label'] ?? null) || tr($b['btn2_label'] ?? null))
        <div class="hero-ctas">
            @include('blocks.free._btn', ['p' => 'btn1', 'cls' => 'btn-pill--ink'])
            @include('blocks.free._btn', ['p' => 'btn2', 'cls' => 'btn-pill--white'])
        </div>
    @endif
@endcomponent
