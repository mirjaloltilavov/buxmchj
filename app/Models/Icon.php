<?php

namespace App\Models;

use App\Support\IconSvg;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Icon extends Model
{
    public const CACHE_KEY = 'icons.map';

    protected $guarded = ['id'];
    protected $casts = ['is_system' => 'boolean'];

    /** name => svg (keshlanadi) */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return static::query()->pluck('svg', 'name')->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    protected static function booted(): void
    {
        static::saving(fn (self $i) => $i->svg = IconSvg::clean($i->svg));
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
