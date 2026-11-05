<?php

namespace App\Models\Concerns;

/**
 * JSON ustunlarda {"uz": "...", "ru": "..."} ko'rinishida saqlangan matnlarni
 * joriy tilga mos qaytaradi. Tarjima bo'lmasa o'zbekchasiga qaytadi.
 */
trait Translatable
{
    public function tr(string $attribute, ?string $locale = null): ?string
    {
        return tr($this->getAttribute($attribute), $locale);
    }
}
