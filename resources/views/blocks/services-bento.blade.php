@php
    $services = \App\Models\Service::active()->get();
    // Bento uyachalari (Figma: Xizmatlar — bento): katta qora, sariq, oq, ko'k (keng), oq, qora, oq
    $slots = ['big', 'acc', 'plain', 'blue', 'plain', 'dark', 'plain'];
@endphp
{{-- Figma: Xizmatlar — bento --}}
<section class="section" id="what">
    <div class="container">
        @include('partials.section-head', ['title' => hs('services_page.nimalarni_oz_zimmamizga_olamiz', 'Nimalarni o‘z zimmamizga olamiz'), 'lead' => hs('services_page.buxgalteriyaning_barcha_yonalishlarini_b', 'Buxgalteriyaning barcha yo‘nalishlarini bitta jamoa yuritadi — siz faqat natijani ko‘rasiz.'), 'lg' => true])
        <div class="sbento" data-stagger="80">
            @foreach($services->take(7) as $i => $service)
                @php $sl = $slots[$i] ?? 'plain'; @endphp
                <div class="sb sb--{{ $sl }} sb--n{{ $i + 1 }}" data-reveal="scale">
                    @if($sl === 'big')
                        <div class="sb__img"><img src="{{ media_url(setting('texts.services_page.bento_image') ?: 'img/services-bento.jpg') }}" alt="" loading="lazy"></div>
                        <div class="sb__body">
                            <div class="sb__head"><span class="icon-tile">{{ icon($service->icon) }}</span><h3>{{ $service->tr('title') }}</h3></div>
                            <p>{{ $service->tr('description') }}</p>
                        </div>
                    @else
                        <span @class(['icon-tile', 'icon-tile--white' => $sl === 'acc', 'icon-tile--blue2' => $sl === 'blue'])>{{ icon($service->icon) }}</span>
                        <div><h3>{{ $service->tr('title') }}</h3><p>{{ $service->tr('description') }}</p></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
