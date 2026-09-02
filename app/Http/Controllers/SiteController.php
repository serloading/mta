<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\Redirect;
use App\Models\SchemaDefinition;
use App\Models\Service;
use App\Models\SeoEntry;
use App\Models\TechnicalService;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function home()
    {
        $metaTitle = 'Kalibrasyon Hizmetleri ve Laboratuvar Cihazları | MTA Endüstri';
        $metaDescription = 'MTA Endüstri; kalibrasyon hizmetleri, laboratuvar cihazları teknik servis desteği ve marka bazlı teknik ürün kataloğu sunar.';
        $products = collect($this->productsData());

        // Öne çıkan kategori çipleri: temsili görsel = eşleşen ilk ürün görseli.
        // Görselli kategoriler önce gelir; en fazla 6 çip gösterilir.
        $imagedProducts = $products->filter(fn ($p) => ! empty($p['image']))->values();
        $categoryImage = function (string $slug, array $keywords) use ($imagedProducts) {
            $hit = $imagedProducts->first(fn ($p) => ($p['category_slug'] ?? null) === $slug);
            if (! $hit) {
                $hit = $imagedProducts->first(function ($p) use ($keywords) {
                    $hay = Str::lower(($p['category_slug'] ?? '') . ' ' . ($p['category'] ?? ''));
                    foreach ($keywords as $kw) {
                        if (Str::contains($hay, $kw)) return true;
                    }
                    return false;
                });
            }
            return $hit['image'] ?? null;
        };

        $featuredCategories = collect([
            ['slug' => 'teraziler', 'name' => 'Teraziler', 'kw' => ['terazi']],
            ['slug' => 'nem-tayin', 'name' => 'Nem Tayin', 'kw' => ['nem']],
            ['slug' => 'titratorler', 'name' => 'Titratörler', 'kw' => ['titr', 'fischer']],
            ['slug' => 'viskozimetre', 'name' => 'Viskozimetre', 'kw' => ['visko']],
            ['slug' => 'ph-metre', 'name' => 'pH Metreler', 'kw' => ['ph metre', 'ph-metre', 'iletkenlik']],
            ['slug' => 'santrifujler', 'name' => 'Santrifüjler', 'kw' => ['santrifuj', 'santrifüj']],
            ['slug' => 'manyetik-karistirici', 'name' => 'Manyetik Karıştırıcılar', 'kw' => ['manyetik kar', 'karistir']],
            ['slug' => 'homojenizator', 'name' => 'Homojenizatörler', 'kw' => ['homojen']],
        ])->map(fn (array $c) => [
            'name' => $c['name'],
            'url' => route('products.category', $c['slug']),
            'image' => $categoryImage($c['slug'], $c['kw']),
        ]);

        $featuredCategories = $featuredCategories->filter(fn ($c) => $c['image'])
            ->concat($featuredCategories->filter(fn ($c) => ! $c['image']))
            ->take(6)
            ->values()
            ->all();

        $brandLogoDir = public_path('images/brands');
        $partnerBrands = collect([
            'shimadzu' => 'Shimadzu',
            'weightlab' => 'Weightlab',
            'velp' => 'VELP',
            'lamy' => 'Lamy Rheology',
            'mettler-toledo' => 'Mettler Toledo',
            'ohaus' => 'Ohaus',
            'kyoto-kem' => 'Kyoto KEM',
            'bellingham-stanley' => 'Bellingham + Stanley',
        ])->filter(fn ($name, $slug) => is_file($brandLogoDir . '/' . $slug . '.png'))
            ->map(fn ($name, $slug) => [
                'name' => $name,
                'logo' => 'images/brands/' . $slug . '.png',
                'url' => route('products.brand', $slug),
                'authorized' => false,
                'role' => null,
            ])->values()->all();

        // Yetkili / merkez servis ortakları (config/mta.php) — marka şeridinin başına eklenir.
        $authorizedRibbon = collect(config('mta.authorized_services', []))
            ->map(function ($entry) use ($brandLogoDir) {
                $target = $entry['primary_target'] ?? null;
                $url = route('technical-services.index');
                if (is_array($target)) {
                    $url = match ($target['type'] ?? '') {
                        'service' => route('services.show', $target['slug']),
                        'technical_service' => route('technical-services.show', $target['slug']),
                        default => route('technical-services.index'),
                    };
                }

                return [
                    'name' => $entry['brand'] ?? '',
                    'logo' => $entry['logo'] ?? '',
                    'url' => $url,
                    'authorized' => true,
                    'role' => $entry['role'] ?? 'Yetkili Servis',
                ];
            })
            ->filter(fn ($b) => $b['logo'] !== '' && is_file(public_path($b['logo'])))
            ->values()
            ->all();

        $partnerBrands = array_merge($authorizedRibbon, $partnerBrands);

        return view('pages.home', [
            'meta' => $this->meta($metaTitle, $metaDescription),
            'services' => $this->servicesData(),
            'technicalServices' => $this->technicalServicesData(),
            'products' => $products->all(),
            'articles' => $this->articlesData(),
            'faqs' => $this->faqsData(),
            'featuredCategories' => $featuredCategories,
            'partnerBrands' => $partnerBrands,
            'genericQuoteCta' => $this->quoteCta('service', null, 'Toplu kalibrasyon teklifi', route('home')),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
            ]),
        ]);
    }

    public function services()
    {
        $servicesSeo = $this->servicesPageSeoContent();
        $services = $this->servicesData();

        return view('pages.services', [
            'meta' => $this->meta($servicesSeo['meta_title'], $servicesSeo['meta_description'], $servicesSeo['image']),
            'services' => $services,
            'servicesSeo' => $servicesSeo,
            'quoteCtas' => $this->quoteCtas($services, 'service'),
            'genericQuoteCta' => $this->quoteCta('service', null, 'Kalibrasyon hizmetleri', route('services.index')),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($servicesSeo['meta_title'], $servicesSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Kalibrasyon Hizmetleri', 'url' => route('services.index')],
                ]),
            ]),
        ]);
    }

    public function serviceDetail(string $slug)
    {
        $service = $this->findBySlug($this->servicesData(), $slug);
        $serviceSeo = $this->normalizeServiceSeoContent($this->serviceSeoContent($service['slug']));
        $metaTitle = $serviceSeo['meta_title'] ?? $service['seo_title'] ?? $service['title'] . ' | MTA Endüstri';
        $metaDescription = $serviceSeo['meta_description'] ?? $service['meta_description'] ?? $service['summary'];
        $products = collect($this->productsData());

        $hasExplicitRelatedProductCategories = isset($serviceSeo['related_products'])
            && array_key_exists('category_slugs', $serviceSeo['related_products']);
        $relatedProductCategorySlugs = $hasExplicitRelatedProductCategories
            ? $serviceSeo['related_products']['category_slugs']
            : (($serviceSeo['slug'] ?? null) === 'kutle-terazi-kalibrasyonu' ? ['teraziler'] : []);

        if ($hasExplicitRelatedProductCategories || $relatedProductCategorySlugs !== []) {
            $products = $products
                ->whereIn('category_slug', $relatedProductCategorySlugs)
                ->take(4)
                ->values();
        }

        return view('pages.service-detail', [
            'meta' => $this->meta($metaTitle, $metaDescription, $service['image'] ?? null),
            'service' => $service,
            'serviceSeo' => $serviceSeo,
            'authorizedService' => $this->authorizedServiceFor('service', $service['slug']),
            'products' => $products,
            'quoteCta' => $this->quoteCta('service', $service['slug'], $service['title'], route('services.show', $service['slug'])),
            'faqs' => $this->faqsData(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Hizmetler', 'url' => route('services.index')],
                    ['name' => $service['title'], 'url' => route('services.show', $service['slug'])],
                ]),
                $this->serviceSchema($service),
            ], $service),
        ]);
    }

    public function technicalServices()
    {
        $technicalServicesSeo = $this->technicalServicesPageSeoContent();
        $technicalServices = $this->technicalServicesData();

        return view('pages.technical-services', [
            'meta' => $this->meta($technicalServicesSeo['meta_title'], $technicalServicesSeo['meta_description'], $technicalServicesSeo['image']),
            'technicalServices' => $technicalServices,
            'technicalServicesSeo' => $technicalServicesSeo,
            'quoteCtas' => $this->quoteCtas($technicalServices, 'technical_service'),
            'genericQuoteCta' => $this->quoteCta('technical_service', null, 'Teknik servis hizmetleri', route('technical-services.index')),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($technicalServicesSeo['meta_title'], $technicalServicesSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Teknik Servis', 'url' => route('technical-services.index')],
                ]),
            ]),
        ]);
    }

    public function technicalServiceDetail(string $slug)
    {
        $technicalService = $this->findBySlug($this->technicalServicesData(), $slug);
        $technicalServiceSeo = $this->normalizeTechnicalServiceSeoContent(
            $this->technicalServiceSeoContent($technicalService['slug']),
        );
        $metaTitle = $technicalServiceSeo['meta_title'] ?? $technicalService['seo_title'] ?? $technicalService['title'] . ' | MTA Endüstri';
        $metaDescription = $technicalServiceSeo['meta_description'] ?? $technicalService['meta_description'] ?? $technicalService['summary'];
        $products = collect($this->productsData());

        $hasExplicitRelatedProductCategories = isset($technicalServiceSeo['related_products'])
            && array_key_exists('category_slugs', $technicalServiceSeo['related_products']);
        $relatedProductCategorySlugs = $hasExplicitRelatedProductCategories
            ? $technicalServiceSeo['related_products']['category_slugs']
            : (($technicalServiceSeo['slug'] ?? null) === 'terazi-teknik-servis' ? ['teraziler'] : []);

        if ($hasExplicitRelatedProductCategories || $relatedProductCategorySlugs !== []) {
            $products = $products
                ->whereIn('category_slug', $relatedProductCategorySlugs)
                ->map(fn ($product) => [
                    ...$product,
                    'image_alt' => $this->productCategoryImageAlt($product['category_slug'] ?? '', $product),
                ])
                ->take(4)
                ->values();
        }

        return view('pages.technical-service-detail', [
            'meta' => $this->meta($metaTitle, $metaDescription, $technicalService['image'] ?? null),
            'technicalService' => $technicalService,
            'technicalServiceSeo' => $technicalServiceSeo,
            'authorizedService' => $this->authorizedServiceFor('technical_service', $technicalService['slug']),
            'services' => $this->servicesData(),
            'products' => $products,
            'quoteCta' => $this->quoteCta('technical_service', $technicalService['slug'], $technicalService['title'], route('technical-services.show', $technicalService['slug'])),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Teknik Servis', 'url' => route('technical-services.index')],
                    ['name' => $technicalService['title'], 'url' => route('technical-services.show', $technicalService['slug'])],
                ]),
                $this->serviceSchema($technicalService),
            ], $technicalService),
        ]);
    }

    /**
     * Bir hizmet / teknik servis / ürün slug'ı için tanımlı yetkili servis kaydını döndürür.
     * Kayıtlar config/mta.php → authorized_services altında.
     */
    private function authorizedServiceFor(string $type, string $slug): ?array
    {
        foreach ((array) config('mta.authorized_services', []) as $key => $entry) {
            if (in_array($slug, (array) ($entry[$type . '_slugs'] ?? []), true)) {
                return ['key' => $key] + $entry;
            }
        }

        return null;
    }

    public function scope()
    {
        $scope = config('mta-scope');
        $categories = $this->scopeCategoriesData();

        $groupCount = collect($categories)->sum(fn ($cat) => count($cat['groups'] ?? []));
        $rowCount = collect($categories)
            ->flatMap(fn ($cat) => $cat['groups'] ?? [])
            ->sum(fn ($group) => count($group['rows'] ?? []));

        $metaTitle = 'Kalibrasyon Kapsamımız | MTA Endüstri';
        $metaDescription = 'MTA Endüstri kalibrasyon kapsamları: cihaz grupları, ölçüm aralıkları, '
            . 'genişletilmiş ölçüm belirsizliği (U, k=2) ve uygulanan metot/standartlar.';

        return view('pages.scope', [
            'meta' => $this->meta($metaTitle, $metaDescription),
            'scopeCategories' => $categories,
            'scopeNote' => $scope['note'] ?? '',
            'scopeStats' => ['categories' => count($categories), 'groups' => $groupCount, 'rows' => $rowCount],
            'genericQuoteCta' => $this->quoteCta('service', null, 'Kalibrasyon kapsamı', route('scope')),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Kapsam', 'url' => route('scope')],
                ]),
            ]),
        ]);
    }

    private function scopeCategoriesData(): array
    {
        static $categories = null;

        if ($categories !== null && ! app()->runningUnitTests()) {
            return $categories;
        }

        if ($this->canReadTable('scope_categories') && \App\Models\ScopeCategory::query()->exists()) {
            return $categories = \App\Models\ScopeCategory::query()
                ->where('is_active', true)
                ->with(['groups' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get()
                ->map(fn (\App\Models\ScopeCategory $cat) => [
                    'slug' => $cat->slug,
                    'icon' => $cat->icon,
                    'title' => $cat->title,
                    'summary' => $cat->summary,
                    'groups' => $cat->groups->map(fn (\App\Models\ScopeGroup $g) => [
                        'id' => $g->key ?: ($cat->slug . '-' . $g->id),
                        'title' => $g->title,
                        'columns' => $g->columns ?? [],
                        'rows' => $g->rows ?? [],
                    ])->all(),
                ])
                ->all();
        }

        return $categories = config('mta-scope.categories', []);
    }

    public function products(Request $request)
    {
        $productsSeo = $this->productsPageSeoContent();

        return view('pages.products', array_merge($this->buildCatalog($request, collect($this->productsData())), [
            'meta' => $this->meta($productsSeo['meta_title'], $productsSeo['meta_description']),
            'productsSeo' => $productsSeo,
            'brands' => $this->productBrands(),
            'genericQuoteCta' => $this->quoteCta('product', null, 'Ürün kataloğu', route('products.index')),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($productsSeo['meta_title'], $productsSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Ürünler', 'url' => route('products.index')],
                ]),
            ]),
        ]));
    }

    /**
     * Ortak katalog: q araması, kategori/marka/durum facet filtreleri, sıralama,
     * sayfalama ve $base kapsamına göre facet listeleri. products/category/brand paylaşır.
     */
    private function buildCatalog(Request $request, \Illuminate\Support\Collection $base, array $opts = []): array
    {
        $lockCategory = $opts['lockCategory'] ?? null;
        $lockBrand = $opts['lockBrand'] ?? null;

        $q = trim((string) $request->query('q', ''));
        $selectedCats = $lockCategory ? [] : array_values(array_filter((array) $request->query('kategori', [])));
        $selectedBrands = $lockBrand ? [] : array_values(array_filter((array) $request->query('marka', [])));
        $selectedStatus = array_values(array_filter((array) $request->query('durum', [])));
        if (! $lockBrand && ($legacyBrand = $request->query('brand'))) {
            $selectedBrands = array_values(array_unique([...$selectedBrands, $legacyBrand]));
        }
        $sort = (string) $request->query('sirala', 'onerilen');

        $filtered = $base;

        if ($q !== '') {
            $needle = Str::lower($q);
            $filtered = $filtered->filter(fn ($p) => str_contains(
                Str::lower(($p['name'] ?? '') . ' ' . ($p['model'] ?? '') . ' ' . ($p['sku'] ?? '') . ' ' . ($p['brand'] ?? '') . ' ' . ($p['category'] ?? '')),
                $needle,
            ));
        }
        if ($selectedCats) {
            $filtered = $filtered->whereIn('category_slug', $selectedCats);
        }
        if ($selectedBrands) {
            $filtered = $filtered->whereIn('brand_slug', $selectedBrands);
        }
        if (in_array('turkak', $selectedStatus, true)) {
            $filtered = $filtered->filter(fn ($p) => ! empty($p['related_services']));
        }
        if (in_array('gorselli', $selectedStatus, true)) {
            $filtered = $filtered->filter(fn ($p) => ! empty($p['image']));
        }

        $filtered = match ($sort) {
            'az' => $filtered->sortBy(fn ($p) => Str::lower($p['name'] ?? '')),
            'za' => $filtered->sortByDesc(fn ($p) => Str::lower($p['name'] ?? '')),
            'marka' => $filtered->sortBy(fn ($p) => Str::lower(($p['brand'] ?? 'zzz') . ' ' . ($p['name'] ?? ''))),
            default => $filtered,
        };
        $filtered = $filtered->values();

        $perPage = 24;
        $page = max(1, (int) $request->query('sayfa', 1));
        $total = $filtered->count();
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'sayfa', 'query' => $request->except('sayfa')],
        );

        // Facet listeleri — sayfanın kapsamına ($base) göre, adet sırasıyla
        $catNames = $this->productCategories()->keyBy('slug');
        $brandNames = $this->productBrands()->keyBy('slug');
        $facetCategories = $base->groupBy('category_slug')
            ->map(fn ($rows, $slug) => [
                'slug' => $slug,
                'name' => $catNames[$slug]['name'] ?? $rows->first()['category'] ?? $slug,
                'count' => $rows->count(),
            ])
            ->filter(fn ($c) => $c['slug'])
            ->sortByDesc('count')
            ->take(20)
            ->values();
        $facetBrands = $base->groupBy('brand_slug')
            ->map(fn ($rows, $slug) => [
                'slug' => $slug,
                'name' => $brandNames[$slug]['name'] ?? $rows->first()['brand'] ?? $slug,
                'count' => $rows->count(),
            ])
            ->filter(fn ($b) => $b['slug'])
            ->sortByDesc('count')
            ->values();

        return [
            'products' => $products,
            'total' => $total,
            'facetCategories' => $facetCategories,
            'facetBrands' => $facetBrands,
            'filters' => [
                'q' => $q,
                'kategori' => $selectedCats,
                'marka' => $selectedBrands,
                'durum' => $selectedStatus,
                'sirala' => $sort,
            ],
            'hasActiveFilters' => $q !== '' || $selectedCats || $selectedBrands || $selectedStatus,
            'catalogLock' => ['category' => $lockCategory, 'brand' => $lockBrand],
            'catalogAction' => $request->url(),
        ];
    }

    public function productCategory(Request $request, string $category)
    {
        $categoryItem = $this->findProductCategory($category);
        $categorySeo = $this->normalizeProductCategorySeoContent(
            $this->productCategorySeoContent($categoryItem['slug']),
            $categoryItem,
        );
        $categorySlugs = $this->productCategoryAndDescendantSlugs($categoryItem['slug']);
        $base = collect($this->productsData())
            ->whereIn('category_slug', $categorySlugs)
            ->map(fn ($product) => [
                ...$product,
                'image_alt' => $this->productCategoryImageAlt($categoryItem['slug'], $product),
            ])
            ->values();

        $metaTitle = $categorySeo['meta_title'] ?? $categoryItem['name'] . ' Ürünleri | MTA Endüstri';
        $metaDescription = $categorySeo['meta_description'] ?? $categoryItem['summary'] ?? $categoryItem['name'] . ' kategorisindeki teknik ürünleri marka, model ve özellik bilgileriyle inceleyin.';

        return view('pages.product-category', array_merge($this->buildCatalog($request, $base, ['lockCategory' => $categoryItem['slug']]), [
            'meta' => array_merge($this->meta($metaTitle, $metaDescription), [
                'robots' => $categorySeo['robots'] ?? 'index,follow',
            ]),
            'category' => $categoryItem,
            'categorySeo' => $categorySeo,
            'brands' => $this->productBrandsForCategory($category),
            'genericQuoteCta' => $this->quoteCta('product', null, $categoryItem['name'] . ' kataloğu', route('products.category', $categoryItem['slug'])),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Ürünler', 'url' => route('products.index')],
                    ['name' => $categoryItem['name'], 'url' => route('products.category', $categoryItem['slug'])],
                ]),
            ]),
        ]));
    }

    public function brands()
    {
        $brandsSeo = $this->brandsPageSeoContent();

        return view('pages.brands', [
            'meta' => $this->meta($brandsSeo['meta_title'], $brandsSeo['meta_description']),
            'brandsSeo' => $brandsSeo,
            'brands' => $this->productBrands(),
            'categories' => $this->productCategories(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($brandsSeo['meta_title'], $brandsSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Markalar', 'url' => route('brands.index')],
                ]),
            ]),
        ]);
    }

    public function productBrand(Request $request, string $brand)
    {
        $brandItem = $this->findProductBrand($brand);
        $brandSeo = $this->normalizeBrandSeoContent(
            $this->brandSeoContent($brandItem['slug']),
            $brandItem,
            $this->productCategoriesForBrand($brand),
        );
        $base = collect($this->productsData())->where('brand_slug', $brand)->values();
        $metaTitle = $brandSeo['meta_title'] ?? $brandItem['name'] . ' Ürünleri | MTA Endüstri';
        $metaDescription = $brandSeo['meta_description'] ?? $brandItem['summary'] ?? $brandItem['name'] . ' markasına ait teknik ürünleri kategori, model ve özellik bilgileriyle inceleyin.';

        return view('pages.product-brand', array_merge($this->buildCatalog($request, $base, ['lockBrand' => $brandItem['slug']]), [
            'meta' => $this->meta($metaTitle, $metaDescription, $brandItem['logo'] ?? null),
            'brand' => $brandItem,
            'brandSeo' => $brandSeo,
            'brands' => $this->productBrands(),
            'genericQuoteCta' => $this->quoteCta('product', null, $brandItem['name'] . ' kataloğu', route('products.brand', $brandItem['slug'])),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Markalar', 'url' => route('brands.index')],
                    ['name' => $brandItem['name'], 'url' => route('products.brand', $brandItem['slug'])],
                ]),
            ]),
        ]));
    }

    public function productDetail(string $slug)
    {
        $normalizedSlug = $this->normalizeProductSlug($slug);
        $product = collect($this->productsData())
            ->first(function ($item) use ($slug, $normalizedSlug) {
                $itemSlug = (string) ($item['slug'] ?? '');
                $oldSlug = (string) ($item['old_slug'] ?? '');

                return $slug === $itemSlug
                    || ($oldSlug !== '' && $slug === $oldSlug)
                    || ($normalizedSlug !== '' && $normalizedSlug === $itemSlug)
                    || ($oldSlug !== '' && $normalizedSlug === $this->normalizeProductSlug($oldSlug));
            });

        abort_unless($product, 404);

        $product['videos'] = collect($product['videos'] ?? [])
            ->merge($this->productVideos($product['slug']))
            ->unique('youtube_id')
            ->values()
            ->all();
        $relatedServices = $this->relatedServicesForProduct($product);
        $productSeo = $this->productDetailSeoContent($product['slug']);
        $product = $productSeo !== [] ? [
            ...$product,
            'name' => $productSeo['h1'] ?? $product['name'],
            'seo_title' => $product['seo_title'] ?? $productSeo['meta_title'] ?? null,
            'meta_description' => $product['meta_description'] ?? $productSeo['meta_description'] ?? null,
            'summary' => $productSeo['hero_text'] ?? $product['summary'],
            'image_alt' => $productSeo['image_alt'] ?? $product['image_alt'] ?? null,
            'metadata' => $productSeo['metadata'] ?? $product['metadata'],
            'specs' => $productSeo['specs'] ?? $product['specs'],
            '_has_custom_detail_seo' => true,
        ] : $product;
        $product = $this->normalizeProductDetailContent($product);
        $productSeo = $this->normalizeProductDetailSeoContent($productSeo, $product);
        $metaDescription = $this->productMetaDescription($product, $relatedServices);
        $metaTitle = $product['seo_title'] ?? $productSeo['meta_title'] ?? $product['name'] . ' | MTA Endüstri';

        return view('pages.product-detail', [
            'meta' => $this->meta($metaTitle, $metaDescription, $product['image'] ?? null, $product),
            'product' => $product,
            'productSeo' => $productSeo,
            'brandLogo' => $this->productBrands()->firstWhere('slug', $product['brand_slug'] ?? '')['logo'] ?? null,
            'services' => $this->servicesData(),
            'relatedServices' => $relatedServices,
            'quoteCta' => $this->quoteCta('product', $product['slug'], $product['name'], route('products.show', $product['slug'])),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Ürünler', 'url' => route('products.index')],
                    ['name' => $product['category'], 'url' => route('products.category', $product['category_slug'])],
                    ['name' => $product['name'], 'url' => route('products.show', $product['slug'])],
                ]),
                $this->productSchema($product, $relatedServices),
            ], $product),
        ]);
    }

    public function knowledge()
    {
        $knowledgeSeo = $this->knowledgePageSeoContent();

        return view('pages.knowledge', [
            'meta' => $this->meta($knowledgeSeo['meta_title'], $knowledgeSeo['meta_description']),
            'knowledgeSeo' => $knowledgeSeo,
            'articles' => $this->articlesData(),
            'categories' => $this->articleCategories(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($knowledgeSeo['meta_title'], $knowledgeSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Bilgi Merkezi', 'url' => route('knowledge.index')],
                ]),
            ]),
        ]);
    }

    public function blog()
    {
        $blogSeo = $this->blogPageSeoContent();

        return view('pages.blog', [
            'meta' => $this->meta($blogSeo['meta_title'], $blogSeo['meta_description']),
            'blogSeo' => $blogSeo,
            'articles' => $this->articlesData(),
            'categories' => $this->articleCategories(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($blogSeo['meta_title'], $blogSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                ]),
            ]),
        ]);
    }

    public function knowledgeCategory(string $category)
    {
        $categoryItem = $this->findArticleCategory($category);
        $categorySeo = $this->knowledgeCategorySeoContent($categoryItem['slug']);
        $articles = collect($this->articlesData())->where('category_slug', $category)->values();
        $metaTitle = $categorySeo['meta_title'] ?? $categoryItem['name'] . ' | MTA Endüstri Bilgi Merkezi';
        $metaDescription = $categorySeo['meta_description'] ?? $categoryItem['name'] . ' kategorisindeki teknik rehber ve blog içerikleri.';

        return view('pages.knowledge-category', [
            'meta' => $this->meta($metaTitle, $metaDescription),
            'articles' => $articles,
            'category' => $categoryItem,
            'categorySeo' => $categorySeo,
            'categories' => $this->articleCategories(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Bilgi Merkezi', 'url' => route('knowledge.index')],
                    ['name' => $categoryItem['name'], 'url' => route('knowledge.category', $categoryItem['slug'])],
                ]),
            ]),
        ]);
    }

    public function articleDetail(string $slug)
    {
        $article = $this->findBySlug($this->articlesData(), $slug);
        $articleSeo = $this->articleSeoContent($article['slug']);
        $article = $articleSeo !== [] ? [
            ...$article,
            'title' => $articleSeo['h1'] ?? $article['title'],
            'excerpt' => $articleSeo['intro'] ?? $article['excerpt'],
            'answer' => $articleSeo['answer'] ?? $article['answer'],
        ] : $article;
        $metaTitle = $article['seo_title'] ?? $articleSeo['meta_title'] ?? $article['title'] . ' | MTA Endüstri';
        $metaDescription = $article['meta_description'] ?? $articleSeo['meta_description'] ?? $article['excerpt'];

        return view('pages.article-detail', [
            'meta' => $this->meta($metaTitle, $metaDescription, $article['image'] ?? null, $article),
            'article' => $article,
            'articleSeo' => $articleSeo,
            'services' => $this->servicesData(),
            'products' => $this->productsData(),
            'faqs' => $this->faqsData(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Bilgi Merkezi', 'url' => route('knowledge.index')],
                    ['name' => $article['category'], 'url' => route('knowledge.category', $article['category_slug'])],
                    ['name' => $article['title'], 'url' => route('knowledge.show', $article['slug'])],
                ]),
                $this->articleSchema($article),
            ], $article),
        ]);
    }

    public function about()
    {
        return $this->staticPage('about');
    }

    public function certificates()
    {
        return $this->staticPage('certificates');
    }

    public function references()
    {
        return $this->staticPage('references');
    }

    public function legal(string $slug)
    {
        $page = config('mta-legal.' . $slug);
        abort_unless(is_array($page), 404);

        $metaTitle = $page['title'] . ' | MTA Endüstri';
        $metaDescription = Str::limit(trim(strip_tags($page['lead'] ?? $page['title'])), 155);

        $legalPages = collect(config('mta-legal', []))
            ->map(fn ($p, $s) => ['slug' => $s, 'title' => $p['title']])
            ->values()
            ->all();

        return view('pages.legal', [
            'meta' => $this->meta($metaTitle, $metaDescription),
            'legal' => $page,
            'legalSlug' => $slug,
            'legalPages' => $legalPages,
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, $metaDescription),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => $page['title'], 'url' => url()->current()],
                ]),
            ]),
        ]);
    }

    public function contact()
    {
        return $this->staticPage('contact', [
            'leadContext' => $this->leadContextFromRequest(request()),
            'formAction' => route('leads.store'),
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $results = $this->searchCatalog($query);

        if ($request->query('format') === 'json') {
            return response()->json([
                'query' => $query,
                'categories' => $results['categories'],
                'brands' => $results['brands'],
                'products' => $results['products'],
            ]);
        }

        $metaTitle = $query !== ''
            ? '"' . $query . '" arama sonuçları | MTA Endüstri'
            : 'Ürün ve hizmet arama | MTA Endüstri';

        return view('pages.search', [
            'meta' => array_merge($this->meta($metaTitle, 'MTA Endüstri kataloğunda cihaz, model kodu, kategori ve marka araması.'), ['robots' => 'noindex,follow']),
            'query' => $query,
            'results' => $results,
            'schema' => $this->schemaGraph([
                $this->webPageSchema($metaTitle, 'MTA Endüstri katalog araması.'),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Arama', 'url' => route('search')],
                ]),
            ]),
        ]);
    }

    private function searchCatalog(string $query): array
    {
        $empty = ['categories' => [], 'brands' => [], 'products' => []];

        if (mb_strlen($query) < 2) {
            return $empty;
        }

        $needle = Str::lower($query);
        $match = fn (?string $haystack) => $haystack !== null && $haystack !== ''
            && str_contains(Str::lower($haystack), $needle);

        $categories = collect($this->productCategoriesData())
            ->filter(fn ($c) => $match($c['name'] ?? null)
                || collect($c['aliases'] ?? [])->contains(fn ($a) => $match($a)))
            ->take(6)
            ->map(fn ($c) => [
                'label' => $c['name'],
                'sub' => 'Kategori',
                'url' => route('products.category', $c['slug']),
            ])
            ->values()
            ->all();

        $brands = collect($this->productBrands())
            ->filter(fn ($b) => $match($b['name'] ?? null)
                || collect($b['aliases'] ?? [])->contains(fn ($a) => $match($a)))
            ->take(6)
            ->map(fn ($b) => [
                'label' => $b['name'],
                'sub' => 'Marka',
                'url' => route('products.brand', $b['slug']),
            ])
            ->values()
            ->all();

        $products = collect($this->productsData())
            ->filter(fn ($p) => $match($p['name'] ?? null)
                || $match($p['model'] ?? null)
                || $match($p['sku'] ?? null)
                || $match($p['brand'] ?? null)
                || $match($p['category'] ?? null))
            ->take(10)
            ->map(fn ($p) => [
                'label' => $p['name'],
                'sub' => trim(($p['brand'] ?? '') . ' · ' . ($p['category'] ?? ''), ' ·'),
                'image' => ! empty($p['image']) ? asset($p['image']) : null,
                'url' => route('products.show', $p['slug']),
            ])
            ->values()
            ->all();

        return compact('categories', 'brands', 'products');
    }

    public function quote(Request $request)
    {
        $leadContext = $this->leadContextFromRequest($request);

        $pageSeo = [
            'meta_title' => 'Teklif Al | MTA Endüstri',
            'meta_description' => 'Ürün, kalibrasyon hizmeti veya teknik servis ihtiyacınız için MTA Endüstri teklif formunu doldurun.',
            'canonical_url' => route('quote'),
            'robots' => 'noindex,follow',
            'h1' => 'Teklif Al',
        ];

        return view('pages.quote', [
            'meta' => $this->meta($pageSeo['meta_title'], $pageSeo['meta_description'], null, $pageSeo),
            'pageSeo' => $pageSeo,
            'leadContext' => $leadContext,
            'formAction' => route('quotes.store'),
            'services' => $this->servicesData(),
            'products' => $this->productsData(),
            'categories' => $this->productCategories(),
            'brands' => $this->productBrands(),
            'technicalServices' => $this->technicalServicesData(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($pageSeo['meta_title'], $pageSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Teklif Al', 'url' => route('quote')],
                ]),
            ], $pageSeo),
        ]);
    }

    private function staticPage(string $view, array $extraData = [])
    {
        $pageSeo = $this->staticPageSeoContent($view);
        $pageSeo = array_replace($pageSeo, $this->databaseStaticPageContent($view));

        abort_unless($pageSeo !== [], 404);

        return view('pages.' . $view, array_merge([
            'meta' => $this->meta($pageSeo['meta_title'], $pageSeo['meta_description'], $pageSeo['image'] ?? null, $pageSeo),
            'pageSeo' => $pageSeo,
            'services' => $this->servicesData(),
            'products' => $this->productsData(),
            'categories' => $this->productCategories(),
            'brands' => $this->productBrands(),
            'technicalServices' => $this->technicalServicesData(),
            'schema' => $this->schemaGraph([
                $this->webPageSchema($pageSeo['meta_title'], $pageSeo['meta_description']),
                $this->breadcrumbSchema([
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => $pageSeo['h1'], 'url' => url()->current()],
                ]),
            ], $pageSeo),
        ], $extraData));
    }

    public function submitLead(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'message' => ['required', 'string', 'max:2000'],
            'source_url' => ['nullable', 'string', 'max:500'],
            'source_type' => ['nullable', 'string', 'max:80'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'product' => ['nullable', 'string', 'max:180'],
            'service' => ['nullable', 'string', 'max:180'],
            'technical_service' => ['nullable', 'string', 'max:180'],
            'website' => ['nullable', 'size:0'],
        ]);

        if ($this->canReadTable('leads')) {
            $productSlug = $validated['product'] ?? $request->query('product');
            $serviceSlug = $validated['service'] ?? $request->query('service');
            $technicalServiceSlug = $validated['technical_service'] ?? $request->query('technical_service');

            $product = filled($productSlug) && $this->canReadTable('products')
                ? Product::query()->where('slug', $productSlug)->first()
                : null;
            $service = filled($serviceSlug) && $this->canReadTable('services')
                ? Service::query()->where('slug', $serviceSlug)->first()
                : null;
            $technicalService = filled($technicalServiceSlug) && $this->canReadTable('technical_services')
                ? TechnicalService::query()->where('slug', $technicalServiceSlug)->first()
                : null;

            Lead::query()->create([
                'product_id' => $product?->id,
                'service_id' => $service?->id,
                'technical_service_id' => $technicalService?->id,
                'name' => $validated['name'],
                'company' => $validated['company'] ?? null,
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'message' => $validated['message'],
                'source_url' => $validated['source_url'] ?? url()->previous(),
                'source_type' => $product ? 'product' : ($service ? 'service' : ($technicalService ? 'technical_service' : ($validated['source_type'] ?? 'contact'))),
                'utm' => collect($request->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']))
                    ->filter()
                    ->all(),
                'payload' => collect($request->except(['_token', 'website']))
                    ->filter()
                    ->all(),
                'status' => 'new',
            ]);
        }

        session()->flash('lead_success', 'Talebiniz alındı. En kısa sürede sizinle iletişime geçeceğiz.');

        return back()->withInput(collect($validated)->except(['message', 'website'])->all());
    }

    private function quoteCtas(array $items, string $type): array
    {
        return collect($items)
            ->mapWithKeys(fn (array $item) => [
                $item['slug'] => $this->quoteCta($type, $item['slug'], $item['title'] ?? $item['name'], match ($type) {
                    'product' => route('products.show', $item['slug']),
                    'technical_service' => route('technical-services.show', $item['slug']),
                    default => route('services.show', $item['slug']),
                }),
            ])
            ->all();
    }

    private function quoteCta(string $type, ?string $slug, string $name, ?string $sourceUrl = null): array
    {
        $query = $slug ? [$type => $slug] : ['source_type' => $type];
        $sourceUrl ??= url()->current();

        return [
            'quote_url' => route('quote', $query),
            'whatsapp_url' => $this->whatsappQuoteUrl($name, $sourceUrl, $type),
        ];
    }

    private function whatsappQuoteUrl(string $name, string $sourceUrl, string $type): string
    {
        $number = $this->whatsappNumber();

        if (! $number) {
            return route('quote', ['source_type' => $type]);
        }

        $typeLabel = match ($type) {
            'product' => 'ürün',
            'technical_service' => 'teknik servis',
            'service' => 'kalibrasyon hizmeti',
            default => 'talep',
        };
        $message = "Merhaba, {$name} için {$typeLabel} teklifi almak istiyorum.\nKaynak: {$sourceUrl}";

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }

    private function whatsappNumber(): ?string
    {
        $phone = config('mta.site.whatsapp') ?: config('mta.site.phone');
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 10) {
            return '90' . $digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return '90' . substr($digits, 1);
        }

        return $digits;
    }

    private function leadContextFromRequest(Request $request): ?array
    {
        foreach ([
            'product' => ['label' => 'Ürün', 'items' => $this->productsData(), 'name_key' => 'name'],
            'service' => ['label' => 'Hizmet', 'items' => $this->servicesData(), 'name_key' => 'title'],
            'technical_service' => ['label' => 'Servis', 'items' => $this->technicalServicesData(), 'name_key' => 'title'],
        ] as $type => $definition) {
            $slug = $request->query($type) ?? $request->input($type);

            if (! filled($slug)) {
                continue;
            }

            $item = collect($definition['items'])->firstWhere('slug', $slug);
            $name = $item[$definition['name_key']] ?? Str::of($slug)->replace('-', ' ')->title()->toString();

            return [
                'type' => $type,
                'label' => $definition['label'],
                'slug' => $slug,
                'name' => $name,
            ];
        }

        $sourceType = $request->query('source_type') ?? $request->input('source_type');
        $sourceName = $request->query('source_name') ?? $request->input('source_name');

        if (filled($sourceType)) {
            return [
                'type' => $sourceType,
                'label' => match ($sourceType) {
                    'product' => 'Ürün',
                    'technical_service' => 'Servis',
                    'service' => 'Hizmet',
                    default => 'Talep',
                },
                'slug' => null,
                'name' => filled($sourceName) ? $sourceName : match ($sourceType) {
                    'product' => 'Ürün teklifi',
                    'technical_service' => 'Teknik servis teklifi',
                    'service' => 'Kalibrasyon hizmeti teklifi',
                    default => 'Teklif talebi',
                },
            ];
        }

        return null;
    }

    public function redirectFallback(string $any)
    {
        $path = '/' . trim($any, '/');

        if ($this->canReadTable('redirects')) {
            $redirect = Redirect::query()
                ->where('source_path', $path)
                ->where('is_active', true)
                ->first();

            if ($redirect) {
                return redirect($redirect->target_path, $redirect->status_code);
            }
        }

        abort(404);
    }

    public function robots()
    {
        $base = rtrim(config('app.url'), '/');

        return Response::make("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /login\nDisallow: /arama\n\nSitemap: {$base}/sitemap.xml\n", 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function sitemap()
    {
        $base = rtrim(config('app.url'), '/');
        $urls = collect([
            '/',
            '/hizmetler',
            '/kapsam',
            '/urunler',
            '/markalar',
            '/teknik-servis',
            '/blog',
            '/bilgi-merkezi',
            '/hakkimizda',
            '/sertifikalar',
            '/referanslar',
            '/iletisim',
        ])
            ->merge(collect($this->servicesData())->map(fn ($item) => '/hizmetler/' . $item['slug']))
            ->merge(collect($this->technicalServicesData())->map(fn ($item) => '/teknik-servis/' . $item['slug']))
            ->merge($this->productCategories()->map(fn ($item) => '/urunler/' . $item['slug']))
            ->merge($this->productBrands()->map(fn ($item) => '/urunler/marka/' . $item['slug']))
            ->merge(collect($this->productsData())->map(fn ($item) => '/urun/' . $item['slug']))
            ->merge($this->articleCategories()->map(fn ($item) => '/bilgi-merkezi/kategori/' . $item['slug']))
            ->merge(collect($this->articlesData())->map(fn ($item) => '/bilgi-merkezi/' . $item['slug']))
            ->map(fn ($path) => [
                'loc' => $base . $path,
                'lastmod' => now()->toAtomString(),
            ]);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    private function findBySlug(array $items, string $slug): array
    {
        $item = collect($items)->firstWhere('slug', $slug);
        abort_unless($item, 404);

        return $item;
    }

    private function servicesData(): array
    {
        static $services = null;

        if ($services !== null && ! app()->runningUnitTests()) {
            return $services;
        }

        if ($this->canReadTable('services') && Service::query()->exists()) {
            $services = Service::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Service $service) => [
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'category' => $service->category ?? 'Kalibrasyon Hizmetleri',
                    'eyebrow' => $service->eyebrow,
                    'summary' => $service->summary,
                    'answer' => $service->answer,
                    'body' => $service->body,
                    'scope' => $service->scope ?? [],
                    'image' => $this->publicAssetPath($service->image),
                    'image_alt' => $service->image_alt,
                    'seo_title' => $service->seo_title,
                    'meta_description' => $service->meta_description,
                    'schema_blocks' => $service->schema_blocks ?? [],
                    'scope_groups' => $service->scope_groups ?? [],
                    'devices' => $service->devices ?? [],
                    'applications' => $service->applications ?? [],
                    'standards' => $service->standards ?? [],
                    'capacity' => $service->capacity ?? [],
                    'process' => $service->process_steps ?? [],
                    'process_steps' => $service->process_steps ?? [],
                    'faq' => $service->faq ?? [],
                    'cta' => $service->cta,
                ])
                ->all();

            return $services;
        }

        return $services = config('mta.services', []);
    }

    private function technicalServicesData(): array
    {
        static $technicalServices = null;

        if ($technicalServices !== null && ! app()->runningUnitTests()) {
            return $technicalServices;
        }

        if ($this->canReadTable('technical_services') && TechnicalService::query()->exists()) {
            $databaseServices = TechnicalService::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (TechnicalService $service) => [
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'category' => $service->category ?? 'Teknik Servis',
                    'summary' => $service->summary,
                    'answer' => $service->answer,
                    'body' => $service->body,
                    'image' => $this->publicAssetPath($service->image),
                    'image_alt' => $service->image_alt,
                    'seo_title' => $service->seo_title,
                    'meta_description' => $service->meta_description,
                    'schema_blocks' => $service->schema_blocks ?? [],
                    'devices' => $service->devices ?? [],
                    'service_steps' => $service->service_steps ?? [],
                    'advantages' => $service->advantages ?? [],
                    'faq' => $service->faq ?? [],
                    'cta' => $service->cta,
                ])
                ->all();

            $configuredServices = config('mta.technical_services', []);
            $databaseSlugs = collect($databaseServices)->pluck('slug')->all();
            $missingConfiguredServices = collect($configuredServices)
                ->reject(fn ($service) => in_array($service['slug'] ?? null, $databaseSlugs, true))
                ->values()
                ->all();

            return $technicalServices = array_merge($databaseServices, $missingConfiguredServices);
        }

        return $technicalServices = config('mta.technical_services', []);
    }

    private function productCategoriesData(): array
    {
        static $categories = null;
        $hiddenSlugs = ['termal-analiz'];

        if ($categories !== null && ! app()->runningUnitTests()) {
            return $categories;
        }

        if ($this->canReadTable('product_categories') && ProductCategory::query()->exists()) {
            $databaseCategories = ProductCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['name', 'slug', 'summary', 'image', 'aliases'])
                ->map(fn (ProductCategory $category) => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'summary' => $category->summary,
                    'image' => $this->publicAssetPath($category->image),
                    'aliases' => $category->aliases ?? [],
                ])
                ->values();

            $configuredCategories = collect(config('mta.product_categories', []));
            $databaseBySlug = $databaseCategories->keyBy('slug');

            $categories = $configuredCategories
                ->map(function (array $category) use ($databaseBySlug) {
                    $databaseCategory = collect($databaseBySlug->get($category['slug'], []))
                        ->filter(fn ($value) => $value !== null && $value !== [])
                        ->all();

                    return array_replace($category, $databaseCategory);
                })
                ->merge($databaseCategories->reject(fn ($category) => $configuredCategories->pluck('slug')->contains($category['slug'])))
                ->reject(fn ($category) => in_array($category['slug'], $hiddenSlugs, true))
                ->values()
                ->all();

            return $categories;
        }

        return $categories = collect(config('mta.product_categories', []))
            ->reject(fn ($category) => in_array($category['slug'], $hiddenSlugs, true))
            ->values()
            ->all();
    }

    private function productBrandsData(): array
    {
        static $brands = null;

        if ($brands !== null && ! app()->runningUnitTests()) {
            return $brands;
        }

        if ($this->canReadTable('product_brands') && ProductBrand::query()->exists()) {
            $brands = ProductBrand::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['name', 'slug', 'summary', 'logo', 'aliases'])
                ->map(fn (ProductBrand $brand) => [
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'summary' => $brand->summary,
                    'logo' => $this->publicAssetPath($brand->logo),
                    'image' => $this->publicAssetPath($brand->logo),
                    'aliases' => $brand->aliases ?? [],
                ])
                ->all();

            return $brands;
        }

        return $brands = config('mta.product_brands', []);
    }

    private function productsData(): array
    {
        static $products = null;

        if ($products !== null && ! app()->runningUnitTests()) {
            return $products;
        }

        if ($this->canReadTable('products')) {
            $products = Product::query()
                ->with(['brand', 'category.services', 'documents', 'videos', 'services'])
                ->where('status', 'published')
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Product $product) => [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'old_url' => $product->old_url,
                    'wp_id' => $product->wp_id,
                    'status' => $product->status,
                    'category' => $product->category?->name ?? 'Ürün',
                    'category_slug' => $product->category?->slug,
                    'brand' => $product->brand?->name ?? 'MTA Endüstri',
                    'brand_slug' => $product->brand?->slug,
                    'model' => $product->model ?: $product->name,
                    'sku' => $product->sku ?: 'Yayın öncesi netleştirilecek',
                    'seo_title' => $product->seo_title,
                    'meta_description' => $product->meta_description,
                    'canonical_url' => $product->canonical_url,
                    'og_title' => $product->og_title,
                    'og_description' => $product->og_description,
                    'og_image' => $this->publicAssetPath($product->og_image),
                    'robots' => $product->robots ?: 'index,follow',
                    'schema_blocks' => $product->schema_blocks ?? [],
                    'summary' => $product->summary ?: $product->name . ' için teknik özellik, doküman ve teklif bilgisi.',
                    'image' => $this->publicAssetPath($product->image),
                    'image_alt' => $product->image_alt,
                    'image_label' => 'Ürün görseli alanı',
                    'features' => $product->features ?? [],
                    'gallery' => collect($product->gallery ?? [])->map(fn ($path) => $this->publicAssetPath($path))->filter()->values()->all(),
                    'metadata' => $product->metadata ?? [],
                    'specs' => $product->specs ?? [],
                    'filter_keys' => $product->filter_keys ?? [],
                    'documents' => $product->documents
                        ->sortBy('sort_order')
                        ->map(fn ($document) => [
                            'title' => $document->title,
                            'type' => $document->type,
                            'path' => $this->publicAssetPath($document->path),
                            'url' => $document->url,
                        ])
                        ->values()
                        ->all(),
                    'videos' => $product->videos
                        ->sortBy('sort_order')
                        ->map(fn ($video) => [
                            'title' => $video->title,
                            'youtube_url' => $video->youtube_url,
                            'youtube_id' => $video->youtube_id,
                        ])
                        ->filter(fn ($video) => filled($video['youtube_id']))
                        ->values()
                        ->all(),
                    'related_services' => $product->services->isNotEmpty()
                        ? $product->services->pluck('slug')->all()
                        : ($product->category?->services->pluck('slug')->all() ?? []),
                ])
                ->map(fn (array $product) => $this->normalizeProductRecord($product))
                ->filter(fn ($product) => ! empty($product['slug']) && ! empty($product['category_slug']) && ! empty($product['brand_slug']))
                ->unique(fn ($product) => $product['category_slug'] . '/' . $product['slug'])
                ->values()
                ->all();

            return $products;
        }

        $configured = collect(config('mta.products', []));
        $imported = $this->importedProducts();

        $products = $configured
            ->merge($imported)
            ->map(fn (array $product) => $this->normalizeProductRecord($product))
            ->filter(fn ($product) => ! empty($product['slug']) && ! empty($product['category_slug']) && ! empty($product['brand_slug']))
            ->unique(fn ($product) => $product['category_slug'] . '/' . $product['slug'])
            ->values()
            ->all();

        return $products;
    }

    private function importedProducts()
    {
        $importPath = storage_path('app/imports/mta-products-normalized.json');

        if (! is_file($importPath)) {
            return collect();
        }

        $decoded = json_decode((string) file_get_contents($importPath), true);

        return is_array($decoded)
            ? collect($decoded)->filter(fn ($product) => is_array($product))->map(fn (array $product) => $this->normalizeProductRecord($product))
            : collect();
    }

    private function normalizeProductRecord(array $product): array
    {
        $originalSlug = (string) ($product['slug'] ?? '');
        $normalizedSlug = $this->normalizeProductSlug($originalSlug !== '' ? $originalSlug : (string) ($product['name'] ?? ''));

        if ($normalizedSlug !== '' && $normalizedSlug !== $originalSlug) {
            $product['old_slug'] = $originalSlug;
            $product['slug'] = $normalizedSlug;
        }

        $product['features'] = $product['features'] ?? [];
        $product['image'] = $this->publicAssetPath($product['image'] ?? null);
        $product['gallery'] = collect($product['gallery'] ?? [])
            ->map(fn ($path) => $this->publicAssetPath($path))
            ->filter()
            ->values()
            ->all();
        $product['metadata'] = $product['metadata'] ?? [];
        $product['specs'] = $product['specs'] ?? [];
        $product['documents'] = collect($product['documents'] ?? [])
            ->map(function ($document) {
                if (! is_array($document)) {
                    return $document;
                }

                return [
                    ...$document,
                    'path' => $this->publicAssetPath($document['path'] ?? null),
                ];
            })
            ->all();
        $product['videos'] = $product['videos'] ?? [];
        $product['schema_blocks'] = $product['schema_blocks'] ?? [];
        $product['image_alt'] = $product['image_alt'] ?? null;
        $product['robots'] = $product['robots'] ?? 'index,follow';

        return $product;
    }

    private function productVideos(string $slug): array
    {
        $videos = [
            'lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set' => [
                ['youtube_id' => 'N0AZOzy5ATo', 'title' => 'Lamy B-One Plus rotasyonel viskozimetre ürün videosu'],
            ],
            'lamy-b-one-plus-rotasyonel-viskozimetre-r2-r7-spindle-set' => [
                ['youtube_id' => 'N0AZOzy5ATo', 'title' => 'Lamy B-One Plus rotasyonel viskozimetre ürün videosu'],
            ],
            'lamy-b-one-plus-rotasyonel-viskozimetre-ku-1-10-spindle' => [
                ['youtube_id' => 'N0AZOzy5ATo', 'title' => 'Lamy B-One Plus rotasyonel viskozimetre KU ürün videosu'],
            ],
            'lamy-b-one-touch-tasinabilir-rotasyonel-viskozimetre' => [
                ['youtube_id' => 'N0AZOzy5ATo', 'title' => 'Lamy B-One Touch taşınabilir rotasyonel viskozimetre ürün videosu'],
            ],
            'lamy-b-one-touch-rotasyonel-viskozimetre' => [
                ['youtube_id' => 'N0AZOzy5ATo', 'title' => 'Lamy B-One Touch rotasyonel viskozimetre ürün videosu'],
            ],
            'lamy-rm100-touch-tasinabilir-rotasyonel-viskozimetre' => [
                ['youtube_id' => 's8iVls57iKo', 'title' => 'Lamy RM100 Touch taşınabilir rotasyonel viskozimetre ürün videosu'],
            ],
            'lamy-first-touch-rotasyonel-viskozimetre' => [
                ['youtube_id' => 'V3Acy6tRzAo', 'title' => 'Lamy First Touch rotasyonel viskozimetre ürün videosu'],
            ],
            'velp-arex-digital-isitmali-manyetik-karistirici' => [
                ['youtube_id' => 'naGhESpQXS4', 'title' => 'VELP AREX Digital ısıtmalı manyetik karıştırıcı ürün videosu'],
            ],
            'velp-arec-x-isitmali-manyetik-karistirici' => [
                ['youtube_id' => 'ljX_nPC_S_Y', 'title' => 'VELP AREC.X ısıtmalı manyetik karıştırıcı ürün videosu'],
            ],
            'velp-are-6-isitmali-manyetik-karistirici' => [
                ['youtube_id' => 'hmQJRTnwd6s', 'title' => 'VELP ARE-6 ısıtmalı manyetik karıştırıcı ürün videosu'],
            ],
            'velp-arex-6-isitmali-manyetik-karistirici' => [
                ['youtube_id' => 'hmQJRTnwd6s', 'title' => 'VELP AREX-6 ısıtmalı manyetik karıştırıcı ürün videosu'],
            ],
            'velp-arex-6-digital-isitmali-manyetik-karistirici' => [
                ['youtube_id' => 'hmQJRTnwd6s', 'title' => 'VELP AREX-6 Digital ısıtmalı manyetik karıştırıcı ürün videosu'],
            ],
            'velp-dlh-mekanik-karistirici' => [
                ['youtube_id' => '3M2LT766gYo', 'title' => 'VELP DLH mekanik karıştırıcı ürün videosu'],
            ],
            'velp-tx4-vorteks-karistirici' => [
                ['youtube_id' => 'HVSXHmHHDhs', 'title' => 'VELP TX4 vorteks karıştırıcı ürün videosu'],
            ],
            'velp-ov5-homojenizator' => [
                ['youtube_id' => 'D8yUsQ9m5vk', 'title' => 'VELP OV5 homojenizatör ürün videosu'],
            ],
        ];

        return $videos[$slug] ?? [];
    }

    private function normalizeProductSlug(string $value): string
    {
        $decoded = rawurldecode($value);
        $decoded = str_replace(['®', '™', 'Ω', 'Ω', 'ω', '&'], ['', '', ' ohm ', ' ohm ', ' ohm ', ' ve '], $decoded);

        return Str::slug($decoded);
    }

    private function articlesData(): array
    {
        static $articles = null;

        if ($articles !== null && ! app()->runningUnitTests()) {
            return $articles;
        }

        if ($this->canReadTable('articles') && Article::query()->exists()) {
            $articles = Article::query()
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->get()
                ->map(fn (Article $article) => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->category,
                    'category_slug' => $article->category_slug,
                    'author' => $article->author ?? 'MTA Endüstri',
                    'reading_time' => $article->reading_time ?? '4 dk okuma',
                    'published_at' => $article->published_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'updated_at' => $article->updated_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'excerpt' => $article->excerpt,
                    'answer' => Str::limit(strip_tags((string) ($article->body ?: $article->excerpt)), 220),
                    'body' => $article->body,
                    'tags' => $article->tags ?? [],
                    'image' => $this->publicAssetPath($article->image),
                    'image_alt' => $article->image_alt,
                    'seo_title' => $article->seo_title,
                    'meta_description' => $article->meta_description,
                    'canonical_url' => $article->canonical_url,
                    'og_title' => $article->og_title,
                    'og_description' => $article->og_description,
                    'og_image' => $this->publicAssetPath($article->og_image),
                    'robots' => $article->robots ?: 'index,follow',
                    'schema_blocks' => $article->schema_blocks ?? [],
                ])
                ->all();

            return $articles = $this->enrichArticles($articles);
        }

        return $articles = $this->enrichArticles(config('mta.articles', []));
    }

    private function enrichArticles(array $articles): array
    {
        $fallbackArticles = [
            [
                'title' => 'Terazi Kalibrasyonu Nedir?',
                'slug' => 'terazi-kalibrasyonu-nedir',
                'category' => 'Kalibrasyon Rehberleri',
                'category_slug' => 'kalibrasyon-rehberleri',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '4 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'Terazi kalibrasyonu, hassas tartım cihazlarında ölçüm güvenilirliği ve düzenli kontrol ihtiyacını anlatan rehber içerik.',
                'answer' => 'Terazi kalibrasyonu, tartım cihazının referans kütlelerle karşılaştırılarak doğruluk durumunun değerlendirilmesidir.',
            ],
            [
                'title' => 'Hassas Terazi Seçim Rehberi',
                'slug' => 'hassas-terazi-secim-rehberi',
                'category' => 'Satın Alma Rehberleri',
                'category_slug' => 'satin-alma-rehberleri',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '5 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'Hassas terazi seçiminde kapasite, okunabilirlik, kullanım ortamı, servis ve kalibrasyon ihtiyacını değerlendiren satın alma rehberi.',
                'answer' => 'Hassas terazi seçiminde kapasite, okunabilirlik, kullanım ortamı, servis desteği ve kalibrasyon ihtiyacı birlikte değerlendirilmelidir.',
            ],
            [
                'title' => 'pH Metre Seçerken Nelere Dikkat Edilmeli?',
                'slug' => 'ph-metre-secerken-nelere-dikkat-edilmeli',
                'category' => 'Laboratuvar Cihazları',
                'category_slug' => 'laboratuvar-cihazlari',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '5 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'pH metre seçiminde elektrot tipi, kullanım alanı, sıcaklık kompanzasyonu, servis ve kalibrasyon ihtiyacına bakılmalıdır.',
                'answer' => 'pH metre seçiminde ölçüm ortamı, elektrot tipi, sıcaklık kompanzasyonu ve teknik destek ihtiyacı birlikte düşünülmelidir.',
            ],
            [
                'title' => 'Refraktometre Nedir?',
                'slug' => 'refraktometre-nedir',
                'category' => 'Laboratuvar Cihazları',
                'category_slug' => 'laboratuvar-cihazlari',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '4 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'Refraktometrelerin kırılma indisi, Brix ve kalite kontrol uygulamalarındaki kullanımını özetleyen rehber.',
                'answer' => 'Refraktometre, numunelerin kırılma indisi veya Brix gibi değerlerini ölçmek için kullanılan laboratuvar cihazıdır.',
            ],
            [
                'title' => 'Viskozimetre Ne İşe Yarar?',
                'slug' => 'viskozimetre-ne-ise-yarar',
                'category' => 'Laboratuvar Cihazları',
                'category_slug' => 'laboratuvar-cihazlari',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '4 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'Viskozimetrelerin sıvı ve yarı akışkan numunelerde viskozite ölçümü için nasıl kullanıldığını anlatan içerik.',
                'answer' => 'Viskozimetre, sıvı veya yarı akışkan numunelerin akışa karşı direncini değerlendiren ölçüm cihazıdır.',
            ],
            [
                'title' => 'Kalibrasyon Periyodu Nasıl Belirlenir?',
                'slug' => 'kalibrasyon-periyodu-nasil-belirlenir',
                'category' => 'Ölçüm Güvenilirliği',
                'category_slug' => 'olcum-guvenilirligi',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '5 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'Kalibrasyon periyodu; cihaz kullanımı, ölçüm riski, kalite prosedürleri ve geçmiş sonuçlara göre değerlendirilmelidir.',
                'answer' => 'Kalibrasyon periyodu cihazın kullanım yoğunluğu, ölçüm riski, kalite prosedürü ve geçmiş performansına göre belirlenir.',
            ],
            [
                'title' => 'Cihaz Bakım Rehberi',
                'slug' => 'cihaz-bakim-rehberi',
                'category' => 'Teknik Servis ve Bakım',
                'category_slug' => 'teknik-servis-ve-bakim',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '5 dk',
                'published_at' => '2026-08-26',
                'updated_at' => '2026-08-26',
                'excerpt' => 'Laboratuvar cihazlarında bakım ihtiyacı, arıza belirtileri ve kalibrasyon öncesi teknik kontrol süreçlerini özetleyen rehber.',
                'answer' => 'Cihaz bakımı; arıza oluşmadan önce performans, güvenilirlik ve servis ihtiyacını kontrol altında tutmaya yardımcı olur.',
            ],
        ];

        return collect($articles)
            ->merge($fallbackArticles)
            ->filter(fn ($article) => ! empty($article['slug']) && ! empty($article['category_slug']))
            ->map(fn ($article) => [
                'title' => $article['title'],
                'slug' => $article['slug'],
                'category' => $article['category'] ?? 'Bilgi Merkezi',
                'category_slug' => $article['category_slug'],
                'author' => $article['author'] ?? 'MTA Teknik Editör',
                'reading_time' => $article['reading_time'] ?? '4 dk',
                'published_at' => $article['published_at'] ?? '2026-08-26',
                'updated_at' => $article['updated_at'] ?? '2026-08-26',
                'excerpt' => $article['excerpt'] ?? $article['title'] . ' hakkında teknik rehber içeriği.',
                'answer' => $article['answer'] ?? $article['excerpt'] ?? $article['title'] . ' hakkında kısa cevap.',
                'body' => $article['body'] ?? null,
                'image' => $article['image'] ?? null,
            ])
            ->unique('slug')
            ->values()
            ->all();
    }

    private function faqsData(): array
    {
        static $faqs = null;

        if ($faqs !== null) {
            return $faqs;
        }

        if ($this->canReadTable('faqs') && Faq::query()->exists()) {
            $faqs = Faq::query()
                ->where('is_active', true)
                ->where('group_key', 'general')
                ->orderBy('sort_order')
                ->get(['question', 'answer'])
                ->map(fn (Faq $faq) => [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ])
                ->all();

            return $faqs;
        }

        return $faqs = config('mta.faqs', []);
    }

    private function categoryBrandMap(): array
    {
        static $map = null;

        if ($map !== null) {
            return $map;
        }

        if ($this->canReadTable('product_category_brand') && ProductCategory::query()->whereHas('brands')->exists()) {
            $databaseMap = ProductCategory::query()
                ->with('brands')
                ->get()
                ->mapWithKeys(fn (ProductCategory $category) => [
                    $category->slug => $category->brands->pluck('slug')->all(),
                ])
                ->all();

            return $map = array_replace(config('mta.product_category_brands', []), $databaseMap);
        }

        return $map = config('mta.product_category_brands', []);
    }

    private function categoryServiceMap(): array
    {
        static $map = null;

        if ($map !== null) {
            return $map;
        }

        if ($this->canReadTable('product_category_service') && ProductCategory::query()->whereHas('services')->exists()) {
            $databaseMap = ProductCategory::query()
                ->with('services')
                ->get()
                ->mapWithKeys(fn (ProductCategory $category) => [
                    $category->slug => $category->services->pluck('slug')->all(),
                ])
                ->all();

            return $map = array_replace(config('mta.product_category_services', []), $databaseMap);
        }

        return $map = config('mta.product_category_services', []);
    }

    private function canReadTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    private function databaseStaticPageContent(string $view): array
    {
        if (! $this->canReadTable('pages')) {
            return [];
        }

        $slug = match ($view) {
            'about' => 'hakkimizda',
            'certificates' => 'sertifikalar',
            'references' => 'referanslar',
            'contact' => 'iletisim',
            default => $view,
        };

        $page = Page::query()
            ->where('status', 'published')
            ->whereIn('slug', [$view, $slug])
            ->orderByDesc('updated_at')
            ->first();

        if (! $page) {
            return [];
        }

        return [
            'meta_title' => $page->seo_title ?: $page->title,
            'meta_description' => $page->meta_description ?: ($page->excerpt ?: Str::limit(strip_tags((string) $page->body), 160)),
            'h1' => $page->title,
            'hero_text' => $page->excerpt,
            'image' => $this->publicAssetPath($page->image),
            'canonical_url' => $page->canonical_url,
            'og_title' => $page->og_title,
            'og_description' => $page->og_description,
            'og_image' => $this->publicAssetPath($page->og_image),
            'robots' => $page->robots ?: 'index,follow',
            'schema_blocks' => $page->schema_blocks ?? [],
        ];
    }

    private function productCategories()
    {
        $products = collect($this->productsData());
        $configured = collect($this->productCategoriesData())->map(fn ($category) => [
            ...$category,
            'count' => $products->whereIn('category_slug', $this->productCategoryAndDescendantSlugs($category['slug']))->count(),
            'image' => $category['image'] ?? $products->first(fn ($product) => in_array($product['category_slug'], $this->productCategoryAndDescendantSlugs($category['slug']), true) && ! empty($product['image']))['image'] ?? null,
            'image_alt' => $products->first(fn ($product) => in_array($product['category_slug'], $this->productCategoryAndDescendantSlugs($category['slug']), true) && ! empty($product['image']))['image_alt'] ?? $category['name'] . ' kategorisi ürün görseli',
        ]);

        $fromProducts = $products->map(fn ($product) => [
            'name' => $product['category'],
            'slug' => $product['category_slug'],
            'summary' => $product['category'] . ' kategorisindeki teknik ürünler.',
            'count' => $products->where('category_slug', $product['category_slug'])->count(),
            'image' => $products->first(fn ($item) => $item['category_slug'] === $product['category_slug'] && ! empty($item['image']))['image'] ?? null,
            'image_alt' => $products->first(fn ($item) => $item['category_slug'] === $product['category_slug'] && ! empty($item['image']))['image_alt'] ?? $product['category'] . ' kategorisi ürün görseli',
        ]);

        return $configured
            ->merge($fromProducts)
            ->reject(fn ($category) => ($category['slug'] ?? null) === 'termal-analiz')
            ->unique('slug')
            ->values();
    }

    private function productCategoryAndDescendantSlugs(string $slug): array
    {
        $childrenByParent = collect($this->productCategoriesData())
            ->filter(fn ($category) => ! empty($category['parent_slug']))
            ->groupBy('parent_slug');
        $slugs = [];

        $walk = function (string $currentSlug) use (&$walk, &$slugs, $childrenByParent): void {
            if (in_array($currentSlug, $slugs, true)) {
                return;
            }

            $slugs[] = $currentSlug;

            foreach ($childrenByParent->get($currentSlug, collect()) as $child) {
                $walk($child['slug']);
            }
        };

        $walk($slug);

        return $slugs;
    }

    private function productBrands()
    {
        $products = collect($this->productsData());
        $configured = collect($this->productBrandsData())->map(fn ($brand) => [
            ...$brand,
            'count' => $products->where('brand_slug', $brand['slug'])->count(),
        ]);

        $fromProducts = $products->map(fn ($product) => [
            'name' => $product['brand'],
            'slug' => $product['brand_slug'],
            'summary' => $product['brand'] . ' markasına ait teknik ürünler.',
            'count' => $products->where('brand_slug', $product['brand_slug'])->count(),
            'logo' => null,
            'image' => null,
        ]);

        return $configured->merge($fromProducts)->unique('slug')->values();
    }

    private function productBrandsForCategory(string $categorySlug)
    {
        $allowedBrands = collect($this->categoryBrandMap()[$categorySlug] ?? []);

        if ($allowedBrands->isEmpty()) {
            return $this->productBrands();
        }

        $products = collect($this->productsData());

        return $this->productBrands()
            ->filter(fn ($brand) => $allowedBrands->contains($brand['slug']))
            ->map(fn ($brand) => [
                ...$brand,
                'count' => $products
                    ->whereIn('category_slug', $this->productCategoryAndDescendantSlugs($categorySlug))
                    ->where('brand_slug', $brand['slug'])
                    ->count(),
            ])
            ->values();
    }

    private function normalizeProductCategorySeoContent(array $categorySeo, array $category): array
    {
        $finalMap = [
            'teraziler' => ['title' => 'Hassas Terazi ve Analitik Terazi Modelleri', 'cta' => 'Terazi İçin Teklif Al'],
            'nem-tayin' => ['title' => 'Nem Tayin Cihazı Modelleri', 'cta' => 'Nem Tayin Cihazı İçin Teklif Al'],
            'kral-fischer' => ['title' => 'Karl Fischer Titratör ve Su Miktarı Tayin Cihazları', 'cta' => 'Karl Fischer Cihazı İçin Teklif Al'],
            'potansiyometrik-titratorler' => ['title' => 'Potansiyometrik Titratör ve Otomatik Titrasyon Cihazları', 'h1' => 'Potansiyometrik Titratörler', 'cta' => 'Titratör İçin Teklif Al'],
            'densitometre' => ['title' => 'Densitometre ve Yoğunluk Ölçer Modelleri', 'cta' => 'Densitometre İçin Teklif Al'],
            'refraktometre' => ['title' => 'Refraktometre ve Dijital Refraktometre Modelleri', 'cta' => 'Refraktometre İçin Teklif Al'],
            'ph-metre' => ['title' => 'pH Metre Modelleri ve Laboratuvar pH Ölçüm Cihazları', 'cta' => 'pH Metre İçin Teklif Al'],
            'ph-iletkenlik' => ['title' => 'pH İletkenlik & Metreler', 'cta' => 'pH İletkenlik & Metreler İçin Teklif Al'],
            'viskozimetre' => ['title' => 'Viskozimetre ve Viskozite Ölçüm Cihazları', 'cta' => 'Viskozimetre İçin Teklif Al'],
            'rotasyonel-viskozimetre' => ['title' => 'Rotasyonel Viskozimetre Modelleri', 'cta' => 'Rotasyonel Viskozimetre İçin Teklif Al'],
            'tekstur-analiz-cihazi' => ['title' => 'Tekstür Analiz Cihazı Modelleri', 'cta' => 'Tekstür Analiz Cihazı İçin Teklif Al'],
            'analitik-teraziler' => ['title' => 'Analitik Terazi Modelleri', 'h1' => 'Analitik Teraziler', 'cta' => 'Analitik Terazi İçin Teklif Al'],
            'hassas-teraziler' => ['title' => 'Hassas Terazi Modelleri', 'h1' => 'Hassas Teraziler', 'cta' => 'Hassas Terazi İçin Teklif Al'],
            'endustriyel-teraziler' => ['title' => 'Endüstriyel Terazi Modelleri', 'h1' => 'Endüstriyel Teraziler', 'cta' => 'Endüstriyel Terazi İçin Teklif Al'],
            'mikro-teraziler' => ['title' => 'Mikro Terazi Modelleri', 'h1' => 'Mikro Teraziler', 'cta' => 'Mikro Terazi İçin Teklif Al'],
            'karistiricilar' => ['title' => 'Karıştırıcılar', 'cta' => 'Karıştırıcı İçin Teklif Al'],
            'isitmali-manyetik-karistirici' => ['title' => 'Isıtmalı Manyetik Karıştırıcı Modelleri', 'h1' => 'Isıtmalı Manyetik Karıştırıcılar', 'cta' => 'Isıtmalı Manyetik Karıştırıcı İçin Teklif Al'],
            'isitmasiz-manyetik-karistirici' => ['title' => 'Isıtmasız Manyetik Karıştırıcı Modelleri', 'h1' => 'Isıtmasız Manyetik Karıştırıcılar', 'cta' => 'Isıtmasız Manyetik Karıştırıcı İçin Teklif Al'],
            'vorteks-karistiricilar' => ['title' => 'Vorteks Karıştırıcı Modelleri', 'h1' => 'Vorteks Karıştırıcılar', 'cta' => 'Vorteks Karıştırıcı İçin Teklif Al'],
            'jar-test' => ['title' => 'Jar Test Cihazları', 'cta' => 'Jar Test İçin Teklif Al'],
            'diger-cevre-cihazlari' => ['title' => 'Diğer Çevre Cihazları', 'cta' => 'Çevre Cihazları İçin Teklif Al'],
            'sogutmali-inkubator' => ['title' => 'Soğutmalı İnkübatör Modelleri', 'h1' => 'Soğutmalı İnkübatör', 'cta' => 'Soğutmalı İnkübatör İçin Teklif Al'],
            'boi-olcum-cihazi' => ['title' => 'BOİ Ölçüm Cihazı Modelleri', 'h1' => 'BOİ Ölçüm Cihazı', 'cta' => 'BOİ Ölçüm Cihazı İçin Teklif Al'],
            'hot-plate' => ['title' => 'Hot Plate ve Isıtıcı Tabla Modelleri', 'h1' => 'Hot Plate', 'cta' => 'Hot Plate İçin Teklif Al'],
            'rotator-calkalayici' => ['title' => 'Rotatör Çalkalayıcı Modelleri', 'h1' => 'Rotatör Çalkalayıcı', 'cta' => 'Rotatör Çalkalayıcı İçin Teklif Al'],
            'pipetler' => ['title' => 'Pipet ve Otomatik Pipet Modelleri', 'h1' => 'Pipetler', 'cta' => 'Pipet İçin Teklif Al'],
            'su-banyolari' => ['title' => 'Su Banyoları', 'cta' => 'Su Banyosu İçin Teklif Al'],
            'su-banyosu' => ['title' => 'Su Banyosu Modelleri', 'h1' => 'Su Banyosu', 'cta' => 'Su Banyosu İçin Teklif Al'],
            'ultrasonik-banyo' => ['title' => 'Ultrasonik Banyo Modelleri', 'h1' => 'Ultrasonik Banyo', 'cta' => 'Ultrasonik Banyo İçin Teklif Al'],
            'santrifujler' => ['title' => 'Santrifüjler', 'cta' => 'Santrifüj İçin Teklif Al'],
            'inkubatorler' => ['title' => 'İnkübatörler', 'cta' => 'İnkübatör İçin Teklif Al'],
            'erime-noktasi' => ['title' => 'Erime Noktası Tayin Cihazları', 'h1' => 'Erime Noktası', 'cta' => 'Erime Noktası Cihazı İçin Teklif Al'],
            'polarimetreler' => ['title' => 'Polarimetreler', 'cta' => 'Polarimetre İçin Teklif Al'],
            'etuv' => ['title' => 'Etüv Cihazı ve Laboratuvar Etüvü Modelleri', 'cta' => 'Etüv İçin Teklif Al'],
            'balon-isiticilar' => ['title' => 'Balon Isıtıcı ve Laboratuvar Isıtıcı Modelleri', 'h1' => 'Balon Isıtıcılar', 'cta' => 'Balon Isıtıcı İçin Teklif Al'],
            'termoreaktor' => ['title' => 'Termoreaktör ve Laboratuvar Sindirim Cihazları', 'cta' => 'Termoreaktör İçin Teklif Al'],
            'homojenizator' => ['title' => 'Homojenizatör ve Numune Hazırlama Cihazları', 'cta' => 'Homojenizatör İçin Teklif Al'],
            'mekanik-karistirici' => ['title' => 'Mekanik Karıştırıcı Modelleri', 'cta' => 'Mekanik Karıştırıcı İçin Teklif Al'],
            'manyetik-karistirici' => ['title' => 'Manyetik Karıştırıcı ve Isıtmalı Manyetik Karıştırıcı Modelleri', 'cta' => 'Manyetik Karıştırıcı İçin Teklif Al'],
        ];

        $slug = $categorySeo['slug'] ?? $category['slug'];
        $categorySeo = array_merge(['slug' => $slug], $categorySeo);
        $final = $finalMap[$slug] ?? [
            'title' => $category['name'] . ' Modelleri',
            'h1' => $category['name'],
            'cta' => $category['name'] . ' İçin Teklif Al',
        ];

        $h1 = $final['h1'] ?? $final['title'];
        $categoryName = $category['name'];
        $cta = $final['cta'];

        $categorySeo['meta_title'] = $categorySeo['meta_title'] ?? $final['title'] . ' | MTA Endüstri';
        $categorySeo['meta_description'] = $categorySeo['meta_description'] ?? "{$final['title']} kategorisindeki ürünleri marka, model ve teknik özellik bilgileriyle inceleyin; MTA Endüstri’den teklif alın.";
        $categorySeo['h1'] = $categorySeo['h1'] ?? $h1;
        $categorySeo['hero_text'] = $categorySeo['hero_text'] ?? "Bu kategori, laboratuvar ve kalite kontrol süreçlerinde kullanılan {$categoryName} ürünlerini marka, model ve teknik özellik bilgileriyle incelemek için hazırlanmıştır. Ürünler sepet/ödeme mantığıyla değil, teknik bilgi ve teklif talebi akışıyla sunulur.";
        $categorySeo['primary_cta'] = $categorySeo['primary_cta'] ?? $cta;
        $categorySeo['secondary_cta'] = $categorySeo['secondary_cta'] ?? 'Teknik Servisi İncele';
        $categorySeo['secondary_cta_url'] = $categorySeo['secondary_cta_url'] ?? route('technical-services.index');

        if (empty($categorySeo['sections']) || count($categorySeo['sections']) < 3) {
            $categorySeo['sections'] = $this->defaultProductCategorySections($categoryName, $slug);
        }

        $categorySeo['brand_section'] = $categorySeo['brand_section'] ?? [
            'title' => "{$categoryName} Markaları",
            'text' => "{$categoryName} ürünleri marka, model, kullanım amacı ve teknik özellik ihtiyacına göre karşılaştırılabilir. Resmi distribütörlük veya yetkili servis iddiası ancak belgeyle doğrulandığında kullanılmalıdır.",
        ];
        $categorySeo['list_title'] = $categorySeo['list_title'] ?? "{$categoryName} Ürün Listesi";
        $categorySeo['list_section'] = $categorySeo['list_section'] ?? [
            'title' => "{$categoryName} Seçiminde Kontrol Edilecek Bilgiler",
            'text' => "Teklif öncesinde cihazın kullanım alanı, beklenen performans, ölçüm veya proses koşulları, aksesuar ihtiyacı ve servis/kalibrasyon ilişkisi birlikte değerlendirilmelidir.",
            'items' => [
                'Marka, model ve ürün kategorisi',
                'Kapasite, ölçüm aralığı veya çalışma koşulları',
                'Kullanım alanı ve numune/proses tipi',
                'Teknik servis, yedek parça ve kalibrasyon ihtiyacı',
            ],
        ];
        $categorySeo['support_links'] = $categorySeo['support_links'] ?? [
            ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
            ['url' => route('technical-services.index'), 'anchor' => 'laboratuvar cihazları teknik servis'],
            ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
            ['url' => route('contact'), 'anchor' => Str::lower($cta)],
        ];
        $categorySeo['faq'] = $categorySeo['faq'] ?? [
            ['question' => "{$categoryName} seçerken nelere dikkat edilmeli?", 'answer' => "{$categoryName} seçiminde kullanım amacı, teknik özellikler, marka/model uyumu, aksesuar ihtiyacı, servis desteği ve kalibrasyon gereksinimi birlikte değerlendirilmelidir."],
            ['question' => "{$categoryName} için online sipariş verilebilir mi?", 'answer' => 'MTA Endüstri ürünleri katalog mantığında sunulur. Satın alma süreci ürün bilgisi ve teklif talebi üzerinden ilerler.'],
            ['question' => 'Ürün için teknik destek alınabilir mi?', 'answer' => 'Evet. Ürün grubu, marka, model ve kullanım ihtiyacı paylaşıldığında teknik ekip ilgili kategoriye göre değerlendirme yapabilir.'],
        ];
        $categorySeo['empty_state'] = $categorySeo['empty_state'] ?? [
            'title' => "{$categoryName} için teknik ürün talebi oluşturun",
            'text' => "Bu kategoride aradığınız marka veya modeli listede göremiyorsanız kullanım alanı, teknik özellik ve adet bilgisini paylaşarak {$categoryName} için teklif talebi oluşturabilirsiniz.",
        ];
        $categorySeo['cta'] = array_merge($categorySeo['cta'] ?? [], [
            'title' => $cta,
            'text' => "İlgilendiğiniz {$categoryName} ürününü, markayı, modeli veya teknik ihtiyacı paylaşarak MTA Endüstri’den teklif talebi oluşturabilirsiniz.",
            'note' => 'Talebinizi doğru kategori ve marka bilgisiyle eşleştirip teknik ekibin değerlendirmesine açalım.',
            'button' => $cta,
            'anchor' => Str::lower($cta),
        ]);
        $categorySeo['robots'] = $final['robots'] ?? 'index,follow';

        return $categorySeo;
    }

    private function defaultProductCategorySections(string $categoryName, string $slug): array
    {
        $type = $this->productTypeFromCategory($slug, $categoryName);

        return [
            [
                'title' => "{$categoryName} Kullanım Alanları",
                'text' => "{$categoryName} ürünleri laboratuvar, kalite kontrol, AR-GE ve üretim destek süreçlerinde kullanım amacına göre değerlendirilir. Doğru ürün seçimi için yalnızca ürün adı değil, çalışma koşulları ve teknik beklenti de netleştirilmelidir.",
            ],
            [
                'title' => 'Seçim Kriterleri',
                'text' => "{$type} seçiminde kullanım alanı, numune veya proses tipi, kapasite, ölçüm aralığı, doğruluk, aksesuar uyumluluğu, bakım ihtiyacı, teknik servis ve ilgili kalibrasyon gereksinimleri birlikte değerlendirilmelidir.",
            ],
            [
                'title' => 'Ürün ve Hizmet Eşleşmesi',
                'text' => 'Bu kategorideki cihazlar kullanım amacına göre ilgili kalibrasyon ve teknik servis süreçleriyle ilişkilendirilebilir. Cihaz seçimi yapılırken yalnızca ilk satın alma değil, bakım, servis, kalibrasyon ve doküman ihtiyacı da değerlendirilmelidir.',
            ],
            [
                'title' => 'Teklif Talebi İçin Gerekli Bilgiler',
                'text' => "{$categoryName} teklifi için marka/model tercihi, kullanım alanı, adet, teknik özellik beklentisi ve varsa mevcut cihaz bilgisi paylaşılmalıdır. Bu bilgiler talebin doğru ürün grubu ve teknik değerlendirmeyle eşleşmesini sağlar.",
            ],
        ];
    }

    private function normalizeProductDetailContent(array $product): array
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? '';
        $category = $product['category'] ?? 'Laboratuvar cihazları';
        $type = $this->productTypeFromCategory($product['category_slug'] ?? '', $category);
        $title = trim($brand . ' ' . $model . ' ' . $type);

        if (
            $title === $type
            || Str::contains(Str::lower($product['name']), Str::lower($type))
            || ($model !== '' && Str::contains(Str::lower($product['name']), Str::lower($model)))
        ) {
            $title = $product['name'];
        }

        if (! empty($product['_has_custom_detail_seo'])) {
            $product['seo_title'] = $product['seo_title'] ?? $this->productSeoTitle($product, $product['name'], $type);
            $product['meta_description'] = $product['meta_description'] ?? trim("{$product['name']} için marka, model, kategori ve teknik özellikleri inceleyin. Ürün bilgisi ve teklif talebi için MTA Endüstri ile iletişime geçin.");
            $product['summary'] = $product['summary'] ?: "{$product['name']}, {$category} grubunda yer alan {$brand} markalı bir laboratuvar cihazıdır.";
            $product['image_alt'] = $product['image_alt'] ?: "{$product['name']} ürün görseli";

            if (empty($product['features'])) {
                $product['features'] = [
                    "{$category} kategorisinde teknik ürün değerlendirmesi",
                    "{$brand} marka ve {$model} model bilgisiyle katalog kaydı",
                    'Teklif öncesi kullanım alanı ve teknik gereksinim değerlendirmesi',
                ];
            }

            if (empty($product['metadata'])) {
                $product['metadata'] = [
                    'Marka' => $brand ?: 'MTA Endüstri',
                    'Kategori' => $category,
                    'Model' => $model ?: $product['name'],
                    'Teklif tipi' => 'Teknik bilgi ve teklif talebi',
                ];
            }

            return $product;
        }

        $product['name'] = $title;
        $product['seo_title'] = $this->productSeoTitle($product, $title, $type);
        $product['meta_description'] = $product['meta_description'] ?: trim("{$title} için marka, model, kategori ve teknik özellikleri inceleyin. Ürün bilgisi ve teklif talebi için MTA Endüstri ile iletişime geçin.");
        $product['summary'] = $product['summary'] ?: "{$title}, {$category} grubunda yer alan {$brand} markalı bir laboratuvar cihazıdır. Ürün sayfasında model bilgisi, teknik özellikler ve kullanım alanı bilgileri birlikte değerlendirilir. Satın alma süreci sepet/ödeme üzerinden değil, teknik bilgi ve teklif talebi üzerinden ilerler.";
        $product['image_alt'] = $product['image_alt'] ?: "{$title} ürün görseli";

        if (empty($product['features'])) {
            $product['features'] = [
                "{$category} kategorisinde teknik ürün değerlendirmesi",
                "{$brand} marka ve {$model} model bilgisiyle katalog kaydı",
                'Teklif öncesi kullanım alanı ve teknik gereksinim değerlendirmesi',
            ];
        }

        if (empty($product['metadata'])) {
            $product['metadata'] = [
                'Marka' => $brand ?: 'MTA Endüstri',
                'Kategori' => $category,
                'Model' => $model ?: $product['name'],
                'Teklif tipi' => 'Teknik bilgi ve teklif talebi',
            ];
        }

        return $product;
    }

    private function normalizeProductDetailSeoContent(array $productSeo, array $product): array
    {
        $productSeo = $productSeo === [] ? [] : $productSeo;

        $productSeo['primary_cta'] = $productSeo['primary_cta'] ?? 'Ürün İçin Teklif Al';
        $productSeo['sections'] = $productSeo['sections'] ?? [
            [
                'title' => 'Ürün Açıklaması',
                'text' => $product['summary'],
            ],
            [
                'title' => 'Teknik Değerlendirme',
                'text' => "{$product['name']} değerlendirilirken kullanım amacı, numune veya ölçüm koşulları, teknik özellik beklentisi, aksesuar ihtiyacı ve varsa servis/kalibrasyon gereksinimi birlikte ele alınmalıdır.",
            ],
            [
                'title' => 'Teklif Süreci',
                'text' => 'MTA Endüstri ürün kataloğu sepet ve online ödeme yerine B2B teklif talebi mantığıyla çalışır. İlgili ürün için marka, model, adet ve kullanım alanı bilgileri paylaşıldığında teknik ekip talebi doğru kategoriyle eşleştirir.',
            ],
        ];
        $productSeo['support_links'] = $productSeo['support_links'] ?? [
            ['url' => route('products.category', $product['category_slug']), 'anchor' => Str::lower($product['category']) . ' ürünleri'],
            ['url' => route('products.brand', $product['brand_slug']), 'anchor' => $product['brand'] . ' ürünleri'],
            ['url' => route('contact'), 'anchor' => 'ürün için teklif al'],
        ];
        $productSeo['faq'] = $productSeo['faq'] ?? [
            ['question' => "{$product['name']} için nasıl teklif alınır?", 'answer' => 'Ürün için marka, model, adet ve kullanım alanı bilgileri paylaşılarak MTA Endüstri iletişim kanallarından teklif talebi oluşturulabilir.'],
            ['question' => "{$product['name']} hangi kategori altında değerlendirilir?", 'answer' => "{$product['name']}, {$product['category']} kategorisi altında marka ve model bilgileriyle listelenir."],
            ['question' => 'Ürün seçiminde hangi bilgiler önemlidir?', 'answer' => 'Kullanım alanı, ölçüm veya proses koşulları, teknik özellikler, aksesuar ihtiyacı, servis ve kalibrasyon gereksinimleri birlikte değerlendirilmelidir.'],
        ];
        $productSeo['cta'] = $productSeo['cta'] ?? [
            'title' => 'Ürün İçin Teklif Al',
            'text' => 'İlgilendiğiniz ürün için marka, model, adet ve kullanım alanı bilgilerinizi paylaşarak teklif talebi oluşturabilirsiniz.',
            'button' => 'Ürün İçin Teklif Al',
        ];

        return $productSeo;
    }

    private function productSeoTitle(array $product, string $title, string $type): string
    {
        $brandModel = trim(($product['brand'] ?? '') . ' ' . ($product['model'] ?? '') . ' ' . $type);
        $cleanTitle = trim(preg_replace('/\s*\([^)]*\)/u', '', $title) ?? $title);
        $cleanTitle = str_replace(['kΩ', 'kΩ', 'kω', '®', '™'], ['kOhm', 'kOhm', 'kOhm', '', ''], $cleanTitle);
        $brandModel = trim(str_replace(['kΩ', 'kΩ', 'kω', '®', '™'], ['kOhm', 'kOhm', 'kOhm', '', ''], $brandModel));
        $baseTitle = mb_strlen($brandModel) >= 16 && mb_strlen($brandModel) <= 58 ? $brandModel : $cleanTitle;

        if (mb_strlen($baseTitle) > 58) {
            $baseTitle = trim(mb_substr($baseTitle, 0, 58));
            $baseTitle = preg_replace('/\s+\S*$/u', '', $baseTitle) ?: $baseTitle;
        }

        return $baseTitle . ' | MTA Endüstri';
    }

    private function productMetaDescription(array $product, $relatedServices): string
    {
        $description = $product['meta_description'] ?? $product['summary'];

        if ($relatedServices->isNotEmpty()) {
            $description .= ' İlgili hizmet: ' . $relatedServices->pluck('title')->take(2)->implode(', ') . '.';
        }

        return Str::limit($description, 165, '');
    }

    private function productTypeFromCategory(string $slug, string $category): string
    {
        return [
            'teraziler' => 'Hassas Terazi',
            'nem-tayin' => 'Nem Tayin Cihazı',
            'kral-fischer' => 'Karl Fischer Titratör',
            'potansiyometrik-titratorler' => 'Potansiyometrik Titratör',
            'densitometre' => 'Densitometre',
            'refraktometre' => 'Refraktometre',
            'ph-metre' => 'pH Metre',
            'ph-iletkenlik' => 'pH İletkenlik & Metreler',
            'viskozimetre' => 'Viskozimetre',
            'rotasyonel-viskozimetre' => 'Rotasyonel Viskozimetre',
            'tekstur-analiz-cihazi' => 'Tekstür Analiz Cihazı',
            'analitik-teraziler' => 'Analitik Terazi',
            'hassas-teraziler' => 'Hassas Terazi',
            'endustriyel-teraziler' => 'Endüstriyel Terazi',
            'mikro-teraziler' => 'Mikro Terazi',
            'karistiricilar' => 'Karıştırıcılar',
            'isitmali-manyetik-karistirici' => 'Isıtmalı Manyetik Karıştırıcı',
            'isitmasiz-manyetik-karistirici' => 'Isıtmasız Manyetik Karıştırıcı',
            'vorteks-karistiricilar' => 'Vorteks Karıştırıcı',
            'jar-test' => 'Jar Test Cihazı',
            'diger-cevre-cihazlari' => 'Çevre Cihazı',
            'sogutmali-inkubator' => 'Soğutmalı İnkübatör',
            'boi-olcum-cihazi' => 'BOİ Ölçüm Cihazı',
            'hot-plate' => 'Hot Plate',
            'rotator-calkalayici' => 'Rotatör Çalkalayıcı',
            'su-banyolari' => 'Su Banyoları',
            'su-banyosu' => 'Su Banyosu',
            'ultrasonik-banyo' => 'Ultrasonik Banyo',
            'santrifujler' => 'Santrifüjler',
            'inkubatorler' => 'İnkübatörler',
            'erime-noktasi' => 'Erime Noktası',
            'polarimetreler' => 'Polarimetreler',
            'etuv' => 'Etüv Cihazı',
            'balon-isiticilar' => 'Balon Isıtıcı',
            'termoreaktor' => 'Termoreaktör',
            'homojenizator' => 'Homojenizatör',
            'mekanik-karistirici' => 'Mekanik Karıştırıcı',
            'manyetik-karistirici' => 'Manyetik Karıştırıcı',
        ][$slug] ?? $category;
    }

    private function productCategoriesForBrand(string $brandSlug)
    {
        $allowedCategories = collect($this->categoryBrandMap())
            ->filter(fn ($brands) => in_array($brandSlug, $brands, true))
            ->keys();

        if ($allowedCategories->isEmpty()) {
            return $this->productCategories();
        }

        $products = collect($this->productsData());

        return $this->productCategories()
            ->filter(fn ($category) => $allowedCategories->contains($category['slug']))
            ->map(fn ($category) => [
                ...$category,
                'count' => $products
                    ->where('category_slug', $category['slug'])
                    ->where('brand_slug', $brandSlug)
                    ->count(),
            ])
            ->values();
    }

    private function activeSpecFilters(Request $request): array
    {
        $filters = $request->query('ozellik', []);

        if (! is_array($filters)) {
            return [];
        }

        return collect($filters)
            ->mapWithKeys(fn ($value, $key) => [Str::slug((string) $key) => Str::slug((string) $value)])
            ->filter()
            ->all();
    }

    private function applySpecFilters($products, array $activeSpecs)
    {
        if ($activeSpecs === []) {
            return $products->values();
        }

        return $products
            ->filter(function ($product) use ($activeSpecs) {
                foreach ($activeSpecs as $labelSlug => $valueSlug) {
                    if (! $this->productMatchesSpec($product, $labelSlug, $valueSlug)) {
                        return false;
                    }
                }

                return true;
            })
            ->values();
    }

    private function productSpecFilters($products, array $activeSpecs)
    {
        // Admin-driven: spec keys the editor ticked in "Filtrede gösterilecek özellikler".
        $adminLabels = collect($products)
            ->flatMap(fn ($product) => $product['filter_keys'] ?? [])
            ->filter(fn ($label) => filled($label))
            ->unique(fn ($label) => Str::slug((string) $label))
            ->values();

        if ($adminLabels->isNotEmpty()) {
            return $this->buildSpecFilters($products, $activeSpecs, $adminLabels, 1, 40, 8);
        }

        // Fallback (no product configured yet): auto-detect from a known priority list.
        $priorityLabels = collect([
            'Hassasiyet',
            'Kapasite',
            'Cihaz Tipi',
            'Ürün Cinsi',
            'Ölçüm Aralığı',
            'Ph Aralığı',
            'Çalışma Sıcaklık Aralığı',
            'Sıcaklık Aralığı',
            'Dönme Hızı',
            'Şaft Materyali',
            'Kablo Ve Bağlantı',
        ]);

        return $this->buildSpecFilters($products, $activeSpecs, $priorityLabels, 2, 18, 5);
    }

    private function buildSpecFilters($products, array $activeSpecs, $labels, int $minValues, int $maxValues, int $maxGroups)
    {
        return collect($labels)
            ->map(function ($label) use ($products, $activeSpecs, $minValues, $maxValues) {
                $labelSlug = Str::slug((string) $label);
                $values = collect($products)
                    ->map(fn ($product) => $this->specValueForLabel($product, $labelSlug))
                    ->filter(fn ($value) => $value !== null && mb_strlen($value) <= 90)
                    ->countBy()
                    ->sortDesc();

                if ($values->count() < $minValues || $values->count() > $maxValues) {
                    return null;
                }

                return [
                    'label' => $label,
                    'slug' => $labelSlug,
                    'active' => $activeSpecs[$labelSlug] ?? null,
                    'clear_url' => $this->specFilterUrl($activeSpecs, $labelSlug),
                    'options' => $values
                        ->take(15)
                        ->map(fn ($count, $value) => [
                            'value' => $value,
                            'slug' => Str::slug($value),
                            'count' => $count,
                            'url' => $this->specFilterUrl($activeSpecs, $labelSlug, Str::slug($value)),
                        ])
                        ->values(),
                ];
            })
            ->filter()
            ->take($maxGroups)
            ->values();
    }

    private function productMatchesSpec(array $product, string $labelSlug, string $valueSlug): bool
    {
        $value = $this->specValueForLabel($product, $labelSlug);

        return $value !== null && Str::slug($value) === $valueSlug;
    }

    private function specValueForLabel(array $product, string $labelSlug): ?string
    {
        foreach (($product['specs'] ?? []) as $label => $value) {
            if (Str::slug((string) $label) === $labelSlug && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function specFilterUrl(array $activeSpecs, string $labelSlug, ?string $valueSlug = null): string
    {
        $query = request()->query();
        $filters = $activeSpecs;

        if ($valueSlug === null) {
            unset($filters[$labelSlug]);
        } else {
            $filters[$labelSlug] = $valueSlug;
        }

        if ($filters === []) {
            unset($query['ozellik']);
        } else {
            $query['ozellik'] = $filters;
        }

        return url()->current() . ($query ? '?' . http_build_query($query) : '');
    }

    private function relatedServicesForProduct(array $product)
    {
        $manualServices = collect($product['related_services'] ?? []);
        $categoryServices = collect($this->categoryServiceMap()[$product['category_slug'] ?? ''] ?? []);
        $serviceSlugs = $manualServices->merge($categoryServices)->filter()->unique()->values();

        return collect($this->servicesData())
            ->filter(fn ($service) => $serviceSlugs->contains($service['slug']))
            ->values();
    }

    private function articleCategories()
    {
        $articles = collect($this->articlesData());

        return $articles->map(fn ($article) => [
            'name' => $article['category'],
            'slug' => $article['category_slug'],
            'count' => $articles->where('category_slug', $article['category_slug'])->count(),
        ])->unique('slug')->values();
    }

    private function brandsPageSeoContent(): array
    {
        return [
            'meta_title' => 'Laboratuvar Cihazları Markaları | MTA Endüstri',
            'meta_description' => 'MTA Endüstri ürün kataloğunda yer alan laboratuvar cihazları markalarını kategori, ürün grubu ve teknik teklif akışıyla inceleyin.',
            'h1' => 'Laboratuvar Cihazları Markaları',
            'hero_text' => 'MTA Endüstri ürün kataloğunda yer alan laboratuvar cihazları markalarını kategori, ürün grubu ve teknik teklif akışıyla inceleyin.',
            'primary_cta' => 'Marka Bazlı Ürünleri İncele',
            'secondary_cta' => 'Ürün Kataloğuna Git',
            'sections' => [
                [
                    'title' => 'Marka Bazlı Laboratuvar Cihazları',
                    'text' => 'Laboratuvar cihazı seçimi yapılırken yalnızca ürün kategorisi değil, marka, model, teknik özellik, uygulama alanı, servis desteği ve kalibrasyon ihtiyacı birlikte değerlendirilmelidir.',
                ],
                [
                    'title' => 'MTA Endüstri Ürün Kataloğunda Yer Alan Markalar',
                    'text' => 'Tartım cihazları, pH ve iletkenlik ölçerler, refraktometreler, densitometreler, viskozimetreler, titratörler, etüvler, karıştırıcılar ve homojenizatörler farklı markalar altında katalog yapısında listelenir.',
                ],
                [
                    'title' => 'Ürün Kategorilerine Göre Marka Seçimi',
                    'text' => 'Laboratuvar cihazı arayan kullanıcılar çoğu zaman önce kategoriye, ardından markaya göre karar verir. Bu nedenle her markanın hangi ürün kategorileriyle ilişkili olduğu açıkça gösterilmelidir.',
                ],
                [
                    'title' => 'Marka, Kalibrasyon ve Teknik Servis Bağlantısı',
                    'text' => 'Marka sayfaları yalnızca ürün listeleme amacı taşımaz; ilgili kalibrasyon ve teknik servis sayfalarıyla birlikte düşünülmelidir. Terazi markaları kütle-terazi kalibrasyonu, karıştırıcı ve homojenizatör grupları devir kalibrasyonu, sıcaklık kontrollü cihazlar sıcaklık kalibrasyonu ile ilişkilendirilebilir.',
                ],
                [
                    'title' => 'Marka Bazlı Ürün Teklif Süreci',
                    'text' => 'Belirli bir markaya ait laboratuvar cihazı veya ölçüm ekipmanı için marka adı, ürün kategorisi, model, kullanım alanı, teknik özellik beklentisi ve adet bilgileriyle teklif talebi oluşturulabilir.',
                ],
            ],
            'brand_cards' => [
                'and' => ['summary' => 'A&D; hassas terazi, analitik terazi ve laboratuvar tartım cihazlarıyla öne çıkan markalardan biridir.', 'anchor' => 'A&D hassas terazi modelleri', 'alt' => 'A&D hassas terazi marka logosu'],
                'ohaus' => ['summary' => 'Ohaus; terazi, nem tayin cihazı, pH metre ve laboratuvar ölçüm cihazları alanlarında katalogda yer alır.', 'anchor' => 'Ohaus laboratuvar cihazları', 'alt' => 'Ohaus laboratuvar cihazları marka logosu'],
                'shimadzu' => ['summary' => 'Shimadzu; analitik terazi, hassas terazi ve laboratuvar tartım uygulamalarıyla ilişkili ürün gruplarında değerlendirilir.', 'anchor' => 'Shimadzu analitik terazi modelleri', 'alt' => 'Shimadzu analitik terazi marka logosu'],
                'weightlab' => ['summary' => 'Weightlab; tartım cihazları, etüvler, balon ısıtıcılar, karıştırıcılar ve numune hazırlama cihazlarıyla katalog yapısında yer alır.', 'anchor' => 'Weightlab laboratuvar cihazları', 'alt' => 'Weightlab tartım ve laboratuvar cihazları marka logosu'],
                'mettler-toledo' => ['summary' => 'Mettler Toledo; pH metre, titratör, densitometre, refraktometre ve laboratuvar ölçüm cihazlarıyla ilişkilendirilebilir.', 'anchor' => 'Mettler Toledo ölçüm cihazları', 'alt' => 'Mettler Toledo ölçüm cihazları marka logosu'],
                'wtw' => ['summary' => 'WTW; pH metre, iletkenlik ölçer ve su analizi cihazlarıyla laboratuvar ve saha ölçümlerinde katalogda yer alır.', 'anchor' => 'WTW pH ve iletkenlik ölçer modelleri', 'alt' => 'WTW pH ve iletkenlik ölçer marka logosu'],
                'kyoto-kem' => ['summary' => 'Kyoto KEM; Karl Fischer titratör, potansiyometrik titratör, refraktometre ve densitometre gibi analiz cihazlarıyla ilişkilendirilebilir.', 'anchor' => 'Kyoto KEM titratör ve refraktometre modelleri', 'alt' => 'Kyoto KEM titratör ve refraktometre marka logosu'],
                'bellingham-stanley' => ['summary' => 'Bellingham + Stanley; refraktometre ve yoğunluk ölçüm cihazları alanında katalog yapısında yer alır.', 'anchor' => 'Bellingham + Stanley refraktometre modelleri', 'alt' => 'Bellingham + Stanley refraktometre marka logosu'],
                'lamy' => ['summary' => 'Lamy; viskozimetre ve reoloji uygulamalarına yönelik laboratuvar ölçüm cihazlarıyla katalogda yer alır.', 'anchor' => 'Lamy viskozimetre modelleri', 'alt' => 'Lamy viskozimetre marka logosu'],
                'velp' => ['summary' => 'VELP; manyetik karıştırıcı, mekanik karıştırıcı, homojenizatör, termoreaktör ve numune hazırlama cihazlarıyla ilişkilidir.', 'anchor' => 'VELP laboratuvar cihazları', 'alt' => 'VELP numune hazırlama cihazları marka logosu'],
                'si-analitik' => ['summary' => 'SI Analitik; titrasyon ve elektrokimya uygulamalarına yönelik laboratuvar analiz cihazlarıyla değerlendirilir.', 'anchor' => 'SI Analitik titrasyon cihazları', 'alt' => 'SI Analitik titrasyon cihazları marka logosu'],
                'stuart' => ['summary' => 'Cole-Parmer Stuart; laboratuvar karıştırma, ısıtma, inkübasyon ve erime noktası tayin cihazlarıyla katalogda yer alan markalardan biridir.', 'anchor' => 'Cole-Parmer Stuart laboratuvar cihazları', 'alt' => 'Cole-Parmer Stuart laboratuvar cihazları marka logosu'],
            ],
            'selection_items' => [
                'Ürün kategorisi ve uygulama alanı',
                'Teknik özellik ve ölçüm aralığı',
                'Kapasite, hassasiyet veya çözünürlük',
                'Servis ve bakım ihtiyacı',
                'Kalibrasyon bağlantısı',
                'Yedek parça ve aksesuar erişimi',
                'Teknik doküman desteği',
                'Kullanım yoğunluğu',
                'Laboratuvar koşulları',
                'Teklif ve teslimat süreci',
            ],
            'support_links' => [
                ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                ['url' => route('products.category', 'teraziler'), 'anchor' => 'hassas terazi markaları'],
                ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre markaları'],
                ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre markaları'],
                ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre markaları'],
                ['url' => route('products.category', 'kral-fischer'), 'anchor' => 'Karl Fischer titratör markaları'],
                ['url' => route('products.category', 'manyetik-karistirici'), 'anchor' => 'manyetik karıştırıcı markaları'],
                ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                ['url' => route('contact'), 'anchor' => 'marka bazlı ürün teklif talebi'],
            ],
            'cta' => [
                'title' => 'Laboratuvar Cihazları Markaları İçin Teklif Alın',
                'text' => 'İlgilendiğiniz marka ve ürün grubunu paylaşın; ihtiyacınıza uygun laboratuvar cihazı için teklif sürecini başlatalım.',
                'button' => 'Marka Bazlı Teklif Al',
                'secondary_button' => 'Ürün Kataloğunu İncele',
            ],
            'faq' => [
                ['question' => 'MTA Endüstri hangi laboratuvar cihazı markalarını listeler?', 'answer' => 'MTA Endüstri ürün kataloğunda A&D, Ohaus, Shimadzu, Weightlab, Mettler Toledo, WTW, Kyoto KEM, Bellingham + Stanley, Lamy, VELP, SI Analitik ve Cole-Parmer Stuart gibi markalara ait ürün grupları listelenebilir.'],
                ['question' => 'Marka sayfalarında hangi bilgiler bulunur?', 'answer' => 'Marka sayfalarında markaya ait ürünler, ilgili ürün kategorileri, kısa marka açıklaması, ürün detay bağlantıları ve teklif talebi yönlendirmeleri bulunur.'],
                ['question' => 'Laboratuvar cihazı markası seçerken nelere dikkat edilmeli?', 'answer' => 'Ürün kategorisi, uygulama alanı, teknik özellikler, servis desteği, kalibrasyon ihtiyacı, yedek parça erişimi ve uzun vadeli kullanım koşulları birlikte değerlendirilmelidir.'],
                ['question' => 'Belirli bir markanın ürünleri için teklif alabilir miyim?', 'answer' => 'Evet. İlgilendiğiniz marka, ürün kategorisi, model, kullanım alanı ve adet bilgilerini paylaşarak teklif talebi oluşturabilirsiniz.'],
            ],
        ];
    }

    private function normalizeBrandSeoContent(array $brandSeo, array $brand, $categories): array
    {
        $categoryLinks = collect($categories)->map(fn ($category) => [
            'url' => route('products.category', $category['slug']),
            'anchor' => $category['name'],
        ])->values()->all();

        $common = [
            'meta_title' => ($brandSeo['meta_title'] ?? $brand['name'] . ' Ürünleri | MTA Endüstri'),
            'meta_description' => "{$brand['name']} ürünlerini MTA Endüstri katalog yapısında kategori, model ve teknik özellik bilgileriyle inceleyin; teklif talebi oluşturun.",
            'h1' => $brandSeo['h1'] ?? $brand['name'] . ' Ürünleri',
            'hero_text' => "{$brand['name']} ürünleri MTA Endüstri ürün kataloğunda ilgili kategori, model ve teknik özellik bilgileriyle listelenir. Kullanıcılar marka altındaki ürünleri kullanım amacı, ürün grubu ve teknik gereksinime göre inceleyerek teklif talebi oluşturabilir.",
            'primary_cta' => "{$brand['name']} Ürünleri İçin Teklif Al",
            'secondary_cta' => 'Ürün Kataloğuna Git',
            'secondary_cta_url' => route('products.index'),
            'logo_alt' => $brandSeo['logo_alt'] ?? $brand['name'] . ' marka logosu',
            'sections' => [
                [
                    'title' => "{$brand['name']} Ürünleri",
                    'text' => "{$brand['name']} ürünleri kategori, model ve teknik özellik bilgileriyle teklif odaklı katalog yapısında sunulur. Sayfa, belgeyle doğrulanması gereken resmi marka ilişkisi iddiaları üretmeden ürün keşfine yardımcı olur.",
                ],
                [
                    'title' => 'Kategori ve Teklif Bağlantısı',
                    'text' => 'Marka sayfasındaki ürünler ilgili kategori sayfaları ve iletişim formuyla ilişkilendirilir. Kullanıcılar ihtiyaç duyduğu ürün grubunu belirleyerek teknik bilgi ve teklif talebi oluşturabilir.',
                ],
                [
                    'title' => 'Teknik Ürün Değerlendirmesi',
                    'text' => "{$brand['name']} markalı bir ürün değerlendirilirken cihazın kullanım amacı, teknik özellikleri, aksesuar ihtiyacı, servis gereksinimi ve varsa kalibrasyon ilişkisi birlikte ele alınmalıdır.",
                ],
                [
                    'title' => 'Marka İlişkisi Notu',
                    'text' => 'Bu sayfa ürün keşfi ve teklif talebi amacıyla hazırlanmıştır. Yetkili distribütörlük, Türkiye distribütörlüğü veya yetkili servis gibi resmi ilişki iddiaları ancak doğrulanmış belgeyle içerikte kullanılmalıdır.',
                ],
            ],
            'support_links' => array_merge($categoryLinks, [
                ['url' => route('products.brand', $brand['slug']), 'anchor' => $brand['name'] . ' ürünleri'],
                ['url' => route('contact'), 'anchor' => 'ürün için teklif al'],
            ]),
            'faq' => [
                ['question' => "{$brand['name']} ürünleri nasıl incelenir?", 'answer' => "{$brand['name']} ürünleri marka sayfasında kategori, model ve teknik özellik ilişkisiyle listelenir."],
                ['question' => "{$brand['name']} ürünleri için teklif alınabilir mi?", 'answer' => 'Evet. İlgili marka, ürün grubu, model, kullanım alanı ve adet bilgisi paylaşılarak teklif talebi oluşturulabilir.'],
                ['question' => 'Bu sayfa resmi marka ilişkisi bilgisi içerir mi?', 'answer' => 'Hayır. Resmi belge sağlanmadan kanıt gerektiren resmi marka ilişkisi iddiaları kullanılmaz.'],
            ],
            'cta' => [
                'title' => "{$brand['name']} Ürünleri İçin Teklif Alın",
                'text' => "{$brand['name']} markasına ait ürün grubu, model ve kullanım ihtiyacınızı paylaşarak teklif sürecini başlatabilirsiniz.",
                'button' => "{$brand['name']} Ürünleri İçin Teklif Al",
            ],
            'empty_state' => [
                'title' => "{$brand['name']} ürünleri için teknik talep oluşturun",
                'text' => "{$brand['name']} markasına ait aradığınız ürün grubu, model veya teknik ihtiyacı paylaşarak MTA Endüstri’den ürün bilgisi ve teklif talebi oluşturabilirsiniz.",
            ],
        ];

        return array_replace_recursive($common, $brandSeo);
    }

    private function brandSeoContent(string $slug): array
    {
        return match ($slug) {
            'and' => [
                'meta_title' => 'A&D Hassas Terazi ve Laboratuvar Terazileri | MTA Endüstri',
                'meta_description' => 'A&D hassas terazi, analitik terazi ve laboratuvar tartım cihazlarını teknik özellikleriyle inceleyin; MTA Endüstri’den teklif alın.',
                'h1' => 'A&D Hassas Terazi ve Laboratuvar Tartım Cihazları',
                'hero_text' => 'A&D; hassas terazi, analitik terazi ve laboratuvar tartım cihazlarıyla MTA Endüstri ürün kataloğunda yer alan markalardan biridir. Bu sayfada A&D markasına ait ürünleri kategori, model ve teknik özellik bilgileriyle inceleyebilir; ihtiyacınıza uygun ürün için teklif talebi oluşturabilirsiniz.',
                'primary_cta' => 'A&D Ürünleri İçin Teklif Al',
                'secondary_cta' => 'Terazi Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'kutle-terazi-kalibrasyonu'),
                'logo_alt' => 'A&D hassas terazi marka logosu',
                'sections' => [
                    ['title' => 'A&D Marka Ürün Grupları', 'text' => 'A&D ürünleri özellikle hassas tartım, laboratuvar terazisi ve nem tayin cihazı ihtiyaçlarıyla ilişkilendirilebilir. Kullanıcılar ürünleri kullanım alanı, kapasite, okunabilirlik ve teknik özelliklere göre değerlendirmelidir.'],
                    ['title' => 'A&D Terazi Modelleri', 'text' => 'A&D hassas terazi ve analitik terazi modelleri; kalite kontrol, AR-GE, numune hazırlama ve rutin laboratuvar tartım uygulamalarında kullanılabilir. Doğru model seçimi için kapasite, okunabilirlik, kalibrasyon tipi ve kullanım ortamı birlikte değerlendirilmelidir.'],
                    ['title' => 'A&D Ürünlerinde Kalibrasyon ve Teknik Servis', 'text' => 'A&D tartım cihazlarında ölçüm güvenilirliği için düzenli terazi kalibrasyonu değerlendirilmelidir. Cihazda stabilite problemi, sıfıra dönmeme, ekran hatası veya sapma varsa terazi teknik servis süreci gerekebilir.'],
                    ['title' => 'A&D Ürünleri İçin Teklif Alın', 'text' => 'A&D markasına ait hassas terazi, analitik terazi veya laboratuvar tartım cihazı ihtiyacınız için marka, model, kapasite ve adet bilgilerinizi paylaşarak teklif talebi oluşturabilirsiniz.'],
                ],
                'support_links' => [
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'hassas terazi ve analitik terazi modelleri'],
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'terazi kalibrasyonu'],
                    ['url' => route('technical-services.show', 'terazi-teknik-servis'), 'anchor' => 'terazi teknik servis'],
                    ['url' => route('contact'), 'anchor' => 'A&D ürün teklif talebi'],
                ],
                'faq' => [
                    ['question' => 'A&D hangi ürün gruplarıyla listelenir?', 'answer' => 'A&D ürünleri hassas terazi, analitik terazi, laboratuvar terazisi ve nem tayin cihazı ihtiyaçlarıyla ilişkilendirilebilir.'],
                    ['question' => 'A&D hassas terazi seçerken nelere bakılmalı?', 'answer' => 'Kapasite, okunabilirlik, kullanım ortamı, kalibrasyon tipi, servis ihtiyacı ve numune tipi birlikte değerlendirilmelidir.'],
                    ['question' => 'A&D teraziler için kalibrasyon gerekir mi?', 'answer' => 'Hassas tartım cihazlarında ölçüm güvenilirliği için düzenli terazi kalibrasyonu değerlendirilmelidir.'],
                ],
                'cta' => [
                    'title' => 'A&D Ürünleri İçin Teklif Alın',
                    'text' => 'A&D hassas terazi, analitik terazi veya laboratuvar tartım cihazı ihtiyacınızı paylaşarak teklif sürecini başlatabilirsiniz.',
                    'button' => 'A&D Ürünleri İçin Teklif Al',
                ],
            ],
            default => [],
        };
    }

    private function productDetailSeoContent(string $slug): array
    {
        if (preg_match('/^lamy-tx-700-(10|20|50|250|500)-n-tekstur-analiz-cihazi$/', $slug, $matches) === 1) {
            return $this->lamyTx700TextureAnalyzerSeoContent((int) $matches[1]);
        }

        if ($slug === 'lamy-first-plus-rotasyonel-viskozimetre-r2-r7-spindle-set') {
            return $this->lamyFirstPlusRotationalViscometerSeoContent('r2-r7');
        }

        if ($slug === 'lamy-first-plus-lr-rotasyonel-viskozimetre-l1-l4-spindle-set') {
            return $this->lamyFirstPlusRotationalViscometerSeoContent('l1-l4');
        }

        if ($slug === 'lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set') {
            return $this->lamyBOnePlusRotationalViscometerSeoContent();
        }

        return match ($slug) {
            'and-fz-500i-hassas-terazi' => [
                'meta_title' => 'A&D FZ-500i Hassas Terazi | 0.001 g Laboratuvar Terazisi',
                'meta_description' => 'A&D FZ-500i hassas terazi; 520 g kapasite ve 0.001 g okunabilirlik ile laboratuvar tartım süreçleri için incelenebilir. Teklif alın.',
                'h1' => 'A&D FZ-500i Hassas Terazi',
                'hero_text' => 'A&D FZ-500i hassas terazi, laboratuvar tartım süreçlerinde 0.001 g okunabilirlik ihtiyacı olan uygulamalar için değerlendirilebilecek bir tartım cihazıdır. MTA Endüstri ürün sayfasında A&D FZ-500i modelinin marka, kategori, teknik özellik ve ilgili kalibrasyon bağlantıları teklif odaklı katalog yapısıyla sunulur.',
                'primary_cta' => 'A&D FZ-500i İçin Teklif Al',
                'secondary_cta' => 'Terazi Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'kutle-terazi-kalibrasyonu'),
                'image_alt' => 'A&D FZ-500i 0.001 g hassas terazi ürün görseli',
                'metadata' => [
                    'Marka' => 'A&D',
                    'Kategori' => 'Teraziler',
                    'Model' => 'FZ-500i',
                    'SKU' => 'MTA-AND-FZ500I',
                    'Kullanım alanı' => 'Laboratuvar tartım',
                ],
                'specs' => [
                    'Marka' => 'A&D',
                    'Model' => 'FZ-500i',
                    'Kategori' => 'Hassas Terazi',
                    'Kapasite' => '520 g',
                    'Okunabilirlik' => '0.001 g',
                    'Kalibrasyon' => 'Harici kalibrasyon',
                    'Ekran' => 'LCD',
                    'Kullanım alanı' => 'Laboratuvar tartım',
                ],
                'sections' => [
                    ['title' => 'A&D FZ-500i Hassas Terazi Özeti', 'text' => 'A&D FZ-500i, hassas tartım gerektiren laboratuvar uygulamalarında kullanılmak üzere değerlendirilebilecek bir hassas terazi modelidir. Cihaz; kalite kontrol, numune hazırlama, AR-GE ve rutin laboratuvar tartım işlemlerinde kapasite, okunabilirlik ve kullanım koşullarına göre incelenmelidir.'],
                    ['title' => 'Laboratuvar Tartım Uygulamaları', 'text' => 'Hassas teraziler; düşük ağırlık farklarının güvenilir şekilde takip edilmesi gereken laboratuvar işlemlerinde kullanılır. A&D FZ-500i modeli; numune tartımı, çözelti hazırlama, kalite kontrol ve AR-GE süreçlerinde kullanım ihtiyacına göre değerlendirilebilir.'],
                    ['title' => 'A&D FZ-500i Kimler İçin Uygundur?', 'text' => 'Bu ürün; hassas tartım yapan laboratuvarlar, kalite kontrol birimleri, üretim destek ekipleri ve AR-GE uygulamaları için değerlendirilebilir. Numune tipi, maksimum tartım ihtiyacı, hassasiyet beklentisi ve çalışma ortamı cihaz seçiminde belirleyici olur.'],
                    ['title' => 'Hassas Terazi Seçerken Nelere Dikkat Edilmeli?', 'text' => 'Hassas terazi seçiminde kapasite, okunabilirlik, tekrarlanabilirlik, kalibrasyon yöntemi, kullanım ortamı, cihaz stabilitesi, ekran yapısı ve servis desteği birlikte değerlendirilmelidir.'],
                    ['title' => 'Terazi Kalibrasyonu ile İlişkisi', 'text' => 'A&D FZ-500i gibi hassas tartım cihazlarında ölçüm güvenilirliği için düzenli terazi kalibrasyonu değerlendirilmelidir. Kalibrasyonda cihaz belirli tartım noktalarında referans kütlelerle karşılaştırılır ve sonuçlar raporlanır.'],
                    ['title' => 'Ürün Teklif Süreci', 'text' => 'A&D FZ-500i hassas terazi için teklif almak isteyen kullanıcılar marka, model, adet, kullanım alanı ve varsa teknik beklentilerini paylaşarak MTA Endüstri teknik ekibine ulaşabilir.'],
                ],
                'support_links' => [
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'hassas terazi ve analitik terazi modelleri'],
                    ['url' => route('products.brand', 'and'), 'anchor' => 'A&D hassas terazi modelleri'],
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'terazi kalibrasyonu'],
                    ['url' => route('technical-services.show', 'terazi-teknik-servis'), 'anchor' => 'terazi teknik servis'],
                    ['url' => route('contact'), 'anchor' => 'A&D FZ-500i teklif talebi'],
                ],
                'faq' => [
                    ['question' => 'A&D FZ-500i hangi uygulamalarda kullanılır?', 'answer' => 'A&D FZ-500i hassas terazi; laboratuvar tartım, numune hazırlama, kalite kontrol ve AR-GE uygulamalarında kullanım ihtiyacına göre değerlendirilebilir.'],
                    ['question' => 'A&D FZ-500i kapasitesi nedir?', 'answer' => 'Bu ürün sayfasında A&D FZ-500i modeli 520 g kapasite ve 0.001 g okunabilirlik bilgisiyle listelenir.'],
                    ['question' => 'Hassas terazi seçiminde nelere dikkat edilmeli?', 'answer' => 'Kapasite, okunabilirlik, kullanım ortamı, kalibrasyon ihtiyacı, servis desteği ve tartılacak numune tipi birlikte değerlendirilmelidir.'],
                    ['question' => 'A&D FZ-500i için kalibrasyon gerekir mi?', 'answer' => 'Hassas tartım cihazlarında ölçüm güvenilirliği için düzenli terazi kalibrasyonu değerlendirilmelidir. Periyot, kullanım yoğunluğu ve kalite prosedürlerine göre belirlenir.'],
                    ['question' => 'Ürün fiyatı nasıl alınır?', 'answer' => 'MTA Endüstri ürünleri teklif odaklı katalog yapısıyla sunulur. A&D FZ-500i için iletişim formu üzerinden ürün teklif talebi oluşturulabilir.'],
                ],
                'cta' => [
                    'title' => 'A&D FZ-500i Hassas Terazi İçin Teklif Alın',
                    'text' => 'A&D FZ-500i hassas terazi için kullanım alanınızı ve adet bilginizi paylaşın; teknik ekibimiz teklif sürecini başlatsın.',
                    'button' => 'Ürün Hakkında Bilgi Al',
                ],
            ],
            'lamy-tx-700-250-n-tekstur-analiz-cihazi' => [
                'meta_title' => 'Lamy TX 700 250 N Tekstür Analiz Cihazı | MTA Endüstri',
                'meta_description' => 'Lamy TX 700 250 N tekstür analiz cihazı; 0.001 N çözünürlük, 7 inç dokunmatik ekran, PT100 sıcaklık probu ve USB veri aktarımıyla incelenebilir.',
                'h1' => 'Lamy TX 700 - 250 N Tekstür Analiz Cihazı',
                'hero_text' => 'Lamy TX 700 - 250 N tekstür analiz cihazı; doku, sertlik, kırılma, sıkıştırma ve benzeri tekstür analizlerinde kuvvet, hız, mesafe ve zaman parametrelerini birlikte değerlendirmek için kullanılan laboratuvar analiz cihazıdır. Ürün sayfasında 250 N sensör, 0.001 N çözünürlük, prob/hücre uyumu ve bağlantı özellikleri teklif odaklı katalog yapısıyla sunulur.',
                'primary_cta' => 'Lamy TX 700 İçin Teklif Al',
                'secondary_cta' => 'Teknik Özellikleri İncele',
                'secondary_cta_url' => '#teknik-ozellikler',
                'image_alt' => 'Lamy TX 700 250 N tekstür analiz cihazı ürün görseli',
                'metadata' => [
                    'Marka' => 'Lamy',
                    'Kategori' => 'Tekstür Analiz Cihazı',
                    'Üst kategori' => 'Viskozimetre',
                    'Model' => 'TX 700 - 250 N',
                    'SKU' => 'LB.LMY.N151250',
                    'Kuvvet kapasitesi' => '250 N (25 kg)',
                    'Çözünürlük' => '0.001 N (0.1 g)',
                    'Kullanım alanı' => 'Tekstür ve doku analizi',
                ],
                'specs' => [
                    'Marka' => 'Lamy',
                    'Model' => 'TX 700 - 250 N',
                    'Kategori' => 'Tekstür Analiz Cihazı',
                    'Üst kategori' => 'Viskozimetre',
                    'Sensör seçimi' => '10 N (1 kg), 20 N (2 kg), 50 N (5 kg), 250 N (25 kg), 500 N (50 kg)',
                    'Seçili sensör' => '250 N (25 kg)',
                    'Çözünürlük' => '0.001 N (0.1 g)',
                    'Hız' => '0.1 - 10 mm/s, +/-0.2%',
                    'Hassasiyet' => '+/- %0.05',
                    'Hareket' => '370 mm yükseklik, 0.1 mm çözünürlük',
                    'Ekran' => '7 inç dokunmatik ekran; güç, hız, mesafe, zaman ve hassasiyet seviyesi göstergeleri',
                    'Sıcaklık' => '-50...300 °C arası sıcaklık değeri gösteren PT100 sensör',
                    'Gerilim' => '90-240 VAC, 50/60 Hz',
                    'Dil' => 'Fransızca, İngilizce, Rusça, İspanyolca, Almanca, Türkçe',
                    'Emniyet ve gizlilik' => 'Kullanıcı adı ve şifre ile belirlenebilen operatör modu',
                    'PC bağlantısı' => 'RS232 portu ve USB',
                    'Yazıcı bağlantısı' => 'USB portu, PCL/5 uyumlu',
                    'Ölçüler' => '610 mm derinlik, 340 mm genişlik, 650 mm yükseklik',
                    'Ağırlık' => '22 kg',
                ],
                'sections' => [
                    ['title' => 'Lamy TX 700 - 250 N Tekstür Analiz Cihazı Özeti', 'text' => 'Lamy TX 700 - 250 N, numunelerin tekstür davranışını kuvvet, mesafe, hız ve zaman parametreleriyle değerlendirmek için kullanılan bir tekstür analiz cihazıdır. Gıda, kozmetik, ilaç, ambalaj, malzeme ve kalite kontrol laboratuvarlarında doku analizi ihtiyacına göre değerlendirilebilir.'],
                    ['title' => 'Tekstür Analizi Hangi Uygulamalarda Kullanılır?', 'text' => 'Tekstür analiz cihazları; sertlik, kırılma, sıkıştırma, penetrasyon, elastikiyet, yapışkanlık ve benzeri fiziksel özelliklerin incelendiği uygulamalarda kullanılır. Ürün geliştirme, kalite kontrol, reçete karşılaştırma ve proses takibi gibi çalışmalarda numune davranışını sayısal olarak değerlendirmeye yardımcı olur.'],
                    ['title' => '250 N Kuvvet Sensörü ve Çözünürlük', 'text' => 'Bu ürün 250 N (25 kg) seçili sensör bilgisiyle listelenmiştir. 0.001 N (0.1 g) çözünürlük, düşük kuvvet değişimlerinin takip edildiği testlerde cihazın teknik değerlendirmesinde dikkate alınması gereken ana alanlardan biridir. Aynı cihaz ailesinde farklı kuvvet sensörleri seçilebildiği için uygulama ihtiyacı sensör seçimini doğrudan etkiler.'],
                    ['title' => 'Prob, Hücre ve Yöntem Programlama', 'text' => 'TX 700 cihazı geniş prob ve hücre yelpazesiyle farklı numune tiplerine uyarlanabilir. Yöntem programlama ve kayıt özellikleri, tekrarlanabilir test koşullarının oluşturulmasına ve farklı numune sonuçlarının karşılaştırılmasına yardımcı olur.'],
                    ['title' => 'Ekran, Veri Aktarımı ve Laboratuvar Kullanımı', 'text' => '7 inç dokunmatik ekran, test sırasında güç, hız, mesafe, zaman ve hassasiyet seviyesi gibi bilgilerin izlenmesini sağlar. USB veri aktarımı ve RS232 bağlantısı, ölçüm sonuçlarının laboratuvar veri akışına dahil edilmesi gereken uygulamalarda önemli olabilir.'],
                    ['title' => 'Teklif Süreci İçin Gerekli Bilgiler', 'text' => 'Lamy TX 700 - 250 N için teklif talebi oluştururken numune tipi, hedef test metodu, ihtiyaç duyulan kuvvet sensörü, prob veya hücre tercihi, adet ve kullanım alanı bilgileri paylaşılmalıdır. Bu bilgiler ürünün doğru aksesuar ve teknik gereksinimlerle değerlendirilmesini sağlar.'],
                ],
                'support_links' => [
                    ['url' => route('products.category', 'tekstur-analiz-cihazi'), 'anchor' => 'tekstür analiz cihazı modelleri'],
                    ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre ve reoloji cihazları'],
                    ['url' => route('products.brand', 'lamy'), 'anchor' => 'Lamy laboratuvar cihazları'],
                    ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                    ['url' => route('contact'), 'anchor' => 'Lamy TX 700 teklif talebi'],
                ],
                'faq' => [
                    ['question' => 'Lamy TX 700 - 250 N ne için kullanılır?', 'answer' => 'Lamy TX 700 - 250 N; gıda, kozmetik, ilaç, ambalaj ve malzeme laboratuvarlarında numune tekstürünün kuvvet, mesafe, hız ve zaman parametreleriyle değerlendirilmesi için kullanılabilir.'],
                    ['question' => 'Bu ürün hangi üst kategori altında yer alır?', 'answer' => 'Bu ürün MTA Endüstri katalog yapısında Viskozimetre üst kategorisi altında Tekstür Analiz Cihazı alt kategorisinde listelenir.'],
                    ['question' => '250 N sensör hangi kapasiteye karşılık gelir?', 'answer' => '250 N sensör yaklaşık 25 kg kuvvet kapasitesiyle listelenmiştir. Uygun sensör seçimi numune tipi, test metodu ve beklenen kuvvet aralığına göre değerlendirilmelidir.'],
                    ['question' => 'Cihazda veri aktarımı var mı?', 'answer' => 'Ürün bilgisine göre cihazda USB veri aktarımı, RS232 PC bağlantısı ve USB üzerinden PCL/5 uyumlu yazıcı bağlantısı bulunur.'],
                    ['question' => 'Teklif almak için hangi bilgiler gerekir?', 'answer' => 'Numune tipi, hedef test metodu, kuvvet sensörü, prob veya hücre ihtiyacı, adet ve kullanım alanı bilgileri teklif talebinde paylaşılmalıdır.'],
                ],
                'cta' => [
                    'title' => 'Lamy TX 700 - 250 N İçin Teklif Alın',
                    'text' => 'Lamy TX 700 - 250 N tekstür analiz cihazı için numune tipinizi, test metodunuzu ve ihtiyaç duyduğunuz prob/sensör bilgisini paylaşın; teknik ekip teklif sürecini başlatsın.',
                    'button' => 'Ürün Hakkında Bilgi Al',
                ],
            ],
            'lamy-tx-700-500-n-tekstur-analiz-cihazi' => [
                'meta_title' => 'Lamy TX 700 500 N Tekstür Analiz Cihazı | MTA Endüstri',
                'meta_description' => 'Lamy TX 700 500 N tekstür analiz cihazı; 0.001 N çözünürlük, 7 inç dokunmatik ekran, PT100 sıcaklık probu ve USB veri aktarımıyla incelenebilir.',
                'h1' => 'Lamy TX 700 - 500 N Tekstür Analiz Cihazı',
                'hero_text' => 'Lamy TX 700 - 500 N tekstür analiz cihazı; daha yüksek kuvvet gerektiren doku, sertlik, kırılma, sıkıştırma ve benzeri tekstür analizlerinde kuvvet, hız, mesafe ve zaman parametrelerini birlikte değerlendirmek için kullanılan laboratuvar analiz cihazıdır. Ürün sayfasında 500 N sensör, 0.001 N çözünürlük, prob/hücre uyumu ve bağlantı özellikleri teklif odaklı katalog yapısıyla sunulur.',
                'primary_cta' => 'Lamy TX 700 500 N İçin Teklif Al',
                'secondary_cta' => 'Teknik Özellikleri İncele',
                'secondary_cta_url' => '#teknik-ozellikler',
                'image_alt' => 'Lamy TX 700 500 N tekstür analiz cihazı ürün görseli',
                'metadata' => [
                    'Marka' => 'Lamy',
                    'Kategori' => 'Tekstür Analiz Cihazı',
                    'Üst kategori' => 'Viskozimetre',
                    'Model' => 'TX 700 - 500 N',
                    'SKU' => 'LB.LMY.N151500',
                    'Kuvvet kapasitesi' => '500 N (50 kg)',
                    'Çözünürlük' => '0.001 N (0.1 g)',
                    'Kullanım alanı' => 'Tekstür ve doku analizi',
                ],
                'specs' => [
                    'Marka' => 'Lamy',
                    'Model' => 'TX 700 - 500 N',
                    'Kategori' => 'Tekstür Analiz Cihazı',
                    'Üst kategori' => 'Viskozimetre',
                    'Sensör seçimi' => '10 N (1 kg), 20 N (2 kg), 50 N (5 kg), 250 N (25 kg), 500 N (50 kg)',
                    'Seçili sensör' => '500 N (50 kg)',
                    'Çözünürlük' => '0.001 N (0.1 g)',
                    'Hız' => '0.1 - 10 mm/s, +/-0.2%',
                    'Hassasiyet' => '+/- %0.05',
                    'Hareket' => '370 mm yükseklik, 0.1 mm çözünürlük',
                    'Ekran' => '7 inç dokunmatik ekran; güç, hız, mesafe, zaman ve hassasiyet seviyesi göstergeleri',
                    'Sıcaklık' => '-50...300 °C arası sıcaklık değeri gösteren PT100 sensör',
                    'Gerilim' => '90-240 VAC, 50/60 Hz',
                    'Dil' => 'Fransızca, İngilizce, Rusça, İspanyolca, Almanca, Türkçe',
                    'Emniyet ve gizlilik' => 'Kullanıcı adı ve şifre ile belirlenebilen operatör modu',
                    'PC bağlantısı' => 'RS232 portu ve USB',
                    'Yazıcı bağlantısı' => 'USB portu, PCL/5 uyumlu',
                    'Ölçüler' => '610 mm derinlik, 340 mm genişlik, 650 mm yükseklik',
                    'Ağırlık' => '22 kg',
                ],
                'sections' => [
                    ['title' => 'Lamy TX 700 - 500 N Tekstür Analiz Cihazı Özeti', 'text' => 'Lamy TX 700 - 500 N, yüksek kuvvet gerektiren numunelerin tekstür davranışını kuvvet, mesafe, hız ve zaman parametreleriyle değerlendirmek için kullanılan bir tekstür analiz cihazıdır. Gıda, kozmetik, ilaç, ambalaj, malzeme ve kalite kontrol laboratuvarlarında doku analizi ihtiyacına göre değerlendirilebilir.'],
                    ['title' => 'Tekstür Analizi Hangi Uygulamalarda Kullanılır?', 'text' => 'Tekstür analiz cihazları; sertlik, kırılma, sıkıştırma, penetrasyon, elastikiyet, yapışkanlık ve benzeri fiziksel özelliklerin incelendiği uygulamalarda kullanılır. Ürün geliştirme, kalite kontrol, reçete karşılaştırma ve proses takibi gibi çalışmalarda numune davranışını sayısal olarak değerlendirmeye yardımcı olur.'],
                    ['title' => '500 N Kuvvet Sensörü ve Çözünürlük', 'text' => 'Bu ürün 500 N (50 kg) seçili sensör bilgisiyle listelenmiştir. 0.001 N (0.1 g) çözünürlük, daha yüksek kuvvet aralığında çalışırken test sonucunun okunabilirliği açısından cihazın teknik değerlendirmesinde dikkate alınması gereken ana alanlardan biridir. Aynı cihaz ailesinde farklı kuvvet sensörleri seçilebildiği için uygulama ihtiyacı sensör seçimini doğrudan etkiler.'],
                    ['title' => 'Prob, Hücre ve Yöntem Programlama', 'text' => 'TX 700 cihazı geniş prob ve hücre yelpazesiyle farklı numune tiplerine uyarlanabilir. Yöntem programlama ve kayıt özellikleri, tekrarlanabilir test koşullarının oluşturulmasına ve farklı numune sonuçlarının karşılaştırılmasına yardımcı olur.'],
                    ['title' => 'Ekran, Veri Aktarımı ve Laboratuvar Kullanımı', 'text' => '7 inç dokunmatik ekran, test sırasında güç, hız, mesafe, zaman ve hassasiyet seviyesi gibi bilgilerin izlenmesini sağlar. USB veri aktarımı ve RS232 bağlantısı, ölçüm sonuçlarının laboratuvar veri akışına dahil edilmesi gereken uygulamalarda önemli olabilir.'],
                    ['title' => 'Teklif Süreci İçin Gerekli Bilgiler', 'text' => 'Lamy TX 700 - 500 N için teklif talebi oluştururken numune tipi, hedef test metodu, ihtiyaç duyulan kuvvet sensörü, prob veya hücre tercihi, adet ve kullanım alanı bilgileri paylaşılmalıdır. Bu bilgiler ürünün doğru aksesuar ve teknik gereksinimlerle değerlendirilmesini sağlar.'],
                ],
                'support_links' => [
                    ['url' => route('products.category', 'tekstur-analiz-cihazi'), 'anchor' => 'tekstür analiz cihazı modelleri'],
                    ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre ve reoloji cihazları'],
                    ['url' => route('products.brand', 'lamy'), 'anchor' => 'Lamy laboratuvar cihazları'],
                    ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                    ['url' => route('contact'), 'anchor' => 'Lamy TX 700 500 N teklif talebi'],
                ],
                'faq' => [
                    ['question' => 'Lamy TX 700 - 500 N ne için kullanılır?', 'answer' => 'Lamy TX 700 - 500 N; yüksek kuvvet aralığı gerektiren gıda, kozmetik, ilaç, ambalaj ve malzeme numunelerinde tekstür davranışının değerlendirilmesi için kullanılabilir.'],
                    ['question' => 'Bu ürün hangi üst kategori altında yer alır?', 'answer' => 'Bu ürün MTA Endüstri katalog yapısında Viskozimetre üst kategorisi altında Tekstür Analiz Cihazı alt kategorisinde listelenir.'],
                    ['question' => '500 N sensör hangi kapasiteye karşılık gelir?', 'answer' => '500 N sensör yaklaşık 50 kg kuvvet kapasitesiyle listelenmiştir. Uygun sensör seçimi numune tipi, test metodu ve beklenen kuvvet aralığına göre değerlendirilmelidir.'],
                    ['question' => 'Cihazda veri aktarımı var mı?', 'answer' => 'Ürün bilgisine göre cihazda USB veri aktarımı, RS232 PC bağlantısı ve USB üzerinden PCL/5 uyumlu yazıcı bağlantısı bulunur.'],
                    ['question' => 'Teklif almak için hangi bilgiler gerekir?', 'answer' => 'Numune tipi, hedef test metodu, kuvvet sensörü, prob veya hücre ihtiyacı, adet ve kullanım alanı bilgileri teklif talebinde paylaşılmalıdır.'],
                ],
                'cta' => [
                    'title' => 'Lamy TX 700 - 500 N İçin Teklif Alın',
                    'text' => 'Lamy TX 700 - 500 N tekstür analiz cihazı için numune tipinizi, test metodunuzu ve ihtiyaç duyduğunuz prob/sensör bilgisini paylaşın; teknik ekip teklif sürecini başlatsın.',
                    'button' => 'Ürün Hakkında Bilgi Al',
                ],
            ],
            default => [],
        };
    }

    private function lamyFirstPlusRotationalViscometerSeoContent(string $variant): array
    {
        $variants = [
            'r2-r7' => [
                'name' => 'Lamy First Plus Rotasyonel Viskozimetre R2-R7 Spindle Set',
                'model' => 'First Plus R2-R7',
                'short_label' => 'R2-R7',
                'system' => 'R2 - R7',
                'spindle' => 'R-2 to R-7 spindle set ile',
                'range' => '100 - 180.000.000 mPa.s',
                'summary_context' => 'geniş viskozite aralığına ihtiyaç duyulan',
            ],
            'l1-l4' => [
                'name' => 'Lamy First Plus LR Rotasyonel Viskozimetre L1-L4 Spindle Set',
                'model' => 'First Plus LR L1-L4',
                'short_label' => 'L1-L4',
                'system' => 'L1 - L4',
                'spindle' => 'L1 - L4 spindle set ile',
                'range' => '15 - 22.000.000 mPa.s',
                'summary_context' => 'LR versiyon ve düşük viskozite aralığına ihtiyaç duyulan',
            ],
        ];

        $data = $variants[$variant] ?? $variants['r2-r7'];

        return [
            'meta_title' => $data['name'] . ' | MTA Endüstri',
            'meta_description' => "Lamy First Plus rotasyonel viskozimetre; {$data['short_label']} spindle set ile {$data['range']} viskozite aralığı, 0.3 - 250 rpm hız ve PT100 sensör sunar.",
            'h1' => $data['name'],
            'hero_text' => "Lamy First Plus rotasyonel viskozimetre; {$data['system']} sistemi ve {$data['range']} viskozite aralığında ölçüm yapmak için kullanılan, modern yaysız ölçüm teknolojisine sahip laboratuvar viskozimetresidir. Ürün sayfasında hız, tork, doğruluk, tekrarlanabilirlik, ölçüm sistemi, bağlantı ve ekran özellikleri teklif odaklı katalog yapısıyla sunulur.",
            'primary_cta' => 'Lamy First Plus İçin Teklif Al',
            'secondary_cta' => 'Teknik Özellikleri İncele',
            'secondary_cta_url' => '#teknik-ozellikler',
            'image_alt' => "Lamy First Plus rotasyonel viskozimetre {$data['short_label']} spindle set ürün görseli",
            'metadata' => [
                'Marka' => 'Lamy',
                'Kategori' => 'Rotasyonel Viskozimetre',
                'Üst kategori' => 'Viskozimetre',
                'Model' => $data['model'],
                'SKU' => 'Yayın öncesi netleştirilecek',
                'Viskozite aralığı' => $data['range'],
                'Spindle set' => $data['system'],
                'Kullanım alanı' => 'Viskozite ölçümü',
            ],
            'specs' => [
                'Marka' => 'Lamy',
                'Model' => $data['model'],
                'Kategori' => 'Rotasyonel Viskozimetre',
                'Üst kategori' => 'Viskozimetre',
                'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
                'Hız' => '0.3 - 250 rpm limitsiz',
                'Tork aralığı' => 'Standart versiyon: 0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
                'Doğrusallık' => 'Tam skalada +/- %1',
                'Tekrarlanabilirlik' => '+/- %0.2',
                'Viskozite ölçümü' => "{$data['system']} sistemi: {$data['range']}",
                'Spindle set' => $data['spindle'],
                'Sıcaklık' => '-50...300 °C arası sıcaklık değeri gösteren PT100 sensör',
                'Ölçüm sistemi' => 'MS DIN, MS ASTM, MS BV, MS VANE, MS SV, MS CP',
                'Sıcaklık kontrolü' => 'EVA DIN, EVA LR-BV, RT1, CP1',
                'PC bağlantısı' => 'RS232 portu ve USB',
                'Yazıcı bağlantısı' => 'USB host portu, PCL/5 uyumlu',
                'Ekran' => '7 inç dokunmatik renkli ekran',
                'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
                'Gerilim' => '90-240 VAC, 50/60 Hz',
                'Dil' => 'Fransızca, İngilizce, Rusça, İspanyolca',
                'Emniyet ve gizlilik' => 'Kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
                'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
                'Ağırlık' => '6.7 kg',
            ],
            'sections' => [
                ['title' => 'Lamy First Plus Rotasyonel Viskozimetre Özeti', 'text' => "Lamy First Plus, sıvı ve yarı akışkan numunelerde viskozite davranışını rotasyonel ölçüm prensibiyle değerlendirmek için kullanılan bir laboratuvar viskozimetresidir. {$data['short_label']} spindle set ile {$data['summary_context']} kalite kontrol, AR-GE ve eğitim laboratuvarlarında değerlendirilebilir."],
                ['title' => "{$data['short_label']} Spindle Set ve Ölçüm Aralığı", 'text' => "Bu ürün {$data['system']} sistemiyle {$data['range']} viskozite aralığında listelenmiştir. Spindle seçimi, numunenin beklenen viskozite değerine, ölçüm hızına ve uygulama metoduna göre belirlenmelidir."],
                ['title' => 'Rotasyonel Viskozimetre Kullanım Alanları', 'text' => 'Rotasyonel viskozimetreler gıda, kozmetik, ilaç, kimya, boya, petrol ürünleri, eğitim ve kalite kontrol laboratuvarlarında kullanılabilir. Numunenin akış davranışı, ürün kıvamı ve proses tekrarlanabilirliği açısından kritik bir parametre olabilir.'],
                ['title' => 'Hız, Tork ve Tekrarlanabilirlik', 'text' => '0.3 - 250 rpm hız aralığı, farklı viskozite seviyelerindeki numunelerin uygun ölçüm koşullarında değerlendirilmesine yardımcı olur. Tork aralığı, doğrusal ölçüm davranışı ve tekrarlanabilirlik değerleri cihaz seçiminde birlikte incelenmelidir.'],
                ['title' => 'Ekran, Bağlantı ve Veri Takibi', 'text' => '7 inç dokunmatik renkli ekran; viskozite, hız, tork, spindle, hassasiyet seviyesi ve tarih/saat gibi parametreleri aynı anda takip etmeyi sağlar. RS232 ve USB bağlantıları, ölçüm verilerinin bilgisayar veya yazıcı akışına dahil edilmesi gereken uygulamalarda önemlidir.'],
                ['title' => 'Teklif Süreci İçin Gerekli Bilgiler', 'text' => "Lamy First Plus rotasyonel viskozimetre için teklif talebi oluştururken numune tipi, beklenen viskozite aralığı, kullanılacak {$data['short_label']} spindle set, sıcaklık kontrol ihtiyacı, ölçüm standardı ve adet bilgileri paylaşılmalıdır."],
            ],
            'support_links' => [
                ['url' => route('products.category', 'rotasyonel-viskozimetre'), 'anchor' => 'rotasyonel viskozimetre modelleri'],
                ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre modelleri'],
                ['url' => route('products.brand', 'lamy'), 'anchor' => 'Lamy viskozimetre modelleri'],
                ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                ['url' => route('contact'), 'anchor' => 'Lamy First Plus teklif talebi'],
            ],
            'faq' => [
                ['question' => 'Lamy First Plus rotasyonel viskozimetre hangi uygulamalarda kullanılır?', 'answer' => 'Gıda, kozmetik, ilaç, kimya, petrol ürünleri, eğitim, kalite kontrol ve AR-GE laboratuvarlarında sıvı veya yarı akışkan numunelerin viskozite davranışını değerlendirmek için kullanılabilir.'],
                ['question' => "{$data['short_label']} sistemi hangi viskozite aralığını kapsar?", 'answer' => "Bu ürün sayfasında {$data['system']} sistemi {$data['range']} viskozite aralığıyla listelenmiştir."],
                ['question' => 'Cihazın hız aralığı nedir?', 'answer' => 'Lamy First Plus rotasyonel viskozimetre 0.3 - 250 rpm limitsiz hız aralığı bilgisiyle listelenmiştir.'],
                ['question' => 'Bu ürün hangi üst kategori altında yer alır?', 'answer' => 'Bu ürün Viskozimetre üst kategorisi altında Rotasyonel Viskozimetre alt kategorisinde listelenir.'],
                ['question' => 'Teklif almak için hangi bilgiler gerekir?', 'answer' => 'Numune tipi, beklenen viskozite aralığı, spindle set ihtiyacı, sıcaklık kontrol ihtiyacı, ölçüm standardı ve adet bilgileri teklif talebinde paylaşılmalıdır.'],
            ],
            'cta' => [
                'title' => 'Lamy First Plus Rotasyonel Viskozimetre İçin Teklif Alın',
                'text' => "Lamy First Plus rotasyonel viskozimetre için numune tipinizi, beklenen viskozite aralığınızı ve {$data['short_label']} spindle set ihtiyacınızı paylaşın; teknik ekip teklif sürecini başlatsın.",
                'button' => 'Ürün Hakkında Bilgi Al',
            ],
        ];
    }

    private function lamyBOnePlusRotationalViscometerSeoContent(): array
    {
        return [
            'meta_title' => 'Lamy B-One Plus Rotasyonel Viskozimetre L1-L4 Spindle Set | MTA Endüstri',
            'meta_description' => 'Lamy B-One Plus rotasyonel viskozimetre; L1-L4 spindle set ile 15 - 22.000.000 mPa.s aralık, 0.3 - 250 rpm hız, sonuç hafızası ve USB veri transferi sunar.',
            'h1' => 'Lamy B-One Plus Rotasyonel Viskozimetre L1-L4 Spindle Set',
            'hero_text' => 'Lamy B-One Plus rotasyonel viskozimetre; modern yaysız ölçüm teknolojisi, 7 inç dokunmatik ekran, sonuç hafızası ve USB üzerinden veri transferi özellikleriyle viskozite ölçümü için kullanılan laboratuvar cihazıdır. Bu ürün L1-L4 spindle set ile 15 - 22.000.000 mPa.s viskozite aralığına göre listelenir.',
            'primary_cta' => 'Lamy B-One Plus İçin Teklif Al',
            'secondary_cta' => 'Teknik Özellikleri İncele',
            'secondary_cta_url' => '#teknik-ozellikler',
            'image_alt' => 'Lamy B-One Plus rotasyonel viskozimetre L1 L4 spindle set ürün görseli',
            'metadata' => [
                'Marka' => 'Lamy',
                'Kategori' => 'Rotasyonel Viskozimetre',
                'Üst kategori' => 'Viskozimetre',
                'Model' => 'B-One Plus L1-L4',
                'SKU' => 'Yayın öncesi netleştirilecek',
                'Viskozite aralığı' => '15 - 22.000.000 mPa.s',
                'Spindle set' => 'L1 - L4',
                'Kullanım alanı' => 'Viskozite ölçümü',
            ],
            'specs' => [
                'Marka' => 'Lamy',
                'Model' => 'B-One Plus L1-L4',
                'Kategori' => 'Rotasyonel Viskozimetre',
                'Üst kategori' => 'Viskozimetre',
                'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
                'Hız' => '0.3 - 250 rpm limitsiz',
                'Tork aralığı' => 'Standart versiyon: 0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
                'Doğrusallık' => 'Tam skalada +/- %1',
                'Tekrarlanabilirlik' => '+/- %0.2',
                'Viskozite ölçüm aralığı' => 'L1 - L4 sistemi: 15 - 22.000.000 mPa.s; R2 - R7 sistemi: 200 - 240.000.000 mPa.s; KU sistemi: 40-141 KU',
                'Spindle set' => 'L-1 to L-4 spindle set ile',
                'Ekran' => '7 inç dokunmatik renkli ekran',
                'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
                'Veri aktarımı' => 'USB üzerinden veri transferi',
                'Sonuç hafızası' => 'Sonuç hafızası özelliği',
                'Gerilim' => '90-240 VAC, 50/60 Hz',
                'Dil' => 'Türkçe, Fransızca, İngilizce, Rusça, İspanyolca, Almanca',
                'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
                'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
                'Ağırlık' => '6.7 kg',
            ],
            'sections' => [
                ['title' => 'Lamy B-One Plus Rotasyonel Viskozimetre Özeti', 'text' => 'Lamy B-One Plus, sıvı ve yarı akışkan numunelerde viskozite davranışını rotasyonel ölçüm prensibiyle değerlendirmek için kullanılan bir laboratuvar viskozimetresidir. Modern yaysız ölçüm teknolojisi, dokunmatik ekran, sonuç hafızası ve USB veri transferiyle kalite kontrol ve AR-GE süreçlerinde değerlendirilebilir.'],
                ['title' => 'L1-L4 Spindle Set ve Ölçüm Aralığı', 'text' => 'Bu ürün L1-L4 spindle set ile 15 - 22.000.000 mPa.s viskozite aralığında listelenmiştir. Aynı teknik tabloda R2-R7 sistemi için 200 - 240.000.000 mPa.s ve KU sistemi için 40-141 KU aralığı da belirtilir.'],
                ['title' => 'Rotasyonel Viskozimetre Kullanım Alanları', 'text' => 'Rotasyonel viskozimetreler eğitim, gıda, kozmetik, eczane, kimya, petrol ürünleri ve kalite kontrol laboratuvarlarında kullanılabilir. Numunenin akış davranışı ve kıvam kontrolü cihaz seçiminde temel parametrelerdir.'],
                ['title' => 'Hız, Tork ve Tekrarlanabilirlik', 'text' => '0.3 - 250 rpm hız aralığı, farklı viskozite seviyelerindeki numunelerin uygun ölçüm koşullarında değerlendirilmesine yardımcı olur. Tork aralığı, doğrusal ölçüm davranışı ve tekrarlanabilirlik değerleri birlikte incelenmelidir.'],
                ['title' => 'Ekran, Hafıza ve Veri Transferi', 'text' => '7 inç dokunmatik renkli ekran; viskozite, hız, tork, spindle, hassasiyet seviyesi ve tarih/saat gibi parametreleri aynı anda takip etmeyi sağlar. Sonuç hafızası ve USB veri transferi, ölçüm kayıtlarının laboratuvar iş akışına alınmasına yardımcı olur.'],
                ['title' => 'Teklif Süreci İçin Gerekli Bilgiler', 'text' => 'Lamy B-One Plus rotasyonel viskozimetre için teklif talebi oluştururken numune tipi, beklenen viskozite aralığı, kullanılacak spindle set, ölçüm standardı, veri aktarım ihtiyacı ve adet bilgileri paylaşılmalıdır.'],
            ],
            'support_links' => [
                ['url' => route('products.category', 'rotasyonel-viskozimetre'), 'anchor' => 'rotasyonel viskozimetre modelleri'],
                ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre modelleri'],
                ['url' => route('products.brand', 'lamy'), 'anchor' => 'Lamy viskozimetre modelleri'],
                ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                ['url' => route('contact'), 'anchor' => 'Lamy B-One Plus teklif talebi'],
            ],
            'faq' => [
                ['question' => 'Lamy B-One Plus rotasyonel viskozimetre hangi uygulamalarda kullanılır?', 'answer' => 'Eğitim, gıda, kozmetik, eczane, kimya, petrol ürünleri, kalite kontrol ve AR-GE laboratuvarlarında sıvı veya yarı akışkan numunelerin viskozite davranışını değerlendirmek için kullanılabilir.'],
                ['question' => 'L1-L4 sistemi hangi viskozite aralığını kapsar?', 'answer' => 'Bu ürün sayfasında L1-L4 sistemi 15 - 22.000.000 mPa.s viskozite aralığıyla listelenmiştir.'],
                ['question' => 'B-One Plus modelinde veri aktarımı var mı?', 'answer' => 'Ürün bilgisine göre cihazda sonuç hafızası ve USB üzerinden veri transferi özellikleri bulunur.'],
                ['question' => 'Bu ürün hangi üst kategori altında yer alır?', 'answer' => 'Bu ürün Viskozimetre üst kategorisi altında Rotasyonel Viskozimetre alt kategorisinde listelenir.'],
                ['question' => 'Teklif almak için hangi bilgiler gerekir?', 'answer' => 'Numune tipi, beklenen viskozite aralığı, spindle set ihtiyacı, ölçüm standardı, veri aktarım beklentisi ve adet bilgileri teklif talebinde paylaşılmalıdır.'],
            ],
            'cta' => [
                'title' => 'Lamy B-One Plus Rotasyonel Viskozimetre İçin Teklif Alın',
                'text' => 'Lamy B-One Plus rotasyonel viskozimetre için numune tipinizi, beklenen viskozite aralığınızı ve L1-L4 spindle set ihtiyacınızı paylaşın; teknik ekip teklif sürecini başlatsın.',
                'button' => 'Ürün Hakkında Bilgi Al',
            ],
        ];
    }

    private function lamyTx700TextureAnalyzerSeoContent(int $force): array
    {
        $variants = [
            10 => ['kg' => '1 kg', 'sku' => 'Yayın öncesi netleştirilecek', 'note' => 'düşük kuvvet aralığı gerektiren'],
            20 => ['kg' => '2 kg', 'sku' => 'Yayın öncesi netleştirilecek', 'note' => 'düşük ve hassas kuvvet aralığı gerektiren'],
            50 => ['kg' => '5 kg', 'sku' => 'Yayın öncesi netleştirilecek', 'note' => 'düşük ve orta kuvvet aralığı gerektiren'],
            250 => ['kg' => '25 kg', 'sku' => 'LB.LMY.N151250', 'note' => 'orta kuvvet aralığı gerektiren'],
            500 => ['kg' => '50 kg', 'sku' => 'LB.LMY.N151500', 'note' => 'daha yüksek kuvvet gerektiren'],
        ];

        $variant = $variants[$force] ?? $variants[250];
        $forceText = "{$force} N";
        $capacity = "{$forceText} ({$variant['kg']})";
        $slug = "lamy-tx-700-{$force}-n-tekstur-analiz-cihazi";

        return [
            'meta_title' => "Lamy TX 700 {$forceText} Tekstür Analiz Cihazı | MTA Endüstri",
            'meta_description' => "Lamy TX 700 {$forceText} tekstür analiz cihazı; 0.001 N çözünürlük, 7 inç dokunmatik ekran, PT100 sıcaklık probu ve USB veri aktarımıyla incelenebilir.",
            'h1' => "Lamy TX 700 - {$forceText} Tekstür Analiz Cihazı",
            'hero_text' => "Lamy TX 700 - {$forceText} tekstür analiz cihazı; {$variant['note']} doku, sertlik, kırılma, sıkıştırma ve benzeri tekstür analizlerinde kuvvet, hız, mesafe ve zaman parametrelerini birlikte değerlendirmek için kullanılan laboratuvar analiz cihazıdır. Ürün sayfasında {$forceText} sensör, 0.001 N çözünürlük, prob/hücre uyumu ve bağlantı özellikleri teklif odaklı katalog yapısıyla sunulur.",
            'primary_cta' => "Lamy TX 700 {$forceText} İçin Teklif Al",
            'secondary_cta' => 'Teknik Özellikleri İncele',
            'secondary_cta_url' => '#teknik-ozellikler',
            'image_alt' => "Lamy TX 700 {$forceText} tekstür analiz cihazı ürün görseli",
            'metadata' => [
                'Marka' => 'Lamy',
                'Kategori' => 'Tekstür Analiz Cihazı',
                'Üst kategori' => 'Viskozimetre',
                'Model' => "TX 700 - {$forceText}",
                'SKU' => $variant['sku'],
                'Kuvvet kapasitesi' => $capacity,
                'Çözünürlük' => '0.001 N (0.1 g)',
                'Kullanım alanı' => 'Tekstür ve doku analizi',
            ],
            'specs' => [
                'Marka' => 'Lamy',
                'Model' => "TX 700 - {$forceText}",
                'Kategori' => 'Tekstür Analiz Cihazı',
                'Üst kategori' => 'Viskozimetre',
                'Sensör seçimi' => '10 N (1 kg), 20 N (2 kg), 50 N (5 kg), 250 N (25 kg), 500 N (50 kg)',
                'Seçili sensör' => $capacity,
                'Çözünürlük' => '0.001 N (0.1 g)',
                'Hız' => '0.1 - 10 mm/s, +/-0.2%',
                'Hassasiyet' => '+/- %0.05',
                'Hareket' => '370 mm yükseklik, 0.1 mm çözünürlük',
                'Ekran' => '7 inç dokunmatik ekran; güç, hız, mesafe, zaman ve hassasiyet seviyesi göstergeleri',
                'Sıcaklık' => '-50...300 °C arası sıcaklık değeri gösteren PT100 sensör',
                'Gerilim' => '90-240 VAC, 50/60 Hz',
                'Dil' => 'Fransızca, İngilizce, Rusça, İspanyolca, Almanca, Türkçe',
                'Emniyet ve gizlilik' => 'Kullanıcı adı ve şifre ile belirlenebilen operatör modu',
                'PC bağlantısı' => 'RS232 portu ve USB',
                'Yazıcı bağlantısı' => 'USB portu, PCL/5 uyumlu',
                'Ölçüler' => '610 mm derinlik, 340 mm genişlik, 650 mm yükseklik',
                'Ağırlık' => '22 kg',
            ],
            'sections' => [
                ['title' => "Lamy TX 700 - {$forceText} Tekstür Analiz Cihazı Özeti", 'text' => "Lamy TX 700 - {$forceText}, {$variant['note']} numunelerin tekstür davranışını kuvvet, mesafe, hız ve zaman parametreleriyle değerlendirmek için kullanılan bir tekstür analiz cihazıdır. Gıda, kozmetik, ilaç, ambalaj, malzeme ve kalite kontrol laboratuvarlarında doku analizi ihtiyacına göre değerlendirilebilir."],
                ['title' => 'Tekstür Analizi Hangi Uygulamalarda Kullanılır?', 'text' => 'Tekstür analiz cihazları; sertlik, kırılma, sıkıştırma, penetrasyon, elastikiyet, yapışkanlık ve benzeri fiziksel özelliklerin incelendiği uygulamalarda kullanılır. Ürün geliştirme, kalite kontrol, reçete karşılaştırma ve proses takibi gibi çalışmalarda numune davranışını sayısal olarak değerlendirmeye yardımcı olur.'],
                ['title' => "{$forceText} Kuvvet Sensörü ve Çözünürlük", 'text' => "Bu ürün {$capacity} seçili sensör bilgisiyle listelenmiştir. 0.001 N (0.1 g) çözünürlük, test sonucunun okunabilirliği açısından cihazın teknik değerlendirmesinde dikkate alınması gereken ana alanlardan biridir. Aynı cihaz ailesinde farklı kuvvet sensörleri seçilebildiği için uygulama ihtiyacı sensör seçimini doğrudan etkiler."],
                ['title' => 'Prob, Hücre ve Yöntem Programlama', 'text' => 'TX 700 cihazı geniş prob ve hücre yelpazesiyle farklı numune tiplerine uyarlanabilir. Yöntem programlama ve kayıt özellikleri, tekrarlanabilir test koşullarının oluşturulmasına ve farklı numune sonuçlarının karşılaştırılmasına yardımcı olur.'],
                ['title' => 'Ekran, Veri Aktarımı ve Laboratuvar Kullanımı', 'text' => '7 inç dokunmatik ekran, test sırasında güç, hız, mesafe, zaman ve hassasiyet seviyesi gibi bilgilerin izlenmesini sağlar. USB veri aktarımı ve RS232 bağlantısı, ölçüm sonuçlarının laboratuvar veri akışına dahil edilmesi gereken uygulamalarda önemli olabilir.'],
                ['title' => 'Teklif Süreci İçin Gerekli Bilgiler', 'text' => "Lamy TX 700 - {$forceText} için teklif talebi oluştururken numune tipi, hedef test metodu, ihtiyaç duyulan kuvvet sensörü, prob veya hücre tercihi, adet ve kullanım alanı bilgileri paylaşılmalıdır. Bu bilgiler ürünün doğru aksesuar ve teknik gereksinimlerle değerlendirilmesini sağlar."],
            ],
            'support_links' => [
                ['url' => route('products.category', 'tekstur-analiz-cihazi'), 'anchor' => 'tekstür analiz cihazı modelleri'],
                ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre ve reoloji cihazları'],
                ['url' => route('products.brand', 'lamy'), 'anchor' => 'Lamy laboratuvar cihazları'],
                ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                ['url' => route('contact'), 'anchor' => "Lamy TX 700 {$forceText} teklif talebi"],
            ],
            'faq' => [
                ['question' => "Lamy TX 700 - {$forceText} ne için kullanılır?", 'answer' => "Lamy TX 700 - {$forceText}; gıda, kozmetik, ilaç, ambalaj ve malzeme laboratuvarlarında numune tekstürünün kuvvet, mesafe, hız ve zaman parametreleriyle değerlendirilmesi için kullanılabilir."],
                ['question' => 'Bu ürün hangi üst kategori altında yer alır?', 'answer' => 'Bu ürün MTA Endüstri katalog yapısında Viskozimetre üst kategorisi altında Tekstür Analiz Cihazı alt kategorisinde listelenir.'],
                ['question' => "{$forceText} sensör hangi kapasiteye karşılık gelir?", 'answer' => "{$forceText} sensör yaklaşık {$variant['kg']} kuvvet kapasitesiyle listelenmiştir. Uygun sensör seçimi numune tipi, test metodu ve beklenen kuvvet aralığına göre değerlendirilmelidir."],
                ['question' => 'Cihazda veri aktarımı var mı?', 'answer' => 'Ürün bilgisine göre cihazda USB veri aktarımı, RS232 PC bağlantısı ve USB üzerinden PCL/5 uyumlu yazıcı bağlantısı bulunur.'],
                ['question' => 'Teklif almak için hangi bilgiler gerekir?', 'answer' => 'Numune tipi, hedef test metodu, kuvvet sensörü, prob veya hücre ihtiyacı, adet ve kullanım alanı bilgileri teklif talebinde paylaşılmalıdır.'],
            ],
            'cta' => [
                'title' => "Lamy TX 700 - {$forceText} İçin Teklif Alın",
                'text' => "Lamy TX 700 - {$forceText} tekstür analiz cihazı için numune tipinizi, test metodunuzu ve ihtiyaç duyduğunuz prob/sensör bilgisini paylaşın; teknik ekip teklif sürecini başlatsın.",
                'button' => 'Ürün Hakkında Bilgi Al',
            ],
        ];
    }

    private function knowledgePageSeoContent(): array
    {
        return [
            'meta_title' => 'Bilgi Merkezi | Kalibrasyon ve Laboratuvar Cihazları Rehberleri',
            'meta_description' => 'Kalibrasyon, laboratuvar cihazları, teknik servis ve ürün seçimi hakkında MTA Endüstri tarafından hazırlanan rehber içerikleri inceleyin.',
            'h1' => 'Bilgi Merkezi',
            'hero_text' => 'MTA Endüstri Bilgi Merkezi; kalibrasyon hizmetleri, laboratuvar cihazları, teknik servis süreçleri ve teknik ürün seçimi hakkında rehber içerikler sunar. Kullanıcılar cihaz seçimi, bakım, kalibrasyon periyodu ve ölçüm güvenilirliği konularında temel bilgilere ulaşabilir.',
            'sections' => [
                ['title' => 'Kalibrasyon Rehberleri', 'text' => 'Kalibrasyon rehberleri; basınç, sıcaklık, terazi, hacim, tork ve devir kalibrasyonu gibi hizmet alanlarını anlaşılır şekilde açıklar.'],
                ['title' => 'Satın Alma Rehberleri', 'text' => 'Laboratuvar cihazı seçimi; ürün adı veya fiyat bilgisiyle sınırlı değildir. Kapasite, ölçüm aralığı, hassasiyet, numune tipi, servis desteği ve kalibrasyon ihtiyacı birlikte değerlendirilmelidir.'],
                ['title' => 'Teknik Servis ve Bakım İçerikleri', 'text' => 'Teknik servis içerikleri; cihaz arızaları, bakım süreçleri, ölçüm stabilitesi problemleri ve kalibrasyon öncesi teknik hazırlık hakkında bilgi verir.'],
            ],
            'featured_titles' => ['Basınç Kalibrasyonu Nedir?', 'Teknik Ürün Seçiminde Dikkat Edilecek Kriterler', 'Terazi Kalibrasyonu Nedir?', 'Hassas Terazi Seçim Rehberi', 'pH Metre Seçim Rehberi'],
            'support_links' => [
                ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                ['url' => route('contact'), 'anchor' => 'bilgi ve teklif talebi'],
            ],
            'cta' => ['title' => 'Teknik Ekibe Danışın', 'text' => 'Okuduğunuz rehberle ilgili ürün, kalibrasyon veya teknik servis ihtiyacınızı paylaşabilirsiniz.', 'button' => 'Teknik Ekibe Danışın'],
        ];
    }

    private function blogPageSeoContent(): array
    {
        return [
            'meta_title' => 'Blog | Laboratuvar Cihazları ve Kalibrasyon Rehberleri',
            'meta_description' => 'Kalibrasyon, laboratuvar cihazları, teknik servis ve ürün seçimi hakkında MTA Endüstri blog içeriklerini inceleyin.',
            'h1' => 'Blog',
            'hero_text' => 'MTA Endüstri Blog; kalibrasyon hizmetleri, laboratuvar cihazları, teknik servis süreçleri ve ürün seçimi hakkında bilgilendirici içerikler sunar. Kullanıcılar cihaz bakımı, ölçüm güvenilirliği, kalibrasyon periyotları ve laboratuvar ekipmanı seçimiyle ilgili rehber yazılara ulaşabilir.',
            'sections' => [
                ['title' => 'Kalibrasyon Rehberleri', 'text' => 'Kalibrasyon rehberleri; basınç, sıcaklık, terazi, hacim, devir ve tork kalibrasyonu gibi hizmet alanlarını anlaşılır şekilde açıklar.'],
                ['title' => 'Laboratuvar Cihazı Seçim Rehberleri', 'text' => 'Laboratuvar cihazı seçimi yalnızca ürün adı veya fiyat bilgisine göre yapılmamalıdır. Kapasite, ölçüm aralığı, hassasiyet, numune tipi, kullanım ortamı, teknik servis desteği ve kalibrasyon ihtiyacı birlikte değerlendirilmelidir.'],
                ['title' => 'Teknik Servis ve Bakım İçerikleri', 'text' => 'Teknik servis içerikleri; laboratuvar cihazlarında arıza belirtileri, bakım ihtiyacı, ölçüm stabilitesi problemleri ve kalibrasyon öncesi teknik hazırlık konularında kullanıcıyı bilgilendirir.'],
            ],
            'featured_titles' => ['Basınç Kalibrasyonu Nedir?', 'Terazi Kalibrasyonu Nedir?', 'Hassas Terazi Seçim Rehberi', 'pH Metre Seçerken Nelere Dikkat Edilmeli?', 'Refraktometre Nedir?', 'Viskozimetre Ne İşe Yarar?', 'Nem Tayin Cihazı Nedir?', 'Kalibrasyon Periyodu Nasıl Belirlenir?'],
            'category_cards' => ['Kalibrasyon Rehberleri', 'Satın Alma Rehberleri', 'Teknik Servis ve Bakım', 'Laboratuvar Cihazları', 'Ölçüm Güvenilirliği'],
            'support_links' => [
                ['url' => route('knowledge.index'), 'anchor' => 'bilgi merkezi'],
                ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                ['url' => route('contact'), 'anchor' => 'bilgi ve teklif talebi'],
            ],
            'cta' => ['title' => 'Teknik Ekibe Danışın', 'text' => 'Blog içeriklerinde yer alan konular hakkında ürün, kalibrasyon veya teknik servis ihtiyacınız varsa MTA Endüstri teknik ekibine ulaşabilirsiniz.', 'button' => 'Teknik Ekibe Danışın'],
        ];
    }

    private function knowledgeCategorySeoContent(string $slug): array
    {
        return match ($slug) {
            'kalibrasyon-rehberleri' => [
                'meta_title' => 'Kalibrasyon Rehberleri | MTA Endüstri Bilgi Merkezi',
                'meta_description' => 'Basınç, sıcaklık, terazi, hacim, devir ve tork kalibrasyonu hakkında teknik rehberleri MTA Endüstri Bilgi Merkezi’nde inceleyin.',
                'h1' => 'Kalibrasyon Rehberleri',
                'hero_text' => 'Kalibrasyon Rehberleri; ölçüm cihazlarının doğruluğu, kalibrasyon periyotları, raporlama süreçleri ve farklı cihaz gruplarına göre kalibrasyon ihtiyaçları hakkında bilgilendirici içerikler sunar.',
                'sections' => [
                    ['title' => 'Kalibrasyon Hakkında Teknik Rehberler', 'text' => 'Kalibrasyon, ölçüm cihazlarının referans sistemlerle karşılaştırılarak doğruluk durumunun değerlendirilmesi sürecidir. Basınç, sıcaklık, kütle-terazi, hacim, devir ve tork gibi farklı ölçüm alanlarında kalibrasyon ihtiyacı cihaz tipine, kullanım yoğunluğuna ve kalite prosedürlerine göre değişebilir.'],
                    ['title' => 'Öne Çıkan Kalibrasyon Konuları', 'text' => 'Kalibrasyon rehberleri kategorisinde kullanıcıların en sık ihtiyaç duyduğu konular; cihaz kalibrasyonu, kalibrasyon periyodu, ölçüm sapması, raporlama, teknik servis öncesi kontrol ve doğru hizmet seçimi etrafında toplanır.'],
                    ['title' => 'İlgili Kalibrasyon Hizmetleri', 'text' => 'Rehber içerikler, MTA Endüstri’nin ilgili kalibrasyon hizmet sayfalarıyla birlikte çalışır. Kullanıcı bir konuyu okuduktan sonra doğrudan ilgili hizmet sayfasına veya teklif formuna yönlendirilebilir.'],
                ],
                'content_items' => ['Basınç Kalibrasyonu Nedir?', 'Sıcaklık Kalibrasyonu Nedir?', 'Terazi Kalibrasyonu Nedir?', 'Hacim Kalibrasyonu Nedir?', 'Devir Kalibrasyonu Nedir?', 'Tork Kalibrasyonu Nedir?', 'Kalibrasyon Periyodu Nasıl Belirlenir?', 'Kalibrasyon Raporunda Hangi Bilgiler Bulunur?'],
                'service_links' => [
                    ['url' => route('services.show', 'basinc-kalibrasyonu'), 'anchor' => 'basınç kalibrasyonu'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'terazi kalibrasyonu'],
                    ['url' => route('services.show', 'hacim-kalibrasyonu'), 'anchor' => 'hacim kalibrasyonu'],
                    ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                    ['url' => route('services.show', 'tork-kalibrasyonu'), 'anchor' => 'tork kalibrasyonu'],
                ],
                'support_links' => [
                    ['url' => route('knowledge.index'), 'anchor' => 'bilgi merkezi'],
                    ['url' => route('blog.index'), 'anchor' => 'blog içerikleri'],
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                    ['url' => route('contact'), 'anchor' => 'kalibrasyon bilgi ve teklif talebi'],
                ],
                'cta' => ['title' => 'Kalibrasyon Hakkında Bilgi veya Teklif Alın', 'text' => 'Kalibrasyon rehberlerinde yer alan konular hakkında cihazınıza özel değerlendirme almak istiyorsanız cihaz tipi, marka, model, ölçüm aralığı ve adet bilgilerinizi paylaşabilirsiniz.', 'button' => 'Kalibrasyon Teklifi Al'],
            ],
            default => $this->defaultKnowledgeCategorySeoContent($slug),
        };
    }

    private function defaultKnowledgeCategorySeoContent(string $slug): array
    {
        $profiles = [
            'laboratuvar-cihazlari' => [
                'name' => 'Laboratuvar Cihazları',
                'description' => 'Laboratuvar cihazları, ürün grupları, kullanım alanları ve teknik seçim kriterleri hakkında rehber içerikleri inceleyin.',
                'focus' => 'laboratuvar cihazları',
                'scope' => 'Laboratuvar cihazları kategorisinde ürün grupları, cihaz seçimi, kullanım alanları ve teknik değerlendirme konuları ele alınır.',
                'items' => ['Laboratuvar cihazı nedir?', 'Cihaz seçerken hangi teknik özelliklere bakılır?', 'pH metre seçimi', 'Refraktometre nedir?', 'Viskozimetre ne işe yarar?', 'Nem tayin cihazı nedir?'],
                'services' => [
                    ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                    ['url' => route('technical-services.index'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'cta' => 'Ürün İçin Teklif Al',
            ],
            'olcum-guvenilirligi' => [
                'name' => 'Ölçüm Güvenilirliği',
                'description' => 'Ölçüm güvenilirliği, kalibrasyon periyodu, cihaz sapması ve raporlama süreçleri hakkında teknik rehberleri inceleyin.',
                'focus' => 'ölçüm güvenilirliği',
                'scope' => 'Ölçüm güvenilirliği kategorisinde doğruluk, sapma, tekrar edilebilirlik, kalibrasyon periyodu ve rapor değerlendirme konuları açıklanır.',
                'items' => ['Ölçüm sapması nedir?', 'Kalibrasyon periyodu nasıl belirlenir?', 'Kalibrasyon raporu nasıl okunur?', 'Arızalı cihaz kalibre edilir mi?', 'Ölçüm kararlılığı neden bozulur?'],
                'services' => [
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                ],
                'cta' => 'Kalibrasyon Teklifi Al',
            ],
            'satin-alma-rehberleri' => [
                'name' => 'Satın Alma Rehberleri',
                'description' => 'Laboratuvar cihazı satın alma sürecinde ürün grubu, marka, model ve teknik özellik seçimi için rehberleri inceleyin.',
                'focus' => 'satın alma rehberleri',
                'scope' => 'Satın alma rehberleri kategorisinde cihaz seçimi, marka/model karşılaştırması, teknik şartname ve teklif öncesi hazırlık konuları yer alır.',
                'items' => ['Hassas terazi seçimi', 'pH metre seçimi', 'Refraktometre seçimi', 'Viskozimetre seçimi', 'Nem tayin cihazı seçimi', 'Teknik şartname hazırlığı'],
                'services' => [
                    ['url' => route('products.index'), 'anchor' => 'ürün kataloğu'],
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'hassas terazi modelleri'],
                ],
                'cta' => 'Teklif Talebi Oluştur',
            ],
            'teknik-servis-ve-bakim' => [
                'name' => 'Teknik Servis ve Bakım',
                'description' => 'Laboratuvar cihazlarında arıza tespiti, bakım, teknik servis ve kalibrasyon öncesi hazırlık rehberlerini inceleyin.',
                'focus' => 'teknik servis ve bakım',
                'scope' => 'Teknik servis ve bakım kategorisinde arıza belirtileri, periyodik bakım, servis sonrası değerlendirme ve kalibrasyon öncesi hazırlık konuları ele alınır.',
                'items' => ['Laboratuvar cihazı arıza belirtileri', 'Terazi teknik servis süreci', 'Sensör ve prob problemleri', 'Display ve elektronik kart arızaları', 'Servis sonrası raporlama', 'Kalibrasyon öncesi teknik hazırlık'],
                'services' => [
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                    ['url' => route('technical-services.show', 'terazi-teknik-servis'), 'anchor' => 'terazi teknik servis'],
                ],
                'cta' => 'Teknik Servis Talebi Oluştur',
            ],
        ];

        $profile = $profiles[$slug] ?? [
            'name' => Str::headline(str_replace('-', ' ', $slug)),
            'description' => 'MTA Endüstri Bilgi Merkezi’nde teknik rehber içerikleri inceleyin.',
            'focus' => 'teknik rehberler',
            'scope' => 'Bu kategoride teknik bilgi, ürün seçimi, kalibrasyon ve servis süreçleri hakkında rehber içerikler yer alır.',
            'items' => ['Teknik değerlendirme', 'Ürün seçimi', 'Kalibrasyon ilişkisi', 'Servis ihtiyacı'],
            'services' => [
                ['url' => route('knowledge.index'), 'anchor' => 'bilgi merkezi'],
                ['url' => route('contact'), 'anchor' => 'teknik bilgi talebi'],
            ],
            'cta' => 'Teknik Ekibe Danışın',
        ];

        return [
            'meta_title' => $profile['name'] . ' | MTA Endüstri Bilgi Merkezi',
            'meta_description' => $profile['description'],
            'h1' => $profile['name'],
            'hero_text' => $profile['scope'] . ' İçerikler ticari sayfalarla çakışmayacak şekilde bilgilendirici arama niyetine göre yapılandırılır.',
            'sections' => [
                ['title' => $profile['name'] . ' Rehberleri', 'text' => $profile['scope']],
                ['title' => 'Arama Niyeti', 'text' => 'Bu kategori, teklif talebi veya ürün listeleme sayfası yerine kullanıcıların konu hakkında teknik bilgi edinmek istediği sorgular için hazırlanır. İlgili ticari ihtiyaçlarda ürün, kalibrasyon veya teknik servis sayfalarına yönlendirme yapılır.'],
                ['title' => 'İlgili Ticari Sayfalar', 'text' => 'Bilgi içerikleri ticari landing page’lerin yerini almaz; doğru bağlamda ürün kategorileri, kalibrasyon hizmetleri, teknik servis sayfaları ve iletişim formuna bağlantı verir.'],
            ],
            'scope_title' => $profile['name'] . ' kapsamında hangi içerikler yer alır?',
            'scope_text' => $profile['scope'],
            'content_items' => $profile['items'],
            'service_links' => $profile['services'],
            'support_links' => [
                ['url' => route('knowledge.index'), 'anchor' => 'bilgi merkezi'],
                ['url' => route('blog.index'), 'anchor' => 'blog içerikleri'],
                ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                ['url' => route('contact'), 'anchor' => $profile['focus'] . ' bilgi talebi'],
            ],
            'cta' => [
                'eyebrow' => $profile['focus'],
                'title' => $profile['cta'],
                'text' => 'Okuduğunuz rehberle ilişkili ürün, kalibrasyon veya teknik servis ihtiyacınızı paylaşarak MTA Endüstri teknik ekibine ulaşabilirsiniz.',
                'button' => $profile['cta'],
            ],
        ];
    }

    private function articleSeoContent(string $slug): array
    {
        return match ($slug) {
            'basinc-kalibrasyonu-nedir' => [
                'meta_title' => 'Basınç Kalibrasyonu Nedir? | MTA Endüstri',
                'meta_description' => 'Basınç kalibrasyonu nedir, hangi cihazlarda yapılır, manometre kalibrasyonu neden önemlidir ve süreç nasıl ilerler? Teknik rehberi inceleyin.',
                'h1' => 'Basınç Kalibrasyonu Nedir?',
                'intro' => 'Basınç kalibrasyonu, basınç ölçen cihazların gösterdiği değerin güvenilir referans sistemlerle karşılaştırılması ve sonuçların raporlanması sürecidir. Manometre, dijital manometre, basınç transmitter, basınç sensörü ve fark basınç ölçer gibi cihazlarda ölçüm güvenilirliği için uygulanabilir.',
                'answer' => 'Basınç kalibrasyonu, basınç ölçen cihazların gösterdiği değerin güvenilir referans sistemlerle karşılaştırılması ve sonuçların raporlanması sürecidir.',
                'image_alt' => 'Basınç kalibrasyonu ve manometre ölçüm uygulaması',
                'sections' => [
                    ['title' => 'Basınç Kalibrasyonu Ne İşe Yarar?', 'text' => 'Basınç ölçüm cihazları üretim, bakım, proses güvenliği ve kalite kontrol süreçlerinde kullanılır. Bu cihazlarda oluşabilecek sapmalar hatalı proses değerlendirmelerine neden olabilir. Kalibrasyon, cihazın ölçüm doğruluğunu takip etmeye yardımcı olur.'],
                    ['title' => 'Hangi Cihazlarda Basınç Kalibrasyonu Yapılır?', 'items' => ['Analog manometre', 'Dijital manometre', 'Basınç transmitter', 'Basınç transdüseri', 'Basınç sensörü', 'Vakum ölçer', 'Fark basınç ölçer']],
                    ['title' => 'Manometre Kalibrasyonu Neden Önemlidir?', 'text' => 'Manometreler proses ve bakım uygulamalarında sık kullanılan basınç göstergeleridir. Düzenli manometre kalibrasyonu, cihazın gösterdiği değerin güvenilirliğini değerlendirmek ve ölçüm sapmalarını takip etmek için önemlidir.'],
                    ['title' => 'Basınç Kalibrasyonu Süreci Nasıl İlerler?', 'items' => ['Cihaz bilgilerinin alınması', 'Ölçüm aralığının belirlenmesi', 'Referans sistemle karşılaştırma', 'Sonuçların değerlendirilmesi', 'Raporlama ve teslim']],
                    ['title' => 'Ne Zaman Teklif Alınmalı?', 'text' => 'Basınç ölçüm cihazınızın periyodik kontrol zamanı geldiyse, cihazda sapma şüphesi varsa veya kalite prosedürleriniz kalibrasyon gerektiriyorsa teknik ekipten teklif alınabilir.'],
                ],
                'support_links' => [
                    ['url' => route('services.show', 'basinc-kalibrasyonu'), 'anchor' => 'basınç kalibrasyonu hizmeti'],
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                    ['url' => route('contact'), 'anchor' => 'basınç kalibrasyonu teklif talebi'],
                ],
                'cta' => ['title' => 'Basınç Kalibrasyonu İçin Teklif Al', 'text' => 'Cihaz tipi, marka, model, ölçüm aralığı ve adet bilgilerinizi paylaşarak basınç kalibrasyonu teklif sürecini başlatabilirsiniz.', 'button' => 'Basınç Kalibrasyonu İçin Teklif Al'],
            ],
            'kalibrasyon-nedir' => [
                'meta_title' => 'Kalibrasyon Nedir? Tanımı, Süreci ve Periyodu | MTA Endüstri',
                'meta_description' => 'Kalibrasyon nedir, neden yapılır, hangi cihazlarda uygulanır? Kalibrasyon ile ayar/doğrulama farkı, izlenebilirlik, ölçüm belirsizliği ve kalibrasyon periyodu bu rehberde.',
                'h1' => 'Kalibrasyon Nedir?',
                'intro' => 'Kalibrasyon, bir ölçüm cihazının gösterdiği değerin, ulusal/uluslararası standartlara izlenebilir bir referans ölçüm sistemiyle karşılaştırılması, aralarındaki sapmanın belirlenmesi ve sonucun ölçüm belirsizliğiyle birlikte bir sertifikada raporlanması sürecidir. Kalibrasyon cihazı "tamir etmez" ya da otomatik olarak "ayarlamaz"; cihazın o an ne kadar doğru ölçtüğünü izlenebilir biçimde belgeler.',
                'answer' => 'Kalibrasyon, bir ölçüm cihazının gösterdiği değerin izlenebilir bir referans ile karşılaştırılması, sapmanın belirlenmesi ve sonuçların belirsizlik bilgisiyle birlikte bir sertifikada raporlanması işlemidir.',
                'image_alt' => 'Kalibrasyon laboratuvarında referans cihazla ölçüm karşılaştırması',
                'sections' => [
                    ['title' => 'Kalibrasyon Ne İşe Yarar?', 'text' => 'Üretim, kalite kontrol, bakım ve AR-GE süreçlerinde alınan kararlar ölçüm sonuçlarına dayanır. Ölçüm cihazları zamanla, kullanımla, taşımayla ve çevre koşullarıyla sapabilir (drift). Kalibrasyon, bu sapmayı görünür kılar; cihazın kabul kriterleri içinde olup olmadığını değerlendirmeye ve hatalı ölçümden kaynaklanan hurda, şikâyet ve güvenlik risklerini azaltmaya yardımcı olur.'],
                    ['title' => 'Kalibrasyon, Ayar ve Doğrulama Arasındaki Fark', 'items' => ['Kalibrasyon: Cihazın gösterdiği değerin referansla karşılaştırılıp sapmanın belirsizlikle raporlanması.', 'Ayar (adjustment): Cihazın referansa yakınlaştırılacak şekilde yeniden düzenlenmesi; ayar sonrası tekrar kalibrasyon gerekir.', 'Doğrulama (verification): Cihazın önceden tanımlı bir tolerans/şartname içinde olup olmadığının kontrolü.']],
                    ['title' => 'İzlenebilirlik ve Ölçüm Belirsizliği', 'text' => 'Güvenilir bir kalibrasyon, kesintisiz bir karşılaştırma zinciriyle (izlenebilirlik) ulusal metroloji enstitüsüne ve SI birimlerine bağlanır. Her kalibrasyon sonucunun bir ölçüm belirsizliği vardır; sertifikada genellikle genişletilmiş belirsizlik (k=2, ~%95 güven) olarak verilir. Belirsizlik, sonucun ne kadar "aralık" taşıdığını gösterir ve kabul kararında dikkate alınır.'],
                    ['title' => 'Hangi Cihazlarda Kalibrasyon Yapılır?', 'items' => ['Terazi ve kütü/tartım cihazları', 'Manometre, basınç transmitter ve sensörleri', 'Termometre, sıcaklık sensörü, etüv ve inkübatör kabinleri', 'Tork anahtarları ve tork ölçüm cihazları', 'Takometre / devir ölçerler', 'Pipet, büret, balon joje gibi hacim ekipmanları']],
                    ['title' => 'Kalibrasyon Periyodu Nasıl Belirlenir?', 'text' => 'Sabit bir "1 yıl" kuralı yoktur. Periyot; cihazın kullanım yoğunluğu, ölçümün kritikliği, üretici önerisi, geçmiş kalibrasyon sonuçlarındaki kararlılık ve ilgili standart/müşteri şartlarına göre belirlenir. Kalibrasyon sonuçları kararlıysa periyot uzatılabilir; sık sapma görülüyorsa kısaltılır. Cihaz düşme, tamir veya taşınma sonrası periyot beklenmeden yeniden kalibre edilir.'],
                    ['title' => 'Akredite Kalibrasyon Neden Önemlidir?', 'text' => 'TÜRKAK gibi bir akreditasyon kurumu tarafından ISO/IEC 17025 kapsamında akredite edilmiş bir laboratuvardan alınan sertifika; laboratuvarın teknik yeterliliğinin, izlenebilirliğinin ve belirsizlik hesabının bağımsız olarak denetlendiği anlamına gelir. Denetim ve tedarik zinciri şartlarında genellikle akredite sertifika istenir.'],
                ],
                'support_links' => [
                    ['url' => route('scope'), 'anchor' => 'kalibrasyon kapsamımız'],
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'kütle ve terazi kalibrasyonu'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('services.show', 'basinc-kalibrasyonu'), 'anchor' => 'basınç kalibrasyonu'],
                    ['url' => route('knowledge.show', 'kalibrasyon-sertifikasi'), 'anchor' => 'kalibrasyon sertifikası nedir'],
                    ['url' => route('contact'), 'anchor' => 'kalibrasyon teklif talebi'],
                ],
                'cta' => ['title' => 'Cihazlarınız İçin Kalibrasyon Teklifi Alın', 'text' => 'Cihaz listenizi, marka/model ve ölçüm aralığı bilgilerini iletin; kapsamımıza uygun kalibrasyonlar için teklif ve termin bilgisiyle dönüş yapalım.', 'button' => 'Kalibrasyon İçin Teklif Al'],
                'faq' => [
                    ['question' => 'Kalibrasyon cihazı tamir eder mi?', 'answer' => 'Hayır. Kalibrasyon, cihazın o anki doğruluğunu belgeler. Sapma toleransı aşıyorsa ayar veya teknik servis ayrı bir işlem olarak yapılır.'],
                    ['question' => 'Kalibrasyon ne sıklıkla yapılmalı?', 'answer' => 'Kullanım yoğunluğu, ölçümün kritikliği, üretici önerisi ve geçmiş sonuçların kararlılığına göre belirlenir; düşme, tamir veya taşıma sonrası periyot beklenmeden tekrarlanır.'],
                    ['question' => 'Akredite kalibrasyon ile normal kalibrasyon farkı nedir?', 'answer' => 'Akredite kalibrasyon, ISO/IEC 17025 kapsamında bağımsız denetlenmiş bir laboratuvardan alınır; izlenebilirlik ve belirsizlik hesabı akreditasyon kurumunca doğrulanmıştır.'],
                    ['question' => 'Kalibrasyon sertifikasının geçerlilik süresi var mı?', 'answer' => 'Sertifikanın kendisi süresiz bir "geçerlilik" taşımaz; kullanıcı, risk değerlendirmesiyle bir sonraki kalibrasyon tarihini belirler.'],
                ],
            ],
            'kalibrasyon-sertifikasi' => [
                'meta_title' => 'Kalibrasyon Sertifikası Nedir? İçeriği ve Okunması | MTA Endüstri',
                'meta_description' => 'Kalibrasyon sertifikası (kalibrasyon raporu) hangi bilgileri içerir, nasıl okunur, TÜRKAK akredite sertifika ile akreditasyonsuz rapor farkı ve geçerlilik süresi.',
                'h1' => 'Kalibrasyon Sertifikası Nedir ve Neleri İçerir?',
                'intro' => 'Kalibrasyon sertifikası (yaygın kullanımıyla kalibrasyon raporu), bir cihazın kalibrasyonunda uygulanan yöntemi, ölçüm noktalarını, bulunan sapmaları ve ölçüm belirsizliğini izlenebilirlik bilgisiyle birlikte belgeleyen resmi kayıttır. Denetimlerde ve tedarik zinciri şartlarında cihazın ölçüm güvenilirliğinin kanıtı olarak istenir.',
                'answer' => 'Kalibrasyon sertifikası; kalibre edilen cihazı, uygulanan yöntemi, ölçüm noktalarını, bulunan sapmaları ve genişletilmiş ölçüm belirsizliğini (k=2) izlenebilirlik bilgisiyle birlikte belgeleyen resmi rapordur.',
                'image_alt' => 'ISO/IEC 17025 kapsamında düzenlenmiş kalibrasyon sertifikası örneği',
                'sections' => [
                    ['title' => 'Kalibrasyon Sertifikasında Bulunması Gereken Bilgiler', 'items' => ['Laboratuvar ve müşteri kimlik bilgileri', 'Cihazın tanımı: marka, model, seri no, ölçüm aralığı', 'Kalibrasyon tarihi ve (varsa) cihazın alındığı tarih', 'Kullanılan referans standartlar ve izlenebilirlik beyanı', 'Ortam koşulları (sıcaklık, nem vb.)', 'Uygulanan yöntem / prosedür referansı', 'Her ölçüm noktası için: gösterilen değer, referans değer, sapma (hata) ve genişletilmiş belirsizlik (k=2)', 'Sonuçların değerlendirmesi ve (talep edildiyse) uygunluk beyanı', 'Yetkili imza / onay']],
                    ['title' => 'Sertifika Nasıl Okunur?', 'text' => 'Önce cihazın kimlik bilgileri ve ölçüm aralığı kontrol edilir. Sonra her ölçüm noktasındaki "sapma" değerine bakılır: bu değer, kullanıcının belirlediği kabul toleransıyla karşılaştırılır. Kabul kararı verilirken sapmaya ek olarak belirsizlik de dikkate alınır (sapma + belirsizlik toleransı aşıyorsa risk vardır). Uygunluk beyanı varsa, hangi karar kuralının kullanıldığına dikkat edilir.'],
                    ['title' => 'TÜRKAK Akredite Sertifika ile Akreditasyonsuz Rapor Farkı', 'text' => 'Akredite sertifika, ISO/IEC 17025 kapsamında TÜRKAK tarafından denetlenmiş bir laboratuvardan alınır ve genellikle TÜRKAK logolu düzenlenir; izlenebilirlik ve belirsizlik hesabı bağımsız olarak doğrulanmıştır. Akreditasyonsuz rapor teknik olarak yararlı olabilir ancak denetimlerde çoğu zaman kabul görmez.'],
                    ['title' => 'Geçerlilik Süresi ve Bir Sonraki Kalibrasyon Tarihi', 'text' => 'Uluslararası uygulamada laboratuvar, sertifikaya sabit bir "geçerlilik süresi" yazmak zorunda değildir; bir sonraki kalibrasyon tarihini kullanıcı, kendi risk değerlendirmesi ve kalite sistemine göre belirler. Yine de birçok laboratuvar, müşteri talebiyle önerilen tarihi belirtir.'],
                ],
                'support_links' => [
                    ['url' => route('knowledge.show', 'kalibrasyon-nedir'), 'anchor' => 'kalibrasyon nedir'],
                    ['url' => route('scope'), 'anchor' => 'kalibrasyon kapsamımız'],
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('certificates'), 'anchor' => 'sertifikalarımız'],
                    ['url' => route('contact'), 'anchor' => 'kalibrasyon teklif talebi'],
                ],
                'cta' => ['title' => 'Akredite Kalibrasyon Sertifikası İçin Teklif Alın', 'text' => 'Cihazlarınız için ISO/IEC 17025 izlenebilir kalibrasyon ve sertifikalandırma sürecini başlatmak üzere cihaz listenizi iletin.', 'button' => 'Kalibrasyon İçin Teklif Al'],
                'faq' => [
                    ['question' => 'Kalibrasyon sertifikası ile kalibrasyon raporu aynı şey mi?', 'answer' => 'Günlük kullanımda çoğunlukla aynı belge kastedilir. Bazı kurumlar "sertifika"yı akredite/onaylı çıktı, "rapor"u iç kullanım çıktısı için ayırır.'],
                    ['question' => 'Sertifikada "uygundur/uygun değildir" yazması gerekir mi?', 'answer' => 'Zorunlu değildir. Uygunluk beyanı yalnızca talep edildiğinde ve tanımlı bir karar kuralıyla verilir; aksi halde sertifika sadece sapma ve belirsizliği raporlar.'],
                    ['question' => 'Kalibrasyon sertifikası ne kadar süre geçerlidir?', 'answer' => 'Sertifikanın kendisi süresiz bir geçerlilik taşımaz; bir sonraki kalibrasyon tarihi kullanıcının risk değerlendirmesiyle belirlenir.'],
                ],
            ],
            'analitik-terazi-nedir' => [
                'meta_title' => 'Analitik Terazi Nedir? Hassas Teraziden Farkı | MTA Endüstri',
                'meta_description' => 'Analitik terazi nedir, hassas teraziden farkı nedir, 0,1 mg okunabilirlik ne demek, analitik terazi seçerken ve kurarken nelere dikkat edilir?',
                'h1' => 'Analitik Terazi Nedir?',
                'intro' => 'Analitik terazi, tipik olarak 0,1 mg (0,0001 g) okunabilirliğe ve numuneyi hava akımından koruyan bir rüzgarlık (draft shield) kabinine sahip, düşük miktarlı numunelerin yüksek doğrulukla tartıldığı laboratuvar terazisidir. Kimya, ilaç, gıda ve AR-GE laboratuvarlarında standart hazırlama, gravimetrik analiz ve reçete tartımlarında kullanılır.',
                'answer' => 'Analitik terazi, tipik olarak 0,1 mg (0,0001 g) okunabilirliğe ve rüzgarlık kabinine sahip, düşük miktarlı numunelerin yüksek doğrulukla tartılması için kullanılan laboratuvar terazisidir.',
                'image_alt' => 'Rüzgarlık kabinli analitik terazi ile laboratuvar tartımı',
                'sections' => [
                    ['title' => 'Analitik Terazi ile Hassas Terazi Arasındaki Fark', 'text' => 'Analitik teraziler 0,1 mg (bazı mikro/yarı mikro modellerde 0,01 mg ve altı) okunabilirliğe ve rüzgarlığa sahiptir; kapasiteleri genellikle 60–320 g arasındadır. Hassas teraziler (precision balance) tipik olarak 1 mg–0,1 g okunabilirlikte, daha yüksek kapasitede ve çoğunlukla rüzgarlıksızdır. Seçim, gereken en küçük tartım miktarı ve toleransına göre yapılır.'],
                    ['title' => 'Okunabilirlik, Kapasite ve Minimum Tartım', 'text' => 'Okunabilirlik (d), terazinin gösterebildiği en küçük adımdır; doğruluk anlamına gelmez. Kritik olan "minimum tartım" değeridir: ölçüm belirsizliği, tartılan miktara göre kabul edilebilir yüzde hatanın altında kalmalıdır. USP ve benzeri gerekliliklerde minimum tartım, tekrarlanabilirliğe göre hesaplanır.'],
                    ['title' => 'Analitik Terazi Seçerken Nelere Dikkat Edilmeli?', 'items' => ['Gereken okunabilirlik ve minimum tartım', 'Maksimum kapasite ve tipik numune kütlesi', 'Tekrarlanabilirlik ve doğrusallık değerleri', 'Dahili (motorlu) kalibrasyon / ayar özelliği', 'İç ortam koşulları: titreşim, hava akımı, sıcaklık kararlılığı', 'Veri çıkışı (RS232/USB/LAN), GLP/GMP raporlama', 'Servis desteği ve kalibrasyon bağlantısı']],
                    ['title' => 'Kurulum ve Doğru Kullanım', 'text' => 'Analitik terazi; titreşimsiz, hava akımından uzak, sıcaklık değişimi az bir tartım masasına, teraziyi terazi seviyesine (su terazisi) getirerek kurulmalıdır. Kullanım öncesi ortama alışması (ekilibrasyon) beklenir, dahili kalibrasyon çalıştırılır ve numuneler pens/spatül ile merkeze konur.'],
                    ['title' => 'Kalibrasyon ve Teknik Servis', 'text' => 'Analitik terazilerde dahili ayar rutin kullanım içindir; ölçüm güvenilirliğinin belgelenmesi için düzenli olarak akredite kütle ve terazi kalibrasyonu yaptırılır. Stabil olmayan gösterge, sürüklenme, kalibrasyon hatası veya kefe/mekanizma sorununda terazi teknik servis desteği gerekir.'],
                ],
                'support_links' => [
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'hassas terazi ve analitik terazi modelleri'],
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'kütle ve terazi kalibrasyonu'],
                    ['url' => route('technical-services.show', 'terazi-teknik-servis'), 'anchor' => 'terazi teknik servis'],
                    ['url' => route('knowledge.show', 'kalibrasyon-nedir'), 'anchor' => 'kalibrasyon nedir'],
                    ['url' => route('contact'), 'anchor' => 'analitik terazi teklif talebi'],
                ],
                'cta' => ['title' => 'Analitik Terazi İçin Teklif Alın', 'text' => 'Gereken okunabilirlik, kapasite ve raporlama ihtiyacınıza uygun analitik terazi modeli için teknik ekibimize ulaşın.', 'button' => 'Analitik Terazi İçin Teklif Al'],
                'faq' => [
                    ['question' => 'Analitik terazi kaç haneli tartar?', 'answer' => 'Klasik analitik teraziler 0,1 mg (0,0001 g) okunabilirliktedir; yarı mikro modeller 0,01 mg, mikro modeller daha altına iner.'],
                    ['question' => 'Analitik terazi ile hassas terazi arasındaki temel fark nedir?', 'answer' => 'Analitik terazi daha düşük okunabilirliğe ve rüzgarlık kabinine sahiptir, kapasitesi daha düşüktür; hassas terazi daha yüksek kapasiteli ve çoğunlukla rüzgarlıksızdır.'],
                    ['question' => 'Analitik terazinin dahili kalibrasyonu yeterli mi?', 'answer' => 'Dahili ayar günlük kullanım içindir. Ölçüm güvenilirliğinin belgelenmesi için düzenli akredite kalibrasyon gereklidir.'],
                ],
            ],
            default => [],
        };
    }

    private function technicalServicesPageSeoContent(): array
    {
        return [
            'meta_title' => 'Laboratuvar Cihazları Teknik Servis | MTA Endüstri',
            'meta_description' => 'Laboratuvar, analiz ve ölçüm cihazları için arıza tespiti, bakım, onarım, yedek parça değerlendirmesi ve teknik servis talebi oluşturun.',
            'h1' => 'Laboratuvar Cihazları Teknik Servis',
            'hero_text' => 'MTA Endüstri teknik servis yapısı; laboratuvar cihazları, analiz ve ölçüm cihazları ile teraziler için arıza tespiti, bakım, onarım, yedek parça değerlendirmesi ve kalibrasyon öncesi teknik hazırlık süreçlerini kapsar. Servis talepleri cihaz tipi, marka, model, arıza belirtisi ve kullanım alanına göre değerlendirilir.',
            'primary_cta' => 'Teknik Servis Talebi Oluştur',
            'secondary_cta' => 'Kalibrasyon Hizmetlerini İncele',
            'image' => 'images/technical-service/laboratuvar-cihazlari-teknik-servis.webp',
            'image_alt' => 'Laboratuvar cihazları teknik servis ve bakım desteği',
            'sections' => [
                [
                    'title' => 'Teknik Servis Hangi Durumlarda Gerekir?',
                    'text' => 'Cihazın ölçüm değeri stabil değilse, ekran veya display sorunu varsa, prob/sensör arızası görülüyorsa, elektronik kart problemi oluşmuşsa, cihaz kalibrasyon kabul etmiyorsa veya mekanik parçalarda hasar varsa teknik servis değerlendirmesi gerekir.',
                ],
                [
                    'title' => 'Kalibrasyon Öncesi Teknik Kontrol',
                    'text' => 'Ölçüm stabilitesi bozulmuş, fiziksel hasar görmüş veya hatalı sonuç üreten cihazlarda kalibrasyon öncesi teknik servis değerlendirmesi gerekebilir. Bu yaklaşım, raporlama ve ölçüm güvenilirliği açısından daha doğru bir süreç sağlar.',
                ],
            ],
            'service_cards' => [
                'laboratuvar-cihazlari-icin-teknik-servis' => [
                    'summary' => 'Laboratuvar cihazları için arıza tespiti, bakım, onarım ve servis sonrası performans değerlendirme desteği.',
                    'anchor' => 'laboratuvar cihazları teknik servis',
                ],
                'analiz-ve-olcum-cihazlari-teknik-servis' => [
                    'summary' => 'Analiz ve ölçüm cihazlarında prob, sensör, elektronik kart, gösterge ve ölçüm stabilitesi odaklı teknik servis değerlendirmesi.',
                    'anchor' => 'analiz ve ölçüm cihazları teknik servis',
                ],
                'terazi-teknik-servis' => [
                    'summary' => 'Hassas terazi, analitik terazi ve endüstriyel tartım cihazları için arıza tespiti, bakım ve onarım desteği.',
                    'anchor' => 'terazi teknik servis',
                ],
            ],
            'scope_section' => [
                'title' => 'Teknik Servis Kapsamı',
                'text' => 'Servis kapsamı cihaz tipine, marka ve modele, arıza belirtisine ve kullanım koşullarına göre belirlenir. Talep aşamasında mümkün olduğunca net cihaz bilgisi paylaşılması değerlendirme sürecini hızlandırır.',
                'items' => [
                    'Arıza tespiti ve teknik ön değerlendirme',
                    'Periyodik bakım ve temizlik kontrolleri',
                    'Onarım ihtiyacı değerlendirmesi',
                    'Yedek parça ve sarf ihtiyacı tespiti',
                    'Sensör, prob ve bağlantı kontrolleri',
                    'Elektronik kart, ekran ve güç besleme kontrolleri',
                    'Motor, fan, pompa ve mekanik aksam değerlendirmesi',
                    'Ölçüm stabilitesi ve performans kontrolü',
                    'Servis sonrası kalibrasyon ihtiyacı değerlendirmesi',
                ],
            ],
            'process' => [
                'title' => 'Teknik Servis Süreci Nasıl İlerler?',
                'text' => 'Teknik servis süreci, cihaz bilgilerinin alınması ve arıza belirtisinin değerlendirilmesiyle başlar. Uygun servis kapsamı belirlendikten sonra bakım, onarım veya kalibrasyon öncesi hazırlık adımları planlanır.',
                'steps' => [
                    'Talep kaydı ve cihaz bilgisinin alınması',
                    'Marka, model ve arıza belirtisinin değerlendirilmesi',
                    'Cihaz kabulü veya yerinde ön inceleme',
                    'Arıza tespiti ve servis kapsamının netleştirilmesi',
                    'Bakım, onarım veya yedek parça değerlendirmesi',
                    'Servis sonrası performans kontrolü',
                    'Teklif, raporlama veya teslim süreci',
                ],
            ],
            'support_section' => [
                'title' => 'Ürün, Kalibrasyon ve Teknik Servis Bağlantısı',
                'text' => 'Laboratuvar cihazlarında teknik servis, ürün tedariği ve kalibrasyon süreçleri çoğu zaman birlikte değerlendirilir. Cihazın onarım sonrası doğrulanması, yeni cihaz ihtiyacının netleşmesi veya düzenli kontrol planı oluşturulması için ilgili sayfalara geçiş yapılabilir.',
                'links' => [
                    ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'teraziler'],
                    ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre'],
                    ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre'],
                    ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre'],
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'terazi kalibrasyonu'],
                ],
            ],
            'cta' => [
                'title' => 'Teknik Servis Talebi Oluşturun',
                'text' => 'Laboratuvar cihazı, analiz ve ölçüm cihazı veya terazi teknik servis talebiniz için cihaz tipi, marka, model, arıza belirtisi ve adet bilgilerini paylaşabilirsiniz.',
                'note' => 'Talebinizi doğru servis başlığıyla eşleştirip teknik değerlendirme sürecini başlatalım.',
                'button' => 'Teknik Servis Talebi Oluştur',
                'anchor' => 'teknik servis talebi',
            ],
            'faq' => [
                [
                    'question' => 'Teknik servis talebi için hangi bilgiler gerekir?',
                    'answer' => 'Cihaz tipi, marka, model, arıza belirtisi, kullanım alanı ve adet bilgileri teknik servis değerlendirmesi için başlangıç bilgilerini sağlar.',
                ],
                [
                    'question' => 'Laboratuvar cihazlarında servis sonrası kalibrasyon gerekir mi?',
                    'answer' => 'Ölçüm yapan cihazlarda bakım veya onarım sonrası kalibrasyon ihtiyacı doğabilir. Bu durum cihaz tipine ve servis işlemine göre değerlendirilir.',
                ],
                [
                    'question' => 'Terazi teknik servis hizmeti hangi cihazları kapsar?',
                    'answer' => 'Hassas terazi, analitik terazi, yarı mikro terazi ve endüstriyel tartım cihazları servis kapsamına göre değerlendirilebilir.',
                ],
                [
                    'question' => 'Arızalı cihaz için önce teklif alınabilir mi?',
                    'answer' => 'Evet. Cihaz bilgileri ve arıza belirtisi paylaşıldığında teknik ekip uygun servis kapsamı ve teklif süreci hakkında dönüş yapar.',
                ],
            ],
        ];
    }

    private function normalizeTechnicalServiceSeoContent(array $technicalServiceSeo): array
    {
        if ($technicalServiceSeo === []) {
            return [];
        }

        return match ($technicalServiceSeo['slug'] ?? null) {
            'analiz-ve-olcum-cihazlari-teknik-servis' => array_merge($technicalServiceSeo, [
                'meta_description' => 'pH metre, iletkenlik ölçer, refraktometre, densitometre, viskozimetre ve titratörler için arıza tespiti, bakım ve teknik servis talebi oluşturun.',
                'hero_text' => 'Analiz ve ölçüm cihazları teknik servis hizmeti; laboratuvar ve kalite kontrol süreçlerinde kullanılan cihazların arıza tespiti, bakım, onarım, sensör/prob kontrolü, ölçüm stabilitesi değerlendirmesi ve kalibrasyon öncesi teknik hazırlık süreçlerini kapsar.',
                'sections' => [
                    [
                        'title' => 'Servis Kapsamındaki Cihaz Grupları',
                        'text' => 'pH metre, iletkenlik ölçer, refraktometre, densitometre, viskozimetre, potansiyometrik titratör ve Karl Fischer titratör gibi cihazlar arıza belirtisi, kullanım yoğunluğu ve ölçüm davranışı dikkate alınarak servis değerlendirmesine alınabilir.',
                    ],
                ],
            ]),
            'laboratuvar-cihazlari-icin-teknik-servis' => array_merge($technicalServiceSeo, [
                'meta_title' => 'Laboratuvar Cihazları İçin Teknik Servis | MTA Endüstri',
                'meta_description' => 'Etüv, nem tayin, karıştırıcı, homojenizatör, titratör, pH metre ve laboratuvar terazileri için teknik servis talebi oluşturun.',
                'h1' => 'Laboratuvar Cihazları İçin Teknik Servis',
                'hero_text' => 'Laboratuvar cihazları için teknik servis; cihazların arıza tespiti, periyodik bakım, onarım, yedek parça değerlendirmesi ve servis sonrası performans kontrolü süreçlerini kapsar. MTA Endüstri, cihaz tipi ve kullanım alanına göre servis talebini teknik olarak değerlendirir.',
                'sections' => [
                    [
                        'title' => 'Kalibrasyon Öncesi Teknik Hazırlık',
                        'text' => 'Kalibrasyon yapılacak cihazın fiziksel veya elektronik arızası varsa önce servis değerlendirmesi yapılmalıdır. Cihaz stabil çalışmadığında kalibrasyon sonucu doğru yorumlanamayabilir.',
                    ],
                ],
            ]),
            'terazi-teknik-servis' => array_merge($technicalServiceSeo, [
                'meta_description' => 'Hassas terazi, analitik terazi ve laboratuvar terazileri için arıza tespiti, bakım, onarım, loadcell kontrolü ve teknik servis talebi oluşturun.',
                'hero_text' => 'Terazi teknik servis hizmeti; hassas, analitik ve endüstriyel terazilerde arıza tespiti, bakım, onarım, loadcell kontrolü, display değerlendirmesi, adaptör ve bağlantı kontrolü ile kalibrasyon öncesi teknik hazırlık süreçlerini kapsar.',
                'primary_cta' => 'Terazi Teknik Servis Talebi Oluştur',
                'image_alt' => 'Hassas terazi teknik servisinde loadcell ve display kontrolü',
                'sections' => [
                    [
                        'title' => 'Terazi Tamiri Hangi Durumlarda Gerekir?',
                        'text' => 'Terazi açılmıyorsa, sıfır değeri stabil değilse, ölçüm tekrarlanabilirliği bozulduysa, display hata veriyorsa, loadcell darbe almışsa veya cihaz kalibrasyon kabul etmiyorsa teknik servis değerlendirmesi yapılmalıdır.',
                    ],
                    [
                        'title' => 'Kalibrasyon ile Servis Ayrımı',
                        'text' => 'Kalibrasyon ölçüm doğruluğunu değerlendirir; teknik servis ise cihazın çalışmasını etkileyen arıza ve bakım konularını ele alır. Arızalı terazilerde önce servis, ardından gerekirse kalibrasyon süreci planlanmalıdır.',
                    ],
                ],
                'cta' => array_merge($technicalServiceSeo['cta'] ?? [], [
                    'button' => 'Terazi Teknik Servis Talebi Oluştur',
                    'anchor' => 'terazi teknik servis talebi',
                ]),
            ]),
            default => $technicalServiceSeo,
        };
    }

    private function productsPageSeoContent(): array
    {
        return [
            'meta_title' => 'Laboratuvar Cihazları ve Teknik Ürün Kataloğu | MTA Endüstri',
            'meta_description' => 'Laboratuvar cihazları, ölçüm ekipmanları ve analiz cihazlarını marka, kategori ve teknik özelliklere göre inceleyin; MTA Endüstri’den teklif alın.',
            'h1' => 'Laboratuvar Cihazları ve Teknik Ürün Kataloğu',
            'hero_text' => 'MTA Endüstri ürün kataloğu; laboratuvar cihazları, ölçüm ekipmanları, analiz cihazları ve teknik ürün gruplarını marka, kategori ve teknik özellik bilgileriyle sunar. Site yapısı sepetli e-ticaret yerine teknik bilgi, ürün karşılaştırma ve teklif talebi akışı üzerine kurgulanır.',
            'primary_cta' => 'Ürün Kataloğunu İncele',
            'secondary_cta' => 'Kategorileri İncele',
            'sections' => [
                [
                    'title' => 'Laboratuvar Cihazları Kataloğu',
                    'text' => 'Laboratuvar cihazları kataloğu; kalite kontrol, AR-GE, üretim ve analiz süreçlerinde kullanılan cihazların marka, model, kategori, teknik özellik ve görsel bilgileriyle incelenebilmesi için hazırlanır.',
                ],
                [
                    'title' => 'Ürünler Nasıl Listelenir?',
                    'text' => 'Ürünler kategori ve marka ilişkisine göre yapılandırılır. Kullanıcılar teraziler, pH iletkenlik & metreler, refraktometreler, viskozimetreler, nem tayin cihazları, titratörler, karıştırıcılar, su banyoları, santrifüjler, inkübatörler, erime noktası cihazları ve polarimetreler gibi ana gruplar üzerinden ilgili markalara ulaşabilir.',
                ],
                [
                    'title' => 'Laboratuvar Cihazı Seçerken Nelere Bakılır?',
                    'text' => 'Cihaz seçiminde kullanım amacı, ölçüm aralığı, kapasite, hassasiyet, numune tipi, servis ihtiyacı, yedek parça erişimi ve kalibrasyon gerekliliği birlikte değerlendirilmelidir.',
                ],
                [
                    'title' => 'Laboratuvar Cihazları Fiyatları ve Teklif Süreci',
                    'text' => 'Ürün fiyatları marka, model, teknik özellik, konfigürasyon ve stok durumuna göre değişebilir. Bu nedenle ürün sayfaları teklif talebi ve teknik değerlendirme odaklı hazırlanır.',
                ],
                [
                    'title' => 'Ürün, Kalibrasyon ve Teknik Servis Bağlantısı',
                    'text' => 'Birçok laboratuvar cihazı kullanım süreci boyunca bakım, teknik servis ve kalibrasyon ihtiyacı doğurabilir. Ürün sayfalarında ilgili kalibrasyon veya teknik servis hizmetlerine yönlendirme yapılır.',
                ],
            ],
            'category_cards' => [
                'teraziler' => ['title' => 'Teraziler', 'summary' => 'Hassas terazi, analitik terazi, yarı mikro terazi ve endüstriyel tartım cihazlarını marka bazlı inceleyin.', 'anchor' => 'terazi modelleri'],
                'analitik-teraziler' => ['title' => 'Analitik Teraziler', 'summary' => 'Yüksek hassasiyetli laboratuvar analiz tartımları için analitik terazi modelleri.', 'anchor' => 'analitik terazi modelleri'],
                'hassas-teraziler' => ['title' => 'Hassas Teraziler', 'summary' => 'Rutin laboratuvar ve kalite kontrol tartımları için hassas terazi modelleri.', 'anchor' => 'hassas terazi modelleri'],
                'endustriyel-teraziler' => ['title' => 'Endüstriyel Teraziler', 'summary' => 'Üretim, depo ve saha tartım süreçleri için endüstriyel terazi çözümleri.', 'anchor' => 'endüstriyel terazi modelleri'],
                'mikro-teraziler' => ['title' => 'Mikro Teraziler', 'summary' => 'Mikro ve yarı mikro tartım uygulamaları için yüksek çözünürlüklü terazi seçenekleri.', 'anchor' => 'mikro terazi modelleri'],
                'ph-metre' => ['title' => 'pH Metre', 'summary' => 'Laboratuvar ve saha kullanımı için pH metre, elektrot ve ölçüm sistemlerini değerlendirin.', 'anchor' => 'pH metre modelleri'],
                'ph-iletkenlik' => ['title' => 'pH İletkenlik & Metreler', 'summary' => 'pH metre, iletkenlik ve çok parametreli ölçüm cihazlarını teknik özellikleriyle listeleyin.', 'anchor' => 'pH iletkenlik ve metreler'],
                'refraktometre' => ['title' => 'Refraktometre', 'summary' => 'Dijital refraktometre ve yoğunluk kırılma indisi ölçüm cihazlarını inceleyin.', 'anchor' => 'refraktometre modelleri'],
                'viskozimetre' => ['title' => 'Viskozimetre', 'summary' => 'Akışkan numuneler için viskozite ölçüm cihazlarını marka ve uygulama alanına göre değerlendirin.', 'anchor' => 'viskozimetre modelleri'],
                'karistiricilar' => ['title' => 'Karıştırıcılar', 'summary' => 'Manyetik, mekanik ve vorteks karıştırıcıları laboratuvar uygulamanıza göre inceleyin.', 'anchor' => 'karıştırıcı modelleri'],
                'isitmali-manyetik-karistirici' => ['title' => 'Isıtmalı Manyetik Karıştırıcılar', 'summary' => 'Karıştırma ve kontrollü ısıtma işlemlerini birlikte yürüten manyetik karıştırıcılar.', 'anchor' => 'ısıtmalı manyetik karıştırıcılar'],
                'isitmasiz-manyetik-karistirici' => ['title' => 'Isıtmasız Manyetik Karıştırıcılar', 'summary' => 'Isıtma gerektirmeyen rutin karıştırma işlemleri için manyetik karıştırıcı modelleri.', 'anchor' => 'ısıtmasız manyetik karıştırıcılar'],
                'vorteks-karistiricilar' => ['title' => 'Vorteks Karıştırıcılar', 'summary' => 'Tüp, vial ve küçük hacimli numune karıştırma işlemleri için vorteks cihazları.', 'anchor' => 'vorteks karıştırıcılar'],
                'jar-test' => ['title' => 'Jar Test', 'summary' => 'Su ve atık su uygulamalarında flokülasyon testleri için jar test cihazları.', 'anchor' => 'jar test cihazları'],
                'diger-cevre-cihazlari' => ['title' => 'Diğer Çevre Cihazları', 'summary' => 'Çevre laboratuvarlarında kullanılan destekleyici analiz ve numune hazırlama cihazları.', 'anchor' => 'çevre cihazları'],
                'sogutmali-inkubator' => ['title' => 'Soğutmalı İnkübatör', 'summary' => 'Kontrollü düşük sıcaklık inkübasyon uygulamaları için soğutmalı inkübatörler.', 'anchor' => 'soğutmalı inkübatör'],
                'boi-olcum-cihazi' => ['title' => 'BOİ Ölçüm Cihazı', 'summary' => 'BOİ ölçümü ve çevre analiz süreçleri için laboratuvar ölçüm cihazları.', 'anchor' => 'BOİ ölçüm cihazı'],
                'hot-plate' => ['title' => 'Hot Plate', 'summary' => 'Numune ısıtma ve hazırlama işlemleri için hot plate ve ısıtıcı tabla modelleri.', 'anchor' => 'hot plate modelleri'],
                'rotator-calkalayici' => ['title' => 'Rotatör Çalkalayıcı', 'summary' => 'Rotasyon ve çalkalama uygulamaları için laboratuvar çalkalayıcı cihazları.', 'anchor' => 'rotatör çalkalayıcı'],
                'su-banyolari' => ['title' => 'Su Banyoları', 'summary' => 'Sıcaklık kontrollü su banyosu modellerini hacim ve çalışma aralığına göre değerlendirin.', 'anchor' => 'su banyosu modelleri'],
                'su-banyosu' => ['title' => 'Su Banyosu', 'summary' => 'Klasik sıcaklık kontrollü laboratuvar su banyosu cihazlarını inceleyin.', 'anchor' => 'su banyosu'],
                'ultrasonik-banyo' => ['title' => 'Ultrasonik Banyo', 'summary' => 'Temizleme ve numune hazırlama süreçleri için ultrasonik banyo modelleri.', 'anchor' => 'ultrasonik banyo'],
                'santrifujler' => ['title' => 'Santrifüjler', 'summary' => 'Numune ayırma süreçleri için laboratuvar santrifüjlerini teknik özellikleriyle inceleyin.', 'anchor' => 'santrifüj modelleri'],
                'inkubatorler' => ['title' => 'İnkübatörler', 'summary' => 'Sıcaklık kontrollü inkübasyon cihazlarını kapasite ve uygulama alanına göre listeleyin.', 'anchor' => 'inkübatör modelleri'],
                'erime-noktasi' => ['title' => 'Erime Noktası', 'summary' => 'Erime noktası tayin cihazlarını analiz ihtiyacınıza göre değerlendirin.', 'anchor' => 'erime noktası cihazları'],
                'polarimetreler' => ['title' => 'Polarimetreler', 'summary' => 'Optik rotasyon ve konsantrasyon ölçümü için polarimetre modellerini inceleyin.', 'anchor' => 'polarimetre modelleri'],
                'nem-tayin' => ['title' => 'Nem Tayin', 'summary' => 'Numune nem oranı ölçümleri için nem tayin cihazlarını marka bazlı listeleyin.', 'anchor' => 'nem tayin cihazları'],
                'kral-fischer' => ['title' => 'Karl Fischer', 'summary' => 'Su miktarı tayini için Karl Fischer titratör ve ilgili analiz sistemlerini inceleyin.', 'anchor' => 'Karl Fischer cihazları'],
                'potansiyometrik-titratorler' => ['title' => 'Potansiyometrik Titratörler', 'summary' => 'Titrasyon uygulamaları için otomatik ve potansiyometrik titratörleri değerlendirin.', 'anchor' => 'potansiyometrik titratörler'],
                'manyetik-karistirici' => ['title' => 'Manyetik Karıştırıcı', 'summary' => 'Isıtmalı ve ısıtmasız manyetik karıştırıcı modellerini laboratuvar ihtiyacınıza göre inceleyin.', 'anchor' => 'manyetik karıştırıcı modelleri'],
                'homojenizator' => ['title' => 'Homojenizatör', 'summary' => 'Numune hazırlama süreçleri için homojenizatör cihazlarını teknik özellikleriyle değerlendirin.', 'anchor' => 'homojenizatör modelleri'],
                'termoreaktor' => ['title' => 'Termoreaktör', 'summary' => 'Sıcaklık kontrollü sindirim ve numune hazırlama uygulamaları için termoreaktör modelleri.', 'anchor' => 'termoreaktör modelleri'],
            ],
            'brand_section' => [
                'title' => 'Marka Bazlı Laboratuvar Cihazları',
                'text' => 'MTA Endüstri ürün kataloğunda marka sayfaları, ilgili markanın ürün gruplarını ve kategori bağlantılarını teklif odaklı incelemek için kullanılır.',
            ],
            'selection_section' => [
                'title' => 'Teknik Destek ve Ürün Danışmanlığı',
                'text' => 'Ürün seçiminde cihazın kullanılacağı uygulama, teknik kapasite, servis ihtiyacı ve kalibrasyon gereksinimi birlikte değerlendirilir.',
                'items' => [
                    'Kullanım amacı ve numune tipi',
                    'Ölçüm aralığı, kapasite ve hassasiyet',
                    'Marka, model ve teknik doküman bilgisi',
                    'Servis ve yedek parça ihtiyacı',
                    'Kalibrasyon veya doğrulama gerekliliği',
                    'Teklif ve teslim süreci',
                ],
            ],
            'support_section' => [
                'title' => 'Katalogdan Hizmete Geçiş',
                'text' => 'Ürün kataloğu sadece ürün listeleme amacı taşımaz. Kullanıcıyı ilgili kalibrasyon hizmetine, teknik servis sayfasına veya teklif formuna yönlendirerek karar sürecini netleştirir.',
                'links' => [
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis'],
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'teraziler'],
                    ['url' => route('products.category', 'ph-iletkenlik'), 'anchor' => 'pH iletkenlik & metreler'],
                    ['url' => route('contact'), 'anchor' => 'ürün teklif talebi'],
                ],
            ],
            'cta' => [
                'title' => 'Ürün Kataloğunu İncele',
                'text' => 'Laboratuvar cihazlarını kategori, marka, model ve teknik özellik bilgileriyle inceleyebilir; ilgilendiğiniz ürün için teklif talebi oluşturabilirsiniz.',
                'note' => 'Talebinizi doğru kategori ve marka bilgisiyle eşleştirip teknik ekibin değerlendirmesine açalım.',
                'button' => 'Ürün Kataloğunu İncele',
                'anchor' => 'laboratuvar cihazları teklif talebi',
            ],
            'faq' => [
                [
                    'question' => 'MTA Endüstri ürünleri online satışla mı sunuyor?',
                    'answer' => 'Site sepetli e-ticaret yerine teklif odaklı kurumsal katalog olarak yapılandırılır. Ürün bilgileri incelenir ve talep formu üzerinden teklif süreci başlatılır.',
                ],
                [
                    'question' => 'Ürünler marka ve kategoriye göre filtrelenebilir mi?',
                    'answer' => 'Evet. Ürün kataloğu kategori, marka ve teknik özellik ilişkileriyle yapılandırılmıştır.',
                ],
                [
                    'question' => 'Ürün sayfalarında hangi bilgiler yer alır?',
                    'answer' => 'Ürün adı, marka, kategori, model, görsel, kısa açıklama, teknik özellikler ve ilgili hizmet bağlantıları yer alır.',
                ],
                [
                    'question' => 'Ürün için teknik servis veya kalibrasyon desteği alınabilir mi?',
                    'answer' => 'Cihaz tipine göre teknik servis veya kalibrasyon ihtiyacı ayrıca değerlendirilebilir.',
                ],
            ],
        ];
    }

    private function staticPageSeoContent(string $view): array
    {
        return match ($view) {
            'about' => [
                'meta_title' => 'MTA Endüstri Hakkında | Laboratuvar Cihazları ve Kalibrasyon',
                'meta_description' => 'MTA Endüstri; laboratuvar cihazları, ekipman tedariği, kalibrasyon hizmetleri ve teknik servis desteğiyle kalite kontrol ve AR-GE laboratuvarlarına çözüm sunar.',
                'h1' => 'MTA Endüstri Hakkında',
                'eyebrow' => 'Kurumsal',
                'hero_text' => '2010 yılında kurulan MTA Endüstri Ürünleri; laboratuvar cihazları, ölçüm ekipmanları, sarf malzeme tedariği, kalibrasyon hizmetleri ve teknik servis desteği alanlarında kalite kontrol ve AR-GE laboratuvarlarına sürdürülebilir destek sunmayı hedefler.',
                'primary_cta' => 'MTA Endüstri ile İletişime Geçin',
                'secondary_cta' => 'Hizmetleri İncele',
                'sections' => [
                    ['title' => 'Laboratuvar ve Ölçüm Cihazlarında Teknik Çözüm Ortağı', 'text' => 'MTA Endüstri; kimya, gıda, ilaç, akademik, plastik, petrokimya ve medikal sektörlerdeki iş ortaklarına laboratuvar cihazları, ekipman tedariği, teknik servis ve kalibrasyon alanlarında destek sunar.'],
                    ['title' => 'MTA Endüstri Ne İş Yapar?', 'text' => 'Firma yapısı ürün kataloğu, kalibrasyon hizmetleri, laboratuvar cihazları teknik servis desteği ve teklif talebi süreçlerini tek çatı altında toplar. Amaç; kullanıcıların cihaz, hizmet ve teknik destek ihtiyacını doğru başlık altında netleştirmesidir.'],
                    ['title' => 'Kalibrasyon Hizmetleri', 'text' => 'Basınç, sıcaklık, tork, devir, kütle-terazi ve hacim kalibrasyonu hizmetleri ayrı kapsamlarla yapılandırılır. Ölçüm güvenilirliği, izlenebilirlik ve düzenli kontrol yaklaşımı ön planda tutulur.'],
                    ['title' => 'Teknik Servis Yaklaşımı', 'text' => 'Laboratuvar cihazlarında arıza tespiti, bakım, onarım, yedek parça değerlendirmesi ve servis sonrası performans kontrolü cihaz tipine göre ele alınır.'],
                    ['title' => 'Ürün Kataloğu ve Marka Yapısı', 'text' => 'Ürün kataloğu; teraziler, pH metreler, refraktometreler, viskozimetreler, nem tayin cihazları, titratörler ve karıştırıcılar gibi cihaz gruplarını marka ve kategori bazında sunar.'],
                ],
                'expertise' => [
                    'Laboratuvar cihazları ve ekipman tedariği',
                    'Kalibrasyon hizmetleri',
                    'Laboratuvar cihazları teknik servis',
                    'Marka ve kategori bazlı ürün kataloğu',
                    'Kalite kontrol ve AR-GE laboratuvarlarına teknik destek',
                ],
                'sectors' => ['Kimya', 'Gıda', 'İlaç', 'Akademik Araştırma', 'Plastik', 'Petrokimya', 'Medikal', 'Kalite Kontrol', 'AR-GE'],
                'support_links' => [
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                    ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                    ['url' => route('contact'), 'anchor' => 'iletişim ve teklif talebi'],
                ],
                'cta' => [
                    'title' => 'MTA Endüstri ile İletişime Geçin',
                    'text' => 'Laboratuvar cihazı, teknik servis veya kalibrasyon ihtiyacınızı paylaşarak doğru başlıkta değerlendirme alabilirsiniz.',
                    'button' => 'İletişime Geçin',
                ],
            ],
            'contact' => [
                'meta_title' => 'İletişim ve Teklif Talebi | MTA Endüstri',
                'meta_description' => 'Kalibrasyon hizmeti, laboratuvar cihazları, teknik servis ve ürün teklif talepleriniz için MTA Endüstri iletişim bilgilerine ulaşın.',
                'h1' => 'İletişim ve Teklif Talebi',
                'eyebrow' => 'İletişim',
                'hero_text' => 'Kalibrasyon hizmeti, laboratuvar cihazları, teknik servis veya ürün tedariği talepleriniz için MTA Endüstri teknik ekibine form, telefon veya e-posta üzerinden ulaşabilirsiniz.',
                'primary_cta' => 'Teklif Talebi Oluştur',
                'sections' => [
                    ['title' => 'Teklif Talebi İçin Bilgi Paylaşımı', 'text' => 'Talebinizin hızlı değerlendirilmesi için cihaz tipi, marka, model, ölçüm aralığı, adet, kullanım alanı ve varsa arıza belirtisi bilgilerini mesaj alanında paylaşabilirsiniz.'],
                    ['title' => 'Hangi Talepler İçin İletişime Geçebilirsiniz?', 'text' => 'Kalibrasyon hizmetleri, laboratuvar cihazları teknik servis desteği, ürün kataloğu, marka bazlı cihaz talepleri ve kurumsal bilgi talepleri için iletişim formunu kullanabilirsiniz.'],
                ],
                'request_info' => [
                    'Talep türü: ürün, teknik servis veya kalibrasyon',
                    'İlgili ürün, hizmet veya kategori adı',
                    'Marka, model ve cihaz adedi',
                    'Ölçüm aralığı veya teknik kullanım bilgisi',
                    'Arıza belirtisi veya servis ihtiyacı',
                ],
                'support_links' => [
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis'],
                    ['url' => route('products.index'), 'anchor' => 'ürün kataloğu'],
                    ['url' => route('certificates'), 'anchor' => 'sertifikalar'],
                ],
                'faq' => [
                    ['question' => 'Teklif talebi için hangi bilgileri paylaşmalıyım?', 'answer' => 'Cihaz tipi, marka, model, adet, kullanım alanı ve talebin ürün, servis veya kalibrasyonla ilgili olduğunu belirtmeniz yeterli başlangıç bilgisini sağlar.'],
                    ['question' => 'Kalibrasyon ve teknik servis talepleri aynı formdan gönderilebilir mi?', 'answer' => 'Evet. Mesaj alanında talep türünü belirttiğinizde teknik ekip talebi doğru başlıkla değerlendirir.'],
                    ['question' => 'Ürün fiyatı için nasıl teklif alabilirim?', 'answer' => 'İlgili ürün, marka veya kategori bilgisini mesaj alanında paylaşarak ürün teklif sürecini başlatabilirsiniz.'],
                ],
            ],
            'certificates' => [
                'meta_title' => 'Sertifikalar ve Kurumsal Belgeler | MTA Endüstri',
                'meta_description' => 'MTA Endüstri sertifikaları, kurumsal belgeleri ve kalite süreçlerine ait belge içeriklerini inceleyin.',
                'h1' => 'Sertifikalar ve Kurumsal Belgeler',
                'eyebrow' => 'Belgeler',
                'hero_text' => 'MTA Endüstri sertifikalar sayfası; kurumsal belgeler, kalite süreçlerine ait dokümanlar ve paylaşılacak belge içerikleri için hazırlanmıştır. Bu sayfada sertifika sorgulama veya doğrulama sistemi bulunmaz.',
                'primary_cta' => 'Belge Bilgisi İçin İletişime Geçin',
                'sections' => [
                    ['title' => 'Kurumsal Belge Alanı', 'text' => 'Paylaşılacak sertifika ve kurumsal belgeler hazır olduğunda bu sayfada belge adı, kısa açıklama, kapsam ve dosya bağlantısı ile yayınlanabilir.'],
                    ['title' => 'Kalite ve Güven Odaklı Yapı', 'text' => 'Belge sayfası, kullanıcıların MTA Endüstri’nin kurumsal dokümanlarını düzenli bir yapı içinde inceleyebilmesi için ayrı bir sayfa olarak korunur.'],
                ],
                'document_types' => [
                    'Kurumsal sertifikalar',
                    'Yetkinlik belgeleri',
                    'Kalite süreç belgeleri',
                    'Tedarikçi veya marka belgeleri',
                    'PDF doküman ve ek bilgiler',
                ],
                'support_links' => [
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('about'), 'anchor' => 'MTA Endüstri hakkında'],
                    ['url' => route('contact'), 'anchor' => 'belge bilgisi talebi'],
                ],
                'faq' => [
                    ['question' => 'Bu sayfada sertifika sorgulama yapılacak mı?', 'answer' => 'Hayır. Bu sayfa sertifika sorgulama sistemi olarak değil, kurumsal belge ve sertifika içeriklerinin sunulacağı sayfa olarak yapılandırılmıştır.'],
                    ['question' => 'Belgeler ne zaman listelenecek?', 'answer' => 'Paylaşılacak belge dosyaları ve yayın onayları netleştiğinde bu alana belge adı, açıklama ve dosya bağlantısı eklenebilir.'],
                    ['question' => 'Belge bilgisi için nasıl iletişime geçebilirim?', 'answer' => 'İletişim sayfasındaki form veya telefon/e-posta bilgileri üzerinden belge talebinizi paylaşabilirsiniz.'],
                ],
                'cta' => [
                    'title' => 'Belge Bilgisi İçin İletişime Geçin',
                    'text' => 'Kurumsal belge veya sertifika bilgisi talebinizi MTA Endüstri teknik ekibine iletebilirsiniz.',
                    'button' => 'İletişime Geçin',
                ],
            ],
            'references' => [
                'meta_title' => 'Referanslar ve Uygulama Alanları | MTA Endüstri',
                'meta_description' => 'MTA Endüstri\'nin laboratuvar cihazları, teknik servis ve kalibrasyon hizmetlerinin kullanıldığı sektör ve uygulama alanlarını inceleyin.',
                'h1' => 'Referanslar ve Uygulama Alanları',
                'eyebrow' => 'Sektörler',
                'hero_text' => 'Referanslar sayfası doğrulanmış müşteri adı veya logo yayınlanana kadar sektör ve uygulama alanları üzerinden kurgulanmalıdır. Gerçek müşteri, logo, proje adı veya başarı metriği üretilmemelidir.',
                'primary_cta' => 'İş Birliği İçin İletişime Geçin',
                'sections' => [
                    ['title' => 'Hizmet Verilen Sektörler', 'text' => 'MTA Endüstri; kalite kontrol, üretim, analiz ve AR-GE süreçlerinde laboratuvar cihazları, teknik servis ve kalibrasyon ihtiyaçları bulunan farklı sektörlere teknik destek sunar.'],
                    ['title' => 'Referans Bilgisi Yayınlama Yaklaşımı', 'text' => 'Bu sayfada doğrulanmamış müşteri adı, logo veya proje bilgisi kullanılmaz. Gerçek referanslar yalnızca paylaşım izni ve net içerik sağlandığında yayınlanır.'],
                ],
                'sectors' => [
                    ['name' => 'Kimya', 'text' => 'Kimyasal analiz, kalite kontrol ve üretim laboratuvarlarında kullanılan cihazlar için ürün, servis ve kalibrasyon desteği.'],
                    ['name' => 'Gıda', 'text' => 'Gıda analizleri, numune hazırlama ve kalite kontrol süreçlerinde kullanılan laboratuvar cihazları için teknik destek.'],
                    ['name' => 'İlaç', 'text' => 'İlaç laboratuvarlarında ölçüm güvenilirliği, cihaz performansı ve düzenli kontrol ihtiyacına yönelik hizmetler.'],
                    ['name' => 'Akademik Araştırma', 'text' => 'Üniversite ve araştırma laboratuvarlarında cihaz tedariği, teknik servis ve kalibrasyon süreçleri.'],
                    ['name' => 'Plastik ve Polimer', 'text' => 'Malzeme testleri, numune hazırlama ve kalite kontrol uygulamalarına yönelik laboratuvar cihazları.'],
                    ['name' => 'Petrokimya', 'text' => 'Endüstriyel analiz, ölçüm ve proses güvenilirliği için laboratuvar cihazı ve kalibrasyon desteği.'],
                    ['name' => 'Medikal', 'text' => 'Medikal laboratuvarlarda kullanılan ölçüm ve analiz ekipmanları için teknik değerlendirme süreçleri.'],
                ],
                'support_links' => [
                    ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                    ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                    ['url' => route('contact'), 'anchor' => 'referans ve iş birliği talebi'],
                ],
                'faq' => [
                    ['question' => 'Referanslarda müşteri isimleri neden görünmüyor?', 'answer' => 'Onaylanmamış müşteri adı, logo veya proje bilgisi kullanılmaz. Referans bilgileri ancak paylaşım izni ve net içerik olduğunda yayınlanır.'],
                    ['question' => 'MTA Endüstri hangi sektörlere hizmet verir?', 'answer' => 'Kimya, gıda, ilaç, akademik araştırma, plastik, petrokimya, medikal ve kalite kontrol laboratuvarları başlıca hizmet alanlarıdır.'],
                    ['question' => 'İş birliği talebi nasıl gönderilir?', 'answer' => 'İletişim sayfasındaki form üzerinden sektör, cihaz grubu ve ihtiyaç bilgilerinizi paylaşabilirsiniz.'],
                ],
                'cta' => [
                    'title' => 'Sektörünüze Uygun Teknik Desteği Netleştirelim',
                    'text' => 'Laboratuvar cihazları, kalibrasyon veya teknik servis ihtiyacınızı paylaşarak doğru çözüm başlığına yönlenebilirsiniz.',
                    'button' => 'İletişime Geçin',
                ],
            ],
            default => [],
        };
    }

    private function servicesPageSeoContent(): array
    {
        return [
            'meta_title' => 'Kalibrasyon Hizmetleri | MTA Endüstri',
            'meta_description' => 'Basınç, sıcaklık, tork, devir, kütle-terazi ve hacim kalibrasyonu hizmetlerini MTA Endüstri teknik ekibiyle inceleyin.',
            'h1' => 'Kalibrasyon Hizmetleri',
            'hero_text' => 'MTA Endüstri; basınç, sıcaklık, tork, devir, kütle-terazi ve hacim kalibrasyonu alanlarında teknik kapsamı netleştirilmiş kalibrasyon hizmetleri sunar. Ölçüm cihazlarının güvenilirliği, üretim, kalite kontrol, laboratuvar ve AR-GE süreçlerinde doğru kararlar alınması için kritik öneme sahiptir.',
            'primary_cta' => 'Kalibrasyon Teklifi Al',
            'secondary_cta' => 'Hizmetleri İncele',
            'image' => 'images/services/mta-kalibrasyon-banner-10.webp',
            'image_alt' => 'Endüstriyel kalibrasyon hizmetleri ve laboratuvar ölçüm süreçleri',
            'sections' => [
                [
                    'title' => 'Endüstriyel Kalibrasyon Hizmetleri',
                    'text' => 'Kalibrasyon hizmetleri, ölçüm cihazlarının gösterdiği değerlerin güvenilir referans sistemlerle karşılaştırılması ve sonuçların raporlanması sürecidir. Endüstriyel tesislerde, laboratuvarlarda ve kalite kontrol süreçlerinde kullanılan cihazların düzenli kontrol edilmesi ölçüm güvenilirliği açısından önemlidir.',
                ],
                [
                    'title' => 'Kalibrasyon Hizmet Alanları',
                    'text' => 'MTA Endüstri kalibrasyon hizmetleri; basınç ölçüm cihazları, sıcaklık ölçüm cihazları, tork ekipmanları, devir ölçüm cihazları, tartım cihazları ve hacim ekipmanları için ayrı hizmet sayfalarıyla yapılandırılır. Her hizmet sayfasında cihaz kapsamı, ölçüm aralığı, süreç ve teklif yönlendirmesi açık şekilde sunulur.',
                ],
            ],
            'service_cards' => [
                'basinc-kalibrasyonu' => [
                    'summary' => 'Manometre, dijital manometre, basınç transmitter, basınç sensörü ve fark basınç ölçerler için kalibrasyon hizmeti.',
                    'anchor' => 'basınç kalibrasyonu',
                ],
                'sicaklik-kalibrasyonu' => [
                    'summary' => 'Termometre, sıcaklık sensörü, PT100, termokupl, datalogger, etüv, inkübatör ve otoklav gibi cihazlar için sıcaklık kalibrasyonu.',
                    'anchor' => 'sıcaklık kalibrasyonu',
                ],
                'kutle-terazi-kalibrasyonu' => [
                    'summary' => 'Hassas terazi, analitik terazi, laboratuvar terazisi, endüstriyel terazi ve kütle ekipmanları için kalibrasyon hizmeti.',
                    'anchor' => 'terazi kalibrasyonu',
                ],
                'hacim-kalibrasyonu' => [
                    'summary' => 'Pipet, büret, balon joje, mezür, piknometre, dispenser ve pistonlu hacim cihazları için hacim kalibrasyonu.',
                    'anchor' => 'hacim kalibrasyonu',
                ],
                'devir-kalibrasyonu' => [
                    'summary' => 'Takometre, santrifüj, karıştırıcı, homojenizatör ve frekans kaynakları için devir kalibrasyonu.',
                    'anchor' => 'devir kalibrasyonu',
                ],
                'tork-kalibrasyonu' => [
                    'summary' => 'Tork anahtarı, torkmetre, tork ölçer ve tork el aletleri için tork kalibrasyonu.',
                    'anchor' => 'tork kalibrasyonu',
                ],
            ],
            'device_section' => [
                'title' => 'Hangi Cihazlar İçin Kalibrasyon Hizmeti Alınır?',
                'text' => 'Kalibrasyon ihtiyacı cihazın kullanım alanına, ölçüm riskine, kalite prosedürüne ve periyodik kontrol planına göre belirlenir. Manometreler, termometreler, teraziler, tork ekipmanları, devir ölçüm cihazları ve laboratuvar hacim ekipmanları düzenli kalibrasyon ihtiyacı doğurabilecek cihaz grupları arasındadır.',
                'items' => [
                    'Manometre ve basınç ölçüm cihazları',
                    'Termometre, sıcaklık sensörü ve datalogger cihazları',
                    'Hassas terazi, analitik terazi ve kütle ekipmanları',
                    'Tork anahtarı, torkmetre ve tork ölçerler',
                    'Takometre, santrifüj ve karıştırıcı cihazlar',
                    'Pipet, büret, balon joje ve hacim ekipmanları',
                ],
            ],
            'process' => [
                'title' => 'Kalibrasyon Süreci Nasıl İlerler?',
                'text' => 'Kalibrasyon süreci, cihaz veya ekipman bilgisinin alınmasıyla başlar. Cihaz tipi, marka, model, ölçüm aralığı, adet ve kullanım alanı değerlendirilir. Uygun kapsam belirlendikten sonra ölçüm noktaları planlanır, referans sistemlerle karşılaştırma yapılır, sonuçlar değerlendirilir ve raporlanır.',
                'steps' => [
                    'Talep ve cihaz bilgisinin alınması',
                    'Kalibrasyon kapsamının belirlenmesi',
                    'Cihaz kabulü veya yerinde değerlendirme',
                    'Referans sistemlerle ölçüm',
                    'Sonuçların değerlendirilmesi',
                    'Raporlama ve teslim',
                ],
            ],
            'support_section' => [
                'title' => 'Kalibrasyon, Teknik Servis ve Ürün Kataloğu Bağlantısı',
                'text' => 'Bazı cihazlarda kalibrasyon öncesinde teknik servis değerlendirmesi gerekebilir. Ölçüm stabilitesi bozulmuş, arızalı veya fiziksel hasar görmüş cihazlarda önce bakım ve onarım ihtiyacı incelenmelidir. Yeni cihaz ihtiyacı olan kullanıcılar ise MTA Endüstri ürün kataloğundaki laboratuvar cihazlarını kategori ve marka bazında inceleyebilir.',
                'links' => [
                    ['url' => route('technical-services.index'), 'anchor' => 'teknik servis hizmetleri'],
                    ['url' => route('products.index'), 'anchor' => 'laboratuvar cihazları'],
                    ['url' => route('contact'), 'anchor' => 'kalibrasyon teklifi'],
                ],
            ],
            'cta' => [
                'title' => 'Kalibrasyon Teklifi Alın',
                'text' => 'Basınç, sıcaklık, tork, devir, kütle-terazi veya hacim kalibrasyonu ihtiyacınız için MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, ölçüm aralığı, adet ve kullanım alanı bilgilerinize göre kalibrasyon kapsamı değerlendirilir.',
                'note' => 'Kalibrasyon ihtiyacınızı paylaşın; cihaz grubuna göre doğru hizmet kapsamını birlikte netleştirelim.',
                'button' => 'Kalibrasyon Teklifi Al',
                'anchor' => 'kalibrasyon teklifi',
            ],
            'faq' => [
                [
                    'question' => 'Kalibrasyon hizmeti ne işe yarar?',
                    'answer' => 'Kalibrasyon hizmeti, ölçüm cihazlarının gösterdiği değerlerin referans sistemlerle karşılaştırılması ve doğruluk durumunun raporlanması için uygulanır.',
                ],
                [
                    'question' => 'Hangi cihazlar için kalibrasyon yapılır?',
                    'answer' => 'Manometre, termometre, sıcaklık sensörü, terazi, tork anahtarı, takometre, pipet, büret, balon joje ve benzeri ölçüm ekipmanları kalibrasyon kapsamında değerlendirilebilir.',
                ],
                [
                    'question' => 'Kalibrasyon periyodu nasıl belirlenir?',
                    'answer' => 'Kalibrasyon periyodu cihazın kullanım yoğunluğu, ölçüm riski, kalite prosedürü, sektör gereklilikleri ve geçmiş kalibrasyon sonuçlarına göre belirlenmelidir.',
                ],
                [
                    'question' => 'Arızalı cihaz kalibrasyona alınır mı?',
                    'answer' => 'Cihaz stabil çalışmıyorsa veya arıza belirtisi varsa önce teknik servis değerlendirmesi gerekebilir. Uygun görülürse servis sonrası kalibrasyon süreci planlanır.',
                ],
            ],
        ];
    }

    private function normalizeServiceSeoContent(array $serviceSeo): array
    {
        if ($serviceSeo === []) {
            return [];
        }

        $serviceSeo = match ($serviceSeo['slug'] ?? null) {
            'basinc-kalibrasyonu' => array_merge($serviceSeo, [
                'scope_chip' => 'Kapsam değerleri belgeyle doğrulanacak',
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Basınç Aralıkları',
                    'text' => 'Basınç kalibrasyonu kapsamı, cihaz tipine ve ölçüm aralığına göre belirlenir. Bağıl basınç ve fark basınç ölçümleri farklı cihaz gruplarıyla değerlendirilir. Mevcut kapsam değerleri belgeyle doğrulanmadan kesin teknik iddia olarak kullanılmamalıdır.',
                    'headings' => ['Cihaz / ekipman grubu', 'Kapsam durumu', 'Açıklama'],
                    'rows' => [
                        ['Analog manometre', 'Belgeyle doğrulanacak', 'Bağıl basınç ölçümleri için değerlendirilir.'],
                        ['Dijital manometre', 'Belgeyle doğrulanacak', 'Sayısal göstergeli basınç ölçüm cihazları için değerlendirilir.'],
                        ['Basınç transdüseri', 'Belgeyle doğrulanacak', 'Basınç sinyali üreten cihazlar için değerlendirilir.'],
                        ['Basınç transmitteri', 'Belgeyle doğrulanacak', 'Proses ölçüm ve kontrol uygulamalarında kullanılan cihazlar içindir.'],
                        ['Fark basınç ölçer', 'Belgeyle doğrulanacak', 'Fark basınç ölçüm cihazları için kapsamlandırılır.'],
                    ],
                ],
            ]),
            'sicaklik-kalibrasyonu' => array_merge($serviceSeo, [
                'scope_chip' => 'Sıcaklık aralıkları belgeyle doğrulanacak',
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Cihaz Grupları',
                    'text' => 'Sıcaklık kalibrasyonu kapsamı, cihaz tipine, ölçüm aralığına ve kullanım amacına göre belirlenir. Tüm sıcaklık aralıkları ve kontrollü hacim sınırları resmi kapsam belgesiyle doğrulanmadan kesin kapasite iddiası olarak sunulmaz.',
                    'headings' => ['Cihaz / ekipman grubu', 'Kapsam durumu', 'Açıklama'],
                    'rows' => [
                        ['Termometre ve sıcaklık sensörleri', 'Belgeyle doğrulanacak', 'Sensör tipine ve kullanım aralığına göre değerlendirilir.'],
                        ['PT100 ve termokupllar', 'Belgeyle doğrulanacak', 'Gösterge ve sensör kombinasyonuna göre planlanır.'],
                        ['Datalogger cihazları', 'Belgeyle doğrulanacak', 'Sıcaklık izleme cihazları için değerlendirilir.'],
                        ['Etüv, inkübatör ve otoklav', 'Belgeyle doğrulanacak', 'Kontrollü hacimlerde sıcaklık davranışı incelenir.'],
                        ['Higrometre ve bağıl nem ölçerler', 'Belgeyle doğrulanacak', 'Sıcaklık ve bağıl nem ölçümleri birlikte değerlendirilebilir.'],
                    ],
                ],
            ]),
            'kutle-terazi-kalibrasyonu' => array_merge($serviceSeo, [
                'meta_description' => 'Hassas terazi, analitik terazi, laboratuvar terazisi, endüstriyel terazi ve kütle ekipmanları için kalibrasyon hizmeti alın.',
                'hero_text' => 'Terazi kalibrasyonu, tartım cihazlarının gösterdiği kütle değerinin referans kütlelerle karşılaştırılarak ölçüm doğruluğunun değerlendirilmesi ve sonuçların raporlanmasıdır. MTA Endüstri; hassas terazi, analitik terazi, laboratuvar terazisi, endüstriyel terazi ve kütle ekipmanları için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'scope_chip' => 'Kütle ve terazi kapsamı belgeyle doğrulanacak',
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Cihaz Grupları',
                    'text' => 'Kütle ve terazi kalibrasyonu kapsamı cihaz kapasitesi, kütle sınıfı ve kullanım amacına göre belirlenir. M1, M2, M3 kütle aralıkları ve terazi kapsamı resmi belgeyle doğrulanmadan kesin teknik iddia olarak sunulmaz.',
                    'headings' => ['Cihaz / ekipman grubu', 'Kapsam durumu', 'Açıklama'],
                    'rows' => [
                        ['M1 sınıfı kütleler', 'Belgeyle doğrulanacak', 'Referans kütle kontrol süreçleri için değerlendirilir.'],
                        ['M2 sınıfı kütleler', 'Belgeyle doğrulanacak', 'Tartım doğrulama ve kalite süreçlerinde kullanılabilir.'],
                        ['M3 sınıfı kütleler', 'Belgeyle doğrulanacak', 'Endüstriyel tartım süreçlerinde kullanılan kütleler için uygundur.'],
                        ['Standart olmayan kütleler', 'Belgeyle doğrulanacak', 'Özel kullanım amaçlı kütleler için değerlendirme yapılır.'],
                        ['Teraziler', 'Belgeyle doğrulanacak', 'Cihaz kapasitesine göre kapsamlandırılır.'],
                    ],
                ],
            ]),
            'hacim-kalibrasyonu' => array_merge($serviceSeo, [
                'meta_title' => 'Hacim Kalibrasyonu, Pipet ve Büret Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Pipet, büret, balon joje, mezür, piknometre, dispenser ve pistonlu hacim cihazları için hacim kalibrasyonu hizmeti alın.',
                'h1' => 'Hacim Kalibrasyonu',
                'hero_text' => 'Hacim kalibrasyonu, laboratuvarlarda kullanılan hacim ölçüm ekipmanlarının referans yöntemlerle değerlendirilmesi ve sonuçların raporlanması sürecidir. MTA Endüstri; pipet, büret, balon joje, mezür, piknometre, dispenser ve pistonlu hacim cihazları için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'image_alt' => 'Pipet ve büret gibi hacim ölçüm ekipmanları için kalibrasyon hizmeti',
                'scope_chip' => 'Hacim kapsamı belgeyle doğrulanacak',
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Ekipman Grupları',
                    'text' => 'Hacim kalibrasyonu kapsamı, ekipmanın nominal hacmine, kullanım şekline ve laboratuvar prosedürüne göre belirlenir. Hacim aralıkları, yöntemler ve ekipman kapsamı belgeyle doğrulanmadan kesin teknik iddia olarak sunulmaz.',
                    'headings' => ['Ekipman grubu', 'Kapsam durumu', 'Açıklama'],
                    'rows' => [
                        ['Pipet', 'Belgeyle doğrulanacak', 'Hacim aktarımı ve analiz süreçlerinde kullanılır.'],
                        ['Büret', 'Belgeyle doğrulanacak', 'Titrasyon işlemleri için kullanılan hacim ekipmanıdır.'],
                        ['Balon joje', 'Belgeyle doğrulanacak', 'Çözelti hazırlama süreçlerinde değerlendirilir.'],
                        ['Mezür', 'Belgeyle doğrulanacak', 'Laboratuvar hacim ölçüm ekipmanları için değerlendirilir.'],
                        ['Piknometre ve dispenser', 'Belgeyle doğrulanacak', 'Yoğunluk ve rutin hacim aktarım işlemleriyle ilişkilidir.'],
                    ],
                ],
            ]),
            'tork-kalibrasyonu' => array_merge($serviceSeo, [
                'meta_description' => 'Tork anahtarı, torkmetre, tork ölçer, tork el aletleri ve tork büyütücüler için tork kalibrasyonu hizmeti alın.',
                'h1' => 'Tork Kalibrasyonu',
                'hero_text' => 'Tork kalibrasyonu, tork ölçen veya tork uygulayan ekipmanların referans sistemlerle karşılaştırılarak ölçüm doğruluğunun değerlendirilmesidir. MTA Endüstri; tork anahtarı, torkmetre, tork ölçer, tork el aletleri ve tork büyütücü ekipmanlar için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'scope_chip' => 'Tork kapsamı belgeyle doğrulanacak',
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Tork Aralığı',
                    'text' => 'Tork kalibrasyonu kapsamı ekipman tipine, ölçüm aralığına ve kullanım amacına göre belirlenir. Tork aralığı resmi belgeyle doğrulanmadan kesin teknik iddia olarak sunulmaz.',
                    'headings' => ['Cihaz / ekipman grubu', 'Kapsam durumu', 'Açıklama'],
                    'rows' => [
                        ['Tork anahtarı', 'Belgeyle doğrulanacak', 'Sıkma ve montaj süreçlerinde kullanılan ekipmanlar için değerlendirilir.'],
                        ['Torkmetre ve tork ölçer', 'Belgeyle doğrulanacak', 'Tork ölçümü yapan cihazlar için kapsamlandırılır.'],
                        ['Tork el aletleri', 'Belgeyle doğrulanacak', 'Montaj ve bakım süreçlerinde kullanılan el aletleri için uygundur.'],
                        ['Tork büyütücü', 'Belgeyle doğrulanacak', 'Yüksek tork uygulamalarında kullanılan ekipmanlar için değerlendirilir.'],
                    ],
                ],
            ]),
            'devir-kalibrasyonu' => array_merge($serviceSeo, [
                'meta_title' => 'Devir Kalibrasyonu, Takometre ve RPM Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Takometre, frekans kaynakları, santrifüj, karıştırıcı ve homojenizatör gibi devir ölçüm cihazları için kalibrasyon hizmeti alın.',
                'h1' => 'Devir Kalibrasyonu',
                'hero_text' => 'Devir kalibrasyonu, dönme hızı veya frekans ölçümü yapan cihazların referans sistemlerle karşılaştırılarak değerlendirilmesi ve sonuçların raporlanmasıdır. MTA Endüstri; takometre, frekans kaynakları, santrifüj, karıştırıcı ve homojenizatör gibi ekipmanlar için devir kalibrasyonu taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'image_alt' => 'Takometre ve laboratuvar karıştırıcıları için devir kalibrasyonu hizmeti',
                'scope_chip' => 'Devir kapsamı belgeyle doğrulanacak',
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Devir Aralığı',
                    'text' => 'Devir kalibrasyonu cihaz tipine ve çalışma aralığına göre planlanır. Devir kapsamı resmi belgeyle doğrulanmadan kesin teknik iddia olarak sunulmaz.',
                    'headings' => ['Cihaz / ekipman grubu', 'Kapsam durumu', 'Açıklama'],
                    'rows' => [
                        ['Takometre', 'Belgeyle doğrulanacak', 'Dönme hızını ölçen cihazlar için değerlendirilir.'],
                        ['Frekans kaynakları', 'Belgeyle doğrulanacak', 'Dönme hızı veya frekans üreten cihazlar için değerlendirilir.'],
                        ['Santrifüj ve karıştırıcı cihazlar', 'Belgeyle doğrulanacak', 'Laboratuvar cihazlarında hız doğrulama için kullanılır.'],
                        ['Homojenizatör', 'Belgeyle doğrulanacak', 'Numune hazırlama cihazlarında hız kontrolüyle ilişkilidir.'],
                    ],
                ],
            ]),
            default => $serviceSeo,
        };

        return $serviceSeo;
    }

    private function serviceSeoContent(string $slug): array
    {
        return match ($slug) {
            'basinc-kalibrasyonu' => [
                'slug' => 'basinc-kalibrasyonu',
                'meta_title' => 'Basınç Kalibrasyonu ve Manometre Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Manometre, dijital manometre, basınç transmitter, basınç sensörü ve fark basınç ölçerler için basınç kalibrasyonu hizmeti alın.',
                'h1' => 'Basınç Kalibrasyonu ve Manometre Kalibrasyonu',
                'hero_text' => 'Basınç kalibrasyonu, basınç ölçen cihazların referans ekipmanlarla karşılaştırılarak ölçüm doğruluğunun değerlendirilmesi ve sonuçların raporlanması sürecidir. MTA Endüstri; analog manometre, dijital manometre, basınç transmitter, basınç transdüseri, basınç sensörü, vakum ölçer ve fark basınç ölçer cihazları için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'primary_cta' => 'Basınç Kalibrasyonu İçin Teklif Al',
                'secondary_cta' => 'Teknik Servis Hizmetlerini İncele',
                'secondary_cta_url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'),
                'image_alt' => 'Manometre ve basınç ölçüm cihazları için basınç kalibrasyonu hizmeti',
                'sections' => [
                    [
                        'title' => 'Basınç Kalibrasyonu Nedir?',
                        'text' => 'Basınç kalibrasyonu, basınç ölçüm cihazının gösterdiği değerin güvenilir referans sistemlerle karşılaştırılmasıdır. Bu işlem sonucunda cihazın ölçüm doğruluğu, sapma durumu ve kullanım amacına uygunluğu değerlendirilir. Üretim, bakım, kalite kontrol ve proses güvenliği süreçlerinde kullanılan basınç ölçüm cihazları için düzenli kalibrasyon önemlidir.',
                    ],
                    [
                        'title' => 'Hangi Basınç Ölçüm Cihazları Kalibre Edilir?',
                        'text' => 'Basınç kalibrasyonu kapsamında analog manometreler, dijital manometreler, basınç transmitterleri, basınç transdüserleri, basınç sensörleri, vakum ölçerler ve fark basınç ölçerler değerlendirilebilir. Cihaz tipi, ölçüm aralığı, kullanım ortamı ve proses gereksinimleri kalibrasyon kapsamının belirlenmesinde dikkate alınır.',
                    ],
                    [
                        'title' => 'Manometre Kalibrasyonu',
                        'text' => 'Manometre kalibrasyonu, analog veya dijital manometrenin belirli basınç noktalarında referans cihazla karşılaştırılmasıdır. Manometreler üretim hatları, bakım uygulamaları, proses sistemleri ve kalite kontrol alanlarında kullanıldığı için ölçüm sapmalarının düzenli olarak takip edilmesi gerekir.',
                    ],
                    [
                        'title' => 'Basınç Transmitter ve Sensör Kalibrasyonu',
                        'text' => 'Basınç transmitterleri ve sensörleri proses kontrol sistemlerinde ölçüm sinyali üreten kritik ekipmanlardır. Bu cihazlarda ölçüm sapması, hatalı proses değerlendirmelerine veya kontrol problemlerine neden olabilir. Kalibrasyon sürecinde cihazın ölçüm aralığı, çıkış sinyali ve kullanım koşulları dikkate alınır.',
                    ],
                ],
                'device_list' => [
                    'title' => 'Basınç Kalibrasyonu Kapsamındaki Cihazlar',
                    'text' => 'Aşağıdaki cihaz grupları ölçüm aralığı, kullanım ortamı ve proses gereksinimlerine göre basınç kalibrasyonu kapsamında değerlendirilebilir.',
                    'items' => [
                        'Analog manometre',
                        'Dijital manometre',
                        'Basınç transmitter',
                        'Basınç transdüseri',
                        'Basınç sensörü',
                        'Vakum ölçer',
                        'Fark basınç ölçer',
                        'Basınç ölçüm ekipmanları',
                    ],
                ],
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Basınç Aralıkları',
                    'text' => 'Basınç kalibrasyonu kapsamı, cihaz tipine ve ölçüm aralığına göre belirlenir. Bağıl basınç ve fark basınç ölçümleri farklı cihaz gruplarıyla değerlendirilir.',
                    'headings' => ['Cihaz / ekipman grubu', 'Örnek kapsam', 'Açıklama'],
                    'rows' => [
                        ['Analog manometre', 'Belgeyle doğrulanacak', 'Bağıl basınç ölçümleri için değerlendirilir.'],
                        ['Dijital manometre', 'Belgeyle doğrulanacak', 'Sayısal göstergeli basınç ölçüm cihazları için uygundur.'],
                        ['Basınç transdüseri', 'Belgeyle doğrulanacak', 'Basınç sinyali üreten cihazlar için değerlendirilir.'],
                        ['Basınç transmitteri', 'Belgeyle doğrulanacak', 'Proses ölçüm ve kontrol uygulamalarında kullanılan cihazlar içindir.'],
                        ['Fark basınç ölçer', 'Belgeyle doğrulanacak', 'Fark basınç ölçüm cihazları için kapsamlandırılır.'],
                    ],
                ],
                'process' => [
                    'title' => 'Basınç Kalibrasyonu Süreci Nasıl İlerler?',
                    'text' => 'Süreç, cihaz tipi ve ölçüm aralığı bilgisinin alınmasıyla başlar. Cihaz kabulü veya yerinde değerlendirme sonrası uygun ölçüm noktaları belirlenir. Referans ekipmanlarla karşılaştırma yapılır, sonuçlar değerlendirilir ve raporlanır.',
                    'steps' => [
                        'Talep ve cihaz bilgisinin alınması',
                        'Cihaz kabulü ve ön kontrol',
                        'Ölçüm noktalarının belirlenmesi',
                        'Referans ekipmanla karşılaştırma',
                        'Sonuçların değerlendirilmesi',
                        'Raporlama ve teslim',
                    ],
                    'descriptions' => [
                        'Cihaz tipi, marka, model, ölçüm aralığı ve adet bilgileri alınır.',
                        'Cihazın fiziksel durumu, bağlantıları ve gösterge davranışı ön kontrolden geçirilir.',
                        'Kullanım amacı ve ölçüm aralığına göre uygun basınç noktaları belirlenir.',
                        'Referans ekipmanla karşılaştırma yapılarak ölçüm verileri alınır.',
                        'Sapma, tekrarlanabilirlik ve kullanım amacına uygunluk teknik olarak incelenir.',
                        'Sonuçlar raporlanır ve kullanıcıya teslim süreci hakkında bilgi verilir.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Basınç Kalibrasyonu Hangi Alanlarda Kullanılır?',
                        'text' => 'Basınç kalibrasyonu; üretim tesisleri, proses hatları, bakım ekipleri, kalite kontrol laboratuvarları, enerji sistemleri ve endüstriyel ölçüm uygulamalarında kullanılır. Basınç ölçümündeki sapmalar proses güvenliği ve ürün kalitesi üzerinde doğrudan etkili olabilir.',
                        'links' => [
                            ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                        ],
                    ],
                    [
                        'title' => 'Kalibrasyon Öncesi Teknik Servis İhtiyacı',
                        'text' => 'Basınç ölçüm cihazında fiziksel hasar, bağlantı problemi, gösterge arızası, sıfır kayması veya tekrarlanabilirlik sorunu varsa kalibrasyon öncesinde teknik servis değerlendirmesi gerekebilir. Stabil olmayan veya arızalı cihazlarda önce teknik durumun incelenmesi daha doğru sonuç verir.',
                        'links' => [
                            ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                        ],
                    ],
                ],
                'cta' => [
                    'title' => 'Basınç Kalibrasyonu İçin Teklif Alın',
                    'text' => 'Manometre, dijital manometre, basınç transmitter, basınç sensörü veya fark basınç ölçer cihazlarınız için kalibrasyon teklifi almak üzere MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, ölçüm aralığı ve adet bilgileriyle talebiniz değerlendirilir.',
                    'note' => 'Basınç ölçüm cihazlarınız için kalibrasyon kapsamını netleştirelim ve teklif sürecini başlatalım.',
                    'button' => 'Basınç Kalibrasyonu İçin Teklif Al',
                    'anchor' => 'basınç kalibrasyonu teklif talebi',
                ],
                'related_products' => [
                    'title' => 'İlgili teknik servis ve kalibrasyon sayfaları',
                    'button' => 'Kalibrasyon Hizmetleri',
                    'url' => route('services.index'),
                    'category_slugs' => [],
                ],
                'faq' => [
                    [
                        'question' => 'Basınç kalibrasyonu ne sıklıkla yapılmalı?',
                        'answer' => 'Kalibrasyon periyodu cihazın kullanım yoğunluğu, proses riski, kalite prosedürü ve sektör gerekliliklerine göre belirlenmelidir. Kritik ölçümlerde kullanılan basınç cihazları için düzenli kontrol planı oluşturulması önerilir.',
                    ],
                    [
                        'question' => 'Manometre kalibrasyonu hangi cihazları kapsar?',
                        'answer' => 'Analog manometreler, dijital manometreler, vakum ölçerler ve farklı basınç göstergeleri kullanım aralığına göre kalibrasyon kapsamında değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Basınç transmitter kalibrasyonu neden önemlidir?',
                        'answer' => 'Basınç transmitterleri proses kontrol sistemlerine ölçüm sinyali gönderir. Ölçüm sapması hatalı proses kontrolüne neden olabileceği için düzenli doğrulama önemlidir.',
                    ],
                    [
                        'question' => 'Kalibrasyon için hangi bilgiler gerekir?',
                        'answer' => 'Cihaz tipi, marka, model, ölçüm aralığı, proses kullanımı ve adet bilgileri teklif süreci için gerekli başlangıç bilgilerini sağlar.',
                    ],
                ],
            ],
            'devir-kalibrasyonu' => [
                'slug' => 'devir-kalibrasyonu',
                'meta_title' => 'Devir Kalibrasyonu ve Takometre Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Takometre, santrifüj, karıştırıcı ve frekans kaynakları için devir kalibrasyonu ve rpm doğrulama hizmeti alın.',
                'h1' => 'Devir Kalibrasyonu ve Takometre Kalibrasyonu',
                'hero_text' => 'Devir kalibrasyonu, dönme hızı veya frekans değeri üreten ya da ölçen cihazların referans sistemlerle karşılaştırılarak doğruluk durumunun değerlendirilmesi sürecidir. MTA Endüstri; takometre, frekans kaynağı, santrifüj, manyetik karıştırıcı, mekanik karıştırıcı ve homojenizatör gibi cihaz grupları için devir kalibrasyonu taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'primary_cta' => 'Devir Kalibrasyonu İçin Teklif Al',
                'secondary_cta' => 'Karıştırıcı Modellerini İncele',
                'secondary_cta_url' => route('products.category', 'manyetik-karistirici'),
                'image_alt' => 'Takometre ve karıştırıcı cihazlar için devir kalibrasyonu hizmeti',
                'sections' => [
                    [
                        'title' => 'Devir Kalibrasyonu Nedir?',
                        'text' => 'Devir kalibrasyonu, cihazın gösterdiği veya ürettiği rpm değerinin güvenilir referans sistemlerle karşılaştırılmasıdır. Dönme hızı; karıştırma, santrifüjleme, numune hazırlama ve viskozite ölçümü gibi laboratuvar süreçlerinde tekrarlanabilir sonuçlar için önemli bir parametredir.',
                    ],
                    [
                        'title' => 'Hangi Cihazlar İçin Devir Kalibrasyonu Yapılır?',
                        'text' => 'Devir kalibrasyonu kapsamında takometreler, frekans kaynakları, frekans standartları, santrifüjler, karıştırıcı cihazlar ve farklı dönme hızı üreten laboratuvar ekipmanları değerlendirilebilir. Cihazın çalışma aralığı, kullanım amacı ve proses gereksinimi kalibrasyon kapsamını belirler.',
                    ],
                    [
                        'title' => 'Takometre ve RPM Kalibrasyonu',
                        'text' => 'Takometre kalibrasyonu, dönme hızını ölçen cihazların belirli rpm noktalarında referans sistemlerle karşılaştırılmasıdır. Bakım ekipleri, üretim hatları ve laboratuvar uygulamalarında kullanılan takometrelerde ölçüm doğruluğunun düzenli takip edilmesi gerekir.',
                    ],
                    [
                        'title' => 'Santrifüj ve Karıştırıcı Devir Kontrolü',
                        'text' => 'Santrifüj, manyetik karıştırıcı, mekanik karıştırıcı ve homojenizatör gibi cihazlarda devir değeri uygulama sonucunu doğrudan etkileyebilir. Hızın stabil olmaması, numune hazırlama ve analiz süreçlerinde tekrarlanabilirliği azaltabilir. Bu nedenle devir doğrulama ihtiyacı teknik olarak değerlendirilmelidir.',
                    ],
                ],
                'device_list' => [
                    'title' => 'Devir Kalibrasyonu Kapsamındaki Cihazlar',
                    'text' => 'Dönme hızı, frekans veya rpm değeri üreten ve ölçen cihaz grupları çalışma aralığına göre devir kalibrasyonu kapsamında değerlendirilebilir.',
                    'items' => [
                        'Takometre',
                        'Frekans kaynağı',
                        'Frekans standardı',
                        'Santrifüj',
                        'Manyetik karıştırıcı',
                        'Mekanik karıştırıcı',
                        'Homojenizatör',
                        'Viskozimetre',
                        'Laboratuvar karıştırıcı cihazları',
                    ],
                ],
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Devir Aralığı',
                    'text' => 'Devir kalibrasyonu cihaz tipine ve çalışma aralığına göre planlanır. Frekans ve rpm değerleri, uygulama ihtiyacına göre belirlenen ölçüm noktalarında değerlendirilir.',
                    'headings' => ['Cihaz / ekipman grubu', 'Örnek kapsam', 'Açıklama'],
                    'rows' => [
                        ['Frekans kaynakları', 'Belgeyle doğrulanacak', 'Dönme hızı veya frekans üreten cihazlar için değerlendirilir.'],
                        ['Frekans standardı', 'Belgeyle doğrulanacak', 'Referans ve kontrol amaçlı kullanılan ekipmanlar içindir.'],
                        ['Santrifüj ve karıştırıcı cihazlar', 'Belgeyle doğrulanacak', 'Laboratuvar cihazlarında hız doğrulama için kullanılır.'],
                    ],
                ],
                'process' => [
                    'title' => 'Devir Kalibrasyonu Süreci Nasıl İlerler?',
                    'text' => 'Süreç, cihazın tipi, çalışma aralığı ve kullanım amacının belirlenmesiyle başlar. Uygun ölçüm noktaları planlanır, referans sistemle karşılaştırma yapılır, sonuçlar değerlendirilir ve raporlanır.',
                    'steps' => [
                        'Talep ve cihaz bilgisinin alınması',
                        'Cihaz tipi ve rpm aralığının değerlendirilmesi',
                        'Ölçüm noktalarının belirlenmesi',
                        'Referans sistemle karşılaştırma',
                        'Sonuçların değerlendirilmesi',
                        'Raporlama ve teslim',
                    ],
                    'descriptions' => [
                        'Cihaz tipi, marka, model, rpm aralığı ve kullanım alanı bilgileri alınır.',
                        'Cihazın çalışma aralığı ve uygulamadaki hız ihtiyacı teknik olarak değerlendirilir.',
                        'Referans karşılaştırması için uygun rpm veya frekans noktaları belirlenir.',
                        'Referans sistemle karşılaştırma yapılarak ölçüm verileri alınır.',
                        'Sapma, hız stabilitesi ve kullanım amacına uygunluk incelenir.',
                        'Sonuçlar raporlanır ve kullanıcıya teslim süreci hakkında bilgi verilir.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'İlgili Laboratuvar Cihazları',
                        'text' => 'Devir kalibrasyonu; manyetik karıştırıcı, mekanik karıştırıcı, homojenizatör ve viskozimetre gibi cihaz kategorileriyle ilişkilendirilebilir. Bu cihazlarda hız kontrolü uygulama sonucunun tekrarlanabilirliği açısından önemlidir.',
                        'links' => [
                            ['url' => route('products.category', 'manyetik-karistirici'), 'anchor' => 'manyetik karıştırıcı modelleri'],
                            ['url' => route('products.category', 'mekanik-karistirici'), 'anchor' => 'mekanik karıştırıcı modelleri'],
                            ['url' => route('products.category', 'homojenizator'), 'anchor' => 'homojenizatör modelleri'],
                            ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre modelleri'],
                        ],
                    ],
                    [
                        'title' => 'Kalibrasyon Öncesi Teknik Servis Değerlendirmesi',
                        'text' => 'Karıştırıcı, homojenizatör veya viskozimetre gibi cihazlarda hız değeri stabil değilse, motor performansı düşmüşse ya da cihaz çalışmıyorsa kalibrasyon öncesinde teknik servis değerlendirmesi gerekebilir.',
                        'links' => [
                            ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                        ],
                    ],
                ],
                'cta' => [
                    'title' => 'Devir Kalibrasyonu İçin Teklif Alın',
                    'text' => 'Takometre, santrifüj, karıştırıcı, homojenizatör veya frekans kaynaklarınız için devir kalibrasyonu teklifi almak üzere MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, rpm aralığı ve adet bilgileriyle talebiniz değerlendirilir.',
                    'note' => 'Devir ölçüm cihazlarınız için kalibrasyon kapsamını netleştirelim ve teklif sürecini başlatalım.',
                    'button' => 'Devir Kalibrasyonu İçin Teklif Al',
                    'anchor' => 'devir kalibrasyonu teklif talebi',
                ],
                'related_products' => [
                    'title' => 'Karıştırıcı, homojenizatör ve viskozimetre modelleri',
                    'button' => 'Karıştırıcı Modelleri',
                    'url' => route('products.category', 'manyetik-karistirici'),
                    'category_slugs' => ['manyetik-karistirici', 'mekanik-karistirici', 'homojenizator', 'viskozimetre'],
                ],
                'faq' => [
                    [
                        'question' => 'Devir kalibrasyonu ne işe yarar?',
                        'answer' => 'Devir kalibrasyonu, rpm veya frekans değeri üreten ya da ölçen cihazların doğruluğunu değerlendirmek için yapılır.',
                    ],
                    [
                        'question' => 'Takometre kalibrasyonu hangi cihazları kapsar?',
                        'answer' => 'Dönme hızını ölçen takometreler, kullanım aralığına ve ölçüm ihtiyacına göre kalibrasyon kapsamında değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Karıştırıcı cihazlarda devir kontrolü neden önemlidir?',
                        'answer' => 'Karıştırma hızı numune hazırlama sürecinin tekrarlanabilirliğini etkileyebilir. Bu nedenle kritik uygulamalarda hız doğrulaması değerlendirilebilir.',
                    ],
                ],
            ],
            'tork-kalibrasyonu' => [
                'slug' => 'tork-kalibrasyonu',
                'meta_title' => 'Tork Kalibrasyonu ve Tork Anahtarı Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Tork anahtarı, torkmetre, tork ölçer, tork büyütücü ve tork el aletleri için kontrollü tork kalibrasyonu hizmeti alın.',
                'h1' => 'Tork Kalibrasyonu ve Tork Anahtarı Kalibrasyonu',
                'hero_text' => 'Tork kalibrasyonu, tork uygulayan veya tork ölçen ekipmanların referans sistemlerle karşılaştırılarak doğruluk durumunun değerlendirilmesi ve sonuçların raporlanması sürecidir. MTA Endüstri; tork anahtarı, torkmetre, tork ölçer, tork el aletleri, tork büyütücü ve referans tork anahtarı gibi ekipmanlar için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'primary_cta' => 'Tork Kalibrasyonu İçin Teklif Al',
                'secondary_cta' => 'Kalibrasyon Hizmetlerini İncele',
                'secondary_cta_url' => route('services.index'),
                'image_alt' => 'Tork anahtarı ve tork ölçüm ekipmanları için tork kalibrasyonu hizmeti',
                'sections' => [
                    [
                        'title' => 'Tork Kalibrasyonu Nedir?',
                        'text' => 'Tork kalibrasyonu, tork uygulayan veya ölçen bir ekipmanın belirli tork noktalarında referans sistemlerle karşılaştırılmasıdır. Bu işlem sonucunda cihazın sapma durumu, ölçüm doğruluğu ve kullanım amacına uygunluğu değerlendirilir. Montaj, bakım, üretim ve kalite kontrol süreçlerinde doğru tork uygulaması ürün güvenilirliği açısından kritik olabilir.',
                    ],
                    [
                        'title' => 'Hangi Tork Ekipmanları Kalibre Edilir?',
                        'text' => 'Tork kalibrasyonu kapsamında tork anahtarları, torkmetreler, tork ölçerler, tork el aletleri, tork büyütücüler ve referans tork anahtarları değerlendirilebilir. Ekipmanın çalışma aralığı, kullanım yoğunluğu, proses gereksinimi ve kalite prosedürü kalibrasyon kapsamının belirlenmesinde dikkate alınır.',
                    ],
                    [
                        'title' => 'Tork Anahtarı Kalibrasyonu',
                        'text' => 'Tork anahtarı kalibrasyonu, ekipmanın belirlenen tork değerlerinde doğru tork uygulayıp uygulamadığının kontrol edilmesini sağlar. Montaj hatları, bakım ekipleri ve kalite kontrol birimleri için tork anahtarlarının düzenli doğrulanması bağlantı güvenliği ve proses tekrarlanabilirliği açısından önemlidir.',
                    ],
                    [
                        'title' => 'Torkmetre ve Tork Ölçer Kalibrasyonu',
                        'text' => 'Torkmetre ve tork ölçer cihazları, uygulanan tork değerinin ölçülmesi veya doğrulanması için kullanılır. Bu cihazlarda oluşabilecek ölçüm sapmaları kalite kontrol sonuçlarını ve üretim değerlendirmelerini etkileyebilir. Kalibrasyon sürecinde cihazın ölçüm aralığı ve kullanım koşulları dikkate alınır.',
                    ],
                ],
                'device_list' => [
                    'title' => 'Tork Kalibrasyonu Kapsamındaki Ekipmanlar',
                    'text' => 'Tork uygulayan veya tork ölçen ekipmanlar çalışma aralığı, kullanım yoğunluğu ve proses gereksinimine göre kalibrasyon kapsamında değerlendirilir.',
                    'items' => [
                        'Tork anahtarı',
                        'Torkmetre',
                        'Tork ölçer',
                        'Tork el aletleri',
                        'Tork büyütücü',
                        'Referans tork anahtarı',
                        'Tork uygulama ekipmanları',
                    ],
                ],
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Tork Aralığı',
                    'text' => 'Tork kalibrasyonu kapsamı ekipman tipine, ölçüm aralığına ve kullanım amacına göre belirlenir. Farklı tork ekipmanları belirlenen noktalarda referans sistemlerle karşılaştırılır.',
                    'headings' => ['Cihaz / ekipman grubu', 'Örnek kapsam', 'Açıklama'],
                    'rows' => [
                        ['Referans tork anahtarı', 'Belgeyle doğrulanacak', 'Referans amaçlı kullanılan tork ekipmanları için değerlendirilir.'],
                        ['Tork el aletleri', 'Belgeyle doğrulanacak', 'Montaj ve bakım süreçlerinde kullanılan el aletleri için uygundur.'],
                        ['Tork büyütücü', 'Belgeyle doğrulanacak', 'Yüksek tork uygulamalarında kullanılan ekipmanlar için kapsamlandırılır.'],
                    ],
                ],
                'process' => [
                    'title' => 'Tork Kalibrasyonu Süreci Nasıl İlerler?',
                    'text' => 'Süreç, ekipmanın tipi, ölçüm aralığı ve kullanım amacının belirlenmesiyle başlar. Cihaz kabulü ve ön kontrol sonrası uygun ölçüm noktaları planlanır. Referans sistemlerle karşılaştırma yapılır, sonuçlar değerlendirilir ve raporlanır.',
                    'steps' => [
                        'Talep ve cihaz bilgisinin alınması',
                        'Cihaz kabulü ve ön kontrol',
                        'Ölçüm noktalarının belirlenmesi',
                        'Referans sistemle tork karşılaştırması',
                        'Sonuçların değerlendirilmesi',
                        'Raporlama ve teslim',
                    ],
                    'descriptions' => [
                        'Cihaz tipi, marka, model, tork aralığı ve adet bilgileri alınır.',
                        'Ekipmanın fiziksel durumu, kilitleme yapısı ve kullanım durumu ön kontrolden geçirilir.',
                        'Kullanım amacı ve ölçüm aralığına göre uygun tork noktaları belirlenir.',
                        'Referans sistemle karşılaştırma yapılarak ölçüm verileri alınır.',
                        'Sapma, tekrarlanabilirlik ve kullanım amacına uygunluk değerlendirilir.',
                        'Sonuçlar raporlanır ve kullanıcıya teslim süreci hakkında bilgi verilir.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Tork Kalibrasyonu Hangi Alanlarda Kullanılır?',
                        'text' => 'Tork kalibrasyonu; otomotiv, makine, üretim, bakım, enerji, savunma, kalite kontrol ve montaj hatlarında kullanılan tork ekipmanları için tercih edilebilir. Yanlış tork uygulaması bağlantı güvenliği, ürün kalitesi ve proses sürekliliği üzerinde doğrudan etki oluşturabilir.',
                        'links' => [
                            ['url' => route('services.index'), 'anchor' => 'kalibrasyon hizmetleri'],
                        ],
                    ],
                    [
                        'title' => 'Kalibrasyon Öncesi Teknik Değerlendirme',
                        'text' => 'Tork ekipmanında mekanik hasar, kilitleme problemi, tekrarlanabilirlik sorunu, ayar problemi veya ölçüm sapması varsa kalibrasyon öncesinde teknik değerlendirme gerekebilir. Fiziksel durumu uygun olmayan ekipmanlarda önce bakım veya onarım ihtiyacı belirlenmelidir.',
                        'links' => [
                            ['url' => route('contact'), 'anchor' => 'tork kalibrasyonu teklif talebi'],
                        ],
                    ],
                ],
                'list_section' => [
                    'title' => 'Tork Kalibrasyonu Kullanım Alanları',
                    'text' => 'Tork ekipmanlarının düzenli doğrulanması, bağlantı güvenliği ve proses tekrarlanabilirliği açısından farklı endüstriyel uygulamalarda değerlendirilir.',
                    'items' => [
                        'Montaj hatları',
                        'Bakım ve onarım ekipleri',
                        'Kalite kontrol birimleri',
                        'Üretim tesisleri',
                        'Makine ve ekipman üretimi',
                        'Enerji ve endüstriyel bakım uygulamaları',
                        'AR-GE ve test süreçleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Tork Kalibrasyonu İçin Teklif Alın',
                    'text' => 'Tork anahtarı, torkmetre, tork ölçer, tork el aleti veya tork büyütücü ekipmanlarınız için kalibrasyon teklifi almak üzere MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, tork aralığı ve adet bilgileriyle talebiniz değerlendirilir.',
                    'note' => 'Tork ekipmanlarınız için kalibrasyon kapsamını netleştirelim ve teklif sürecini başlatalım.',
                    'button' => 'Tork Kalibrasyonu İçin Teklif Al',
                    'anchor' => 'tork kalibrasyonu teklif talebi',
                ],
                'related_products' => [
                    'title' => 'İlgili kalibrasyon hizmetleri',
                    'button' => 'Kalibrasyon Hizmetleri',
                    'url' => route('services.index'),
                    'category_slugs' => [],
                ],
                'faq' => [
                    [
                        'question' => 'Tork kalibrasyonu ne sıklıkla yapılmalı?',
                        'answer' => 'Kalibrasyon periyodu ekipmanın kullanım yoğunluğu, proses riski, kalite prosedürü ve üretim gereksinimlerine göre belirlenmelidir. Kritik montaj süreçlerinde kullanılan tork ekipmanları için düzenli kontrol planı oluşturulması önerilir.',
                    ],
                    [
                        'question' => 'Tork anahtarı kalibrasyonu neden önemlidir?',
                        'answer' => 'Tork anahtarının doğru değerde tork uygulaması bağlantı güvenliği ve montaj kalitesi açısından önemlidir. Sapma oluşması durumunda bağlantılar gereğinden az veya fazla sıkılabilir.',
                    ],
                    [
                        'question' => 'Torkmetre kalibrasyonu hangi cihazları kapsar?',
                        'answer' => 'Tork ölçümü yapan torkmetreler, tork ölçerler ve referans tork ekipmanları ölçüm aralığına göre kalibrasyon kapsamında değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Tork kalibrasyonu için hangi bilgiler gerekir?',
                        'answer' => 'Cihaz tipi, marka, model, tork aralığı, kullanım alanı ve adet bilgileri teklif süreci için gerekli başlangıç bilgilerini sağlar.',
                    ],
                ],
            ],
            'kutle-terazi-kalibrasyonu' => [
                'slug' => 'kutle-terazi-kalibrasyonu',
                'meta_title' => 'Terazi Kalibrasyonu ve Kütle Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Hassas terazi, analitik terazi, laboratuvar terazisi ve kütleler için ölçüm güvenilirliğini destekleyen terazi kalibrasyonu hizmeti alın.',
                'h1' => 'Terazi Kalibrasyonu ve Kütle Kalibrasyonu',
                'hero_text' => 'Terazi kalibrasyonu, tartım cihazlarının referans kütlelerle karşılaştırılarak ölçüm doğruluğunun değerlendirilmesi ve sonuçların raporlanması sürecidir. MTA Endüstri; hassas terazi, analitik terazi, laboratuvar terazisi, endüstriyel terazi ve kütle ekipmanları için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'primary_cta' => 'Terazi Kalibrasyonu İçin Teklif Al',
                'secondary_cta' => 'Hassas Terazi Modellerini İncele',
                'image_alt' => 'Hassas terazi ve kütle ekipmanları için terazi kalibrasyonu hizmeti',
                'sections' => [
                    [
                        'title' => 'Terazi Kalibrasyonu Nedir?',
                        'text' => 'Terazi kalibrasyonu, tartım cihazının gösterdiği değerin izlenebilir referans kütlelerle karşılaştırılmasıdır. Bu işlem sonucunda cihazın ölçüm doğruluğu, sapma durumu ve kullanım amacına uygunluğu değerlendirilir. Kalibrasyon süreci; laboratuvar, kalite kontrol, üretim ve AR-GE alanlarında güvenilir tartım sonuçları elde etmek için önemlidir.',
                    ],
                    [
                        'title' => 'Hangi Teraziler Kalibre Edilir?',
                        'text' => 'Kütle ve terazi kalibrasyonu kapsamında hassas teraziler, analitik teraziler, laboratuvar terazileri, endüstriyel teraziler ve otomatik ağırlık kontrol terazileri değerlendirilebilir. Cihazın kapasitesi, okunabilirliği, kullanım alanı ve çalışma koşulları kalibrasyon kapsamının belirlenmesinde dikkate alınır.',
                    ],
                    [
                        'title' => 'Kütle Kalibrasyonu Kapsamı',
                        'text' => 'Kütle kalibrasyonu; M1, M2, M3 sınıfı kütleler ve standart olmayan kütlelerin referans değerlerle karşılaştırılması sürecidir. Kütlelerin düzenli kontrol edilmesi, tartım cihazlarının doğrulanması ve ölçüm zincirinin güvenilirliği açısından önem taşır.',
                    ],
                    [
                        'title' => 'Hassas Terazi Kalibrasyonu Neden Önemlidir?',
                        'text' => 'Hassas teraziler düşük ağırlık farklarını ölçtüğü için çevresel koşullar, kullanım yoğunluğu, cihazın konumu ve bakım durumu sonuçları etkileyebilir. Hassas terazi kalibrasyonu, cihazın ölçüm güvenilirliğini takip etmek ve kalite süreçlerinde doğru kararlar almak için uygulanır.',
                    ],
                ],
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Cihaz Grupları',
                    'text' => 'Kütle ve terazi kalibrasyonu; M1, M2, M3 sınıfı kütleler, standart olmayan kütleler, laboratuvar terazileri, hassas teraziler ve yüksek kapasiteli tartım cihazları için planlanabilir. Ölçüm noktaları ve kapsam, cihaz kapasitesi ve kullanım amacına göre belirlenir.',
                    'headings' => ['Cihaz / ekipman grubu', 'Örnek kapsam', 'Açıklama'],
                    'rows' => [
                        ['M1 sınıfı kütleler', '2 kg / 20 kg', 'Referans kütle kontrol süreçleri için değerlendirilir.'],
                        ['M2 sınıfı kütleler', '1 kg / 50 kg', 'Tartım doğrulama ve kalite süreçlerinde kullanılabilir.'],
                        ['M3 sınıfı kütleler', '1 kg / 50 kg', 'Endüstriyel tartım süreçlerinde kullanılan kütleler için uygundur.'],
                        ['Standart olmayan kütleler', '1 g / 30 kg', 'Özel kullanım amaçlı kütleler için değerlendirme yapılır.'],
                        ['Teraziler', 'Belgeyle doğrulanacak', 'Cihaz kapasitesine göre kapsamlandırılır.'],
                        ['Otomatik ağırlık kontrol terazileri', 'Belgeyle doğrulanacak', 'Üretim ve kontrol süreçlerinde kullanılan tartım sistemleri için değerlendirilir.'],
                    ],
                ],
                'process' => [
                    'title' => 'Terazi Kalibrasyonu Süreci Nasıl İlerler?',
                    'text' => 'Kalibrasyon süreci talep ve cihaz bilgisinin alınmasıyla başlar. Cihaz kabulünde terazi modeli, kapasitesi, okunabilirliği ve fiziksel durumu kontrol edilir. Belirlenen ölçüm noktalarında referans kütlelerle karşılaştırma yapılır. Sonuçlar değerlendirilir, raporlanır ve kullanıcıya teslim edilir.',
                    'steps' => [
                        'Talep ve cihaz bilgisinin alınması',
                        'Cihaz kabulü ve ön kontrol',
                        'Referans kütlelerle ölçüm',
                        'Sonuçların değerlendirilmesi',
                        'Raporlama ve teslim',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Terazi Kalibrasyonu ve Teknik Servis İlişkisi',
                        'text' => 'Terazilerde stabilite problemi, tekrarlanabilirlik sorunu, ekran arızası, mekanik hasar veya ölçüm sapması varsa kalibrasyon öncesinde teknik servis değerlendirmesi gerekebilir. Bu nedenle terazi kalibrasyonu, gerektiğinde terazi teknik servis süreciyle birlikte ele alınmalıdır.',
                    ],
                    [
                        'title' => 'Hassas Terazi ve Analitik Terazi Seçimi',
                        'text' => 'Yeni bir tartım cihazı ihtiyacı olan kullanıcılar için hassas terazi ve analitik terazi modelleri marka, kapasite ve teknik özelliklerine göre incelenebilir. MTA Endüstri ürün kataloğunda A&D, Ohaus, Shimadzu ve Weightlab markalarına ait terazi modelleri listelenir.',
                    ],
                ],
                'cta' => [
                    'title' => 'Terazi Kalibrasyonu İçin Teklif Alın',
                    'text' => 'Hassas terazi, analitik terazi, laboratuvar terazisi, endüstriyel terazi veya kütle ekipmanlarınız için kalibrasyon teklifi almak üzere MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, kapasite ve adet bilgileriyle talebiniz değerlendirilir.',
                    'note' => 'Tartım cihazlarınız için kalibrasyon kapsamını netleştirelim ve teklif sürecini başlatalım.',
                    'button' => 'Terazi Kalibrasyonu İçin Teklif Al',
                ],
                'faq' => [
                    [
                        'question' => 'Terazi kalibrasyonu ne sıklıkla yapılmalı?',
                        'answer' => 'Kalibrasyon periyodu cihazın kullanım yoğunluğuna, kalite prosedürlerine, sektör gerekliliklerine ve ölçüm riskine göre belirlenmelidir. Yoğun kullanılan hassas teraziler için periyodik kontrol planı oluşturulması önerilir.',
                    ],
                    [
                        'question' => 'Hassas terazi kalibrasyonu ile analitik terazi kalibrasyonu aynı mıdır?',
                        'answer' => 'Temel yöntem benzer olsa da cihazın okunabilirliği, kapasitesi, kullanım amacı ve ölçüm noktaları farklılık gösterebilir. Analitik teraziler daha hassas ölçüm yaptığı için çevresel koşullar ve cihaz stabilitesi daha kritik hale gelir.',
                    ],
                    [
                        'question' => 'Kalibrasyon öncesinde teknik servis gerekir mi?',
                        'answer' => 'Cihazda stabilite problemi, ekran arızası, mekanik hasar veya tekrarlanabilirlik sorunu varsa kalibrasyon öncesinde teknik servis değerlendirmesi gerekebilir.',
                    ],
                    [
                        'question' => 'Terazi kalibrasyonu için hangi bilgiler gerekir?',
                        'answer' => 'Marka, model, kapasite, okunabilirlik, cihaz adedi, kullanım alanı ve varsa mevcut sorun bilgileri teklif süreci için yeterli başlangıç bilgilerini sağlar.',
                    ],
                ],
            ],
            'sicaklik-kalibrasyonu' => [
                'slug' => 'sicaklik-kalibrasyonu',
                'meta_title' => 'Sıcaklık Kalibrasyonu ve Termometre Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Termometre, sıcaklık sensörü, PT100, etüv, inkübatör, otoklav ve datalogger cihazları için sıcaklık kalibrasyonu hizmeti alın.',
                'h1' => 'Sıcaklık Kalibrasyonu ve Termometre Kalibrasyonu',
                'hero_text' => 'Sıcaklık kalibrasyonu, sıcaklık ölçen veya sıcaklık kontrollü çalışan cihazların referans sistemlerle karşılaştırılarak ölçüm doğruluğunun değerlendirilmesi ve sonuçların raporlanması sürecidir. MTA Endüstri; termometre, sıcaklık sensörü, PT100, termokupl, datalogger, etüv, inkübatör, otoklav ve kontrollü hacimler için sıcaklık kalibrasyonu taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'primary_cta' => 'Sıcaklık Kalibrasyonu İçin Teklif Al',
                'secondary_cta' => 'Etüv Cihazlarını İncele',
                'secondary_cta_url' => route('products.category', 'etuv'),
                'image_alt' => 'Termometre ve sıcaklık sensörleri için sıcaklık kalibrasyonu hizmeti',
                'sections' => [
                    [
                        'title' => 'Sıcaklık Kalibrasyonu Nedir?',
                        'text' => 'Sıcaklık kalibrasyonu, sıcaklık ölçüm cihazının veya sıcaklık kontrollü ekipmanın gösterdiği değerin güvenilir referans sistemlerle karşılaştırılmasıdır. Bu süreç sonucunda cihazın ölçüm doğruluğu, sapma durumu ve kullanım amacına uygunluğu değerlendirilir. Laboratuvar, üretim, kalite kontrol ve proses uygulamalarında sıcaklık doğruluğu güvenilir sonuçlar için kritik öneme sahiptir.',
                    ],
                    [
                        'title' => 'Hangi Cihazlar İçin Sıcaklık Kalibrasyonu Yapılır?',
                        'text' => 'Sıcaklık kalibrasyonu kapsamında termometreler, dijital termometreler, sıvılı cam termometreler, sıcaklık sensörleri, PT100 problar, termokupllar, IR termometreler, pirometreler, datalogger cihazları, higrometreler ve sıcaklık kontrollü hacimler değerlendirilebilir.',
                    ],
                    [
                        'title' => 'Kontrollü Hacimlerde Sıcaklık Kalibrasyonu',
                        'text' => 'Etüv, inkübatör, otoklav, sıvı banyo, soğuk oda ve iklimlendirme kabini gibi kontrollü hacimlerde sıcaklık dağılımı ve sıcaklık stabilitesi değerlendirilir. Bu cihazlarda yalnızca gösterge değeri değil, hacim içindeki farklı noktaların sıcaklık davranışı da ölçüm güvenilirliği açısından önemlidir.',
                    ],
                    [
                        'title' => 'Termometre, PT100 ve Termokupl Kalibrasyonu',
                        'text' => 'Termometre, PT100 ve termokupl gibi sıcaklık ölçüm elemanları farklı sıcaklık aralıklarında ve farklı proses koşullarında kullanılabilir. Kalibrasyon sürecinde cihazın veya sensörün belirli ölçüm noktalarında referans değerlerle karşılaştırılması yapılır.',
                    ],
                    [
                        'title' => 'Sıcaklık ve Nem Kalibrasyonu',
                        'text' => 'Higrometre, bağıl nem ölçer ve sıcaklık-nem datalogger cihazlarında sıcaklık ve bağıl nem değerleri birlikte değerlendirilebilir. Özellikle depolama, soğuk zincir, laboratuvar ortam izleme ve kalite kontrol süreçlerinde sıcaklık ve nem ölçümlerinin güvenilirliği önemlidir.',
                    ],
                ],
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Cihaz Grupları',
                    'text' => 'Sıcaklık kalibrasyonu kapsamı, cihaz tipine, ölçüm aralığına ve kullanım amacına göre belirlenir. Sensörler, termometreler, göstergeler, kontrollü hacimler ve nem ölçüm cihazları farklı ölçüm noktalarıyla değerlendirilebilir.',
                    'headings' => ['Cihaz / ekipman grubu', 'Örnek kapsam', 'Açıklama'],
                    'rows' => [
                        ['Direnç ve ısıl çift sensörleri', '-80 °C / 1100 °C', 'Sensör tipine ve kullanım aralığına göre değerlendirilir.'],
                        ['Sıcaklık göstergeleri', 'Belgeyle doğrulanacak', 'Gösterge ve sensör kombinasyonuna göre planlanır.'],
                        ['Pirometre ve IR termometreler', '-20 °C < T < 100 °C', 'Temassız sıcaklık ölçüm cihazları için değerlendirilir.'],
                        ['Sıvılı cam termometreler', 'Belgeyle doğrulanacak', 'Laboratuvar termometreleri için uygundur.'],
                        ['Kontrollü hacimler', 'Belgeyle doğrulanacak', 'Etüv, inkübatör, sıvı banyo, otoklav ve fırın grupları için kullanılır.'],
                        ['Higrometre ve bağıl nem ölçerler', '20 °C ≤ T ≤ 26 °C, 20 %rh ≤ RH ≤ 90 %rh', 'Sıcaklık ve bağıl nem ölçümleri birlikte değerlendirilir.'],
                    ],
                ],
                'process' => [
                    'title' => 'Sıcaklık Kalibrasyonu Süreci Nasıl İlerler?',
                    'text' => 'Süreç, talep edilen cihaz veya sistem bilgisinin alınmasıyla başlar. Cihaz tipi, ölçüm aralığı, kullanım alanı ve gerekli ölçüm noktaları değerlendirilir. Referans ekipmanlarla karşılaştırma yapılır, sonuçlar teknik olarak incelenir ve raporlanır.',
                    'steps' => [
                        'Talep ve cihaz bilgisinin alınması',
                        'Cihaz kabulü veya yerinde değerlendirme',
                        'Ölçüm noktalarının belirlenmesi',
                        'Referans sistemlerle karşılaştırma',
                        'Sonuçların değerlendirilmesi',
                        'Raporlama ve teslim',
                    ],
                    'descriptions' => [
                        'Cihaz tipi, marka, model, ölçüm aralığı ve kullanım alanı bilgileri alınır.',
                        'Cihaz laboratuvarda veya yerinde ölçüm koşullarına göre değerlendirilir.',
                        'Sıcaklık noktaları ve kontrollü hacim ölçüm düzeni planlanır.',
                        'Referans sistemlerle karşılaştırma yapılarak ölçüm verileri alınır.',
                        'Sapma, stabilite ve kullanım amacına uygunluk teknik olarak incelenir.',
                        'Sonuçlar raporlanır ve kullanıcıya teslim süreci hakkında bilgi verilir.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Sıcaklık Kalibrasyonu Hangi Alanlarda Kullanılır?',
                        'text' => 'Sıcaklık kalibrasyonu; laboratuvar analizleri, kalite kontrol, üretim süreçleri, depolama koşulları, sterilizasyon, soğuk zincir, medikal cihaz kullanımı ve AR-GE çalışmalarında tercih edilir. Sıcaklık sapmaları ürün kalitesi, proses güvenliği ve analiz sonuçları üzerinde doğrudan etkili olabilir.',
                        'links' => [
                            ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                        ],
                    ],
                    [
                        'title' => 'İlgili Laboratuvar Cihazları',
                        'text' => 'Sıcaklık kalibrasyonu; etüv, nem tayin cihazı, pH metre, refraktometre ve sıcaklık kontrollü laboratuvar cihazlarıyla ilişkilendirilebilir. Yeni cihaz ihtiyacı olan kullanıcılar MTA Endüstri ürün kataloğunda ilgili kategori sayfalarını inceleyebilir.',
                        'links' => [
                            ['url' => route('products.category', 'etuv'), 'anchor' => 'etüv cihazı modelleri'],
                            ['url' => route('products.category', 'nem-tayin'), 'anchor' => 'nem tayin cihazı modelleri'],
                            ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre modelleri'],
                            ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre modelleri'],
                        ],
                    ],
                ],
                'cta' => [
                    'title' => 'Sıcaklık Kalibrasyonu İçin Teklif Alın',
                    'text' => 'Termometre, sıcaklık sensörü, PT100, termokupl, datalogger, etüv, inkübatör, otoklav veya sıcaklık-nem ölçüm cihazlarınız için kalibrasyon teklifi almak üzere MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, ölçüm aralığı ve adet bilgileriyle talebiniz değerlendirilir.',
                    'note' => 'Sıcaklık ölçüm cihazlarınız ve kontrollü hacimleriniz için kalibrasyon kapsamını netleştirelim.',
                    'button' => 'Sıcaklık Kalibrasyonu İçin Teklif Al',
                    'anchor' => 'sıcaklık kalibrasyonu teklif talebi',
                ],
                'related_products' => [
                    'title' => 'Etüv, nem tayin, pH metre ve refraktometre modelleri',
                    'button' => 'Etüv Cihazları',
                    'url' => route('products.category', 'etuv'),
                    'category_slugs' => ['etuv', 'nem-tayin', 'ph-metre', 'refraktometre'],
                ],
                'faq' => [
                    [
                        'question' => 'Sıcaklık kalibrasyonu ne sıklıkla yapılmalı?',
                        'answer' => 'Kalibrasyon periyodu cihazın kullanım yoğunluğu, ölçüm riski, kalite prosedürleri ve sektör gerekliliklerine göre belirlenmelidir. Kritik süreçlerde kullanılan cihazlar için düzenli kalibrasyon planı oluşturulması önerilir.',
                    ],
                    [
                        'question' => 'Termometre kalibrasyonu ile sıcaklık sensörü kalibrasyonu aynı mıdır?',
                        'answer' => 'Temel amaç benzer olsa da cihaz tipi, ölçüm aralığı, bağlantı yapısı ve kullanım koşulları farklı olabilir. Termometre, PT100 ve termokupl gibi ekipmanlar kendi teknik özelliklerine göre değerlendirilir.',
                    ],
                    [
                        'question' => 'Etüv kalibrasyonu neden önemlidir?',
                        'answer' => 'Etüvlerde sıcaklık dağılımı ve stabilite, numune kurutma ve sıcaklık kontrollü işlemlerin doğruluğunu etkiler. Bu nedenle belirli uygulamalarda etüv sıcaklık performansının doğrulanması önemlidir.',
                    ],
                    [
                        'question' => 'Datalogger kalibrasyonu yapılır mı?',
                        'answer' => 'Sıcaklık veya sıcaklık-nem datalogger cihazları, ölçüm aralığı ve kullanım amacına göre kalibrasyon kapsamında değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Sıcaklık kalibrasyonu için hangi bilgiler gerekir?',
                        'answer' => 'Cihaz tipi, marka, model, ölçüm aralığı, adet, kullanım alanı ve talep edilen ölçüm noktaları teklif süreci için gerekli başlangıç bilgilerini sağlar.',
                    ],
                ],
            ],
            'hacim-kalibrasyonu' => [
                'slug' => 'hacim-kalibrasyonu',
                'meta_title' => 'Hacim Kalibrasyonu ve Pipet Kalibrasyonu | MTA Endüstri',
                'meta_description' => 'Pipet, büret, balon joje, mezür, piknometre ve dispenser gibi laboratuvar hacim ekipmanları için hacim kalibrasyonu hizmeti alın.',
                'h1' => 'Hacim Kalibrasyonu ve Pipet Kalibrasyonu',
                'hero_text' => 'Hacim kalibrasyonu, hacim ölçen laboratuvar ekipmanlarının belirli hacim noktalarında referans yöntemlerle doğrulanması ve sonuçların raporlanması sürecidir. MTA Endüstri; pipet, büret, balon joje, mezür, piknometre, dispenser ve pistonlu hacim cihazları için kalibrasyon taleplerini teknik kapsam doğrultusunda değerlendirir.',
                'primary_cta' => 'Hacim Kalibrasyonu İçin Teklif Al',
                'secondary_cta' => 'Laboratuvar Cihazlarını İncele',
                'secondary_cta_url' => route('products.index'),
                'image_alt' => 'Pipet ve laboratuvar hacim ekipmanları için hacim kalibrasyonu hizmeti',
                'sections' => [
                    [
                        'title' => 'Hacim Kalibrasyonu Nedir?',
                        'text' => 'Hacim kalibrasyonu, hacim ölçen ekipmanın verdiği değerin güvenilir referans yöntemlerle karşılaştırılmasıdır. Bu süreç sonucunda ekipmanın doğruluğu, sapma durumu ve kullanım amacına uygunluğu değerlendirilir. Laboratuvar analizlerinde hacimsel ölçümlerin doğru yapılması, sonuç güvenilirliği açısından kritik öneme sahiptir.',
                    ],
                    [
                        'title' => 'Hangi Ekipmanlar İçin Hacim Kalibrasyonu Yapılır?',
                        'text' => 'Hacim kalibrasyonu kapsamında cam mezürler, pipetler, büretler, balon jojeler, piknometreler, pistonlu pipetler, pistonlu büretler, dispenserler ve plastik mezürler değerlendirilebilir. Ekipmanın hacim aralığı, kullanım amacı ve laboratuvar prosedürü kalibrasyon kapsamının belirlenmesinde dikkate alınır.',
                    ],
                    [
                        'title' => 'Pipet, Büret ve Balon Joje Kalibrasyonu',
                        'text' => 'Pipet, büret ve balon joje gibi laboratuvar cam malzemeleri, hacimsel analizlerde doğru miktarda sıvı aktarımı veya hazırlığı için kullanılır. Bu ekipmanların kalibrasyonu, özellikle titrasyon, çözelti hazırlama ve kalite kontrol analizlerinde ölçüm güvenilirliğini destekler.',
                    ],
                    [
                        'title' => 'Pistonlu Pipet ve Dispenser Kalibrasyonu',
                        'text' => 'Pistonlu pipet ve dispenserler, küçük hacimlerin tekrarlanabilir şekilde aktarılması gereken laboratuvar süreçlerinde kullanılır. Kullanım yoğunluğu, mekanik aşınma, conta yapısı ve operatör kullanımı hacim doğruluğunu etkileyebilir. Düzenli kontrol, hacimsel işlemlerin güvenilirliği açısından önemlidir.',
                    ],
                    [
                        'title' => 'Piknometre Kalibrasyonu ve Yoğunluk Ölçümleri',
                        'text' => 'Piknometreler yoğunluk ve özgül ağırlık ölçümlerinde kullanılan hacimsel ekipmanlardır. Piknometre kalibrasyonu, yoğunluk hesaplamalarında kullanılan hacim değerinin güvenilirliğini destekler. Bu nedenle densitometre ve yoğunluk ölçümü yapılan laboratuvar süreçleriyle doğal olarak ilişkilidir.',
                    ],
                ],
                'scope' => [
                    'title' => 'Ölçüm Kapsamı ve Ekipman Grupları',
                    'text' => 'Hacim kalibrasyonu kapsamı, ekipmanın nominal hacmine, kullanım şekline ve laboratuvar prosedürüne göre belirlenir. Cam hacim ekipmanları, pistonlu hacim cihazları ve dispenser grupları farklı ölçüm noktalarıyla değerlendirilebilir.',
                    'headings' => ['Ekipman grubu', 'Örnek kapsam', 'Açıklama'],
                    'rows' => [
                        ['Cam mezür', 'Belgeyle doğrulanacak', 'Cam hacim ölçüm ekipmanları için değerlendirilir.'],
                        ['Pipet', 'Belgeyle doğrulanacak', 'Hacim aktarımı ve analiz süreçlerinde kullanılır.'],
                        ['Büret', 'Belgeyle doğrulanacak', 'Titrasyon işlemleri için kullanılan hacim ekipmanıdır.'],
                        ['Balon joje', 'Belgeyle doğrulanacak', 'Çözelti hazırlama süreçlerinde değerlendirilir.'],
                        ['Piknometre', 'Belgeyle doğrulanacak', 'Yoğunluk ölçümlerinde kullanılan hacim ekipmanıdır.'],
                        ['Pistonlu pipet', 'Belgeyle doğrulanacak', 'Tekrarlanabilir küçük hacim aktarımı için kullanılır.'],
                        ['Dispenser', 'Belgeyle doğrulanacak', 'Rutin hacim aktarım işlemleri için değerlendirilir.'],
                    ],
                ],
                'process' => [
                    'title' => 'Hacim Kalibrasyonu Süreci Nasıl İlerler?',
                    'text' => 'Süreç, kalibrasyonu yapılacak ekipmanın tipi, nominal hacmi ve kullanım amacının belirlenmesiyle başlar. Ekipman kabulü ve ön kontrol sonrası uygun ölçüm noktaları planlanır. Referans yöntemlerle ölçüm yapılır, sonuçlar değerlendirilir ve raporlanır.',
                    'steps' => [
                        'Talep ve ekipman bilgisinin alınması',
                        'Ekipman kabulü ve ön kontrol',
                        'Ölçüm noktalarının belirlenmesi',
                        'Referans yöntemle hacim doğrulama',
                        'Sonuçların değerlendirilmesi',
                        'Raporlama ve teslim',
                    ],
                    'descriptions' => [
                        'Ekipman tipi, nominal hacim, adet ve kullanım alanı bilgileri alınır.',
                        'Pipet, büret, balon joje veya dispenser fiziksel olarak ön kontrolden geçirilir.',
                        'Nominal hacim ve kullanım prosedürüne göre ölçüm noktaları belirlenir.',
                        'Uygun referans yöntemle hacimsel doğrulama ölçümleri yapılır.',
                        'Sapma ve kullanım amacına uygunluk teknik ekip tarafından değerlendirilir.',
                        'Kalibrasyon sonucu raporlanır ve teslim bilgileri kullanıcıya paylaşılır.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Hacim Kalibrasyonu Hangi Alanlarda Kullanılır?',
                        'text' => 'Hacim kalibrasyonu; kimya, gıda, ilaç, çevre, akademik araştırma, kalite kontrol ve AR-GE laboratuvarlarında kullanılır. Titrasyon, çözelti hazırlama, yoğunluk ölçümü, numune seyreltme ve rutin analiz süreçlerinde hacimsel ekipmanların doğruluğu önemlidir.',
                        'links' => [
                            ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                            ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                        ],
                    ],
                    [
                        'title' => 'İlgili Laboratuvar Cihazları',
                        'text' => 'Hacim kalibrasyonu; Karl Fischer titratörleri, potansiyometrik titratörler, densitometreler ve laboratuvar ölçüm cihazlarıyla ilişkilendirilebilir. Yeni cihaz ihtiyacı olan kullanıcılar MTA Endüstri ürün kataloğunda ilgili kategori sayfalarını inceleyebilir.',
                        'links' => [
                            ['url' => route('products.category', 'kral-fischer'), 'anchor' => 'Karl Fischer titratör modelleri'],
                            ['url' => route('products.category', 'potansiyometrik-titratorler'), 'anchor' => 'potansiyometrik titratör modelleri'],
                            ['url' => route('products.category', 'densitometre'), 'anchor' => 'densitometre modelleri'],
                        ],
                    ],
                ],
                'cta' => [
                    'title' => 'Hacim Kalibrasyonu İçin Teklif Alın',
                    'text' => 'Pipet, büret, balon joje, mezür, piknometre, dispenser veya pistonlu hacim cihazlarınız için kalibrasyon teklifi almak üzere MTA Endüstri teknik ekibine ulaşabilirsiniz. Ekipman tipi, hacim aralığı, adet ve kullanım alanı bilgileriyle talebiniz değerlendirilir.',
                    'note' => 'Laboratuvar hacim ekipmanlarınız için kalibrasyon kapsamını netleştirelim.',
                    'button' => 'Hacim Kalibrasyonu İçin Teklif Al',
                    'anchor' => 'hacim kalibrasyonu teklif talebi',
                ],
                'related_products' => [
                    'title' => 'Karl Fischer, titratör ve densitometre modelleri',
                    'button' => 'Laboratuvar Cihazları',
                    'url' => route('products.index'),
                    'category_slugs' => ['kral-fischer', 'potansiyometrik-titratorler', 'densitometre'],
                ],
                'faq' => [
                    [
                        'question' => 'Hacim kalibrasyonu ne işe yarar?',
                        'answer' => 'Hacim kalibrasyonu, pipet, büret, balon joje, mezür, piknometre ve dispenser gibi ekipmanların hacim doğruluğunu değerlendirmek için yapılır.',
                    ],
                    [
                        'question' => 'Pipet kalibrasyonu ne sıklıkla yapılmalı?',
                        'answer' => 'Periyot; kullanım yoğunluğu, laboratuvar prosedürü, kalite sistemi ve ölçüm riskine göre belirlenmelidir. Yoğun kullanılan pipetlerde düzenli kontrol planı oluşturulması önerilir.',
                    ],
                    [
                        'question' => 'Büret ve balon joje kalibrasyonu neden önemlidir?',
                        'answer' => 'Büret ve balon joje gibi ekipmanlar titrasyon ve çözelti hazırlama işlemlerinde kullanılır. Hacim sapmaları analiz sonucunu doğrudan etkileyebilir.',
                    ],
                    [
                        'question' => 'Dispenser kalibrasyonu yapılır mı?',
                        'answer' => 'Evet, dispenserler hacim aktarım doğruluğu açısından kalibrasyon kapsamında değerlendirilebilir. Ekipmanın hacim aralığı ve kullanım amacı dikkate alınır.',
                    ],
                    [
                        'question' => 'Hacim kalibrasyonu için hangi bilgiler gerekir?',
                        'answer' => 'Ekipman tipi, nominal hacim, adet, kullanım alanı ve varsa özel ölçüm noktaları teklif süreci için gerekli başlangıç bilgilerini sağlar.',
                    ],
                ],
            ],
            default => [],
        };
    }

    private function technicalServiceSeoContent(string $slug): array
    {
        return match ($slug) {
            'laboratuvar-cihazlari-icin-teknik-servis' => [
                'slug' => 'laboratuvar-cihazlari-icin-teknik-servis',
                'meta_title' => 'Laboratuvar Cihazları Teknik Servis ve Bakım | MTA Endüstri',
                'meta_description' => 'Laboratuvar cihazları için arıza tespiti, bakım, onarım, yedek parça değerlendirmesi ve teknik servis desteği alın.',
                'h1' => 'Laboratuvar Cihazları Teknik Servis ve Bakım',
                'hero_text' => 'Laboratuvar cihazları teknik servis hizmeti; analiz, ölçüm, ısıtma, karıştırma ve numune hazırlama cihazlarında arıza tespiti, bakım, onarım ve performans kontrolü süreçlerini kapsar. MTA Endüstri; laboratuvar cihazlarında servis ihtiyacını cihaz tipi, kullanım alanı ve teknik belirtiler doğrultusunda değerlendirir.',
                'primary_cta' => 'Laboratuvar Cihazı Servis Talebi Oluştur',
                'secondary_cta' => 'Kalibrasyon Hizmetlerini İncele',
                'secondary_cta_url' => route('services.index'),
                'image_alt' => 'Laboratuvar cihazları teknik servis ve bakım uygulaması',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Cihazları Teknik Servis Nedir?',
                        'text' => 'Laboratuvar cihazları teknik servis, cihazlarda oluşan arıza, performans düşüşü, ölçüm stabilitesi problemi, mekanik veya elektronik sorunların değerlendirilmesi sürecidir. Amaç, cihazın güvenilir çalışmasını sağlamak, kullanım kesintisini azaltmak ve gerektiğinde kalibrasyon öncesi teknik uygunluğunu değerlendirmektir.',
                    ],
                    [
                        'title' => 'Hangi Laboratuvar Cihazları Değerlendirilir?',
                        'text' => 'Teknik servis kapsamında ölçüm cihazları, analiz cihazları, sıcaklık kontrollü cihazlar, karıştırıcılar, numune hazırlama cihazları ve tartım ekipmanları değerlendirilebilir. Cihaz tipi, marka, model, kullanım yoğunluğu ve arıza belirtisi servis kapsamının belirlenmesinde dikkate alınır.',
                    ],
                    [
                        'title' => 'Kalibrasyon Öncesi Teknik Hazırlık',
                        'text' => 'Kalibrasyon yapılacak cihazın mekanik, elektronik ve ölçümsel açıdan stabil olması gerekir. Cihazda arıza, ölçüm sapması, sensör problemi veya tekrarlanabilirlik sorunu varsa kalibrasyon öncesinde teknik servis değerlendirmesi gerekebilir. Bu yaklaşım, kalibrasyon sonucunun daha doğru yorumlanmasına yardımcı olur.',
                    ],
                    [
                        'title' => 'Laboratuvar Cihazı Bakım ve Onarım Süreci',
                        'text' => 'Servis süreci, cihaz ve arıza bilgisinin alınmasıyla başlar. Ön değerlendirme sonrasında cihazın fiziksel durumu, elektronik bileşenleri, sensörleri, bağlantıları ve performansı incelenir. Gerekli bakım, onarım veya parça değerlendirmesi belirlendikten sonra kullanıcıya teknik değerlendirme ve teklif bilgisi iletilir.',
                    ],
                ],
                'device_list' => [
                    'title' => 'Teknik Servis Kapsamındaki Cihaz Grupları',
                    'text' => 'Laboratuvar cihazları teknik servis kapsamında analiz, ölçüm, sıcaklık kontrol, numune hazırlama ve tartım cihazları marka, model ve arıza belirtisine göre değerlendirilebilir.',
                    'items' => [
                        'Etüv ve sıcaklık kontrollü cihazlar',
                        'Nem tayin cihazları',
                        'pH metre ve iletkenlik ölçerler',
                        'Refraktometre ve densitometreler',
                        'Viskozimetreler',
                        'Manyetik karıştırıcılar',
                        'Mekanik karıştırıcılar',
                        'Homojenizatörler',
                        'Titratörler',
                        'Laboratuvar terazileri',
                    ],
                ],
                'faults' => [
                    'title' => 'Sık Görülen Laboratuvar Cihazı Arızaları',
                    'text' => 'Laboratuvar cihazlarında ekran hatası, sensör arızası, ısıtma problemi, motor performans düşüşü, elektronik kart sorunu, bağlantı hatası, ölçüm stabilitesi problemi veya mekanik hasar görülebilir. Bu belirtiler cihazın kullanım güvenilirliğini ve analiz sonuçlarını etkileyebilir.',
                    'items' => [
                        'Cihazın açılmaması',
                        'Ekran veya kontrol paneli hatası',
                        'Sensör veya prob arızası',
                        'Isıtma performansının düşmesi',
                        'Motor veya karıştırma hızında problem',
                        'Ölçüm değerinin stabil olmaması',
                        'Elektronik kart veya güç bağlantısı sorunu',
                        'Mekanik hasar veya bağlantı problemi',
                        'Kalibrasyon kabul etmeme',
                        'Tekrarlanabilir sonuç alınamaması',
                    ],
                ],
                'process' => [
                    'title' => 'Laboratuvar Cihazı Bakım ve Onarım Süreci',
                    'text' => 'Servis süreci, cihaz ve arıza bilgisinin alınmasıyla başlar. Ön değerlendirme sonrasında cihazın fiziksel durumu, elektronik bileşenleri, sensörleri, bağlantıları ve performansı incelenir. Gerekli bakım, onarım veya parça değerlendirmesi belirlendikten sonra kullanıcıya teknik değerlendirme ve teklif bilgisi iletilir.',
                    'steps' => [
                        'Servis talebi ve cihaz bilgisinin alınması',
                        'Arıza belirtisinin değerlendirilmesi',
                        'Cihaz kabulü veya yerinde ön kontrol',
                        'Teknik inceleme ve arıza tespiti',
                        'Bakım, onarım veya parça değerlendirmesi',
                        'Servis sonrası performans kontrolü',
                        'Gerekirse kalibrasyon yönlendirmesi',
                    ],
                    'descriptions' => [
                        'Cihaz tipi, marka, model, kullanım alanı ve adet bilgileri alınır.',
                        'Belirti, kullanım yoğunluğu, hata kodu ve performans düşüşü değerlendirilir.',
                        'Cihazın yerinde mi yoksa servis ortamında mı inceleneceği belirlenir.',
                        'Mekanik, elektronik, sensör, bağlantı ve performans kontrolleri yapılır.',
                        'Gerekli bakım, onarım veya yedek parça ihtiyacı kullanıcıyla paylaşılır.',
                        'Servis sonrası cihazın çalışma davranışı ve ölçüm stabilitesi kontrol edilir.',
                        'Cihaz uygunsa ilgili kalibrasyon hizmetine yönlendirme yapılır.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Sıcaklık Kontrollü Laboratuvar Cihazları İçin Servis',
                        'text' => 'Etüv, inkübatör, otoklav, termoreaktör ve benzeri sıcaklık kontrollü cihazlarda ısıtma performansı, sıcaklık sensörü, kontrol paneli, fan ve kabin içi sıcaklık davranışı önemlidir. Isıtma problemi veya sıcaklık dalgalanması görüldüğünde teknik servis değerlendirmesi yapılmalıdır.',
                        'links' => [
                            ['url' => route('products.category', 'etuv'), 'anchor' => 'etüv cihazı modelleri'],
                            ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                        ],
                    ],
                    [
                        'title' => 'Karıştırıcı ve Numune Hazırlama Cihazları İçin Servis',
                        'text' => 'Manyetik karıştırıcı, mekanik karıştırıcı ve homojenizatörlerde motor performansı, hız kontrolü, tabla, şaft, prob, bağlantı ve elektronik kontrol sistemi servis açısından değerlendirilir. Hızın stabil olmaması veya cihazın çalışmaması numune hazırlama sürecini doğrudan etkileyebilir.',
                        'links' => [
                            ['url' => route('products.category', 'manyetik-karistirici'), 'anchor' => 'manyetik karıştırıcı modelleri'],
                            ['url' => route('products.category', 'homojenizator'), 'anchor' => 'homojenizatör modelleri'],
                            ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                        ],
                    ],
                    [
                        'title' => 'Ölçüm Cihazlarında Stabilite ve Prob Kontrolü',
                        'text' => 'pH metre, iletkenlik ölçer, refraktometre, densitometre ve viskozimetre gibi ölçüm cihazlarında prob, sensör, ölçüm hücresi, ekran ve elektronik sistemler ölçüm güvenilirliğini etkiler. Stabil sonuç alınamaması veya cihazın kalibrasyon kabul etmemesi servis ihtiyacına işaret edebilir.',
                        'links' => [
                            ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                            ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre modelleri'],
                            ['url' => route('products.category', 'nem-tayin'), 'anchor' => 'nem tayin cihazı modelleri'],
                        ],
                    ],
                    [
                        'title' => 'İlgili Ürün ve Kalibrasyon Sayfaları',
                        'text' => 'Laboratuvar cihazları teknik servis sayfası, ürün kategori sayfaları ve kalibrasyon hizmetleriyle birlikte çalışır. Kullanıcılar mevcut cihazları için servis talebi oluşturabilir veya yeni cihaz ihtiyacı varsa ürün katalog sayfalarını inceleyebilir.',
                        'links' => [
                            ['url' => route('technical-services.show', 'terazi-teknik-servis'), 'anchor' => 'terazi teknik servis'],
                            ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre modelleri'],
                            ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre modelleri'],
                        ],
                    ],
                ],
                'cta' => [
                    'title' => 'Laboratuvar Cihazları Teknik Servis İçin Teklif Alın',
                    'text' => 'Laboratuvar cihazınızda arıza, performans düşüşü, ölçüm stabilitesi problemi veya bakım ihtiyacı varsa MTA Endüstri teknik ekibine ulaşabilirsiniz. Marka, model, cihaz tipi, arıza belirtisi, kullanım alanı ve adet bilgileriyle servis değerlendirme süreci başlatılır.',
                    'note' => 'Laboratuvar cihazınızdaki arıza veya bakım ihtiyacını teknik ekiple paylaşın; servis sürecini birlikte netleştirelim.',
                    'button' => 'Laboratuvar Cihazı Servis Talebi Oluştur',
                    'anchor' => 'laboratuvar cihazları servis talebi',
                ],
                'related_products' => [
                    'title' => 'Laboratuvar cihazları ve ölçüm ekipmanları',
                    'button' => 'Ürün Kataloğunu İncele',
                    'url' => route('products.index'),
                    'category_slugs' => ['etuv', 'nem-tayin', 'ph-metre', 'manyetik-karistirici', 'homojenizator', 'viskozimetre'],
                ],
                'faq' => [
                    [
                        'question' => 'Laboratuvar cihazları teknik servis ne zaman gerekir?',
                        'answer' => 'Cihaz çalışmıyorsa, hata veriyorsa, ölçüm değeri stabil değilse, ısıtma veya motor performansı düşmüşse, sensör ya da ekran sorunu varsa teknik servis değerlendirmesi gerekir.',
                    ],
                    [
                        'question' => 'Kalibrasyon öncesi teknik servis gerekir mi?',
                        'answer' => 'Cihaz stabil değilse, arızalıysa veya tekrarlanabilir sonuç vermiyorsa kalibrasyon öncesinde teknik servis değerlendirmesi yapılması gerekebilir.',
                    ],
                    [
                        'question' => 'Servis talebi için hangi bilgiler gerekir?',
                        'answer' => 'Cihaz tipi, marka, model, arıza belirtisi, kullanım alanı, adet ve varsa mevcut hata kodu servis değerlendirmesi için yeterli başlangıç bilgilerini sağlar.',
                    ],
                    [
                        'question' => 'Laboratuvar cihazı bakım süreci nasıl ilerler?',
                        'answer' => 'Talep alınır, arıza belirtisi değerlendirilir, cihaz teknik incelemeye alınır, bakım veya onarım ihtiyacı belirlenir ve kullanıcıya teklif bilgisi iletilir.',
                    ],
                ],
            ],
            'analiz-ve-olcum-cihazlari-teknik-servis' => [
                'slug' => 'analiz-ve-olcum-cihazlari-teknik-servis',
                'meta_title' => 'Analiz ve Ölçüm Cihazları Teknik Servis | MTA Endüstri',
                'meta_description' => 'Analiz ve ölçüm cihazları için arıza tespiti, bakım, onarım, prob/sensör kontrolü ve teknik servis desteği alın.',
                'h1' => 'Analiz ve Ölçüm Cihazları Teknik Servis',
                'hero_text' => 'Analiz ve ölçüm cihazları teknik servis hizmeti; laboratuvarlarda kullanılan pH metre, iletkenlik ölçer, refraktometre, densitometre, viskozimetre, titratör ve benzeri cihazlarda arıza tespiti, bakım, onarım ve performans değerlendirme süreçlerini kapsar. MTA Endüstri, ölçüm güvenilirliğini etkileyen teknik sorunları cihaz tipi ve kullanım alanına göre değerlendirir.',
                'primary_cta' => 'Analiz Cihazı Servis Talebi Oluştur',
                'secondary_cta' => 'Ölçüm Cihazlarını İncele',
                'secondary_cta_url' => route('products.category', 'ph-iletkenlik'),
                'image_alt' => 'Analiz ve ölçüm cihazları için teknik servis uygulaması',
                'sections' => [
                    [
                        'title' => 'Analiz Cihazları Teknik Servis Nedir?',
                        'text' => 'Analiz cihazları teknik servis, laboratuvar ölçüm ve analiz cihazlarında oluşan arıza, performans düşüşü, ölçüm sapması veya kullanım kaynaklı sorunların teknik olarak değerlendirilmesidir. Bu süreçte cihazın ölçüm stabilitesi, sensör/prob durumu, elektronik kontrol sistemi, ekran, bağlantılar ve uygulama performansı incelenir.',
                    ],
                    [
                        'title' => 'Hangi Analiz ve Ölçüm Cihazları Değerlendirilir?',
                        'text' => 'Teknik servis kapsamında laboratuvarlarda kullanılan farklı analiz ve ölçüm cihazları değerlendirilebilir. Cihazın marka, model, kullanım alanı, arıza belirtisi ve ölçüm parametreleri servis kapsamının belirlenmesinde dikkate alınır.',
                    ],
                    [
                        'title' => 'Ölçüm Stabilitesi ve Sapma Problemleri',
                        'text' => 'Analiz ve ölçüm cihazlarında en kritik sorunlardan biri ölçüm değerinin stabil olmaması veya beklenen değerden sapmasıdır. Bu durum prob, sensör, elektrot, sıcaklık kompanzasyonu, ölçüm hücresi, yazılım ayarı, elektronik kart veya kullanıcı prosedürlerinden kaynaklanabilir.',
                    ],
                    [
                        'title' => 'Prob, Sensör ve Elektrot Kontrolleri',
                        'text' => 'pH metre, iletkenlik ölçer, titratör ve benzeri cihazlarda prob, sensör ve elektrot yapısı ölçüm sonucunu doğrudan etkiler. Elektrot yaşlanması, kirlenme, yanlış saklama, kablo hasarı veya bağlantı problemi ölçüm hatalarına neden olabilir.',
                    ],
                ],
                'device_list' => [
                    'title' => 'Analiz ve Ölçüm Cihazı Grupları',
                    'text' => 'Analiz ve ölçüm cihazları teknik servis kapsamında prob, sensör, elektrot, ölçüm hücresi, motor, pompa, büret ve elektronik kontrol bileşenleri cihaz tipine göre değerlendirilir.',
                    'items' => [
                        'pH metre',
                        'pH ve iletkenlik ölçer',
                        'İletkenlik ölçer',
                        'Refraktometre',
                        'Densitometre',
                        'Viskozimetre',
                        'Karl Fischer titratör',
                        'Potansiyometrik titratör',
                        'Nem tayin cihazı',
                        'Laboratuvar ölçüm cihazları',
                    ],
                ],
                'faults' => [
                    'title' => 'Sık Görülen Analiz Cihazı Arızaları',
                    'text' => 'Analiz ve ölçüm cihazlarında görülen arızalar cihaz tipine göre değişir. Ancak ölçüm değerinin stabil olmaması, cihazın kalibrasyon kabul etmemesi, sensör veya prob hatası, ekran arızası, yazılım problemi ve elektronik kart sorunları sık karşılaşılan belirtiler arasındadır.',
                    'items' => [
                        'Ölçüm değerinin sürekli değişmesi',
                        'Kalibrasyon kabul etmeme',
                        'Prob veya elektrot algılama sorunu',
                        'Sıcaklık sensörü hatası',
                        'Ekran veya kontrol paneli arızası',
                        'Yazılım veya metot hatası',
                        'Numune hücresi veya optik sistem problemi',
                        'Pompa, büret veya dozaj sistemi sorunu',
                        'Motor veya hız kontrol problemi',
                        'Elektronik kart ya da güç bağlantısı arızası',
                    ],
                ],
                'process' => [
                    'title' => 'Teknik Servis Süreci Nasıl İlerler?',
                    'text' => 'Servis süreci, cihaz tipi ve arıza belirtisinin alınmasıyla başlar. Ön değerlendirme sonrası cihazın fiziksel durumu, ölçüm bileşenleri, sensörleri, bağlantıları ve performansı incelenir. Gerekli bakım, onarım, parça değerlendirmesi veya kalibrasyon yönlendirmesi kullanıcıya raporlanır.',
                    'steps' => [
                        'Servis talebi ve cihaz bilgisinin alınması',
                        'Arıza belirtisinin değerlendirilmesi',
                        'Cihaz kabulü veya yerinde ön kontrol',
                        'Prob, sensör ve ölçüm bileşenlerinin incelenmesi',
                        'Teknik arıza tespiti',
                        'Bakım, onarım veya parça değerlendirmesi',
                        'Servis sonrası performans kontrolü',
                        'Gerekirse kalibrasyon yönlendirmesi',
                    ],
                    'descriptions' => [
                        'Cihaz tipi, marka, model, ölçüm parametresi ve kullanım alanı bilgileri alınır.',
                        'Belirti, hata kodu, stabilite sorunu ve uygulama davranışı ön değerlendirmeye alınır.',
                        'Cihazın yerinde mi yoksa servis ortamında mı inceleneceği belirlenir.',
                        'Prob, sensör, elektrot, ölçüm hücresi, bağlantı ve elektronik bileşenler incelenir.',
                        'Arıza kaynağı cihaz tipi ve uygulama koşullarına göre değerlendirilir.',
                        'Gerekli bakım, onarım veya yedek parça ihtiyacı kullanıcıyla paylaşılır.',
                        'Servis sonrası cihazın ölçüm davranışı ve stabilitesi kontrol edilir.',
                        'Cihaz uygunsa ilgili sıcaklık, hacim, devir veya diğer kalibrasyon süreçlerine yönlendirilir.',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'pH Metre ve İletkenlik Ölçer Teknik Servis',
                        'text' => 'pH metre ve iletkenlik ölçerlerde kalibrasyon kabul etmeme, ölçüm değerinin dalgalanması, sıcaklık sensörü hatası, prob algılama problemi, ekran veya bağlantı sorunu görülebilir. Bu cihazlarda doğru prob seçimi, düzenli bakım ve teknik kontrol ölçüm güvenilirliği açısından önemlidir.',
                        'links' => [
                            ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre modelleri'],
                            ['url' => route('products.category', 'ph-iletkenlik'), 'anchor' => 'pH ve iletkenlik ölçer cihazları'],
                        ],
                    ],
                    [
                        'title' => 'Refraktometre ve Densitometre Teknik Servis',
                        'text' => 'Refraktometre ve densitometrelerde numune hücresi, optik sistem, sıcaklık sensörü, ekran, yazılım veya elektronik kontrol kaynaklı sorunlar ölçüm sonucunu etkileyebilir. Ölçüm tekrarlanabilirliğinin bozulması, cihazın hata vermesi veya sıcaklık kontrolünün doğru çalışmaması teknik servis ihtiyacına işaret edebilir.',
                        'links' => [
                            ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre modelleri'],
                            ['url' => route('products.category', 'densitometre'), 'anchor' => 'densitometre modelleri'],
                            ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                        ],
                    ],
                    [
                        'title' => 'Viskozimetre ve Titratör Teknik Servis',
                        'text' => 'Viskozimetrelerde motor, spindle bağlantısı, hız kontrolü ve sıcaklık probu; titratörlerde ise büret, pompa, elektrot, dozaj sistemi ve yazılım ayarları analiz performansı açısından önemlidir. Bu bileşenlerde oluşan sorunlar analiz sonucunun güvenilirliğini azaltabilir.',
                        'links' => [
                            ['url' => route('products.category', 'viskozimetre'), 'anchor' => 'viskozimetre modelleri'],
                            ['url' => route('products.category', 'kral-fischer'), 'anchor' => 'Karl Fischer titratör modelleri'],
                            ['url' => route('products.category', 'potansiyometrik-titratorler'), 'anchor' => 'potansiyometrik titratör modelleri'],
                        ],
                    ],
                    [
                        'title' => 'Kalibrasyon Öncesi Teknik Hazırlık',
                        'text' => 'Analiz ve ölçüm cihazlarında kalibrasyon öncesinde cihazın stabil çalışması gerekir. Sensör, prob, elektrot, ölçüm hücresi veya elektronik bileşenlerde sorun varsa önce teknik servis değerlendirmesi yapılmalıdır. Servis sonrası cihazın ölçüm performansı kontrol edilerek ilgili kalibrasyon hizmeti planlanabilir.',
                        'links' => [
                            ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                            ['url' => route('services.show', 'hacim-kalibrasyonu'), 'anchor' => 'hacim kalibrasyonu'],
                            ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                        ],
                    ],
                ],
                'cta' => [
                    'title' => 'Analiz ve Ölçüm Cihazları İçin Servis Talebi Oluşturun',
                    'text' => 'Laboratuvar analiz veya ölçüm cihazınızda arıza, ölçüm sapması, prob/sensör sorunu, stabilite problemi veya bakım ihtiyacı varsa MTA Endüstri teknik ekibine ulaşabilirsiniz. Cihaz tipi, marka, model, ölçüm parametresi, arıza belirtisi ve kullanım alanı bilgileriyle servis değerlendirme süreci başlatılır.',
                    'note' => 'Analiz veya ölçüm cihazınızdaki teknik sorunu paylaşın; servis kapsamını birlikte netleştirelim.',
                    'button' => 'Analiz Cihazı Servis Talebi Oluştur',
                    'anchor' => 'analiz cihazı servis talebi',
                ],
                'related_products' => [
                    'title' => 'Analiz ve ölçüm cihazı kategorileri',
                    'button' => 'Ölçüm Cihazlarını İncele',
                    'url' => route('products.category', 'ph-iletkenlik'),
                    'category_slugs' => ['ph-metre', 'ph-iletkenlik', 'refraktometre', 'densitometre', 'viskozimetre', 'kral-fischer', 'potansiyometrik-titratorler'],
                ],
                'faq' => [
                    [
                        'question' => 'Analiz cihazları teknik servis ne zaman gerekir?',
                        'answer' => 'Cihaz stabil ölçüm yapmıyorsa, kalibrasyon kabul etmiyorsa, hata veriyorsa, prob veya sensör sorunu oluşuyorsa, ekran ya da elektronik bileşen problemi görülüyorsa teknik servis değerlendirmesi gerekir.',
                    ],
                    [
                        'question' => 'Ölçüm cihazlarında prob ve sensör kontrolü neden önemlidir?',
                        'answer' => 'Prob, sensör ve elektrotlar ölçüm sonucunu doğrudan etkiler. Kirlenme, yaşlanma, bağlantı sorunu veya yanlış saklama ölçüm sapmasına neden olabilir.',
                    ],
                    [
                        'question' => 'Servis sonrası kalibrasyon gerekir mi?',
                        'answer' => 'Bakım veya onarım sonrası cihazın ölçüm performansının doğrulanması gerekebilir. Bu durumda cihaz tipine göre sıcaklık, hacim, devir veya ilgili kalibrasyon süreçleri değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Analiz cihazı servis talebi için hangi bilgiler gerekir?',
                        'answer' => 'Cihaz tipi, marka, model, ölçüm parametresi, arıza belirtisi, kullanım alanı, adet ve varsa hata kodu servis değerlendirmesi için gerekli başlangıç bilgilerini sağlar.',
                    ],
                    [
                        'question' => 'pH metre veya iletkenlik ölçer kalibrasyon kabul etmiyorsa ne yapılmalı?',
                        'answer' => 'Önce prob, elektrot, sensör, bağlantı ve cihaz ayarları teknik olarak değerlendirilmelidir. Cihaz stabil hale geldikten sonra kalibrasyon süreci yeniden ele alınabilir.',
                    ],
                ],
            ],
            'terazi-teknik-servis' => [
                'slug' => 'terazi-teknik-servis',
                'meta_title' => 'Terazi Teknik Servis ve Terazi Tamiri | MTA Endüstri',
                'meta_description' => 'Hassas terazi, analitik terazi ve laboratuvar terazileri için arıza tespiti, bakım, onarım ve teknik servis desteği alın.',
                'h1' => 'Terazi Teknik Servis ve Terazi Tamiri',
                'hero_text' => 'Terazi teknik servis hizmeti; hassas terazi, analitik terazi, laboratuvar terazisi ve endüstriyel tartım cihazlarında arıza tespiti, bakım, onarım ve ölçüm stabilitesi değerlendirmesi için uygulanır. MTA Endüstri, tartım cihazlarında servis ihtiyacını teknik olarak değerlendirir ve gerektiğinde kalibrasyon süreciyle birlikte ele alır.',
                'primary_cta' => 'Terazi Servis Talebi Oluştur',
                'secondary_cta' => 'Terazi Kalibrasyonunu İncele',
                'image_alt' => 'Hassas terazi ve analitik terazi teknik servis uygulaması',
                'sections' => [
                    [
                        'title' => 'Terazi Teknik Servis Hizmeti Nedir?',
                        'text' => 'Terazi teknik servis hizmeti, tartım cihazlarında oluşan arıza, sapma, stabilite problemi veya kullanım kaynaklı performans düşüşlerinin değerlendirilmesini kapsar. Cihazın marka, model, kapasite, okunabilirlik ve kullanım alanı bilgileri incelenerek servis ihtiyacı belirlenir. Amaç, tartım cihazının güvenilir ve tekrarlanabilir ölçüm yapabilecek duruma getirilmesidir.',
                    ],
                    [
                        'title' => 'Hangi Teraziler İçin Servis Desteği Verilir?',
                        'text' => 'Terazi teknik servis kapsamında hassas teraziler, analitik teraziler, laboratuvar terazileri, endüstriyel teraziler ve otomatik ağırlık kontrol terazileri değerlendirilebilir. Cihaz tipi ve arıza durumuna göre yerinde kontrol, laboratuvar ortamında servis veya detaylı teknik inceleme planlanabilir.',
                    ],
                    [
                        'title' => 'Hassas Terazi ve Analitik Terazi Arızaları',
                        'text' => 'Hassas terazi ve analitik teraziler, düşük ağırlık farklarını ölçtüğü için çevresel koşullardan, mekanik darbelerden, elektronik bileşen sorunlarından ve düzenli bakım eksikliğinden etkilenebilir. Ölçüm sonucunun sürekli değişmesi, cihazın sıfıra dönmemesi, ekran hataları, kalibrasyon hataları veya tekrarlanabilirlik problemleri teknik servis ihtiyacına işaret edebilir.',
                    ],
                ],
                'faults' => [
                    'title' => 'Sık Görülen Terazi Arızaları',
                    'text' => 'Terazilerde en sık karşılaşılan sorunlar arasında ölçüm stabilitesinin bozulması, cihazın açılmaması, ekran veya tuş takımı arızaları, ağırlık değerinin sapması, mekanik hasar, güç adaptörü sorunları ve kalibrasyon hataları yer alır. Bu belirtiler görüldüğünde cihazın kullanımına devam etmek ölçüm güvenilirliğini olumsuz etkileyebilir.',
                    'items' => [
                        'Tartım değerinin sürekli değişmesi',
                        'Terazinin sıfıra dönmemesi',
                        'Ekran veya tuş takımı arızası',
                        'Cihazın açılmaması',
                        'Ağırlık değerinde sapma',
                        'Mekanik darbe veya tabla problemi',
                        'Kalibrasyon hatası',
                        'Tekrarlanabilirlik sorunu',
                        'Güç adaptörü veya bağlantı problemi',
                        'Ortam koşullarına bağlı stabilite problemi',
                    ],
                ],
                'process' => [
                    'title' => 'Terazi Bakım ve Onarım Süreci Nasıl İlerler?',
                    'text' => 'Servis süreci, cihaz ve arıza bilgisinin alınmasıyla başlar. Ön değerlendirme sonrası cihazın fiziksel durumu, elektronik bileşenleri, sensör yapısı, ekranı, bağlantıları ve tartım performansı kontrol edilir. Gerekli bakım veya onarım adımları belirlendikten sonra kullanıcıya teknik değerlendirme ve teklif bilgisi iletilir.',
                    'steps' => [
                        'Servis talebi ve cihaz bilgisinin alınması',
                        'Arıza belirtisinin değerlendirilmesi',
                        'Cihaz kabulü veya yerinde ön kontrol',
                        'Teknik inceleme ve arıza tespiti',
                        'Bakım, onarım veya parça değerlendirmesi',
                        'Servis sonrası performans kontrolü',
                        'Gerekirse kalibrasyon yönlendirmesi',
                    ],
                ],
                'support_sections' => [
                    [
                        'title' => 'Kalibrasyon Öncesi Terazi Servisi',
                        'text' => 'Terazide mekanik hasar, stabilite problemi veya ölçüm sapması varsa doğrudan kalibrasyon yapılması uygun olmayabilir. Bu durumda cihazın önce teknik servis sürecinden geçirilmesi gerekir. Servis sonrası tartım performansı kontrol edilir ve uygun görülürse terazi kalibrasyonu için yönlendirme yapılır.',
                    ],
                    [
                        'title' => 'Terazi Kalibrasyonu ile Teknik Servis Arasındaki Fark',
                        'text' => 'Terazi teknik servis, cihazdaki arıza, bakım veya performans problemlerini gidermeye odaklanır. Terazi kalibrasyonu ise cihazın ölçüm değerinin referans kütlelerle karşılaştırılması ve sonuçların raporlanmasıdır. Arızalı veya stabil olmayan bir cihazda önce servis, ardından kalibrasyon süreci değerlendirilmelidir.',
                    ],
                    [
                        'title' => 'Yeni Hassas Terazi İhtiyacı Olan Kullanıcılar İçin',
                        'text' => 'Mevcut cihazın servis maliyeti, kullanım yaşı veya teknik durumu yeni cihaz ihtiyacını gündeme getirebilir. Bu durumda MTA Endüstri ürün kataloğunda A&D, Ohaus, Shimadzu ve Weightlab markalarına ait hassas terazi, analitik terazi ve laboratuvar terazisi modelleri incelenebilir.',
                    ],
                ],
                'cta' => [
                    'title' => 'Terazi Teknik Servis İçin Teklif Alın',
                    'text' => 'Hassas terazi, analitik terazi veya laboratuvar terazinizde arıza, sapma, stabilite problemi ya da bakım ihtiyacı varsa MTA Endüstri teknik ekibine ulaşabilirsiniz. Marka, model, kapasite, arıza belirtisi ve kullanım alanı bilgileriyle servis değerlendirme süreci başlatılır.',
                    'note' => 'Tartım cihazınızdaki arıza veya bakım ihtiyacını teknik ekiple paylaşın; servis sürecini birlikte netleştirelim.',
                    'button' => 'Terazi Servis Talebi Oluştur',
                ],
                'faq' => [
                    [
                        'question' => 'Terazi teknik servis ne zaman gerekir?',
                        'answer' => 'Tartım değeri sürekli değişiyorsa, cihaz sıfıra dönmüyorsa, ekran hatası veriyorsa, kalibrasyon hatası oluşuyorsa veya ölçüm sonucunda sapma gözleniyorsa teknik servis değerlendirmesi gerekir.',
                    ],
                    [
                        'question' => 'Hassas terazi tamiri yapılmadan kalibrasyon yapılabilir mi?',
                        'answer' => 'Cihaz stabil değilse, mekanik hasar varsa veya tekrarlanabilir ölçüm alınamıyorsa önce teknik servis değerlendirmesi yapılmalıdır. Uygun görülürse servis sonrası kalibrasyon süreci planlanır.',
                    ],
                    [
                        'question' => 'Terazi servis talebi için hangi bilgiler gerekir?',
                        'answer' => 'Marka, model, kapasite, okunabilirlik, arıza belirtisi, cihaz adedi ve kullanım alanı bilgileri servis değerlendirmesi için yeterli başlangıç bilgilerini sağlar.',
                    ],
                    [
                        'question' => 'Terazi bakım süreci ne kadar sürer?',
                        'answer' => 'Süre; cihazın arıza durumuna, parça ihtiyacına, servis yoğunluğuna ve yapılacak işlemlere göre değişir. Ön değerlendirme sonrasında kullanıcıya süreç hakkında bilgi verilir.',
                    ],
                    [
                        'question' => 'Servis sonrası terazi kalibrasyonu gerekir mi?',
                        'answer' => 'Bakım veya onarım sonrasında cihazın ölçüm performansının doğrulanması için terazi kalibrasyonu önerilebilir. Bu karar cihazın kullanım amacı ve kalite prosedürlerine göre değerlendirilmelidir.',
                    ],
                ],
            ],
            default => [],
        };
    }

    private function productCategorySeoContent(string $slug): array
    {
        return match ($slug) {
            'teraziler' => [
                'slug' => 'teraziler',
                'meta_title' => 'Hassas Terazi ve Analitik Terazi Modelleri | MTA Endüstri',
                'meta_description' => 'Hassas terazi, analitik terazi ve laboratuvar terazisi modellerini A&D, Ohaus, Shimadzu ve Weightlab markalarıyla inceleyin; teklif alın.',
                'h1' => 'Hassas Terazi ve Analitik Terazi Modelleri',
                'hero_text' => 'Hassas teraziler; laboratuvar, kalite kontrol, üretim ve AR-GE süreçlerinde doğru tartım sonuçları almak için kullanılan temel ölçüm cihazlarıdır. MTA Endüstri teraziler kategorisinde A&D, Ohaus, Shimadzu ve Weightlab markalarına ait hassas terazi, analitik terazi ve laboratuvar terazisi modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Hassas Terazi İçin Teklif Al',
                'secondary_cta' => 'Terazi Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'kutle-terazi-kalibrasyonu'),
                'brand_eyebrow' => 'Terazi markaları',
                'list_title' => 'Hassas terazi modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Tartım Süreçleri İçin Hassas Teraziler',
                        'text' => 'Hassas terazi seçimi, yalnızca kapasite ve okunabilirlik değerlerine göre yapılmamalıdır. Numune tipi, kullanım sıklığı, ortam koşulları, kalibrasyon ihtiyacı, veri aktarımı ve servis desteği birlikte değerlendirilmelidir. Laboratuvarlarda kullanılan tartım cihazları; reçete hazırlama, numune analizi, kalite kontrol ve AR-GE uygulamalarında ölçüm güvenilirliğini doğrudan etkiler.',
                    ],
                    [
                        'title' => 'Analitik Terazi, Hassas Terazi ve Laboratuvar Terazisi Arasındaki Farklar',
                        'text' => 'Analitik teraziler, düşük okunabilirlik değerleriyle hassas tartım işlemlerinde tercih edilir. Hassas teraziler daha geniş laboratuvar uygulamalarında kullanılırken, laboratuvar terazileri farklı kapasite ve hassasiyet ihtiyaçlarına göre seçilir. Doğru cihaz seçimi için kapasite, hassasiyet, kalibrasyon tipi, ekran yapısı, bağlantı seçenekleri ve kullanım alanı birlikte incelenmelidir.',
                    ],
                    [
                        'title' => 'Hassas Terazi Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Hassas terazi seçiminde okunabilirlik, maksimum kapasite, tekrarlanabilirlik, doğrusal sapma, kalibrasyon tipi, çevresel koşullar ve kullanım sıklığı birlikte değerlendirilmelidir. Ayrıca cihazın servis desteği, yedek parça erişimi ve kalibrasyon süreci de uzun vadeli kullanım güvenilirliği açısından önemlidir.',
                    ],
                    [
                        'title' => 'Terazi Kalibrasyonu ve Teknik Servis Bağlantısı',
                        'text' => 'Tartım cihazlarının güvenilir sonuç verebilmesi için düzenli kontrol, bakım ve kalibrasyon süreçleri önemlidir. Hassas terazi ve analitik terazi modelleri, kütle ve terazi kalibrasyonu hizmetiyle ilişkilendirilmelidir. Arıza, stabilite problemi, ekran sorunu veya bakım ihtiyacı olan cihazlar için terazi teknik servis sayfasına yönlendirme yapılmalıdır.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'A&D, Ohaus, Shimadzu ve Weightlab Terazi Modelleri',
                    'text' => 'MTA Endüstri teraziler kategorisinde A&D, Ohaus, Shimadzu ve Weightlab markalarına ait farklı kapasite ve hassasiyet seçeneklerine sahip modeller listelenir. Kullanıcılar ürünleri marka, model ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'cta' => [
                    'title' => 'Hassas Terazi İçin Teklif Alın',
                    'text' => 'Laboratuvarınız, kalite kontrol biriminiz veya üretim süreciniz için uygun hassas terazi modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Marka, model, kapasite, hassasiyet ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun hassas terazi, analitik terazi veya laboratuvar terazisi modeli için teknik ekibe ulaşın.',
                    'button' => 'Hassas Terazi İçin Teklif Al',
                    'anchor' => 'hassas terazi teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'terazi kalibrasyonu'],
                    ['url' => route('technical-services.show', 'terazi-teknik-servis'), 'anchor' => 'terazi teknik servis'],
                ],
                'brand_alt_texts' => [
                    'and' => 'A&D hassas terazi marka logosu',
                    'ohaus' => 'Ohaus terazi marka logosu',
                    'shimadzu' => 'Shimadzu analitik terazi marka logosu',
                    'weightlab' => 'Weightlab laboratuvar terazisi marka logosu',
                ],
                'brand_anchors' => [
                    'and' => 'A&D hassas terazi modelleri',
                    'ohaus' => 'Ohaus terazi modelleri',
                    'shimadzu' => 'Shimadzu analitik terazi modelleri',
                    'weightlab' => 'Weightlab laboratuvar terazileri',
                ],
            ],
            'nem-tayin' => [
                'slug' => 'nem-tayin',
                'meta_title' => 'Nem Tayin Cihazı Modelleri ve Nem Analiz Cihazları | MTA Endüstri',
                'meta_description' => 'Nem tayin cihazı ve nem analiz cihazı modellerini A&D, Ohaus, Shimadzu ve Weightlab markalarıyla inceleyin; teknik özelliklere göre teklif alın.',
                'h1' => 'Nem Tayin Cihazı Modelleri ve Nem Analiz Cihazları',
                'hero_text' => 'Nem tayin cihazları; numunelerdeki nem oranını tartım ve kontrollü ısıtma prensibiyle belirlemek için kullanılan laboratuvar analiz cihazlarıdır. MTA Endüstri nem tayin kategorisinde A&D, Ohaus, Shimadzu ve Weightlab markalarına ait farklı kapasite, okunabilirlik ve ısıtma teknolojilerine sahip modelleri teknik özellikleriyle listeler.',
                'primary_cta' => 'Nem Tayin Cihazı İçin Teklif Al',
                'secondary_cta' => 'İlgili Kalibrasyon Hizmetlerini İncele',
                'secondary_cta_url' => route('services.show', 'kutle-terazi-kalibrasyonu'),
                'brand_eyebrow' => 'Nem tayin cihazı markaları',
                'list_title' => 'Nem tayin cihazı modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Nem Analizi İçin Nem Tayin Cihazları',
                        'text' => 'Nem tayin cihazları; gıda, ilaç, kimya, plastik, tarım, kozmetik ve kalite kontrol laboratuvarlarında numune nem oranının belirlenmesi için kullanılır. Üretim kalitesi, raf ömrü, formülasyon kararlılığı ve proses kontrolü açısından nem değeri kritik bir parametre olabilir. Doğru cihaz seçimi, numune tipi ve analiz ihtiyacına göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Nem Tayin Cihazı Nasıl Çalışır?',
                        'text' => 'Nem tayin cihazı, numuneyi hassas tartım sistemi üzerinde ölçer ve kontrollü ısıtma ile kurutma işlemi uygular. Kurutma sürecinde oluşan ağırlık kaybı üzerinden numunenin nem oranı hesaplanır. Bu nedenle cihaz performansı hem tartım doğruluğu hem de sıcaklık kontrolüyle ilişkilidir.',
                    ],
                    [
                        'title' => 'Halojen Nem Tayin Cihazı Nedir?',
                        'text' => 'Halojen nem tayin cihazları, hızlı ısıtma ve kontrollü kurutma süreçleri sayesinde laboratuvarlarda pratik nem analizi sağlar. Halojen ısıtma teknolojisi, birçok numune tipi için hızlı sonuç alınmasına yardımcı olabilir. Uygun metot seçimi ve sıcaklık ayarı, güvenilir analiz sonuçları için önemlidir.',
                    ],
                    [
                        'title' => 'Nem Tayin Cihazı Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Nem tayin cihazı seçiminde kapasite, okunabilirlik, ısıtma tipi, sıcaklık aralığı, metot hafızası, analiz süresi, veri aktarımı, kullanım kolaylığı ve bakım gereksinimi değerlendirilmelidir. Numune yapısı, beklenen nem aralığı ve rutin analiz yoğunluğu cihaz seçiminde belirleyici olur.',
                    ],
                    [
                        'title' => 'Nem Tayin Cihazlarında Kalibrasyon ve Teknik Servis',
                        'text' => 'Nem tayin cihazlarında güvenilir sonuç için tartım sistemi ve sıcaklık kontrolü birlikte değerlendirilmelidir. Tartım doğruluğu kütle ve terazi kalibrasyonu; ısıtma ve sıcaklık performansı ise sıcaklık kalibrasyonu ile ilişkilendirilebilir. Cihazda ısıtma problemi, tartım sapması, ekran hatası veya metot çalıştırma sorunu varsa laboratuvar cihazları teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'A&D, Ohaus, Shimadzu ve Weightlab Nem Tayin Modelleri',
                    'text' => 'MTA Endüstri nem tayin kategorisinde A&D, Ohaus, Shimadzu ve Weightlab markalarına ait laboratuvar nem analizi için kullanılan modeller listelenir. Kullanıcılar ürünleri marka, model, kapasite, okunabilirlik, ısıtma tipi ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Nem Tayin Cihazı Kullanım Alanları',
                    'text' => 'Nem tayin cihazları; ham madde kabul, üretim kontrol, son ürün kalite kontrolü ve AR-GE çalışmalarında kullanılabilir. Numune tipi ve analiz yöntemi, cihaz seçiminin en kritik belirleyicilerindendir.',
                    'items' => [
                        'Gıda ve içecek kalite kontrolü',
                        'Plastik ve polimer hammaddeleri',
                        'İlaç ve farmasötik ürün analizleri',
                        'Kimyasal ham madde kontrolü',
                        'Tarım ve yem analizleri',
                        'Kozmetik formülasyon çalışmaları',
                        'AR-GE ve proses kontrol süreçleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Nem Tayin Cihazı İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda nem analizi, kalite kontrol veya proses takibi için uygun nem tayin cihazı modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Numune tipi, kapasite ihtiyacı, analiz sıklığı, marka tercihi ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun halojen nem tayin cihazı veya laboratuvar nem analiz cihazı için teknik ekibe ulaşın.',
                    'button' => 'Nem Tayin Cihazı İçin Teklif Al',
                    'anchor' => 'nem tayin cihazı teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'kutle-terazi-kalibrasyonu'), 'anchor' => 'kütle ve terazi kalibrasyonu'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.category', 'teraziler'), 'anchor' => 'hassas terazi modelleri'],
                ],
                'brand_alt_texts' => [
                    'and' => 'A&D nem tayin cihazı marka logosu',
                    'ohaus' => 'Ohaus nem tayin cihazı marka logosu',
                    'shimadzu' => 'Shimadzu nem analiz cihazı marka logosu',
                    'weightlab' => 'Weightlab nem tayin cihazı marka logosu',
                ],
                'brand_anchors' => [
                    'and' => 'A&D nem tayin cihazları',
                    'ohaus' => 'Ohaus nem tayin cihazları',
                    'shimadzu' => 'Shimadzu nem analiz cihazları',
                    'weightlab' => 'Weightlab nem tayin cihazları',
                ],
                'faq' => [
                    [
                        'question' => 'Nem tayin cihazı ne işe yarar?',
                        'answer' => 'Nem tayin cihazı, numunedeki nem oranını tartım ve kontrollü ısıtma yöntemiyle belirlemek için kullanılır. Gıda, kimya, ilaç, plastik ve kalite kontrol laboratuvarlarında tercih edilir.',
                    ],
                    [
                        'question' => 'Halojen nem tayin cihazı nedir?',
                        'answer' => 'Halojen nem tayin cihazı, numuneyi halojen ısıtma sistemiyle kurutarak ağırlık kaybı üzerinden nem oranını hesaplayan analiz cihazıdır.',
                    ],
                    [
                        'question' => 'Nem tayin cihazı seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Kapasite, okunabilirlik, ısıtma tipi, sıcaklık aralığı, metot hafızası, analiz süresi, veri aktarımı ve numune tipi birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Nem tayin cihazı için kalibrasyon gerekir mi?',
                        'answer' => 'Cihazın tartım sistemi kütle ve terazi kalibrasyonu; sıcaklık performansı ise sıcaklık kalibrasyonu kapsamında değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Nem tayin cihazı teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Tartım sonucu sapıyorsa, ısıtma çalışmıyorsa, cihaz hata veriyorsa, ekran veya kontrol paneli sorunu varsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'kral-fischer' => [
                'slug' => 'kral-fischer',
                'meta_title' => 'Karl Fischer Titratör ve Su Miktarı Tayin Cihazları | MTA Endüstri',
                'meta_description' => 'Karl Fischer titratör ve su miktarı tayin cihazlarını Kyoto KEM, Mettler Toledo ve SI Analitik markalarıyla inceleyin.',
                'h1' => 'Karl Fischer Titratör ve Su Miktarı Tayin Cihazları',
                'hero_text' => 'Karl Fischer titratörler; sıvı, katı veya gaz numunelerde su miktarını belirlemek için kullanılan özel titrasyon cihazlarıdır. MTA Endüstri Kral Fischer kategorisinde Kyoto KEM, Mettler Toledo ve SI Analitik markalarına ait su miktarı tayin cihazlarını teknik özellikleriyle listeler.',
                'primary_cta' => 'Karl Fischer Titratör İçin Teklif Al',
                'secondary_cta' => 'Hacim Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'hacim-kalibrasyonu'),
                'brand_eyebrow' => 'Karl Fischer markaları',
                'list_title' => 'Karl Fischer titratör modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Su Miktarı Tayini İçin Karl Fischer Titratörler',
                        'text' => 'Karl Fischer titrasyonu, numunelerdeki su miktarının belirlenmesi için kullanılan hassas bir analiz yöntemidir. Gıda, ilaç, kimya, petrokimya, kozmetik ve kalite kontrol laboratuvarlarında su içeriği ürün kalitesi ve proses güvenilirliği açısından kritik olabilir.',
                    ],
                    [
                        'title' => 'Karl Fischer Titratör Ne İşe Yarar?',
                        'text' => 'Karl Fischer titratör, numunedeki su miktarını kimyasal titrasyon yöntemiyle belirlemek için kullanılır. Bu cihazlar özellikle düşük su miktarlarının güvenilir şekilde ölçülmesi gereken laboratuvar uygulamalarında tercih edilir.',
                    ],
                    [
                        'title' => 'Volumetrik ve Kulometrik Karl Fischer Arasındaki Fark',
                        'text' => 'Volumetrik Karl Fischer yöntemi genellikle daha yüksek su miktarı içeren numunelerde tercih edilirken, kulometrik Karl Fischer yöntemi çok düşük su miktarlarının belirlenmesinde kullanılabilir. Uygun yöntem seçimi numune yapısı, beklenen su oranı ve analiz sıklığına göre değerlendirilmelidir.',
                    ],
                    [
                        'title' => 'Karl Fischer Cihazı Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Karl Fischer cihazı seçiminde ölçüm yöntemi, beklenen su aralığı, numune tipi, titrasyon hücresi, elektrot yapısı, büret veya dozaj sistemi, otomasyon uyumu, veri yönetimi ve bakım gereksinimi birlikte değerlendirilmelidir.',
                    ],
                    [
                        'title' => 'Karl Fischer Titratörlerde Kalibrasyon ve Teknik Servis',
                        'text' => 'Karl Fischer titratörlerde büret, dozaj sistemi, elektrot, titrasyon hücresi ve yazılım ayarları analiz performansını etkiler. Hacimsel dozaj doğruluğu hacim kalibrasyonu ile ilişkilendirilebilir; cihaz stabilitesi için laboratuvar cihazları teknik servis desteği gerekebilir.',
                    ],
                    [
                        'title' => 'Karl Fischer ve Nem Tayin Cihazı Arasındaki Fark',
                        'text' => 'Nem tayin cihazları numunedeki nem oranını tartım ve ısıtma yöntemiyle belirlerken, Karl Fischer titratörler su miktarını kimyasal titrasyon yöntemiyle ölçer. Daha spesifik su tayini gereken uygulamalarda Karl Fischer yöntemi tercih edilebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Kyoto KEM, Mettler Toledo ve SI Analitik Karl Fischer Modelleri',
                    'text' => 'MTA Endüstri Kral Fischer kategorisinde Kyoto KEM, Mettler Toledo ve SI Analitik markalarına ait farklı uygulama ihtiyaçlarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, titrasyon yöntemi ve teknik özelliklerine göre inceleyebilir.',
                ],
                'list_section' => [
                    'title' => 'Karl Fischer Titratör Kullanım Alanları',
                    'text' => 'Karl Fischer titratörler; hammaddeler, solventler, yağlar, ilaç formülasyonları, gıda numuneleri, kozmetik ürünler ve petrokimya numunelerinde su miktarı tayini için kullanılabilir.',
                    'items' => [
                        'İlaç ve farmasötik laboratuvarlar',
                        'Kimyasal hammadde analizleri',
                        'Petrokimya ve yağ numuneleri',
                        'Gıda ve içecek kalite kontrolü',
                        'Kozmetik formülasyon çalışmaları',
                        'Solvent ve reaktif kontrolü',
                        'AR-GE ve metot geliştirme süreçleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Karl Fischer Titratör İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda su miktarı tayini, kalite kontrol veya metot geliştirme uygulamaları için uygun Karl Fischer titratör modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Numune tipi, beklenen su aralığı, analiz sıklığı, yöntem tercihi ve marka ihtiyacınıza göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun Karl Fischer titratör veya su miktarı tayin cihazı için teknik ekibe ulaşın.',
                    'button' => 'Karl Fischer Titratör İçin Teklif Al',
                    'anchor' => 'Karl Fischer titratör teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'hacim-kalibrasyonu'), 'anchor' => 'hacim kalibrasyonu'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.category', 'nem-tayin'), 'anchor' => 'nem tayin cihazı modelleri'],
                    ['url' => route('products.category', 'potansiyometrik-titratorler'), 'anchor' => 'potansiyometrik titratör modelleri'],
                ],
                'brand_alt_texts' => [
                    'kyoto-kem' => 'Kyoto KEM Karl Fischer titratör marka logosu',
                    'mettler-toledo' => 'Mettler Toledo Karl Fischer cihazı marka logosu',
                    'si-analitik' => 'SI Analitik titrasyon cihazları marka logosu',
                ],
                'brand_anchors' => [
                    'kyoto-kem' => 'Kyoto KEM Karl Fischer modelleri',
                    'mettler-toledo' => 'Mettler Toledo Karl Fischer cihazları',
                    'si-analitik' => 'SI Analitik titrasyon cihazları',
                ],
                'faq' => [
                    [
                        'question' => 'Karl Fischer titratör ne işe yarar?',
                        'answer' => 'Karl Fischer titratör, numunelerdeki su miktarını kimyasal titrasyon yöntemiyle belirlemek için kullanılır. İlaç, kimya, petrokimya, gıda ve kalite kontrol laboratuvarlarında tercih edilir.',
                    ],
                    [
                        'question' => 'Volumetrik ve kulometrik Karl Fischer arasındaki fark nedir?',
                        'answer' => 'Volumetrik Karl Fischer daha yüksek su miktarı içeren numunelerde tercih edilebilirken, kulometrik Karl Fischer çok düşük su miktarlarının ölçümünde kullanılabilir.',
                    ],
                    [
                        'question' => 'Karl Fischer cihazı seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Numune tipi, beklenen su aralığı, titrasyon yöntemi, elektrot yapısı, büret/dozaj sistemi, otomasyon uyumu, veri yönetimi ve bakım gereksinimi değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Karl Fischer ile nem tayin cihazı arasındaki fark nedir?',
                        'answer' => 'Nem tayin cihazı tartım ve ısıtma yöntemiyle nem oranı belirler. Karl Fischer titratör ise kimyasal titrasyon yöntemiyle su miktarını ölçer. Uygun yöntem analiz hedeflerine göre seçilmelidir.',
                    ],
                    [
                        'question' => 'Karl Fischer titratör için teknik servis gerekir mi?',
                        'answer' => 'Elektrot performansı düşüyorsa, büret veya pompa sisteminde sorun varsa, cihaz stabil ölçüm yapmıyorsa ya da yazılım/elektronik hata veriyorsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'potansiyometrik-titratorler' => [
                'slug' => 'potansiyometrik-titratorler',
                'meta_title' => 'Potansiyometrik Titratör ve Otomatik Titrasyon Cihazları | MTA Endüstri',
                'meta_description' => 'Potansiyometrik titratör ve otomatik titrasyon cihazlarını Mettler Toledo, Kyoto KEM ve SI Analitik markalarıyla inceleyin.',
                'h1' => 'Potansiyometrik Titratör ve Otomatik Titrasyon Cihazları',
                'hero_text' => 'Potansiyometrik titratörler; laboratuvarlarda asit-baz, redoks, çöktürme ve kompleksometrik titrasyon gibi analiz süreçlerinde kullanılan otomatik titrasyon cihazlarıdır. MTA Endüstri potansiyometrik titratörler kategorisinde Mettler Toledo, Kyoto KEM ve SI Analitik markalarına ait titrasyon cihazlarını teknik özellikleriyle listeler.',
                'primary_cta' => 'Potansiyometrik Titratör İçin Teklif Al',
                'secondary_cta' => 'Hacim Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'hacim-kalibrasyonu'),
                'brand_eyebrow' => 'Titratör markaları',
                'list_title' => 'Potansiyometrik titratör modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Titrasyon Analizleri İçin Potansiyometrik Titratörler',
                        'text' => 'Potansiyometrik titratörler; kalite kontrol, AR-GE, üretim destek ve analiz laboratuvarlarında tekrarlanabilir titrasyon sonuçları elde etmek için kullanılır. Manuel titrasyon süreçlerine göre metot yönetimi, dozaj kontrolü, veri kaydı ve operatör etkisinin azaltılması açısından avantaj sağlayabilir.',
                    ],
                    [
                        'title' => 'Potansiyometrik Titratör Ne İşe Yarar?',
                        'text' => 'Potansiyometrik titratör, numunedeki kimyasal bileşenin miktarını elektrot sinyali ve kontrollü titrant ilavesiyle belirlemek için kullanılır. Cihaz; titrant dozajı, elektrot ölçümü, eşdeğerlik noktası tespiti ve sonuç hesaplama adımlarını kontrollü bir süreç içinde yönetir.',
                    ],
                    [
                        'title' => 'Otomatik Titratör Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Otomatik titratör seçiminde titrasyon türü, büret hacmi, dozaj hassasiyeti, elektrot uyumu, metot hafızası, otomasyon seçenekleri, veri aktarımı, yazılım özellikleri ve bakım gereksinimi birlikte değerlendirilmelidir. Numune yoğunluğu ve rutin analiz sayısı da cihaz seçiminde belirleyici olur.',
                    ],
                    [
                        'title' => 'Potansiyometrik Titrasyon Kullanım Alanları',
                        'text' => 'Potansiyometrik titrasyon; asit-baz tayini, redoks titrasyonu, klorür analizi, toplam asitlik, alkalinite, kompleksometrik analizler ve kalite kontrol uygulamalarında kullanılabilir. Uygun elektrot, titrant ve metot seçimi analiz sonucunun güvenilirliği açısından önemlidir.',
                    ],
                    [
                        'title' => 'Potansiyometrik Titratörlerde Hacim Doğruluğu ve Kalibrasyon',
                        'text' => 'Titrasyon sonuçlarının güvenilirliği, kullanılan titrant hacminin doğru ve tekrarlanabilir dozlanmasıyla doğrudan ilişkilidir. Büret, dispenser ve dozaj sistemleri hacimsel doğruluk açısından değerlendirilmelidir. Bu nedenle potansiyometrik titratörler hacim kalibrasyonu süreçleriyle doğal olarak ilişkilendirilebilir.',
                    ],
                    [
                        'title' => 'Titratör Teknik Servis ve Bakım',
                        'text' => 'Potansiyometrik titratörlerde elektrot performansı, büret veya pompa sistemi, dozaj ünitesi, karıştırıcı, ekran, yazılım ve elektronik bileşenler analiz performansını etkileyebilir. Cihazın stabil sonuç vermemesi, titrant dozajında sorun yaşanması, elektrot hatası veya yazılım problemi görülmesi halinde laboratuvar cihazları teknik servis değerlendirmesi gerekebilir.',
                    ],
                    [
                        'title' => 'Karl Fischer Titratör ile Potansiyometrik Titratör Arasındaki Fark',
                        'text' => 'Karl Fischer titratörler numunelerde su miktarı tayini için özel olarak kullanılırken, potansiyometrik titratörler farklı kimyasal titrasyon uygulamalarında tercih edilir. Her iki cihaz grubu da titrasyon temelli analiz süreçlerinde yer alır; ancak yöntem, elektrot, reaktif ve kullanım amacı farklıdır.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Mettler Toledo, Kyoto KEM ve SI Analitik Titratör Modelleri',
                    'text' => 'MTA Endüstri potansiyometrik titratörler kategorisinde Mettler Toledo, Kyoto KEM ve SI Analitik markalarına ait farklı uygulama ihtiyaçlarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, titrasyon yöntemi, elektrot uyumu ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Potansiyometrik Titrasyon Kullanım Alanları',
                    'text' => 'Potansiyometrik titratörler farklı kimyasal analiz türlerinde, kalite kontrol süreçlerinde ve metot geliştirme çalışmalarında kullanılabilir. Uygun elektrot, titrant ve dozaj sistemi seçimi analiz güvenilirliğini doğrudan etkiler.',
                    'items' => [
                        'Asit-baz titrasyonları',
                        'Redoks titrasyonları',
                        'Çöktürme titrasyonları',
                        'Kompleksometrik titrasyonlar',
                        'Gıda ve içecek kalite kontrolü',
                        'Kimyasal ham madde analizleri',
                        'İlaç ve kozmetik laboratuvarları',
                        'AR-GE ve metot geliştirme süreçleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Potansiyometrik Titratör İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda otomatik titrasyon, kalite kontrol veya metot geliştirme uygulamaları için uygun potansiyometrik titratör modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Analiz türü, numune tipi, titrasyon yöntemi, otomasyon ihtiyacı ve marka tercihinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun potansiyometrik titratör veya otomatik titrasyon cihazı için teknik ekibe ulaşın.',
                    'button' => 'Potansiyometrik Titratör İçin Teklif Al',
                    'anchor' => 'potansiyometrik titratör teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'hacim-kalibrasyonu'), 'anchor' => 'hacim kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.category', 'kral-fischer'), 'anchor' => 'Karl Fischer titratör modelleri'],
                    ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre modelleri'],
                ],
                'brand_alt_texts' => [
                    'kyoto-kem' => 'Kyoto KEM potansiyometrik titratör marka logosu',
                    'mettler-toledo' => 'Mettler Toledo otomatik titratör marka logosu',
                    'si-analitik' => 'SI Analitik titrasyon cihazları marka logosu',
                ],
                'brand_anchors' => [
                    'kyoto-kem' => 'Kyoto KEM titratör modelleri',
                    'mettler-toledo' => 'Mettler Toledo titratör modelleri',
                    'si-analitik' => 'SI Analitik titrasyon cihazları',
                ],
                'faq' => [
                    [
                        'question' => 'Potansiyometrik titratör ne işe yarar?',
                        'answer' => 'Potansiyometrik titratör, elektrot sinyali ve kontrollü titrant ilavesiyle kimyasal analizlerde eşdeğerlik noktasını belirlemek için kullanılır.',
                    ],
                    [
                        'question' => 'Otomatik titratör seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Titrasyon türü, büret hacmi, dozaj hassasiyeti, elektrot uyumu, metot hafızası, otomasyon seçenekleri, veri aktarımı ve bakım gereksinimi birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Potansiyometrik titratör hangi analizlerde kullanılır?',
                        'answer' => 'Asit-baz, redoks, çöktürme, kompleksometrik titrasyon, klorür analizi, toplam asitlik ve alkalinite gibi laboratuvar analizlerinde kullanılabilir.',
                    ],
                    [
                        'question' => 'Karl Fischer titratör ile potansiyometrik titratör aynı mıdır?',
                        'answer' => 'Hayır. Karl Fischer titratör su miktarı tayini için kullanılır. Potansiyometrik titratör ise farklı kimyasal titrasyon uygulamalarında tercih edilen daha genel bir otomatik titrasyon cihazıdır.',
                    ],
                    [
                        'question' => 'Potansiyometrik titratör için teknik servis gerekir mi?',
                        'answer' => 'Elektrot performansı düşüyorsa, büret veya pompa sisteminde sorun varsa, cihaz stabil sonuç vermiyorsa ya da yazılım/elektronik hata oluşuyorsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'ph-metre' => [
                'slug' => 'ph-metre',
                'meta_title' => 'pH Metre Modelleri ve Laboratuvar pH Ölçüm Cihazları | MTA Endüstri',
                'meta_description' => 'Laboratuvar pH metre, portatif pH metre ve çok parametreli ölçüm cihazlarını Mettler Toledo, Ohaus ve WTW markalarıyla inceleyin; teklif alın.',
                'h1' => 'pH Metre Modelleri ve Laboratuvar pH Ölçüm Cihazları',
                'hero_text' => 'pH metreler; laboratuvar, kalite kontrol, üretim ve saha ölçümlerinde sıvı numunelerin pH değerini belirlemek için kullanılan temel ölçüm cihazlarıdır. MTA Endüstri pH metre kategorisinde Mettler Toledo, Ohaus ve WTW markalarına ait laboratuvar tipi, portatif ve çok parametreli ölçüm cihazlarını teknik özellikleriyle listeler.',
                'primary_cta' => 'pH Metre İçin Teklif Al',
                'secondary_cta' => 'pH & İletkenlik Cihazlarını İncele',
                'secondary_cta_url' => route('products.category', 'ph-iletkenlik'),
                'brand_eyebrow' => 'pH metre markaları',
                'list_title' => 'pH metre modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar ve Saha Ölçümleri İçin pH Metreler',
                        'text' => 'pH metre seçimi, ölçüm ortamına ve numune tipine göre yapılmalıdır. Laboratuvar uygulamalarında masaüstü pH metreler hassas ve tekrarlanabilir ölçüm için tercih edilirken, saha uygulamalarında portatif pH metreler pratik kullanım sağlar. Ölçüm güvenilirliği için cihaz, elektrot, sıcaklık kompanzasyonu ve bakım süreci birlikte değerlendirilmelidir.',
                    ],
                    [
                        'title' => 'pH Metre Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'pH metre seçiminde ölçüm aralığı, çözünürlük, doğruluk, sıcaklık kompanzasyonu, elektrot uyumluluğu, veri kayıt özelliği, kullanım ortamı ve bakım gereksinimi dikkate alınmalıdır. Numune yapısı, sıcaklık değişimi ve kullanım sıklığı da doğru cihaz seçiminde belirleyici olur.',
                    ],
                    [
                        'title' => 'Masaüstü, Portatif ve Çok Parametreli pH Metreler',
                        'text' => 'Masaüstü pH metreler laboratuvar ortamında hassas ölçüm ve düzenli analiz süreçleri için uygundur. Portatif pH metreler saha ölçümleri ve hızlı kontroller için tercih edilir. Çok parametreli cihazlar ise pH, iletkenlik, sıcaklık ve farklı ölçüm parametrelerini tek cihaz üzerinde takip etmek isteyen kullanıcılar için çözüm sunar.',
                    ],
                    [
                        'title' => 'pH Elektrodu, Bakım ve Ölçüm Güvenilirliği',
                        'text' => 'pH ölçümlerinde yalnızca cihaz değil, kullanılan elektrotun tipi ve bakım durumu da sonucu doğrudan etkiler. Elektrotun numune yapısına uygun seçilmesi, düzenli temizlenmesi, uygun saklama çözeltisinde korunması ve gerektiğinde değiştirilmesi ölçüm güvenilirliği için önemlidir.',
                    ],
                    [
                        'title' => 'pH Metrelerde Kalibrasyon ve Teknik Servis Bağlantısı',
                        'text' => 'pH metrelerde ölçüm doğruluğunu korumak için düzenli kontrol, bakım ve uygun kalibrasyon prosedürleri önemlidir. Elektrot sorunu, ölçüm stabilitesi problemi, sıcaklık sensörü arızası veya ekran/bağlantı hatası görüldüğünde teknik servis değerlendirmesi gerekebilir. İlgili cihaz grupları sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis süreçleriyle ilişkilendirilebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Mettler Toledo, Ohaus ve WTW pH Metre Modelleri',
                    'text' => 'MTA Endüstri pH metre kategorisinde Mettler Toledo, Ohaus ve WTW markalarına ait farklı kullanım alanlarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, ölçüm parametresi ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'cta' => [
                    'title' => 'pH Metre İçin Teklif Alın',
                    'text' => 'Laboratuvarınız, kalite kontrol biriminiz veya saha ölçüm süreçleriniz için uygun pH metre modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Marka, model, kullanım alanı, ölçüm parametreleri ve elektrot ihtiyacınıza göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun pH metre, elektrot veya çok parametreli ölçüm cihazı için teknik ekibe ulaşın.',
                    'button' => 'pH Metre İçin Teklif Al',
                    'anchor' => 'pH metre teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('products.category', 'ph-iletkenlik'), 'anchor' => 'pH ve iletkenlik ölçer cihazları'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'brand_alt_texts' => [
                    'mettler-toledo' => 'Mettler Toledo pH metre marka logosu',
                    'ohaus' => 'Ohaus pH metre marka logosu',
                    'wtw' => 'WTW pH ve iletkenlik ölçüm cihazları marka logosu',
                ],
                'brand_anchors' => [
                    'mettler-toledo' => 'Mettler Toledo pH metre modelleri',
                    'ohaus' => 'Ohaus pH metre modelleri',
                    'wtw' => 'WTW pH metre modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'pH metre seçerken en önemli kriterler nelerdir?',
                        'answer' => 'Ölçüm aralığı, doğruluk, sıcaklık kompanzasyonu, elektrot uyumluluğu, kullanım ortamı ve veri kayıt ihtiyacı pH metre seçiminde birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Laboratuvar pH metre ile portatif pH metre arasındaki fark nedir?',
                        'answer' => 'Laboratuvar pH metreler genellikle masaüstü kullanım, hassas ölçüm ve düzenli analiz süreçleri için tercih edilir. Portatif pH metreler ise saha ölçümleri ve hızlı kontroller için daha uygundur.',
                    ],
                    [
                        'question' => 'pH elektrodu neden önemlidir?',
                        'answer' => 'pH ölçüm sonucunu doğrudan etkileyen ana bileşen elektrottur. Numune tipine uygun elektrot seçimi, düzenli bakım ve doğru saklama ölçüm güvenilirliği için gereklidir.',
                    ],
                    [
                        'question' => 'pH metre teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Ölçüm değeri stabil değilse, cihaz kalibrasyon kabul etmiyorsa, sıcaklık sensörü hatası varsa veya ekran/bağlantı sorunları görülüyorsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'ph-iletkenlik' => [
                'slug' => 'ph-iletkenlik',
                'meta_title' => 'İletkenlik Ölçer ve pH & İletkenlik Cihazları | MTA Endüstri',
                'meta_description' => 'İletkenlik ölçer, pH ve iletkenlik ölçer ve çok parametreli ölçüm cihazlarını WTW, Mettler Toledo ve Ohaus markalarıyla inceleyin.',
                'h1' => 'İletkenlik Ölçer ve pH & İletkenlik Cihazları',
                'hero_text' => 'pH ve iletkenlik ölçer cihazları; laboratuvar, kalite kontrol, çevre analizi, su analizi ve saha ölçümlerinde pH, iletkenlik ve sıcaklık değerlerini takip etmek için kullanılır. MTA Endüstri pH & iletkenlik kategorisinde WTW, Mettler Toledo ve Ohaus markalarına ait laboratuvar tipi, portatif ve çok parametreli ölçüm cihazlarını teknik özellikleriyle listeler.',
                'primary_cta' => 'İletkenlik Ölçer İçin Teklif Al',
                'secondary_cta' => 'pH Metre Modellerini İncele',
                'secondary_cta_url' => route('products.category', 'ph-metre'),
                'brand_eyebrow' => 'pH & iletkenlik markaları',
                'list_title' => 'pH ve iletkenlik ölçer modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar ve Saha Ölçümleri İçin İletkenlik Ölçerler',
                        'text' => 'İletkenlik ölçerler, su ve sıvı numunelerde iyonik iletkenliğin değerlendirilmesi için kullanılır. Laboratuvar analizlerinde, kalite kontrol süreçlerinde, çevre ölçümlerinde ve saha uygulamalarında tercih edilebilir.',
                    ],
                    [
                        'title' => 'pH ve İletkenlik Ölçer Ne İşe Yarar?',
                        'text' => 'pH ve iletkenlik ölçer cihazları, sıvı numunelerin asitlik-bazlık seviyesi ile elektriksel iletkenlik değerlerini ölçmek için kullanılır. Çok parametreli modeller pH, iletkenlik, sıcaklık ve farklı ölçüm değerlerini tek cihaz üzerinde takip etmeye yardımcı olur.',
                    ],
                    [
                        'title' => 'Masaüstü, Portatif ve Çok Parametreli Ölçüm Cihazları',
                        'text' => 'Masaüstü pH ve iletkenlik ölçerler laboratuvar ortamında düzenli analiz süreçleri için uygundur. Portatif modeller saha ölçümleri ve hızlı kontroller için tercih edilir. Çok parametreli ölçüm cihazları ise farklı sensör kombinasyonlarını tek sistemde kullanmak isteyen laboratuvarlar için çözüm sunar.',
                    ],
                    [
                        'title' => 'İletkenlik Ölçer Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'İletkenlik ölçer seçiminde ölçüm aralığı, doğruluk, çözünürlük, sıcaklık kompanzasyonu, prob uyumu, veri kayıt özelliği, kullanım ortamı ve bakım gereksinimi dikkate alınmalıdır.',
                    ],
                    [
                        'title' => 'Prob, Sensör ve Sıcaklık Kompanzasyonu',
                        'text' => 'pH ve iletkenlik ölçümlerinde kullanılan prob veya sensörün numune tipine uygun olması ölçüm sonucunu doğrudan etkiler. Sıcaklık kompanzasyonu, özellikle iletkenlik ölçümlerinde güvenilir sonuç için önemlidir.',
                    ],
                    [
                        'title' => 'pH & İletkenlik Cihazlarında Kalibrasyon ve Teknik Servis',
                        'text' => 'pH ve iletkenlik ölçerlerde ölçüm güvenilirliği için düzenli kontrol, prob bakımı ve cihaz performansının takip edilmesi önemlidir. Sıcaklık sensörüyle ilişkili doğrulama ihtiyaçları sıcaklık kalibrasyonu kapsamında değerlendirilebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'WTW, Mettler Toledo ve Ohaus pH & İletkenlik Modelleri',
                    'text' => 'MTA Endüstri pH & iletkenlik kategorisinde WTW, Mettler Toledo ve Ohaus markalarına ait farklı kullanım alanlarına uygun cihazlar listelenir. Kullanıcılar ürünleri marka, model, ölçüm parametresi, prob uyumu ve teknik özelliklerine göre inceleyebilir.',
                ],
                'cta' => [
                    'title' => 'İletkenlik Ölçer İçin Teklif Alın',
                    'text' => 'Laboratuvarınız, kalite kontrol biriminiz veya saha ölçüm süreçleriniz için uygun iletkenlik ölçer ya da çok parametreli ölçüm cihazı seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Ölçüm parametreleri, kullanım alanı, prob ihtiyacı ve marka tercihinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun pH ve iletkenlik ölçer, portatif cihaz veya çok parametreli ölçüm sistemi için teknik ekibe ulaşın.',
                    'button' => 'İletkenlik Ölçer İçin Teklif Al',
                    'anchor' => 'iletkenlik ölçer teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('products.category', 'ph-metre'), 'anchor' => 'pH metre modelleri'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'brand_alt_texts' => [
                    'wtw' => 'WTW pH ve iletkenlik ölçer marka logosu',
                    'mettler-toledo' => 'Mettler Toledo ölçüm cihazları marka logosu',
                    'ohaus' => 'Ohaus pH ve iletkenlik cihazları marka logosu',
                ],
                'brand_anchors' => [
                    'wtw' => 'WTW iletkenlik ölçer modelleri',
                    'mettler-toledo' => 'Mettler Toledo pH metre modelleri',
                    'ohaus' => 'Ohaus pH ve iletkenlik cihazları',
                ],
                'faq' => [
                    [
                        'question' => 'İletkenlik ölçer ne işe yarar?',
                        'answer' => 'İletkenlik ölçer, sıvı numunelerde iyonik iletkenlik değerini ölçmek için kullanılır. Su analizi, kalite kontrol, çevre ölçümü ve laboratuvar uygulamalarında tercih edilir.',
                    ],
                    [
                        'question' => 'pH ve iletkenlik ölçer ile pH metre arasındaki fark nedir?',
                        'answer' => 'pH metre yalnızca pH ölçümüne odaklanırken, pH ve iletkenlik ölçer cihazları pH, iletkenlik ve çoğu modelde sıcaklık gibi birden fazla parametreyi takip edebilir.',
                    ],
                    [
                        'question' => 'Portatif iletkenlik ölçer ne zaman tercih edilir?',
                        'answer' => 'Saha ölçümü, hızlı kontrol veya laboratuvar dışı uygulamalarda portatif iletkenlik ölçerler tercih edilir. Taşınabilirlik ve dayanıklılık bu cihazlarda önemli kriterlerdir.',
                    ],
                    [
                        'question' => 'İletkenlik ölçer seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Ölçüm aralığı, doğruluk, sıcaklık kompanzasyonu, prob uyumu, veri kayıt özelliği, kullanım ortamı ve bakım ihtiyacı birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'pH ve iletkenlik cihazları için teknik servis gerekir mi?',
                        'answer' => 'Ölçüm değeri stabil değilse, cihaz probu algılamıyorsa, kalibrasyon kabul etmiyorsa, sıcaklık sensörü veya ekran hatası varsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'refraktometre' => [
                'slug' => 'refraktometre',
                'meta_title' => 'Refraktometre Modelleri ve Dijital Refraktometreler | MTA Endüstri',
                'meta_description' => 'Refraktometre ve dijital refraktometre modellerini Kyoto KEM, Mettler Toledo ve Bellingham + Stanley markalarıyla inceleyin; teklif alın.',
                'h1' => 'Refraktometre Modelleri ve Dijital Refraktometreler',
                'hero_text' => 'Refraktometreler; sıvı numunelerde kırılma indisi, Brix ve konsantrasyon ölçümleri için kullanılan laboratuvar ölçüm cihazlarıdır. MTA Endüstri refraktometre kategorisinde Kyoto KEM, Mettler Toledo ve Bellingham + Stanley markalarına ait dijital refraktometre ve laboratuvar refraktometresi modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Refraktometre İçin Teklif Al',
                'secondary_cta' => 'İlgili Kalibrasyon Hizmetini İncele',
                'secondary_cta_url' => route('services.show', 'sicaklik-kalibrasyonu'),
                'brand_eyebrow' => 'Refraktometre markaları',
                'list_title' => 'Refraktometre modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Ölçümleri İçin Refraktometreler',
                        'text' => 'Refraktometreler; gıda, kimya, ilaç, petrokimya ve kalite kontrol laboratuvarlarında numune konsantrasyonu ve kırılma indisi ölçümleri için kullanılır. Ölçüm güvenilirliği; cihazın doğruluğu, sıcaklık kontrolü, numune hazırlığı ve düzenli bakım süreçleriyle doğrudan ilişkilidir.',
                    ],
                    [
                        'title' => 'Dijital Refraktometre Nedir?',
                        'text' => 'Dijital refraktometre, numunenin kırılma indisi veya Brix değerini elektronik ölçüm sistemiyle gösteren cihazdır. Analog cihazlara göre daha hızlı okuma, daha kolay veri takibi ve kullanıcı hatasını azaltan ölçüm süreçleri sunabilir. Doğru model seçimi, ölçülecek numunenin yapısına ve beklenen hassasiyete göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Refraktometre Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Refraktometre seçiminde ölçüm aralığı, doğruluk, çözünürlük, sıcaklık kompanzasyonu, numune hacmi, temizleme kolaylığı ve veri aktarım özellikleri değerlendirilmelidir. Brix ölçümü, kırılma indisi ölçümü veya farklı konsantrasyon uygulamaları için cihazın teknik özellikleri uygulama ihtiyacına uygun olmalıdır.',
                    ],
                    [
                        'title' => 'Refraktometrelerde Sıcaklık Etkisi ve Kalibrasyon',
                        'text' => 'Refraktometre ölçümlerinde sıcaklık, kırılma indisi ve Brix sonucunu etkileyebilir. Bu nedenle cihazın sıcaklık kompanzasyonu, ölçüm ortamı ve düzenli kontrol süreçleri önemlidir. İlgili cihazlar, ihtiyaç halinde sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis süreçleriyle ilişkilendirilebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Kyoto KEM, Mettler Toledo ve Bellingham + Stanley Refraktometre Modelleri',
                    'text' => 'MTA Endüstri refraktometre kategorisinde Kyoto KEM, Mettler Toledo ve Bellingham + Stanley markalarına ait laboratuvar tipi ve dijital refraktometre modelleri listelenir. Kullanıcılar ürünleri marka, model, ölçüm parametresi ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Refraktometre Kullanım Alanları',
                    'text' => 'Refraktometreler; şeker oranı, konsantrasyon, kırılma indisi ve kalite kontrol ölçümlerinde kullanılır. Gıda ve içecek analizleri, kimyasal çözelti kontrolü, ilaç üretimi, petrokimya uygulamaları ve AR-GE çalışmalarında farklı numune tipleri için tercih edilebilir.',
                    'items' => [
                        'Gıda ve içecek kalite kontrolü',
                        'Kimyasal çözelti analizleri',
                        'İlaç ve kozmetik laboratuvarları',
                        'Petrokimya uygulamaları',
                        'AR-GE ve ürün geliştirme süreçleri',
                        'Brix ve konsantrasyon ölçümleri',
                        'Kırılma indisi ölçümleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Refraktometre İçin Teklif Alın',
                    'text' => 'Laboratuvarınız veya kalite kontrol süreciniz için uygun refraktometre modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Ölçüm parametresi, numune tipi, marka tercihi ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun dijital refraktometre veya laboratuvar refraktometresi için teknik ekibe ulaşın.',
                    'button' => 'Refraktometre İçin Teklif Al',
                    'anchor' => 'refraktometre teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'brand_alt_texts' => [
                    'kyoto-kem' => 'Kyoto KEM refraktometre marka logosu',
                    'mettler-toledo' => 'Mettler Toledo refraktometre marka logosu',
                    'bellingham-stanley' => 'Bellingham + Stanley refraktometre marka logosu',
                ],
                'brand_anchors' => [
                    'kyoto-kem' => 'Kyoto KEM refraktometre modelleri',
                    'mettler-toledo' => 'Mettler Toledo refraktometre modelleri',
                    'bellingham-stanley' => 'Bellingham + Stanley refraktometre modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Refraktometre ne işe yarar?',
                        'answer' => 'Refraktometre, sıvı numunelerin kırılma indisi, Brix veya konsantrasyon değerlerini ölçmek için kullanılır. Gıda, kimya, ilaç ve kalite kontrol laboratuvarlarında yaygın olarak tercih edilir.',
                    ],
                    [
                        'question' => 'Dijital refraktometre seçerken nelere dikkat edilmeli?',
                        'answer' => 'Ölçüm aralığı, doğruluk, sıcaklık kompanzasyonu, numune tipi, veri aktarımı ve temizleme kolaylığı dijital refraktometre seçiminde değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Refraktometre ölçümünde sıcaklık neden önemlidir?',
                        'answer' => 'Sıcaklık değişimi kırılma indisi ve Brix değerlerini etkileyebilir. Bu nedenle sıcaklık kompanzasyonu ve kontrollü ölçüm koşulları güvenilir sonuçlar için önemlidir.',
                    ],
                    [
                        'question' => 'Refraktometre için teknik servis gerekir mi?',
                        'answer' => 'Ölçüm sapması, ekran hatası, numune haznesi sorunu, sıcaklık sensörü problemi veya tekrarlanabilirlik sorunu varsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'densitometre' => [
                'slug' => 'densitometre',
                'meta_title' => 'Densitometre ve Yoğunluk Ölçer Modelleri | MTA Endüstri',
                'meta_description' => 'Densitometre, yoğunluk ölçer ve özgül ağırlık ölçer modellerini Kyoto KEM, Mettler Toledo ve Bellingham + Stanley markalarıyla inceleyin.',
                'h1' => 'Densitometre ve Yoğunluk Ölçer Modelleri',
                'hero_text' => 'Densitometreler; sıvı numunelerde yoğunluk ve özgül ağırlık ölçümü için kullanılan laboratuvar ölçüm cihazlarıdır. MTA Endüstri densitometre kategorisinde Kyoto KEM, Mettler Toledo ve Bellingham + Stanley markalarına ait dijital yoğunluk ölçer modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Densitometre İçin Teklif Al',
                'secondary_cta' => 'Refraktometre Modellerini İncele',
                'secondary_cta_url' => route('products.category', 'refraktometre'),
                'brand_eyebrow' => 'Densitometre markaları',
                'list_title' => 'Densitometre modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Yoğunluk Ölçümleri İçin Densitometreler',
                        'text' => 'Densitometreler; kalite kontrol, AR-GE, üretim destek ve analiz laboratuvarlarında sıvı numunelerin yoğunluk değerini belirlemek için kullanılır. Yoğunluk ölçümü; ürün standardizasyonu, proses kontrolü, hammadde kabulü ve formülasyon çalışmalarında önemli bir parametre olabilir.',
                    ],
                    [
                        'title' => 'Densitometre Ne İşe Yarar?',
                        'text' => 'Densitometre, sıvı numunenin yoğunluk veya özgül ağırlık değerini ölçmek için kullanılır. Ölçüm sonucu; numunenin bileşimi, konsantrasyonu, sıcaklık koşulları ve proses uygunluğu hakkında değerlendirme yapılmasına yardımcı olur.',
                    ],
                    [
                        'title' => 'Yoğunluk Ölçer Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Yoğunluk ölçer seçiminde ölçüm aralığı, doğruluk, çözünürlük, sıcaklık kontrolü, numune hacmi, otomatik numune besleme uyumu, veri aktarımı ve temizlik kolaylığı birlikte değerlendirilmelidir.',
                    ],
                    [
                        'title' => 'Dijital Densitometre ve Özgül Ağırlık Ölçer Kullanımı',
                        'text' => 'Dijital densitometreler, yoğunluk ve özgül ağırlık ölçümlerinin daha kontrollü ve tekrarlanabilir şekilde yapılmasına yardımcı olur. Rutin kalite kontrol analizlerinde hızlı sonuç, düşük numune hacmi, veri kaydı ve sıcaklık kontrollü ölçüm özellikleri avantaj sağlayabilir.',
                    ],
                    [
                        'title' => 'Densitometrelerde Sıcaklık Etkisi ve Kalibrasyon',
                        'text' => 'Yoğunluk ölçümleri sıcaklık değişimlerinden etkilenebilir. Bu nedenle densitometrelerde sıcaklık kontrolü, ölçüm ortamı ve numune hazırlığı güvenilir sonuçlar için önemlidir. Cihazın sıcaklık performansı sıcaklık kalibrasyonu; hacimsel ölçüm süreçleri ise hacim kalibrasyonu ile ilişkilendirilebilir.',
                    ],
                    [
                        'title' => 'Densitometre Teknik Servis ve Bakım',
                        'text' => 'Densitometrelerde ölçüm hücresi, sıcaklık sensörü, ekran, elektronik kontrol sistemi, pompa veya numune besleme bileşenlerinde sorunlar görülebilir. Ölçüm değerinin stabil olmaması veya numune geçişinde problem yaşanması teknik servis değerlendirmesi gerektirebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Kyoto KEM, Mettler Toledo ve Bellingham + Stanley Densitometre Modelleri',
                    'text' => 'MTA Endüstri densitometre kategorisinde Kyoto KEM, Mettler Toledo ve Bellingham + Stanley markalarına ait farklı uygulama ihtiyaçlarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, ölçüm aralığı, doğruluk ve teknik özelliklerine göre inceleyebilir.',
                ],
                'list_section' => [
                    'title' => 'Densitometre Kullanım Alanları',
                    'text' => 'Densitometreler; kimya, gıda, içecek, ilaç, kozmetik, petrokimya ve AR-GE laboratuvarlarında kullanılabilir. Yoğunluk ve özgül ağırlık ölçümü; ürün kalitesi, karışım oranı, konsantrasyon takibi ve proses kontrolü açısından önemlidir.',
                    'items' => [
                        'Gıda ve içecek kalite kontrolü',
                        'Kimyasal çözelti analizleri',
                        'İlaç ve kozmetik laboratuvarları',
                        'Petrokimya ve yağ numuneleri',
                        'Hammadde kabul kontrolleri',
                        'AR-GE ve formülasyon çalışmaları',
                        'Konsantrasyon ve özgül ağırlık ölçümleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Densitometre İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda yoğunluk ölçümü, özgül ağırlık takibi veya kalite kontrol uygulamaları için uygun densitometre modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Numune tipi, ölçüm aralığı, sıcaklık ihtiyacı, marka tercihi ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun dijital densitometre veya yoğunluk ölçer modeli için teknik ekibe ulaşın.',
                    'button' => 'Densitometre İçin Teklif Al',
                    'anchor' => 'densitometre teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre modelleri'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('services.show', 'hacim-kalibrasyonu'), 'anchor' => 'hacim kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'brand_alt_texts' => [
                    'kyoto-kem' => 'Kyoto KEM densitometre marka logosu',
                    'mettler-toledo' => 'Mettler Toledo yoğunluk ölçer marka logosu',
                    'bellingham-stanley' => 'Bellingham + Stanley densitometre marka logosu',
                ],
                'brand_anchors' => [
                    'kyoto-kem' => 'Kyoto KEM densitometre modelleri',
                    'mettler-toledo' => 'Mettler Toledo densitometre modelleri',
                    'bellingham-stanley' => 'Bellingham + Stanley yoğunluk ölçer modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Densitometre ne işe yarar?',
                        'answer' => 'Densitometre, sıvı numunelerin yoğunluk veya özgül ağırlık değerini ölçmek için kullanılır. Kalite kontrol, AR-GE ve proses takibi süreçlerinde tercih edilir.',
                    ],
                    [
                        'question' => 'Yoğunluk ölçer seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Ölçüm aralığı, doğruluk, çözünürlük, sıcaklık kontrolü, numune hacmi, veri aktarımı ve temizlik kolaylığı birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Densitometre ölçümünde sıcaklık neden önemlidir?',
                        'answer' => 'Sıcaklık değişimi sıvı yoğunluğunu etkileyebilir. Bu nedenle sıcaklık kontrolü ve ölçüm koşullarının tekrarlanabilir olması güvenilir sonuçlar için önemlidir.',
                    ],
                    [
                        'question' => 'Densitometre ile refraktometre arasındaki fark nedir?',
                        'answer' => 'Densitometre yoğunluk ve özgül ağırlık ölçümü yaparken, refraktometre kırılma indisi ve Brix gibi değerleri ölçer. Uygulamaya göre iki cihaz birlikte de değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Densitometre teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Ölçüm değeri stabil değilse, cihaz hata veriyorsa, numune hücresinde veya sıcaklık kontrolünde sorun varsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'manyetik-karistirici' => [
                'slug' => 'manyetik-karistirici',
                'meta_title' => 'Manyetik Karıştırıcı Modelleri | Isıtmalı ve Isıtmasız | MTA Endüstri',
                'meta_description' => 'Isıtmalı, ısıtmasız ve çok pozisyonlu manyetik karıştırıcı modellerini VELP ve Weightlab markalarıyla inceleyin; teklif alın.',
                'h1' => 'Manyetik Karıştırıcı Modelleri',
                'hero_text' => 'Manyetik karıştırıcılar; laboratuvarlarda sıvı numunelerin kontrollü karıştırılması, çözelti hazırlanması ve ısıtmalı karıştırma uygulamaları için kullanılan temel numune hazırlama cihazlarıdır. MTA Endüstri manyetik karıştırıcı kategorisinde VELP ve Weightlab markalarına ait ısıtmalı, ısıtmasız ve çok pozisyonlu manyetik karıştırıcı modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Manyetik Karıştırıcı İçin Teklif Al',
                'secondary_cta' => 'Devir Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'devir-kalibrasyonu'),
                'brand_eyebrow' => 'Manyetik karıştırıcı markaları',
                'list_title' => 'Manyetik karıştırıcı modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Numune Hazırlama İçin Manyetik Karıştırıcılar',
                        'text' => 'Manyetik karıştırıcılar, sıvı numunelerin homojen karışım elde edecek şekilde karıştırılması için kullanılır. Laboratuvarlarda çözelti hazırlama, reaksiyon takibi, ısıtmalı karıştırma ve rutin numune hazırlama süreçlerinde tercih edilir. Doğru cihaz seçimi, karıştırılacak hacim, numune viskozitesi, hız aralığı ve sıcaklık ihtiyacına göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Isıtmalı ve Isıtmasız Manyetik Karıştırıcı Arasındaki Fark',
                        'text' => 'Isıtmasız manyetik karıştırıcılar yalnızca karıştırma işlemi için kullanılırken, ısıtmalı manyetik karıştırıcılar karıştırma ile birlikte kontrollü sıcaklık uygulaması sağlar. Isıtmalı modeller; kimyasal çözelti hazırlama, reaksiyon süreçleri ve sıcaklık kontrollü laboratuvar uygulamaları için tercih edilebilir. Uygulama ihtiyacına göre tabla sıcaklığı, sıcaklık kontrol hassasiyeti ve güvenlik özellikleri değerlendirilmelidir.',
                    ],
                    [
                        'title' => 'Manyetik Karıştırıcı Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Manyetik karıştırıcı seçiminde maksimum karıştırma hacmi, hız aralığı, sıcaklık kapasitesi, tabla malzemesi, kontrol tipi, dijital gösterge, güvenlik fonksiyonları ve kullanım yoğunluğu dikkate alınmalıdır. Numunenin viskozitesi ve çalışma süresi de cihaz performansını etkileyen önemli kriterlerdir.',
                    ],
                    [
                        'title' => 'Çok Pozisyonlu ve Dijital Manyetik Karıştırıcılar',
                        'text' => 'Çok pozisyonlu manyetik karıştırıcılar aynı anda birden fazla numunenin karıştırılması gereken laboratuvarlarda verimli kullanım sağlar. Dijital manyetik karıştırıcılar ise hız ve sıcaklık değerlerinin daha net takip edilmesine yardımcı olur. Bu cihazlar özellikle rutin analiz, seri numune hazırlama ve tekrarlanabilir çalışma koşulları için tercih edilebilir.',
                    ],
                    [
                        'title' => 'Manyetik Karıştırıcılarda Devir ve Sıcaklık Kontrolü',
                        'text' => 'Manyetik karıştırıcılarda karıştırma hızı ve sıcaklık kontrolü, numune hazırlama sürecinin tekrarlanabilirliği açısından önemlidir. Hız kontrolüyle ilgili doğrulama ihtiyaçları devir kalibrasyonu; ısıtmalı modellerde sıcaklık kontrolüyle ilgili ihtiyaçlar ise sıcaklık kalibrasyonu süreçleriyle ilişkilendirilebilir.',
                    ],
                    [
                        'title' => 'Manyetik Karıştırıcı Teknik Servis ve Bakım',
                        'text' => 'Manyetik karıştırıcılarda motor, tabla, sıcaklık sensörü, kontrol paneli, elektronik kart veya güç bağlantısı kaynaklı sorunlar görülebilir. Hız değerinin stabil olmaması, ısıtma performansının düşmesi, cihazın çalışmaması veya ekran hataları teknik servis değerlendirmesi gerektirebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'VELP ve Weightlab Manyetik Karıştırıcı Modelleri',
                    'text' => 'MTA Endüstri manyetik karıştırıcı kategorisinde VELP ve Weightlab markalarına ait farklı laboratuvar uygulamalarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, ısıtma özelliği, hız aralığı ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Manyetik Karıştırıcı Kullanım Alanları',
                    'text' => 'Manyetik karıştırıcılar çözelti hazırlama, reaksiyon takibi, seri numune hazırlama ve sıcaklık kontrollü laboratuvar uygulamalarında kullanılır. Cihaz seçimi uygulama hacmi, karıştırma hızı ve sıcaklık ihtiyacına göre netleştirilmelidir.',
                    'items' => [
                        'Çözelti hazırlama süreçleri',
                        'Isıtmalı karıştırma uygulamaları',
                        'Rutin numune hazırlama',
                        'Seri laboratuvar analizleri',
                        'Kimyasal reaksiyon takibi',
                        'Kalite kontrol laboratuvarları',
                        'AR-GE uygulamaları',
                    ],
                ],
                'cta' => [
                    'title' => 'Manyetik Karıştırıcı İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda çözelti hazırlama, numune karıştırma veya sıcaklık kontrollü karıştırma uygulamaları için uygun manyetik karıştırıcı modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Marka, model, karıştırma hacmi, sıcaklık ihtiyacı ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun ısıtmalı, ısıtmasız veya çok pozisyonlu manyetik karıştırıcı için teknik ekibe ulaşın.',
                    'button' => 'Manyetik Karıştırıcı İçin Teklif Al',
                    'anchor' => 'manyetik karıştırıcı teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.category', 'mekanik-karistirici'), 'anchor' => 'mekanik karıştırıcı modelleri'],
                ],
                'brand_alt_texts' => [
                    'velp' => 'VELP manyetik karıştırıcı marka logosu',
                    'weightlab' => 'Weightlab manyetik karıştırıcı marka logosu',
                ],
                'brand_anchors' => [
                    'velp' => 'VELP manyetik karıştırıcı modelleri',
                    'weightlab' => 'Weightlab manyetik karıştırıcı modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Manyetik karıştırıcı ne işe yarar?',
                        'answer' => 'Manyetik karıştırıcı, sıvı numunelerin manyetik balık yardımıyla homojen şekilde karıştırılmasını sağlar. Çözelti hazırlama, reaksiyon takibi ve rutin laboratuvar uygulamalarında kullanılır.',
                    ],
                    [
                        'question' => 'Isıtmalı manyetik karıştırıcı ne zaman tercih edilir?',
                        'answer' => 'Karıştırma işlemiyle birlikte sıcaklık kontrolü gerekiyorsa ısıtmalı manyetik karıştırıcı tercih edilir. Kimyasal çözelti hazırlama ve sıcaklık kontrollü reaksiyonlarda kullanışlıdır.',
                    ],
                    [
                        'question' => 'Manyetik karıştırıcı seçerken hangi özelliklere bakılmalı?',
                        'answer' => 'Karıştırma hacmi, hız aralığı, sıcaklık kapasitesi, tabla malzemesi, kontrol tipi, güvenlik özellikleri ve numune viskozitesi birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Manyetik karıştırıcı için kalibrasyon gerekir mi?',
                        'answer' => 'Karıştırma hızının doğrulanması gerekiyorsa devir kalibrasyonu; ısıtmalı modellerde sıcaklık kontrolünün doğrulanması gerekiyorsa sıcaklık kalibrasyonu değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Manyetik karıştırıcı teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Cihaz çalışmıyorsa, hız değeri stabil değilse, ısıtma yapmıyorsa, ekran veya kontrol paneli hatası varsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'homojenizator' => [
                'slug' => 'homojenizator',
                'meta_title' => 'Homojenizatör Modelleri ve Laboratuvar Homojenizatörleri | MTA Endüstri',
                'meta_description' => 'Laboratuvar homojenizatörü ve numune hazırlama cihazlarını VELP ve Weightlab markalarıyla inceleyin; teknik özelliklere göre teklif alın.',
                'h1' => 'Homojenizatör Modelleri ve Laboratuvar Homojenizatörleri',
                'hero_text' => 'Homojenizatörler; laboratuvarlarda numunelerin parçalanması, karıştırılması, dağıtılması ve homojen hale getirilmesi için kullanılan numune hazırlama cihazlarıdır. MTA Endüstri homojenizatör kategorisinde VELP ve Weightlab markalarına ait farklı hacim, hız ve uygulama ihtiyaçlarına uygun laboratuvar homojenizatörü modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Homojenizatör İçin Teklif Al',
                'secondary_cta' => 'Devir Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'devir-kalibrasyonu'),
                'brand_eyebrow' => 'Homojenizatör markaları',
                'list_title' => 'Homojenizatör modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Numune Hazırlama İçin Homojenizatörler',
                        'text' => 'Homojenizatörler; gıda, kimya, ilaç, biyoteknoloji, kozmetik ve AR-GE laboratuvarlarında numune hazırlama süreçlerinde kullanılır. Numunenin yapısına göre parçalama, dağıtma, emülsiyon oluşturma veya homojen karışım elde etme amacıyla tercih edilebilir. Doğru cihaz seçimi, uygulama ihtiyacına ve numune özelliklerine göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Homojenizatör Ne İşe Yarar?',
                        'text' => 'Homojenizatör, farklı yoğunluk veya yapıya sahip numunelerin daha dengeli ve tekrarlanabilir bir karışım haline getirilmesini sağlar. Katı-sıvı karışımlar, süspansiyonlar, emülsiyonlar ve viskoz numuneler için uygun başlık ve hız aralığı seçilerek numune hazırlama süreci kontrol altına alınabilir.',
                    ],
                    [
                        'title' => 'Homojenizatör Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Homojenizatör seçiminde numune hacmi, numune viskozitesi, hız aralığı, motor gücü, prob veya başlık uyumu, çalışma süresi, temizlik kolaylığı ve güvenlik özellikleri değerlendirilmelidir. Kullanılacak uygulamanın parçalama mı, karıştırma mı yoksa emülsiyon oluşturma mı olduğu cihaz seçiminde belirleyici olur.',
                    ],
                    [
                        'title' => 'Rotor-Stator, El Tipi ve Tezgah Tipi Homojenizatörler',
                        'text' => 'Rotor-stator homojenizatörler yüksek hızlı parçalama ve dağıtma uygulamaları için tercih edilir. El tipi modeller daha düşük hacimli ve esnek kullanım gerektiren işlemler için uygundur. Tezgah tipi homojenizatörler ise daha kontrollü, tekrarlanabilir ve yoğun kullanım gerektiren laboratuvar süreçlerinde avantaj sağlar.',
                    ],
                    [
                        'title' => 'Homojenizatör Kullanım Alanları',
                        'text' => 'Homojenizatörler gıda analizleri, kimyasal numune hazırlama, kozmetik formülasyon, ilaç laboratuvarları, biyoteknoloji uygulamaları ve kalite kontrol süreçlerinde kullanılabilir. Numune yapısı ve hedeflenen analiz yöntemi, cihaz ve aksesuar seçimini doğrudan etkiler.',
                    ],
                    [
                        'title' => 'Homojenizatörlerde Devir Kontrolü ve Teknik Servis',
                        'text' => 'Homojenizatörlerde hız kontrolü, motor performansı ve başlık durumu numune hazırlama kalitesini etkiler. Hız değerinin doğrulanması gereken uygulamalarda devir kalibrasyonu değerlendirilebilir. Motor, prob, başlık, bağlantı veya elektronik kontrol sorunlarında laboratuvar cihazları teknik servis desteği gerekebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'VELP ve Weightlab Homojenizatör Modelleri',
                    'text' => 'MTA Endüstri homojenizatör kategorisinde VELP ve Weightlab markalarına ait farklı numune hazırlama uygulamalarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, hız aralığı, hacim kapasitesi ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Homojenizatör Kullanım Alanları',
                    'text' => 'Homojenizatörler farklı sektörlerde numune hazırlama, emülsiyon, dispersiyon, parçalama ve homojen karışım elde etme süreçleri için tercih edilir. Uygulama alanı, cihaz tipi ve başlık seçimi üzerinde doğrudan belirleyicidir.',
                    'items' => [
                        'Gıda laboratuvarlarında numune hazırlama',
                        'Kimyasal çözelti ve süspansiyon hazırlama',
                        'Kozmetik ve ilaç formülasyon çalışmaları',
                        'Biyoteknoloji ve AR-GE uygulamaları',
                        'Kalite kontrol numune hazırlığı',
                        'Emülsiyon ve dispersiyon işlemleri',
                        'Viskoz numunelerin karıştırılması',
                    ],
                ],
                'cta' => [
                    'title' => 'Homojenizatör İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda numune parçalama, karıştırma, emülsiyon hazırlama veya homojen karışım elde etme uygulamaları için uygun homojenizatör modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Numune tipi, hacim, viskozite, hız ihtiyacı ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun laboratuvar homojenizatörü veya numune hazırlama cihazı için teknik ekibe ulaşın.',
                    'button' => 'Homojenizatör İçin Teklif Al',
                    'anchor' => 'homojenizatör teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.category', 'mekanik-karistirici'), 'anchor' => 'mekanik karıştırıcı modelleri'],
                    ['url' => route('products.category', 'manyetik-karistirici'), 'anchor' => 'manyetik karıştırıcı modelleri'],
                ],
                'brand_alt_texts' => [
                    'velp' => 'VELP homojenizatör marka logosu',
                    'weightlab' => 'Weightlab homojenizatör marka logosu',
                ],
                'brand_anchors' => [
                    'velp' => 'VELP homojenizatör modelleri',
                    'weightlab' => 'Weightlab homojenizatör modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Homojenizatör ne işe yarar?',
                        'answer' => 'Homojenizatör, laboratuvar numunelerinin parçalanması, dağıtılması, karıştırılması veya homojen hale getirilmesi için kullanılır. Gıda, kimya, ilaç, kozmetik ve AR-GE uygulamalarında tercih edilir.',
                    ],
                    [
                        'question' => 'Homojenizatör seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Numune hacmi, viskozite, hız aralığı, motor gücü, prob/başlık uyumu, temizlik kolaylığı ve kullanım yoğunluğu birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Rotor-stator homojenizatör hangi uygulamalarda kullanılır?',
                        'answer' => 'Rotor-stator homojenizatörler yüksek hızlı parçalama, dispersiyon, emülsiyon ve yoğun karıştırma uygulamalarında tercih edilir.',
                    ],
                    [
                        'question' => 'Homojenizatör için kalibrasyon gerekir mi?',
                        'answer' => 'Hız değerinin doğrulanması gereken uygulamalarda devir kalibrasyonu değerlendirilebilir. Ayrıca cihaz performansı düzenli teknik kontrollerle takip edilmelidir.',
                    ],
                    [
                        'question' => 'Homojenizatör teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Motor performansı düşüyorsa, hız kontrolü stabil değilse, prob veya başlık bağlantısında sorun varsa, cihaz çalışmıyor ya da anormal ses oluşturuyorsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'viskozimetre' => [
                'slug' => 'viskozimetre',
                'meta_title' => 'Viskozimetre Modelleri ve Viskozite Ölçüm Cihazları | MTA Endüstri',
                'meta_description' => 'Viskozimetre ve dijital viskozite ölçüm cihazlarını Lamy markasıyla inceleyin; teknik özelliklere göre teklif alın.',
                'h1' => 'Viskozimetre Modelleri ve Viskozite Ölçüm Cihazları',
                'hero_text' => 'Viskozimetreler; sıvı, yarı akışkan ve viskoz numunelerin akış davranışını değerlendirmek için kullanılan laboratuvar ölçüm cihazlarıdır. MTA Endüstri viskozimetre kategorisinde Lamy markasına ait dijital, rotasyonel ve laboratuvar tipi viskozite ölçüm cihazlarını teknik özellikleriyle listeler.',
                'primary_cta' => 'Viskozimetre İçin Teklif Al',
                'secondary_cta' => 'İlgili Kalibrasyon Hizmetlerini İncele',
                'secondary_cta_url' => route('services.show', 'devir-kalibrasyonu'),
                'brand_eyebrow' => 'Viskozimetre markaları',
                'list_title' => 'Viskozimetre modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar ve Kalite Kontrol İçin Viskozimetreler',
                        'text' => 'Viskozimetreler; gıda, kimya, boya, kozmetik, ilaç, polimer ve petrokimya sektörlerinde kalite kontrol ve AR-GE süreçlerinde kullanılır. Numunenin akışkanlık davranışı, ürün performansı ve proses kontrolü açısından kritik olabilir. Doğru viskozimetre seçimi, numune tipi ve ölçüm metoduna göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Viskozimetre Ne İşe Yarar?',
                        'text' => 'Viskozimetre, bir sıvının veya yarı akışkan numunenin akmaya karşı gösterdiği direnci ölçmek için kullanılır. Viskozite değeri; ürün kıvamı, proses tekrarlanabilirliği, kalite kontrol kriterleri ve formülasyon geliştirme çalışmalarında önemli bir parametredir.',
                    ],
                    [
                        'title' => 'Rotasyonel ve Dijital Viskozimetre Modelleri',
                        'text' => 'Rotasyonel viskozimetreler, spindle veya ölçüm geometrisi üzerinden numunenin viskozite davranışını değerlendirir. Dijital viskozimetreler ölçüm değerlerinin okunması, takip edilmesi ve tekrarlanabilir analiz süreçlerinde kolaylık sağlayabilir. Cihaz seçimi; hız aralığı, spindle uyumu, numune hacmi ve sıcaklık koşullarına göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Viskozimetre Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Viskozimetre seçiminde ölçüm aralığı, hız seçenekleri, spindle seti, numune sıcaklığı, doğruluk, veri aktarımı, kullanım kolaylığı ve uygulama standardı birlikte değerlendirilmelidir. Düşük viskoziteli, yüksek viskoziteli veya non-Newtonian davranış gösteren numuneler için uygun cihaz ve aksesuar seçimi önemlidir.',
                    ],
                    [
                        'title' => 'Viskozimetrelerde Sıcaklık ve Devir Kontrolü',
                        'text' => 'Viskozite ölçümleri sıcaklık ve hız koşullarından doğrudan etkilenebilir. Bu nedenle viskozimetre kullanımında sıcaklık kontrolü, spindle seçimi ve devir ayarlarının doğru değerlendirilmesi gerekir. Hız doğrulama ihtiyaçları devir kalibrasyonu; sıcaklık kontrollü uygulamalar ise sıcaklık kalibrasyonu ile ilişkilendirilebilir.',
                    ],
                    [
                        'title' => 'Viskozimetre Teknik Servis ve Bakım',
                        'text' => 'Viskozimetrelerde motor, spindle bağlantısı, kontrol paneli, ekran, sıcaklık probu veya elektronik bileşen kaynaklı sorunlar görülebilir. Ölçüm değerinin stabil olmaması, spindle hareketinde problem yaşanması, cihazın hata vermesi veya tekrarlanabilir sonuç alınamaması teknik servis değerlendirmesi gerektirebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Lamy Viskozimetre Modelleri',
                    'text' => 'MTA Endüstri viskozimetre kategorisinde Lamy markasına ait farklı uygulama ihtiyaçlarına uygun modeller listelenir. Kullanıcılar ürünleri marka, model, ölçüm aralığı, hız seçenekleri ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Viskozimetre Kullanım Alanları',
                    'text' => 'Viskozimetreler; ürün kıvamı, formülasyon kararlılığı, proses kontrolü ve kalite değerlendirme süreçlerinde kullanılabilir. Numune tipi ve uygulama standardı, cihaz ve aksesuar seçiminde belirleyicidir.',
                    'items' => [
                        'Gıda ve içecek kalite kontrolü',
                        'Boya, kaplama ve mürekkep uygulamaları',
                        'Kozmetik ve kişisel bakım ürünleri',
                        'İlaç ve formülasyon çalışmaları',
                        'Polimer ve kimyasal çözeltiler',
                        'Yağ ve petrokimya numuneleri',
                        'AR-GE ve proses kontrol çalışmaları',
                    ],
                ],
                'cta' => [
                    'title' => 'Viskozimetre İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda viskozite ölçümü, kalite kontrol veya AR-GE uygulamaları için uygun viskozimetre modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Numune tipi, viskozite aralığı, sıcaklık ihtiyacı, marka tercihi ve kullanım alanı bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun Lamy veya laboratuvar tipi viskozimetre için teknik ekibe ulaşın.',
                    'button' => 'Viskozimetre İçin Teklif Al',
                    'anchor' => 'viskozimetre teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'devir-kalibrasyonu'), 'anchor' => 'devir kalibrasyonu'],
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'brand_alt_texts' => [
                    'lamy' => 'Lamy viskozimetre marka logosu',
                ],
                'brand_anchors' => [
                    'lamy' => 'Lamy viskozimetre modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Viskozimetre ne işe yarar?',
                        'answer' => 'Viskozimetre, sıvı veya yarı akışkan numunelerin akmaya karşı gösterdiği direnci ölçmek için kullanılır. Viskozite ölçümü kalite kontrol, formülasyon ve proses takibi için önemlidir.',
                    ],
                    [
                        'question' => 'Viskozimetre seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Ölçüm aralığı, hız seçenekleri, spindle uyumu, numune sıcaklığı, doğruluk, veri aktarımı ve uygulama standardı birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Viskozite ölçümünde sıcaklık neden önemlidir?',
                        'answer' => 'Sıcaklık değişimi numunenin akış davranışını doğrudan etkileyebilir. Bu nedenle sıcaklık kontrolü ve ölçüm koşullarının tekrarlanabilir olması güvenilir sonuçlar için önemlidir.',
                    ],
                    [
                        'question' => 'Viskozimetre için kalibrasyon gerekir mi?',
                        'answer' => 'Hız doğrulama ihtiyaçları devir kalibrasyonu, sıcaklık kontrollü uygulamalar ise sıcaklık kalibrasyonu kapsamında değerlendirilebilir. Ayrıca cihazın teknik performansı düzenli servis kontrolleriyle takip edilmelidir.',
                    ],
                ],
            ],
            'etuv' => [
                'slug' => 'etuv',
                'meta_title' => 'Etüv Cihazı Modelleri ve Laboratuvar Etüvleri | MTA Endüstri',
                'meta_description' => 'Laboratuvar etüvü ve kurutma etüvü modellerini teknik özellikleriyle inceleyin; sıcaklık kontrolü ve kullanım ihtiyacınıza göre teklif alın.',
                'h1' => 'Etüv Cihazı Modelleri ve Laboratuvar Etüvleri',
                'hero_text' => 'Etüv cihazları; laboratuvarlarda kurutma, ısıtma, numune hazırlama ve sıcaklık kontrollü işlemler için kullanılan temel cihazlardır. MTA Endüstri etüv kategorisinde farklı hacim, sıcaklık aralığı ve kontrol özelliklerine sahip laboratuvar etüvü modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Etüv Cihazı İçin Teklif Al',
                'secondary_cta' => 'Sıcaklık Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'sicaklik-kalibrasyonu'),
                'brand_eyebrow' => 'Etüv markaları',
                'list_title' => 'Etüv cihazı modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Kurutma İşlemleri İçin Etüv Cihazları',
                        'text' => 'Etüv cihazları; kalite kontrol, AR-GE, üretim destek ve analiz laboratuvarlarında numune kurutma, malzeme şartlandırma ve sıcaklık kontrollü bekletme işlemleri için kullanılır. Doğru etüv seçimi, işlem sıcaklığı, numune hacmi, sıcaklık homojenliği ve kullanım sıklığına göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Etüv Cihazı Ne İşe Yarar?',
                        'text' => 'Etüv cihazı, belirlenen sıcaklık değerinde kontrollü ısıtma veya kurutma işlemi yapmak için kullanılır. Numunenin neminin uzaklaştırılması, malzemenin belirli sıcaklıkta bekletilmesi veya laboratuvar süreçlerinde tekrarlanabilir sıcaklık koşullarının sağlanması amacıyla tercih edilir.',
                    ],
                    [
                        'title' => 'Etüv Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Etüv seçiminde iç hacim, sıcaklık aralığı, sıcaklık homojenliği, kontrol hassasiyeti, hava sirkülasyonu, raf yapısı, güvenlik özellikleri ve kullanım yoğunluğu birlikte değerlendirilmelidir. Numune tipi ve uygulama süresi de cihaz seçiminde belirleyici olur.',
                    ],
                    [
                        'title' => 'Kurutma Etüvü ve Sıcaklık Kontrollü Laboratuvar Uygulamaları',
                        'text' => 'Kurutma etüvleri, numunelerdeki nemin kontrollü şekilde uzaklaştırılması için kullanılır. Sıcaklık kontrollü etüv uygulamaları ise numune hazırlama, malzeme testleri, stabilite değerlendirmeleri ve kalite kontrol süreçlerinde önemli rol oynar. Uygulamanın hassasiyetine göre sıcaklık dağılımı ve kontrol performansı dikkate alınmalıdır.',
                    ],
                    [
                        'title' => 'Etüvlerde Sıcaklık Kalibrasyonu ve Teknik Servis',
                        'text' => 'Etüv cihazlarında sıcaklık kontrolü ve kabin içi sıcaklık dağılımı ölçüm güvenilirliği açısından önemlidir. Sıcaklık performansının doğrulanması gereken durumlarda sıcaklık kalibrasyonu değerlendirilebilir. Isıtma problemi, sensör hatası, kontrol paneli arızası, fan veya hava sirkülasyonu sorunu varsa laboratuvar cihazları teknik servis desteği gerekebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'Weightlab Etüv Modelleri',
                    'text' => 'MTA Endüstri etüv kategorisinde Weightlab markasına ait laboratuvar kullanımına uygun etüv modelleri listelenir. Kullanıcılar ürünleri model, hacim, sıcaklık aralığı ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Etüv Kullanım Alanları',
                    'text' => 'Etüv cihazları; gıda, kimya, ilaç, plastik, akademik araştırma, medikal ve kalite kontrol laboratuvarlarında kullanılabilir. Numune kurutma, malzeme şartlandırma, ısıtma ve kontrollü bekletme işlemleri yaygın kullanım alanları arasındadır.',
                    'items' => [
                        'Numune kurutma işlemleri',
                        'Gıda ve kimya laboratuvarları',
                        'İlaç ve medikal kalite kontrol süreçleri',
                        'Plastik ve malzeme testleri',
                        'AR-GE laboratuvarları',
                        'Akademik araştırma laboratuvarları',
                        'Sıcaklık kontrollü bekletme işlemleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Etüv Cihazı İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda kurutma, ısıtma veya sıcaklık kontrollü işlem ihtiyacı için uygun etüv cihazını seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Hacim, sıcaklık aralığı, kullanım amacı, marka tercihi ve adet bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun laboratuvar etüvü veya kurutma etüvü için teknik ekibe ulaşın.',
                    'button' => 'Etüv Cihazı İçin Teklif Al',
                    'anchor' => 'etüv cihazı teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.brand', 'weightlab'), 'anchor' => 'Weightlab etüv modelleri'],
                    ['url' => route('products.category', 'nem-tayin'), 'anchor' => 'nem tayin cihazı modelleri'],
                ],
                'brand_alt_texts' => [
                    'weightlab' => 'Weightlab etüv cihazı marka logosu',
                ],
                'brand_anchors' => [
                    'weightlab' => 'Weightlab etüv modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Etüv cihazı ne işe yarar?',
                        'answer' => 'Etüv cihazı, laboratuvarlarda numune kurutma, ısıtma ve sıcaklık kontrollü bekletme işlemleri için kullanılır.',
                    ],
                    [
                        'question' => 'Laboratuvar etüvü seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'İç hacim, sıcaklık aralığı, sıcaklık homojenliği, kontrol hassasiyeti, hava sirkülasyonu, raf yapısı ve kullanım yoğunluğu birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Kurutma etüvü hangi alanlarda kullanılır?',
                        'answer' => 'Kurutma etüvleri gıda, kimya, ilaç, plastik, akademik araştırma ve kalite kontrol laboratuvarlarında numune kurutma ve şartlandırma işlemleri için kullanılabilir.',
                    ],
                    [
                        'question' => 'Etüv cihazı için kalibrasyon gerekir mi?',
                        'answer' => 'Sıcaklık kontrolü ve kabin içi sıcaklık dağılımının doğrulanması gereken uygulamalarda sıcaklık kalibrasyonu değerlendirilebilir.',
                    ],
                    [
                        'question' => 'Etüv teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Cihaz istenen sıcaklığa ulaşmıyorsa, sıcaklık dalgalanması varsa, sensör veya kontrol paneli hatası oluşuyorsa, fan ya da ısıtma sistemi düzgün çalışmıyorsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'termoreaktor' => [
                'slug' => 'termoreaktor',
                'meta_title' => 'Termoreaktör Modelleri ve Laboratuvar Sindirim Cihazları | MTA Endüstri',
                'meta_description' => 'Termoreaktör ve laboratuvar sindirim cihazlarını VELP markasıyla inceleyin; sıcaklık kontrollü uygulamalarınız için teklif alın.',
                'h1' => 'Termoreaktör Modelleri ve Laboratuvar Sindirim Cihazları',
                'hero_text' => 'Termoreaktörler; laboratuvarlarda numunelerin kontrollü sıcaklıkta reaksiyon, sindirim veya ısıtma süreçlerinden geçirilmesi için kullanılan cihazlardır. MTA Endüstri termoreaktör kategorisinde VELP markasına ait laboratuvar uygulamalarına uygun termoreaktör ve sindirim cihazı modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Termoreaktör İçin Teklif Al',
                'secondary_cta' => 'Sıcaklık Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'sicaklik-kalibrasyonu'),
                'brand_eyebrow' => 'Termoreaktör markaları',
                'list_title' => 'Termoreaktör modelleri listesi',
                'sections' => [
                    [
                        'title' => 'Laboratuvar Uygulamaları İçin Termoreaktörler',
                        'text' => 'Termoreaktörler, numunelerin belirli sıcaklık ve süre koşullarında işlenmesi gereken laboratuvar uygulamalarında kullanılır. Kimya, çevre, gıda, atık su, kalite kontrol ve AR-GE laboratuvarlarında kontrollü reaksiyon ve sindirim süreçleri için tercih edilebilir. Uygulama sonucunun tekrarlanabilir olması için sıcaklık kontrolü ve program yönetimi önemlidir.',
                    ],
                    [
                        'title' => 'Termoreaktör Ne İşe Yarar?',
                        'text' => 'Termoreaktör, numunelerin belirlenen sıcaklıkta ve belirlenen süre boyunca kontrollü şekilde ısıtılmasını sağlar. Sindirim, reaksiyon hazırlığı, kimyasal analiz ön işlemleri ve bazı kalite kontrol metotlarında kullanılabilir. Cihazın blok yapısı, tüp uyumu ve sıcaklık performansı uygulama sonucunu doğrudan etkileyebilir.',
                    ],
                    [
                        'title' => 'Laboratuvar Sindirim Cihazı Kullanımı',
                        'text' => 'Laboratuvar sindirim cihazları, numune yapısının analiz öncesinde uygun hale getirilmesi gereken süreçlerde kullanılır. Özellikle çevre analizleri, atık su kontrolleri, kimyasal numune hazırlama ve COD gibi uygulamalarda kontrollü sıcaklık ve süre yönetimi kritik olabilir. Doğru cihaz seçimi, kullanılan metot ve tüp yapısına göre yapılmalıdır.',
                    ],
                    [
                        'title' => 'Termoreaktör Seçerken Nelere Dikkat Edilmeli?',
                        'text' => 'Termoreaktör seçiminde sıcaklık aralığı, sıcaklık stabilitesi, blok kapasitesi, tüp çapı ve tüp adedi, programlama seçenekleri, zamanlayıcı, güvenlik özellikleri, kullanım yoğunluğu ve cihazın bakım gereksinimi birlikte değerlendirilmelidir. Rutin analizlerde tekrarlanabilir çalışma koşulları sunan cihazlar tercih edilmelidir.',
                    ],
                    [
                        'title' => 'Termoreaktörlerde Sıcaklık Kontrolü ve Kalibrasyon',
                        'text' => 'Termoreaktörlerde sıcaklık kontrolü, reaksiyon ve sindirim sürecinin güvenilirliği açısından önemlidir. Blok sıcaklığı, sıcaklık stabilitesi ve programlanan değerle gerçek çalışma koşulları arasındaki uyum uygulama sonucunu etkileyebilir. Bu nedenle sıcaklık performansının doğrulanması gereken uygulamalarda sıcaklık kalibrasyonu değerlendirilebilir.',
                    ],
                    [
                        'title' => 'Termoreaktör Teknik Servis ve Bakım',
                        'text' => 'Termoreaktörlerde ısıtma bloğu, sıcaklık sensörü, kontrol paneli, zamanlayıcı, elektronik kart veya güç bağlantısı kaynaklı sorunlar görülebilir. Cihazın istenen sıcaklığa ulaşmaması, sıcaklık dalgalanması, program hatası, ekran problemi veya ısıtma bloğu arızası teknik servis değerlendirmesi gerektirebilir.',
                    ],
                ],
                'brand_section' => [
                    'title' => 'VELP Termoreaktör Modelleri',
                    'text' => 'MTA Endüstri termoreaktör kategorisinde VELP markasına ait laboratuvar uygulamalarına uygun modeller listelenir. Kullanıcılar ürünleri model, sıcaklık aralığı, blok kapasitesi, tüp uyumu ve teknik özelliklerine göre inceleyebilir; ihtiyaç duydukları cihaz için teklif talebi oluşturabilir.',
                ],
                'list_section' => [
                    'title' => 'Termoreaktör Kullanım Alanları',
                    'text' => 'Termoreaktörler; çevre analizleri, atık su testleri, COD analiz hazırlığı, kimyasal sindirim, numune ön hazırlığı, kalite kontrol ve AR-GE çalışmalarında kullanılabilir. Uygulamanın gerektirdiği sıcaklık, süre ve numune kabı özellikleri cihaz seçiminde belirleyici olur.',
                    'items' => [
                        'Çevre analiz laboratuvarları',
                        'Atık su ve COD analizleri',
                        'Kimyasal numune hazırlama',
                        'Gıda kalite kontrol uygulamaları',
                        'İlaç ve kozmetik laboratuvarları',
                        'Akademik araştırma laboratuvarları',
                        'AR-GE ve metot geliştirme süreçleri',
                    ],
                ],
                'cta' => [
                    'title' => 'Termoreaktör İçin Teklif Alın',
                    'text' => 'Laboratuvarınızda kontrollü reaksiyon, sindirim veya numune hazırlama uygulamaları için uygun termoreaktör modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Uygulama türü, sıcaklık ihtiyacı, tüp kapasitesi, marka tercihi ve kullanım yoğunluğu bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun termoreaktör veya laboratuvar sindirim cihazı için teknik ekibe ulaşın.',
                    'button' => 'Termoreaktör İçin Teklif Al',
                    'anchor' => 'termoreaktör teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.brand', 'velp'), 'anchor' => 'VELP termoreaktör modelleri'],
                    ['url' => route('products.category', 'balon-isiticilar'), 'anchor' => 'balon ısıtıcı modelleri'],
                    ['url' => route('products.category', 'manyetik-karistirici'), 'anchor' => 'manyetik karıştırıcı modelleri'],
                ],
                'brand_alt_texts' => [
                    'velp' => 'VELP termoreaktör marka logosu',
                ],
                'brand_anchors' => [
                    'velp' => 'VELP termoreaktör modelleri',
                ],
                'faq' => [
                    [
                        'question' => 'Termoreaktör ne işe yarar?',
                        'answer' => 'Termoreaktör, numunelerin belirli sıcaklık ve süre koşullarında kontrollü şekilde ısıtılması, reaksiyona hazırlanması veya sindirim işleminden geçirilmesi için kullanılır.',
                    ],
                    [
                        'question' => 'Laboratuvar sindirim cihazı hangi uygulamalarda kullanılır?',
                        'answer' => 'Çevre analizleri, atık su kontrolleri, COD analiz hazırlığı, kimyasal numune hazırlama, kalite kontrol ve AR-GE uygulamalarında kullanılabilir.',
                    ],
                    [
                        'question' => 'Termoreaktör seçerken hangi kriterlere bakılmalı?',
                        'answer' => 'Sıcaklık aralığı, blok kapasitesi, tüp uyumu, programlama seçenekleri, zamanlayıcı, güvenlik özellikleri ve kullanım yoğunluğu birlikte değerlendirilmelidir.',
                    ],
                    [
                        'question' => 'Termoreaktör için sıcaklık kalibrasyonu gerekir mi?',
                        'answer' => 'Sıcaklık kontrolünün kritik olduğu uygulamalarda termoreaktörün sıcaklık performansı değerlendirilebilir. Bu durumda sıcaklık kalibrasyonu veya teknik kontrol süreci planlanabilir.',
                    ],
                    [
                        'question' => 'Termoreaktör teknik servis ihtiyacı nasıl anlaşılır?',
                        'answer' => 'Cihaz istenen sıcaklığa ulaşmıyorsa, sıcaklık dalgalanması varsa, program hatası veriyorsa, ekran ya da kontrol paneli çalışmıyorsa teknik servis değerlendirmesi gerekebilir.',
                    ],
                ],
            ],
            'inkubatorler' => [
                'slug' => 'inkubatorler',
                'meta_title' => 'İnkübatör Modelleri ve Laboratuvar İnkübatörleri | MTA Endüstri',
                'meta_description' => 'İnkübatör, soğutmalı inkübatör ve çalkalamalı inkübatör modellerini teknik özellikleriyle inceleyin; laboratuvar ihtiyacınıza göre teklif alın.',
                'h1' => 'İnkübatör Modelleri ve Laboratuvar İnkübatörleri',
                'hero_text' => 'İnkübatörler; laboratuvarlarda numunelerin sabit sıcaklık ve kontrollü ortam koşullarında bekletilmesi için kullanılan cihazlardır. MTA Endüstri inkübatör kategorisinde soğutmalı inkübatör ve çalkalamalı inkübatör dahil farklı hacim, sıcaklık aralığı ve kontrol özelliklerine sahip laboratuvar inkübatörü modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'İnkübatör İçin Teklif Al',
                'secondary_cta' => 'Sıcaklık Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'sicaklik-kalibrasyonu'),
                'brand_eyebrow' => 'İnkübatör markaları',
                'list_title' => 'İnkübatör modelleri listesi',
                'sections' => [
                    ['title' => 'Laboratuvar İnkübatörü Ne İşe Yarar?', 'text' => 'İnkübatör, numunelerin belirlenen sıcaklıkta ve kontrollü koşullarda inkübe edilmesini sağlar. Mikrobiyoloji, gıda, çevre, ilaç ve AR-GE laboratuvarlarında kültür geliştirme, BOİ analizi, stabilite testi ve numune bekletme uygulamalarında kullanılır.'],
                    ['title' => 'İnkübatör Çeşitleri: Soğutmalı ve Çalkalamalı İnkübatör', 'text' => 'Soğutmalı inkübatör, ortam sıcaklığının altındaki değerlerde çalışabildiği için düşük sıcaklık gerektiren uygulamalarda tercih edilir. Çalkalamalı inkübatör ise sıcaklık kontrolüyle birlikte numuneyi çalkalayarak homojen karışım ve havalandırma sağlar. İhtiyaca göre standart, soğutmalı veya çalkalamalı model seçilir.'],
                    ['title' => 'İnkübatör Seçerken Nelere Dikkat Edilmeli?', 'text' => 'İç hacim, sıcaklık aralığı, sıcaklık homojenliği ve stabilitesi, soğutma ihtiyacı, çalkalama gereksinimi, programlama, raf yapısı ve kullanım yoğunluğu birlikte değerlendirilmelidir. Numune tipi ve uygulama süresi de belirleyicidir.'],
                    ['title' => 'İnkübatörlerde Sıcaklık Kalibrasyonu ve Teknik Servis', 'text' => 'İnkübatörde kabin içi sıcaklık dağılımı ve stabilitesi sonuç güvenilirliği açısından önemlidir. Sıcaklık performansının doğrulanması gereken durumlarda sıcaklık kalibrasyonu değerlendirilebilir. Isıtma/soğutma problemi, sensör hatası, kontrol paneli veya çalkalama mekanizması arızasında teknik servis desteği gerekebilir.'],
                ],
                'cta' => [
                    'title' => 'İnkübatör İçin Teklif Alın',
                    'text' => 'Laboratuvarınız için standart, soğutmalı veya çalkalamalı inkübatör modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Hacim, sıcaklık aralığı, soğutma/çalkalama ihtiyacı ve adet bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun laboratuvar inkübatörü, soğutmalı inkübatör veya çalkalamalı inkübatör için teknik ekibe ulaşın.',
                    'button' => 'İnkübatör İçin Teklif Al',
                    'anchor' => 'inkübatör teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                    ['url' => route('products.category', 'etuv'), 'anchor' => 'etüv cihazı modelleri'],
                    ['url' => route('products.category', 'su-banyosu'), 'anchor' => 'su banyosu modelleri'],
                ],
                'faq' => [
                    ['question' => 'İnkübatör ne işe yarar?', 'answer' => 'İnkübatör, numunelerin sabit sıcaklık ve kontrollü koşullarda bekletilmesi ve inkübasyonu için kullanılır.'],
                    ['question' => 'Soğutmalı inkübatör ile standart inkübatör farkı nedir?', 'answer' => 'Soğutmalı inkübatör ortam sıcaklığının altındaki değerlerde çalışabilir; standart inkübatör genellikle ortam sıcaklığının üzerindeki değerlerde çalışır.'],
                    ['question' => 'Çalkalamalı inkübatör hangi uygulamalarda kullanılır?', 'answer' => 'Sıcaklık kontrolüyle birlikte çalkalama gerektiren kültür geliştirme, çözünürlük ve homojen karışım uygulamalarında kullanılır.'],
                    ['question' => 'İnkübatör için kalibrasyon gerekir mi?', 'answer' => 'Kabin içi sıcaklık dağılımı ve stabilitesinin doğrulanması gereken uygulamalarda sıcaklık kalibrasyonu değerlendirilebilir.'],
                ],
            ],
            'hot-plate' => [
                'slug' => 'hot-plate',
                'meta_title' => 'Hot Plate ve Isıtıcılı Manyetik Karıştırıcı Modelleri | MTA Endüstri',
                'meta_description' => 'Hot plate (ısıtıcı tabla) ve ısıtmalı manyetik karıştırıcı modellerini teknik özellikleriyle inceleyin; laboratuvar ihtiyacınıza göre teklif alın.',
                'h1' => 'Hot Plate (Isıtıcı Tabla) Modelleri',
                'hero_text' => 'Hot plate cihazları; laboratuvarlarda numunelerin kontrollü şekilde ısıtılması için kullanılan ısıtıcı tablalardır. Çoğu model ısıtma ile manyetik karıştırmayı birlikte sunar. MTA Endüstri hot plate kategorisinde farklı tabla malzemesi, sıcaklık aralığı ve karıştırma özelliklerine sahip laboratuvar hot plate modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Hot Plate İçin Teklif Al',
                'secondary_cta' => 'Manyetik Karıştırıcı Modellerini İncele',
                'secondary_cta_url' => route('products.category', 'manyetik-karistirici'),
                'brand_eyebrow' => 'Hot plate markaları',
                'list_title' => 'Hot plate modelleri listesi',
                'sections' => [
                    ['title' => 'Laboratory Hot Plate Ne İşe Yarar?', 'text' => 'Hot plate, numunelerin belirlenen sıcaklıkta kontrollü şekilde ısıtılması için kullanılır. Çözelti hazırlama, reaksiyon ısıtması, buharlaştırma ve numune ön işlemlerinde tercih edilir. Isıtmalı manyetik karıştırıcı modellerinde ısıtma ve karıştırma aynı anda yapılabilir.'],
                    ['title' => 'Hot Plate ve Isıtmalı Manyetik Karıştırıcı Farkı', 'text' => 'Sade hot plate yalnızca ısıtma yapar; ısıtmalı manyetik karıştırıcı ise ısıtma ile birlikte manyetik balık aracılığıyla karıştırma sağlar. Karıştırma ihtiyacı olan uygulamalarda ısıtmalı manyetik karıştırıcı, yalnızca yüzey ısıtması yeterliyse hot plate seçilir.'],
                    ['title' => 'Hot Plate Seçerken Nelere Dikkat Edilmeli?', 'text' => 'Tabla malzemesi (seramik, alüminyum, cam seramik), maksimum sıcaklık, sıcaklık kontrol hassasiyeti, tabla ölçüsü, karıştırma hızı aralığı, sıcaklık probu desteği ve kimyasal dayanım birlikte değerlendirilmelidir.'],
                    ['title' => 'Hot Plate Teknik Servis ve Kontrol', 'text' => 'Isıtmıyorsa, sıcaklık sapması varsa, karıştırma motoru düzensiz çalışıyorsa veya kontrol paneli yanıt vermiyorsa laboratuvar cihazları teknik servis desteği gerekebilir.'],
                ],
                'cta' => [
                    'title' => 'Hot Plate İçin Teklif Alın',
                    'text' => 'Laboratuvarınız için uygun hot plate veya ısıtmalı manyetik karıştırıcı modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Tabla ölçüsü, sıcaklık aralığı, karıştırma ihtiyacı ve adet bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun laboratuvar hot plate veya ısıtmalı manyetik karıştırıcı için teknik ekibe ulaşın.',
                    'button' => 'Hot Plate İçin Teklif Al',
                    'anchor' => 'hot plate teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('products.category', 'manyetik-karistirici'), 'anchor' => 'manyetik karıştırıcı modelleri'],
                    ['url' => route('products.category', 'karistiricilar'), 'anchor' => 'karıştırıcılar kategorisi'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'faq' => [
                    ['question' => 'Hot plate ne için kullanılır?', 'answer' => 'Hot plate, laboratuvarda numunelerin kontrollü sıcaklıkta ısıtılması için kullanılan ısıtıcı tabladır.'],
                    ['question' => 'Hot plate ile manyetik karıştırıcı aynı cihaz mı?', 'answer' => 'Isıtmalı manyetik karıştırıcı modellerinde ısıtma ve karıştırma birlikte yapılır; sade hot plate yalnızca ısıtma yapar.'],
                    ['question' => 'Hot plate seçerken neye dikkat edilmeli?', 'answer' => 'Tabla malzemesi, maksimum sıcaklık, kontrol hassasiyeti, tabla ölçüsü ve karıştırma hızı aralığı birlikte değerlendirilmelidir.'],
                ],
            ],
            'su-banyosu' => [
                'slug' => 'su-banyosu',
                'meta_title' => 'Su Banyosu Modelleri ve Laboratuvar Su Banyoları | MTA Endüstri',
                'meta_description' => 'Su banyosu ve sirkülasyonlu su banyosu modellerini teknik özellikleriyle inceleyin; sıcaklık kontrollü uygulamalarınız için teklif alın.',
                'h1' => 'Su Banyosu Modelleri ve Laboratuvar Su Banyoları',
                'hero_text' => 'Su banyoları; laboratuvarlarda numunelerin sabit sıcaklıkta bekletilmesi, ısıtılması veya inkübe edilmesi için kullanılan sıcaklık kontrollü cihazlardır. MTA Endüstri su banyosu kategorisinde farklı hacim, sıcaklık aralığı ve sirkülasyon özelliklerine sahip laboratuvar su banyosu modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Su Banyosu İçin Teklif Al',
                'secondary_cta' => 'Sıcaklık Kalibrasyonunu İncele',
                'secondary_cta_url' => route('services.show', 'sicaklik-kalibrasyonu'),
                'brand_eyebrow' => 'Su banyosu markaları',
                'list_title' => 'Su banyosu modelleri listesi',
                'sections' => [
                    ['title' => 'Laboratuvar Su Banyosu Ne İşe Yarar?', 'text' => 'Su banyosu, su ortamı üzerinden kontrollü ve homojen sıcaklık sağlayarak numunelerin belirli sıcaklıkta bekletilmesini veya ısıtılmasını sağlar. Numune inkübasyonu, ekstraksiyon, çözme, viskozite hazırlığı ve sıcaklık şartlandırma uygulamalarında kullanılır.'],
                    ['title' => 'Su Banyosu Çeşitleri', 'text' => 'Standart su banyoları sabit sıcaklıkta bekletme için kullanılır. Sirkülasyonlu su banyoları pompa yardımıyla sıcaklık homojenliğini artırır ve harici sistemlere sıcaklık aktarabilir. Çalkalamalı su banyoları ise sıcaklık kontrolüyle birlikte numuneyi hareket ettirir.'],
                    ['title' => 'Su Banyosu Seçerken Nelere Dikkat Edilmeli?', 'text' => 'İç hazne hacmi, sıcaklık aralığı, sıcaklık stabilitesi ve homojenliği, sirkülasyon ihtiyacı, kapak tipi, malzeme dayanımı ve kullanım yoğunluğu birlikte değerlendirilmelidir.'],
                    ['title' => 'Su Banyolarında Sıcaklık Kalibrasyonu ve Teknik Servis', 'text' => 'Su banyosunda sıcaklık doğruluğu ve homojenliği sonuç güvenilirliği açısından önemlidir. Doğrulama gereken durumlarda sıcaklık kalibrasyonu değerlendirilebilir. Isıtma problemi, sıcaklık sapması, pompa veya kontrol paneli arızasında teknik servis desteği gerekebilir.'],
                ],
                'cta' => [
                    'title' => 'Su Banyosu İçin Teklif Alın',
                    'text' => 'Laboratuvarınız için standart, sirkülasyonlu veya çalkalamalı su banyosu modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Hacim, sıcaklık aralığı, sirkülasyon ihtiyacı ve adet bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun laboratuvar su banyosu için teknik ekibe ulaşın.',
                    'button' => 'Su Banyosu İçin Teklif Al',
                    'anchor' => 'su banyosu teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('services.show', 'sicaklik-kalibrasyonu'), 'anchor' => 'sıcaklık kalibrasyonu'],
                    ['url' => route('products.category', 'ultrasonik-banyo'), 'anchor' => 'ultrasonik banyo modelleri'],
                    ['url' => route('products.category', 'inkubatorler'), 'anchor' => 'inkübatör modelleri'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'faq' => [
                    ['question' => 'Su banyosu ne işe yarar?', 'answer' => 'Su banyosu, numunelerin su ortamı üzerinden kontrollü ve homojen sıcaklıkta bekletilmesi veya ısıtılması için kullanılır.'],
                    ['question' => 'Sirkülasyonlu su banyosu ne zaman gerekir?', 'answer' => 'Yüksek sıcaklık homojenliği gereken veya harici bir sisteme sıcaklık aktarılması gereken uygulamalarda sirkülasyonlu su banyosu tercih edilir.'],
                    ['question' => 'Su banyosu için kalibrasyon gerekir mi?', 'answer' => 'Sıcaklık doğruluğu ve homojenliğinin doğrulanması gereken uygulamalarda sıcaklık kalibrasyonu değerlendirilebilir.'],
                ],
            ],
            'ultrasonik-banyo' => [
                'slug' => 'ultrasonik-banyo',
                'meta_title' => 'Ultrasonik Banyo Modelleri ve Ultrasonik Temizleyiciler | MTA Endüstri',
                'meta_description' => 'Ultrasonik banyo ve ultrasonik temizleyici modellerini hacim, frekans ve ısıtma özelliklerine göre inceleyin; laboratuvar ihtiyacınıza göre teklif alın.',
                'h1' => 'Ultrasonik Banyo Modelleri ve Ultrasonik Temizleyiciler',
                'hero_text' => 'Ultrasonik banyolar; yüksek frekanslı ses dalgalarıyla oluşan kavitasyon etkisini kullanarak laboratuvar malzemelerinin, cam ekipmanların ve parçaların etkin şekilde temizlenmesi için kullanılır. MTA Endüstri ultrasonik banyo kategorisinde farklı hazne hacmi, frekans ve ısıtma özelliklerine sahip modelleri teknik özellikleriyle listeler.',
                'primary_cta' => 'Ultrasonik Banyo İçin Teklif Al',
                'secondary_cta' => 'Su Banyosu Modellerini İncele',
                'secondary_cta_url' => route('products.category', 'su-banyosu'),
                'brand_eyebrow' => 'Ultrasonik banyo markaları',
                'list_title' => 'Ultrasonik banyo modelleri listesi',
                'sections' => [
                    ['title' => 'Ultrasonik Banyo Ne İşe Yarar?', 'text' => 'Ultrasonik banyo, sıvı içinde oluşturulan yüksek frekanslı titreşimlerle mikro kabarcıklar üreterek yüzeydeki kir, yağ, kalıntı ve partikülleri çözer. Laboratuvar cam malzemeleri, filtreler, metal parçalar ve hassas ekipmanların temizliğinde kullanılır; ayrıca gaz giderme ve numune dağıtma amacıyla da kullanılabilir.'],
                    ['title' => 'Ultrasonik Banyo Seçerken Nelere Dikkat Edilmeli?', 'text' => 'Hazne hacmi ve iç ölçüler, ultrasonik güç ve frekans, ısıtma özelliği ve sıcaklık aralığı, zamanlayıcı, gaz giderme fonksiyonu ve hazne malzemesi birlikte değerlendirilmelidir. Temizlenecek parçanın boyutu ve kirlilik tipi frekans seçiminde belirleyicidir.'],
                    ['title' => 'Ultrasonik Temizleyici Kullanım Alanları', 'text' => 'Kalite kontrol, AR-GE, mikrobiyoloji, kimya ve medikal laboratuvarlarında cam malzeme, pipet, filtre, elek ve parça temizliğinde yaygın olarak kullanılır. Sanayi uygulamalarında da hassas parça temizliği için tercih edilir.'],
                    ['title' => 'Ultrasonik Banyo Teknik Servis', 'text' => 'Ultrasonik güç düşmüşse, ısıtma çalışmıyorsa, transdüser sesi anormalse veya kontrol paneli yanıt vermiyorsa teknik servis değerlendirmesi gerekebilir.'],
                ],
                'cta' => [
                    'title' => 'Ultrasonik Banyo İçin Teklif Alın',
                    'text' => 'Laboratuvarınız için uygun hacim, frekans ve ısıtma özelliğine sahip ultrasonik banyo modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Hazne hacmi, ısıtma ihtiyacı ve adet bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun ultrasonik banyo veya ultrasonik temizleyici için teknik ekibe ulaşın.',
                    'button' => 'Ultrasonik Banyo İçin Teklif Al',
                    'anchor' => 'ultrasonik banyo teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('products.category', 'su-banyosu'), 'anchor' => 'su banyosu modelleri'],
                    ['url' => route('products.category', 'santrifujler'), 'anchor' => 'santrifüj modelleri'],
                    ['url' => route('technical-services.show', 'laboratuvar-cihazlari-icin-teknik-servis'), 'anchor' => 'laboratuvar cihazları teknik servis'],
                ],
                'faq' => [
                    ['question' => 'Ultrasonik banyo ne işe yarar?', 'answer' => 'Ultrasonik banyo, yüksek frekanslı titreşimlerle oluşan kavitasyon etkisiyle laboratuvar malzemelerinin ve parçaların temizlenmesi için kullanılır.'],
                    ['question' => 'Ultrasonik banyo seçerken neye dikkat edilmeli?', 'answer' => 'Hazne hacmi, ultrasonik güç ve frekans, ısıtma özelliği, zamanlayıcı ve hazne malzemesi birlikte değerlendirilmelidir.'],
                    ['question' => 'Ultrasonik banyo gaz giderme için kullanılabilir mi?', 'answer' => 'Evet, birçok model sıvı numunelerde çözünmüş gazların uzaklaştırılması (degassing) için de kullanılabilir.'],
                ],
            ],
            'polarimetreler' => [
                'slug' => 'polarimetreler',
                'meta_title' => 'Polarimetre Modelleri ve Dijital Polarimetreler | MTA Endüstri',
                'meta_description' => 'Dijital polarimetre modellerini optik çevirme açısı ölçümü, sıcaklık kontrolü ve numune tipine göre inceleyin; laboratuvar ihtiyacınıza göre teklif alın.',
                'h1' => 'Polarimetre Modelleri ve Dijital Polarimetreler',
                'hero_text' => 'Polarimetreler; optik olarak aktif maddelerin polarize ışığı çevirme açısını ölçerek konsantrasyon, saflık ve şeker (Brix/pol) analizinde kullanılan cihazlardır. MTA Endüstri polarimetre kategorisinde dijital okuma, sıcaklık kontrolü ve otomatik ölçüm özelliklerine sahip laboratuvar polarimetre modellerini teknik özellikleriyle listeler.',
                'primary_cta' => 'Polarimetre İçin Teklif Al',
                'secondary_cta' => 'Refraktometre Modellerini İncele',
                'secondary_cta_url' => route('products.category', 'refraktometre'),
                'brand_eyebrow' => 'Polarimetre markaları',
                'list_title' => 'Polarimetre modelleri listesi',
                'sections' => [
                    ['title' => 'Polarimetre Ne İşe Yarar?', 'text' => 'Polarimetre, optik olarak aktif bileşiklerin (şeker, laktik asit, tartarik asit, amino asitler, esansiyel yağlar vb.) polarize ışığın titreşim düzlemini döndürme miktarını ölçer. Bu değer maddenin konsantrasyonu, saflığı veya kimliği hakkında bilgi verir. Gıda, ilaç, kimya ve şeker endüstrisinde yaygın kullanılır.'],
                    ['title' => 'Dijital Polarimetre Seçerken Nelere Dikkat Edilmeli?', 'text' => 'Ölçüm aralığı ve çözünürlüğü, ölçüm doğruluğu, dalga boyu, sıcaklık kontrolü/kompanzasyonu, ölçüm skalaları (açısal, uluslararası şeker skalası, Brix), otomatik ölçüm ve veri aktarım özellikleri birlikte değerlendirilmelidir.'],
                    ['title' => 'Polarimetre Kullanım Alanları', 'text' => 'İlaç etkin madde saflık kontrolü, şeker ve şerbet analizleri, esansiyel yağ ve aroma kontrolü, fermentasyon takibi ve akademik araştırmalarda kullanılır.'],
                    ['title' => 'Polarimetre Kalibrasyonu ve Teknik Servis', 'text' => 'Polarimetrelerde optik yol ve okuma doğruluğu kontrol tüpleri veya referans plakalarla düzenli olarak doğrulanmalıdır. Okuma sapması, ışık kaynağı zayıflaması, sıcaklık kontrol hatası veya yazılım problemi durumunda teknik servis desteği gerekebilir.'],
                ],
                'cta' => [
                    'title' => 'Polarimetre İçin Teklif Alın',
                    'text' => 'Laboratuvarınız için uygun ölçüm aralığı, skala ve sıcaklık kontrol özelliğine sahip dijital polarimetre modelini seçmek istiyorsanız MTA Endüstri teknik ekibine ulaşabilirsiniz. Numune tipi, ölçüm skalası ve adet bilgilerinize göre teklif süreci başlatılır.',
                    'note' => 'İhtiyacınıza uygun dijital polarimetre için teknik ekibe ulaşın.',
                    'button' => 'Polarimetre İçin Teklif Al',
                    'anchor' => 'polarimetre teklif talebi',
                ],
                'support_links' => [
                    ['url' => route('products.category', 'refraktometre'), 'anchor' => 'refraktometre modelleri'],
                    ['url' => route('products.category', 'densitometre'), 'anchor' => 'densitometre modelleri'],
                    ['url' => route('technical-services.show', 'analiz-ve-olcum-cihazlari-teknik-servis'), 'anchor' => 'analiz ve ölçüm cihazları teknik servis'],
                ],
                'faq' => [
                    ['question' => 'Polarimetre ne ölçer?', 'answer' => 'Polarimetre, optik olarak aktif maddelerin polarize ışığı çevirme açısını (optik rotasyon) ölçer; bu değer konsantrasyon ve saflık analizinde kullanılır.'],
                    ['question' => 'Polarimetre hangi sektörlerde kullanılır?', 'answer' => 'İlaç, gıda, şeker, kimya ve aroma endüstrileri ile akademik araştırma laboratuvarlarında kullanılır.'],
                    ['question' => 'Polarimetre kalibrasyonu nasıl yapılır?', 'answer' => 'Sertifikalı kontrol tüpleri veya kuvars referans plakalarıyla okuma doğruluğu periyodik olarak doğrulanır.'],
                ],
            ],
            default => [],
        };
    }

    private function productCategoryImageAlt(string $categorySlug, array $product): string
    {
        return match ($categorySlug) {
            'teraziler' => $this->scaleProductImageAlt($product),
            'ph-metre' => $this->phMeterProductImageAlt($product),
            'ph-iletkenlik' => $this->phConductivityProductImageAlt($product),
            'refraktometre' => $this->refractometerProductImageAlt($product),
            'densitometre' => $this->densitometerProductImageAlt($product),
            'kral-fischer' => $this->karlFischerProductImageAlt($product),
            'potansiyometrik-titratorler' => $this->potentiometricTitratorProductImageAlt($product),
            'manyetik-karistirici' => $this->magneticStirrerProductImageAlt($product),
            'homojenizator' => $this->homogenizerProductImageAlt($product),
            'viskozimetre' => $this->viscometerProductImageAlt($product),
            'nem-tayin' => $this->moistureAnalyzerProductImageAlt($product),
            'etuv' => $this->ovenProductImageAlt($product),
            'termoreaktor' => $this->thermoreactorProductImageAlt($product),
            default => $product['image_alt'] ?? $product['name'],
        };
    }

    private function potentiometricTitratorProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'Kyoto KEM' && Str::contains($nameAndModel, 'at710')) {
            return 'Kyoto KEM AT710 potansiyometrik titratör ürün görseli';
        }

        if ($brand === 'Mettler Toledo') {
            return 'Mettler Toledo otomatik titratör laboratuvar cihazı';
        }

        if ($brand === 'SI Analitik') {
            return 'SI Analitik potansiyometrik titrasyon cihazı';
        }

        if ($brand === 'Kyoto KEM') {
            return 'Kyoto KEM potansiyometrik titratör ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' potansiyometrik titratör ürün görseli');
    }

    private function karlFischerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'Kyoto KEM' && Str::contains($nameAndModel, 'mkc-710')) {
            return 'Kyoto KEM MKC-710 Karl Fischer titratör ürün görseli';
        }

        if ($brand === 'Kyoto KEM') {
            return 'Kyoto KEM Kral Fischer su miktarı tayin cihazı';
        }

        if ($brand === 'Mettler Toledo') {
            return 'Mettler Toledo Karl Fischer titratör ürün görseli';
        }

        if ($brand === 'SI Analitik') {
            return 'SI Analitik Karl Fischer titrasyon cihazı';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' Karl Fischer titratör ürün görseli');
    }

    private function densitometerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;

        if ($brand === 'Kyoto KEM') {
            return 'Kyoto KEM densitometre yoğunluk ölçer ürün görseli';
        }

        if ($brand === 'Mettler Toledo') {
            return 'Mettler Toledo dijital densitometre laboratuvar cihazı';
        }

        if ($brand === 'Bellingham + Stanley') {
            return 'Bellingham + Stanley yoğunluk ölçer ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' densitometre ürün görseli');
    }

    private function phConductivityProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'WTW' && Str::contains($nameAndModel, ['multi 3630', '3630'])) {
            return 'WTW Multi 3630 IDS pH ve iletkenlik ölçer ürün görseli';
        }

        if ($brand === 'WTW') {
            return 'WTW portatif iletkenlik ölçer laboratuvar cihazı';
        }

        if ($brand === 'Mettler Toledo') {
            return 'Mettler Toledo pH ve iletkenlik ölçer ürün görseli';
        }

        if ($brand === 'Ohaus') {
            return 'Ohaus çok parametreli ölçüm cihazı ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' pH ve iletkenlik ölçer ürün görseli');
    }

    private function ovenProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $cleanModel = $model ? trim(preg_replace('/\s*et[uü]v\s*$/i', '', $model)) : null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if (Str::contains($nameAndModel, ['un1060', 'memmert'])) {
            return 'Weightlab Memmert UN1060 etüv cihazı ürün görseli';
        }

        if (Str::contains($nameAndModel, 'wf-ht45')) {
            return 'Weightlab WF-HT45 laboratuvar etüvü kurutma cihazı';
        }

        if (Str::contains($nameAndModel, 'wf-ht65')) {
            return 'Weightlab WF-HT65 sıcaklık kontrollü etüv cihazı';
        }

        if (Str::contains($nameAndModel, 'wf-ht125')) {
            return 'Weightlab WF-HT125 etüv cihazı iç hacim ve raf yapısı görseli';
        }

        return trim($brand . ' ' . ($cleanModel ?: $product['name'] ?? '') . ' etüv cihazı ürün görseli');
    }

    private function thermoreactorProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'VELP' && Str::contains($nameAndModel, ['eco 6', 'eco6'])) {
            return 'VELP ECO 6 termoreaktör laboratuvar sindirim cihazı';
        }

        if ($brand === 'VELP') {
            return 'VELP laboratuvar termoreaktör ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' termoreaktör ürün görseli');
    }

    private function moistureAnalyzerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'Ohaus' && Str::contains($nameAndModel, 'mb120')) {
            return 'Ohaus MB120 nem tayin cihazı ürün görseli';
        }

        if ($brand === 'Ohaus') {
            return 'Ohaus halojen nem tayin cihazı laboratuvar analiz cihazı';
        }

        if ($brand === 'A&D') {
            return 'A&D nem tayin cihazı ürün görseli';
        }

        if ($brand === 'Shimadzu') {
            return 'Shimadzu nem analiz cihazı ürün görseli';
        }

        if ($brand === 'Weightlab') {
            return 'Weightlab laboratuvar nem tayin cihazı ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' nem tayin cihazı ürün görseli');
    }

    private function viscometerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'Lamy' && Str::contains($nameAndModel, ['dijital', 'digital'])) {
            return 'Lamy dijital viskozite ölçüm cihazı';
        }

        if ($brand === 'Lamy') {
            return 'Lamy viskozimetre ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' viskozimetre ürün görseli');
    }

    private function homogenizerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'VELP' && Str::contains($nameAndModel, ['yüksek hızlı', 'yuksek hizli', 'high speed'])) {
            return 'VELP yüksek hızlı homojenizatör ürün görseli';
        }

        if ($brand === 'VELP') {
            return 'VELP homojenizatör laboratuvar numune hazırlama cihazı';
        }

        if ($brand === 'Weightlab') {
            return 'Weightlab homojenizatör ürün görseli';
        }

        if (Str::contains($nameAndModel, ['rotor', 'stator'])) {
            return 'Rotor-stator homojenizatör laboratuvar uygulaması';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' homojenizatör ürün görseli');
    }

    private function magneticStirrerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'VELP' && Str::contains($nameAndModel, 'arex-6')) {
            return 'VELP AREX-6 ısıtmalı manyetik karıştırıcı ürün görseli';
        }

        if ($brand === 'VELP' && Str::contains($nameAndModel, 'dijital')) {
            return 'VELP dijital manyetik karıştırıcı laboratuvar cihazı';
        }

        if ($brand === 'VELP') {
            return trim('VELP ' . ($model ?: '') . ' manyetik karıştırıcı ürün görseli');
        }

        if ($brand === 'Weightlab') {
            return 'Weightlab manyetik karıştırıcı ürün görseli';
        }

        if (Str::contains($nameAndModel, ['çok pozisyonlu', 'cok pozisyonlu', 'multi'])) {
            return 'Çok pozisyonlu manyetik karıştırıcı laboratuvar uygulaması';
        }

        if (Str::contains($nameAndModel, ['ısıtmasız', 'isitmasiz'])) {
            return 'Isıtmasız manyetik karıştırıcı numune hazırlama cihazı';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' manyetik karıştırıcı ürün görseli');
    }

    private function refractometerProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $nameAndModel = Str::lower(($product['name'] ?? '') . ' ' . ($model ?? ''));

        if ($brand === 'Kyoto KEM' && Str::contains($nameAndModel, 'ra-600')) {
            return 'Kyoto KEM RA-600 dijital refraktometre ürün görseli';
        }

        if ($brand === 'Kyoto KEM') {
            return 'Kyoto KEM refraktometre laboratuvar ölçüm cihazı';
        }

        if ($brand === 'Mettler Toledo') {
            return 'Mettler Toledo refraktometre ürün görseli';
        }

        if ($brand === 'Bellingham + Stanley' && Str::contains($nameAndModel, 'rfm')) {
            return 'Bellingham + Stanley RFM serisi dijital refraktometre';
        }

        if ($brand === 'Bellingham + Stanley') {
            return 'Bellingham + Stanley laboratuvar refraktometresi ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' refraktometre ürün görseli');
    }

    private function phMeterProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;
        $modelText = $model ? ' ' . $model : '';

        if ($brand === 'Mettler Toledo' && Str::contains(Str::lower(($product['name'] ?? '') . ' ' . $modelText), 'elekt')) {
            return 'Mettler Toledo pH elektrodu ürün görseli';
        }

        if ($brand === 'Mettler Toledo') {
            return 'Mettler Toledo pH metre laboratuvar ölçüm cihazı';
        }

        if ($brand === 'Ohaus') {
            return 'Ohaus pH metre ürün görseli';
        }

        if ($brand === 'WTW' && Str::contains(Str::lower(($product['name'] ?? '') . ' ' . $modelText), 'multi')) {
            return 'WTW Multi serisi çok parametreli pH metre cihazı';
        }

        if ($brand === 'WTW') {
            return 'WTW pH metre ve iletkenlik ölçer ürün görseli';
        }

        return trim($brand . $modelText . ' pH metre ürün görseli');
    }

    private function scaleProductImageAlt(array $product): string
    {
        $brand = $product['brand'] ?? '';
        $model = $product['model'] ?? null;

        if ($brand === 'A&D' && $model && str_contains(Str::lower($model), 'fz-500i')) {
            return 'A&D FZ-500i hassas terazi ürün görseli';
        }

        if ($brand === 'A&D' && $model && str_contains(Str::lower($model), 'gx')) {
            return 'A&D GX serisi hassas terazi laboratuvar tartım cihazı';
        }

        if ($brand === 'Ohaus') {
            return 'Ohaus hassas terazi laboratuvar tartım cihazı';
        }

        if ($brand === 'Shimadzu') {
            return 'Shimadzu analitik terazi ürün görseli';
        }

        if ($brand === 'Weightlab') {
            return 'Weightlab laboratuvar terazisi ürün görseli';
        }

        return trim($brand . ' ' . ($model ?: $product['name'] ?? '') . ' hassas terazi ürün görseli');
    }

    private function findProductCategory(string $slug): array
    {
        $category = $this->productCategories()->firstWhere('slug', $slug);
        abort_unless($category, 404);

        return $category;
    }

    private function findProductBrand(string $slug): array
    {
        $brand = $this->productBrands()->firstWhere('slug', $slug);
        abort_unless($brand, 404);

        return $brand;
    }

    private function findArticleCategory(string $slug): array
    {
        $category = $this->articleCategories()->firstWhere('slug', $slug);
        abort_unless($category, 404);

        return $category;
    }

    private function meta(string $title, string $description, ?string $image = null, array $overrides = []): array
    {
        $meta = [
            'title' => $title,
            'description' => $description,
            'canonical' => url()->current(),
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $this->absoluteAsset($image ?: 'mta-logo.png'),
            'robots' => 'index,follow',
        ];

        $meta = $this->mergeMetaOverrides($meta, [
            'title' => $overrides['seo_title'] ?? null,
            'description' => $overrides['meta_description'] ?? null,
            'canonical' => $overrides['canonical_url'] ?? null,
            'og_title' => $overrides['og_title'] ?? null,
            'og_description' => $overrides['og_description'] ?? null,
            'og_image' => $overrides['og_image'] ?? null,
            'robots' => $overrides['robots'] ?? null,
        ]);

        if ($seoEntry = $this->currentSeoEntry()) {
            $meta = $this->mergeMetaOverrides($meta, [
                'title' => $seoEntry->title,
                'description' => $seoEntry->description,
                'canonical' => $seoEntry->canonical_url,
                'og_title' => $seoEntry->og_title,
                'og_description' => $seoEntry->og_description,
                'og_image' => $this->publicAssetPath($seoEntry->og_image),
                'robots' => $seoEntry->robots,
            ]);
        }

        $meta['og_title'] ??= $meta['title'];
        $meta['og_description'] ??= $meta['description'];
        $meta['og_image'] = $this->absoluteAsset($meta['og_image'] ?? 'mta-logo.png');

        return $meta;
    }

    private function mergeMetaOverrides(array $meta, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (filled($value)) {
                $meta[$key] = $key === 'og_image' ? $this->absoluteAsset((string) $value) : $value;
            }
        }

        return $meta;
    }

    private function currentSeoEntry(): ?SeoEntry
    {
        if (! $this->canReadTable('seo_entries')) {
            return null;
        }

        $path = '/' . trim(request()->path(), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');
        $routeName = request()->route()?->getName();

        $query = SeoEntry::query();

        return (clone $query)->where('path', $path)->first()
            ?: ($routeName ? $query->where('route_name', $routeName)->first() : null);
    }

    private function publicAssetPath(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = ltrim((string) $path, '/');

        if (Str::startsWith($path, ['http://', 'https://', 'storage/', 'images/', 'mta-logo.png', 'favicon.png'])) {
            return $path;
        }

        return 'storage/' . $path;
    }

    private function absoluteAsset(?string $path): string
    {
        if (! filled($path)) {
            return asset('mta-logo.png');
        }

        $path = (string) $path;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset($this->publicAssetPath($path));
    }

    private function organizationSchema(): array
    {
        $sameAs = collect(SiteSettings::socialLinks())
            ->pluck('url')
            ->filter()
            ->values()
            ->all();

        return [
            '@type' => 'Organization',
            'name' => config('mta.site.name'),
            'url' => config('app.url'),
            'logo' => asset('mta-logo.png'),
            'email' => config('mta.site.email'),
            'telephone' => config('mta.site.phone'),
            'faxNumber' => config('mta.site.fax'),
            'sameAs' => $sameAs,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Bahçelievler, Köknar Sk. No:15/B',
                'addressLocality' => 'Pendik',
                'addressRegion' => 'İstanbul',
                'postalCode' => '34890',
                'addressCountry' => 'TR',
            ],
        ];
    }

    private function schemaGraph(array $items, array $content = []): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                $this->organizationSchema(),
                ...$items,
                ...$this->customSchemaItems($content),
                ...$this->currentSeoEntrySchemaItems(),
            ])),
        ];
    }

    private function customSchemaItems(array $content): array
    {
        return $this->schemaBlocksToItems($content['schema_blocks'] ?? []);
    }

    private function currentSeoEntrySchemaItems(): array
    {
        $seoEntry = $this->currentSeoEntry();

        if (! $seoEntry) {
            return [];
        }

        $items = $this->schemaBlocksToItems($seoEntry->schema_blocks ?? []);

        if ($items !== [] || blank($seoEntry->schema_type)) {
            return $items;
        }

        return [[
            '@type' => $seoEntry->schema_type,
            ...($seoEntry->schema_payload ?? []),
        ]];
    }

    private function schemaBlocksToItems(array $blocks): array
    {
        if (! $this->canReadTable('schema_definitions')) {
            return [];
        }

        $definitions = SchemaDefinition::query()
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        return collect($blocks)
            ->filter(fn ($block) => is_array($block) && ($block['is_active'] ?? true) && filled($block['type'] ?? null))
            ->map(function (array $block) use ($definitions): ?array {
                $definition = $definitions->get($block['type']);

                if (! $definition) {
                    return null;
                }

                $payload = collect($block['payload'] ?? [])
                    ->filter(fn ($value) => filled($value))
                    ->all();

                $schema = array_replace(
                    $definition->default_payload ?? [],
                    $payload,
                    ['@type' => $definition->schema_type],
                );

                if (filled($block['name'] ?? null)) {
                    $schema['name'] = $block['name'];
                }

                foreach (['url', 'image', 'logo'] as $assetKey) {
                    if (! empty($schema[$assetKey]) && is_string($schema[$assetKey])) {
                        $schema[$assetKey] = $this->absoluteAsset($schema[$assetKey]);
                    }
                }

                return $schema;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function webPageSchema(string $title, string $description): array
    {
        return [
            '@type' => 'WebPage',
            'name' => $title,
            'description' => $description,
            'url' => url()->current(),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => config('mta.site.name'),
                'url' => config('app.url'),
            ],
        ];
    }

    private function breadcrumbSchema(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    private function serviceSchema(array $service): array
    {
        return [
            '@type' => 'Service',
            'name' => $service['title'],
            'description' => $service['summary'],
            'image' => ! empty($service['image']) ? asset($service['image']) : null,
            'provider' => [
                '@type' => 'Organization',
                'name' => config('mta.site.name'),
            ],
        ];
    }

    private function productSchema(array $product, $relatedServices = null): array
    {
        $schema = [
            '@type' => 'Product',
            'name' => $product['name'],
            'brand' => [
                '@type' => 'Brand',
                'name' => $product['brand'],
            ],
            'model' => $product['model'] ?? null,
            'sku' => $product['sku'] ?? null,
            'image' => collect([$product['image'] ?? null, ...($product['gallery'] ?? [])])
                ->filter()
                ->map(fn ($image) => asset($image))
                ->values()
                ->all(),
            'category' => $product['category'],
            'description' => $product['summary'],
        ];

        if (! empty($product['videos'])) {
            $schema['video'] = collect($product['videos'])
                ->filter(fn ($video) => ! empty($video['youtube_id']))
                ->map(fn ($video) => [
                    '@type' => 'VideoObject',
                    'name' => $video['title'],
                    'embedUrl' => 'https://www.youtube-nocookie.com/embed/' . $video['youtube_id'],
                ])
                ->values()
                ->all();
        }

        if (! empty($product['specs'])) {
            $schema['additionalProperty'] = collect($product['specs'])
                ->map(fn ($value, $name) => [
                    '@type' => 'PropertyValue',
                    'name' => $name,
                    'value' => $value,
                ])
                ->values()
                ->all();
        }

        if ($relatedServices && $relatedServices->isNotEmpty()) {
            $schema['isRelatedTo'] = $relatedServices
                ->map(fn ($service) => [
                    '@type' => 'Service',
                    'name' => $service['title'],
                    'url' => route('services.show', $service['slug']),
                    'description' => $service['summary'],
                ])
                ->all();
        }

        return $schema;
    }

    private function articleSchema(array $article): array
    {
        return [
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['excerpt'],
            'datePublished' => $article['published_at'] ?? null,
            'dateModified' => $article['updated_at'] ?? null,
            'author' => [
                '@type' => 'Organization',
                'name' => $article['author'] ?? config('mta.site.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('mta.site.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('mta-logo.png'),
                ],
            ],
        ];
    }
}
