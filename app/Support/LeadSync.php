<?php

namespace App\Support;

use App\Models\Lead;
use App\Services\Integrations\AmoCrm;
use App\Services\Integrations\GoogleSheets;
use Filament\Notifications\Notification;

/** Admin paneldan arizalarni qo'lda Google Sheets / amoCRM'ga yuborish */
class LeadSync
{
    public const BATCH = 50;

    public static function toSheets(iterable $leads): void
    {
        $leads = collect($leads);
        if ($leads->isEmpty()) {
            Notification::make()->title('Yuboriladigan ariza yo‘q')->info()->send();

            return;
        }
        [$ok, $msg] = app(GoogleSheets::class)->send($leads->sortBy('id'));
        self::notify($ok, $msg);
    }

    public static function toAmo(iterable $leads): void
    {
        $amo = new AmoCrm;
        $done = 0;
        $errors = [];
        foreach (collect($leads)->sortBy('id') as $l) {
            if ($l->amo_lead_id) {
                continue;
            }
            [$ok, $msg] = $amo->push($l);
            $ok ? $done++ : $errors[] = '#'.$l->id.': '.$msg;
            if (count($errors) >= 3) {
                break; // token xato bo'lsa hammasini urinmaymiz
            }
        }
        self::notify(! $errors, trim(($done ? "{$done} ta ariza amoCRM’ga yuborildi. " : '').implode("\n", $errors)) ?: 'Yuboriladigan ariza yo‘q (hammasi avval yuborilgan).');
    }

    private static function notify(bool $ok, string $msg): void
    {
        $n = Notification::make()->title($ok ? 'Tayyor' : 'Xatolik')->body($msg);
        ($ok ? $n->success() : $n->danger()->persistent())->send();
    }
}
