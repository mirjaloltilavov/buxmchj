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
