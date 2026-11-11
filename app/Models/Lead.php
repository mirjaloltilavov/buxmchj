<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = [
        'new' => 'Yangi',
        'contacted' => 'Bog‘lanildi',
        'done' => 'Yakunlandi',
        'spam' => 'Spam',
    ];

    public const INTERESTS = [
        'course' => 'O‘quv kursi',
        'service' => 'Buxgalteriya xizmati',
        'guide' => 'Bepul qo‘llanma (popup)',
    ];

    protected $casts = ['contacted_at' => 'datetime', 'closed_at' => 'datetime'];

    /** Holat o'zgarganda vaqtlarni avtomatik qo'yadi (statistika uchun) */
    protected static function booted(): void
    {
        static::saving(function (self $lead) {
            if ($lead->isDirty('status')) {
                if ($lead->status !== 'new' && ! $lead->contacted_at) {
                    $lead->contacted_at = now();
                }
                $lead->closed_at = in_array($lead->status, ['done', 'spam'], true) ? ($lead->closed_at ?? now()) : null;
            }
        });
    }

    /** Arizaning manbasi (statistikada ko'rinadi) */
    public function channel(): string
    {
        return static::channelOf($this->utm_source, $this->utm_medium, $this->referrer);
    }

    public static function channelOf(?string $utmSource, ?string $utmMedium, ?string $referrer): string
    {
        if ($utmSource) {
            return $utmSource.($utmMedium ? ' / '.$utmMedium : '');
        }
        if ($referrer) {
            $host = parse_url($referrer, PHP_URL_HOST) ?: $referrer;

            return preg_replace('/^www\./', '', $host);
        }

        return 'To‘g‘ridan-to‘g‘ri';
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Arizalar bilan ishlay oladigan faol foydalanuvchilar */
    public static function staff()
    {
        return User::where('is_active', true)->whereIn('role', ['super_admin', 'admin', 'manager'])->orderBy('name');
    }
}
