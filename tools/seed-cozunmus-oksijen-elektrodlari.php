<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'Çözünmüş Oksijen Ölçüm Elektrodları',
    'slug' => 'cozunmus-oksijen-olcum-elektrodlari',
    'summary' => 'Çözünmüş oksijen ölçümleri için galvanik ve optik sensörlü elektrodlar.',
    'aliases' => ['Elektrodlar', 'Elektrotlar', 'Oksijen Elektrodu', 'Çözünmüş Oksijen Elektrodu', 'DO Elektrodu'],
];

$brand = [
    'name' => 'SI Analitik',
    'slug' => 'si-analitik',
    'summary' => 'Elektrokimya, titrasyon ve laboratuvar ölçüm ekipmanları.',
    'logo' => 'images/brands/si-analitik.png',
    'aliases' => ['SI Analytics', 'SI Analytic', 'SI Analitik'],
];

$brochures = [
    'ox-1100-plus-cozunmus-oksijen-olcum-elektrodu' => [],
];

$products = [
    [
        'name' => 'OX 1100+ Çözünmüş Oksijen Ölçüm Elektrodu',
        'slug' => 'ox-1100-plus-cozunmus-oksijen-olcum-elektrodu',
        'image_slugs' => ['ox-1100-plus-cozunmus-oksijen-olcum-elektrodu'],
        'model' => 'OX 1100+',
        'summary' => 'OX 1100+; 0-60 mg/L çözünmüş oksijen ölçüm aralığına sahip, NTC sıcaklık kompanzasyonlu galvanik oksijen elektrodudur.',
        'body' => 'OX 1100+ çözünmüş oksijen ölçüm elektrodu; galvanik sensör yapısı, Pt katot, Ag anot, SMEK tipi kafa bağlantısı ve NTC sıcaklık kompanzasyonu ile çözünmüş oksijen ölçümlerinde kullanılır. Uygun bağlantı kablosu LS 1 ST4 OX olarak belirtilmiştir.',
        'features' => [
            'Galvanik sensör yapısı',
            'Pt katot ve Ag anot',
            'SMEK tipi kafa bağlantısı',
            'NTC sıcaklık kompanzasyonu',
            'LS 1 ST4 OX bağlantı kablosu ile uyumluluk',
        ],
        'specs' => [
            'Ürün tipi' => 'Çözünmüş oksijen ölçüm elektrodu',
            'Model' => 'OX 1100+',
            'Uzunluk' => '120 mm',
            'Çalışma sıcaklığı' => '0-45 °C',
            'Ölçüm aralığı' => '0-60 mg/L',
            'Sensör tipi' => 'Galvanik sensör',
            'Katot' => 'Pt',
            'Anot' => 'Ag',
            'Kafa bağlantısı' => 'SMEK tipi',
            'Sıcaklık kompanzasyonu' => 'NTC',
            'Uygun bağlantı kablosu' => 'LS 1 ST4 OX',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Çözünmüş Oksijen Ölçüm Elektrodları',
            'Model' => 'OX 1100+',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Çözünmüş oksijen ölçüm elektrodu',
            'Ölçüm aralığı' => '0-60 mg/L',
        ],
        'image_alt' => 'OX 1100+ çözünmüş oksijen ölçüm elektrodu ürün görseli',
    ],
];

$db->beginTransaction();

$stmt = $db->prepare('select id from product_categories where slug = :slug');
$stmt->execute(['slug' => $category['slug']]);
$categoryId = $stmt->fetchColumn();

if (! $categoryId) {
    $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_categories')->fetchColumn();
    $stmt = $db->prepare('insert into product_categories (name, slug, summary, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :aliases, 1, :sort_order, :created_at, :updated_at)');
    $stmt->execute([
        'name' => $category['name'],
        'slug' => $category['slug'],
        'summary' => $category['summary'],
        'aliases' => $json($category['aliases']),
        'sort_order' => $sortOrder,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $categoryId = $db->lastInsertId();
} else {
    $stmt = $db->prepare('update product_categories set name = :name, summary = :summary, aliases = :aliases, is_active = 1, updated_at = :updated_at where id = :id');
    $stmt->execute([
        'id' => $categoryId,
        'name' => $category['name'],
        'summary' => $category['summary'],
        'aliases' => $json($category['aliases']),
        'updated_at' => $now,
    ]);
}

$stmt = $db->prepare('select id from product_brands where slug = :slug');
$stmt->execute(['slug' => $brand['slug']]);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_brands')->fetchColumn();
    $stmt = $db->prepare('insert into product_brands (name, slug, summary, logo, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :logo, :aliases, 1, :sort_order, :created_at, :updated_at)');
    $stmt->execute([
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
} else {
    $stmt = $db->prepare('update product_brands set name = :name, summary = :summary, logo = :logo, aliases = :aliases, is_active = 1, updated_at = :updated_at where id = :id');
    $stmt->execute([
        'id' => $brandId,
        'name' => $brand['name'],
        'summary' => $brand['summary'],
        'logo' => $brand['logo'],
        'aliases' => $json($brand['aliases']),
        'updated_at' => $now,
    ]);
}

$stmt = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$stmt->execute(['category_id' => $categoryId, 'brand_id' => $brandId]);

if ((int) $stmt->fetchColumn() === 0) {
    $stmt = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');
    $stmt->execute([
        'category_id' => $categoryId,
        'brand_id' => $brandId,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}

$selectProduct = $db->prepare('select id from products where product_category_id = :category_id and slug = :slug');
$insertProduct = $db->prepare("insert into products (product_category_id, product_brand_id, name, slug, model, sku, old_url, summary, body, image, image_alt, features, metadata, specs, status, is_featured, sort_order, published_at, created_at, updated_at) values (:category_id, :brand_id, :name, :slug, :model, :sku, :old_url, :summary, :body, :image, :image_alt, :features, :metadata, :specs, 'published', 0, :sort_order, :published_at, :created_at, :updated_at)");
$updateProduct = $db->prepare("update products set product_brand_id = :brand_id, name = :name, model = :model, sku = :sku, old_url = :old_url, summary = :summary, body = :body, image = :image, image_alt = :image_alt, features = :features, metadata = :metadata, specs = :specs, status = 'published', is_featured = 0, sort_order = :sort_order, published_at = :published_at, updated_at = :updated_at where id = :id");
$deleteDocuments = $db->prepare('delete from product_documents where product_id = :product_id');
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, :path, :url, :sort_order, :created_at, :updated_at)');

foreach ($products as $index => $product) {
    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => $product['metadata']['SKU'] === 'Yayın öncesi netleştirilecek' ? null : $product['metadata']['SKU'],
        'old_url' => null,
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['image_slugs']),
        'image_alt' => $product['image_alt'],
        'features' => $json($product['features']),
        'metadata' => $json($product['metadata']),
        'specs' => $json($product['specs']),
        'sort_order' => ($index + 1) * 10,
        'published_at' => $now,
        'updated_at' => $now,
    ];

    if ($productId) {
        $payload['id'] = $productId;
        $updateProduct->execute($payload);
    } else {
        $insertProduct->execute([
            'category_id' => $categoryId,
            'slug' => $product['slug'],
            'created_at' => $now,
            ...$payload,
        ]);
        $productId = $db->lastInsertId();
    }

    $deleteDocuments->execute(['product_id' => $productId]);

    foreach ($brochures[$product['slug']] as $documentIndex => $document) {
        $insertDocument->execute([
            'product_id' => $productId,
            'title' => $document['title'] ?? 'Broşür',
            'type' => $document['type'] ?? 'catalog',
            'path' => $document['path'] ?? null,
            'url' => $document['url'] ?? null,
            'sort_order' => ($documentIndex + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

$db->commit();

echo 'category_id=' . $categoryId . PHP_EOL;
echo 'brand_id=' . $brandId . PHP_EOL;
foreach ($products as $product) {
    echo 'product_slug=' . $product['slug'] . PHP_EOL;
    echo 'image=' . ($imageFor($product['image_slugs']) ?: 'missing') . PHP_EOL;
    echo 'documents=' . count($brochures[$product['slug']]) . PHP_EOL;
}
echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
