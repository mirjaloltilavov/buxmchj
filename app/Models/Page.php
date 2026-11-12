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
        'title' => 'array', 'lead' => 'array', 'content' => 'array', 'sections' => 'array', 'seo_title' => 'array', 'seo_description' => 'array',
        'show_consult' => 'boolean', 'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    /** Admin yaratgan (tizimniki bo'lmagan) sahifalar */
    public function scopeCustom(Builder $q): Builder
    {
        return $q->whereNull('system');
    }

    public function isSystem(): bool
    {
        return filled($this->system);
    }

    /** Tizim sahifasi yozuvi (home, courses, …) — so'rov ichida keshlanadi */
    public static function forSystem(string $key): ?self
    {
        static $cache = [];

        return array_key_exists($key, $cache) ? $cache[$key] : ($cache[$key] = static::where('system', $key)->first());
    }

    /** Saytdagi manzil (kurs va maqola shablonlarida — yo'q) */
    public function publicUrl(?string $locale = null): ?string
    {
        if (! $this->isSystem()) {
            return $this->url($locale);
        }

        return in_array($this->system, ['course', 'article'], true) ? null : lroute($this->system, [], $locale);
    }

    /** Yangi sahifaning boshlang'ich tuzilishi: sarlavha, matn, ariza formasi */
    public static function starterSections(): array
    {
        return [
            ['type' => 'page_head', 'data' => []],
            ['type' => 'text', 'data' => []],
            ['type' => 'consultation', 'data' => ['interest' => 'course']],
        ];
    }

    public function url(?string $locale = null): string
    {
        return lroute('page', ['page' => $this->slug], $locale);
    }
}
