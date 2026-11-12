<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Barcha sahifalar bitta joyda (admin → Sahifalar): saytning asosiy sahifalari ham
 * "tizim sahifasi" sifatida pages jadvaliga yoziladi. Avval "Sahifa tuzilishi"da saqlangan tartib ko'chiriladi.
 */
return new class extends Migration
{
    private const SYSTEM = [
        'home' => ['Bosh sahifa', 'Главная'], 'courses' => ['Kurslar', 'Курсы'], 'services' => ['Buxgalteriya xizmati', 'Бухгалтерские услуги'],
        'contact' => ['Aloqa', 'Контакты'], 'blog' => ['Maqolalar', 'Статьи'],
        'course' => ['Kurs sahifasi (shablon)', 'Страница курса (шаблон)'], 'article' => ['Maqola sahifasi (shablon)', 'Страница статьи (шаблон)'],
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('pages', 'system')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('system', 30)->nullable()->unique()->after('id');
            });
        }
        $old = DB::table('settings')->where('key', 'layouts')->value('value');
        $old = $old ? json_decode($old, true) : [];
        $sort = -100;
        foreach (self::SYSTEM as $key => [$uz, $ru]) {
            if (DB::table('pages')->where('system', $key)->exists()) {
                continue;
            }
            DB::table('pages')->insert([
                'system' => $key, 'slug' => '_'.$key, 'title' => json_encode(['uz' => $uz, 'ru' => $ru], JSON_UNESCAPED_UNICODE),
                'sections' => ! empty($old[$key]) ? json_encode(array_values($old[$key]), JSON_UNESCAPED_UNICODE) : null,
                'is_active' => true, 'show_consult' => false, 'sort' => $sort++, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('pages')->whereNotNull('system')->delete();
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique(['system']);
            $table->dropColumn('system');
        });
    }
};
