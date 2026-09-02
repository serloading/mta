<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductDocument;
use App\Models\ProductVideo;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TechnicalService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_core_admin_modules(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        foreach ([
            '/admin',
            '/admin/articles',
            '/admin/products',
            '/admin/product-categories',
            '/admin/product-brands',
            '/admin/services',
            '/admin/technical-services',
            '/admin/faqs',
            '/admin/leads',
            '/admin/redirects',
            '/admin/pages',
            '/admin/seo-entries',
            '/admin/schema-definitions',
            '/admin/site-settings',
            '/admin/media-assets',
            '/admin/users',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_editor_cannot_manage_users(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
    }

    public function test_contact_form_creates_lead(): void
    {
        $this->post('/iletisim', [
            'name' => 'Test Kullanıcı',
            'company' => 'MTA Test',
            'phone' => '+90 555 111 22 33',
            'email' => 'test@example.com',
            'message' => 'Ürün için teklif almak istiyorum.',
            'website' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'name' => 'Test Kullanıcı',
            'status' => 'new',
        ]);
    }

    public function test_quote_form_keeps_product_context_on_lead(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Teraziler',
            'slug' => 'teraziler',
            'is_active' => true,
        ]);
        $brand = ProductBrand::query()->create([
            'name' => 'A&D',
            'slug' => 'and',
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'product_brand_id' => $brand->id,
            'name' => 'Teklif Test Ürünü',
            'slug' => 'teklif-test-urunu',
            'summary' => 'Teklif test özeti.',
            'features' => ['Test özellik'],
            'specs' => ['Kapasite' => '520 g'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/teklif-al?product=teklif-test-urunu')
            ->assertOk()
            ->assertSee('Teklif Test Ürünü')
            ->assertSee('name="product" value="teklif-test-urunu"', false);

        $this->post('/teklif-al', [
            'name' => 'Ürün Talebi',
            'phone' => '+90 555 111 22 33',
            'email' => 'urun@example.com',
            'message' => 'Bu ürün için teklif istiyorum.',
            'product' => 'teklif-test-urunu',
            'source_type' => 'product',
            'source_name' => 'Teklif Test Ürünü',
            'website' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'name' => 'Ürün Talebi',
            'product_id' => $product->id,
            'source_type' => 'product',
        ]);
    }

    public function test_quote_form_keeps_service_and_technical_service_contexts(): void
    {
        $service = Service::query()->create([
            'title' => 'Basınç Kalibrasyonu',
            'slug' => 'basinc-kalibrasyonu',
            'summary' => 'Basınç özeti.',
            'answer' => 'Basınç cevabı.',
            'body' => 'Basınç açıklaması.',
            'is_active' => true,
        ]);
        $technicalService = TechnicalService::query()->create([
            'title' => 'Terazi Teknik Servis',
            'slug' => 'terazi-teknik-servis',
            'summary' => 'Servis özeti.',
            'answer' => 'Servis cevabı.',
            'body' => 'Servis açıklaması.',
            'is_active' => true,
        ]);

        $this->post('/teklif-al', [
            'name' => 'Hizmet Talebi',
            'phone' => '+90 555 111 22 33',
            'message' => 'Hizmet teklifi istiyorum.',
            'service' => 'basinc-kalibrasyonu',
            'source_type' => 'service',
            'website' => '',
        ])->assertRedirect();

        $this->post('/teklif-al', [
            'name' => 'Servis Talebi',
            'phone' => '+90 555 111 22 44',
            'message' => 'Servis teklifi istiyorum.',
            'technical_service' => 'terazi-teknik-servis',
            'source_type' => 'technical_service',
            'website' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'name' => 'Hizmet Talebi',
            'service_id' => $service->id,
            'source_type' => 'service',
        ]);
        $this->assertDatabaseHas('leads', [
            'name' => 'Servis Talebi',
            'technical_service_id' => $technicalService->id,
            'source_type' => 'technical_service',
        ]);
    }

    public function test_redirect_fallback_uses_active_redirects(): void
    {
        \App\Models\Redirect::query()->create([
            'source_path' => '/eski-url',
            'target_path' => '/urunler',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $this->get('/eski-url')->assertRedirect('/urunler');
    }

    public function test_product_page_renders_gallery_video_and_pdf_documents(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Teraziler',
            'slug' => 'teraziler',
            'is_active' => true,
        ]);
        $brand = ProductBrand::query()->create([
            'name' => 'A&D',
            'slug' => 'and',
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'product_brand_id' => $brand->id,
            'name' => 'Test Hassas Terazi',
            'slug' => 'test-hassas-terazi',
            'summary' => 'Test ürün özeti.',
            'image' => 'media/products/test.webp',
            'gallery' => ['media/products/gallery/test-1.webp', 'media/products/gallery/test-2.webp'],
            'features' => ['0.001 g okunabilirlik'],
            'specs' => ['Kapasite' => '520 g'],
            'status' => 'published',
            'published_at' => now(),
        ]);
        ProductDocument::query()->create([
            'product_id' => $product->id,
            'title' => 'PDF Katalog',
            'type' => 'catalog',
            'path' => 'media/documents/test-katalog.pdf',
        ]);
        ProductVideo::query()->create([
            'product_id' => $product->id,
            'title' => 'Ürün Tanıtım Videosu',
            'youtube_url' => 'https://www.youtube.com/watch?v=N0AZOzy5ATo',
        ]);

        $this->get('/urun/test-hassas-terazi')
            ->assertOk()
            ->assertSee('product-gallery-strip', false)
            ->assertSee('Kataloğu İncele')
            ->assertSee('storage/media/documents/test-katalog.pdf', false)
            ->assertSee('Dokümanlar ve PDF Kataloglar')
            ->assertSee('PDF Katalog')
            ->assertSee('youtube-nocookie.com/embed/N0AZOzy5ATo', false);
    }

    public function test_product_page_renders_admin_schema_blocks(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Teraziler',
            'slug' => 'teraziler',
            'is_active' => true,
        ]);
        $brand = ProductBrand::query()->create([
            'name' => 'A&D',
            'slug' => 'and',
            'is_active' => true,
        ]);

        Product::query()->create([
            'product_category_id' => $category->id,
            'product_brand_id' => $brand->id,
            'name' => 'Schema Test Ürünü',
            'slug' => 'schema-test-urunu',
            'summary' => 'Schema test özeti.',
            'status' => 'published',
            'published_at' => now(),
            'schema_blocks' => [
                [
                    'type' => 'video',
                    'name' => 'Admin seçili ürün videosu',
                    'is_active' => true,
                    'payload' => [
                        'embedUrl' => 'https://www.youtube-nocookie.com/embed/SCHEMA123',
                        'description' => 'Admin panelinden eklenen video schema açıklaması.',
                    ],
                ],
            ],
        ]);

        $this->get('/urun/schema-test-urunu')
            ->assertOk()
            ->assertSee('VideoObject')
            ->assertSee('Admin seçili ürün videosu')
            ->assertSee('SCHEMA123')
            ->assertDontSee('Kataloğu İncele');
    }

    public function test_layout_uses_admin_social_links(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'social_links'],
            [
                'group' => 'site',
                'value' => [
                    'links' => [
                        ['name' => 'LinkedIn', 'label' => 'in', 'url' => 'https://example.com/mta-linkedin'],
                    ],
                ],
            ],
        );

        $this->get('/')
            ->assertOk()
            ->assertSee('https://example.com/mta-linkedin', false);
    }

    public function test_admin_category_and_brand_images_render_on_catalog_pages(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Teraziler',
            'slug' => 'teraziler',
            'summary' => 'Admin kategori özeti.',
            'image' => 'media/categories/admin-teraziler.webp',
            'is_active' => true,
        ]);
        $brand = ProductBrand::query()->create([
            'name' => 'A&D',
            'slug' => 'and',
            'summary' => 'Admin marka özeti.',
            'logo' => 'media/brands/admin-and.webp',
            'is_active' => true,
        ]);
        Product::query()->create([
            'product_category_id' => $category->id,
            'product_brand_id' => $brand->id,
            'name' => 'Görsel Test Ürünü',
            'slug' => 'gorsel-test-urunu',
            'summary' => 'Görsel test özeti.',
            'features' => ['Test özellik'],
            'specs' => ['Kapasite' => '520 g'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        // /urunler artık filtreli ürün kataloğu: marka logo ribbon + ürün kartları
        $this->get('/urunler')
            ->assertOk()
            ->assertSee('Tüm Ürün Kataloğu')
            ->assertSee('storage/media/brands/admin-and.webp', false)
            ->assertSee('Görsel Test Ürünü');

        $this->get('/urunler/teraziler')
            ->assertOk()
            ->assertSee('storage/media/categories/admin-teraziler.webp', false);

        $this->get('/urunler/marka/and')
            ->assertOk()
            ->assertSee('storage/media/brands/admin-and.webp', false);

        $this->get('/markalar')
            ->assertOk()
            ->assertSee('Katalogda Yer Alan Markalar', false)
            ->assertSee('max-w-[1320px]', false)
            ->assertSee(route('products.brand', 'and'), false)
            ->assertDontSee('taxonomy-card', false)
            ->assertDontSee('card-kicker">1 ürün', false);
    }

    public function test_product_category_pages_use_filtered_catalog_layout(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Viskozimetre',
            'slug' => 'viskozimetre',
            'summary' => 'Viskozimetre kategori özeti.',
            'is_active' => true,
        ]);
        $velp = ProductBrand::query()->create([
            'name' => 'VELP',
            'slug' => 'velp',
            'logo' => 'media/brands/velp.webp',
            'is_active' => true,
        ]);
        $lamy = ProductBrand::query()->create([
            'name' => 'Lamy',
            'slug' => 'lamy',
            'logo' => 'media/brands/lamy.webp',
            'is_active' => true,
        ]);

        foreach ([$velp, $lamy] as $brand) {
            Product::query()->create([
                'product_category_id' => $category->id,
                'product_brand_id' => $brand->id,
                'name' => $brand->name . ' Test Viskozimetre',
                'slug' => Str::slug($brand->name . ' Test Viskozimetre'),
                'summary' => 'Viskozimetre ürün özeti.',
                'model' => 'MODEL-SHOULD-HIDE-' . $brand->slug,
                'sku' => 'SKU-SHOULD-HIDE-' . $brand->slug,
                'features' => ['Test özellik'],
                'specs' => ['Ölçüm aralığı' => '10-1000 cP'],
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        // Kategori sayfası artık /urunler ile aynı filtreli katalog partial'ını kullanır
        $this->get('/urunler/viskozimetre')
            ->assertOk()
            ->assertSee('data-catalog', false)
            ->assertSee('data-catalog-filter', false)
            ->assertSee('catalog-card', false)
            ->assertSee('İncele / Teklif İste')
            ->assertSee('VELP Test Viskozimetre')
            // kategori sabitken sol panelde "Kategoriler" bloğu görünmez, markalar görünür
            ->assertSee('Markalar')
            // dahili placeholder model/sku kartlara sızmamalı
            ->assertDontSee('MODEL-SHOULD-HIDE')
            ->assertDontSee('SKU-SHOULD-HIDE')
            // eski taksonomi/cui yapısı kalmadı
            ->assertDontSee('cui-hero', false)
            ->assertDontSee('catalog-sidebar', false)
            ->assertDontSee('taxonomy-stats', false);

        // Marka sayfası da aynı katalog + marka logosu hero'da
        $this->get('/urunler/marka/velp')
            ->assertOk()
            ->assertSee('data-catalog', false)
            ->assertSee('storage/media/brands/velp.webp', false)
            ->assertSee('VELP Test Viskozimetre');
    }
}
