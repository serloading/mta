<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Vorteks Karıştırıcılar',
    'slug' => 'vorteks-karistiricilar',
    'summary' => 'Tüp, vial, plaka ve küçük hacimli numuneler için vorteks karıştırıcılar.',
    'aliases' => [
        'Vorteks Karıştırıcı',
        'Vorteks Karıştırıcılar',
        'Vortex Mixer',
        'Vortex Mixers',
    ],
];

$baseFeatures = [
    'Teknopolimer kaplama ile kimyasallara karşı optimum direnç',
    'Çinko alaşımlı taban ile yüksek stabilite',
    'Kaymayı önleyen kauçuk ayaklar',
    'IP 42 ile partiküllere ve sıvılara karşı koruma',
];

$makeProduct = function (
    string $model,
    string $slug,
    array $imageSlugs,
    string $deviceType,
    string $speed,
    string $mode,
    string $feet,
    string $weight,
    string $dimensions,
    string $catalogUrl,
    array $features,
    array $extraSpecs = []
) use ($baseFeatures): array {
    return [
        'name' => "VELP {$model} Vorteks Karıştırıcı",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} vorteks karıştırıcı; {$deviceType} kullanım, 4.5 mm orbital çap, {$speed} karıştırma hızı ve {$mode} çalışma modu ile tüp, plaka ve küçük hacimli numune karıştırma işlemleri için kullanılır.",
        'body' => "VELP {$model} vorteks karıştırıcı, laboratuvarlarda hızlı ve kararlı vorteksleme ihtiyacı için geliştirilmiş masa üstü bir modeldir. Çinko alaşımlı tabanı ve teknopolimer gövde yapısı yüksek hızda stabilite, kimyasal direnç ve uzun süreli kullanım desteği sağlar.",
        'features' => [
            ...$features,
            ...$baseFeatures,
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Vorteks Karıştırıcılar',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => $deviceType,
            'Orbital çap' => '4.5 mm',
            'Karıştırma hızı' => $speed,
            'Çalışma modu' => $mode,
            'Kullanım alanı' => 'Vorteks karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => $deviceType,
            'Orbital çap' => '4.5 mm',
            'Karıştırma hızı' => $speed,
            'Çalışma modu' => $mode,
            'Gövde malzemesi' => 'Çinko alaşımı ve teknopolimer',
            'Destek sistemi' => $feet,
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => $dimensions,
            'Koruma sınıfı CEI EN 60529' => 'IP 42',
            'Güç' => '15 W',
            ...$extraSpecs,
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => $catalogUrl,
                'path' => null,
            ],
        ],
        'image_alt' => "VELP {$model} vorteks karıştırıcı ürün görseli",
    ];
};

$touchStartFeatures = [
    'Tüp destek yuvası üzerine bastırıldığında karıştırmanın otomatik başlaması',
    'Elektronik kontrollü sabit veya değişken hızlı karıştırma',
];

$irFeatures = [
    'IR sensör vasıtasıyla tüp algılandığında karıştırmanın başlaması',
    'Bastırma gerektirmeyen kullanım ile yorgunluğu azaltma',
    'Tekrarlayan zorlama hasarını azaltmaya yardımcı çalışma yapısı',
    'IR sensör algılaması ile otomatik başlama modu',
    'Tüpler, plakalar ve şişeler için geniş ve kolay takılabilen aksesuar seçenekleri',
    'Birçok platform ve aksesuarla kullanılabilen çok yönlü yapı',
    'Uzun ömürlü tasarım',
];

$products = [
    $makeProduct(
        'RX3',
        'velp-rx3-vorteks-karistirici',
        ['velp-rx3-vorteks-karistirici', 'rx3-vorteks-karistirici', 'r3-vorteks-karistirici'],
        'Analog, masa üstü',
        '3000 rpm sabit',
        'Dokunma',
        'Kaymayı önleyen 4 ayak',
        '2.7 kg',
        '150 x 130 x 165 mm',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717161242_2992velp_vortex_mixers_comparison_table.pdf',
        [
            'Elektronik kontrollü 3000 rpm sabit karıştırma',
            ...$touchStartFeatures,
        ]
    ),
    $makeProduct(
        'ZX3',
        'velp-zx3-vorteks-karistirici',
        ['velp-zx3-vorteks-karistirici', 'zx3-vorteks-karistirici'],
        'Analog, masa üstü',
        '3000 rpm ayarlanabilir',
        'Dokunma ve sürekli mod',
        'Kaymayı önleyen 4 ayak',
        '2.7 kg',
        '150 x 130 x 165 mm',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717161333_2885velp_vortex_mixers_comparison_table.pdf',
        [
            '3000 rpm seviyesine kadar ayarlanabilen ve elektronik olarak kontrol edilen değişken hızlı karıştırma',
            ...$touchStartFeatures,
            'Dokunmatik modda otomatik başlatma veya sürekli çalışma',
            'Tüpler, plakalar ve şişeler için geniş ve kolay takılabilen aksesuar seçenekleri',
            'Yüksek hızda sabit kalmayı destekleyen 4 kaymayan ayak ve çinko taban',
            'Kararlı karıştırma için ergonomik ve yenilikçi tasarım',
            'Birçok platform ve aksesuarla kullanılabilen çok yönlü yapı',
        ]
    ),
    $makeProduct(
        'ZX4',
        'velp-zx4-vorteks-karistirici',
        ['velp-zx4-vorteks-karistirici', 'zx4-vorteks-karistirici'],
        'Analog, masa üstü',
        '3000 rpm ayarlanabilir',
        'Infrared sensörlü çalışma ve sürekli mod',
        'Kaymayı önleyen 4 ayak',
        '2.7 kg',
        '150 x 130 x 165 mm',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717161408_2693velp_vortex_mixers_comparison_table.pdf',
        [
            '3000 rpm seviyesine kadar ayarlanabilen ve elektronik olarak kontrol edilen değişken hızlı karıştırma',
            ...$irFeatures,
            'Yüksek hızda sabit kalmayı destekleyen 4 kaymayan ayak ve çinko taban',
            'Kararlı karıştırma için ergonomik ve yenilikçi tasarım',
        ]
    ),
    $makeProduct(
        'TX4',
        'velp-tx4-vorteks-karistirici',
        ['velp-tx4-vorteks-karistirici', 'tx4-vorteks-karistirici', 'tx4-votek-karistirici'],
        'Dijital, masa üstü',
        '3000 rpm ayarlanabilir',
        'Infrared sensörlü çalışma ve sürekli mod',
        'Kaymayı önleyen 4 ayak',
        '2.7 kg',
        '150 x 130 x 165 mm',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717161452_2969velp_vortex_mixers_comparison_table.pdf',
        [
            '3000 rpm seviyesine kadar ayarlanabilen ve elektronik olarak kontrol edilen değişken hızlı karıştırma',
            ...$irFeatures,
            'Titreşim zamanının ve hızının dijital kontrolü',
            'Karıştırma hızı, çalışma süresi ve çalışma modunu gösteren parlak LCD ekran',
            'Tek bakışta önemli parametrelerin izlenebilmesi',
        ],
        [
            'Zamanlayıcı süresi' => '0 ila 999 saat 59 dakika arası ayarlanabilir',
            'Ekran' => 'LCD',
        ]
    ),
    $makeProduct(
        'CLASSIC',
        'velp-classic-vorteks-karistirici',
        ['velp-classic-vorteks-karistirici', 'classic-vorteks-karistirici'],
        'Analog, masa üstü',
        '0-3000 rpm',
        'Dokunma ve sürekli mod',
        'Kaymayı önleyen 3 ayak',
        '2.2 kg',
        '180 x 70 x 220 mm',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717161521_2749velp_vortex_mixers_comparison_table.pdf',
        [
            '3000 rpm seviyesine kadar ayarlanabilen ve elektronik olarak kontrol edilen değişken hızlı karıştırma',
            ...$touchStartFeatures,
            'Dokunmatik modda otomatik başlatma veya sürekli çalışma',
            'Tüpler, plakalar ve şişeler için geniş ve kolay takılabilen aksesuar seçenekleri',
            'Kararlılığı optimize eden çalışma modu',
            'Ergonomik, yenilikçi ve konforlu tasarım',
            'Birçok platform ve aksesuarla kullanılabilen çok yönlü yapı',
            'Kompakt ve az yer kaplayan yapı',
        ]
    ),
    $makeProduct(
        'WIZARD',
        'velp-wizard-vorteks-karistirici',
        ['velp-wizard-vorteks-karistirici', 'wizard-vorteks-karistirici'],
        'Analog, masa üstü',
        '0-3000 rpm',
        'Infrared sensörlü çalışma ve sürekli mod',
        'Kaymayı önleyen 3 ayak',
        '2.2 kg',
        '180 x 70 x 220 mm',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717161550_2649velp_vortex_mixers_comparison_table.pdf',
        [
            '3000 rpm seviyesine kadar ayarlanabilen ve elektronik olarak kontrol edilen değişken hızlı karıştırma',
            ...$irFeatures,
            'Kararlılığı optimize eden çalışma modu',
            'Ergonomik, yenilikçi ve konforlu tasarım',
            'Kompakt ve az yer kaplayan yapı',
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
