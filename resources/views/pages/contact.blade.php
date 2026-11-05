@extends('layouts.app')
@section('title', __('Aloqa').' — Buxgalter MChJ')

@php
    $phone = setting('phone', '+998 55 500 37 37');
    $branch = $branches->first();
@endphp

@section('content')
    @component('partials.page-hero', ['title' => __('Biz bilan bog‘laning'), 'lead' => hs('contact.lead', 'Kurslar yoki buxgalteriya xizmati bo‘yicha savolingiz bo‘lsa — qo‘ng‘iroq qiling, yozing yoki ofisimizga keling. Ish vaqtida 15 daqiqa ichida javob beramiz.'), 'crumbs' => [__('Aloqa') => null]])
        <div class="contact-grid">
            <a class="contact-card" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ icon('call') }}<small>{{ __('Telefon') }}</small><b>{{ $phone }}</b></a>
            <a class="contact-card" href="{{ setting('telegram_url', 'https://t.me/buxgalter_mchj') }}" target="_blank" rel="noopener">{{ icon('send-2') }}<small>Telegram</small><b>{{ setting('telegram_handle', '@buxgalter_mchj') }}</b></a>
            <a class="contact-card" href="{{ setting('instagram_url', 'https://instagram.com/buxgalter_mchj') }}" target="_blank" rel="noopener">{{ icon('instagram') }}<small>Instagram</small><b>{{ setting('instagram_handle', '@buxgalter_mchj') }}</b></a>
            <div class="contact-card">{{ icon('clock') }}<small>{{ __('Ish vaqti') }}</small><b>{{ tr(setting('work_hours')) ?: __('Du–Sha, 9:00–18:00') }}</b></div>
        </div>
    @endcomponent

    <section class="section" id="form">
        <div class="container form-wrap">
            <div class="reveal">
                <h2 class="h2">{{ __('Savolingizni qoldiring — o‘zimiz qo‘ng‘iroq qilamiz') }}</h2>
                <p style="font-size:18px;color:var(--text);margin-top:20px">{{ __('Formani to‘ldiring: mutaxassisimiz siz bilan bog‘lanib, mos kurs yoki xizmat turini tanlashga yordam beradi.') }}</p>
                <div class="benefits">
                    @foreach([['Tez javob', 'Ish vaqtida 15 daqiqa ichida qayta qo‘ng‘iroq qilamiz.', 'clock'], ['Aniq maslahat', 'Kurs, tarif yoki xizmat narxi bo‘yicha savollarga javob beramiz.', 'message-question'], ['Majburiyatsiz', 'Konsultatsiya bepul, hech narsaga majbur qilmaydi.', 'tick-circle']] as [$t, $d, $i])
                        <div class="benefit"><span class="icon-tile">{{ icon($i) }}</span><div><b>{{ __($t) }}</b><p>{{ __($d) }}</p></div></div>
                    @endforeach
                </div>
            </div>
            @include('partials.lead-form', ['withMessage' => true])
        </div>
    </section>

    @if($branches->isNotEmpty())
        <section class="section section--soft">
            <div class="container">
                <div class="section-head"><h2 class="h2">{{ __('Manzilimiz') }}</h2><p>{{ __('Offline kurslar Toshkentdagi o‘quv markazimizda o‘tadi.') }}</p></div>
                <div class="branches">
                    @foreach($branches as $b)
                        <div class="branch-card">
                            <b style="font-size:22px">{{ $b->tr('name') }}</b>
                            <div class="row">{{ icon('location') }} <span>{{ $b->tr('address') }}</span></div>
                            @if($b->tr('hours'))<div class="row">{{ icon('clock') }} <span>{{ $b->tr('hours') }}</span></div>@endif
                            @if($b->phone)<div class="row">{{ icon('call') }} <a href="tel:{{ preg_replace('/[^0-9+]/', '', $b->phone) }}">{{ $b->phone }}</a></div>@endif
                            @if($b->map_url)<a class="btn btn--acc" href="{{ $b->map_url }}" target="_blank" rel="noopener">{{ __('Yo‘l ko‘rsatish') }} {{ icon('arrow-right') }}</a>@endif
                        </div>
                    @endforeach
                    @if(setting('map_embed'))
                        <div class="map">{!! setting('map_embed') !!}</div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @include('sections.faq')
    @include('sections.instagram')
@endsection
