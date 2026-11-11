<?php

namespace App\Services;

use App\Mail\LeadReceived;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Yangi ariza haqida xabar: Telegram, email, Google Sheets va amoCRM (admin panel → Sayt sozlamalari → Arizalar xabarnomasi).
 * Sozlanmagan kanal o'tkazib yuboriladi; ariza baribir bazada saqlanadi.
 */
class LeadNotifier
{
    public function notify(Lead $lead): void
    {
        if ($this->enabled('notify_tg_enabled') && $this->telegramReady()) {
            $this->telegram($this->leadText($lead));
        }
        if ($this->enabled('notify_mail_enabled') && $this->mailRecipients()) {
            try {
                $this->mailer()->to($this->mailRecipients())->send($this->withFrom(new LeadReceived($lead)));
            } catch (\Throwable $e) {
                Log::warning('Lead email failed: '.$e->getMessage());
            }
        }
        $sheets = app(\App\Services\Integrations\GoogleSheets::class);
        if ($sheets->enabled()) {
            [$ok, $msg] = $sheets->send([$lead]);
            $ok || Log::warning('Lead → Google Sheets: '.$msg);
        }
        $amo = new \App\Services\Integrations\AmoCrm;
        if ($amo->enabled()) {
            [$ok, $msg] = $amo->push($lead);
            $ok || Log::warning('Lead → amoCRM: '.$msg);
        }
    }

    /** Admin paneldagi "Sinov xabari" tugmasi uchun: [muvaffaqiyat, izoh] */
    public function testTelegram(): array
    {
        if (! $this->telegramReady()) {
            return [false, 'Bot token va Chat ID kiritilmagan.'];
        }
        $errors = $this->telegram("✅ Sinov xabari: Buxgalter sayti arizalari shu yerga keladi.\n🕒 ".now()->format('d.m.Y H:i'));

        return $errors ? [false, implode("\n", $errors)] : [true, 'Telegram’ga sinov xabari yuborildi.'];
    }

    public function testMail(): array
    {
        $to = $this->mailRecipients();
        if (! $to) {
            return [false, 'Qabul qiluvchi email kiritilmagan.'];
        }
        try {
            $sample = new Lead(['name' => 'Sinov Mijoz', 'phone' => '+998 90 000 00 00', 'interest' => 'course', 'message' => 'Bu sinov xabari. Hammasi to‘g‘ri sozlangan.', 'source' => 'admin']);
            $sample->created_at = now();
            $this->mailer()->to($to)->send($this->withFrom(new LeadReceived($sample, true)));

            return [true, 'Sinov xati yuborildi: '.implode(', ', $to)];
        } catch (\Throwable $e) {
            return [false, 'Email yuborilmadi: '.$e->getMessage()];
        }
    }

    private function enabled(string $key): bool
    {
        $v = setting($key);

        return $v === null ? true : (bool) $v;
    }

    private function telegramReady(): bool
    {
        return filled(setting('telegram_bot_token')) && filled(setting('telegram_chat_id'));
    }

    /** @return array<string> xatolar */
    private function telegram(string $text): array
    {
        $token = trim((string) setting('telegram_bot_token'));
        $errors = [];
        foreach (preg_split('/[\s,;]+/', (string) setting('telegram_chat_id'), -1, PREG_SPLIT_NO_EMPTY) as $chat) {
            try {
                $r = Http::timeout(8)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", ['chat_id' => $chat, 'text' => $text]);
                if (! $r->ok()) {
                    $errors[] = "Chat {$chat}: ".($r->json('description') ?: 'xato '.$r->status());
                }
            } catch (\Throwable $e) {
                $errors[] = "Chat {$chat}: ".$e->getMessage();
                Log::warning('Telegram notify failed: '.$e->getMessage());
            }
        }

        return $errors;
    }

    private function leadText(Lead $lead): string
    {
        return "🆕 Yangi ariza\n"
            ."👤 {$lead->name}\n"
            ."📞 {$lead->phone}\n"
            .'📌 '.(Lead::INTERESTS[$lead->interest] ?? '—')."\n"
            .($lead->message ? "💬 {$lead->message}\n" : '')
            .'🌐 '.($lead->source ?: '—')."\n"
            .'📈 '.$lead->channel()."\n"
            .'🔗 '.url('/admin/leads');
    }

    /** @return array<string> */
    private function mailRecipients(): array
    {
        return array_values(array_filter(preg_split('/[\s,;]+/', (string) setting('notify_mail_to'), -1, PREG_SPLIT_NO_EMPTY), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    }

    private function withFrom(LeadReceived $mail): LeadReceived
    {
        $address = setting('mail_from_address') ?: (setting('mail_method') === 'smtp' && filter_var(setting('smtp_username'), FILTER_VALIDATE_EMAIL) ? setting('smtp_username') : null)
            ?: config('mail.from.address') ?: 'noreply@'.preg_replace('/^www\./', '', (string) parse_url(config('app.url'), PHP_URL_HOST) ?: 'buxmchj.uz');
        if (! str_contains($address, '.') || str_ends_with($address, '@127.0.0.1') || str_ends_with($address, '@localhost')) {
            $address = 'noreply@buxmchj.uz';
        }

        return $mail->from($address, setting('mail_from_name') ?: setting('site_name', 'Buxgalter MChJ'));
    }

    /** Admin'da SMTP berilgan bo'lsa o'sha bilan, aks holda serverning standart pochtasi (sendmail) bilan */
    private function mailer()
    {
        if (setting('mail_method') === 'smtp' && filled(setting('smtp_host'))) {
            $enc = setting('smtp_encryption');

            return Mail::build([
                'transport' => 'smtp',
                'scheme' => $enc === 'ssl' ? 'smtps' : 'smtp',
                'host' => setting('smtp_host'),
                'port' => (int) (setting('smtp_port') ?: ($enc === 'ssl' ? 465 : 587)),
                'username' => setting('smtp_username'),
                'password' => setting('smtp_password'),
                'timeout' => 15,
            ]);
        }

        return Mail::mailer(in_array(config('mail.default'), ['log', 'array'], true) ? 'sendmail' : config('mail.default'));
    }
}
