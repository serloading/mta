<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'answer')) {
            Schema::table('services', function (Blueprint $table) {
                $table->text('answer')->nullable()->after('summary');
            });
        }

        if (! Schema::hasColumn('services', 'seo_title')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('seo_title')->nullable()->after('image_alt');
            });
        }

        if (! Schema::hasColumn('services', 'meta_description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->text('meta_description')->nullable()->after('seo_title');
            });
        }

        if (! Schema::hasColumn('services', 'capacity')) {
            Schema::table('services', function (Blueprint $table) {
                $table->json('capacity')->nullable()->after('devices');
            });
        }

        if (! Schema::hasColumn('technical_services', 'answer')) {
            Schema::table('technical_services', function (Blueprint $table) {
                $table->text('answer')->nullable()->after('summary');
            });
        }

        if (! Schema::hasColumn('technical_services', 'seo_title')) {
            Schema::table('technical_services', function (Blueprint $table) {
                $table->string('seo_title')->nullable()->after('image_alt');
            });
        }

        if (! Schema::hasColumn('technical_services', 'meta_description')) {
            Schema::table('technical_services', function (Blueprint $table) {
                $table->text('meta_description')->nullable()->after('seo_title');
            });
        }
    }

    public function down(): void
    {
        Schema::table('technical_services', function (Blueprint $table) {
            $table->dropColumn(['answer', 'seo_title', 'meta_description']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['answer', 'seo_title', 'meta_description', 'capacity']);
        });
    }
};
