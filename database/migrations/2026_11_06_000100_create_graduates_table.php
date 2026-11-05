<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('subtitle')->nullable(); // "BH-36 guruhi · ish joyi"
            $table->string('photo')->nullable();
            $table->string('video_url')->nullable(); // YouTube havola
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduates');
    }
};
