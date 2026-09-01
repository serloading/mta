<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$categories = [
    'rotator-calkalayici' => [
        'name' => 'Rotatör Çalkalayıcı',
        'summary' => 'Rotasyon ve çalkalama uygulamaları için laboratuvar çalkalayıcıları.',
        'aliases' => ['Rotatör Çalkalayıcı', 'Rotator Calkalayici', 'Rotatör Çalkalayıcılar'],
    ],
    'ph-metre' => [
        'name' => 'pH Metre',
        'summary' => 'Laboratuvar ve saha kullanımı için pH metre, türbidimetre ve ölçüm cihazları.',
        'aliases' => ['PH Metre', 'pH metre', 'Türbidimetre', 'Turbidimeter'],
    ],
];

$products = [
    [
        'category_slug' => 'rotator-calkalayici',
        'name' => 'VELP ROTAX-6.8 Rotatör Çalkalayıcı',
        'slug' => 'velp-rotax-6-8-rotator-calkalayici',
        'image_slugs' => ['velp-rotax-6-8-rotator-calkalayici', 'rotax-6-8-rotator-calkalayici', 'rotax-6-8'],
        'model' => 'ROTAX-6.8',
        'old_url' => null,
        'summary' => 'VELP ROTAX-6.8 rotatör çalkalayıcı; 0-30 rpm elektronik hız ayarı, 6 x 2 L veya 8 x 1 L şişe kapasitesi ve LCD hız göstergesi ile kullanılır.',
        'body' => 'VELP ROTAX-6.8, laboratuvarlarda şişe rotasyonu ve çalkalama uygulamaları için dijital masa üstü çözüm sunar. UNI 10802 ve DIN 38414 standartlarına uygun çalışma yapısı, engellenen rotasyonda otomatik durma güvenliği ve epoksi kaplı metal gövdesiyle yoğun kullanımı destekler.',
        'features' => [
            '30 rpm değerine kadar elektronik hız ayarı ve kontrolü',
            'LCD hız göstergesi',
            'UNI 10802 ve DIN 38414 standartlarına uygunluk',
            'Kullanışlı ve güvenli çalışma',
            '6 x 2 L veya adaptörle 8 x 1 L şişe kapasitesi',
            'Rotasyonu engelleyen durumda otomatik durma',
            'Sağlam tasarım',
            'Kimyasal ve mekanik korozyona karşı güçlü direnç',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Dönme hızı ayarı' => '0-30 rpm',
            'Hız ayar hassasiyeti' => '1 rpm',
            'Kapasite' => '6 x 2 L şişe veya 8 x 1 L şişe',
            'Maksimum / minimum şişe yükseklikleri' => '1 L: 200-270 mm; 2 L: 220-280 mm',
            'Maksimum şişe çapları' => '1 L: 110 mm; 2 L: 135 mm',
            'Ekran' => 'LCD',
            'Gövde malzemesi' => 'Epoksi kaplı metal',
            'Ağırlık' => '30 kg',
            'Boyutlar (GxYxD)' => '665 x 520 x 470 mm',
            'Güç' => '100 W',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Rotatör Çalkalayıcı',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'ROTAX-6.8',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Kapasite' => '6 x 2 L veya 8 x 1 L',
            'Dönme hızı' => '0-30 rpm',
        ],
        'documents' => [],
        'image_alt' => 'VELP ROTAX-6.8 rotatör çalkalayıcı ürün görseli',
    ],
    [
        'category_slug' => 'ph-metre',
        'name' => 'VELP TB1 Türbidimetre',
        'slug' => 'velp-tb1-turbidimetre',
        'image_slugs' => ['velp-tb1-turbidimetre', 'tb1-turbidimetre', 'velp-tb1'],
        'model' => 'TB1',
        'old_url' => null,
        'summary' => 'VELP TB1 türbidimetre; 0-1000 NTU ölçüm aralığı, ISO 7027 uyumlu 90 derece nephelometrik ölçüm metodu ve IP 67 koruması ile portatif bulanıklık ölçümü sağlar.',
        'body' => 'VELP TB1, sıvı numunelerde bulanıklık ölçümü için portatif dijital türbidimetre çözümüdür. NIST izlenebilir standartlar, 850 nm kızılötesi ışık kaynağı, dayanıklı ABS gövde ve komple aksesuar setiyle saha ve laboratuvar kullanımına uygundur.',
        'features' => [
            'Sıvı numunelerin bulanıklığını basit ve doğru şekilde ölçme',
            'Sezgisel ve kolay kalibrasyon',
            'Saniyeler içinde sonuç alma',
            'NIST izlenebilir standartlar',
            'ISO 7027 standardına göre ölçüm',
            'IP 67 ile partikül ve su girişine karşı koruma',
            'Sert ABS kasa',
            '800, 100, 20 ve 0.02 NTU kalibrasyon standartlarıyla teslim',
            '3 numune tüpü, bez, silikon yağı, pil ve taşıma çantası dahil',
            'Bir pil setiyle 1200 ölçüme kadar kullanım',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, portatif',
            'Ölçüm aralığı' => '0-1000 NTU',
            'Ölçüm doğruluğu' => '0-500 NTU aralığında ±%2; 501-1000 NTU aralığında ±%3',
            'Tekrarlanabilirlik' => '±0.01 NTU veya okunan değerin ±%1’i, hangisi daha büyükse',
            'Ölçüm metodu' => 'ISO 7027 uyumlu nephelometrik (90°)',
            'Kalibrasyon standartları' => '0.02, 20, 100, 800 NTU',
            'Işık kaynağı' => 'Kızılötesi emisyon diyodu, 850 nm dalga boyu',
            'Koruma derecesi' => 'IP 67 (CEI EN 60529)',
            'Gövde malzemesi' => 'ABS',
            'Ağırlık' => '0.2 kg',
            'Boyutlar (GxYxD)' => '68 x 50 x 155 mm',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'pH Metre',
            'Model' => 'TB1',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ölçüm aralığı' => '0-1000 NTU',
            'Koruma derecesi' => 'IP 67',
        ],
        'documents' => [],
        'image_alt' => 'VELP TB1 türbidimetre ürün görseli',
    ],
];

$imageFor = function (array|string $slugs) use ($root): ?string {
    foreach ((array) $slugs as $slug) {
        foreach (['', '-1', '-2'] as $suffix) {
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

$stmt = $db->prepare('select id from product_brands where slug = :slug');
$stmt->execute(['slug' => 'velp']);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    throw new RuntimeException('VELP markası bulunamadı.');
}

$selectCategory = $db->prepare('select id from product_categories where slug = :slug');
$insertCategory = $db->prepare('insert into product_categories (name, slug, summary, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :aliases, 1, :sort_order, :created_at, :updated_at)');
$updateCategory = $db->prepare('update product_categories set name = :name, summary = :summary, aliases = :aliases, is_active = 1, updated_at = :updated_at where id = :id');
$selectCategoryBrand = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$insertCategoryBrand = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');

$categoryIds = [];

foreach ($categories as $slug => $category) {
    $selectCategory->execute(['slug' => $slug]);
    $categoryId = $selectCategory->fetchColumn();

    if (! $categoryId) {
        $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_categories')->fetchColumn();
        $insertCategory->execute([
            'name' => $category['name'],
            'slug' => $slug,
            'summary' => $category['summary'],
            'aliases' => $json($category['aliases']),
            'sort_order' => $sortOrder,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $categoryId = $db->lastInsertId();
    } else {
        $updateCategory->execute([
            'name' => $category['name'],
            'summary' => $category['summary'],
            'aliases' => $json($category['aliases']),
            'updated_at' => $now,
            'id' => $categoryId,
        ]);
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

    $categoryIds[$slug] = $categoryId;
}

$stmt = $db->prepare('select id from services where slug = :slug');
$stmt->execute(['slug' => 'devir-kalibrasyonu']);
$rpmServiceId = $stmt->fetchColumn();

if ($rpmServiceId && isset($categoryIds['rotator-calkalayici'])) {
    $stmt = $db->prepare('select count(*) from product_category_service where product_category_id = :category_id and service_id = :service_id');
    $stmt->execute(['category_id' => $categoryIds['rotator-calkalayici'], 'service_id' => $rpmServiceId]);

    if ((int) $stmt->fetchColumn() === 0) {
        $stmt = $db->prepare('insert into product_category_service (product_category_id, service_id, created_at, updated_at) values (:category_id, :service_id, :created_at, :updated_at)');
        $stmt->execute([
            'category_id' => $categoryIds['rotator-calkalayici'],
            'service_id' => $rpmServiceId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

$selectProduct = $db->prepare('select id from products where product_category_id = :category_id and slug = :slug');
$insertProduct = $db->prepare("insert into products (product_category_id, product_brand_id, name, slug, model, sku, old_url, summary, body, image, image_alt, features, metadata, specs, status, is_featured, sort_order, published_at, created_at, updated_at) values (:category_id, :brand_id, :name, :slug, :model, :sku, :old_url, :summary, :body, :image, :image_alt, :features, :metadata, :specs, 'published', 0, :sort_order, :published_at, :created_at, :updated_at)");
$updateProduct = $db->prepare("update products set product_brand_id = :brand_id, name = :name, model = :model, sku = :sku, old_url = :old_url, summary = :summary, body = :body, image = :image, image_alt = :image_alt, features = :features, metadata = :metadata, specs = :specs, status = 'published', is_featured = 0, sort_order = :sort_order, published_at = :published_at, updated_at = :updated_at where id = :id");
$deleteDocuments = $db->prepare('delete from product_documents where product_id = :product_id');
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, :path, :url, :sort_order, :created_at, :updated_at)');

$sortCounters = [];

foreach ($products as $product) {
    $categorySlug = $product['category_slug'];
    $categoryId = $categoryIds[$categorySlug];
    $sortCounters[$categorySlug] = ($sortCounters[$categorySlug] ?? 0) + 10;

    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => null,
        'old_url' => $product['old_url'],
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['image_slugs']),
        'image_alt' => $product['image_alt'],
        'features' => $json($product['features']),
        'metadata' => $json($product['metadata']),
        'specs' => $json($product['specs']),
        'sort_order' => $sortCounters[$categorySlug],
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

echo 'brand_id=' . $brandId . PHP_EOL;
foreach ($categories as $slug => $category) {
    echo 'category=' . $slug . ':' . $categoryIds[$slug] . PHP_EOL;
}

foreach ($products as $product) {
    echo 'product_slug=' . $product['slug'] . PHP_EOL;
    echo 'image=' . ($imageFor($product['image_slugs']) ?: 'missing') . PHP_EOL;
}

echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
