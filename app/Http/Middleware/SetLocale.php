<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** URL prefiksi bo'yicha til: "/ru/..." -> ru, qolgani -> uz. */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'ru' ? 'ru' : 'uz';
        app()->setLocale($locale);
        \Carbon\Carbon::setLocale($locale === 'ru' ? 'ru' : 'uz_Latn');

        return $next($request);
    }
}
