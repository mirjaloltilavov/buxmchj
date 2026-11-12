@php
    // Blok ma'lumoti o'zi yuklanadi — istalgan sahifaga qo'yish mumkin
    $svcPlans = \App\Models\ServicePlan::active()->get();
    $base = \App\Support\Blocks::serviceBase($svcPlans);
@endphp
{{-- Figma: Xizmat tariflari --}}
@if($svcPlans->isNotEmpty())
    <section class="section section--soft" id="plans">
        <div class="container">
            @include('partials.section-head', ['title' => hs('services_page.xizmat_tariflari', 'Xizmat tariflari'), 'lead' => hs('services_page.narx_tashkilot_shakli_qqs_va_faoliyat_tu', 'Narx tashkilot shakli, QQS va faoliyat turiga bog‘liq. Aniq narxni konsultatsiyada aytamiz.'), 'lg' => true])
            <div class="pcards pcards--4" data-snap style="--n: {{ max(1, min(4, $svcPlans->count())) }}">
                @foreach($svcPlans as $i => $plan)
                    <div @class(['pcard', 'pcard--sm', 'pcard--hl' => $plan->is_highlighted]) data-reveal="up40" style="--d: {{ $i * 80 + ($plan->is_highlighted ? 120 : 0) }}ms">
                        <div class="pcard__head"><h3>{{ $plan->tr('name') }}</h3><p>{{ $plan->tr('subtitle') }}</p></div>
                        <div class="pcard__price"><small>{{ __('Oyiga') }}</small><b>{{ money_uz($plan->price) }} {{ __('so‘m') }}</b></div>
                        <a href="#consultation" class="btn-pill btn-pill--l {{ $plan->is_highlighted ? 'btn-pill--ink' : 'btn-pill--outline' }} pcard__btn" data-consult><span class="label">{{ hs('services_page.ariza_qoldirish', 'Ariza qoldirish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                        <hr>
                        <ul class="pcard__features">@foreach(($plan->features ?? []) as $f)<li>{{ icon('tick-circle') }}<span>{{ tr($f['text'] ?? $f) }}</span></li>@endforeach</ul>
                    </div>
                @endforeach
            </div>
            <p class="note">{{ hs('services_page.narxlar_taxminiy_korxona_hajmi_va_oylik', 'Narxlar taxminiy. Korxona hajmi va oylik operatsiyalar soniga qarab o‘zgarishi mumkin.') }}</p>
        </div>
    </section>
@endif
