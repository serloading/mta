<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('editor')->after('password')->index();
                $table->boolean('is_active')->default(true)->after('role');
            });
        }

        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'tags')) {
                $table->json('tags')->nullable()->after('category_slug');
            }

            if (! Schema::hasColumn('articles', 'image_alt')) {
                $table->string('image_alt')->nullable()->after('image');
            }

            if (! Schema::hasColumn('articles', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('image_alt');
                $table->text('meta_description')->nullable()->after('seo_title');
                $table->string('canonical_url')->nullable()->after('meta_description');
                $table->string('og_title')->nullable()->after('canonical_url');
                $table->text('og_description')->nullable()->after('og_title');
                $table->string('og_image')->nullable()->after('og_description');
                $table->string('robots')->default('index,follow')->after('og_image');
            }

            if (! Schema::hasColumn('articles', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('reading_time')->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'gallery')) {
                $table->json('gallery')->nullable()->after('image_alt');
            }

            if (! Schema::hasColumn('products', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('published_at');
                $table->text('meta_description')->nullable()->after('seo_title');
                $table->string('canonical_url')->nullable()->after('meta_description');
                $table->string('og_title')->nullable()->after('canonical_url');
                $table->text('og_description')->nullable()->after('og_title');
                $table->string('og_image')->nullable()->after('og_description');
                $table->string('robots')->default('index,follow')->after('og_image');
            }

            if (! Schema::hasColumn('products', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('robots')->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('pages', function (Blueprint $table) {
            if (! Schema::hasColumn('pages', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('published_at');
                $table->text('meta_description')->nullable()->after('seo_title');
                $table->string('canonical_url')->nullable()->after('meta_description');
                $table->string('og_title')->nullable()->after('canonical_url');
                $table->text('og_description')->nullable()->after('og_title');
                $table->string('og_image')->nullable()->after('og_description');
                $table->string('robots')->default('index,follow')->after('og_image');
            }

            if (! Schema::hasColumn('pages', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('robots')->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('seo_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('seo_entries', 'og_title')) {
                $table->string('og_title')->nullable()->after('canonical_url');
                $table->text('og_description')->nullable()->after('og_title');
            }
        });

        if (! Schema::hasTable('media_assets')) {
            Schema::create('media_assets', function (Blueprint $table) {
                $table->id();
                $table->string('disk')->default('public');
                $table->string('path')->unique();
                $table->string('file_name');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->string('alt_text')->nullable();
                $table->json('metadata')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        collect([
            ['route_name' => 'home', 'path' => '/', 'title' => 'Kalibrasyon Hizmetleri ve Laboratuvar Cihazları | MTA Endüstri'],
            ['route_name' => 'services.index', 'path' => '/hizmetler', 'title' => 'Kalibrasyon Hizmetleri'],
            ['route_name' => 'technical-services.index', 'path' => '/teknik-servis', 'title' => 'Teknik Servis'],
            ['route_name' => 'products.index', 'path' => '/urunler', 'title' => 'Laboratuvar Cihazları ve Teknik Ürün Kataloğu'],
            ['route_name' => 'brands.index', 'path' => '/markalar', 'title' => 'Laboratuvar Cihazı Markaları'],
            ['route_name' => 'blog.index', 'path' => '/blog', 'title' => 'Blog'],
            ['route_name' => 'knowledge.index', 'path' => '/bilgi-merkezi', 'title' => 'Bilgi Merkezi'],
            ['route_name' => 'about', 'path' => '/hakkimizda', 'title' => 'Hakkımızda'],
            ['route_name' => 'certificates', 'path' => '/sertifikalar', 'title' => 'Sertifikalar'],
            ['route_name' => 'references', 'path' => '/referanslar', 'title' => 'Referanslar'],
            ['route_name' => 'contact', 'path' => '/iletisim', 'title' => 'İletişim'],
        ])->each(function (array $entry): void {
            DB::table('seo_entries')->updateOrInsert(
                ['path' => $entry['path']],
                [
                    'route_name' => $entry['route_name'],
                    'title' => $entry['title'],
                    'robots' => 'index,follow',
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');

        foreach (['articles', 'products', 'pages'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $table): void {
                foreach (['tags', 'image_alt', 'seo_title', 'meta_description', 'canonical_url', 'og_title', 'og_description', 'og_image', 'robots', 'gallery', 'created_by', 'updated_by'] as $column) {
                    if (Schema::hasColumn($table->getTable(), $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
