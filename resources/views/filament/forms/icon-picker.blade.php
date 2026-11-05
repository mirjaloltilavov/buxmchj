@php $icons = $getIcons(); $statePath = $getStatePath(); @endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
            icons: @js($icons),
            q: '',
            open: false,
            value: $wire.entangle('{{ $statePath }}'),
            get current() { return this.icons.find(i => i.n === this.value) },
            get list() { const q = this.q.trim().toLowerCase(); return q ? this.icons.filter(i => (i.n + ' ' + i.l).toLowerCase().includes(q)) : this.icons },
            pick(n) { this.value = n; this.open = false; },
        }" class="bx-picker" x-on:keydown.escape.window="open = false">
        <button type="button" class="bx-picker__btn" x-on:click="open = !open">
            <span class="bx-ic bx-picker__cur" x-html="current ? current.s : ''"></span>
            <span class="bx-picker__name" x-text="current ? current.l : 'Ikonka tanlang'"></span>
            <x-heroicon-m-chevron-down class="bx-picker__chev" />
        </button>
        <div class="bx-picker__panel" x-show="open" x-cloak x-transition.opacity x-on:click.outside="open = false">
            <div class="bx-picker__top">
                <input type="search" x-model="q" placeholder="Qidirish (masalan: calendar, user, hujjat)…" class="bx-picker__search" x-on:keydown.enter.prevent>
                <a href="{{ \App\Filament\Resources\IconResource::getUrl('index') }}" target="_blank" class="bx-picker__add">+ SVG qo‘shish</a>
            </div>
            <div class="bx-picker__grid">
                <template x-for="i in list" :key="i.n">
                    <button type="button" class="bx-picker__item" :class="{ 'is-on': i.n === value }" :title="i.l + ' — ' + i.n" x-on:click="pick(i.n)">
                        <span class="bx-ic" x-html="i.s"></span>
                    </button>
                </template>
                <p x-show="!list.length" class="bx-picker__empty">Topilmadi. “SVG qo‘shish” orqali o‘zingiznikini qo‘shing.</p>
            </div>
        </div>
    </div>
</x-dynamic-component>
