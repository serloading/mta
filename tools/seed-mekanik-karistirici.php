<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Mekanik Karıştırıcılar',
    'slug' => 'mekanik-karistirici',
    'summary' => 'Yüksek hacimli veya yoğun numuneler için mekanik karıştırıcılar.',
    'aliases' => [
        'Mekanik Karıştırıcı',
        'Mekanik Karıştırıcılar',
        'Overhead Stirrer',
        'Overhead Stirrers',
    ],
];

$baseMechanicalFeatures = [
    'Özel teknopolimer yapı ile optimum kimyasal direnç ve gelişmiş kullanım',
    'Paslanmayı engellemeye yardımcı gövde yapısı',
    'Sıçrama ve kabarcık oluşumunu azaltan yumuşak başlama',
    'Yoğun karıştırma gerektiren görevlerde çalışma',
    'Uzun ömürlü, dayanıklı motor',
    'Bloke olduğunda otomatik durma; yanma veya hasar riskini azaltma',
    'Aşırı yük, aşırı akım ve aşırı sıcaklık koruması',
    'IP 40 standardına göre sızdırmaz yapı',
    'Alet gerektirmeyen, elle sıkıştırılabilen şaft sabitleyici tutamak',
    'Agresif sıvı ve buharlara karşı sızdırmaz gövde koruması',
    'Bakım için ekstra masraf gerektirmeden yıllarca sürekli çalışma hedefi',
    'Kolay ve hızlı kurulum',
    'İnce kasa ile laboratuvarda az yer kaplama',
];

$makeMechanicalProduct = function (
    string $model,
    string $slug,
    array $imageSlugs,
    string $speed,
    string $bodyMaterial,
    string $viscosity,
    string $torque,
    string $volume,
    string $weight,
    string $dimensions,
    string $power,
    string $catalogUrl,
    array $extraSpecs = [],
    array $extraFeatures = [],
    array $extraDocuments = []
) use ($baseMechanicalFeatures): array {
    $documents = [
        [
            'title' => 'Katalog / Türkçe',
            'type' => 'catalog',
            'url' => $catalogUrl,
            'path' => null,
        ],
        ...$extraDocuments,
    ];

    return [
        'name' => "VELP {$model} Mekanik Karıştırıcı",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} mekanik karıştırıcı; analog masa üstü kullanım, {$speed} karıştırma hızı, {$volume} karıştırma hacmi, {$viscosity} maksimum viskozite ve {$torque} maksimum tork ile laboratuvar uygulamaları için kullanılır.",
        'body' => "VELP {$model} mekanik karıştırıcı, zorlu karıştırma görevleri için tasarlanmış masa üstü overhead stirrer modelidir. {$bodyMaterial} gövde yapısı, yumuşak başlama, otomatik durma ve aşırı yük/akım/sıcaklık korumalarıyla uzun süreli laboratuvar kullanımını destekler.",
        'features' => [
            ...$baseMechanicalFeatures,
            ...$extraFeatures,
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Mekanik Karıştırıcılar',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Karıştırma hacmi' => $volume,
            'Karıştırma hızı' => $speed,
            'Maksimum viskozite' => $viscosity,
            'Maksimum tork' => $torque,
            'Kullanım alanı' => 'Mekanik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Karıştırma hızı' => $speed,
            'Gövde malzemesi' => $bodyMaterial,
            'Kullanılabilen şaft kalınlığı' => '1-10 mm',
            'Maksimum viskozite' => $viscosity,
            'Maksimum tork' => $torque,
            'Çalışma sıcaklığı' => '0-40 °C',
            'Karıştırma hacmi' => $volume,
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => $dimensions,
            'Güç' => $power,
            'Koruma sınıfı CEI EN 60529' => 'IP 40',
            ...$extraSpecs,
        ],
        'documents' => $documents,
        'image_alt' => "VELP {$model} mekanik karıştırıcı ürün görseli",
    ];
};

$products = [
    [
        'name' => 'VELP ES Mekanik Karıştırıcı',
        'slug' => 'velp-es-mekanik-karistirici',
        'image_slugs' => ['velp-es-mekanik-karistirici', 'es-mekanik-karistirici'],
        'model' => 'ES',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-es-mekanik-karistirici',
        'summary' => 'VELP ES mekanik karıştırıcı; analog masa üstü kullanım, 50-1300 rpm karıştırma hızı, 15 litre karıştırma hacmi, 1000 mPa.s maksimum viskozite ve 15 ncm maksimum tork ile laboratuvar uygulamaları için kullanılır.',
        'body' => 'VELP ES mekanik karıştırıcı, teknopolimer gövdesiyle kimyasallara karşı direnç ve kompakt laboratuvar kullanımı sunar. Yumuşak başlama, otomatik durma ve aşırı yük/akım/sıcaklık korumaları; yoğun veya değişken numunelerde güvenli karıştırma sürecini destekler.',
        'features' => [
            'Özel teknopolimer yapı ile optimum kimyasal direnç ve gelişmiş kullanım',
            'Paslanmayı engellemeye yardımcı gövde yapısı',
            'Sıçrama ve kabarcık oluşumunu azaltan yumuşak başlama',
            'Uzun ömürlü, dayanıklı motor',
            'Bloke olduğunda otomatik durma; yanma veya hasar riskini azaltma',
            'Aşırı yük, aşırı akım ve aşırı sıcaklık koruması',
            'IP 40 standardına göre sızdırmaz yapı',
            'Alet gerektirmeyen, elle sıkıştırılabilen şaft sabitleyici tutamak',
            'Agresif sıvı ve buharlara karşı sızdırmaz gövde koruması',
            'Bakım için ekstra masraf gerektirmeden yıllarca sürekli çalışma hedefi',
            'Kolay ve hızlı kurulum',
            'İnce kasa ile laboratuvarda az yer kaplama',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Mekanik Karıştırıcılar',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'ES',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Karıştırma hacmi' => '15 litre',
            'Karıştırma hızı' => '50-1300 rpm',
            'Maksimum viskozite' => '1000 mPa.s',
            'Maksimum tork' => '15 ncm',
            'Kullanım alanı' => 'Mekanik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Karıştırma hızı' => '50-1300 rpm',
            'Gövde malzemesi' => 'Teknopolimer',
            'Kullanılabilen şaft kalınlığı' => '1-10 mm',
            'Maksimum viskozite' => '1000 mPa.s',
            'Maksimum tork' => '15 ncm',
            'Çalışma sıcaklığı' => '0-40 °C',
            'Karıştırma hacmi' => '15 litre',
            'Ağırlık' => '1.3 kg',
            'Boyutlar (GxYxD)' => '80 x 160 x 200 mm',
            'Güç' => '30 W',
            'Koruma sınıfı CEI EN 60529' => 'IP 40',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717154116_2974velp_overhead_stirrers_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP ES mekanik karıştırıcı ürün görseli',
    ],
    $makeMechanicalProduct(
        'LS',
        'velp-ls-mekanik-karistirici',
        ['velp-ls-mekanik-karistirici', 'ls-mekanik-karistirici'],
        '50-2000 rpm',
        'Teknopolimer ve epoksi kaplı alüminyum',
        '25000 mPa.s',
        '40 ncm',
        '25 litre',
        '2.3 kg',
        '80 x 215 x 196 mm',
        '120 W',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717154146_2743velp_overhead_stirrers_comparison_table.pdf'
    ),
    $makeMechanicalProduct(
        'DLS',
        'velp-dls-mekanik-karistirici',
        ['velp-dls-mekanik-karistirici', 'dls-mekanik-karistirici', 'dls-mekanik-karistiricilar'],
        '50-2000 rpm',
        'Teknopolimer ve epoksi kaplı alüminyum',
        '25000 mPa.s',
        '40 ncm',
        '25 litre',
        '2.5 kg',
        '80 x 215 x 196 mm',
        '120 W',
        'https://www.sentezgroup.com.tr/img/mc-content/20170718081207_2501velp_overhead_stirrers_comparison_table.pdf',
        [
            'Zamanlayıcı süresi' => '999 saat 59 dakikaya kadar ayarlanabilir',
            'Ekran' => 'LCD',
        ],
        [
            'Son derece aydınlık ve okunması kolay LCD ekran',
            'Tüm ayarlanan ve anlık okunan parametre değerlerinin paralel görüntülenmesi',
            'Ayarlanmış hız, gerçek hız, moment ve zamanlayıcı bilgilerini gösterme',
            'Hız, gerçek hız, tork ve kalan sürenin doğru şekilde izlenebilmesi',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Değişen yük altında tam ve doğru hız kontrolü',
        ],
        [
            [
                'title' => 'Katalog / İngilizce',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170718081214_2865velp_overhead_stirrers_dls_dijital.pdf',
                'path' => null,
            ],
        ]
    ),
    $makeMechanicalProduct(
        'LH',
        'velp-lh-mekanik-karistirici',
        ['velp-lh-mekanik-karistirici', 'lh-mekanik-karistirici'],
        '50-2000 rpm',
        'Teknopolimer ve epoksi kaplı alüminyum',
        '50000 mPa.s',
        '80 ncm',
        '40 litre',
        '2.9 kg',
        '80 x 230 x 196 mm',
        '190 W',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717154336_2578velp_overhead_stirrers_comparison_table.pdf'
    ),
    $makeMechanicalProduct(
        'PW',
        'velp-pw-mekanik-karistirici',
        ['velp-pw-mekanik-karistirici', 'pw-mekanik-karistirici'],
        '20-1200 rpm',
        'Teknopolimer ve epoksi kaplı alüminyum',
        '100000 mPa.s',
        '120 ncm',
        '70 litre',
        '2.9 kg',
        '80 x 230 x 196 mm',
        '190 W',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717154524_2501velp_overhead_stirrers_comparison_table.pdf'
    ),
    $makeMechanicalProduct(
        'DLH',
        'velp-dlh-mekanik-karistirici',
        ['velp-dlh-mekanik-karistirici', 'dlh-mekanik-karistirici', 'dlh-mekanik-karistiricilar'],
        '50-2000 rpm',
        'Teknopolimer ve epoksi kaplı alüminyum',
        '50000 mPa.s',
        '80 ncm',
        '40 litre',
        '3 kg',
        '80 x 230 x 196 mm',
        '190 W',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717154425_2692velp_overhead_stirrers_comparison_table.pdf',
        [
            'Zamanlayıcı süresi' => '999 saat 59 dakikaya kadar ayarlanabilir',
            'Ekran' => 'LCD',
        ],
        [
            'Son derece aydınlık ve okunması kolay LCD ekran',
            'Tüm ayarlanan ve anlık okunan parametre değerlerinin paralel görüntülenmesi',
            'Ayarlanmış hız, gerçek hız, moment ve zamanlayıcı bilgilerini gösterme',
            'Hız, gerçek hız, tork ve kalan sürenin doğru şekilde izlenebilmesi',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Değişen yük altında tam ve doğru hız kontrolü',
        ]
    ),
];

$imageFor = function (array|string $slugs) use ($root): ?string {
    foreach ((array) $slugs as $slug) {
        foreach (['', '-1'] as $suffix) {
            foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
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
