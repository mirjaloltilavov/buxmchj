{{-- Bitta qiymatni turiga qarab ko'rsatish. $kind, $v, $emptyLabel --}}
@switch($kind)
    @case('image')
        @if(filled($v) && is_string($v))<img src="{{ media_url($v) }}" alt="" class="bx-set__img">@else<span class="bx-set__empty">Rasm yo‘q</span>@endif
        @break
    @case('files')
        @php $files = collect(is_array($v) ? $v : [])->filter()->map(fn ($f) => basename((string) $f))->values(); @endphp
        @if($files->isNotEmpty())
            <span class="bx-set__chips">@foreach($files as $fn)<a class="bx-pill is-yes" href="{{ url($fn) }}" target="_blank" rel="noopener">{{ $fn }}</a>@endforeach</span>
        @else<span class="bx-set__empty">Fayl yuklanmagan</span>@endif
        @break
    @case('bool')
        <span class="bx-pill {{ $v ? 'is-yes' : '' }}">{{ $v ? 'Ha' : 'Yo‘q' }}</span>
        @break
    @case('secret')
        @if(filled($v))<span class="bx-pill is-yes">Kiritilgan ••••••</span>@else<span class="bx-set__empty">Kiritilmagan</span>@endif
        @break
    @case('date')
        @if(filled($v)){{ \Illuminate\Support\Carbon::parse($v)->format('d.m.Y') }}@else<span class="bx-set__empty">—</span>@endif
        @break
    @case('html')
        @if(filled($v)){{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $v)), 220) }}@else<span class="bx-set__empty">Kiritilmagan</span>@endif
        @break
    @case('list')
        @php $items = collect(is_array($v) ? $v : [])->map(fn ($i) => is_array($i) ? ($i['text']['uz'] ?? $i['title']['uz'] ?? $i['name']['uz'] ?? (is_string($i['text'] ?? null) ? $i['text'] : null)) : $i)->filter()->values(); @endphp
        @if($items->isNotEmpty())
            <span class="bx-set__chips">@foreach($items as $it)<span class="bx-pill">{{ $it }}</span>@endforeach</span>
        @else<span class="bx-set__empty">Kiritilmagan</span>@endif
        @break
    @default
        @if(filled($v) && ! is_array($v))<span class="bx-set__txt">{{ \Illuminate\Support\Str::limit((string) $v, 260) }}</span>@else<span class="bx-set__empty">{{ $emptyLabel }}</span>@endif
@endswitch
