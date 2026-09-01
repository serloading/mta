<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('schema_definitions')) {
            Schema::create('schema_definitions', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('key')->unique();
                $table->string('schema_type');
                $table->text('description')->nullable();
                $table->json('applicable_to')->nullable();
                $table->json('default_payload')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('key')->unique();
                $table->string('group')->nullable()->index();
                $table->json('value')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        foreach (['articles', 'products', 'pages', 'seo_entries', 'services', 'technical_services'] as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'schema_blocks')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $afterColumn = match ($tableName) {
                    'seo_entries' => Schema::hasColumn($tableName, 'schema_payload') ? 'schema_payload' : 'robots',
                    default => Schema::hasColumn($tableName, 'robots') ? 'robots' : 'meta_description',
                };

                $table->json('schema_blocks')->nullable()->after($afterColumn);
            });
        }

        $schemas = [
            [
                'name' => 'Kuruluş Bilgisi',
                'key' => 'organization',
                'schema_type' => 'Organization',
                'description' => 'Firma adı, iletişim ve adres bilgisini arama motorlarına açıklar.',
                'applicable_to' => ['global', 'page'],
                'default_payload' => ['name' => 'MTA Endüstri', 'url' => config('app.url')],
                'sort_order' => 10,
            ],
            [
                'name' => 'Web Sitesi',
                'key' => 'website',
                'schema_type' => 'WebSite',
                'description' => 'Site adı, adresi ve genel arama bağlamı için kullanılır.',
                'applicable_to' => ['global', 'page'],
                'default_payload' => ['name' => 'MTA Endüstri', 'url' => config('app.url')],
                'sort_order' => 20,
            ],
            [
                'name' => 'Web Sayfası',
                'key' => 'webpage',
                'schema_type' => 'WebPage',
                'description' => 'Kurumsal ve açılış sayfalarının başlık, açıklama ve URL bilgisini taşır.',
                'applicable_to' => ['page', 'service', 'technical_service'],
                'default_payload' => ['name' => '', 'description' => '', 'url' => ''],
                'sort_order' => 30,
            ],
            [
                'name' => 'Ürün',
                'key' => 'product',
                'schema_type' => 'Product',
                'description' => 'Ürün adı, marka, görsel, teknik özellik ve ilgili hizmetleri destekler.',
                'applicable_to' => ['product'],
                'default_payload' => ['name' => '', 'brand' => '', 'sku' => '', 'description' => ''],
                'sort_order' => 40,
            ],
            [
                'name' => 'Blog Yazısı',
                'key' => 'article',
                'schema_type' => 'Article',
                'description' => 'Blog ve bilgi merkezi içeriklerinin yazar, tarih ve başlık bilgisini taşır.',
                'applicable_to' => ['article'],
                'default_payload' => ['headline' => '', 'description' => '', 'author' => 'MTA Endüstri'],
                'sort_order' => 50,
            ],
            [
                'name' => 'Hizmet',
                'key' => 'service',
                'schema_type' => 'Service',
                'description' => 'Kalibrasyon veya teknik servis hizmetlerinin kapsamını açıklar.',
                'applicable_to' => ['service', 'technical_service'],
                'default_payload' => ['name' => '', 'description' => '', 'provider' => 'MTA Endüstri'],
                'sort_order' => 60,
            ],
            [
                'name' => 'Sık Sorulan Sorular',
                'key' => 'faq',
                'schema_type' => 'FAQPage',
                'description' => 'Sayfada yayınlanan soru-cevap blokları için kullanılır.',
                'applicable_to' => ['page', 'product', 'article', 'service', 'technical_service'],
                'default_payload' => ['name' => '', 'description' => ''],
                'sort_order' => 70,
            ],
            [
                'name' => 'Video',
                'key' => 'video',
                'schema_type' => 'VideoObject',
                'description' => 'YouTube ürün veya hizmet videoları için ek video bilgisi sağlar.',
                'applicable_to' => ['product', 'article', 'service', 'technical_service'],
                'default_payload' => ['name' => '', 'embedUrl' => '', 'description' => ''],
                'sort_order' => 80,
            ],
            [
                'name' => 'Görsel',
                'key' => 'image',
                'schema_type' => 'ImageObject',
                'description' => 'Önemli görseller için URL, açıklama ve alternatif metin bağlamı verir.',
                'applicable_to' => ['page', 'product', 'article', 'service', 'technical_service'],
                'default_payload' => ['url' => '', 'caption' => ''],
                'sort_order' => 90,
            ],
            [
                'name' => 'Yerel Firma',
                'key' => 'local_business',
                'schema_type' => 'LocalBusiness',
                'description' => 'Adres, telefon ve çalışma alanı olan firma sayfaları için uygundur.',
                'applicable_to' => ['global', 'page'],
                'default_payload' => ['name' => 'MTA Endüstri', 'telephone' => config('mta.site.phone')],
                'sort_order' => 100,
            ],
            [
                'name' => 'Ekmek Kırıntısı',
                'key' => 'breadcrumb',
                'schema_type' => 'BreadcrumbList',
                'description' => 'Sayfa hiyerarşisini arama motorlarına aktarır.',
                'applicable_to' => ['page', 'product', 'article', 'service', 'technical_service'],
                'default_payload' => ['name' => '', 'description' => ''],
                'sort_order' => 110,
            ],
        ];

        foreach ($schemas as $schema) {
            DB::table('schema_definitions')->updateOrInsert(
                ['key' => $schema['key']],
                [
                    'name' => $schema['name'],
                    'schema_type' => $schema['schema_type'],
                    'description' => $schema['description'],
                    'applicable_to' => json_encode($schema['applicable_to'], JSON_UNESCAPED_UNICODE),
                    'default_payload' => json_encode($schema['default_payload'], JSON_UNESCAPED_UNICODE),
                    'is_active' => true,
                    'sort_order' => $schema['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'social_links'],
            [
                'group' => 'site',
                'value' => json_encode([
                    'links' => config('mta.site.social_links', []),
                ], JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'tracking_codes'],
            [
                'group' => 'site',
                'value' => json_encode([
                    'verification_meta' => '',
                    'head_scripts' => '',
                    'body_start_scripts' => '',
                    'body_end_scripts' => '',
                ], JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        foreach (['articles', 'products', 'pages', 'seo_entries', 'services', 'technical_services'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'schema_blocks')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('schema_blocks');
            });
        }

        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('schema_definitions');
    }
};
