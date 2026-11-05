{{-- Hero slider (Figma: Hero slider — 1/2). Slaydlar admin paneldan boshqariladi. --}}
@if($slides->isNotEmpty())
<section class="hero" data-hero data-delay="{{ (int) setting('hero_autoplay', 6) * 1000 }}" style="--hero-delay: {{ (int) setting('hero_autoplay', 6) }}s">
    <div class="hero-deco" aria-hidden="true">{{ icon('hero-deco') }}</div>
    <div class="hero-glow" aria-hidden="true"></div>

    @foreach($slides as $i => $slide)
        @php $c = $slide->cards ?? []; @endphp
        <div @class(['hero-slide', 'hero-slide--'.$slide->theme, 'is-active' => $i === 0]) data-slide="{{ $i }}" @if($i) aria-hidden="true" @endif>
            <div class="container hero-inner">
                <div class="hero-copy">
                    @if($slide->tr('chip'))
                        <span class="chip"><i class="dot"></i>{{ $slide->tr('chip') }}</span>
                    @endif
                    @if($i === 0)
                        <h1>{{ $slide->tr('title') }}</h1>
                    @else
                        <h2 class="hero-h">{{ $slide->tr('title') }}</h2>
                    @endif
                    @if($slide->tr('lead'))<p class="hero-lead">{{ $slide->tr('lead') }}</p>@endif
                    <div class="hero-ctas">
                        @if($slide->tr('primary_label'))
                            <a href="{{ $slide->primary_url ?: '#consultation' }}" class="btn-pill btn-pill--ink btn-pill--xl" @if(! $slide->primary_url || str_starts_with($slide->primary_url, '#consult')) data-consult @endif>
                                {{ $slide->tr('primary_label') }} <span class="arrow">{{ icon('arrow-diag') }}</span>
                            </a>
                        @endif
                        @if($slide->tr('secondary_label'))
                            <a href="{{ $slide->secondary_url ?: '#' }}" class="btn btn--white">{{ $slide->tr('secondary_label') }}</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="hero-media" aria-hidden="true">
                @if($slide->image)
                    <div class="hero-portrait"><img src="{{ media_url($slide->image) }}" alt="" @if($i) loading="lazy" @else fetchpriority="high" @endif></div>
                @endif
                @if(tr($c['badge_title'] ?? null))
                    <div class="float-card float-card--1c">
                        <span class="icon-tile">{{ icon($c['badge_icon'] ?? 'monitor') }}</span>
                        <span><span class="fc-title" style="display:block">{{ tr($c['badge_title']) }}</span><span class="fc-sub">{{ tr($c['badge_sub'] ?? null) }}</span></span>
                    </div>
                @endif
                @if(tr($c['list_title'] ?? null))
                    @php $items = $c['list_items'] ?? []; $done = max(0, count($items) - 1); @endphp
                    <div class="float-card float-card--program">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span class="fc-title">{{ tr($c['list_title']) }}</span>
                            <span class="fc-sub" style="font-weight:500">{{ tr($c['list_counter'] ?? null) }}</span>
                        </div>
                        <div class="progress"><i></i></div>
                        @foreach($items as $k => $item)
                            <div @class(['check-row', 'is-muted' => $k >= $done])>{{ icon('tick-circle') }} {{ tr($item['text'] ?? $item) }}</div>
                        @endforeach
                    </div>
                @endif
                @if(tr($c['dark_title'] ?? null))
                    <a href="{{ $c['dark_url'] ?? '#' }}" class="float-card float-card--dark">
                        <span style="display:flex;flex-direction:column;gap:4px">
                            <span class="fc-sub" style="color:var(--muted-2)">{{ tr($c['dark_sub'] ?? null) }}</span>
                            <span class="fc-title">{{ tr($c['dark_title']) }}</span>
                        </span>
                        <span class="arrow-btn">{{ icon('arrow-diag') }}</span>
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    @if($slides->count() > 1)
        <div class="hero-controls">
            <div class="container">
                <div class="hero-tabs" role="tablist">
                    @foreach($slides as $i => $slide)
                        <button type="button" role="tab" @class(['hero-tab', 'is-active' => $i === 0]) data-tab="{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                            <span class="t"><b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b> {{ $slide->tr('tab_label') }}</span>
                            <span class="bar"><i></i></span>
                        </button>
                    @endforeach
                </div>
                <div class="hero-arrows">
                    <button type="button" class="prev" aria-label="{{ __('Oldingi') }}" data-prev>{{ icon('arrow-left') }}</button>
                    <button type="button" class="next" aria-label="{{ __('Keyingi') }}" data-next>{{ icon('arrow-right') }}</button>
                </div>
            </div>
        </div>
    @endif
</section>
@endif
