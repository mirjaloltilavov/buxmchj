{{-- Admin logotipi: yorug' rejimda qora, qorong'i rejimda oq variant --}}
<span class="bx-brand" style="display:inline-flex;align-items:center">
    <span class="bx-brand-light" style="display:inline-block;height:2rem;width:auto;color:#16171a">{!! file_get_contents(resource_path('icons/logo.svg')) !!}</span>
    <span class="bx-brand-dark" style="display:none;height:2rem;width:auto;color:#fff">{!! file_get_contents(resource_path('icons/logo-white.svg')) !!}</span>
</span>
<style>
    .bx-brand svg{height:2rem;width:auto;display:block}
    .dark .bx-brand-light{display:none!important}
    .dark .bx-brand-dark{display:inline-block!important}
</style>
