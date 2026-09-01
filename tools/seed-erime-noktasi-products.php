<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'Erime Noktası',
    'slug' => 'erime-noktasi',
    'summary' => 'Erime noktası tayini için laboratuvar analiz cihazları.',
    'aliases' => ['Erime Noktası', 'Erime Noktası Tayin', 'Erime Noktası Tayin Cihazı'],
];

$brand = [
    'name' => 'Cole-Parmer Stuart',
    'slug' => 'stuart',
    'summary' => 'Cole-Parmer Stuart; laboratuvar karıştırma, ısıtma, inkübasyon ve erime noktası tayin cihazları.',
    'logo' => null,
    'aliases' => ['STUART', 'Stuart', 'Cole-Parmer Stuart', 'Cole Parmer Stuart', 'COLE-PARMER STUART'],
];

$products = [
    [
        'name' => 'Cole-Parmer Stuart MP-200D | Dijital Erime Noktası Tayin Cihazı',
        'slug' => 'cole-parmer-stuart-mp-200d-dijital-erime-noktasi-tayin-cihazi',
        'model' => 'MP-200D',
        'device_type' => 'Dijital erime noktası tayin cihazı',
    ],
    [
        'name' => 'Cole-Parmer Stuart MP-250D | Dijital Erime Noktası Tayin Cihazı',
        'slug' => 'cole-parmer-stuart-mp-250d-dijital-erime-noktasi-tayin-cihazi',
        'model' => 'MP-250D',
        'device_type' => 'Dijital erime noktası tayin cihazı',
    ],
    [
        'name' => 'Cole-Parmer Stuart MP-400D | Dijital Erime Noktası Tayin Cihazı',
        'slug' => 'cole-parmer-stuart-mp-400d-dijital-erime-noktasi-tayin-cihazi',
        'model' => 'MP-400D',
        'device_type' => 'Dijital erime noktası tayin cihazı',
    ],
    [
        'name' => 'Cole-Parmer Stuart SMP50 | Dijital Erime Noktası Tayin Cihazı',
        'slug' => 'cole-parmer-stuart-smp50-dijital-erime-noktasi-tayin-cihazi',
        'model' => 'SMP50',
        'device_type' => 'Dijital erime noktası tayin cihazı',
    ],
    [
        'name' => 'Cole-Parmer Stuart MP-100 | Analog Erime Noktası Tayin Cihazı',
        'slug' => 'cole-parmer-stuart-mp-100-analog-erime-noktasi-tayin-cihazi',
        'model' => 'MP-100',
        'device_type' => 'Analog erime noktası tayin cihazı',
    ],
];

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
$deleteVideos = $db->prepare('delete from product_videos where product_id = :product_id');

foreach ($products as $index => $product) {
    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => null,
        'old_url' => null,
        'summary' => $product['name'],
        'body' => 'Ürün içeriği daha sonra eklenecektir.',
        'image' => $imageFor($product['slug']),
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json([]),
        'metadata' => $json([
            'Marka' => 'Cole-Parmer Stuart',
            'Kategori' => 'Erime Noktası',
            'Model' => $product['model'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => $product['device_type'],
            'İçerik durumu' => 'Daha sonra tamamlanacak',
        ]),
        'specs' => $json([
            'Model' => $product['model'],
            'Cihaz tipi' => $product['device_type'],
            'İçerik durumu' => 'Daha sonra tamamlanacak',
            'Görsel durumu' => 'Placeholder',
        ]),
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
    $deleteVideos->execute(['product_id' => $productId]);
}

echo 'seeded=' . count($products) . PHP_EOL;
echo 'category=' . $category['slug'] . PHP_EOL;
