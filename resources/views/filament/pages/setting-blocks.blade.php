<x-filament-panels::page>
    @if($hint)<p class="text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>@endif

    @php
        $gk = array_keys($groups);
        $gid = fn ($i) => 'g'.($i + 1);
    @endphp
    {{-- Ichki yon menyu: bo'limlar va bloklar; o'ngda faqat tanlangan bo'lim. Tanlov havolada (#…) saqlanadi --}}
    <div class="bx-shell" x-data="{
            g: '{{ $gid(0) }}', q: '',
            idx: {{ \Illuminate\Support\Js::from(collect($groups)->map(fn ($bl, $gr) => collect($bl)->pluck('title')->push($gr)->implode(' | '))->values()) }},
            none() { return this.q && !this.idx.some(t => t.toLowerCase().includes(this.q.toLowerCase())); },
            init() {
                this.fromHash();
                window.addEventListener('hashchange', () => this.fromHash());
            },
            fromHash() {
                const h = location.hash.slice(1);
                if (/^g\d+$/.test(h) && this.$refs['sec_' + h]) this.g = h;
                else if (h.startsWith('blk-')) { const c = document.getElementById(h); if (c) { this.g = c.closest('[data-g]').dataset.g; this.$nextTick(() => this.flash(c)); } }
            },
            go(g, blk) {
                this.q = ''; this.g = g;
                history.replaceState(null, '', '#' + (blk ? 'blk-' + blk : g));
                this.$nextTick(() => {
                    if (blk) this.flash(document.getElementById('blk-' + blk));
                    else window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            },
            flash(el) {
                if (!el) return;
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                el.classList.remove('is-flash'); void el.offsetWidth; el.classList.add('is-flash');
            },
            hit(t) { return !this.q || t.toLowerCase().includes(this.q.toLowerCase()); },
        }">
        <aside class="bx-nav">
            <div class="bx-nav__inner">
                <label class="bx-nav__search">
                    <x-filament::icon icon="heroicon-m-magnifying-glass" class="h-4 w-4" />
                    <input type="search" x-model.debounce.150ms="q" placeholder="Bo‘lim qidirish…">
                </label>
                <nav class="bx-nav__list">
                    @foreach($groups as $group => $blocks)
                        @php $id = $gid($loop->index); @endphp
                        <div class="bx-nav__grp" x-show="!q || {{ \Illuminate\Support\Js::from(collect($blocks)->pluck('title')->push($group)->implode(' | ')) }}.toLowerCase().includes(q.toLowerCase())">
                            <button type="button" class="bx-nav__item" :class="{ 'is-active': g === '{{ $id }}' && !q }" x-on:click="go('{{ $id }}')">
                                <x-filament::icon :icon="$groupIcons[$group] ?? 'heroicon-o-folder'" class="h-5 w-5" />
                                <span>{{ $group }}</span>
                                <em>{{ count($blocks) }}</em>
                            </button>
                            @if(count($blocks) > 1)
                                <ul class="bx-nav__sub" x-show="g === '{{ $id }}' || q" x-collapse>
                                    @foreach($blocks as $b)
                                        <li x-show="hit({{ \Illuminate\Support\Js::from($b['title'].' '.$group) }})">
                                            <a href="#blk-{{ $b['key'] }}" x-on:click.prevent="go('{{ $id }}', '{{ $b['key'] }}')">
                                                {{ $b['title'] }}
                                                @if($b['toggle'])<i @class(['bx-nav__dot', 'is-on' => $b['toggle']['on']]) title="{{ $b['toggle']['on'] ? 'Yoqilgan' : 'O‘chirilgan' }}"></i>@endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="bx-main">
    @foreach($groups as $group => $blocks)
        @php $id = $gid($loop->index); @endphp
        <section class="bx-set" data-g="{{ $id }}" x-ref="sec_{{ $id }}" x-show="q ? {{ \Illuminate\Support\Js::from(collect($blocks)->pluck('title')->push($group)->implode(' | ')) }}.toLowerCase().includes(q.toLowerCase()) : g === '{{ $id }}'">
            <h2 class="bx-set__group">
                <x-filament::icon :icon="$groupIcons[$group] ?? 'heroicon-o-folder'" class="h-5 w-5" />{{ $group }}
            </h2>
            <div class="bx-set__grid">
                @foreach($blocks as $b)
                    <article class="bx-set__card" id="blk-{{ $b['key'] }}" wire:key="blk-{{ $b['key'] }}" x-show="!q || hit({{ \Illuminate\Support\Js::from($b['title'].' '.$group) }})">
                        <header class="bx-set__head">
                            <span class="bx-set__ico"><x-filament::icon :icon="$b['icon']" class="h-5 w-5" /></span>
                            <div class="bx-set__ttl">
                                <b>{{ $b['title'] }}</b>
                                @if($b['description'])<small>{{ $b['description'] }}</small>@endif
                            </div>
                            @if($b['toggle'])
                                <button type="button" wire:click="toggleBlock('{{ $b['key'] }}')" class="bx-sw {{ $b['toggle']['on'] ? 'is-on' : '' }}" role="switch" aria-checked="{{ $b['toggle']['on'] ? 'true' : 'false' }}" title="{{ $b['toggle']['on'] ? 'O‘chirish' : 'Yoqish' }}">
                                    <i></i><span>{{ $b['toggle']['on'] ? 'Yoqilgan' : 'O‘chirilgan' }}</span>
                                </button>
                            @endif
                            <x-filament::button size="sm" color="gray" icon="heroicon-m-pencil-square" wire:click="mountAction('editBlock', { key: '{{ $b['key'] }}' })">Tahrirlash</x-filament::button>
                        </header>
                        <dl class="bx-set__rows {{ $b['toggle'] && ! $b['toggle']['on'] ? 'is-off' : '' }}">
                            @foreach($b['rows'] as $r)
                                <div class="bx-set__row {{ $r['wide'] ? 'is-wide' : '' }}">
                                    <dt>{{ $r['label'] }}</dt>
                                    <dd>
                                        @php $v = $r['value']; @endphp
                                        @switch($r['kind'])
                                            @case('image')
                                                @if(filled($v) && is_string($v))<img src="{{ media_url($v) }}" alt="" class="bx-set__img">@else<span class="bx-set__empty">Rasm yo‘q</span>@endif
                                                @break
                                            @case('files')
                                                @php $files = collect(is_array($v) ? $v : [])->filter()->map(fn ($f) => basename((string) $f))->values(); @endphp
                                                @if($files->isNotEmpty())
                                                    <span class="bx-set__chips">@foreach($files as $fn)<a class="bx-pill is-yes" href="{{ url($fn) }}" target="_blank" rel="noopener">{{ $fn }}</a>@endforeach</span>
                                                @else<span class="bx-set__empty">Fayl yuklanmagan</span>@endif
                                                @break
                                            @case('bool')
                                                <span class="bx-pill {{ $v ? 'is-yes' : '' }}">{{ $v ? 'Ha' : 'Yo‘q' }}</span>
                                                @break
                                            @case('secret')
                                                @if(filled($v))<span class="bx-pill is-yes">Kiritilgan ••••••</span>@else<span class="bx-set__empty">Kiritilmagan</span>@endif
                                                @break
                                            @case('date')
                                                @if(filled($v)){{ \Illuminate\Support\Carbon::parse($v)->format('d.m.Y') }}@else<span class="bx-set__empty">—</span>@endif
                                                @break
                                            @case('html')
                                                @if(filled($v)){{ \Illuminate\Support\Str::limit(trim(strip_tags((string) $v)), 220) }}@else<span class="bx-set__empty">Kiritilmagan</span>@endif
                                                @break
                                            @case('list')
                                                @php $items = collect(is_array($v) ? $v : [])->map(fn ($i) => is_array($i) ? ($i['text']['uz'] ?? $i['title']['uz'] ?? $i['name']['uz'] ?? null) : $i)->filter()->values(); @endphp
                                                @if($items->isNotEmpty())
                                                    <span class="bx-set__chips">@foreach($items as $it)<span class="bx-pill">{{ $it }}</span>@endforeach</span>
                                                @else<span class="bx-set__empty">Kiritilmagan</span>@endif
                                                @break
                                            @default
                                                @if(filled($v) && ! is_array($v))<span class="bx-set__txt">{{ \Illuminate\Support\Str::limit((string) $v, 260) }}</span>@else<span class="bx-set__empty">{{ $emptyLabel }}</span>@endif
                                        @endswitch
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                        @if($b['test'])
                            <footer class="bx-set__foot">
                                <span>Sozlamani saqlagach, ishlashini tekshiring</span>
                                <x-filament::button size="sm" color="gray" icon="heroicon-m-paper-airplane" wire:click="testBlock('{{ $b['key'] }}')" wire:target="testBlock('{{ $b['key'] }}')">{{ $b['test'] }}</x-filament::button>
                            </footer>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
            <p class="bx-empty" x-show="none()" x-cloak>Hech narsa topilmadi.</p>
        </div>
    </div>
</x-filament-panels::page>
