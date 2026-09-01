<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'Polarimetreler',
    'slug' => 'polarimetreler',
    'summary' => 'Optik rotasyon ve konsantrasyon ölçümü için polarimetreler.',
    'aliases' => ['Polarimetreler', 'Polarimetre', 'Dijital Polarimetre'],
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
        'name' => 'D7 Polarimetre',
        'slug' => 'd7-polarimetre',
        'model' => 'D7',
        'summary' => 'D7, entegre LED ışık kaynaklı, 0-360 °A ve ±30 °Z ölçüm aralığına sahip polarimetredir.',
        'body' => 'D7 polarimetre; entegre LED ışık kaynağı ve 0-360 °A, ±30 °Z ölçüm aralığı ile optik rotasyon ölçümlerinde kullanılır.',
        'features' => [
            'Entegre LED ışık kaynağı',
            '0-360 °A ölçüm aralığı',
            '±30 °Z ölçüm aralığı',
        ],
        'specs' => [
            'Ürün tipi' => 'Polarimetre',
            'Model' => 'D7',
            'Işık kaynağı' => 'Entegre LED',
            'Ölçüm aralığı' => '0-360 °A; ±30 °Z',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 430 Dijital Polarimetre',
        'slug' => 'adp-430-dijital-polarimetre',
        'model' => 'ADP 430',
        'summary' => 'ADP 430, 589 nm dalga boyuna sahip dijital polarimetredir.',
        'body' => 'ADP 430 dijital polarimetre, 589 nm dalga boyu ile rutin polarimetre ölçümlerinde kullanılır.',
        'features' => [
            'Dijital polarimetre',
            '589 nm dalga boyu',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 430',
            'Dalga boyu' => '589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 450 Dijital Polarimetre',
        'slug' => 'adp-450-dijital-polarimetre',
        'model' => 'ADP 450',
        'summary' => 'ADP 450, 21 CFR Bölüm 11 uyumlu, 589 nm dalga boyuna ve Peltier sıcaklık kontrolüne sahip dijital polarimetredir.',
        'body' => 'ADP 450 dijital polarimetre; 21 CFR Bölüm 11 uyumu, 589 nm dalga boyu ve Peltier sıcaklık kontrollü yapısıyla laboratuvar polarimetre ölçümlerinde kullanılır.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Dijital polarimetre',
            '589 nm dalga boyu',
            'Peltier sıcaklık kontrolü',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 450',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Dalga boyu' => '589 nm',
            'Sıcaklık kontrolü' => 'Peltier',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [
            ['title' => 'ADP 450 tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=WR3Sdb_v7Fk', 'youtube_id' => 'WR3Sdb_v7Fk'],
        ],
    ],
    [
        'name' => 'ADP 610 Dijital Polarimetre',
        'slug' => 'adp-610-dijital-polarimetre',
        'model' => 'ADP 610',
        'summary' => 'ADP 610, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü, 7.4 inç dokunmatik ekranlı ve 589 nm tek dalga boylu dijital polarimetredir.',
        'body' => 'ADP 610 dijital polarimetre; Peltier sıcaklık kontrolü, yüksek çözünürlüklü 7.4 inç dokunmatik ekran ve 589 nm tek dalga boyu ile kullanılır. 21 CFR Bölüm 11 uyumludur.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Peltier sıcaklık kontrolü',
            'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Tek dalga boyu: 589 nm',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 610',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Sıcaklık kontrolü' => 'Peltier',
            'Ekran' => 'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dalga boyu' => '589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 620 Dijital Polarimetre',
        'slug' => 'adp-620-dijital-polarimetre',
        'model' => 'ADP 620',
        'summary' => 'ADP 620, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü, 7.4 inç dokunmatik ekranlı ve 546/589 nm iki dalga boylu dijital polarimetredir.',
        'body' => 'ADP 620 dijital polarimetre; Peltier sıcaklık kontrolü, yüksek çözünürlüklü 7.4 inç dokunmatik ekran ve 546/589 nm dalga boyları ile kullanılır. 21 CFR Bölüm 11 uyumludur.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Peltier sıcaklık kontrolü',
            'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'İki dalga boyu: 546 ve 589 nm',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 620',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Sıcaklık kontrolü' => 'Peltier',
            'Ekran' => 'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dalga boyları' => '546, 589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 622 Dijital Polarimetre',
        'slug' => 'adp-622-dijital-polarimetre',
        'model' => 'ADP 622',
        'summary' => 'ADP 622, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü, 7.4 inç dokunmatik ekranlı ve 365/589 nm iki dalga boylu dijital polarimetredir.',
        'body' => 'ADP 622 dijital polarimetre; Peltier sıcaklık kontrolü, yüksek çözünürlüklü 7.4 inç dokunmatik ekran ve 365/589 nm dalga boyları ile kullanılır. 21 CFR Bölüm 11 uyumludur.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Peltier sıcaklık kontrolü',
            'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'İki dalga boyu: 365 ve 589 nm',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 622',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Sıcaklık kontrolü' => 'Peltier',
            'Ekran' => 'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dalga boyları' => '365, 589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 640 Dijital Polarimetre',
        'slug' => 'adp-640-dijital-polarimetre',
        'model' => 'ADP 640',
        'summary' => 'ADP 640, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü, 7.4 inç dokunmatik ekranlı ve dört dalga boylu dijital polarimetredir.',
        'body' => 'ADP 640 dijital polarimetre; Peltier sıcaklık kontrolü, yüksek çözünürlüklü 7.4 inç dokunmatik ekran ve 405/436/546/589 nm dalga boyları ile kullanılır. 21 CFR Bölüm 11 uyumludur.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Peltier sıcaklık kontrolü',
            'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dört dalga boyu: 405, 436, 546 ve 589 nm',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 640',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Sıcaklık kontrolü' => 'Peltier',
            'Ekran' => 'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dalga boyları' => '405, 436, 546, 589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 650 Dijital Polarimetre',
        'slug' => 'adp-650-dijital-polarimetre',
        'model' => 'ADP 650',
        'summary' => 'ADP 650, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü, 7.4 inç dokunmatik ekranlı ve beş dalga boylu dijital polarimetredir.',
        'body' => 'ADP 650 dijital polarimetre; Peltier sıcaklık kontrolü, yüksek çözünürlüklü 7.4 inç dokunmatik ekran ve 365/405/436/546/589 nm dalga boyları ile kullanılır. 21 CFR Bölüm 11 uyumludur.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Peltier sıcaklık kontrolü',
            'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Beş dalga boyu: 365, 405, 436, 546 ve 589 nm',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 650',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Sıcaklık kontrolü' => 'Peltier',
            'Ekran' => 'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dalga boyları' => '365, 405, 436, 546, 589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
        'videos' => [],
    ],
    [
        'name' => 'ADP 660 Dijital Polarimetre',
        'slug' => 'adp-660-dijital-polarimetre',
        'model' => 'ADP 660',
        'summary' => 'ADP 660, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü, 7.4 inç dokunmatik ekranlı ve altı dalga boylu dijital polarimetredir.',
        'body' => 'ADP 660 dijital polarimetre; Peltier sıcaklık kontrolü, yüksek çözünürlüklü 7.4 inç dokunmatik ekran ve 325/365/405/436/546/589 nm dalga boyları ile kullanılır. 21 CFR Bölüm 11 uyumludur.',
        'features' => [
            '21 CFR Bölüm 11 uyumlu',
            'Peltier sıcaklık kontrolü',
            'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Altı dalga boyu: 325, 365, 405, 436, 546 ve 589 nm',
        ],
        'specs' => [
            'Ürün tipi' => 'Dijital polarimetre',
            'Model' => 'ADP 660',
            'Uyumluluk' => '21 CFR Bölüm 11',
            'Sıcaklık kontrolü' => 'Peltier',
            'Ekran' => 'Yüksek çözünürlüklü 7.4 inç dokunmatik ekran',
            'Dalga boyları' => '325, 365, 405, 436, 546, 589 nm',
            'Görsel durumu' => 'Placeholder',
        ],
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

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => null,
        'old_url' => null,
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['slug']),
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json($product['features']),
        'metadata' => $json([
            'Marka' => 'Bellingham + Stanley',
            'Kategori' => 'Polarimetreler',
            'Model' => $product['model'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => $product['specs']['Ürün tipi'],
        ]),
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
