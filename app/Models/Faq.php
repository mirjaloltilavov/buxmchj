<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use Translatable;

    protected $guarded = ['id'];

    protected $casts = [
        'pages' => 'array', 'question' => 'array', 'answer' => 'array', 'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function scopeFor(Builder $q, string $page): Builder
    {
        return $q->active()->whereJsonContains('pages', $page);
    }
}
