{{-- Figma: Promo popup (Kurs chegirma / Xizmat chegirma / Qo‘llanma). Admin: Sayt sozlamalari → Popuplar --}}
@php
    $cfg = setting('popups') ?: [];
    $p = $cfg[$type] ?? [];
    $enabled = $p['enabled'] ?? true;
    $deadline = ! empty($p['deadline']) ? \Illuminate\Support\Carbon::parse($p['deadline'])->endOfDay() : now()->endOfMonth();
    if ($deadline->isPast()) {
        $enabled = false;
    }
    $months = app()->getLocale() === 'ru'
        ? ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря']
        : ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentyabr', 'oktyabr', 'noyabr', 'dekabr'];
    $deadlineLabel = $deadline->day.'-'.$months[$deadline->month - 1].' '.$deadline->year;
    $t = fn ($key, $default) => tr($p[$key] ?? null) ?: __($default);
@endphp
@if($enabled)
<div class="popup popup--promo popup--{{ $type }}" data-promo="{{ $type }}" data-delay="{{ (int) ($cfg['delay'] ?? 15) }}" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="popup-overlay" data-popup-close></div>
    <div class="popup-box promo" data-sheet>
        <span class="promo__grab" aria-hidden="true"></span>
        <button class="icon-btn promo__close" type="button" data-popup-close aria-label="{{ __('Yopish') }}">{{ icon('plus', 18) }}</button>

        @if($type === 'course')
            <div class="promo__img">
                <img src="{{ media_url($p['image'] ?? 'img/articles/birinchi-ish.jpg') }}" alt="" loading="lazy">
                <span class="promo__sticker"><b>−20%</b><small>{{ __('chegirma') }}</small></span>
            </div>
            <div class="promo__body">
                <div class="promo__head">
                    <h3>{{ $t('title', 'Kursga hozir yoziling — 20 % chegirma') }}</h3>
                    <p>{{ $t('text', 'To‘lovni bir martada amalga oshirsangiz, istalgan tarifga 20 % chegirma beriladi. Joylar soni cheklangan.') }}</p>
                </div>
                <div class="promo__price">
                    <div class="promo__row"><b><span data-promo-count="{{ (int) ($p['price_new'] ?? 2160000) }}">{{ money_uz((int) ($p['price_new'] ?? 2160000)) }}</span> {{ __('so‘m') }}</b><s>{{ money_uz((int) ($p['price_old'] ?? 2700000)) }}</s></div>
                    <small>{{ $t('caption', 'Premium tarif · bir martalik to‘lovda') }}</small>
                </div>
                <div class="promo__timer">
                    <small>{{ __('Taklif tugashiga qoldi:') }}</small>
                    @include('partials.countdown', ['deadline' => $deadline])
                </div>
                <div class="promo__actions">
                    <a href="#consultation" class="btn-pill btn-pill--yellow btn-pill--l btn-pill--block" data-consult data-popup-go><span class="label">{{ __('Chegirma bilan yozilish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                    <button type="button" class="promo__later" data-popup-close>{{ __('Rahmat, keyinroq') }}</button>
                </div>
            </div>
        @elseif($type === 'service')
            @php $points = array_values(array_filter(array_map('trim', preg_split('/\R/', tr($p['points'] ?? null) ?: '')))); @endphp
            <div class="promo__body promo__body--dark">
                <div class="promo__top">
                    <span class="icon-tile promo__gift">{{ icon('gift') }}</span>
                </div>
                <div class="promo__head">
                    <h3>{{ $t('title', 'Birinchi oy buxgalteriya xizmati — 50 % chegirma') }}</h3>
                    <p>{{ $t('text', 'Shartnoma tuzgan yangi mijozlar uchun birinchi oy xizmati yarim narxda. Hisobingiz holatini esa bepul tekshirib beramiz.') }}</p>
                </div>
                <ul class="promo__ticks">
                    @foreach($points ?: [__('Hisob holatini bepul tekshiramiz'), __('Shaxsiy buxgalter biriktiriladi'), __('Rasmiy shartnoma asosida ishlaymiz')] as $pi => $pt)
                        <li style="--d: {{ 300 + $pi * 60 }}ms">{{ icon('tick-circle') }}<span>{{ $pt }}</span></li>
                    @endforeach
                </ul>
                <div class="promo__timer">
                    <small>{{ __('Taklif tugashiga qoldi:') }}</small>
                    @include('partials.countdown', ['deadline' => $deadline, 'dark' => true])
                </div>
                <div class="promo__actions">
                    <a href="#consultation" class="btn-pill btn-pill--yellow btn-pill--l btn-pill--block shine" data-consult data-popup-go data-interest="service"><span class="label">{{ __('Taklifdan foydalanish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                    <span class="promo__until">{{ icon('calendar', 16) }} {{ __('Taklif :date gacha amal qiladi', ['date' => $deadlineLabel]) }}</span>
                </div>
            </div>
        @else
            <div class="promo__img promo__img--guide"><img src="{{ media_url($p['image'] ?? 'img/popup-guide.jpg') }}" alt="" loading="lazy"></div>
            <div class="promo__body">
                <div class="promo__head">
                    <h3>{{ $t('title', 'YaTT va MChJ uchun soliq kalendari 2027') }}</h3>
                    <p>{{ $t('text', 'Barcha hisobot va to‘lov muddatlari bitta PDF faylda. Raqamingizni qoldiring — Telegram orqali yuboramiz.') }}</p>
                </div>
                <form class="promo__form lead-form lead-form--bare" method="post" action="{{ route('lead.store') }}" data-lead-form novalidate>
                    @csrf
                    <div class="form-fields">
                        <input type="text" name="website" tabindex="-1" autocomplete="off" class="sr-only" aria-hidden="true">
                        <input type="hidden" name="source" value="{{ request()->path() }} · popup">
                        <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
                        <input type="hidden" name="interest" value="guide">
                        <input type="hidden" name="message" value="{{ $t('title', 'YaTT va MChJ uchun soliq kalendari 2027') }}">
                        <label class="field">
                            <span>{{ __('Telefon raqam') }}</span>
                            <span class="input">{{ icon('call') }}<input type="tel" name="phone" required placeholder="+998 __ ___ __ __" autocomplete="tel" inputmode="tel" data-phone></span>
                        </label>
                        <p class="form-error" data-form-error hidden></p>
                        <button type="submit" class="btn-pill btn-pill--ink btn-pill--l btn-pill--block"><span class="label">{{ __('Qo‘llanmani olish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></button>
                        <p class="form-note">{{ __('Ma’lumotlaringiz uchinchi shaxslarga berilmaydi.') }}</p>
                    </div>
                    <div class="form-success" role="status" aria-live="polite"><span class="ok">{{ icon('tick-circle') }}</span><b>{{ __('Qo‘llanma Telegram’ga yuborildi') }}</b><span class="fc-sub">{{ __('Telegram’ni tekshiring — fayl bir necha daqiqada keladi.') }}</span></div>
                </form>
            </div>
        @endif
    </div>
</div>
@endif
