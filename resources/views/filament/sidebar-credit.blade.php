{{-- Yon menyu pastida: panel nomi, versiya va ishlab chiquvchi --}}
<div class="bx-credit" x-show="$store.sidebar.isOpen" x-cloak>
    <div class="bx-credit__app"><b>Buxgalter</b> Admin panel <span class="bx-credit__ver">v{{ config('app.version') }}</span></div>
    <a href="https://7design.uz" target="_blank" rel="noopener" class="bx-credit__dev" title="Seven Design Studio — 7design.uz">
        <span>Ishlab chiquvchi</span>
        @include('partials.seven-logo', ['w' => 72])
    </a>
</div>
