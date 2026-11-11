<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Panelga login (username) bilan kirish: email ixtiyoriy bo'ladi */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 60)->nullable()->unique()->after('name');
            });
        }
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // Mavjud foydalanuvchilarga login: email'ning @ gacha qismi (band bo'lsa raqam qo'shiladi)
        foreach (\Illuminate\Support\Facades\DB::table('users')->whereNull('username')->get(['id', 'email']) as $u) {
            $base = preg_replace('/[^A-Za-z0-9._-]/', '', strstr((string) $u->email, '@', true) ?: 'user') ?: 'user';
            $name = $base;
            for ($i = 2; \Illuminate\Support\Facades\DB::table('users')->where('username', $name)->exists(); $i++) {
                $name = $base.$i;
            }
            \Illuminate\Support\Facades\DB::table('users')->where('id', $u->id)->update(['username' => $name]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
