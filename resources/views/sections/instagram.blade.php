{{-- Figma: Instagram --}}
@if($instagram->isNotEmpty())
<section class="insta" id="instagram">
    <div class="container insta-head">
        <h2 class="h2">{{ setting('instagram_handle', '@buxgalter_mchj') }}</h2>
        <a href="{{ setting('instagram_url', 'https://instagram.com/buxgalter_mchj') }}" target="_blank" rel="noopener" class="btn-pill btn-pill--ink btn-pill--l">{{ __('Obuna bo‘lish') }} <span class="arrow">{{ icon('arrow-diag') }}</span></a>
    </div>
    <div class="insta-track">
        @foreach([1, 2] as $loopNo)
            @foreach($instagram as $post)
                <a href="{{ $post->url ?: setting('instagram_url', 'https://instagram.com/buxgalter_mchj') }}" target="_blank" rel="noopener" class="insta-post" @if($loopNo === 2) aria-hidden="true" tabindex="-1" @endif>
                    <img src="{{ media_url($post->image) }}" alt="" loading="lazy">
                    <span class="glass-btn">{{ icon('instagram') }}</span>
                </a>
            @endforeach
        @endforeach
    </div>
</section>
@endif
