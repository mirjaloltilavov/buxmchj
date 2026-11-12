@php $services = \App\Models\Service::active()->where('on_home', true)->take(6)->get(); @endphp
@include('sections.services-overview')
