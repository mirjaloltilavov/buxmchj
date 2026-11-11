<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    public const EVENTS = [
        'created' => 'Qo‘shdi',
        'updated' => 'O‘zgartirdi',
        'deleted' => 'O‘chirdi',
        'login' => 'Tizimga kirdi',
        'settings' => 'Sozlamani o‘zgartirdi',
    ];

    /** Model nomlari (jurnalda ko'rinadi) */
    public const SUBJECTS = [
        Course::class => 'Kurs', Article::class => 'Maqola', ArticleCategory::class => 'Maqola kategoriyasi',
        Service::class => 'Xizmat', ServicePlan::class => 'Xizmat tarifi', Teacher::class => 'Ustoz', Faq::class => 'Savol-javob',
        Graduate::class => 'Bitiruvchi', HeroSlide::class => 'Hero slayd', InstagramPost::class => 'Instagram post',
        Branch::class => 'Filial', Lead::class => 'Ariza', User::class => 'Foydalanuvchi', Icon::class => 'Ikonka', Setting::class => 'Sozlama',
    ];

    protected $guarded = ['id'];

    protected $casts = ['changes' => 'array', 'created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjectName(): string
    {
        return self::SUBJECTS[$this->subject_type] ?? class_basename((string) $this->subject_type);
    }

    public static function record(string $event, $subject = null, ?array $changes = null, ?string $label = null): void
    {
        try {
            static::create([
                'user_id' => auth()->id(),
                'event' => $event,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),
                'subject_label' => $label ?? ($subject ? static::labelFor($subject) : null),
                'changes' => $changes ?: null,
                'ip' => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // jurnal yozilmasa ham asosiy amal to'xtamasin
        }
    }

    public static function labelFor($m): string
    {
        foreach (['title', 'name', 'question', 'label', 'key', 'phone', 'url'] as $f) {
            $v = $m->getAttribute($f);
            if (is_array($v)) {
                $v = $v['uz'] ?? reset($v);
            }
            if (filled($v) && is_scalar($v)) {
                return \Illuminate\Support\Str::limit((string) $v, 80);
            }
        }

        return '#'.$m->getKey();
    }
}
