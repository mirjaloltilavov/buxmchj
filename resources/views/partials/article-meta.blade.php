<div class="meta">
    @if(($withCategory ?? false) && $article->category)<span class="cat">{{ $article->category->tr('name') }}</span>@endif
    <span>{{ icon('calendar') }} {{ $article->dateLabel() }}</span>
    <span>{{ icon('clock') }} {{ $article->read_minutes }} {{ __('daqiqa') }}</span>
</div>
