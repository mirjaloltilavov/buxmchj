<section class="page-hero">
    <div class="hero-deco" aria-hidden="true">{{ icon('hero-deco') }}</div>
    <div class="container">
        <nav class="crumbs" aria-label="breadcrumb">
            <a href="{{ lroute('home') }}">{{ __('Asosiy') }}</a>
            @foreach(($crumbs ?? []) as $label => $url)
                <span aria-hidden="true">/</span>
                @if($url)<a href="{{ $url }}">{{ $label }}</a>@else<span>{{ $label }}</span>@endif
            @endforeach
        </nav>
        <h1>{{ $title }}</h1>
        @if(! empty($lead))<p class="lead">{{ $lead }}</p>@endif
        {{ $slot ?? '' }}
    </div>
</section>
