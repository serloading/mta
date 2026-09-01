<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Analitik Teraziler',
    'slug' => 'analitik-teraziler',
    'summary' => 'Laboratuvar analizleri için yüksek hassasiyetli analitik teraziler.',
    'aliases' => [
        'Analitik Terazi',
        'Analitik Teraziler',
        'Analytical Balance',
    ],
];

$products = [
    [
        'name' => 'Weightlab WSA-224 Analitik Terazi',
        'slug' => 'weightlab-wsa-224-analitik-terazi',
        'image_slugs' => [
            'weightlab-wsa-224-analitik-terazi',
            'weightlab-wsa-224-analitik-terazi-220-gr-01-mg',
            'wsa-224-analitik-terazi',
        ],
        'model' => 'WSA-224',
        'sku' => null,
        'old_url' => 'https://www.weightlabinstruments.com/urun/wsa-224-analitik-terazi/',
        'summary' => 'Weightlab WSA-224 analitik terazi; 220 g tartım kapasitesi, 0.0001 g (0.1 mg) hassasiyet, dahili ve harici kalibrasyon, RS232C/USB bağlantıları ve IP54 ekran korumasıyla laboratuvar analiz tartımları için kullanılır.',
        'body' => 'Weightlab WSA-224 analitik terazi, yüksek çözünürlüklü arkadan aydınlatmalı LCD ekranı, sıcaklık kompanzasyonu, tam kapasiteli dara ve aşırı yük korumasıyla hassas laboratuvar tartım süreçleri için tasarlanmıştır. Yoğunluk ölçümü, parça sayımı, alt tartım fonksiyonu ve PC/yazıcı bağlantılarıyla analitik uygulamalarda pratik kullanım sağlar.',
        'features' => [
            'Yüksek çözünürlüklü, arkadan aydınlatmalı LCD ekran',
            '110 mm diyagonal ekran',
            'Kapasite göstergesi',
            'Dahili ve harici kalibrasyon seçeneği',
            'Aşırı yük koruması',
            'Otomatik olarak ayarlanan çevresel ayarlar',
            'Tam kapasiteli dara',
            'Sıcaklık kompanzasyonu',
            'Aşırı yük ve alarm fonksiyonu',
            'Çoklu tartım birimleri (g/oz/ct)',
            'Katı ve sıvılar için yoğunluk ölçümü',
            'Parça sayımı',
            'Alt tartım fonksiyonu ve tartım kancası',
            'IP54 su sıçramalarına ve toza karşı koruma için ekran ve ekran toz kapağı',
            'PC ve yazıcı bağlantısı için standart RS232C ve USB çıkışları',
            'Ayrılabilir ekran',
            'Sağ ve sol cam için kilit sistemi ve kalemi',
            'Opsiyonel yoğunluk ölçüm kiti',
        ],
        'metadata' => [
            'Marka' => 'Weightlab',
            'Kategori' => 'Analitik Teraziler',
            'Üst kategori' => 'Teraziler',
            'Model' => 'WSA-224',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Tartım kapasitesi' => '220 g',
            'Hassasiyet' => '0.0001 g (0.1 mg)',
            'Kalibrasyon' => 'Dahili ve harici',
            'Kullanım alanı' => 'Analitik tartım',
        ],
        'specs' => [
            'Tartım kapasitesi' => '220 g',
            'Hassasiyet' => '0.0001 g (0.1 mg)',
            'Tekrarlanabilirlik' => '0.0001 g (0.1 mg)',
            'Doğrusallık' => '0.0002 g (0.2 mg)',
            'Stabilite süresi' => '3 saniye',
            'Kalibrasyon' => 'Dahili ve harici',
            'Çalışma ortamı' => '5-40 °C, 85% RH',
            'Kefe boyutu' => 'Ø 90 mm',
            'Alttan tartım kancası' => 'Standart',
            'Koruma sınıfı' => 'IP-54 ekran',
            'Cihaz boyutları' => '365 x 338 x 223 mm',
            'Net ağırlık' => '6 kg',
            'Ekran' => '110 mm diyagonal arkadan aydınlatmalı LCD',
            'Bağlantı' => 'RS232C ve USB',
        ],
        'documents' => [
            [
                'title' => 'Şartname',
                'type' => 'specification',
                'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/20_3_1.doc',
                'path' => null,
            ],
            [
                'title' => 'Tanıtım Dosyası',
                'type' => 'catalog',
                'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WSA-224%20&%20WSA-224T_1.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'Weightlab WSA-224 analitik terazi ürün görseli',
    ],
    [
        'name' => 'Weightlab WSA-224T Analitik Terazi',
        'slug' => 'weightlab-wsa-224t-analitik-terazi',
        'image_slugs' => [
            'weightlab-wsa-224t-analitik-terazi',
            'weightlab-wsa-224t-analitik-terazi-220-gr-01-mg',
            'wsa-224t-analitik-terazi',
        ],
        'model' => 'WSA-224T',
        'sku' => null,
        'old_url' => 'https://www.weightlabinstruments.com/urun/wsa-224t-analitik-terazi-2/',
        'summary' => 'Weightlab WSA-224T analitik terazi; 220 g tartım kapasitesi, 0.0001 g (0.1 mg) hassasiyet, TFT dokunmatik ekran, dahili/harici kalibrasyon, GLP & GMP uyumlu kayıt yapısı ve Türkçe menü seçeneğiyle hassas laboratuvar tartımları için kullanılır.',
        'body' => 'Weightlab WSA-224T, dokunmatik ekranlı analitik terazi yapısıyla yoğunluk ölçümü, parça sayımı, yüzde tartım, kullanıcı tanımlı birim, tepe ölçümü, hayvan tartımı ve dahili istatistik hesaplama gibi gelişmiş tartım fonksiyonlarını bir arada sunar. RS232 ve USB bağlantıları, şifreli menü kilidi, dahili tarih-saat ve ayrılabilir ekran yapısı laboratuvar kayıt süreçlerini destekler.',
        'features' => [
            'Yüksek çözünürlüklü TFT dokunmatik ekran',
            'Kapasite göstergesi',
            'Dahili ve harici kalibrasyon seçeneği',
            'Aşırı yük koruması',
            'Otomatik olarak ayarlanan çevresel ayarlar',
            'Tam kapasite dara alma',
            'Sıcaklık kompanzasyonu',
            'Aşırı yük ve alarm fonksiyonu',
            '18 adet çoklu tartım ünitesi',
            'Katı ve sıvılar için yoğunluk ölçümü',
            'Parça sayımı, yüzde tartım ve kullanıcı tanımlı birim',
            'Tepe ölçümü, hayvan tartımı ve dahili istatistik hesaplama fonksiyonu',
            'Alt tartım fonksiyonu ve tartım kancası',
            'Su sıçramaları ve toza karşı korumalı IP54 ekran ve ekran toz kapağı',
            'PC ve yazıcı bağlantısı için standart RS232C ve USB çıkışları',
            'Ayrılabilir ekran',
            'Sağ ve sol cam için kilit sistemi ve kalemi',
            'GLP & GMP uyumlu belgeler',
            'Türkçe ve İngilizce dil seçeneği',
            'Yerleşik tarih ve saat işlevi',
            'Yetkili şifreli menü yapısı',
            'Çevrimiçi sistem güncellemesi',
            'Kullanıcı dostu menü yapısı',
            'Opsiyonel yoğunluk ölçüm kiti',
        ],
        'metadata' => [
            'Marka' => 'Weightlab',
            'Kategori' => 'Analitik Teraziler',
            'Üst kategori' => 'Teraziler',
            'Model' => 'WSA-224T',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Tartım kapasitesi' => '220 g',
            'Hassasiyet' => '0.0001 g (0.1 mg)',
            'Kalibrasyon' => 'Dahili ve harici',
            'Dil seçeneği' => 'Türkçe ve İngilizce',
            'Kullanım alanı' => 'Dokunmatik analitik tartım',
        ],
        'specs' => [
            'Tartım kapasitesi' => '220 g',
            'Hassasiyet' => '0.0001 g (0.1 mg)',
            'Tekrarlanabilirlik' => '0.0001 g (0.1 mg)',
            'Doğrusallık' => '0.0002 g (0.2 mg)',
            'Stabilite süresi' => '3 sn',
            'Kalibrasyon' => 'Dahili ve harici',
            'RS232' => 'Standart',
            'USB (Type B)' => 'Standart',
            'Alttan tartım fonksiyonu' => 'Standart',
            'Alttan tartım kancası' => 'Standart',
            'Şifreli menü kilidi' => 'Standart',
            'Dahili tarih ve saat' => 'Standart',
            'Dahili istatistik hesaplama' => 'Standart',
            'Dil seçeneği' => 'Türkçe ve İngilizce',
            'Çalışma ortamı' => '5-40 °C, 85% RH',
            'Kefe çapı' => 'Ø 90 mm',
            'Ekran' => 'Dokunmatik ekran',
            'Koruma sınıfı' => 'IP-54',
            'Cihaz boyutları' => '365 x 338 x 223 mm',
            'Net ağırlık' => '6.0 kg',
        ],
        'documents' => [
            [
                'title' => 'Şartname',
                'type' => 'specification',
                'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WSA-224T_1.doc',
                'path' => null,
            ],
            [
                'title' => 'Tanıtım Dosyası',
                'type' => 'catalog',
                'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WSA-224%20&%20WSA-224T_2.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'Weightlab WSA-224T analitik terazi ürün görseli',
    ],
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
$stmt->execute(['slug' => 'weightlab']);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    throw new RuntimeException('Weightlab markası bulunamadı.');
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
