{{-- Tugma: $b — blok ma'lumoti, $p — prefiks (btn, btn1…), $cls — uslub --}}
@php
    $lbl = tr($b[$p.'_label'] ?? null);
    $url = trim((string) ($b[$p.'_url'] ?? ''));
    $isConsult = $url === '' || $url === '#consultation';
@endphp
@if($lbl)
    <a href="{{ $isConsult ? '#consultation' : $url }}" class="btn-pill btn-pill--l {{ $cls ?? 'btn-pill--ink' }}" @if($isConsult) data-consult @endif @if(str_starts_with($url, 'http')) target="_blank" rel="noopener" @endif><span class="label">{{ $lbl }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
@endif
