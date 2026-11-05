<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('body');   // {"uz": ["YaTT", ...], "ru": [...]}
            $table->json('author')->nullable()->after('tags'); // {"uz": "Buxgalter jamoasi", "ru": "..."}
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['tags', 'author']);
        });
    }
};
