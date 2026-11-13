<x-filament-widgets::widget>
    <x-filament::section :icon="$icon" icon-color="warning">
        <x-slot name="heading">{{ $heading }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
        @if(empty($rows))
            <p class="bx-rank__empty">Bu davrda ma’lumot yo‘q</p>
        @else
            <div class="bx-rank">
                <div class="bx-rank__head">
                    <span>Nomi</span>
                    @foreach($cols as $c)<span class="bx-rank__x">{{ $c }}</span>@endforeach
                    <span class="bx-rank__v">{{ $valueLabel }}</span>
                </div>
                @foreach($rows as $r)
                    <div class="bx-rank__row" title="{{ $r['hint'] ?? $r['label'] }}">
                        <div class="bx-rank__name">
                            <i class="bx-rank__bar" style="width: {{ max(2, $r['bar']) }}%"></i>
                            @if(! empty($r['icon']))<span class="bx-rank__ic">{{ $r['icon'] }}</span>@endif
                            <span class="bx-rank__l">{{ $r['label'] }}
                                @if(! empty($r['hint']))
                                    <small>{{ $r['hint'] }}</small>
                                @endif
                            </span>
                        </div>
                        @foreach(($r['extra'] ?? []) as $x)<span class="bx-rank__x">{{ $x }}</span>@endforeach
                        <span class="bx-rank__v"><b>{{ number_format($r['value'], 0, '.', ' ') }}</b><em>{{ $r['share'] }}%</em></span>
                    </div>
                @endforeach
                @if($more)<p class="bx-rank__more">Yana {{ $more }} ta</p>@endif
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
