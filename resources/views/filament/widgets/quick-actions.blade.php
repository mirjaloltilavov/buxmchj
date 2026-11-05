<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem">
            <div>
                <h2 style="font-size:1.25rem;font-weight:700;line-height:1.3">{{ $greeting }} 👋</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Bugun nima qilamiz? Eng ko‘p kerak bo‘ladigan amallar shu yerda.</p>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                @foreach($links as $l)
                    <x-filament::button tag="a" :href="$l[2]" :icon="$l[1]" :color="($l[3] ?? false) ? 'gray' : 'primary'" :outlined="! ($l[0] === 'Maqolalar')" :target="($l[3] ?? false) ? '_blank' : null">{{ $l[0] }}</x-filament::button>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
