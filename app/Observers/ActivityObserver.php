<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/** Kontent o'zgarishlarini jurnalga yozadi (faqat admin paneldan — tizimga kirgan foydalanuvchi bo'lsa). */
class ActivityObserver
{
    private const HIDDEN = ['password', 'remember_token', 'updated_at', 'created_at', 'last_login_at'];

    public function created(Model $m): void
    {
        if (auth()->check()) {
            ActivityLog::record('created', $m);
        }
    }

    public function updated(Model $m): void
    {
        if (! auth()->check()) {
            return;
        }
        $changes = [];
        foreach ($m->getChanges() as $k => $new) {
            if (in_array($k, self::HIDDEN, true)) {
                if ($k === 'password') {
                    $changes['password'] = ['old' => '••••', 'new' => 'yangilandi'];
                }
                continue;
            }
            $changes[$k] = ['old' => $m->getOriginal($k), 'new' => $m->getAttribute($k)];
        }
        if ($changes) {
            ActivityLog::record('updated', $m, $changes);
        }
    }

    public function deleted(Model $m): void
    {
        if (auth()->check()) {
            ActivityLog::record('deleted', $m);
        }
    }
}
