<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('articles', 'author')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->string('author')->nullable()->after('category_slug');
            });
        }

        if (! Schema::hasColumn('articles', 'reading_time')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->string('reading_time')->nullable()->after('author');
            });
        }
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['author', 'reading_time']);
        });
    }
};
