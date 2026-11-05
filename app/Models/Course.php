<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use Translatable;

    protected $guarded = ['id'];

    protected $casts = [
        'title' => 'array', 'excerpt' => 'array', 'description' => 'array', 'price_note' => 'array',
        'tags' => 'array', 'audience' => 'array', 'program' => 'array', 'outcomes' => 'array', 'plans' => 'array',
        'seo_title' => 'array', 'seo_description' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
