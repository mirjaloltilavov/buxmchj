<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('icons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();      // masalan: calculator, hi-academic-cap, my-star
            $table->string('label')->nullable();   // admin'da ko'rinadigan nom
            $table->string('group')->default('custom'); // site | heroicons | custom
            $table->longText('svg');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('icons');
    }
};
