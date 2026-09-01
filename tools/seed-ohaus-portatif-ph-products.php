<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'pH İletkenlik & Metreler',
    'slug' => 'ph-iletkenlik',
    'summary' => 'pH metre, iletkenlik, oksijen ve çok parametreli ölçüm cihazları.',
    'aliases' => ['pH&İletkenlik', 'pH-İletkenlik Ölçerler', 'pH ve İletkenlik', 'Portatif Tip Cihazlar'],
];

$brand = [
    'name' => 'Ohaus',
    'slug' => 'ohaus',
    'summary' => 'Terazi, nem tayin, pH metre, iletkenlik ölçer ve laboratuvar cihazları.',
    'logo' => 'images/brands/ohaus.png',
    'aliases' => ['OHAUS', 'Ohaus Türkiye'],
];

$products = [
    [
        'name' => 'OHAUS ST300-G Portatif pH Metre',
        'slug' => 'ohaus-st-300-ph-g',
        'model' => 'ST300-G',
        'sku' => '83033961',
        'summary' => 'OHAUS ST300-G; laboratuvar ve saha çalışmaları için pH, mV ve sıcaklık ölçümü yapan portatif pH metredir.',
        'features' => ['pH, mV ve sıcaklık ölçümü', '0.00-14.00 pH ölçüm aralığı', '30 ölçüm hafızası', 'IP54 koruma', 'OHAUS ST320 pH elektrodu'],
        'specs' => [
            'Marka' => 'OHAUS',
            'Model' => 'ST300-G',
            'Ürün kodu' => '83033961',
            'Set içeriği' => 'ST300 pH metre, taşıma çantası, IP54 aksesuarları, elektrot klipsi, bilek sargısı, 4 adet AAA pil, toz tampon çözeltileri',
            'Set elektrotları' => 'OHAUS ST320 pH elektrodu (83033967)',
            'Ürün cinsi' => 'Portatif tip pH-ORP metre',
            'Ölçüm aralığı' => '0.00-14.00 pH; -1999-1999 mV; 0-100 °C',
            'Ölçüm çözünürlüğü' => '0.01 pH; 1 mV; 0.1 °C',
            'Hata payı' => '±0.01 pH; ±1 mV; ±0.5 °C',
            'Kalibrasyon' => '3 nokta; 4 önceden tanımlanmış tampon grubu',
            'Hafıza' => '30 ölçüm ve son kalibrasyon verisi',
            'Güç' => '4 AAA; >500 çalışma saati',
            'Boyut/ağırlık' => 'Yaklaşık 90 W x 150 D x 35 H mm / 0.18 kg (pilsiz)',
            'Ekran' => 'LCD',
            'Sıcaklık kompanzasyonu' => 'Otomatik ve manuel',
            'IP sınıfı' => 'IP54',
            'Kasa malzemesi' => 'ABS',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/ST-300-SERISI-TEKNIK-OZELLIKLERI.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/OHAUS-ST-300-pH.doc'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/ST300_DoC_EN-ES-FR-DE-IT.pdf'],
        ],
        'videos' => [
            ['title' => 'Ürün Videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=oggOK2r7pwg', 'youtube_id' => 'oggOK2r7pwg'],
        ],
    ],
    [
        'name' => 'OHAUS ST400-G Portatif pH Metre',
        'slug' => 'ohaus-st400-g',
        'model' => 'ST400-G',
        'sku' => '30468966',
        'summary' => 'OHAUS ST400-G; IP67 korumalı, şarj edilebilir bataryalı portatif pH metredir.',
        'features' => ['pH, mV ve sıcaklık ölçümü', '-2.00-16.00 pH ölçüm aralığı', '1000 ölçüm hafızası', 'IP67 koruma', 'Micro-USB iletişim'],
        'specs' => [
            'Marka' => 'OHAUS',
            'Model' => 'ST400-G',
            'Ürün kodu' => '30468966',
            'Set içeriği' => 'ST400 pH metre, taşıma çantası, IP67 aksesuarları, elektrot klipsi, bilek sargısı, 4 adet AAA pil',
            'Set elektrotları' => 'OHAUS ST320 IP67 pH elektrodu (30468960)',
            'Ürün cinsi' => 'Portatif tip pH-ORP metre',
            'Ölçüm aralığı' => '-2.00-16.00 pH; -1999-1999 mV; -5-110 °C',
            'Ölçüm çözünürlüğü' => '0.01 pH; 1 mV; 0.1 °C',
            'Hata payı' => '±0.01 pH; ±1 mV; ±0.5 °C',
            'Kalibrasyon' => '5 nokta; 6 önceden tanımlanmış tampon grubu',
            'Hafıza' => '1000 ölçüm ve 5 kalibrasyon verisi',
            'Bağlantı tipi' => 'IP67 BNC pH, IP67 Cinch sıcaklık',
            'Güç' => 'Şarj edilebilir Li-batarya, 2600 mAh',
            'Boyut/ağırlık' => 'Yaklaşık 100 W x 230 D x 40 H mm / 0.35 kg (pilsiz)',
            'Ekran' => 'LCD',
            'Sıcaklık kompanzasyonu' => 'Otomatik ve manuel',
            'IP sınıfı' => 'IP67',
            'Kasa malzemesi' => 'ABS',
            'İletişim' => 'Micro-USB',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2020/04/Starter-Portable-Meters-Datasheet-EN-80774673_D-v2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2020/04/OHAUS-ST-400-pH.doc'],
        ],
        'videos' => [],
    ],
    [
        'name' => 'OHAUS ST400D-G Portatif Çözünmüş Oksijen Metre',
        'slug' => 'ohaus-st400d-g',
        'model' => 'ST400D-G',
        'sku' => '30378542',
        'summary' => 'OHAUS ST400D-G; optik sensörlü, düşük bakım gereksinimli portatif çözünmüş oksijen ölçerdir.',
        'features' => ['Çözünmüş oksijen ve sıcaklık ölçümü', 'Optik DO sensör teknolojisi', '99 ölçüm hafızası', 'IP54 koruma', 'OHAUS STDO21 optik DO elektrodu'],
        'specs' => [
            'Marka' => 'OHAUS',
            'Model' => 'ST400D-G',
            'Ürün kodu' => '30378542',
            'Set içeriği' => 'ST400D oksijen ölçer, taşıma çantası, IP54 aksesuarları, elektrot klipsi, bilek sargısı, 4 adet AAA pil',
            'Set elektrotları' => 'OHAUS STDO21 optik DO elektrodu (30378544)',
            'Ürün cinsi' => 'Portatif tip çözünmüş oksijen ölçer',
            'Ölçüm aralığı' => '0.0-%200.0; 0.00-20.0 mg/L (ppm)',
            'Ölçüm çözünürlüğü' => '%0.1; 0.01 mg/L (ppm)',
            'Barometrik aralık' => '50.0-115.0 kPa',
            'Barometrik çözünürlük' => '0.1 kPa',
            'Kalibrasyon' => '1 veya 2 nokta',
            'Hafıza' => '99 ölçüm ve son kalibrasyon verisi',
            'Bağlantı tipi' => 'Mini-DIN',
            'Güç' => '4 AAA; >12 çalışma saati',
            'Boyut/ağırlık' => 'Yaklaşık 90 W x 150 D x 35 H mm / 0.16 kg (pilsiz)',
            'Ekran' => 'LCD',
            'Sıcaklık kompanzasyonu' => 'Otomatik',
            'Tuzluluk kompanzasyonu' => '0.0-40.0 ppt',
            'IP sınıfı' => 'IP54',
            'Kasa malzemesi' => 'ABS',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2020/04/Starter-Portable-Meters-Datasheet-EN-80774673_D-v2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2020/04/OHAUS-ST-400-D.doc'],
        ],
        'videos' => [],
    ],
    [
        'name' => 'OHAUS ST400M-G Portatif Multiparametre Ölçer',
        'slug' => 'ohaus-st400m-g',
        'model' => 'ST400M-G',
        'sku' => '30468992',
        'summary' => 'OHAUS ST400M-G; pH, mV, iletkenlik, TDS, tuzluluk, dirençlilik ve sıcaklık ölçümü yapan IP67 portatif multiparametre ölçerdir.',
        'features' => ['pH ve iletkenlik multiparametre ölçümü', 'TDS, tuzluluk ve dirençlilik ölçümü', '1000 ölçüm hafızası', 'IP67 koruma', 'Micro-USB iletişim'],
        'specs' => [
            'Marka' => 'OHAUS',
            'Model' => 'ST400M-G',
            'Ürün kodu' => '30468992',
            'Set içeriği' => 'ST400M multiparametre ölçer, taşıma çantası, IP67 aksesuarları, elektrot klipsi, bilek sargısı, 4 adet AAA pil',
            'Set elektrotları' => 'OHAUS ST320 IP67 pH elektrodu (30468960) ve OHAUS STCON3 IP67 (30468962)',
            'Ürün cinsi' => 'Portatif tip multiparametre ölçer',
            'Ölçüm aralığı (pH)' => '-2-16.00 pH',
            'Ölçüm çözünürlüğü (pH)' => '0.01 pH',
            'Ölçüm aralığı (iletkenlik)' => '0.0 µS/cm-199.9 mS/cm',
            'Ölçüm çözünürlüğü (iletkenlik)' => '0.1 µS/cm, otomatik aralık',
            'Ölçüm aralığı (TDS)' => '0.1 mg/L-199 g/L',
            'Ölçüm aralığı (tuzluluk)' => '0.0-99.9 psu',
            'Ölçüm aralığı (dirençlilik)' => '0-20 MΩ•cm',
            'Ölçüm aralığı (sıcaklık)' => '-5-110 °C',
            'Kalibrasyon' => '5 nokta, 6 önceden tanımlanmış tampon grubu',
            'Hafıza' => '1000 ölçüm ve her sensör ID’si için 5 kalibrasyon verisi',
            'Bağlantı tipi' => 'Mini-DIN',
            'Güç' => 'Şarj edilebilir Li-batarya, 2600 mAh',
            'IP sınıfı' => 'IP67 BNC pH, IP67 Cinch sıcaklık, IP67 LTW',
            'İletişim' => 'Micro-USB',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2020/04/Starter-Portable-Meters-Datasheet-EN-80774673_D-v2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2020/04/OHAUS-ST-400M.doc'],
        ],
        'videos' => [],
    ],
    [
        'name' => 'Ohaus ST 300 C-G Portatif İletkenlik Ölçer',
        'slug' => 'ohaus-st-300-c-g',
        'model' => 'ST 300 C-G',
        'sku' => 'MTA-OHAUS-ST300CG',
        'summary' => 'Ohaus ST 300 C-G; portatif iletkenlik, TDS ve sıcaklık ölçümü için 30 ölçüm hafızalı cihazdır.',
        'features' => ['Portatif iletkenlik ölçer', '0.0-199.9 mS/cm ölçüm aralığı', 'TDS ölçümü', '30 ölçüm hafızası', 'IP54 koruma'],
        'specs' => [
            'Ölçüm aralığı' => '0.0 µS/cm-199.9 mS/cm; 0.1 mg/L-199.9 mg/L (TDS); 0-100 °C',
            'Hassasiyet' => '0.1 °C otomatik ölçüm',
            'Hafıza' => '30 set hafıza, tarih ve zamanla son kalibrasyon datası',
            'Kalibrasyon' => 'Tek noktalı',
            'Koruma' => 'IP54',
            'Batarya' => '4 x AAA (500 saat)',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/Starter-Portable-Meters-Datasheet-EN-80774673_D-v2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/OHAUS-ST-300-C.doc'],
        ],
        'videos' => [],
    ],
    [
        'name' => 'Ohaus ST 300 D-G Portatif Oksijen Metre',
        'slug' => 'ohaus-st-300-d-g',
        'model' => 'ST 300 D-G',
        'sku' => 'MTA-OHAUS-ST300DG',
        'summary' => 'Ohaus ST 300 D-G; ppm, mg/L ve yüzde modlarında çözünmüş oksijen ölçümü yapan portatif oksijen metredir.',
        'features' => ['Portatif oksijen metre', 'ppm, mg/L ve yüzde DO ölçümü', '1 veya 2 noktalı kalibrasyon', '30 kayıt hafızası', 'IP54 koruma'],
        'specs' => [
            'Ölçüm aralığı' => '0.0-199.9%, 200-400%; 0.00-19.99, 20.0-45.0 mg/L; 0.00-19.99, 20.0-45.0 ppm; 0-50 °C',
            'Çözünürlük' => '0.1%, 1%; 0.1 mg/L, 1 mg/L; 0.01 ppm, 0.1 ppm; 0.1 °C',
            'Barometrik seviye' => '375-825 mmHg; 500-1100 mbar; 500-1100 hPa',
            'Barometrik çözünürlük' => '1 mmHg; 1 mbar; 1 hPa',
            'Hata limiti' => '±1%, ±0.3 °C',
            'Kalibrasyon' => '1 veya 2 nokta',
            'Sıcaklık kompanzasyonu' => 'ATC ve MTC',
            'Tuzluluk kompanzasyonu' => '0.0-50.0 ppt',
            'Hafıza' => '30 set hafıza, tarih ve zamanla son kalibrasyon datası',
            'Koruma' => 'IP54',
            'Batarya' => '4 x AAA (500 saat)',
            'Gövde' => 'ABS',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/Starter-Portable-Meters-Datasheet-EN-80774673_D-v2-1.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2017/02/OHAUS-ST-300-D-oxy.doc'],
        ],
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
}

$stmt = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$stmt->execute(['category_id' => $categoryId, 'brand_id' => $brandId]);
if ((int) $stmt->fetchColumn() === 0) {
    $stmt = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');
    $stmt->execute(['category_id' => $categoryId, 'brand_id' => $brandId, 'created_at' => $now, 'updated_at' => $now]);
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
    $metadata = [
        'Marka' => 'Ohaus',
        'Kategori' => 'pH & İletkenlik',
        'Alt kategori' => 'Portatif Tip Cihazlar',
        'Model' => $product['model'],
        'SKU' => $product['sku'],
        'Görsel durumu' => 'Placeholder',
    ];

    $payload = [
        'category_id' => $categoryId,
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => $product['sku'],
        'old_url' => 'https://biltekas.com/urun/' . $product['slug'] . '/',
        'summary' => $product['summary'],
        'body' => $product['summary'] . ' Teknik özellikler, broşür ve şartname dokümanları ürün sayfasında yer alır.',
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json($product['features']),
        'metadata' => $json($metadata),
        'specs' => $json($product['specs']),
        'sort_order' => 2600 + (($index + 1) * 10),
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

echo "Seeded " . count($products) . " Ohaus portable pH/conductivity/oxygen products.\n";
