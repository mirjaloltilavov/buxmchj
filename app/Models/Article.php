<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use Translatable;

    protected $guarded = ['id'];

    protected $casts = [
        'title' => 'array', 'excerpt' => 'array', 'body' => 'array', 'tags' => 'array', 'author' => 'array',
        'seo_title' => 'array', 'seo_description' => 'array',
        'published_at' => 'datetime', 'is_featured' => 'boolean', 'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->orderByDesc('published_at');
    }

    /** "18 oktyabr 2026" / "18 октября 2026" */
    public function dateLabel(): string
    {
        if (! $this->published_at) {
            return '';
        }
        $months = app()->getLocale() === 'ru'
            ? ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря']
            : ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentyabr', 'oktyabr', 'noyabr', 'dekabr'];

        return $this->published_at->day.' '.$months[$this->published_at->month - 1].' '.$this->published_at->year;
    }
}
