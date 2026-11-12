@extends('layouts.app')
@section('title', __('Sahifa topilmadi').' — '.setting('site_name', 'Buxgalter MChJ'))
@section('description', __('Siz qidirgan sahifa topilmadi. Kurslar, buxgalteriya xizmati yoki maqolalar bo‘limiga o‘ting.'))
@section('noindex', '1')
@section('content')
    @include('partials.page-hero', ['title' => __('Sahifa topilmadi'), 'lead' => __('Havola eskirgan yoki sahifa o‘chirilgan bo‘lishi mumkin. Quyidagi bo‘limlardan birini tanlang.')])
    <section class="section">
        <div class="container">
            <div class="hero-ctas">
                <a href="{{ lroute('home') }}" class="btn-pill btn-pill--ink btn-pill--l"><span class="label">{{ __('Bosh sahifa') }}</span><span class="arrow">{{ icon('arrow-right') }}</span></a>
                <a href="{{ lroute('courses') }}" class="btn btn--white">{{ __('Kurslar') }}</a>
                <a href="{{ lroute('services') }}" class="btn btn--white">{{ __('Buxgalteriya xizmati') }}</a>
                <a href="{{ lroute('blog') }}" class="btn btn--white">{{ __('Maqolalar') }}</a>
                <a href="{{ lroute('contact') }}" class="btn btn--white">{{ __('Aloqa') }}</a>
            </div>
        </div>
    </section>
@endsection
