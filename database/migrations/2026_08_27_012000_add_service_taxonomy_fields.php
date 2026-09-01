<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('services', 'category')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('category')->nullable()->after('slug');
            });
        }

        if (! Schema::hasColumn('services', 'scope')) {
            Schema::table('services', function (Blueprint $table) {
                $table->json('scope')->nullable()->after('body');
            });
        }

        if (! Schema::hasColumn('services', 'applications')) {
            Schema::table('services', function (Blueprint $table) {
                $table->json('applications')->nullable()->after('devices');
            });
        }

        if (! Schema::hasColumn('services', 'standards')) {
            Schema::table('services', function (Blueprint $table) {
                $table->json('standards')->nullable()->after('applications');
            });
        }

        if (! Schema::hasColumn('technical_services', 'category')) {
            Schema::table('technical_services', function (Blueprint $table) {
                $table->string('category')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        Schema::table('technical_services', function (Blueprint $table) {
            $table->dropColumn(['category']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['category', 'scope', 'applications', 'standards']);
        });
    }
};
