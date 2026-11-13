<x-filament-widgets::widget>
    <x-filament::section wire:poll.15s>
        <div class="bx-live">
            <div class="bx-live__nums">
                <div class="bx-live__big"><span class="bx-live__dot"></span><b>{{ $count }}</b><span>hozir saytda</span></div>
                <div class="bx-live__sm"><b>{{ $last30 }}</b><span>oxirgi 30 daqiqada</span></div>
                <div class="bx-live__sm"><b>{{ $todayPeople }}</b><span>bugun odam</span></div>
                <div class="bx-live__sm"><b>{{ $todayViews }}</b><span>bugun sahifa ko‘rish</span></div>
            </div>
            @if($live->isNotEmpty())
                <div class="bx-live__list">
                    @foreach($live as $v)
                        @php $cur = $v->views->first()?->path ?? $v->exit_path; @endphp
                        <div class="bx-live__row">
                            <span class="bx-live__flag" title="{{ \App\Support\Analytics\Stats::country($v->country) }}">{{ \App\Support\Analytics\Stats::flag($v->country) }}</span>
                            <span class="bx-live__page"><b>{{ \App\Support\Analytics\Stats::pageLabel((string) $cur) }}</b><small>{{ $cur }}</small></span>
                            <span class="bx-live__meta">{{ ['mobile' => '📱', 'tablet' => '📟', 'desktop' => '💻'][$v->device] ?? '' }} {{ $v->browser }}</span>
                            <span class="bx-live__meta">{{ $v->source ?: 'To‘g‘ridan-to‘g‘ri' }}</span>
                            <span class="bx-live__meta">{{ $v->pageviews }} sahifa · {{ \App\Support\Analytics\Stats::duration($v->duration) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="bx-rank__empty">Hozir saytda hech kim yo‘q. Ro‘yxat har 15 soniyada yangilanadi.</p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
