{{-- Figma: FAQ --}}
@if($faqs->isNotEmpty())
<section class="section" id="faq">
    <div class="container faq">
        <div class="left reveal">
            <h2 class="h2">{{ hs('faq.title', 'Ko‘p so‘raladigan savollar') }}</h2>
            <p>{{ hs('faq.lead', 'Javob topmadingizmi? Telegram’da yozing yoki qo‘ng‘iroq qiling.') }}</p>
            <a href="{{ setting('telegram_url', 'https://t.me/buxgalter_mchj') }}" target="_blank" rel="noopener" class="btn btn--blue">{{ icon('send-2') }} {{ __('Telegram’da yozish') }}</a>
        </div>
        <div class="accordion" data-accordion>
            @foreach($faqs as $i => $faq)
                <details class="acc-item reveal" @if($i === 0) open @endif>
                    <summary>{{ icon('message-question') }}<span class="q">{{ $faq->tr('question') }}</span><span class="pm" aria-hidden="true"></span></summary>
                    <div class="acc-body">{!! nl2br(e($faq->tr('answer'))) !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif
