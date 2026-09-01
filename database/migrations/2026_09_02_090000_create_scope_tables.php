<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scope_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->string('summary')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('scope_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scope_category_id')->constrained()->cascadeOnDelete();
            $table->string('key')->nullable();
            $table->string('title');
            $table->json('columns')->nullable();
            $table->json('rows')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scope_groups');
        Schema::dropIfExists('scope_categories');
    }
};
