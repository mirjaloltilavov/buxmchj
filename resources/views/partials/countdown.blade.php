<div @class(['countdown', 'countdown--dark' => $dark ?? false]) data-countdown="{{ $deadline->toIso8601String() }}">
    @foreach([['d', __('kun')], ['h', __('soat')], ['m', __('daq')], ['s', __('son')]] as [$k, $label])
        <span class="countdown__cell"><b data-cd="{{ $k }}">00</b><small>{{ $label }}</small></span>
    @endforeach
</div>
