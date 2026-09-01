<?php

namespace App\Support;

use DOMDocument;
use Illuminate\Support\Str;
use XMLReader;

class WpProductImport
{
    private array $categories;

    private array $brands;

    private array $categoryBrands;

    private array $categoryServices;

    public function __construct(
        private readonly string $sourcePath,
        private readonly string $publicProductsPath,
    ) {
        $this->categories = config('mta.product_categories', []);
        $this->brands = config('mta.product_brands', []);
        $this->categoryBrands = config('mta.product_category_brands', []);
        $this->categoryServices = config('mta.product_category_services', []);
    }

    public function run(bool $includeDrafts = false, ?int $limit = null): array
    {
        $attachments = [];
        $rawProducts = [];

        $reader = new XMLReader();
        $reader->open($this->sourcePath);

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->localName !== 'item') {
                continue;
            }

            $item = simplexml_load_string($reader->readOuterXml());

            if (! $item) {
                continue;
            }

            $parsed = $this->parseItem($item);

            if ($parsed['post_type'] === 'attachment' && $parsed['post_id']) {
                $attachments[$parsed['post_id']] = [
                    'title' => $parsed['title'],
                    'slug' => $parsed['slug'],
                    'url' => $parsed['attachment_url'],
                    'basename' => $this->basenameFromUrl($parsed['attachment_url']),
                ];

                continue;
            }

            if ($parsed['post_type'] === 'product') {
                $rawProducts[] = $parsed;
            }
        }

        $reader->close();

        $availableImages = $this->availableImages();
        $products = [];
        $excluded = [];
        $statusCounts = [];
        $reasonCounts = [];

        foreach ($rawProducts as $raw) {
            $statusCounts[$raw['status']] = ($statusCounts[$raw['status']] ?? 0) + 1;

            if (! $includeDrafts && $raw['status'] !== 'publish') {
                $this->exclude($excluded, $reasonCounts, $raw, 'status_not_publish');
                continue;
            }

            $category = $this->resolveCategory($raw);

            if (! $category) {
                $this->exclude($excluded, $reasonCounts, $raw, 'unsupported_category');
                continue;
            }

            $brand = $this->resolveBrand($raw, $category);

            if (! $brand) {
                $this->exclude($excluded, $reasonCounts, $raw, 'unsupported_brand_or_missing_brand');
                continue;
            }

            if (! in_array($brand['slug'], $this->categoryBrands[$category['slug']] ?? [], true)) {
                $this->exclude($excluded, $reasonCounts, $raw, 'brand_not_allowed_for_category', [
                    'resolved_category' => $category['name'],
                    'resolved_brand' => $brand['name'],
                ]);
                continue;
            }

            $products[] = $this->normalizeProduct($raw, $category, $brand, $attachments, $availableImages);

            if ($limit && count($products) >= $limit) {
                break;
            }
        }

        return [
            'summary' => [
                'source' => $this->sourcePath,
                'total_products_in_xml' => count($rawProducts),
                'status_counts' => $statusCounts,
                'included_products' => count($products),
                'excluded_products' => count($excluded),
                'excluded_reason_counts' => $reasonCounts,
                'include_drafts' => $includeDrafts,
                'limit' => $limit,
            ],
            'products' => $products,
            'excluded' => $excluded,
        ];
    }

    private function parseItem(\SimpleXMLElement $item): array
    {
        $namespaces = $item->getNamespaces(true);
        $wp = $item->children($namespaces['wp'] ?? '');
        $content = $item->children($namespaces['content'] ?? '');
        $excerpt = $item->children($namespaces['excerpt'] ?? '');

        $categories = [];

        foreach ($item->category as $category) {
            $attributes = $category->attributes();
            $categories[] = [
                'domain' => (string) $attributes['domain'],
                'nicename' => (string) $attributes['nicename'],
                'name' => trim((string) $category),
            ];
        }

        $meta = [];

        foreach ($wp->postmeta ?? [] as $postmeta) {
            $key = trim((string) $postmeta->children($namespaces['wp'] ?? '')->meta_key);
            $value = trim((string) $postmeta->children($namespaces['wp'] ?? '')->meta_value);

            if ($key !== '') {
                $meta[$key] = $value;
            }
        }

        return [
            'post_id' => (string) ($wp->post_id ?? ''),
            'post_type' => (string) ($wp->post_type ?? ''),
            'status' => (string) ($wp->status ?? ''),
            'title' => trim((string) $item->title),
            'slug' => (string) ($wp->post_name ?? ''),
            'link' => (string) $item->link,
            'content' => (string) ($content->encoded ?? ''),
            'excerpt' => (string) ($excerpt->encoded ?? ''),
            'attachment_url' => (string) ($wp->attachment_url ?? ''),
            'categories' => $categories,
            'meta' => $meta,
            'modified_at' => (string) ($wp->post_modified ?? ''),
        ];
    }

    private function normalizeProduct(array $raw, array $category, array $brand, array $attachments, array $availableImages): array
    {
        $specs = $this->extractSpecs($raw);
        $image = $this->resolveImage($raw, $attachments, $availableImages);
        $model = $this->deriveModel($raw['title'], $brand['name']);
        $summary = $this->summary($raw, $category, $brand);

        return [
            'name' => $raw['title'],
            'slug' => $raw['slug'] ?: Str::slug($raw['title']),
            'old_url' => $raw['link'],
            'wp_id' => $raw['post_id'],
            'status' => $raw['status'],
            'category' => $category['name'],
            'category_slug' => $category['slug'],
            'brand' => $brand['name'],
            'brand_slug' => $brand['slug'],
            'model' => $model,
            'sku' => $raw['meta']['_sku'] ?? 'MTA-' . strtoupper($brand['slug']) . '-' . strtoupper(Str::slug($model, '')),
            'seo_title' => $raw['title'] . ' | MTA Endüstri',
            'meta_description' => Str::limit($summary, 155, ''),
            'summary' => $summary,
            'image' => $image,
            'image_alt' => $raw['title'] . ' ürün görseli',
            'image_label' => 'Ürün görseli alanı',
            'features' => $this->features($category, $brand),
            'metadata' => [
                'Marka' => $brand['name'],
                'Kategori' => $category['name'],
                'Model' => $model,
                'SKU' => $raw['meta']['_sku'] ?? 'Yayın öncesi netleştirilecek',
                'Kullanım alanı' => $category['summary'] ?? $category['name'],
            ],
            'specs' => $specs ?: [
                'Ürün grubu' => $category['name'],
                'Marka' => $brand['name'],
                'Model' => $model,
            ],
            'documents' => ['Katalog PDF alanı', 'Datasheet alanı'],
            'related_services' => $this->categoryServices[$category['slug']] ?? [],
        ];
    }

    private function resolveCategory(array $raw): ?array
    {
        $terms = collect($raw['categories'])
            ->where('domain', 'product_cat')
            ->values()
            ->all();

        $titleKey = $this->key($raw['title']);

        if ($this->hasAny($titleKey, ['kralfischer', 'karlfischer', 'kf'])) {
            return $this->findCategory('kral-fischer');
        }

        foreach ($terms as $term) {
            $termKey = $this->key($term['name'] . ' ' . $term['nicename']);

            if ($this->hasAny($termKey, ['potansyometriktitratorlervekralfischer', 'potansiyometriktitratorlervekralfischer'])) {
                if ($this->hasAny($titleKey, ['kralfischer', 'karlfischer', 'kf', 'mkc'])) {
                    return $this->findCategory('kral-fischer');
                }

                return $this->findCategory('potansiyometrik-titratorler');
            }

            foreach ($this->categories as $category) {
                $keys = collect([$category['name'], $category['slug'], ...($category['aliases'] ?? [])])
                    ->map(fn ($value) => $this->key($value))
                    ->all();

                if (in_array($termKey, $keys, true) || collect($keys)->contains(fn ($key) => $key !== '' && str_contains($termKey, $key))) {
                    return $category;
                }
            }
        }

        return $this->categoryFromTitle($raw['title']);
    }

    private function resolveBrand(array $raw, ?array $category = null): ?array
    {
        $terms = collect($raw['categories'])
            ->filter(fn ($term) => in_array($term['domain'], ['product_brand', 'pa_marka'], true))
            ->values()
            ->all();

        foreach ($terms as $term) {
            $termKey = $this->key($term['name'] . ' ' . $term['nicename']);

            foreach ($this->brands as $brand) {
                $keys = collect([$brand['name'], $brand['slug'], ...($brand['aliases'] ?? [])])
                    ->map(fn ($value) => $this->key($value))
                    ->all();

                if (in_array($termKey, $keys, true) || collect($keys)->contains(fn ($key) => $key !== '' && str_contains($termKey, $key))) {
                    return $brand;
                }
            }
        }

        $titleKey = $this->key($raw['title']);

        foreach ($this->brands as $brand) {
            $keys = collect([$brand['name'], ...($brand['aliases'] ?? [])])
                ->map(fn ($value) => $this->key($value))
                ->all();

            if (collect($keys)->contains(fn ($key) => $key !== '' && str_starts_with($titleKey, $key))) {
                return $brand;
            }
        }

        if ($category) {
            $inferredBrand = $this->brandFromModelFamily($titleKey, $category['slug']);

            if ($inferredBrand) {
                return $inferredBrand;
            }
        }

        return null;
    }

    private function findCategory(string $slug): ?array
    {
        return collect($this->categories)->firstWhere('slug', $slug);
    }

    private function findBrand(string $slug): ?array
    {
        return collect($this->brands)->firstWhere('slug', $slug);
    }

    private function brandFromModelFamily(string $titleKey, string $categorySlug): ?array
    {
        if (
            $categorySlug === 'kral-fischer'
            && ($this->hasAny($titleKey, ['kralfischer', 'karlfischer']) || preg_match('/^mk[cvh]\d+/', $titleKey))
        ) {
            return $this->findBrand('kyoto-kem');
        }

        if ($categorySlug === 'potansiyometrik-titratorler' && $this->hasAny($titleKey, ['at710'])) {
            return $this->findBrand('kyoto-kem');
        }

        if (
            $categorySlug === 'densitometre'
            && $this->hasAny($titleKey, ['da650', 'da645', 'da640', 'da100', 'densityspecificgravity', 'asca6400'])
        ) {
            return $this->findBrand('kyoto-kem');
        }

        if (
            $categorySlug === 'refraktometre'
            && $this->hasAny($titleKey, ['ra130', 'ra620', 'ra600', 'refractiveindex', 'asca6400'])
        ) {
            return $this->findBrand('kyoto-kem');
        }

        return null;
    }

    private function categoryFromTitle(string $title): ?array
    {
        $key = $this->key($title);
        $rules = [
            'teraziler' => ['terazi', 'balance'],
            'nem-tayin' => ['nemtayin', 'moisture'],
            'kral-fischer' => ['kralfischer', 'karlfischer'],
            'potansiyometrik-titratorler' => ['titrator', 'titratoru', 'titratör'],
            'densitometre' => ['densito', 'density', 'yogunluk'],
            'refraktometre' => ['refraktometre', 'refractometer'],
            'ph-metre' => ['phmetre'],
            'ph-iletkenlik' => ['iletkenlik', 'conductivity'],
            'viskozimetre' => ['viskozimetre', 'viscometer'],
            'etuv' => ['etuv', 'etüv', 'oven'],
            'balon-isiticilar' => ['balonisitici', 'heatingmantle'],
            'termoreaktor' => ['termoreaktor', 'thermoreactor'],
            'termal-analiz' => ['thermal', 'termalanaliz'],
            'homojenizator' => ['homojenizator', 'homogenizer'],
            'mekanik-karistirici' => ['mekanikkaristirici', 'overheadstirrer'],
            'manyetik-karistirici' => ['manyetikkaristirici', 'magneticstirrer'],
        ];

        foreach ($rules as $slug => $needles) {
            if ($this->hasAny($key, $needles)) {
                return $this->findCategory($slug);
            }
        }

        return null;
    }

    private function extractSpecs(array $raw): array
    {
        $labels = [
            'pa_hassasiyet' => 'Hassasiyet',
            'pa_kapasite' => 'Kapasite',
            'pa_cihaz-tipi' => 'Cihaz Tipi',
            'pa_dis-olculer' => 'Dış Ölçüler',
            'pa_kefe-boyutu' => 'Kefe Boyutu',
            'pa_renk' => 'Renk',
        ];

        $specs = [];

        foreach ($raw['categories'] as $term) {
            if (! str_starts_with($term['domain'], 'pa_') || $term['domain'] === 'pa_marka') {
                continue;
            }

            $label = $labels[$term['domain']] ?? Str::headline(str_replace(['pa_', '-'], ['', ' '], $term['domain']));
            $specs[$label] = $term['name'];
        }

        foreach ($this->extractTableSpecs($raw['content']) as $label => $value) {
            if (! isset($specs[$label])) {
                $specs[$label] = $value;
            }
        }

        return array_slice($specs, 0, 18, true);
    }

    private function extractTableSpecs(?string $html): array
    {
        $html = trim((string) $html);

        if ($html === '' || ! str_contains($html, '<table')) {
            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return [];
        }

        $specs = [];

        foreach ($dom->getElementsByTagName('tr') as $row) {
            $cells = [];

            foreach (['th', 'td'] as $tagName) {
                foreach ($row->getElementsByTagName($tagName) as $cell) {
                    $text = $this->cleanText($cell->textContent);

                    if ($text !== '') {
                        $cells[] = $text;
                    }
                }
            }

            if (count($cells) < 2) {
                continue;
            }

            $label = $this->normalizeSpecLabel($cells[0]);
            $value = trim(implode(' ', array_slice($cells, 1)));

            if ($label === '' || $value === '' || $this->isIgnoredSpecLabel($label)) {
                continue;
            }

            $specs[$label] = Str::limit($value, 260);
        }

        return $specs;
    }

    private function normalizeSpecLabel(string $label): string
    {
        $label = trim(preg_replace('/\s+/u', ' ', $label) ?? '');
        $label = trim($label, " \t\n\r\0\x0B:-");

        return Str::headline(Str::lower($label));
    }

    private function isIgnoredSpecLabel(string $label): bool
    {
        return $this->hasAny($label, [
            'teknikozellikler',
            'urunresmi',
            'gorsel',
            'resim',
            'image',
        ]);
    }

    private function resolveImage(array $raw, array $attachments, array $availableImages): ?string
    {
        $thumbnailId = $raw['meta']['_thumbnail_id'] ?? null;
        $candidates = [];

        if ($thumbnailId && isset($attachments[$thumbnailId])) {
            $candidates[] = $attachments[$thumbnailId]['basename'];
        }

        $candidates[] = $raw['slug'] . '.jpg';
        $candidates[] = $raw['slug'] . '.png';
        $candidates[] = $raw['slug'] . '.webp';

        foreach ($candidates as $candidate) {
            if (! $candidate) {
                continue;
            }

            $key = $this->imageKey($candidate);

            if (isset($availableImages[$key])) {
                return 'images/products/' . $availableImages[$key];
            }
        }

        $slugKey = $this->key($raw['slug']);
        $match = collect($availableImages)->first(fn ($filename, $key) => $slugKey !== '' && str_contains($this->key($key), $slugKey));

        return $match ? 'images/products/' . $match : null;
    }

    private function availableImages(): array
    {
        if (! is_dir($this->publicProductsPath)) {
            return [];
        }

        $images = [];

        foreach (scandir($this->publicProductsPath) ?: [] as $filename) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            if (! preg_match('/\.(jpe?g|png|webp)$/i', $filename)) {
                continue;
            }

            $images[$this->imageKey($filename)] = $filename;
        }

        return $images;
    }

    private function basenameFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return basename((string) parse_url(html_entity_decode($url), PHP_URL_PATH));
    }

    private function imageKey(string $filename): string
    {
        return strtolower(preg_replace('/-\d+x\d+(?=\.[a-z]+$)/i', '', $filename));
    }

    private function summary(array $raw, array $category, array $brand): string
    {
        $text = $this->cleanText($raw['excerpt']) ?: $this->cleanText($raw['content']);

        if ($text === '' || $this->isDemoText($text) || $this->isSpecDump($text)) {
            return $brand['name'] . ' markalı ' . $category['name'] . ' ürünü için teknik özellik, doküman ve teklif bilgisi.';
        }

        return Str::limit($text, 220);
    }

    private function features(array $category, array $brand): array
    {
        return [
            $brand['name'] . ' marka ürün grubu',
            $category['name'] . ' kategorisine uygun katalog kaydı',
            'Teknik özellik ve doküman alanları',
            'İlgili kalibrasyon hizmetleriyle eşleştirilebilir',
        ];
    }

    private function deriveModel(string $title, string $brand): string
    {
        $model = trim(preg_replace('/^' . preg_quote($brand, '/') . '\s+/iu', '', $title));

        if ($model === $title && $brand === 'A&D') {
            $model = trim(preg_replace('/^(A&D|AND|AnD)\s+/iu', '', $title));
        }

        return $model ?: $title;
    }

    private function cleanText(?string $html): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text ?? '');
    }

    private function isDemoText(string $text): bool
    {
        $key = $this->key($text);

        return $this->hasAny($key, [
            'loremipsum',
            'bathroomsink',
            'cubiliavestibulum',
            'vox',
            'demoeticaretdukkani',
            'dummyxtemos',
        ]);
    }

    private function isSpecDump(string $text): bool
    {
        $key = $this->key($text);

        return str_starts_with($key, 'teknikozellikler') || $this->hasAny($key, ['urunismiurunkodu', 'markamodelcihaztipi']);
    }

    private function exclude(array &$excluded, array &$reasonCounts, array $raw, string $reason, array $extra = []): void
    {
        $reasonCounts[$reason] = ($reasonCounts[$reason] ?? 0) + 1;

        $excluded[] = [
            'reason' => $reason,
            'wp_id' => $raw['post_id'],
            'title' => $raw['title'],
            'status' => $raw['status'],
            'categories' => $raw['categories'],
            ...$extra,
        ];
    }

    private function key(string $value): string
    {
        $value = Str::lower($value);
        $value = strtr($value, [
            'ı' => 'i',
            'İ' => 'i',
            'ğ' => 'g',
            'ü' => 'u',
            'ş' => 's',
            'ö' => 'o',
            'ç' => 'c',
            '&' => 'and',
            '+' => 'and',
        ]);

        return preg_replace('/[^a-z0-9]+/', '', $value) ?? '';
    }

    private function hasAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $this->key($needle))) {
                return true;
            }
        }

        return false;
    }
}
