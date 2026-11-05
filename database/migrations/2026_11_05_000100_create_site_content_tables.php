<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sayt kontenti uchun jadvallar. Matnli maydonlar JSON: {"uz": "...", "ru": "..."}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('theme')->default('courses'); // courses | services
            $table->json('tab_label');
            $table->json('chip')->nullable();
            $table->json('title');
            $table->json('lead')->nullable();
            $table->json('primary_label')->nullable();
            $table->string('primary_url')->nullable();
            $table->json('secondary_label')->nullable();
            $table->string('secondary_url')->nullable();
            $table->string('image')->nullable();
            $table->json('cards')->nullable(); // suzuvchi kartochkalar matni
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->json('price_note')->nullable();
            $table->json('tags')->nullable();      // [{icon, text:{uz,ru}, accent}]
            $table->json('audience')->nullable();  // kurs kimlar uchun
            $table->json('program')->nullable();   // modullar
            $table->json('outcomes')->nullable();  // kurs yakunida
            $table->json('plans')->nullable();     // tariflar
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('calculator');
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_plans', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('subtitle')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->string('cover')->nullable();
            $table->unsignedSmallInteger('read_minutes')->default(5);
            $table->timestamp('published_at')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('role')->nullable();
            $table->json('bio')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->json('pages')->nullable(); // ['home','courses','services','contact']
            $table->json('question');
            $table->json('answer');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('url')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('address');
            $table->string('phone')->nullable();
            $table->json('hours')->nullable();
            $table->string('map_url')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('interest')->nullable(); // course | service
            $table->text('message')->nullable();
            $table->string('source')->nullable();   // qaysi sahifadan
            $table->string('locale', 5)->nullable();
            $table->string('status')->default('new'); // new | contacted | done | spam
            $table->text('note')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['leads', 'branches', 'instagram_posts', 'faqs', 'teachers', 'articles', 'article_categories', 'service_plans', 'services', 'courses', 'hero_slides', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
