<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_service')) {
            Schema::create('product_service', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_id', 'service_id'], 'product_service_unique');
            });
        }

        if (! Schema::hasTable('product_videos')) {
            Schema::create('product_videos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('youtube_url')->nullable();
                $table->string('youtube_id')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_videos');
        Schema::dropIfExists('product_service');
    }
};
