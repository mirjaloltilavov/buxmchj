{{-- Sahifa konstruktori: $items — [['type' => ..., 'data' => [...]], ...], $ctx — sahifa kaliti --}}
@foreach($items as $item)
    @php
        $def = \App\Support\Blocks::get($item['type'] ?? '');
        $b = $item['data'] ?? [];
    @endphp
    @continue(! $def || ! empty($b['hidden']))
    @if(filled($b['anchor'] ?? null))<span id="{{ $b['anchor'] }}" class="block-anchor" aria-hidden="true"></span>@endif
    @include($def[3], ['b' => $b, 'ctx' => $ctx ?? 'page', 'pageTitle' => $pageTitle ?? null])
@endforeach
