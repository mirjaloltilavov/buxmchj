@php
    // Blok ma'lumoti o'zi yuklanadi — istalgan sahifaga qo'yish mumkin
    $svcPlans = \App\Models\ServicePlan::active()->get();
    $base = \App\Support\Blocks::serviceBase($svcPlans);
@endphp
{{-- Figma: Narx kalkulyatori --}}
<section class="section" id="calculator">
    <div class="container">
        @include('partials.section-head', ['title' => hs('services_page.narxni_hisoblang', 'Narxni hisoblang'), 'lead' => hs('services_page.bir_necha_savolga_javob_bering_taxminiy', 'Bir necha savolga javob bering — taxminiy oylik narxni ko‘rasiz.'), 'lg' => true])
        <div class="calc" data-calc data-base='@json($base)' data-som="{{ __('so‘m') }}">
            <div class="calc__q" data-reveal>
                <div class="calc__row">
                    <p class="calc__lbl"><i>1</i>{{ hs('services_page.tashkilot_shakli', 'Tashkilot shakli') }}</p>
                    <div class="segmented"><label><input type="radio" name="org" value="yatt"><span>{{ __('YaTT') }}</span></label><label><input type="radio" name="org" value="llc" checked><span>{{ __('MChJ') }}</span></label></div>
                </div>
                <div class="calc__row">
                    <p class="calc__lbl"><i>2</i>{{ hs('services_page.qqs_tolovchisimisiz', 'QQS to‘lovchisimisiz?') }}</p>
                    <div class="segmented"><label><input type="radio" name="vat" value="0"><span>{{ __('Yo‘q') }}</span></label><label><input type="radio" name="vat" value="1" checked><span>{{ __('Ha') }}</span></label></div>
                </div>
                <div class="calc__row">
                    <p class="calc__lbl"><i>3</i>{{ hs('services_page.faoliyat_turi', 'Faoliyat turi') }}</p>
                    <div class="segmented"><label><input type="radio" name="kind" value="local" checked><span>{{ hs('services_page.ichki_savdo_va_xizmat', 'Ichki savdo va xizmat') }}</span></label><label><input type="radio" name="kind" value="import"><span>{{ hs('services_page.import_eksport', 'Import-eksport') }}</span></label></div>
                </div>
                <div class="calc__row">
                    <p class="calc__lbl"><i>4</i>{{ hs('services_page.korxona_hajmi', 'Korxona hajmi') }}</p>
                    <label class="range">
                        <span class="range__top"><b>{{ hs('services_page.xodimlar_soni', 'Xodimlar soni') }}</b><em data-out="emp">10 {{ __('kishi') }}</em></span>
                        <input type="range" name="emp" min="1" max="50" value="10" step="1">
                        <span class="range__minmax"><span>1</span><span>50+</span></span>
                    </label>
                    <label class="range">
                        <span class="range__top"><b>{{ hs('services_page.oylik_operatsiyalar_soni', 'Oylik operatsiyalar soni') }}</b><em data-out="ops">~120 {{ __('ta') }}</em></span>
                        <input type="range" name="ops" min="0" max="500" value="120" step="10">
                        <span class="range__minmax"><span>0</span><span>500+</span></span>
                    </label>
                </div>
            </div>
            <div class="calc__res" data-reveal="card" style="--d:120ms">
                <div class="calc__top">
                    <span class="badge-n" data-res="badge">{{ __('MChJ') }} · {{ hs('services_page.qqs_tolovchisi', 'QQS to‘lovchisi') }}</span>
                    <p class="calc__cap">{{ hs('services_page.taxminiy_oylik_narx', 'Taxminiy oylik narx') }}</p>
                    <p class="calc__sum"><span data-res="sum">{{ money_uz($base['llc_vat']) }}</span> {{ __('so‘m') }}</p>
                    <p class="calc__hint">{{ hs('services_page.aniq_narx_korxonangiz_holatini_organgach', 'Aniq narx korxonangiz holatini o‘rgangach aytiladi.') }}</p>
                    <dl class="calc__sum-list">
                        <div data-row="org"><dt>{{ hs('services_page.tashkilot', 'Tashkilot') }}</dt><dd data-res="org">{{ __('MChJ') }}</dd></div>
                        <div data-row="vat"><dt>{{ __('QQS') }}</dt><dd data-res="vat">{{ __('Ha') }}</dd></div>
                        <div data-row="kind"><dt>{{ hs('services_page.faoliyat', 'Faoliyat') }}</dt><dd data-res="kind">{{ hs('services_page.ichki_savdo_va_xizmat', 'Ichki savdo va xizmat') }}</dd></div>
                        <div data-row="emp"><dt>{{ hs('services_page.xodimlar', 'Xodimlar') }}</dt><dd data-res="emp">10 {{ __('kishi') }}</dd></div>
                        <div data-row="ops"><dt>{{ hs('services_page.operatsiyalar', 'Operatsiyalar') }}</dt><dd data-res="ops">~120 {{ __('ta / oy') }}</dd></div>
                    </dl>
                </div>
                <a href="#consultation" class="btn-pill btn-pill--yellow btn-pill--l calc__btn" data-consult><span class="label">{{ hs('services_page.maxsus_taklif_oling', 'Maxsus taklif oling') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
            </div>
        </div>
    </div>
</section>
