<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'Laboratuvar Tipi Refraktometreler',
    'slug' => 'laboratuvar-tipi-refraktometreler',
    'summary' => 'Laboratuvar ve kalite kontrol uygulamaları için masa tipi dijital refraktometreler.',
    'aliases' => ['Laboratuar Tipi Refraktometreler', 'Laboratuvar Refraktometresi', 'Dijital Refraktometreler', 'RFM Refraktometre'],
];

$brand = [
    'name' => 'Bellingham + Stanley',
    'slug' => 'bellingham-stanley',
    'summary' => 'Refraktometre ve polarimetre çözümleri.',
    'logo' => 'images/brands/bellingham-stanley.png',
    'aliases' => ['BELİNGHAMSTARLY', 'Bellingham and Stanley', 'Bellingham + Stanley'],
];

$products = [
    [
        'name' => 'RFM712-M Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm712-m-laboratuvar-tipi-refraktometre',
        'model' => 'RFM712-M',
        'range' => '0-50 °Brix',
        'resolution' => '0.1 °Brix',
        'videos' => [],
    ],
    [
        'name' => 'RFM732-M Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm732-m-laboratuvar-tipi-refraktometre',
        'model' => 'RFM732-M',
        'range' => '0-95 °Brix',
        'resolution' => '0.1 °Brix',
        'videos' => [],
    ],
    [
        'name' => 'RFM742-M Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm742-m-laboratuvar-tipi-refraktometre',
        'model' => 'RFM742-M',
        'range' => '0-80 °Brix',
        'resolution' => '0.01 °Brix',
        'videos' => [],
    ],
    [
        'name' => 'RFM330-M Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm330-m-laboratuvar-tipi-refraktometre',
        'model' => 'RFM330-M',
        'range' => '1.32-1.58 RI; 0-100 °Brix',
        'resolution' => '0.0001 RI; 0.1 °Brix',
        'videos' => [],
    ],
    [
        'name' => 'RFM330-T Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm330-t-laboratuvar-tipi-refraktometre',
        'model' => 'RFM330-T',
        'range' => '1.32-1.58 RI; 0-100 °Brix',
        'resolution' => '0.0001 RI; 0.1 °Brix',
        'videos' => [],
    ],
    [
        'name' => 'RFM340-M Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm340-m-laboratuvar-tipi-refraktometre',
        'model' => 'RFM340-M',
        'range' => '1.32-1.58 RI; 0-100 °Brix',
        'resolution' => '0.00001 RI; 0.01 °Brix',
        'compliance' => null,
        'videos' => [
            ['title' => 'RFM340-M tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=Tjcaw7CLN-E', 'youtube_id' => 'Tjcaw7CLN-E'],
        ],
    ],
    [
        'name' => 'RFM340-T Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm340-t-laboratuvar-tipi-refraktometre',
        'model' => 'RFM340-T',
        'range' => '1.32-1.58 RI; 0-100 °Brix',
        'resolution' => '0.00001 RI; 0.01 °Brix',
        'compliance' => null,
        'videos' => [
            ['title' => 'RFM340-T tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=Tjcaw7CLN-E', 'youtube_id' => 'Tjcaw7CLN-E'],
        ],
    ],
    [
        'name' => 'RFM960-T Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm960-t-laboratuvar-tipi-refraktometre',
        'model' => 'RFM960-T',
        'range' => '1.30-1.70 RI; 0-100 °Brix',
        'resolution' => '0.0001 RI; 0.1 °Brix',
        'compliance' => '21 CFR Bölüm 11 uyumlu',
        'videos' => [],
    ],
    [
        'name' => 'RFM970-T Laboratuvar Tipi Refraktometre',
        'slug' => 'rfm970-t-laboratuvar-tipi-refraktometre',
        'model' => 'RFM970-T',
        'range' => '1.30-1.70 RI; 0-100 °Brix',
        'resolution' => '0.00001 RI; 0.01 °Brix',
        'compliance' => '21 CFR Bölüm 11 uyumlu',
        'videos' => [],
    ],
    [
        'name' => 'Abbe 5 Laboratuvar Tipi Refraktometre',
        'slug' => 'abbe-5-laboratuvar-tipi-refraktometre',
        'model' => 'Abbe 5',
        'range' => '1.30-1.70 RI; 0-95 °Brix',
        'resolution' => '0.0005 RI; 0.25 °Brix',
        'compliance' => null,
        'videos' => [],
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
$insertVideo = $db->prepare('insert into product_videos (product_id, title, youtube_url, youtube_id, sort_order, created_at, updated_at) values (:product_id, :title, :youtube_url, :youtube_id, :sort_order, :created_at, :updated_at)');

foreach ($products as $index => $product) {
    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $features = [
        'Laboratuvar tipi refraktometre',
        'Ölçüm aralığı: ' . $product['range'],
        'Çözünürlük: ' . $product['resolution'],
    ];

    if (! empty($product['compliance'])) {
        $features[] = $product['compliance'];
    }

    $specs = [
        'Ürün tipi' => 'Laboratuvar tipi refraktometre',
        'Model' => $product['model'],
        'Ölçüm aralığı' => $product['range'],
        'Çözünürlük' => $product['resolution'],
        'Görsel durumu' => 'Placeholder',
    ];

    if (! empty($product['compliance'])) {
        $specs['Uyumluluk'] = $product['compliance'];
    }

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => null,
        'old_url' => null,
        'summary' => "{$product['model']} laboratuvar tipi refraktometre; {$product['range']} ölçüm aralığı ve {$product['resolution']} çözünürlük değerleriyle kullanılır.",
        'body' => "{$product['model']} laboratuvar tipi refraktometre, Brix ve/veya kırılma indisi ölçümü gerektiren laboratuvar ve kalite kontrol uygulamaları için kullanılır. Ölçüm aralığı {$product['range']}, çözünürlüğü {$product['resolution']} olarak belirtilmiştir." . (! empty($product['compliance']) ? ' ' . $product['compliance'] . ' yapıdadır.' : ''),
        'image' => $imageFor($product['slug']),
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json($features),
        'metadata' => $json([
            'Marka' => 'Bellingham + Stanley',
            'Kategori' => 'Laboratuvar Tipi Refraktometreler',
            'Üst kategori' => 'Refraktometre',
            'Model' => $product['model'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Laboratuvar tipi refraktometre',
            'Uyumluluk' => $product['compliance'] ?? null,
        ]),
        'specs' => $json($specs),
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

    foreach ($product['videos'] as $videoIndex => $video) {
        $insertVideo->execute([
            'product_id' => $productId,
            'title' => $video['title'],
            'youtube_url' => $video['youtube_url'],
            'youtube_id' => $video['youtube_id'],
            'sort_order' => ($videoIndex + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

echo 'seeded=' . count($products) . PHP_EOL;
echo 'category=' . $category['slug'] . PHP_EOL;
