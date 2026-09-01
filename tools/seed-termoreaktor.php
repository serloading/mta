<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Termoreaktör',
    'slug' => 'termoreaktor',
    'summary' => 'Kimyasal analizlerde kontrollü reaksiyon, sindirim ve KOİ hazırlığı için termoreaktör cihazları.',
    'aliases' => [
        'Termoreaktör',
        'Termoreaktor',
        'Thermoreactor',
        'Laboratuvar Sindirim Cihazı',
    ],
];

$baseFeatures = [
    'Yüksek sıcaklıkta yakma metodu ile 160 °C seviyesinde 30 dakikada hızlı KOİ analizine uygun kullanım',
    '150 °C seviyesinde 120 dakikada klasik metot ile KOİ analizine uygun kullanım',
    'Çalışma süresi dolduğunda otomatik kapanma',
    'Ayarlanan sıcaklığa ulaşıldığında görsel veya sesli-görsel uyarı',
    'Zamanlayıcı geri sayım süresi bittiğinde uyarı',
    'Aşırı sıcaklık için sesli ve görsel emniyet sinyali',
    'Opsiyonel cam tüpler, kondanser ve tüp rack gibi geniş aksesuar seçenekleri',
];

$makeProduct = function (
    string $model,
    string $slug,
    array $imageSlugs,
    string $deviceType,
    string $capacity,
    string $temperature,
    string $timer,
    string $weight,
    string $dimensions,
    string $power,
    array $features,
    array $extraSpecs = []
) use ($baseFeatures): array {
    return [
        'name' => "VELP {$model} Termoreaktör",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} termoreaktör; {$deviceType} kullanım, {$capacity} kapasite, {$temperature} sıcaklık kontrolü ve {$timer} zamanlayıcı yapısıyla KOİ ve laboratuvar sindirim uygulamaları için kullanılır.",
        'body' => "VELP {$model} termoreaktör, numunelerin belirli sıcaklık ve süre koşullarında güvenli ve tekrarlanabilir şekilde hazırlanması için geliştirilmiş masa üstü bir laboratuvar sindirim cihazıdır. Epoksi kaplı metal gövde, sıcaklık stabilitesi, homojenlik ve aşırı sıcaklık emniyeti ile rutin analiz çalışmalarını destekler.",
        'features' => [
            ...$features,
            ...$baseFeatures,
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Termoreaktör',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => $deviceType,
            'Kapasite' => $capacity,
            'Sıcaklık' => $temperature,
            'Zamanlayıcı' => $timer,
            'Kullanım alanı' => 'KOİ analizi / laboratuvar sindirim',
        ],
        'specs' => [
            'Cihaz tipi' => $deviceType,
            'Kapasite' => $capacity,
            'Sıcaklık' => $temperature,
            'Zamanlayıcı' => $timer,
            'Sıcaklık stabilitesi' => '± 0.5 °C',
            'Sıcaklık homojenliği' => '± 0.5 °C',
            'Sıcaklık doğruluğu' => '± 1 °C',
            'Aşırı sıcaklık emniyeti' => 'Sesli ve görsel sinyal ile',
            'Gövde malzemesi' => 'Epoksi kaplı metal',
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => $dimensions,
            'Güç' => $power,
            ...$extraSpecs,
        ],
        'documents' => [],
        'image_alt' => "VELP {$model} termoreaktör ürün görseli",
    ];
};

$fixedProgramFeatures = [
    '70, 100, 120, 150 ve 160 °C sıcaklık programları',
    '30, 60, 120 dakika veya sürekli çalıştırma için zamanlayıcı programları',
    'Seçeneklerle ilgili tuşlara basılarak kolay program seçimi',
    'Ayarlanan sıcaklık ve süre değerlerini gösteren LED göstergeler',
    'Sıçramayı önleyip kullanıcıyı koruyan şeffaf termoreaktör kapağı ile yüksek güvenlik',
];

$freeProgramFeatures = [
    'Ortam sıcaklığından hedef sıcaklığa elektronik ve hassas sıcaklık kontrolü',
    'Isıtma süresinin 1 ila 199 dakika arasında ayarlanabilmesi veya sürekli çalıştırma',
    'Sıcaklık değerini ve kalan zamanı gösteren LCD ekran',
    'Zamanlayıcı geri sayım değerinin ekrandan izlenebilmesi',
];

$products = [
    $makeProduct(
        'ECO8',
        'velp-eco8-termoreaktor',
        ['velp-eco8-termoreaktor', 'eco8-termoreaktor'],
        'Sabit programlı, masa üstü',
        '8 adet Ø16 mm + 1 adet Ø22 mm çaplı tüp',
        '70, 100, 120, 150 ve 160 °C sabit programlar',
        '30, 60, 120 dk veya süresiz',
        '2 kg',
        '135 x 95 x 230 mm',
        '140 W',
        [
            '8 adet 16 mm + 1 adet 22 mm çaplı tüple aynı anda çalışma',
            'Reaktif tüketimini azaltan 16 mm çaplı tüplerle çalışabilme',
            ...$fixedProgramFeatures,
        ],
        [
            'Sıcaklık programları' => '70, 100, 120, 150 ve 160 °C (5 program)',
            'Zamanlayıcı programları' => '30, 60, 120 dk veya süresiz (4 program)',
        ]
    ),
    $makeProduct(
        'ECO25',
        'velp-eco25-termoreaktor',
        ['velp-eco25-termoreaktor', 'eco25-termoreaktor'],
        'Sabit programlı, masa üstü',
        '25 adet Ø16 mm çaplı tüp',
        '70, 100, 120, 150 ve 160 °C sabit programlar',
        '30, 60, 120 dk veya süresiz',
        '3.8 kg',
        '155 x 95 x 275 mm',
        '400 W',
        [
            '25 adet 16 mm çaplı tüple aynı anda çalışma',
            'Reaktif tüketimini azaltan 16 mm çaplı tüplerle çalışabilme',
            ...$fixedProgramFeatures,
            'Kullanıcı değmeden tüm tüpleri tek seferde çıkarıp koymayı sağlayan opsiyonel tüp çıkarıcısı aksesuarı',
        ],
        [
            'Sıcaklık programları' => '70, 100, 120, 150 ve 160 °C (5 program)',
            'Zamanlayıcı programları' => '30, 60, 120 dk veya süresiz (4 program)',
        ]
    ),
    $makeProduct(
        'ECO16',
        'velp-eco16-termoreaktor',
        ['velp-eco16-termoreaktor', 'eco16-termoreaktor'],
        'Dijital, masa üstü',
        '14 adet Ø16 mm + 2 adet Ø22 mm çaplı tüp',
        'Ortam sıcaklığı - 160 °C, 1 °C adımlarla',
        '0-199 dk veya sürekli çalıştırma',
        '3.6 kg',
        '168 x 110 x 269 mm',
        '550 W',
        [
            '14 adet Ø16 mm + 2 adet Ø22 mm çaplı tüple aynı anda çalışma',
            'Reaktif tüketimini azaltan 16 mm çaplı tüplerle çalışabilme',
            ...$freeProgramFeatures,
            'Opsiyonel şeffaf termoreaktör kapağı ile sıçrama önleme ve kullanıcı güvenliği',
        ],
        [
            'Sıcaklık ayarı' => 'Ortam sıcaklığı - 160 °C, 1 °C adımlarla',
            'Ekran' => 'LCD',
        ]
    ),
    $makeProduct(
        'ECO6',
        'velp-eco6-termoreaktor',
        ['velp-eco6-termoreaktor', 'eco6-termoreaktor'],
        'Dijital, masa üstü',
        '6 adet Ø42 mm (200 ml) veya 6 adet Ø22 mm veya 18 adet Ø16 mm çaplı tüp',
        'Ortam sıcaklığı - 200 °C, 1 °C adımlarla',
        '0-199 dk veya sürekli çalıştırma',
        '5.6 kg',
        '198 x 132 x 319 mm / kapaklı 400 x 280 x 270 mm',
        '700 W',
        [
            '6 adet Ø42 mm (200 ml), 6 adet Ø22 mm veya 18 adet Ø16 mm çaplı tüple çalışma',
            'Reaktif tüketimini azaltan 16 mm çaplı 18 tüple çalışabilme',
            ...$freeProgramFeatures,
            'Opsiyonel sıçrama önleyici tüp kapakları ile kullanıcı ve numune güvenliği',
            'Opsiyonel çap küçültücü adaptörler, cam tüpler ve tüp rack aksesuarları',
        ],
        [
            'Sıcaklık ayarı' => 'Ortam sıcaklığı - 200 °C, 1 °C adımlarla',
            'Boyutlar kapaksız (GxYxD)' => '198 x 132 x 319 mm',
            'Boyutlar kapaklı (GxYxD)' => '400 x 280 x 270 mm',
            'Ekran' => 'LCD',
        ]
    ),
];

$imageFor = function (array|string $slugs) use ($root): ?string {
    foreach ((array) $slugs as $slug) {
        foreach (['', '-1'] as $suffix) {
            foreach (['webp', 'jpg', 'jpeg', 'png', 'avif'] as $extension) {
                $relative = "images/products/{$slug}{$suffix}.{$extension}";

                if (is_file($root . '/public/' . $relative)) {
                    return $relative;
                }
            }
        }
    }

    return null;
};

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
        'name' => $category['name'],
        'summary' => $category['summary'],
        'aliases' => $json($category['aliases']),
        'updated_at' => $now,
        'id' => $categoryId,
    ]);
}

$stmt = $db->prepare('select id from product_brands where slug = :slug');
$stmt->execute(['slug' => 'velp']);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    throw new RuntimeException('VELP markası bulunamadı.');
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
        'sku' => $product['sku'],
        'old_url' => $product['old_url'],
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['image_slugs'] ?? $product['slug']),
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

    foreach ($product['documents'] as $documentIndex => $document) {
        $insertDocument->execute([
            'product_id' => $productId,
            'title' => $document['title'],
            'type' => $document['type'],
            'path' => $document['path'],
            'url' => $document['url'],
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
    echo 'image=' . ($imageFor($product['image_slugs'] ?? $product['slug']) ?: 'missing') . PHP_EOL;
}
echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
