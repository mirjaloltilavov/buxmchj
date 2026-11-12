@php $courses = \App\Models\Course::active()->get(); @endphp
@include('sections.courses-overview')
