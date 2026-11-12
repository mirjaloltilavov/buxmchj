@php
    // Qaysi sahifa savollari: blokda tanlangan yoki joriy sahifaniki
    $set = ($b['set'] ?? null) ?: (['course' => 'courses', 'article' => 'home', 'blog' => 'home', 'page' => 'home'][$ctx] ?? $ctx);
    $faqs = \App\Models\Faq::for($set)->get();
@endphp
@include('sections.faq')
