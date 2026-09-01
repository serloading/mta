<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'BOİ Ölçüm Cihazı',
    'slug' => 'boi-olcum-cihazi',
    'summary' => 'BOİ ölçümü ve çevre analizleri için BOD sensör sistemleri.',
    'aliases' => [
        'BOİ Ölçüm Cihazı',
        'BOI Olcum Cihazi',
        'BOD Ölçüm Cihazı',
        'BOD Sensor',
    ],
];

$baseFeatures = [
    'BOİ analizi için pratik, kolay ve güvenilir çözüm',
    '90, 250, 600 ve 999 ppm BOİ tahmini ölçeklerinde ölçüm',
    'Daha yüksek değerlerin numune seyreltilerek ölçülebilmesi',
    'Manyetik karıştırma istasyonlu modellerde VELP karıştırma kalitesi',
    'Doğrudan numuneyi içeren şişeye uyan sensör başlıkları',
    'Analizin hafta sonu boyunca devam edebilmesi',
    'Beş gün sonra bile herhangi bir zamanda doğrudan okunabilen sonuçlar',
    'Kompakt, çıkartılabilir ve kolay taşınabilir manyetik karıştırma tabanı istasyonları',
    'IP 54 ile partikül ve sıvı girişine karşı koruma',
    'Güvenlik sınıfında 3 IEC 1010 uygunluğu',
    'Tüm modeller için ayrıca BOİ inkübatörü gereklidir',
];

$products = [
    [
        'name' => 'VELP BOD Sensör Sistem Cıvasız BOİ Ölçüm Cihazı',
        'slug' => 'velp-bod-sensor-sistem-civasiz-boi-olcum-cihazi',
        'image_slugs' => ['velp-bod-sensor-sistem-civasiz-boi-olcum-cihazi', 'bod-sensor-sistem-civasiz-boi-olcum-cihazi'],
        'model' => 'BOD Sensör Sistem',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-bod-sensor-sistem-civasiz-boi-olcum-cihazi',
        'summary' => 'VELP BOD Sensör Sistem; cıvasız elektronik basınç probu ile ölçüm yapan, 500 ml şişe kapasitesi, 3 haneli LED ekran ve BOD Sensor, Sensor Set, System-6, System-10 kapasite seçenekleri sunan dijital BOİ ölçüm çözümüdür.',
        'body' => 'VELP BOD Sensör Sistem, biyokimyasal oksijen ihtiyacı analizlerinde cıva içermeyen elektronik basınç probu teknolojisiyle doğrudan mg/l (ppm) okuma sağlar. Tekli sensörden 6 veya 10 pozisyonlu komple sistemlere kadar farklı kapasite seçenekleriyle çevre ve atık su laboratuvarlarında kullanılır.',
        'features' => [
            ...$baseFeatures,
            'Cıva içermeyen elektronik basınç probu ile ölçüm',
            'Mikroişlemci kontrollü basınç transdüseri ile BOİ değerinin doğrudan sensör ekranına aktarılması',
            'Hesaplama yapmaya gerek kalmadan mg/l (ppm) cinsinden doğrudan okuma',
            'Her şişe için 24 saat aralıklarla 5 BOİ değerinin otomatik saklanması',
            'BOD Sensör: tek bir BOİ analizi için sensör',
            'BOD Sensör Seti: sensör, koyu renkli cam şişe, alkali tutucu ve manyetik karıştırma balığı',
            'BOD Sensör Sistemi-6: 6 konumlu karıştırma istasyonu ve 6 test için komple paket',
            'BOD Sensör Sistemi-10: 10 konumlu karıştırma istasyonu ve 10 test için komple paket',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'BOİ Ölçüm Cihazı',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'BOD Sensör Sistem',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Ölçüm teknolojisi' => 'Cıvasız, elektronik basınç probu',
            'Numune kapasitesi' => 'Her şişe 500 ml',
            'Kullanım alanı' => 'BOİ / BOD analizi',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Cihaz kapasiteleri' => 'BOD Sensor tekli sensör / Sensor Set tekli set / System-6 altılı set ve karıştırıcı / System-10 onlu set ve karıştırıcı',
            'Numune kapasitesi' => 'Her şişe 500 ml',
            'Ölçüm değeri' => 'mg/l (ppm) olarak doğrudan ekranda görülür',
            'Ölçüm teknolojisi' => 'Cıvasız, elektronik basınç probu ile ölçüm',
            'Veri hafızası' => 'Her şişe için 24 saat aralıklarla 5 BOİ değeri',
            'BOİ değerleri okuma' => 'Anlık değer ve 5 gün sonundaki değer ekrandan görülebilir',
            'Son BOİ analizi' => 'Görülebilir',
            'Analiz öncesi tahmini skalalar' => '90, 250, 600, 999 ppm BOİ',
            'Ekran' => '3 haneli LED',
            'Güvenlik sınıfı' => '3 IEC 1010',
            'Elektronik koruma derecesi' => 'IP 54 (CEI EN 60529)',
            'Ağırlık' => '2.3 kg',
            'Boyutlar (GxYxD)' => '270 x 300 x 185 mm',
            'Güç' => '2 W',
        ],
        'documents' => [],
        'image_alt' => 'VELP BOD Sensör Sistem cıvasız BOİ ölçüm cihazı ürün görseli',
    ],
    [
        'name' => 'VELP BOD EVO Sensor Sistem Cıvasız Kablosuz BOİ Ölçüm Cihazı',
        'slug' => 'velp-bod-evo-sensor-sistem-civasiz-kablosuz-boi-olcum-cihazi',
        'image_slugs' => ['velp-bod-evo-sensor-sistem-civasiz-kablosuz-boi-olcum-cihazi', 'bod-evo-sensor-sistem-civasiz-kablosuz-boi-olcum-cihazi'],
        'model' => 'BOD EVO Sensor Sistem',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-bod-evo-sensor-sistem-civasiz-kablosuz-boi-olcum-cihazi',
        'summary' => 'VELP BOD EVO Sensor Sistem; cıvasız elektronik basınç probu, kablosuz iletişim, BODSoft yazılım yönetimi, 500 ml şişe kapasitesi ve altı numunelik sistem yapısıyla BOİ analizleri için kullanılır.',
        'body' => 'VELP BOD EVO Sensor Sistem, kablosuz BOD sensör başlıkları ve DataBox arayüzü ile BOİ analiz verilerinin PC üzerinden yönetilmesini sağlar. BODSoft yazılımı sayesinde gerçek zaman çizelgesi, pil durumu, veri karşılaştırma ve grafik oluşturma süreçleri izlenebilir.',
        'features' => [
            ...$baseFeatures,
            'Yenilikçi kablosuz teknoloji ile uzaktan çalışma',
            'Kablosuz BOD sensör başlığının veriyi Wireless DataBox cihazına iletmesi',
            'BODSoft yazılımı ile verilerin yönetimi, karşılaştırılması ve grafik oluşturulması',
            'BOİ değerinin sensör ekranına veya yazılım ile PC’ye aktarılması',
            'Gerçek zaman çizelgesi ve sensör pil durumunun izlenmesi',
            'İnkübatör kapısını açmadan analiz takibi',
            'Aynı anda 80 sensör başlığına kadar izleme',
            'Özelleştirilebilir süre aralıklarıyla otomatik veri depolama',
            'Analiz desteği için önceden kurulu metod kütüphanesi',
            'BOD EVO Sensör Sistemi-6 ile 6 test için komple paket',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'BOİ Ölçüm Cihazı',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'BOD EVO Sensor Sistem',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Ölçüm teknolojisi' => 'Cıvasız, elektronik basınç probu',
            'İletişim' => 'Kablosuz DataBox / PC',
            'Numune kapasitesi' => 'Her şişe 500 ml',
            'Kullanım alanı' => 'Kablosuz BOİ / BOD analizi',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Cihaz kapasitesi' => 'Altı numune için set (sensör + şişe) ve karıştırıcı',
            'Numune kapasitesi' => 'Her şişe 500 ml',
            'Ölçüm değeri' => 'mg/l (ppm) olarak ekranda veya PC’de görülür',
            'Ölçüm teknolojisi' => 'Cıvasız, elektronik basınç probu ile ölçüm',
            'Veri hafızası (5 günlük BOD)' => 'Her 30 dk / 1 / 2 / 4 / 6 / 8 / 12 / 24 saatte',
            'Veri hafızası (uzun süreler)' => 'Her 2 / 4 / 6 / 8 / 12 / 24 saatte',
            'BOİ değerleri okuma' => 'Otomatik veri hesaplama ve BOD yazılımı üzerinden yönetim',
            'Son BOİ analizi' => 'Görülebilir',
            'Analiz öncesi tahmini skalalar' => '90, 250, 600, 999 ppm BOİ',
            'Ekran' => '3 haneli LED',
            'Gövde malzemesi' => 'Teknopolimer',
            'Güvenlik sınıfı' => '3 IEC 1010',
            'Elektronik koruma derecesi' => 'IP 54 (CEI EN 60529)',
            'Ağırlık' => '2.3 kg',
            'Boyutlar (GxYxD)' => '270 x 300 x 185 mm',
            'Güç' => '2 W',
        ],
        'documents' => [],
        'image_alt' => 'VELP BOD EVO Sensor Sistem cıvasız kablosuz BOİ ölçüm cihazı ürün görseli',
    ],
    [
        'name' => 'VELP BMS6 Cıvalı Manometrik BOİ Ölçüm Cihazı',
        'slug' => 'velp-bms6-civali-manometrik-boi-olcum-cihazi',
        'image_slugs' => ['velp-bms6-civali-manometrik-boi-olcum-cihazi', 'bms6-civali-manometrik-boi-olcum-cihazi'],
        'model' => 'BMS6',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-bms6-civali-manometrik-boi-olcum-cihazi',
        'summary' => 'VELP BMS6 cıvalı manometrik BOİ ölçüm cihazı; altı numune şişesi, 500 ml şişelerde 100-400 ml numune kapasitesi ve 90, 250, 600, 999 ppm BOİ tahmini skalalarıyla geleneksel BOİ analizleri için kullanılır.',
        'body' => 'VELP BMS6, biyokimyasal oksijen talebinin geleneksel cıvalı manometrik yöntemle ölçülmesi için tasarlanmış analog masa üstü BOİ sistemidir. Altı şişeli düzenekte her şişe kendi manometresiyle takip edilir ve 6 testlik paket yapısı çevre laboratuvarlarında BOİ uygulamalarını destekler.',
        'features' => [
            'Biyokimyasal Oksijen Talebi testi için geleneksel cıvalı manometrik yöntem',
            '90, 250, 600 ve 999 ppm BOİ tahmini ölçeklerinde ölçüm',
            '1000 mg/l (ppm) seviyesine kadar konsantrasyonlarda kullanım',
            'Daha yüksek değerlerin numune seyreltilerek ölçülebilmesi',
            'Her şişede kendi manometresi bulunan 6 şişeli düzenek',
            '500 ml şişelerde 100-400 ml numune hacmi',
            '6 konumlu karıştırma istasyonu, koyu renkli cam şişeler, alkalin tutucu, cıva ve manyetik karıştırma balıkları içeren paket',
            'Tüm modeller için ayrıca BOİ inkübatörü gereklidir',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'BOİ Ölçüm Cihazı',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'BMS6',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Ölçüm teknolojisi' => 'Cıvalı manometrik',
            'Cihaz kapasitesi' => 'Altı adet numune şişesi',
            'Kullanım alanı' => 'Manometrik BOİ / BOD analizi',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Cihaz kapasitesi' => 'Altı adet numune şişesi',
            'Numune kapasitesi' => '500 ml şişeler 100-400 ml numune alabilir',
            'Ölçüm teknolojisi' => 'Cıvalı manometrik',
            'Analiz öncesi tahmini skalalar' => '90, 250, 600, 999 ppm BOİ',
            'Ağırlık' => '7 kg',
            'Boyutlar (GxYxD)' => '360 x 350 x 210 mm',
        ],
        'documents' => [],
        'image_alt' => 'VELP BMS6 cıvalı manometrik BOİ ölçüm cihazı ürün görseli',
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
