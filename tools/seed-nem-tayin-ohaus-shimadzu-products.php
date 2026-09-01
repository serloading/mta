<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Nem Tayin',
    'slug' => 'nem-tayin',
    'summary' => 'Numune nem oranı ölçümü için nem tayin cihazları ve nem analizörleri.',
    'aliases' => ['Nemtayin', 'Nem Tayin Cihazları', 'Nem Tayin Cihazı', 'Nem Ölçer'],
];

$brands = [
    'ohaus' => [
        'name' => 'Ohaus',
        'slug' => 'ohaus',
        'summary' => 'Terazi, nem tayin, pH metre, iletkenlik ölçer ve laboratuvar cihazları.',
        'logo' => 'images/brands/ohaus.png',
        'aliases' => ['OHAUS', 'Ohaus Türkiye'],
    ],
    'shimadzu' => [
        'name' => 'Shimadzu',
        'slug' => 'shimadzu',
        'summary' => 'Analitik cihazlar, tartım sistemleri ve laboratuvar çözümleri.',
        'logo' => 'images/brands/shimadzu.png',
        'aliases' => ['SHIMADZU'],
    ],
];

$products = [
    [
        'brand_slug' => 'shimadzu',
        'name' => 'Shimadzu MOC-63u Nem Tayin Cihazı',
        'slug' => 'shimadzu-moc-63u-nem-tayin-cihazi',
        'model' => 'MOC-63u',
        'sku' => 'MTA-SHIMADZU-MOC63U',
        'old_url' => 'https://biltekas.com/urun/moc-63u-nem-tayin-cihazi/',
        'summary' => 'Shimadzu MOC-63u; 60 g kapasite, 0.001 g / %0.01 hassasiyet ve 50-200 °C sıcaklık aralığına sahip halojen lambalı nem tayin cihazıdır.',
        'features' => [
            '400 W çubuk halojen lamba ile hızlı sonuç',
            '60 g kapasite ve 0.001 g / %0.01 hassasiyet',
            '50-200 °C sıcaklık aralığı',
            '100 sonuçluk dahili hafıza',
            '10 adet metot hafızası',
            'RS-232, USB ve I/O haberleşme portları',
            'Kapak hareketine duyarlı otomatik start/stop',
        ],
        'metadata' => [
            'Marka' => 'Shimadzu',
            'Kategori' => 'Nem Tayin',
            'Model' => 'MOC-63u',
            'SKU' => 'MTA-SHIMADZU-MOC63U',
            'Ürün tipi' => 'Nem tayin cihazı',
            'Görsel durumu' => 'Placeholder',
        ],
        'specs' => [
            'Model' => 'MOC-63u',
            'Kapasite' => '60 g',
            'Hassasiyet' => '0.001 g / %0.01',
            'Sıcaklık aralığı' => '50-200 °C',
            'Kefe ebadı' => '95 mm',
            'Isıtıcı' => '400 W çubuk halojen lamba',
            'Kullanım' => '11 adet tuş ile pratik kullanım',
            'PC bağlantısı' => 'WindowsDirect PC bağlantısı',
            'Sonuç hafızası' => '100 sonuç',
            'Metot hafızası' => '10 metot',
            'Isıtma modu' => '5 farklı ısıtma modu',
            'Ekran' => 'Arkadan aydınlatmalı LCD ekran',
            'Kasa' => 'Anti-manyetik alaşım kasa',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'MOC-63u Kataloğu', 'type' => 'catalog', 'url' => 'http://www.ilkaymuhendislik.com/uploads/MOC63.pdf'],
        ],
        'videos' => [
            ['title' => 'Ürün Videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=w_mOmFMEd04', 'youtube_id' => 'w_mOmFMEd04'],
        ],
    ],
    [
        'brand_slug' => 'ohaus',
        'name' => 'Ohaus MB32 Nem Tayin Cihazı',
        'slug' => 'ohaus-mb32-nem-tayin-cihazi',
        'model' => 'MB32',
        'sku' => '30971934',
        'old_url' => 'https://biltekas.com/urun/ohaus-mb32-nem-tayin-cihazi/',
        'summary' => 'Ohaus MB32; 90 g kapasite, 1 mg / %0.01 okunabilirlik ve 40-180 °C sıcaklık aralığına sahip karbon fiber ısıtıcılı nem tayin cihazıdır.',
        'features' => [
            '90 g maksimum kapasite',
            '1 mg / %0.01 okunabilirlik',
            'Karbon fiber ısıtıcı',
            'USB ve RS232 arayüz',
            'Standart ve hızlı ısıtma tipleri',
            '2 satır 4 inç LCD ekran',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'Nem Tayin',
            'Model' => 'MB32',
            'SKU' => '30971934',
            'Ürün tipi' => 'Nem tayin cihazı',
            'Görsel durumu' => 'Placeholder',
        ],
        'specs' => [
            'Marka' => 'Ohaus',
            'Model' => 'MB32',
            'Ürün kodu' => '30971934',
            'Maksimum kapasite' => '90 gr',
            'Okunabilirlik' => '1 mg / 0.01%',
            'Isıtıcı tipi' => 'Karbon fiber',
            'Arayüz' => 'USB, RS232',
            'Kalibrasyon' => 'Harici',
            'Ekran' => '2 satır 4 inç LCD ekran',
            'Sonuç gösterimi' => 'RG%; sıcaklık; DC%; zaman; MC%; ağırlık',
            'Isıtma tipleri' => 'Standart, hızlı',
            'Nem aralığı' => '0.01%-100%',
            'Sıcaklık aralığı' => '40-180 °C',
            'Tekrarlanabilirlik' => '%0.02 (10 g numune); %0.15 (3 g numune)',
            'Testi bitirme kriterleri' => 'Otomatik, zaman ayarlı, manuel',
            'Kefe boyutu' => '90 mm dairesel',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Ürün Broşürü', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2024/10/EU__Datasheet_MB32_EN__80776947.pdf'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2024/10/30984799_EU_DoC_A_MB32MB62MB92_2024-06-04.pdf'],
        ],
        'videos' => [],
    ],
    [
        'brand_slug' => 'ohaus',
        'name' => 'Ohaus MB62 Nem Tayin Cihazı',
        'slug' => 'mb62',
        'model' => 'MB62',
        'sku' => '30971935',
        'old_url' => 'https://biltekas.com/urun/mb62/',
        'summary' => 'Ohaus MB62; 90 g kapasite, 1 mg / %0.01 okunabilirlik, 40-200 °C sıcaklık aralığı ve 100 sonuç hafızalı nem tayin cihazıdır.',
        'features' => [
            '90 g maksimum kapasite',
            '1 mg / %0.01 okunabilirlik',
            '40-200 °C sıcaklık aralığı',
            'Standart, hızlı, rampa ve kademeli ısıtma',
            '100 sonuç ve 20 metot hafızası',
            'Çok renkli durum ışığı',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'Nem Tayin',
            'Model' => 'MB62',
            'SKU' => '30971935',
            'Ürün tipi' => 'Nem tayin cihazı',
            'Görsel durumu' => 'Placeholder',
        ],
        'specs' => [
            'Marka' => 'Ohaus',
            'Model' => 'MB62',
            'Ürün kodu' => '30971935',
            'Maksimum kapasite' => '90 gr',
            'Okunabilirlik' => '1 mg / 0.01%',
            'Isıtıcı tipi' => 'Karbon fiber',
            'Arayüz' => 'USB, RS232',
            'Kalibrasyon' => 'Harici',
            'Ekran' => '2 satır 5 inç LCD ekran',
            'Sonuç gösterimi' => 'MC%, DC%, RG%, zaman, sıcaklık, ağırlık, metot no',
            'Isıtma tipleri' => 'Standart, hızlı, rampa, kademeli',
            'Nem aralığı' => '0.01%-100%',
            'Sıcaklık aralığı' => '40-200 °C',
            'Tekrarlanabilirlik' => '%0.018 (10 g numune); %0.10 (3 g numune)',
            'Testi bitirme kriterleri' => 'Otomatik, zaman ayarlı, manuel',
            'Metot hafızası' => '20',
            'Sonuç hafızası' => '100',
            'ID yönetimi' => '10 ID',
            'Durum ışığı' => 'Çok renkli durum ışığı',
            'Operasyon talimatları' => 'İlerleme göstergesi, grafik gösterimi',
            'Kefe boyutu' => '90 mm dairesel',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Ürün Broşürü', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2024/10/EU__Datasheet_MB62_EN__80776949.pdf'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2024/10/30984799_EU_DoC_A_MB32MB62MB92_2024-06-04.pdf'],
        ],
        'videos' => [],
    ],
    [
        'brand_slug' => 'ohaus',
        'name' => 'Ohaus MB92 Nem Tayin Cihazı',
        'slug' => 'mb92',
        'model' => 'MB92',
        'sku' => '30971936',
        'old_url' => 'https://biltekas.com/urun/mb92/',
        'summary' => 'Ohaus MB92; 90 g kapasite, 1 mg / %0.01 okunabilirlik, 40-200 °C sıcaklık aralığı ve 4.3 inç kapasitif dokunmatik ekranlı nem tayin cihazıdır.',
        'features' => [
            '90 g maksimum kapasite',
            '1 mg / %0.01 okunabilirlik',
            '4.3 inç kapasitif dokunmatik ekran',
            'Standart, hızlı, rampa ve kademeli ısıtma',
            '200 sonuç ve 20 metot hafızası',
            'İlerleme göstergesi ve grafik gösterimi',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'Nem Tayin',
            'Model' => 'MB92',
            'SKU' => '30971936',
            'Ürün tipi' => 'Nem tayin cihazı',
            'Görsel durumu' => 'Placeholder',
        ],
        'specs' => [
            'Marka' => 'Ohaus',
            'Model' => 'MB92',
            'Ürün kodu' => '30971936',
            'Maksimum kapasite' => '90 gr',
            'Okunabilirlik' => '1 mg / 0.01%',
            'Isıtıcı tipi' => 'Karbon fiber',
            'Arayüz' => 'USB, RS232',
            'Kalibrasyon' => 'Harici',
            'Ekran' => '4.3 inç kapasitif dokunmatik ekran',
            'Sonuç gösterimi' => 'RG%; sıcaklık; kurutma eğrisi; DC%; zaman; yöntem adı; istatistikler; MC%; ağırlık',
            'Isıtma tipleri' => 'Standart, hızlı, rampa, kademeli',
            'Nem aralığı' => '0.01%-100%',
            'Sıcaklık aralığı' => '40-200 °C',
            'Tekrarlanabilirlik' => '%0.015 (10 g numune); %0.08 (3 g numune)',
            'Testi bitirme kriterleri' => 'Otomatik, zaman ayarlı, manuel',
            'Metot hafızası' => '20',
            'Sonuç hafızası' => '200',
            'ID yönetimi' => '10 ID',
            'Durum ışığı' => 'Çok renkli durum ışığı',
            'Operasyon talimatları' => 'İlerleme göstergesi, grafik gösterimi',
            'Kefe boyutu' => '90 mm dairesel',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Ürün Broşürü', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2024/10/EU__Datasheet_MB92_EN__80776951.pdf'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2024/10/30984799_EU_DoC_A_MB32MB62MB92_2024-06-04.pdf'],
        ],
        'videos' => [],
    ],
    [
        'brand_slug' => 'ohaus',
        'name' => 'Ohaus MC 2000 Tahıl Nem Ölçer',
        'slug' => 'ohaus-mc-2000',
        'model' => 'MC 2000',
        'sku' => 'MTA-OHAUS-MC2000',
        'old_url' => 'https://biltekas.com/urun/ohaus-mc-2000/',
        'summary' => 'Ohaus MC 2000; tarımsal uygulamalarda tahıl nem içeriğini 5 saniyede ölçmek için tasarlanmış elde taşınabilir hububat nem analiz cihazıdır.',
        'features' => [
            'Tahıl nem ölçümü için portatif cihaz',
            '%3-%45 nem aralığı',
            '5 saniye ölçüm süresi',
            '%0.1 okunabilirlik',
            '200 ml numune hacmi',
            '50 prosedürlük kütüphane',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'Nem Tayin',
            'Eski kategori' => 'Gıda Kontrol Cihazı',
            'Model' => 'MC 2000',
            'SKU' => 'MTA-OHAUS-MC2000',
            'Ürün tipi' => 'Tahıl nem ölçer',
            'Görsel durumu' => 'Placeholder',
        ],
        'specs' => [
            'Nem aralığı' => '%3-%45',
            'Ölçüm süresi' => '5 sn',
            'Okunabilirlik' => '%0.1',
            'Numune hacmi' => '200 ml',
            'Kullanım' => 'Bütün tahıl analizi; örnek hazırlama veya öğütme gerekmez',
            'Sıcaklık dengeleme' => 'Otomatik sıcaklık dengeleme',
            'Ölçüm modları' => '50 prosedürlük kütüphane, önceden programlanmış 10 prosedür',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [],
        'videos' => [],
    ],
];

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
}

$brandIds = [];
$selectBrand = $db->prepare('select id from product_brands where slug = :slug');
$insertBrand = $db->prepare('insert into product_brands (name, slug, summary, logo, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :logo, :aliases, 1, :sort_order, :created_at, :updated_at)');
$updateBrand = $db->prepare('update product_brands set name = :name, summary = :summary, logo = :logo, aliases = :aliases, is_active = 1, updated_at = :updated_at where id = :id');

foreach ($brands as $brand) {
    $selectBrand->execute(['slug' => $brand['slug']]);
    $brandId = $selectBrand->fetchColumn();

    if ($brandId) {
        $updateBrand->execute([
            'id' => $brandId,
            'name' => $brand['name'],
            'summary' => $brand['summary'],
            'logo' => $brand['logo'],
            'aliases' => $json($brand['aliases']),
            'updated_at' => $now,
        ]);
    } else {
        $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_brands')->fetchColumn();
        $insertBrand->execute([
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
    }

    $brandIds[$brand['slug']] = $brandId;
}

$selectCategoryBrand = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$insertCategoryBrand = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');

foreach ($brandIds as $brandId) {
    $selectCategoryBrand->execute(['category_id' => $categoryId, 'brand_id' => $brandId]);
    if ((int) $selectCategoryBrand->fetchColumn() === 0) {
        $insertCategoryBrand->execute(['category_id' => $categoryId, 'brand_id' => $brandId, 'created_at' => $now, 'updated_at' => $now]);
    }
}

$selectProduct = $db->prepare('select id from products where slug = :slug limit 1');
$insertProduct = $db->prepare("insert into products (product_category_id, product_brand_id, name, slug, model, sku, old_url, summary, body, image, image_alt, gallery, features, metadata, specs, status, is_featured, sort_order, published_at, seo_title, meta_description, robots, created_at, updated_at) values (:category_id, :brand_id, :name, :slug, :model, :sku, :old_url, :summary, :body, null, :image_alt, null, :features, :metadata, :specs, 'published', 0, :sort_order, :published_at, :seo_title, :meta_description, 'index,follow', :created_at, :updated_at)");
$updateProduct = $db->prepare("update products set product_category_id = :category_id, product_brand_id = :brand_id, name = :name, model = :model, sku = :sku, old_url = :old_url, summary = :summary, body = :body, image = null, image_alt = :image_alt, gallery = null, features = :features, metadata = :metadata, specs = :specs, status = 'published', is_featured = 0, sort_order = :sort_order, published_at = :published_at, seo_title = :seo_title, meta_description = :meta_description, robots = 'index,follow', updated_at = :updated_at where id = :id");
$deleteDocuments = $db->prepare('delete from product_documents where product_id = :product_id');
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, null, :url, :sort_order, :created_at, :updated_at)');
$deleteVideos = $db->prepare('delete from product_videos where product_id = :product_id');
$insertVideo = $db->prepare('insert into product_videos (product_id, title, youtube_url, youtube_id, sort_order, created_at, updated_at) values (:product_id, :title, :youtube_url, :youtube_id, :sort_order, :created_at, :updated_at)');

foreach ($products as $index => $product) {
    $selectProduct->execute(['slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $payload = [
        'category_id' => $categoryId,
        'brand_id' => $brandIds[$product['brand_slug']],
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => $product['sku'],
        'old_url' => $product['old_url'],
        'summary' => $product['summary'],
        'body' => $product['summary'] . ' Teknik özellikler, belge bilgileri ve teklif talebi için ürün sayfası kullanılabilir.',
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json($product['features']),
        'metadata' => $json($product['metadata']),
        'specs' => $json($product['specs']),
        'sort_order' => 2750 + (($index + 1) * 10),
        'published_at' => $now,
        'seo_title' => $product['name'] . ' | MTA Endüstri',
        'meta_description' => mb_substr($product['summary'], 0, 155, 'UTF-8'),
        'updated_at' => $now,
    ];

    if ($productId) {
        $payload['id'] = $productId;
        $updateProduct->execute($payload);
    } else {
        $payload['slug'] = $product['slug'];
        $payload['created_at'] = $now;
        $insertProduct->execute($payload);
        $productId = $db->lastInsertId();
    }

    $deleteDocuments->execute(['product_id' => $productId]);
    foreach ($product['documents'] as $documentIndex => $document) {
        $insertDocument->execute([
            'product_id' => $productId,
            'title' => $document['title'],
            'type' => $document['type'],
            'url' => $document['url'],
            'sort_order' => ($documentIndex + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

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

$db->commit();

echo "Seeded " . count($products) . " Ohaus/Shimadzu moisture analyzer products.\n";
