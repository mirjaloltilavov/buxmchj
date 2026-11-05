<div @class(['section-head', 'section-head--lg' => $lg ?? false])>
    <h2 class="h2">{{ $title }}</h2>
    @if(! empty($lead))<p>{{ $lead }}</p>@endif
    {{ $slot ?? '' }}
</div>
