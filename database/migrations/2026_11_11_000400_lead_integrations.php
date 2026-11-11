<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Arizaning Google Sheets va amoCRM'ga yuborilgani */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'sheets_synced_at')) {
                $table->timestamp('sheets_synced_at')->nullable();
            }
            if (! Schema::hasColumn('leads', 'amo_lead_id')) {
                $table->unsignedBigInteger('amo_lead_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['sheets_synced_at', 'amo_lead_id']);
        });
    }
};
