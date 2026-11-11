<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('utm_source', 120)->nullable()->after('source');
            $table->string('utm_medium', 120)->nullable()->after('utm_source');
            $table->string('utm_campaign', 160)->nullable()->after('utm_medium');
            $table->string('referrer', 255)->nullable()->after('utm_campaign');
            $table->timestamp('contacted_at')->nullable()->after('status');
            $table->timestamp('closed_at')->nullable()->after('contacted_at');
            $table->index(['created_at', 'status']);
        });
        // mavjud arizalar: taxminiy vaqtlar (statistika bo'sh qolmasin)
        DB::table('leads')->where('status', '!=', 'new')->whereNull('contacted_at')->update(['contacted_at' => DB::raw('updated_at')]);
        DB::table('leads')->whereIn('status', ['done', 'spam'])->whereNull('closed_at')->update(['closed_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['created_at', 'status']);
            $table->dropColumn(['utm_source', 'utm_medium', 'utm_campaign', 'referrer', 'contacted_at', 'closed_at']);
        });
    }
};
