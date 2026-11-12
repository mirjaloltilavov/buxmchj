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
        @if(is_array($v) && collect($v)->contains(fn ($i) => is_array($i) && array_key_exists('type', $i)))
            {{-- Sahifa tuzilishi: seksiyalar tartib bilan --}}
            <ol class="bx-layout">
                @foreach(array_values($v) as $n => $it)
                    @php $t = tr($it['data']['title'] ?? null); @endphp
                    <li @class(['is-hidden' => ! empty($it['data']['hidden'])])><i>{{ $n + 1 }}</i>{{ \App\Support\Blocks::label($it['type'] ?? '') }}@if($t)<em>— {{ \Illuminate\Support\Str::limit($t, 40) }}</em>@endif @if(! empty($it['data']['hidden']))<small>yashirilgan</small>@endif</li>
                @endforeach
            </ol>
            @break
        @endif
        @if(is_array($v) && collect($v)->contains(fn ($i) => is_array($i) && array_key_exists('links', $i)))
            {{-- Ustunli menyu: har ustun — sarlavha va havolalari --}}
            <div class="bx-cols">
                @foreach($v as $col)
                    <div class="bx-cols__c">
                        <b>{{ $col['title']['uz'] ?? '—' }}</b>
                        <span class="bx-set__chips">
                            @if(($col['auto'] ?? null) === 'courses')<span class="bx-pill is-yes">+ barcha kurslar</span>@endif
                            @foreach(($col['links'] ?? []) as $l)<span class="bx-pill">{{ $l['label']['uz'] ?? '—' }}</span>@endforeach
                            @if(($col['auto'] ?? null) === 'socials')<span class="bx-pill is-yes">+ ijtimoiy tarmoqlar</span>@endif
                        </span>
                    </div>
                @endforeach
            </div>
            @break
        @endif
        @php $items = collect(is_array($v) ? $v : [])->map(fn ($i) => is_array($i) ? ($i['text']['uz'] ?? $i['label']['uz'] ?? $i['title']['uz'] ?? $i['name']['uz'] ?? (is_string($i['text'] ?? null) ? $i['text'] : null)) : $i)->filter()->values(); @endphp
        @if($items->isNotEmpty())
            <span class="bx-set__chips">@foreach($items as $it)<span class="bx-pill">{{ $it }}</span>@endforeach</span>
        @else<span class="bx-set__empty">Kiritilmagan</span>@endif
        @break
    @default
        @if(filled($v) && ! is_array($v))<span class="bx-set__txt">{{ \Illuminate\Support\Str::limit((string) $v, 260) }}</span>@else<span class="bx-set__empty">{{ $emptyLabel }}</span>@endif
@endswitch
