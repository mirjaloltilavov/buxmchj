<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Yangi ariza haqida Telegram'ga xabar yuboradi (admin panel → Sozlamalar → Telegram bot).
 * Sozlanmagan bo'lsa hech narsa qilmaydi; ariza baribir bazada saqlanadi.
 */
class LeadNotifier
{
    public function notify(Lead $lead): void
    {
        $token = setting('telegram_bot_token');
        $chat = setting('telegram_chat_id');
        if (blank($token) || blank($chat)) {
            return;
        }

        $text = "🆕 Yangi ariza\n"
            ."👤 {$lead->name}\n"
            ."📞 {$lead->phone}\n"
            .'📌 '.(Lead::INTERESTS[$lead->interest] ?? '—')."\n"
            .($lead->message ? "💬 {$lead->message}\n" : '')
            .'🌐 '.($lead->source ?: '—');

        try {
            Http::timeout(5)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chat,
                'text' => $text,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram notify failed: '.$e->getMessage());
        }
    }
}
