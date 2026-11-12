<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Translatable;

    /** Bu manzillar saytning o'z sahifalari — yangi sahifaga berib bo'lmaydi */
    public const RESERVED = ['admin', 'ru', 'uz', 'livewire', 'lead', 'courses', 'services', 'blog', 'articles', 'contact', 'maxfiylik-siyosati', 'ommaviy-oferta', 'sitemap', 'robots', 'storage', 'uploads', 'css', 'js', 'img', 'fonts', 'filament', 'login', 'logout'];

    protected $guarded = ['id'];

    protected $casts = [
        'title' => 'array', 'lead' => 'array', 'content' => 'array', 'seo_title' => 'array', 'seo_description' => 'array',
        'show_consult' => 'boolean', 'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function url(?string $locale = null): string
    {
        return lroute('page', ['page' => $this->slug], $locale);
    }
}
