<?php

$root = dirname(__DIR__);
$normalizedPath = $root . '/storage/app/imports/mta-products-normalized.json';
$xmlPath = dirname($root) . '/mtaendstri.WordPress.2026-08-26.xml';

if (! is_file($normalizedPath)) {
    fwrite(STDERR, "Normalized import file not found: {$normalizedPath}\n");
    exit(1);
}

$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$brands = [
    'and' => [
        'name' => 'A&D',
        'slug' => 'and',
        'summary' => 'Hassas terazi, analitik terazi, mikro terazi, endüstriyel terazi ve nem tayin cihazları.',
        'logo' => 'images/brands/and.png',
        'aliases' => ['AND', 'A&D', 'A and D'],
    ],
    'kyoto-kem' => [
        'name' => 'Kyoto KEM',
        'slug' => 'kyoto-kem',
        'summary' => 'Titrasyon, Karl Fischer, yoğunluk ve refraktometre analiz cihazları.',
        'logo' => 'images/brands/kyoto-kem.png',
        'aliases' => ['Kyoto KEM', 'KEM', 'Kyoto Electronics'],
    ],
];

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

function xml_context_by_slug(string $xmlPath): array
{
    if (! is_file($xmlPath)) {
        return [];
    }

    $reader = new XMLReader();
    $reader->open($xmlPath);
    $items = [];

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

        if ((string) ($wp->post_type ?? '') !== 'product') {
            continue;
        }

        $slug = (string) ($wp->post_name ?? '');
        if ($slug === '') {
            continue;
        }

        $html = (string) ($content->encoded ?? '');
        $documents = [];
        if ($html !== '') {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            libxml_clear_errors();

            if ($loaded) {
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
                    } elseif (str_contains($lower, 'ce')) {
                        $type = 'certificate';
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
            }
        }

        preg_match_all('/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,})[^\s"\'<>]*/', $html, $matches, PREG_SET_ORDER);
        $videos = [];
        foreach ($matches as $match) {
            $videos[$match[1]] = [
                'title' => 'Ürün Videosu',
                'youtube_url' => html_entity_decode($match[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'youtube_id' => $match[1],
            ];
        }

        $items[$slug] = [
            'wp_id' => (string) ($wp->post_id ?? ''),
            'old_url' => (string) $item->link,
            'body' => clean_text($html ?: (string) ($excerpt->encoded ?? '')),
            'documents' => array_values($documents),
            'videos' => array_values($videos),
        ];
    }

    $reader->close();

    return $items;
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

$products = array_values(array_filter(
    json_decode(file_get_contents($normalizedPath), true) ?: [],
    fn (array $product): bool => in_array($product['brand_slug'] ?? '', ['and', 'kyoto-kem'], true),
));

$xmlContext = xml_context_by_slug($xmlPath);

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

$selectCategory = $db->prepare('select id, name from product_categories where slug = :slug');
$insertCategory = $db->prepare('insert into product_categories (name, slug, summary, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :aliases, 1, :sort_order, :created_at, :updated_at)');
$selectCategoryBrand = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$insertCategoryBrand = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');

$selectProduct = $db->prepare('select id from products where slug = :slug limit 1');
$insertProduct = $db->prepare("insert into products (product_category_id, product_brand_id, name, slug, model, sku, wp_id, old_url, summary, body, image, image_alt, gallery, features, metadata, specs, status, is_featured, sort_order, published_at, seo_title, meta_description, robots, created_at, updated_at) values (:category_id, :brand_id, :name, :slug, :model, :sku, :wp_id, :old_url, :summary, :body, null, :image_alt, null, :features, :metadata, :specs, 'published', 0, :sort_order, :published_at, :seo_title, :meta_description, 'index,follow', :created_at, :updated_at)");
$updateProduct = $db->prepare("update products set product_category_id = :category_id, product_brand_id = :brand_id, name = :name, model = :model, sku = :sku, wp_id = :wp_id, old_url = :old_url, summary = :summary, body = :body, image = null, image_alt = :image_alt, gallery = null, features = :features, metadata = :metadata, specs = :specs, status = 'published', is_featured = 0, sort_order = :sort_order, published_at = :published_at, seo_title = :seo_title, meta_description = :meta_description, robots = 'index,follow', updated_at = :updated_at where id = :id");
$deleteDocuments = $db->prepare('delete from product_documents where product_id = :product_id');
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, null, :url, :sort_order, :created_at, :updated_at)');
$deleteVideos = $db->prepare('delete from product_videos where product_id = :product_id');
$insertVideo = $db->prepare('insert into product_videos (product_id, title, youtube_url, youtube_id, sort_order, created_at, updated_at) values (:product_id, :title, :youtube_url, :youtube_id, :sort_order, :created_at, :updated_at)');

$stats = ['matched' => count($products), 'inserted' => 0, 'updated' => 0, 'documents_inserted' => 0, 'videos_inserted' => 0, 'redirects_upserted' => 0];

foreach ($products as $index => $product) {
    $brandSlug = $product['brand_slug'];
    $brandId = $brandIds[$brandSlug];
    $categorySlug = $product['category_slug'] ?: 'urunler';
    $selectCategory->execute(['slug' => $categorySlug]);
    $categoryRow = $selectCategory->fetch(PDO::FETCH_ASSOC);

    if (! $categoryRow) {
        $categoryName = $product['category'] ?: ucwords(str_replace('-', ' ', $categorySlug));
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
    } else {
        $categoryId = (int) $categoryRow['id'];
        $categoryName = $categoryRow['name'];
    }

    $selectCategoryBrand->execute(['category_id' => $categoryId, 'brand_id' => $brandId]);
    if ((int) $selectCategoryBrand->fetchColumn() === 0) {
        $insertCategoryBrand->execute(['category_id' => $categoryId, 'brand_id' => $brandId, 'created_at' => $now, 'updated_at' => $now]);
    }

    $context = $xmlContext[$product['slug']] ?? [];
    $summary = $product['summary'] ?: (($context['body'] ?? '') !== '' ? limit_text($context['body'], 420) : "{$product['name']}; {$categoryName} kategorisinde {$brands[$brandSlug]['name']} markalı ürün kaydıdır.");
    $body = ($context['body'] ?? '') !== '' ? limit_text($context['body'], 1800) : $summary . ' Teknik özellikler ve teklif bilgisi için ürün dokümanları incelenebilir.';
    $metadata = $product['metadata'] ?: [
        'Marka' => $brands[$brandSlug]['name'],
        'Kategori' => $categoryName,
        'Model' => $product['model'] ?: $product['name'],
        'SKU' => $product['sku'] ?: 'Yayın öncesi netleştirilecek',
    ];
    $metadata['Görsel durumu'] = 'Placeholder';

    $payload = [
        'category_id' => $categoryId,
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'] ?: $product['name'],
        'sku' => $product['sku'] ?: 'MTA-' . strtoupper($brandSlug) . '-' . strtoupper(str_replace('-', '', $product['slug'])),
        'wp_id' => $product['wp_id'] ?: ($context['wp_id'] ?? null),
        'old_url' => $product['old_url'] ?: ($context['old_url'] ?? null),
        'summary' => $summary,
        'body' => $body,
        'image_alt' => $product['image_alt'] ?: ($product['name'] . ' ürün görseli'),
        'features' => $json($product['features'] ?: [$brands[$brandSlug]['name'] . ' marka ürün', $categoryName . ' kategorisi']),
        'metadata' => $json($metadata),
        'specs' => $json(($product['specs'] ?: ['Ürün grubu' => $categoryName, 'Marka' => $brands[$brandSlug]['name']])),
        'sort_order' => 3400 + (($index + 1) * 10),
        'published_at' => $now,
        'seo_title' => $product['seo_title'] ?: ($product['name'] . ' | MTA Endüstri'),
        'meta_description' => $product['meta_description'] ?: limit_text($summary, 155),
        'updated_at' => $now,
    ];

    $selectProduct->execute(['slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    if ($productId) {
        $payload['id'] = $productId;
        $updateProduct->execute($payload);
        $stats['updated']++;
    } else {
        $payload['slug'] = $product['slug'];
        $payload['created_at'] = $now;
        $insertProduct->execute($payload);
        $productId = $db->lastInsertId();
        $stats['inserted']++;
    }

    $documents = $context['documents'] ?? [];
    if ($documents !== []) {
        $deleteDocuments->execute(['product_id' => $productId]);
        foreach ($documents as $documentIndex => $document) {
            $insertDocument->execute([
                'product_id' => $productId,
                'title' => $document['title'],
                'type' => $document['type'],
                'url' => $document['url'],
                'sort_order' => ($documentIndex + 1) * 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $stats['documents_inserted']++;
        }
    }

    $videos = $context['videos'] ?? [];
    if ($videos !== []) {
        $deleteVideos->execute(['product_id' => $productId]);
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

    $oldPath = parse_url((string) ($payload['old_url'] ?? ''), PHP_URL_PATH);
    if (is_string($oldPath) && $oldPath !== '') {
        $stats['redirects_upserted'] += upsert_redirect($db, $oldPath, '/urun/' . $product['slug'], $now) ? 1 : 0;
    }
}

$db->commit();

echo 'Imported A&D/Kyoto KEM products: ' . json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
