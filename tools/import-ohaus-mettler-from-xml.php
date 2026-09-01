<?php

$root = dirname(__DIR__);
$xmlPath = dirname($root) . '/mtaendstri.WordPress.2026-08-26.xml';
$normalizedPath = $root . '/storage/app/imports/mta-products-normalized.json';

if (! is_file($xmlPath)) {
    fwrite(STDERR, "XML file not found: {$xmlPath}\n");
    exit(1);
}

$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$brands = [
    'ohaus' => [
        'name' => 'Ohaus',
        'slug' => 'ohaus',
        'summary' => 'Terazi, nem tayin, pH metre, iletkenlik ölçer, karıştırıcı ve laboratuvar cihazları.',
        'logo' => 'images/brands/ohaus.png',
        'aliases' => ['OHAUS', 'Ohaus Türkiye'],
    ],
    'mettler-toledo' => [
        'name' => 'Mettler Toledo',
        'slug' => 'mettler-toledo',
        'summary' => 'pH metre, iletkenlik, titrasyon, yoğunluk ve laboratuvar ölçüm cihazları.',
        'logo' => 'images/brands/mettler-toledo.png',
        'aliases' => ['Mettler Toledo', 'METTLER TOLEDO', 'Mettler Toledo Türkiye'],
    ],
];

$normalizedProducts = [];
if (is_file($normalizedPath)) {
    foreach (json_decode(file_get_contents($normalizedPath), true) ?: [] as $product) {
        if (! empty($product['slug'])) {
            $normalizedProducts[(string) $product['slug']] = $product;
        }
    }
}

function text_key(string $value): string
{
    $value = mb_strtolower($value, 'UTF-8');
    $value = strtr($value, [
        'ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c',
        'İ' => 'i', 'Ğ' => 'g', 'Ü' => 'u', 'Ş' => 's', 'Ö' => 'o', 'Ç' => 'c',
        'Ω' => 'ohm', 'Ω' => 'ohm', 'ω' => 'ohm',
    ]);

    return preg_replace('/[^a-z0-9]+/', '', $value) ?: '';
}

function slugify(string $value): string
{
    $value = mb_strtolower(trim($value), 'UTF-8');
    $value = rawurldecode($value);
    $value = strtr($value, [
        'ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c',
        'İ' => 'i', 'Ğ' => 'g', 'Ü' => 'u', 'Ş' => 's', 'Ö' => 'o', 'Ç' => 'c',
        'Ω' => 'ohm', 'Ω' => 'ohm', 'ω' => 'ohm', '&' => ' ve ',
    ]);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = trim((string) $value, '-');

    return preg_replace('/-+/', '-', $value) ?: 'urun';
}

function normalize_product_slug(string $slug): string
{
    $slug = trim($slug);
    $slug = str_ireplace('k%cf%89', 'k-ohm', $slug);
    $slug = str_replace(['kω', 'kΩ', 'kΩ'], 'k-ohm', $slug);
    $decoded = rawurldecode($slug);
    $decoded = str_replace(['kω', 'kΩ', 'kΩ'], 'k-ohm', $decoded);

    $special = [
        'ohaus-st400d-g-portatif-cozunmus-oksijen-metre' => 'ohaus-st400d-g',
    ];

    return $special[$decoded] ?? slugify($decoded);
}

function clean_text(string $html): string
{
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim((string) $text);
}

function limit_text(string $text, int $limit): string
{
    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $limit - 3, 'UTF-8')) . '...';
}

function dom_from_html(string $html): ?DOMDocument
{
    if (trim($html) === '') {
        return null;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();

    return $loaded ? $dom : null;
}

function extract_specs(string $html): array
{
    $dom = dom_from_html($html);
    if (! $dom) {
        return [];
    }

    $specs = [];
    $lastKey = null;

    foreach ($dom->getElementsByTagName('tr') as $row) {
        $cells = [];

        foreach (['th', 'td'] as $tag) {
            foreach ($row->getElementsByTagName($tag) as $cell) {
                $cells[] = trim(preg_replace('/\s+/u', ' ', $cell->textContent));
            }
        }

        $cells = array_values(array_filter($cells, fn ($cell) => $cell !== ''));

        if (count($cells) < 2) {
            continue;
        }

        $key = trim($cells[0], " \t\n\r\0\x0B:：");
        $value = trim(implode(' ', array_slice($cells, 1)));

        if ($key === '' && $lastKey) {
            $specs[$lastKey] .= '; ' . $value;
            continue;
        }

        if ($key !== '' && $value !== '' && mb_strlen($key, 'UTF-8') <= 90) {
            $specs[$key] = $value;
            $lastKey = $key;
        }
    }

    return $specs;
}

function extract_documents(string $html): array
{
    $dom = dom_from_html($html);
    if (! $dom) {
        return [];
    }

    $documents = [];
    foreach ($dom->getElementsByTagName('a') as $link) {
        $href = trim((string) $link->getAttribute('href'));
        $title = trim(preg_replace('/\s+/u', ' ', $link->textContent));

        if ($href === '' || ! preg_match('/\.(pdf|docx?|xlsx?)(\?|$)/i', $href)) {
            continue;
        }

        $lower = mb_strtolower($title . ' ' . $href, 'UTF-8');
        $type = 'catalog';
        if (str_contains($lower, 'şartname') || str_contains($lower, 'sartname')) {
            $type = 'specification';
        } elseif (str_contains($lower, 'ce') || str_contains($lower, 'doc')) {
            $type = 'certificate';
        } elseif (str_contains($lower, 'broşür') || str_contains($lower, 'brosur') || str_contains($lower, 'datasheet')) {
            $type = 'catalog';
        }

        if ($title === '' || mb_strlen($title, 'UTF-8') > 80) {
            $title = match ($type) {
                'specification' => 'Şartname',
                'certificate' => 'CE Belgesi',
                default => 'Broşür',
            };
        }

        $documents[$href] = ['title' => $title, 'type' => $type, 'url' => $href];
    }

    return array_values($documents);
}

function extract_videos(string $html): array
{
    preg_match_all('/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,})[^\s"\'<>]*/', $html, $matches, PREG_SET_ORDER);

    $videos = [];
    foreach ($matches as $match) {
        $id = $match[1];
        $videos[$id] = [
            'title' => 'Ürün Videosu',
            'youtube_url' => html_entity_decode($match[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'youtube_id' => $id,
        ];
    }

    return array_values($videos);
}

function category_slug_from_terms(array $terms, array $knownCategories): string
{
    $map = [
        'ph-iletkenlik-olcerler' => 'ph-iletkenlik',
        'ph-iletkenlik-o2' => 'ph-iletkenlik',
        'portatif-ph-cihazlar' => 'ph-iletkenlik',
        'yogunluk-ozgul-agirlik-olcer' => 'densitometre',
        'yogunluk-olcerler' => 'densitometre',
        'mekanik-karistiricilar' => 'mekanik-karistirici',
        'isitmali-manyetik-karistiricilar' => 'isitmali-manyetik-karistirici',
        'manyetik-karistiricilar' => 'manyetik-karistirici',
        'hassas-teraziler' => 'hassas-teraziler',
        'teraziler' => 'teraziler',
    ];

    $preferred = [];
    foreach ($terms as $term) {
        if (($term['domain'] ?? '') !== 'product_cat') {
            continue;
        }

        $slug = slugify((string) ($term['nicename'] ?: $term['name']));
        if (in_array($slug, ['simple', 'uncategorized'], true)) {
            continue;
        }

        $preferred[] = $map[$slug] ?? $slug;
    }

    foreach (['ph-iletkenlik', 'densitometre', 'hassas-teraziler', 'mekanik-karistirici', 'isitmali-manyetik-karistirici', 'manyetik-karistirici', 'teraziler'] as $slug) {
        if (in_array($slug, $preferred, true) && isset($knownCategories[$slug])) {
            return $slug;
        }
    }

    foreach ($preferred as $slug) {
        if (isset($knownCategories[$slug])) {
            return $slug;
        }
    }

    return $preferred[0] ?? 'ph-iletkenlik';
}

function detect_brand(array $raw): ?string
{
    $haystack = text_key($raw['title'] . ' ' . implode(' ', array_map(fn ($term) => $term['name'] . ' ' . $term['nicename'], $raw['categories'])));

    if (str_contains($haystack, 'ohaus')) {
        return 'ohaus';
    }

    if (str_contains($haystack, 'mettler') || str_contains($haystack, 'toledo')) {
        return 'mettler-toledo';
    }

    return null;
}

function parse_products_from_xml(string $xmlPath): array
{
    $products = [];
    $reader = new XMLReader();
    $reader->open($xmlPath);

    while ($reader->read()) {
        if ($reader->nodeType !== XMLReader::ELEMENT || $reader->localName !== 'item') {
            continue;
        }

        $item = simplexml_load_string($reader->readOuterXml(), 'SimpleXMLElement', LIBXML_NOCDATA);
        if (! $item) {
            continue;
        }

        $namespaces = $item->getNamespaces(true);
        $wp = $item->children($namespaces['wp'] ?? '');
        $content = $item->children($namespaces['content'] ?? '');
        $excerpt = $item->children($namespaces['excerpt'] ?? '');

        if ((string) ($wp->post_type ?? '') !== 'product' || (string) ($wp->status ?? '') !== 'publish') {
            continue;
        }

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

        $raw = [
            'wp_id' => (string) ($wp->post_id ?? ''),
            'title' => trim((string) $item->title),
            'slug' => (string) ($wp->post_name ?? ''),
            'link' => (string) $item->link,
            'content' => (string) ($content->encoded ?? ''),
            'excerpt' => (string) ($excerpt->encoded ?? ''),
            'categories' => $categories,
            'meta' => $meta,
        ];

        $brandSlug = detect_brand($raw);
        if (! in_array($brandSlug, ['ohaus', 'mettler-toledo'], true)) {
            continue;
        }

        $raw['brand_slug'] = $brandSlug;
        $products[] = $raw;
    }

    $reader->close();

    return $products;
}

function upsert_redirect(PDO $db, string $sourcePath, string $targetPath, string $now): bool
{
    $sourcePath = '/' . trim($sourcePath, '/');
    $targetPath = '/' . trim($targetPath, '/');

    if ($sourcePath === '/' || $sourcePath === $targetPath) {
        return false;
    }

    $stmt = $db->prepare('select id from redirects where source_path = :source_path');
    $stmt->execute(['source_path' => $sourcePath]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare('update redirects set target_path = :target_path, status_code = 301, is_active = 1, updated_at = :updated_at where id = :id');
        $stmt->execute(['id' => $id, 'target_path' => $targetPath, 'updated_at' => $now]);
        return true;
    }

    $stmt = $db->prepare('insert into redirects (source_path, target_path, status_code, is_active, created_at, updated_at) values (:source_path, :target_path, 301, 1, :created_at, :updated_at)');
    $stmt->execute([
        'source_path' => $sourcePath,
        'target_path' => $targetPath,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return true;
}

$knownCategories = [];
foreach ($db->query('select id, name, slug, summary from product_categories') as $row) {
    $knownCategories[$row['slug']] = [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'slug' => $row['slug'],
        'summary' => $row['summary'],
    ];
}

$rawProducts = parse_products_from_xml($xmlPath);

$db->beginTransaction();

$selectBrand = $db->prepare('select id from product_brands where slug = :slug');
$insertBrand = $db->prepare('insert into product_brands (name, slug, summary, logo, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :logo, :aliases, 1, :sort_order, :created_at, :updated_at)');
$updateBrand = $db->prepare('update product_brands set name = :name, summary = :summary, logo = :logo, aliases = :aliases, is_active = 1, updated_at = :updated_at where id = :id');
$brandIds = [];

foreach ($brands as $brand) {
    $selectBrand->execute(['slug' => $brand['slug']]);
    $brandId = $selectBrand->fetchColumn();

    if ($brandId) {
        $updateBrand->execute([
            'id' => $brandId,
            'name' => $brand['name'],
            'summary' => $brand['summary'],
            'logo' => $brand['logo'],
            'aliases' => $json($brand['aliases']),
            'updated_at' => $now,
        ]);
    } else {
        $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_brands')->fetchColumn();
        $insertBrand->execute([
            'name' => $brand['name'],
            'slug' => $brand['slug'],
            'summary' => $brand['summary'],
            'logo' => $brand['logo'],
            'aliases' => $json($brand['aliases']),
            'sort_order' => $sortOrder,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $brandId = $db->lastInsertId();
    }

    $brandIds[$brand['slug']] = (int) $brandId;
}

$selectCategory = $db->prepare('select id, name, summary from product_categories where slug = :slug');
$insertCategory = $db->prepare('insert into product_categories (name, slug, summary, image, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, null, :aliases, 1, :sort_order, :created_at, :updated_at)');
$selectCategoryBrand = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$insertCategoryBrand = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');

$selectProduct = $db->prepare('select id from products where slug = :slug limit 1');
$insertProduct = $db->prepare("insert into products (product_category_id, product_brand_id, name, slug, model, sku, wp_id, old_url, summary, body, image, image_alt, gallery, features, metadata, specs, status, is_featured, sort_order, published_at, seo_title, meta_description, canonical_url, og_title, og_description, og_image, robots, created_at, updated_at) values (:category_id, :brand_id, :name, :slug, :model, :sku, :wp_id, :old_url, :summary, :body, null, :image_alt, null, :features, :metadata, :specs, 'published', 0, :sort_order, :published_at, :seo_title, :meta_description, null, null, null, null, 'index,follow', :created_at, :updated_at)");
$updateProduct = $db->prepare("update products set product_category_id = :category_id, product_brand_id = :brand_id, name = :name, model = :model, sku = :sku, wp_id = :wp_id, old_url = :old_url, summary = :summary, body = :body, image = null, image_alt = :image_alt, gallery = null, features = :features, metadata = :metadata, specs = :specs, status = 'published', is_featured = 0, sort_order = :sort_order, published_at = :published_at, seo_title = :seo_title, meta_description = :meta_description, robots = 'index,follow', updated_at = :updated_at where id = :id");

$countRealDocuments = $db->prepare("select count(*) from product_documents where product_id = :product_id and coalesce(url, '') <> ''");
$deleteDocuments = $db->prepare('delete from product_documents where product_id = :product_id');
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, null, :url, :sort_order, :created_at, :updated_at)');
$countVideos = $db->prepare('select count(*) from product_videos where product_id = :product_id');
$insertVideo = $db->prepare('insert into product_videos (product_id, title, youtube_url, youtube_id, sort_order, created_at, updated_at) values (:product_id, :title, :youtube_url, :youtube_id, :sort_order, :created_at, :updated_at)');

$stats = [
    'matched' => count($rawProducts),
    'inserted' => 0,
    'updated' => 0,
    'documents_inserted' => 0,
    'documents_preserved' => 0,
    'videos_inserted' => 0,
    'redirects_upserted' => 0,
];

foreach ($rawProducts as $index => $raw) {
    $rawSlug = (string) $raw['slug'];
    $slug = normalize_product_slug($rawSlug);
    $normalized = $normalizedProducts[$rawSlug] ?? $normalizedProducts[$slug] ?? [];
    $brandSlug = $normalized['brand_slug'] ?? $raw['brand_slug'];
    $brand = $brands[$brandSlug];
    $brandId = $brandIds[$brandSlug];

    $categorySlug = $normalized['category_slug'] ?? category_slug_from_terms($raw['categories'], $knownCategories);
    $selectCategory->execute(['slug' => $categorySlug]);
    $categoryRow = $selectCategory->fetch(PDO::FETCH_ASSOC);

    if (! $categoryRow) {
        $categoryName = $normalized['category'] ?? ucwords(str_replace('-', ' ', $categorySlug));
        $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_categories')->fetchColumn();
        $insertCategory->execute([
            'name' => $categoryName,
            'slug' => $categorySlug,
            'summary' => $categoryName . ' ürünleri.',
            'aliases' => $json([$categoryName]),
            'sort_order' => $sortOrder,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $categoryId = (int) $db->lastInsertId();
        $categoryName = $categoryName;
        $knownCategories[$categorySlug] = ['id' => $categoryId, 'name' => $categoryName, 'slug' => $categorySlug, 'summary' => $categoryName . ' ürünleri.'];
    } else {
        $categoryId = (int) $categoryRow['id'];
        $categoryName = $categoryRow['name'];
    }

    $selectCategoryBrand->execute(['category_id' => $categoryId, 'brand_id' => $brandId]);
    if ((int) $selectCategoryBrand->fetchColumn() === 0) {
        $insertCategoryBrand->execute([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $rawText = clean_text($raw['excerpt'] ?: $raw['content']);
    $summary = $normalized['summary'] ?? ($rawText !== '' ? limit_text($rawText, 420) : "{$raw['title']}; {$categoryName} kategorisinde {$brand['name']} markalı ürün kaydıdır.");
    $body = $summary . ' Teknik özellikler ve teklif bilgisi için ürün dokümanları incelenebilir.';
    $specs = $normalized['specs'] ?? extract_specs($raw['content']);
    $metadata = $normalized['metadata'] ?? [
        'Marka' => $brand['name'],
        'Kategori' => $categoryName,
        'Model' => $normalized['model'] ?? preg_replace('/^(OHAUS|Ohaus|Mettler Toledo)\s+/iu', '', $raw['title']),
        'SKU' => $raw['meta']['_sku'] ?? 'Yayın öncesi netleştirilecek',
    ];
    $metadata['Görsel durumu'] = 'Placeholder';
    $features = $normalized['features'] ?? [
        $brand['name'] . ' marka ürün',
        $categoryName . ' kategorisi',
        'Teknik özellik ve teklif talebine uygun katalog kaydı',
    ];

    $payload = [
        'category_id' => $categoryId,
        'brand_id' => $brandId,
        'name' => $normalized['name'] ?? $raw['title'],
        'model' => $normalized['model'] ?? preg_replace('/^(OHAUS|Ohaus|Mettler Toledo)\s+/iu', '', $raw['title']),
        'sku' => $normalized['sku'] ?? ($raw['meta']['_sku'] ?? ('MTA-' . strtoupper($brandSlug) . '-' . strtoupper(slugify($raw['title'])))),
        'wp_id' => $normalized['wp_id'] ?? $raw['wp_id'],
        'old_url' => $normalized['old_url'] ?? $raw['link'],
        'summary' => $summary,
        'body' => $body,
        'image_alt' => $normalized['image_alt'] ?? (($normalized['name'] ?? $raw['title']) . ' ürün görseli'),
        'features' => $json(array_values($features)),
        'metadata' => $json($metadata),
        'specs' => $json($specs ?: ['Ürün grubu' => $categoryName, 'Marka' => $brand['name']]),
        'sort_order' => 3000 + (($index + 1) * 10),
        'published_at' => $now,
        'seo_title' => $normalized['seo_title'] ?? (($normalized['name'] ?? $raw['title']) . ' | MTA Endüstri'),
        'meta_description' => $normalized['meta_description'] ?? limit_text($summary, 155),
        'updated_at' => $now,
    ];

    $selectProduct->execute(['slug' => $slug]);
    $productId = $selectProduct->fetchColumn();

    if ($productId) {
        $payload['id'] = $productId;
        $updateProduct->execute($payload);
        $stats['updated']++;
    } else {
        $payload['slug'] = $slug;
        $payload['created_at'] = $now;
        $insertProduct->execute($payload);
        $productId = $db->lastInsertId();
        $stats['inserted']++;
    }

    $documents = extract_documents($raw['content']);
    if ($documents === [] && isset($normalized['documents']) && is_array($normalized['documents'])) {
        foreach ($normalized['documents'] as $document) {
            if (is_array($document)) {
                $documents[] = [
                    'title' => $document['title'] ?? 'Doküman',
                    'type' => $document['type'] ?? 'catalog',
                    'url' => $document['url'] ?? null,
                ];
            } elseif (is_string($document) && trim($document) !== '') {
                $documents[] = ['title' => trim($document), 'type' => 'catalog', 'url' => null];
            }
        }
    }

    $countRealDocuments->execute(['product_id' => $productId]);
    if ((int) $countRealDocuments->fetchColumn() > 0) {
        $stats['documents_preserved']++;
    } elseif ($documents !== []) {
        $deleteDocuments->execute(['product_id' => $productId]);
        foreach ($documents as $documentIndex => $document) {
            $insertDocument->execute([
                'product_id' => $productId,
                'title' => $document['title'],
                'type' => $document['type'],
                'url' => $document['url'] ?? null,
                'sort_order' => ($documentIndex + 1) * 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $stats['documents_inserted']++;
        }
    }

    $videos = extract_videos($raw['content']);
    $countVideos->execute(['product_id' => $productId]);
    if ((int) $countVideos->fetchColumn() === 0 && $videos !== []) {
        foreach ($videos as $videoIndex => $video) {
            $insertVideo->execute([
                'product_id' => $productId,
                'title' => $video['title'],
                'youtube_url' => $video['youtube_url'],
                'youtube_id' => $video['youtube_id'],
                'sort_order' => ($videoIndex + 1) * 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $stats['videos_inserted']++;
        }
    }

    if ($rawSlug !== $slug) {
        $stats['redirects_upserted'] += upsert_redirect($db, '/urun/' . $rawSlug, '/urun/' . $slug, $now) ? 1 : 0;
    }

    $oldPath = parse_url($raw['link'], PHP_URL_PATH);
    if (is_string($oldPath) && $oldPath !== '') {
        $stats['redirects_upserted'] += upsert_redirect($db, $oldPath, '/urun/' . $slug, $now) ? 1 : 0;
    }
}

$db->commit();

echo 'Imported XML Ohaus/Mettler products: ' . json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
