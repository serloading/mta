<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Tekstür Analiz Cihazı',
    'slug' => 'tekstur-analiz-cihazi',
    'summary' => 'Gıda, kozmetik, ilaç ve malzeme laboratuvarlarında doku, sertlik, kırılma ve benzeri tekstür analizleri için kullanılan cihazlar.',
    'aliases' => ['Tekstür Analiz Cihazı', 'Tekstur Analiz Cihazi', 'Texture Analyzer', 'Tekstür Analiz Cihazları'],
];

$commonFeatures = [
    'Entegre sıcaklık probu',
    'Programlama ve kayıt yöntemleri',
    'Veri kaydı ve USB aktarımı',
    'Birçok farklı prob ve hücre ile kullanım',
    'Değiştirilebilir kuvvet sensörleri',
    'Yüksekliği ayarlanabilir tepsi',
];

$sensorOptions = '10 N (1 kg), 20 N (2 kg), 50 N (5 kg), 250 N (25 kg), 500 N (50 kg)';

$makeProduct = function (string $force, string $kg, ?string $sku) use ($commonFeatures, $sensorOptions): array {
    $slugForce = strtolower(str_replace(' ', '-', $force));
    $displaySku = $sku ?: 'Yayın öncesi netleştirilecek';

    return [
        'name' => "Lamy TX 700 - {$force} Tekstür Analiz Cihazı",
        'slug' => "lamy-tx-700-{$slugForce}-tekstur-analiz-cihazi",
        'model' => "TX 700 - {$force}",
        'sku' => $sku,
        'old_url' => "https://www.labor.com.tr/urun/lamy-tx-700-{$slugForce}-tekstur-analiz-cihazi",
        'summary' => "Lamy TX 700 - {$force} tekstür analiz cihazı; {$force} ({$kg}) kuvvet sensörü, 0.001 N çözünürlük, dokunmatik ekran, yöntem programlama ve USB veri aktarımıyla doku analizi uygulamaları için kullanılır.",
        'body' => 'TX-700 tekstür analiz cihazı; geniş prob ve hücre yelpazesiyle doku analizi uygulamaları için tasarlanmıştır. Doğrudan eğrileri gösteren dokunmatik ekran, yöntem programlama, kayıt ve ölçüm analizi özellikleriyle laboratuvar ve kalite kontrol süreçlerinde değerlendirilir.',
        'features' => $commonFeatures,
        'metadata' => [
            'Marka' => 'Lamy',
            'Kategori' => 'Tekstür Analiz Cihazı',
            'Üst kategori' => 'Viskozimetre',
            'Model' => "TX 700 - {$force}",
            'SKU' => $displaySku,
            'Kuvvet kapasitesi' => "{$force} ({$kg})",
            'Çözünürlük' => '0.001 N (0.1 g)',
            'Kullanım alanı' => 'Tekstür ve doku analizi',
        ],
        'specs' => [
            'Sensör seçimi' => $sensorOptions,
            'Seçili sensör' => "{$force} ({$kg})",
            'Çözünürlük' => '0.001 N (0.1 g)',
            'Hız' => '0.1 - 10 mm/s, +/-0.2%',
            'Hassasiyet' => '+/- %0.05',
            'Hareket' => '370 mm yükseklik, 0.1 mm çözünürlük',
            'Ekran' => '7 inç dokunmatik ekran; güç, hız, mesafe, zaman ve hassasiyet seviyesi göstergeleri',
            'Sıcaklık' => '-50...300 °C arası sıcaklık değeri gösteren PT100 sensör',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce, Rusça, İspanyolca, Almanca, Türkçe',
            'Emniyet ve gizlilik' => 'Kullanıcı adı ve şifre ile belirlenebilen operatör modu',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB portu, PCL/5 uyumlu',
            'Ölçüler' => '610 mm derinlik, 340 mm genişlik, 650 mm yükseklik',
            'Ağırlık' => '22 kg',
        ],
        'image_alt' => "Lamy TX 700 {$force} tekstür analiz cihazı ürün görseli",
    ];
};

$products = [
    $makeProduct('10 N', '1 kg', null),
    $makeProduct('20 N', '2 kg', null),
    $makeProduct('50 N', '5 kg', null),
    $makeProduct('250 N', '25 kg', 'LB.LMY.N151250'),
    $makeProduct('500 N', '50 kg', 'LB.LMY.N151500'),
];

$imageFor = function (string $slug) use ($root): ?string {
    foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
        $relative = "images/products/{$slug}.{$extension}";

        if (is_file($root . '/public/' . $relative)) {
            return $relative;
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
$stmt->execute(['slug' => 'lamy']);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    throw new RuntimeException('Lamy markası bulunamadı.');
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
        'image' => $imageFor($product['slug']),
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

        continue;
    }

    $insertProduct->execute([
        'category_id' => $categoryId,
        'slug' => $product['slug'],
        'created_at' => $now,
        ...$payload,
    ]);
}

$db->commit();

echo 'category_id=' . $categoryId . PHP_EOL;
echo 'brand_id=' . $brandId . PHP_EOL;
echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
