@extends('layouts.app')
@section('title', __('Buxgalteriya xizmati').' — Buxgalter MChJ')

@section('content')
    @component('partials.page-hero', ['title' => __('Buxgalteriya xizmati'), 'lead' => hs('services_page.lead', 'Hisob yuritish, soliq hisobotlari, ish haqi va bank operatsiyalari — tajribali jamoaga topshiring, o‘zingiz biznesingizga e’tibor bering.'), 'crumbs' => [__('Buxgalteriya xizmati') => null]])
        <div class="hero-ctas">
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--xl" data-consult>{{ __('Bepul konsultatsiya') }} <span class="arrow">{{ icon('arrow-diag') }}</span></a>
            <a href="#plans" class="btn btn--white">{{ __('Xizmat narxlari') }}</a>
        </div>
    @endcomponent

    <section class="section">
        <div class="container">
            <div class="section-head reveal">
                <h2 class="h2">{{ __('Nimalarni o‘z zimmamizga olamiz') }}</h2>
                <p>{{ __('Buxgalteriyaning barcha yo‘nalishlarini bitta jamoa yuritadi — siz faqat natijani ko‘rasiz.') }}</p>
            </div>
            <div class="service-cards" style="grid-template-columns:repeat(auto-fit,minmax(300px,1fr))">
                @foreach($services as $i => $service)
                    <div class="service-card reveal">
                        <div class="top"><span class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="icon-tile icon-tile--44">{{ icon($service->icon) }}</span></div>
                        <div><h3>{{ $service->tr('title') }}</h3><p>{{ $service->tr('description') }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if($plans->isNotEmpty())
        <section class="section section--soft" id="plans">
            <div class="container">
                <div class="section-head reveal">
                    <h2 class="h2">{{ __('Xizmat tariflari') }}</h2>
                    <p>{{ __('Narx tashkilot shakli, QQS va faoliyat turiga bog‘liq. Aniq narxni konsultatsiyada aytamiz.') }}</p>
                </div>
                <div class="plans">
                    @foreach($plans as $plan)
                        <div @class(['plan', 'reveal', 'is-hl' => $plan->is_highlighted])>
                            <div><h3>{{ $plan->tr('name') }}</h3><p class="sub">{{ $plan->tr('subtitle') }}</p></div>
                            <p class="price"><small>{{ __('Oyiga') }}</small>{{ money_uz($plan->price) }} {{ __('so‘m') }}</p>
                            <ul>@foreach(($plan->features ?? []) as $f)<li>{{ icon('tick-circle') }} {{ tr($f['text'] ?? $f) }}</li>@endforeach</ul>
                            <a href="#consultation" class="btn {{ $plan->is_highlighted ? 'btn--acc' : 'btn--ghost' }}" data-consult>{{ __('Ariza qoldirish') }}</a>
                        </div>
                    @endforeach
                </div>
                <p class="note">{{ __('Narxlar taxminiy. Korxona hajmi va oylik operatsiyalar soniga qarab o‘zgarishi mumkin.') }}</p>
            </div>
        </section>
    @endif

    @include('sections.faq')
    @include('sections.consultation', ['interest' => 'service'])
@endsection
