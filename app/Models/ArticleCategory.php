<?php

namespace App\Models;

use App\Models\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    use Translatable;

    protected $guarded = ['id'];

    protected $casts = ['name' => 'array'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
