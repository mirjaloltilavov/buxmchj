<x-filament-panels::page>
    @if($hint)<p class="text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>@endif

    @foreach($groups as $group => $blocks)
        <section class="bx-set">
            <h2 class="bx-set__group">{{ $group }}</h2>
            <div class="bx-set__grid">
                @foreach($blocks as $b)
                    <article class="bx-set__card" wire:key="blk-{{ $b['key'] }}">
                        <header class="bx-set__head">
                            <span class="bx-set__ico"><x-filament::icon :icon="$b['icon']" class="h-5 w-5" /></span>
                            <div class="bx-set__ttl">
                                <b>{{ $b['title'] }}</b>
                                @if($b['description'])<small>{{ $b['description'] }}</small>@endif
                            </div>
                            @if($b['toggle'])
                                <button type="button" wire:click="toggleBlock('{{ $b['key'] }}')" class="bx-sw {{ $b['toggle']['on'] ? 'is-on' : '' }}" role="switch" aria-checked="{{ $b['toggle']['on'] ? 'true' : 'false' }}" title="{{ $b['toggle']['on'] ? 'O‘chirish' : 'Yoqish' }}">
                                    <i></i><span>{{ $b['toggle']['on'] ? 'Yoqilgan' : 'O‘chirilgan' }}</span>
                                </button>
                            @endif
                            <x-filament::button size="sm" color="gray" icon="heroicon-m-pencil-square" wire:click="mountAction('editBlock', { key: '{{ $b['key'] }}' })">Tahrirlash</x-filament::button>
                        </header>
                        <dl class="bx-set__rows {{ $b['toggle'] && ! $b['toggle']['on'] ? 'is-off' : '' }}">
                            @foreach($b['rows'] as $r)
                                <div class="bx-set__row {{ $r['wide'] ? 'is-wide' : '' }}">
                                    <dt>{{ $r['label'] }}</dt>
                                    <dd>
                                        @php $v = $r['value']; @endphp
                                        @switch($r['kind'])
                                            @case('image')
                                                @if(filled($v) && is_string($v))<img src="{{ media_url($v) }}" alt="" class="bx-set__img">@else<span class="bx-set__empty">Rasm yo‘q</span>@endif
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
                                                @php $items = collect(is_array($v) ? $v : [])->map(fn ($i) => is_array($i) ? ($i['text']['uz'] ?? $i['title']['uz'] ?? $i['name']['uz'] ?? null) : $i)->filter()->values(); @endphp
                                                @if($items->isNotEmpty())
                                                    <span class="bx-set__chips">@foreach($items as $it)<span class="bx-pill">{{ $it }}</span>@endforeach</span>
                                                @else<span class="bx-set__empty">Kiritilmagan</span>@endif
                                                @break
                                            @default
                                                @if(filled($v) && ! is_array($v))<span class="bx-set__txt">{{ \Illuminate\Support\Str::limit((string) $v, 260) }}</span>@else<span class="bx-set__empty">{{ $emptyLabel }}</span>@endif
                                        @endswitch
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
</x-filament-panels::page>
