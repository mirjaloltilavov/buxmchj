<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

/**
 * Serverda SSH bo'lmagani uchun bitta buyruq bilan o'rnatish/yangilash:
 *   php artisan site:install
 * Plesk → "Запланированные задачи" → "Выполнить PHP-скрипт" orqali ishga tushiriladi.
 * Takroran ishga tushirish xavfsiz: mavjud kontent va adminlar o'zgarmaydi.
 */
class SiteInstall extends Command
{
    protected $signature = 'site:install {--fresh-admin : ADMIN_PASSWORD bilan admin parolini qayta o‘rnatish}';

    protected $description = 'Migratsiyalar, boshlang‘ich kontent, admin foydalanuvchi va keshlarni tayyorlash';

    public function handle(): int
    {
        if (blank(config('app.key'))) {
            $this->info('APP_KEY yaratilmoqda...');
            Artisan::call('key:generate', ['--force' => true]);
            $this->line(Artisan::output());
            $this->warn('APP_KEY .env ga yozildi. Buyruqni yana bir marta ishga tushiring.');

            return self::SUCCESS;
        }

        $this->info('1/4 Migratsiyalar...');
        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        $this->info('2/4 Boshlang‘ich kontent...');
        (new ContentSeeder)->run();

        $this->info('3/4 Admin foydalanuvchi...');
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        if ($email && $password) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                User::create(['name' => 'Admin', 'username' => User::where('username', 'admin')->exists() ? null : 'admin', 'email' => $email, 'password' => Hash::make($password), 'role' => 'super_admin', 'is_active' => true]);
                $this->line("Admin yaratildi: {$email}");
            } elseif ($this->option('fresh-admin')) {
                $user->update(['password' => Hash::make($password), 'role' => 'super_admin', 'is_active' => true]);
                $this->line("Admin paroli yangilandi: {$email}");
            } else {
                $this->line("Admin allaqachon bor: {$email}");
            }
        } else {
            $this->warn('.env da ADMIN_EMAIL va ADMIN_PASSWORD berilmagan — admin yaratilmadi.');
        }

        // Sayt faolligi: davlatlar bazasi (bir marta) va 400 kundan eski tashriflarni tozalash
        try {
            $old = \App\Models\Visit::where('started_at', '<', now()->subDays(400))->delete();
            if ($old) {
                $this->line("Eski tashriflar o‘chirildi: {$old}");
            }
            if (! \App\Support\Analytics\GeoIp::available()) {
                $this->line('Davlatlar bazasi yuklanmoqda (DB-IP Lite)...');
                @ini_set('memory_limit', '512M');
                [, $msg] = \App\Support\Analytics\GeoIp::update();
                $this->line($msg);
            }
        } catch (\Throwable $e) {
            $this->warn('Sayt faolligi: '.$e->getMessage());
        }

        $this->info('4/4 Keshlar...');
        foreach (['config:cache', 'route:cache', 'view:cache'] as $cmd) {
            Artisan::call($cmd);
        }
        Artisan::call('filament:optimize');
        $this->info('Tayyor.');

        return self::SUCCESS;
    }
}
