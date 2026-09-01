<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Soğutmalı İnkübatör',
    'slug' => 'sogutmali-inkubator',
    'summary' => 'BOİ uygulamaları, düşük sıcaklık inkübasyonu ve kontrollü sıcaklık çalışmaları için soğutmalı inkübatörler.',
    'aliases' => [
        'Soğutmalı İnkübatör',
        'Sogutmali Inkubator',
        'Refrigerated Incubator',
        'BOD Incubator',
    ],
];

$commonFeatures = [
    'BOİ ölçüm cihazlarına özel tasarım',
    'Fanlı havalandırma ile üniform sıcaklık',
    'Yüksek verim ve geniş kapasite',
    'A+ sınıfı enerji derecesinde soğutma sistemi ile enerji tasarrufu',
    'Düşük işletme maliyeti',
    'BOİ ölçüm cihazlarına güç sağlayan dahili 2 adet elektrik prizi',
    'Opsiyonel multi soket uzatma kablosu',
];

$programmableFeatures = [
    'BOİ ölçüm cihazlarının yanı sıra farklı analizler için kullanım',
    '3 ila 50 °C arasında ayarlanabilir inkübasyon sıcaklığı',
    '3 basamaklı dahili sıcaklık göstergesi',
    'Opsiyonel TEMPSoft yazılımı ile inkübatör iç sıcaklığını PC üzerinden yönetebilme',
    'Zaman ve sıcaklık yönetimi için TEMPSoft yazılım desteği',
    'Opsiyonel kablosuz DataBox arayüzü ile sıcaklık alarm eşikleri ve çalışma rampaları yönetimi',
    'Özel grafikle iç sıcaklık trendinin izlenebilmesi ve elektronik tabloya kaydedilebilmesi',
    'GLP uyarınca sürekli izleme ve veri kaydı desteği',
    'Verilerin inkübatörden Wireless DataBox alıcısına kablosuz mod ile gönderilmesi',
];

$makeProduct = function (
    string $model,
    string $slug,
    array $imageSlugs,
    string $deviceType,
    string $volume,
    string $bodCapacity,
    string $temperature,
    string $display,
    string $technology,
    string $shelves,
    string $weight,
    string $dimensions,
    string $power,
    array $features,
    array $extraSpecs = []
): array {
    return [
        'name' => "VELP {$model} Soğutmalı İnkübatör",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} soğutmalı inkübatör; {$volume} toplam hacim, {$temperature} sıcaklık, {$bodCapacity} BOİ cihaz kapasitesi ve {$display} dijital gösterge ile BOİ ve kontrollü inkübasyon uygulamaları için kullanılır.",
        'body' => "VELP {$model} soğutmalı inkübatör, BOİ ölçüm sistemleri ve kontrollü sıcaklık gerektiren laboratuvar uygulamaları için tasarlanmış dik tip dijital inkübatördür. Fanlı havalandırma, elektronik termoregülasyon, dahili prizler ve enerji verimli soğutma yapısı uzun süreli inkübasyon süreçlerini destekler.",
        'features' => $features,
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Soğutmalı İnkübatör',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => $deviceType,
            'Toplam hacim' => $volume,
            'Sıcaklık' => $temperature,
            'BOİ cihaz kapasitesi' => $bodCapacity,
            'Kullanım alanı' => 'BOİ inkübasyonu / soğutmalı inkübasyon',
        ],
        'specs' => [
            'Cihaz tipi' => $deviceType,
            'Toplam hacim' => $volume,
            'BOİ cihaz kapasitesi' => $bodCapacity,
            'Sıcaklık' => $temperature,
            'İç sıcaklık kararlılığı' => '± 0.5 °C',
            'Dijital gösterge' => $display,
            'Ölçüm teknolojisi' => $technology,
            'Raf sayısı' => $shelves,
            'Dahili priz sayısı' => '2 elektrik soketi',
            'Multi soket uzatma kablosu' => 'Var (opsiyonel)',
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => $dimensions,
            'Güç' => $power,
            ...$extraSpecs,
        ],
        'documents' => [],
        'image_alt' => "VELP {$model} soğutmalı inkübatör ürün görseli",
    ];
};

$products = [
    $makeProduct(
        'FTC120',
        'velp-ftc120-sogutmali-inkubator',
        ['velp-ftc120-sogutmali-inkubator', 'ftc120-sogutmali-inkubator'],
        'Dijital, dik tip',
        '120 litre',
        '3 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi',
        '20 °C sabit sıcaklık',
        '2 haneli, 1 °C çözünürlük',
        'Sabit sıcaklıkta elektronik termoregülasyon',
        '2 raf',
        '36 kg',
        '540 x 912 x 550 mm',
        '120 W',
        [
            ...$commonFeatures,
            'Standart 20 °C sıcaklıkta inkübe edilen numuneler için ideal kullanım',
            '3 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi cihazını aynı anda inkübe edebilme',
            'Dahili sıcaklığın 2 basamaklı gösterimi',
        ]
    ),
    $makeProduct(
        'FOC120E',
        'velp-foc120e-sogutmali-inkubator',
        ['velp-foc120e-sogutmali-inkubator', 'foc120e-sogutmali-inkubator'],
        'Dijital, dik tip',
        '120 litre',
        '3 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi',
        '3.0 - 50.0 °C',
        '3 haneli, 0.1 °C çözünürlük',
        'Otomatik ayarlı elektronik termoregülasyon',
        '2 raf',
        '36 kg',
        '540 x 912 x 550 mm',
        '120 W',
        [
            ...$commonFeatures,
            ...$programmableFeatures,
            '3 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi cihazını aynı anda inkübe edebilme',
        ],
        [
            'Sıcaklık aralığı' => '3.0 - 50.0 °C',
            'İç sıcaklık homojenliği' => '± 0.5 °C',
        ]
    ),
    $makeProduct(
        'FOC120I',
        'velp-foc120i-seffaf-ic-kapili-sogutmali-inkubator',
        ['velp-foc120i-seffaf-ic-kapili-sogutmali-inkubator', 'foc120i-sogutmali-inkubator'],
        'Dijital, dik tip',
        '120 litre',
        '2 adet 6lı veya 1 adet 10lu BOİ Sensör Sistemi',
        '3.0 - 50.0 °C',
        '3 haneli, 0.1 °C çözünürlük',
        'Otomatik ayarlı elektronik termoregülasyon',
        '2 raf',
        '36 kg',
        '540 x 912 x 550 mm',
        '120 W',
        [
            ...$commonFeatures,
            ...$programmableFeatures,
            'Analize mani olmadan örnek gözlemi için dahili şeffaf iç kapı',
            '2 adet 6lı veya 1 adet 10lu BOİ Sensör Sistemi cihazını aynı anda inkübe edebilme',
        ],
        [
            'Şeffaf iç kapı' => 'Var',
            'Sıcaklık aralığı' => '3.0 - 50.0 °C',
            'İç sıcaklık homojenliği' => '± 0.5 °C',
        ]
    ),
    $makeProduct(
        'FOC215E',
        'velp-foc215e-sogutmali-inkubator',
        ['velp-foc215e-sogutmali-inkubator', 'foc215e-sogutmali-inkubator'],
        'Dijital, dik tip',
        '215 litre',
        '5 adet 6lı veya 3 adet 10lu BOİ Sensör Sistemi',
        '3.0 - 50.0 °C',
        '3 haneli, 0.1 °C çözünürlük',
        'Otomatik ayarlı elektronik termoregülasyon',
        '4 raf (istenirse 2 tane daha eklenebilir)',
        '46.3 kg',
        '540 x 1263 x 550 mm',
        '400 W',
        [
            ...$commonFeatures,
            ...$programmableFeatures,
            '5 adet 6lı veya 3 adet 10lu BOİ Sensör Sistemi cihazını aynı anda inkübe edebilme',
        ],
        [
            'Sıcaklık aralığı' => '3.0 - 50.0 °C',
            'İç sıcaklık homojenliği' => '± 0.5 °C',
        ]
    ),
    $makeProduct(
        'FOC215I',
        'velp-foc215i-seffaf-ic-kapili-sogutmali-inkubator',
        ['velp-foc215i-seffaf-ic-kapili-sogutmali-inkubator', 'foc215i-sogutmali-inkubator'],
        'Dijital, dik tip',
        '215 litre',
        '4 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi',
        '3.0 - 50.0 °C',
        '3 haneli, 0.1 °C çözünürlük',
        'Otomatik ayarlı elektronik termoregülasyon',
        '4 raf (istenirse 2 tane daha eklenebilir)',
        '46.3 kg',
        '540 x 1263 x 550 mm',
        '400 W',
        [
            ...$commonFeatures,
            ...$programmableFeatures,
            'Analize mani olmadan örnek gözlemi için dahili şeffaf iç kapı',
            '4 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi cihazını aynı anda inkübe edebilme',
        ],
        [
            'Şeffaf iç kapı' => 'Var',
            'Sıcaklık aralığı' => '3.0 - 50.0 °C',
            'İç sıcaklık homojenliği' => '± 0.5 °C',
        ]
    ),
    $makeProduct(
        'FOC215IL',
        'velp-foc215il-seffaf-ic-kapili-aydinlatmali-sogutmali-inkubator',
        ['velp-foc215il-seffaf-ic-kapili-aydinlatmali-sogutmali-inkubator', 'foc215il-sogutmali-inkubator'],
        'Dijital, dik tip, aydınlatmalı',
        '215 litre',
        '4 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi',
        '3.0 - 50.0 °C',
        '3 haneli, 0.1 °C çözünürlük',
        'Otomatik ayarlı elektronik termoregülasyon',
        '4 raf, ikisi LED ışıklı (istenirse 2 tane daha eklenebilir)',
        '50 kg',
        '540 x 1300 x 550 mm',
        '400 W',
        [
            ...$programmableFeatures,
            'Bakteri kültürleri, fermantasyon, bitki büyümesi, tohum çimlenmesi, ekim ve bitki ıslahı gibi sabit sıcaklık ve aydınlatma gerektiren analizler için uygun kullanım',
            'Her biri 20.000 lux/raf olmak üzere 2 ışıklı raf',
            'Rafların altına yatay monte edilen 6şar adet LED çubuk ile aydınlatma',
            'Rafın tamamında ışık homojenliği ve sabit sıcaklık',
            'Aydınlatma süresi döngüleri için ayarlanabilir zamanlayıcı',
            'Dahili 2 adet elektrik prizi ve opsiyonel multi soket uzatma kablosu',
            'Analize mani olmadan örnek gözlemi için dahili şeffaf iç kapı',
            '4 adet 6lı veya 2 adet 10lu BOİ Sensör Sistemi cihazını aynı anda inkübe edebilme',
            'Fanlı havalandırma ile üniform sıcaklık',
            'A+ sınıfı enerji derecesinde soğutma sistemi ile enerji tasarrufu',
            'Düşük işletme maliyeti',
        ],
        [
            'Şeffaf iç kapı' => 'Var',
            'Sıcaklık aralığı' => '3.0 - 50.0 °C',
            'İç sıcaklık homojenliği' => '± 0.5 °C',
            'Aydınlatma sistemi' => 'LED',
            'Işık akısı' => '20.000 lux/raf',
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
