@php
    // Blok ma'lumoti o'zi yuklanadi — istalgan sahifaga qo'yish mumkin
    $svcPlans = \App\Models\ServicePlan::active()->get();
    $base = \App\Support\Blocks::serviceBase($svcPlans);
@endphp
{{-- Figma: Ishni qanday boshlaymiz — sticky --}}
<section class="section section--soft" id="start">
    <div class="container sticky2" data-steps>
        <div class="sticky2__left">
            <div class="program__text" data-reveal>
                <h2 class="h2">{{ hs('services_page.ishni_qanday_boshlaymiz', 'Ishni qanday boshlaymiz') }}</h2>
                <p>{{ hs('services_page.buxgalteriyani_bizga_topshirish_tort_bos', 'Buxgalteriyani bizga topshirish to‘rt bosqichda bo‘ladi. Birinchi konsultatsiya bepul.') }}</p>
            </div>
            <a href="#consultation" class="btn-pill btn-pill--ink btn-pill--l" data-consult data-reveal style="--d:120ms"><span class="label">{{ hs('services_page.ariza_qoldirish', 'Ariza qoldirish') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
        </div>
        <div class="sticky2__steps">
            <div class="steps-line" aria-hidden="true"><i></i></div>
            @foreach([
                ['document-text', 'Ariza qoldirasiz', 'Ism va telefon raqamingiz kifoya. Bir ish kuni ichida bog‘lanamiz.'],
                ['search-normal', 'Holatni o‘rganamiz', 'Bepul konsultatsiyada hisob va hisobotlaringiz holatini ko‘rib chiqamiz.'],
                ['task-square', 'Shartnoma tuzamiz', 'Tarif, xizmatlar ro‘yxati va javobgarlik shartnomada belgilanadi.'],
                ['calculator', 'Buxgalteriyani yuritamiz', 'Bosh buxgalter biriktiriladi, hisobotlar o‘z vaqtida topshiriladi.'],
            ] as $i => [$ic, $t, $d])
                <div class="sstep" data-step data-reveal>
                    <div class="sstep__top"><span class="n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><span class="sstep__done">{{ icon('tick-circle') }}</span><span class="icon-tile icon-tile--40">{{ icon($ic) }}</span></div>
                    <div><h3>{{ __($t) }}</h3><p>{{ __($d) }}</p></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
