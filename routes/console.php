<?php

use App\Support\WpProductImport;
use App\Models\Article;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\TechnicalService;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mta:create-admin
    {email : Admin e-posta adresi}
    {--name=Site Admin : Admin adı}
    {--password= : Şifre; boş bırakılırsa gizli olarak sorulur}', function () {
        $password = $this->option('password') ?: $this->secret('Admin şifresi');

        if (! $password || mb_strlen($password) < 10) {
            $this->error('Admin şifresi en az 10 karakter olmalı.');

            return self::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name' => $this->option('name'),
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $this->info("Admin hesabı hazır: {$user->email}");

        return self::SUCCESS;
    })->purpose('MTA admin paneli için bilinçli şekilde Admin hesabı oluşturur veya aktif eder');

Artisan::command('mta:import-products
    {--source= : WordPress XML export path}
    {--output=storage/app/imports/mta-products-normalized.json : Normalized product JSON path}
    {--report=storage/app/imports/mta-products-import-report.json : Import audit report JSON path}
    {--limit= : Limit included products}
    {--include-drafts : Include non-published WordPress products}', function () {
        $source = $this->option('source') ?: base_path('../mtaendstri.WordPress.2026-08-26.xml');
        $output = base_path($this->option('output'));
        $report = base_path($this->option('report'));
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;

        if (! is_file($source)) {
            $this->error("WordPress XML bulunamadı: {$source}");

            return self::FAILURE;
        }

        foreach ([$output, $report] as $path) {
            $directory = dirname($path);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        $import = new WpProductImport($source, public_path('images/products'));
        $result = $import->run((bool) $this->option('include-drafts'), $limit);

        file_put_contents(
            $output,
            json_encode($result['products'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        file_put_contents(
            $report,
            json_encode([
                'summary' => $result['summary'],
                'excluded' => $result['excluded'],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->info('WordPress ürün exportu tarandı.');
        $this->line('XML içindeki ürün: ' . $result['summary']['total_products_in_xml']);
        $this->line('Alınan ürün: ' . $result['summary']['included_products']);
        $this->line('Dışarıda kalan ürün: ' . $result['summary']['excluded_products']);

        foreach ($result['summary']['excluded_reason_counts'] as $reason => $count) {
            $this->line(" - {$reason}: {$count}");
        }

        $this->line("Ürün JSON: {$output}");
        $this->line("Rapor JSON: {$report}");

        return self::SUCCESS;
    })
    ->purpose('WordPress XML ürünlerini MTA kategori-marka kuralına göre süzer ve raporlar');

Artisan::command('mta:sync-content', function () {
    $services = collect(config('mta.services', []))->mapWithKeys(function ($item, $index) {
        $service = Service::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [
                'title' => $item['title'],
                'category' => $item['category'] ?? 'Kalibrasyon Hizmetleri',
                'eyebrow' => $item['eyebrow'] ?? null,
                'summary' => $item['summary'] ?? null,
                'answer' => $item['answer'] ?? null,
                'body' => $item['body'] ?? null,
                'scope' => $item['scope'] ?? null,
                'image' => $item['image'] ?? null,
                'image_alt' => $item['image_alt'] ?? null,
                'seo_title' => $item['seo_title'] ?? null,
                'meta_description' => $item['meta_description'] ?? null,
                'scope_groups' => $item['scope_groups'] ?? null,
                'devices' => $item['devices'] ?? null,
                'applications' => $item['applications'] ?? null,
                'standards' => $item['standards'] ?? null,
                'capacity' => $item['capacity'] ?? null,
                'process_steps' => $item['process_steps'] ?? ($item['process'] ?? null),
                'faq' => $item['faq'] ?? null,
                'cta' => $item['cta'] ?? null,
                'is_active' => true,
                'sort_order' => $index,
            ]
        );

        return [$item['slug'] => $service];
    });

    $technicalServices = collect(config('mta.technical_services', []))->mapWithKeys(function ($item, $index) {
        $service = TechnicalService::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [
                'title' => $item['title'],
                'category' => $item['category'] ?? 'Teknik Servis',
                'summary' => $item['summary'] ?? null,
                'answer' => $item['answer'] ?? null,
                'body' => $item['body'] ?? null,
                'image' => $item['image'] ?? null,
                'image_alt' => $item['image_alt'] ?? null,
                'seo_title' => $item['seo_title'] ?? null,
                'meta_description' => $item['meta_description'] ?? null,
                'devices' => $item['devices'] ?? null,
                'service_steps' => $item['service_steps'] ?? null,
                'advantages' => $item['advantages'] ?? null,
                'faq' => $item['faq'] ?? null,
                'cta' => $item['cta'] ?? null,
                'is_active' => true,
                'sort_order' => $index,
            ]
        );

        return [$item['slug'] => $service];
    });

    $categories = collect(config('mta.product_categories', []))->mapWithKeys(function ($item, $index) {
        $category = ProductCategory::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [
                'name' => $item['name'],
                'summary' => $item['summary'] ?? null,
                'image' => $item['image'] ?? null,
                'aliases' => $item['aliases'] ?? null,
                'is_active' => true,
                'sort_order' => $index,
            ]
        );

        return [$item['slug'] => $category];
    });

    $brands = collect(config('mta.product_brands', []))->mapWithKeys(function ($item, $index) {
        $brand = ProductBrand::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [
                'name' => $item['name'],
                'summary' => $item['summary'] ?? null,
                'logo' => $item['logo'] ?? null,
                'aliases' => $item['aliases'] ?? null,
                'is_active' => true,
                'sort_order' => $index,
            ]
        );

        return [$item['slug'] => $brand];
    });

    foreach (config('mta.product_category_brands', []) as $categorySlug => $brandSlugs) {
        $category = $categories->get($categorySlug);

        if (! $category) {
            continue;
        }

        $category->brands()->sync(
            collect($brandSlugs)->map(fn ($slug) => $brands->get($slug)?->id)->filter()->all()
        );
    }

    foreach (config('mta.product_category_services', []) as $categorySlug => $serviceSlugs) {
        $category = $categories->get($categorySlug);

        if (! $category) {
            continue;
        }

        $category->services()->sync(
            collect($serviceSlugs)->map(fn ($slug) => $services->get($slug)?->id)->filter()->all()
        );
    }

    $importPath = storage_path('app/imports/mta-products-normalized.json');
    $importedProducts = is_file($importPath)
        ? collect(json_decode((string) file_get_contents($importPath), true))->filter(fn ($item) => is_array($item))
        : collect();

    $products = ($importedProducts->isNotEmpty() ? $importedProducts : collect(config('mta.products', [])))
        ->unique(fn ($item) => ($item['category_slug'] ?? '') . '/' . ($item['slug'] ?? ''))
        ->values();

    if ($importedProducts->isNotEmpty()) {
        Product::query()->whereNull('wp_id')->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    $products->each(function ($item, $index) use ($categories, $brands) {
        $category = $categories->get($item['category_slug'] ?? null);
        $brand = $brands->get($item['brand_slug'] ?? null);

        if (! $category || ! $brand) {
            return;
        }

        $rawStatus = $item['status'] ?? 'publish';

        Product::query()->updateOrCreate(
            [
                'product_category_id' => $category->id,
                'slug' => $item['slug'],
            ],
            [
                'product_brand_id' => $brand->id,
                'name' => $item['name'],
                'model' => $item['model'] ?? null,
                'sku' => $item['sku'] ?? null,
                'wp_id' => $item['wp_id'] ?? null,
                'old_url' => $item['old_url'] ?? null,
                'summary' => $item['summary'] ?? null,
                'body' => $item['body'] ?? null,
                'image' => $item['image'] ?? null,
                'image_alt' => $item['image_alt'] ?? null,
                'features' => $item['features'] ?? null,
                'metadata' => $item['metadata'] ?? null,
                'specs' => $item['specs'] ?? null,
                'status' => $rawStatus === 'publish' ? 'published' : $rawStatus,
                'is_featured' => (bool) ($item['is_featured'] ?? false),
                'sort_order' => $index,
                'published_at' => $rawStatus === 'publish' ? now() : null,
            ]
        );
    });

    collect(config('mta.articles', []))->each(function ($item) {
        Article::query()->updateOrCreate(
            ['slug' => $item['slug']],
            [
                'title' => $item['title'],
                'category' => $item['category'] ?? null,
                'category_slug' => $item['category_slug'] ?? null,
                'author' => $item['author'] ?? 'MTA Endüstri',
                'reading_time' => $item['reading_time'] ?? '4 dk okuma',
                'excerpt' => $item['excerpt'] ?? null,
                'body' => $item['body'] ?? null,
                'image' => $item['image'] ?? null,
                'status' => 'published',
                'published_at' => now(),
            ]
        );
    });

    collect(config('mta.faqs', []))->each(function ($item, $index) {
        Faq::query()->updateOrCreate(
            ['group_key' => 'general', 'question' => $item['question']],
            [
                'answer' => $item['answer'],
                'is_active' => true,
                'sort_order' => $index,
            ]
        );
    });

    $this->info('MTA içerikleri database tablolarına aktarıldı.');
    $this->line('Hizmetler: ' . $services->count());
    $this->line('Teknik servisler: ' . $technicalServices->count());
    $this->line('Kategoriler: ' . $categories->count());
    $this->line('Markalar: ' . $brands->count());
    $this->line('Ürünler toplam: ' . Product::query()->count());
    $this->line('Yayındaki XML ürünleri: ' . Product::query()->whereNotNull('wp_id')->where('status', 'published')->count());
    $this->line('Taslak ürünler: ' . Product::query()->where('status', 'draft')->count());
    $this->line('Blog yazıları: ' . Article::query()->count());
    $this->line('SSS: ' . Faq::query()->count());
})->purpose('Statik MTA config ve temiz ürün import verisini database tablolarına aktarır');

Artisan::command('mta:sync-scope {--fresh : Önce tüm kapsam verisini sil} {--force : Mevcut kayıtları da config değeriyle EZ (admin düzenlemeleri kaybolur)}', function () {
    $fresh = $this->option('fresh');
    $force = $this->option('force') || $fresh;

    if ($fresh) {
        \App\Models\ScopeGroup::query()->delete();
        \App\Models\ScopeCategory::query()->delete();
    }

    $created = ['cat' => 0, 'group' => 0];
    $updated = ['cat' => 0, 'group' => 0];

    foreach (config('mta-scope.categories', []) as $ci => $cat) {
        $category = \App\Models\ScopeCategory::query()->firstWhere('slug', $cat['slug']);
        $catPayload = [
            'icon' => $cat['icon'] ?? null,
            'title' => $cat['title'],
            'summary' => $cat['summary'] ?? null,
            'sort_order' => $ci,
            'is_active' => true,
        ];

        if (! $category) {
            $category = \App\Models\ScopeCategory::query()->create(['slug' => $cat['slug']] + $catPayload);
            $created['cat']++;
        } elseif ($force) {
            $category->update($catPayload);
            $updated['cat']++;
        }

        foreach ($cat['groups'] ?? [] as $gi => $group) {
            $key = $group['id'] ?? ($cat['slug'] . '-' . ($gi + 1));
            $existing = \App\Models\ScopeGroup::query()
                ->where('scope_category_id', $category->id)
                ->where('key', $key)
                ->first();
            $groupPayload = [
                'title' => $group['title'],
                'columns' => $group['columns'] ?? [],
                'rows' => $group['rows'] ?? [],
                'sort_order' => $gi,
                'is_active' => true,
            ];

            if (! $existing) {
                \App\Models\ScopeGroup::query()->create(
                    ['scope_category_id' => $category->id, 'key' => $key] + $groupPayload,
                );
                $created['group']++;
            } elseif ($force) {
                $existing->update($groupPayload);
                $updated['group']++;
            }
        }
    }

    $this->info(sprintf(
        'Kapsam senkronize edildi. Eklenen: %d kategori / %d grup. Güncellenen: %d kategori / %d grup.%s',
        $created['cat'], $created['group'], $updated['cat'], $updated['group'],
        $force ? '' : ' (Mevcut kayıtlara dokunulmadı; ezmek için --force.)'
    ));
})->purpose('config/mta-scope.php verisini scope_categories / scope_groups tablolarına aktarır (varsayılan: sadece eksikleri ekler)');
