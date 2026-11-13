<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Saytga bitta tashrif (sessiya: 30 daqiqa harakatsizlikdan keyin yangisi boshlanadi) */
class Visit extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = ['started_at' => 'datetime', 'last_seen_at' => 'datetime', 'is_new' => 'boolean'];

    public const SOURCE_TYPES = [
        'search' => 'Qidiruv tizimlari', 'social' => 'Ijtimoiy tarmoqlar', 'ads' => 'Reklama (UTM)',
        'referral' => 'Boshqa saytlar', 'direct' => 'To‘g‘ridan-to‘g‘ri',
    ];

    public function views(): HasMany
    {
        return $this->hasMany(Pageview::class);
    }
}
