<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Sayt faolligi: tashriflar (sessiyalar) va sahifa ko'rishlar. IP saqlanmaydi — faqat davlat. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('visits')) {
            Schema::create('visits', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->uuid('visitor')->index();
                $table->boolean('is_new')->default(true);
                $table->dateTime('started_at')->index();
                $table->dateTime('last_seen_at')->index();
                $table->unsignedSmallInteger('pageviews')->default(0);
                $table->unsignedInteger('duration')->default(0);
                $table->string('landing', 255)->nullable();
                $table->string('exit_path', 255)->nullable();
                $table->string('source_type', 20)->default('direct')->index();
                $table->string('source', 80)->nullable()->index();
                $table->string('referrer_host', 120)->nullable();
                $table->string('utm_source', 120)->nullable();
                $table->string('utm_medium', 120)->nullable();
                $table->string('utm_campaign', 160)->nullable();
                $table->char('country', 2)->nullable()->index();
                $table->string('device', 10)->nullable();
                $table->string('browser', 30)->nullable();
                $table->string('os', 30)->nullable();
                $table->string('lang', 5)->nullable();
                $table->string('screen', 12)->nullable();
            });
        }
        if (! Schema::hasTable('pageviews')) {
            Schema::create('pageviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
                $table->string('path', 255)->index();
                $table->string('title', 255)->nullable();
                $table->unsignedInteger('duration')->default(0);
                $table->dateTime('created_at')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pageviews');
        Schema::dropIfExists('visits');
    }
};
