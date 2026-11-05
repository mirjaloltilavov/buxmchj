@php $items = collect($getState() ?? [])->values()->all(); @endphp
<div class="bx-cards bx-cards--{{ $kind }}" style="--cols:{{ $cols }}">
    @forelse($items as $i => $item)
        <div class="bx-card"><div class="bx-card__prev">{{ \App\Filament\Support\CardPreview::render($kind, $item, $i, count($items)) }}</div></div>
    @empty
        <p class="bx-cards__empty">Kiritilmagan</p>
    @endforelse
</div>
