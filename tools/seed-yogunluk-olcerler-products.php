<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'Yoğunluk Ölçerler',
    'slug' => 'yogunluk-olcerler',
    'summary' => 'Sıvı numunelerde yoğunluk ve özgül ağırlık ölçümü için yoğunluk ölçer cihazlar.',
    'aliases' => ['Yoğunluk Ölçer', 'Yogunluk Olcerler', 'Density Meter', 'DSG Yoğunluk Ölçer'],
];

$brand = [
    'name' => 'Bellingham + Stanley',
    'slug' => 'bellingham-stanley',
    'summary' => 'Refraktometre, polarimetre ve yoğunluk ölçer çözümleri.',
    'logo' => 'images/brands/bellingham-stanley.png',
    'aliases' => ['BELİNGHAMSTARLY', 'Bellingham and Stanley', 'Bellingham + Stanley'],
];

$product = [
    'name' => 'Bellingham+Stanley DSG Serisi Yoğunluk Ölçerler',
    'slug' => 'bellingham-stanley-dsg-serisi-yogunluk-olcerler',
    'model' => 'DSG Serisi',
    'summary' => 'Bellingham+Stanley DSG serisi, 21 CFR Bölüm 11 uyumlu, Peltier sıcaklık kontrollü ve 20 saniye okuma süreli yoğunluk ölçer serisidir.',
    'body' => 'Bellingham+Stanley DSG serisi yoğunluk ölçerler; kolay kullanım, güvenilir yapı ve hassas ölçüm ihtiyaçları için tasarlanmıştır. DSG 40 ve DSG 50 model seçenekleri; yoğunluk, özgül ağırlık, sükroz (%w/w, °Brix), etanol (%w/w, %v/v) ve kullanıcı tanımlı metotlarla çalışır. Peltier sıcaklık kontrolü, detaylı denetim yolları ve FDA 21 CFR Bölüm 11 uyumu ile kalite kontrol ve regülasyon odaklı laboratuvar kullanımlarına uygundur.',
    'features' => [
        'FDA 21 CFR Bölüm 11 uyumlu',
        'Dokunmatik ekran',
        'Metot esaslı kullanım',
        'Peltier sıcaklık kontrolü',
        '20 saniye okuma süresi',
        'Kapsamlı temizlik için güçlü hava pompası',
        'Standart luer konektörler',
        'DSG 40 ve DSG 50 model seçenekleri',
    ],
    'specs' => [
        'Ürün tipi' => 'Yoğunluk ölçer',
        'Seri' => 'DSG Serisi',
        'Uyumluluk' => 'FDA 21 CFR Bölüm 11',
        'Kullanım özellikleri' => 'Kullanımı ve temizlenmesi kolay; dokunmatik ekran; metot esaslı kullanım; isteğe bağlı klavye ve fare desteği; 2 benzersiz model seçeneği; kapsamlı temizlik için güçlü hava pompası',
        'Güvenilirlik özellikleri' => 'Yüksek kaliteli yapı; standart luer konektörler; Avrupa üretimi; yerel müşteri desteği; kapsamlı özelleştirme',
        'Hassasiyet özellikleri' => 'Mükemmel yeniden üretilebilirlik; 20 saniye okuma süresi; Peltier sıcaklık kontrolü; FDA 21 CFR Bölüm 11 uyumlu; detaylı denetim yolları',
        'DSG 40' => 'Ölçüm aralığı: 0-3 g/cm³; metotlar: yoğunluk (g/cm³), özgül ağırlık, sükroz %w/w (°Brix), etanol (%w/w, %v/v), kullanıcı isteği; okuma tipi: tek atım; okuma süresi: 20 saniye; çözünürlük: 0.0001 g/cm³; hassasiyet: 0.0001 g/cm³; tekrarlanabilirlik: 0.0001 g/cm³; sıcaklık hassasiyeti: 0.05 °C; sıcaklık tekrarlanabilirliği: 0.01 °C; sıcaklık ölçüm aralığı: 10-95 °C; sıcaklık çözünürlüğü: 0.01 °C; sıcaklık stabilizasyon süresi: algılanan numuneye dayalı akıllı stabilizasyon',
        'DSG 50' => 'Ölçüm aralığı: 0-3 g/cm³; metotlar: yoğunluk (g/cm³), özgül ağırlık, sükroz %w/w (°Brix), etanol (%w/w, %v/v), kullanıcı isteği; okuma tipi: tek atım; okuma süresi: 20 saniye; çözünürlük: 0.00001 g/cm³; hassasiyet: 0.00005 g/cm³; tekrarlanabilirlik: 0.00002 g/cm³; sıcaklık hassasiyeti: 0.03 °C; sıcaklık tekrarlanabilirliği: 0.02 °C; sıcaklık ölçüm aralığı: 10-95 °C; sıcaklık çözünürlüğü: 0.01 °C; sıcaklık stabilizasyon süresi: algılanan numuneye dayalı akıllı stabilizasyon',
        'Görsel durumu' => 'Placeholder',
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
        'Kategori' => 'Yoğunluk Ölçerler',
        'Üst kategori' => 'Densitometre',
        'Model' => $product['model'],
        'SKU' => 'Yayın öncesi netleştirilecek',
        'Ürün tipi' => 'Yoğunluk ölçer',
    ]),
    'specs' => $json($product['specs']),
    'sort_order' => 10,
    'published_at' => $now,
    'updated_at' => $now,
];

if ($productId) {
    $payload['id'] = $productId;
    $updateProduct->execute($payload);
} else {
    $payload['category_id'] = $categoryId;
    $payload['slug'] = $product['slug'];
    $payload['created_at'] = $now;
    $insertProduct->execute($payload);
    $productId = $db->lastInsertId();
}

$deleteDocuments->execute(['product_id' => $productId]);
$deleteVideos->execute(['product_id' => $productId]);

echo "Seeded density meter products.\n";
