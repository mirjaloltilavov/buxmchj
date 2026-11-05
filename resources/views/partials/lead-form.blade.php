<form class="lead-form" method="post" action="{{ route('lead.store') }}" data-lead-form novalidate>
    @csrf
    <h3>{{ __('Ariza') }}</h3>
    <div class="form-fields">
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="sr-only" aria-hidden="true">
        <input type="hidden" name="source" value="{{ request()->path() }}">
        <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
        <label class="field">
            <span>{{ __('Ismingiz') }}</span>
            <span class="input">{{ icon('user') }}<input type="text" name="name" required maxlength="120" placeholder="{{ __('Masalan, Dilnoza') }}" autocomplete="name"></span>
        </label>
        <label class="field">
            <span>{{ __('Telefon raqam') }}</span>
            <span class="input">{{ icon('call') }}<input type="tel" name="phone" required placeholder="+998 __ ___ __ __" autocomplete="tel" inputmode="tel" data-phone></span>
        </label>
        <div class="field">
            <span>{{ __('Nima qiziqtiradi?') }}</span>
            <div class="segmented">
                <label><input type="radio" name="interest" value="course" @checked(($interest ?? 'course') === 'course')><span>{{ icon('book') }} {{ __('O‘quv kursi') }}</span></label>
                <label><input type="radio" name="interest" value="service" @checked(($interest ?? '') === 'service')><span>{{ icon('briefcase') }} {{ __('Buxgalteriya xizmati') }}</span></label>
            </div>
        </div>
        @if($withMessage ?? false)
            <label class="field">
                <span>{{ __('Xabaringiz (ixtiyoriy)') }}</span>
                <span class="input"><textarea name="message" maxlength="2000" rows="3"></textarea></span>
            </label>
        @endif
        <p class="form-error" data-form-error hidden></p>
        <button type="submit" class="btn btn--acc btn--block">{{ __('Ariza yuborish') }} {{ icon('arrow-right') }}</button>
        <p class="form-note">{{ __('Ma’lumotlaringiz faqat siz bilan bog‘lanish uchun ishlatiladi.') }}</p>
    </div>
    <div class="form-success" role="status">{{ __('Rahmat! Arizangiz qabul qilindi. Tez orada siz bilan bog‘lanamiz.') }}</div>
</form>
