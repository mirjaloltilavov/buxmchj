@php $teachers = \App\Models\Teacher::active()->get(); @endphp
@include('sections.teachers', ['soft' => (bool) ($b['soft'] ?? false)])
