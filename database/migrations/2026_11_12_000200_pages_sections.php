<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Qo'shimcha sahifalar ham konstruktor (seksiyalar) bilan yig'iladi */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pages', 'sections')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->json('sections')->nullable()->after('content');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('sections');
        });
    }
};
