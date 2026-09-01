<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'Taşınabilir Tip Optik Refraktometreler',
    'slug' => 'tasinabilir-tip-optik-refraktometreler',
    'summary' => 'Saha ve hızlı kontrol uygulamaları için taşınabilir optik refraktometre serileri.',
    'aliases' => ['Taşınabilir Optik Refraktometreler', 'El Tipi Refraktometre', 'Optik Refraktometre', 'E-Line Refraktometre', 'Eclipse Refraktometre'],
];

$brand = [
    'name' => 'Bellingham + Stanley',
    'slug' => 'bellingham-stanley',
    'summary' => 'Refraktometre ve polarimetre çözümleri.',
    'logo' => 'images/brands/bellingham-stanley.png',
    'aliases' => ['BELİNGHAMSTARLY', 'Bellingham and Stanley', 'Bellingham + Stanley'],
];

$eLineOptions = [
    '44-801' => 'E-line: %0-10 şeker (°Brix), otomatik sıcaklık kompanzasyonlu',
    '44-802' => 'E-line: %0-18 şeker (°Brix), otomatik sıcaklık kompanzasyonlu',
    '44-803' => 'E-line: %0-32 şeker (°Brix), otomatik sıcaklık kompanzasyonlu',
    '44-804' => 'E-line: %28-62 şeker (°Brix), otomatik sıcaklık kompanzasyonlu',
    '44-805' => 'E-line: %45-82 şeker (°Brix), otomatik sıcaklık kompanzasyonlu',
    '44-808' => 'E-line: tuzluluk, %0-100 tuz ve 1.000-1.070 SG, otomatik sıcaklık kompanzasyonlu',
    '44-812' => 'E-line: balda su tayini için %10-30, otomatik sıcaklık kompanzasyonlu',
    '44-820' => 'E-line: glikol, 0...-50 °C EP/EG ve 0...70 % EP/EG, otomatik sıcaklık kompanzasyonlu',
    '44-821' => 'E-line: otomotiv, 0...-50 °C EG/EP ve 1.1-1.4 SG akü asidi, otomatik sıcaklık kompanzasyonlu',
    '44-822' => 'E-line: otomotiv AdBlue, EG/EP koruma °C ve akü asidi, otomatik sıcaklık kompanzasyonlu',
    '44-823' => 'E-line: bira, 1.000-1.120 SG / %0-32 şeker (°Brix), otomatik sıcaklık kompanzasyonlu',
    '44-825' => 'E-line: klinik, 1.000-1.050 üre SG, 0-12 serum protein, 1.33-1.36 RI, otomatik sıcaklık kompanzasyonlu',
    '44-828' => 'E-line: kırılma indisi 1.333-1.384, otomatik sıcaklık kompanzasyonlu',
    '44-829' => 'E-line: kırılma indisi 1.435-1.520, otomatik sıcaklık kompanzasyonlu',
    '44-809' => 'E-line: şarap için %0-40 °Brix ve 0-25 °AP, otomatik sıcaklık kompanzasyonlu',
    '44-817' => 'E-line: şarap için %0-32 °Brix ve 0-140 Oechsle D, otomatik sıcaklık kompanzasyonlu',
    '44-818' => 'E-line: şarap için %0-32 °Brix, 0-140 Oechsle ve 0-27 KMW, otomatik sıcaklık kompanzasyonlu',
    '44-819' => 'E-line: şarap için %0-20 °Boume ve 0-25 °AP, otomatik sıcaklık kompanzasyonlu',
    '44-807' => 'E-line: çift skalalı, %0-80 şeker (°Brix)',
    '44-806' => 'E-line: üç skalalı, %0-90 şeker (°Brix)',
    '44-861' => 'Değerli taşlar için 1.30-1.81 RI',
];

$eclipseOptions = [
    '45-01' => '% Şeker (°Brix), ölçüm aralığı 0-15, ölçek taksimatı 0.1',
    '45-02' => '% Şeker (°Brix), ölçüm aralığı 0-30, ölçek taksimatı 0.2',
    '45-03' => '% Şeker (°Brix), ölçüm aralığı 0-50, ölçek taksimatı 0.5',
    '45-08' => '% Şeker (°Brix), ölçüm aralığı 28-65, ölçek taksimatı 0.2',
    '45-05' => '% Şeker (°Brix), ölçüm aralığı 45-80, ölçek taksimatı 0.2',
    '45-06' => '% Şeker (°Brix), ölçüm aralığı 72-95, ölçek taksimatı 0.2',
    '45-22' => 'Şarap - °Zeiss (ABV), ölçüm aralığı 10-135, ölçek taksimatı 1',
    '45-27' => 'Balda su tayini için (%), ölçüm aralığı 10-30, ölçek taksimatı 0.2',
    '45-81' => 'Şeker (°Brix) - düşük hacim, ölçüm aralığı 0-50, ölçek taksimatı 0.5',
    '45-82' => 'Şeker (°Brix) - düşük hacim, ölçüm aralığı 45-80, ölçek taksimatı 0.2',
    '45-26' => 'Nişasta (%), ölçüm aralığı 0-30, ölçek taksimatı 0.2',
    '45-41' => 'Refraktif indeks, ölçüm aralığı 1.33-1.42, ölçek taksimatı 0.001',
    '45-44' => 'Antifriz °C Protection (EG/PG), ölçüm aralığı 0...-40, ölçek taksimatı 5; akü asidi SG, ölçüm aralığı 1.1-1.35, ölçek taksimatı 0.05',
    '45-46' => 'Antifriz % Ethylene Glycol (EG), ölçüm aralığı 0-60, ölçek taksimatı 2.5; % Propylene Glycol (PG), ölçüm aralığı 0-60, ölçek taksimatı 2.5',
    '45-65' => '% Şeker (°Brix), ölçüm aralığı 45-80, ölçek taksimatı 0.2',
];

$optionText = fn (array $options): string => implode(' | ', array_map(
    fn (string $catalogNo, string $description): string => $catalogNo . ': ' . $description,
    array_keys($options),
    $options,
));

$products = [
    [
        'name' => 'E-Line Serisi Taşınabilir Tip Optik Refraktometreler',
        'slug' => 'e-line-serisi-tasinabilir-tip-optik-refraktometreler',
        'model' => 'E-Line Serisi',
        'summary' => 'E-Line serisi taşınabilir tip optik refraktometreler; şeker, tuzluluk, balda su, glikol, otomotiv, bira, klinik, kırılma indisi, şarap ve değerli taş uygulamaları için farklı katalog seçenekleri sunar.',
        'body' => 'E-Line serisi taşınabilir tip optik refraktometreler, saha ve hızlı kontrol uygulamalarında farklı ölçüm skalalarına göre seçilebilen optik refraktometrelerden oluşur. Seri; °Brix şeker ölçümü, tuzluluk, balda su tayini, glikol, otomotiv sıvıları, bira, klinik ölçümler, kırılma indisi, şarap ölçümleri ve değerli taş uygulamaları için katalog seçenekleri içerir.',
        'features' => [
            'Taşınabilir tip optik refraktometre serisi',
            'Şeker, tuzluluk, balda su tayini, glikol, otomotiv, bira, klinik ve şarap uygulamaları',
            'Birçok modelde otomatik sıcaklık kompanzasyonu',
            'Kırılma indisi ve değerli taş ölçüm seçenekleri',
            '21 katalog seçeneği',
        ],
        'specs' => [
            'Ürün tipi' => 'Taşınabilir tip optik refraktometre',
            'Seri' => 'E-Line',
            'Katalog seçenekleri' => $optionText($eLineOptions),
            'Görsel durumu' => 'Placeholder',
        ],
    ],
    [
        'name' => 'Eclipse Serisi Taşınabilir Tip Optik Refraktometreler',
        'slug' => 'eclipse-serisi-tasinabilir-tip-optik-refraktometreler',
        'model' => 'Eclipse Serisi',
        'summary' => 'Eclipse serisi taşınabilir tip optik refraktometreler; Brix, şarap, balda su, düşük hacim, nişasta, refraktif indeks ve antifriz uygulamaları için katalog seçenekleri sunar.',
        'body' => 'Eclipse serisi taşınabilir tip optik refraktometreler; şeker (°Brix), şarap, balda su tayini, düşük hacim ölçümleri, nişasta, refraktif indeks ve antifriz/akü asidi uygulamaları için farklı ölçüm aralığı ve ölçek taksimatı seçenekleriyle sunulur.',
        'features' => [
            'Taşınabilir tip optik refraktometre serisi',
            'Şeker (°Brix), şarap, balda su, nişasta ve refraktif indeks uygulamaları',
            'Antifriz ve akü asidi SG ölçüm seçenekleri',
            'Düşük hacim ölçüm seçenekleri',
            '15 katalog seçeneği',
        ],
        'specs' => [
            'Ürün tipi' => 'Taşınabilir tip optik refraktometre',
            'Seri' => 'Eclipse',
            'Katalog seçenekleri' => $optionText($eclipseOptions),
            'Görsel durumu' => 'Placeholder',
        ],
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
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['slug']),
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json($product['features']),
        'metadata' => $json([
            'Marka' => 'Bellingham + Stanley',
            'Kategori' => 'Taşınabilir Tip Optik Refraktometreler',
            'Üst kategori' => 'Refraktometre',
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
}

echo 'seeded=' . count($products) . PHP_EOL;
echo 'category=' . $category['slug'] . PHP_EOL;
