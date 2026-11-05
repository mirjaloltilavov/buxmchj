<?php

namespace App\Filament\Support;

/** resources/icons ichidagi ikonkalar ro'yxati (Select uchun). */
class Icons
{
    public static function options(): array
    {
        $names = collect(glob(resource_path('icons/*.svg')))
            ->map(fn ($f) => basename($f, '.svg'))
            ->reject(fn ($n) => str_starts_with($n, 'logo') || str_starts_with($n, 'flag') || $n === 'hero-deco')
            ->values();

        return $names->combine($names)->all();
    }
}
