<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use Translatable;

    protected $guarded = ['id'];

    protected $casts = [
        'tab_label' => 'array', 'chip' => 'array', 'title' => 'array', 'lead' => 'array',
        'primary_label' => 'array', 'secondary_label' => 'array', 'cards' => 'array', 'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }
}
