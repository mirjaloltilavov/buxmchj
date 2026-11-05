{{-- Figma: ichki sahifa Hero (sariq gradient, breadcrumbs, sarlavha, tugmalar, stat'lar, rasm + info kartalar) --}}
<section class="p-hero" data-page-hero>
    <div class="hero-deco" aria-hidden="true">{{ icon('hero-deco') }}</div>
    <div class="container p-hero__body">
        <div class="p-hero__left">
            <nav class="crumbs" aria-label="breadcrumb">
                @if($crumbsRoot ?? true)<a href="{{ lroute('home') }}">{{ __('Asosiy') }}</a>@endif
                @foreach(($crumbs ?? []) as $label => $url)
                    @if(($crumbsRoot ?? true) || ! $loop->first){{ icon('arrow-right') }}@endif
                    @if($url)<a href="{{ $url }}">{{ $label }}</a>@else<b>{{ $label }}</b>@endif
                @endforeach
            </nav>
            @if(! empty($badge))<span class="chip chip--light"><i class="dot"></i>{{ $badge }}</span>@endif
            <div class="p-hero__text">
                <h1>{{ $title }}</h1>
                @if(! empty($lead))<p class="p-hero__lead">{{ $lead }}</p>@endif
            </div>
            {{ $slot ?? '' }}
        </div>
        @if(! empty($image))
            <div class="p-hero__media">
                <div class="p-hero__img"><img src="{{ media_url($image) }}" alt="" fetchpriority="high"></div>
                {{ $cards ?? '' }}
            </div>
        @endif
    </div>
</section>
