@php
    $items = $items();
    $kind = $getKind();
    $addAction = $getAction('add');
    $wide = in_array($kind, ['outcome', 'feature', 'tag', 'stat'], true);
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="bx-cards bx-cards--{{ $kind }}" style="--cols:{{ $getColumnsCount() }}">
        @forelse($items as $i => $item)
            <div class="bx-card" wire:key="card-{{ $kind }}-{{ $i }}">
                <div class="bx-card__prev">{{ \App\Filament\Support\CardPreview::render($kind, $item, $i, count($items)) }}</div>
                <div class="bx-card__bar">
                    <span class="bx-card__n">{{ $i + 1 }}</span>
                    <span class="bx-card__ctl">
                        {{ ($getAction('up'))(['index' => $i]) }}
                        {{ ($getAction('down'))(['index' => $i]) }}
                        {{ ($getAction('duplicate'))(['index' => $i]) }}
                        {{ ($getAction('edit'))(['index' => $i]) }}
                        {{ ($getAction('remove'))(['index' => $i]) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="bx-cards__empty">Hozircha {{ $getItemName() }} yo‘q. Pastdagi tugma bilan qo‘shing.</p>
        @endforelse
    </div>
    @if($canAdd())
        <div style="margin-top:.75rem">{{ $addAction }}</div>
    @else
        <p class="text-sm text-gray-500" style="margin-top:.5rem">Eng ko‘pi bilan {{ count($items) }} ta {{ $getItemName() }} bo‘lishi mumkin.</p>
    @endif
</x-dynamic-component>
