<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Jar Test',
    'slug' => 'jar-test',
    'summary' => 'Su ve atık su uygulamalarında flokülasyon ve jar test çalışmaları için cihazlar.',
    'aliases' => [
        'Jar Test',
        'Jar Test Cihazı',
        'Flokülatör',
        'Flokulator',
    ],
];

$baseFeatures = [
    'VELP karıştırma kalitesinden gelen güvenilir ve tekrarlanabilir sonuçlar',
    'Elektronik olarak kontrol edilen DC dişli motor ile hassas ve doğru hız',
    'Numuneyi görmeyi kolaylaştıran aydınlatma sistemi',
    'Aletsiz olarak yüksekliği ayarlanabilen paslanmaz çelik karıştırma çubukları',
    'Kimyasal ve mekanik korozyona karşı güçlü direnç',
];

$makeProduct = function (
    string $model,
    string $slug,
    array $imageSlugs,
    string $deviceType,
    string $speedLabel,
    string $speedMode,
    string $timer,
    string $lighting,
    string $weight,
    string $dimensions,
    string $power,
    array $features,
    array $extraSpecs = []
) use ($baseFeatures): array {
    $timerText = $timer !== '' ? ", {$timer} zamanlayıcı" : '';

    return [
        'name' => "VELP {$model} Jar Test Cihazı",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} jar test cihazı; {$deviceType} kullanım, {$speedLabel} karıştırma hızı{$timerText} ve {$speedMode} ile su ve atık su flokülasyon uygulamaları için kullanılır.",
        'body' => "VELP {$model} jar test cihazı, birden fazla numunenin aynı anda karşılaştırılabilir koşullarda karıştırılması için geliştirilmiştir. Aydınlatmalı panel, paslanmaz çelik karıştırma çubukları ve DC dişli motor yapısı; flokülasyon, koagülasyon ve rutin su analizlerinde izlenebilir, tekrarlanabilir çalışma sağlar.",
        'features' => [
            ...$features,
            ...$baseFeatures,
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Jar Test',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => $deviceType,
            'Karıştırma hızı' => $speedLabel,
            'Hız seçimi' => $speedMode,
            'Zamanlayıcı' => $timer !== '' ? $timer : 'Yok',
            'Kullanım alanı' => 'Jar test / flokülasyon',
        ],
        'specs' => [
            'Cihaz tipi' => $deviceType,
            'Karıştırma hızı' => $speedLabel,
            'Hız seçimi' => $speedMode,
            'Paslanmaz çelik karıştırma çubukları' => 'Ayarlandığı pozisyonda duran sapı ile yükseklik ayarı yapılabilir',
            'Motor tipi' => 'DC gear motor',
            'Gövde malzemesi' => 'Epoksi kaplı metal',
            'Aydınlatma / arka panel' => $lighting,
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => $dimensions,
            'Güç' => $power,
            ...($timer !== '' ? ['Zamanlayıcı ayarı' => $timer] : []),
            ...$extraSpecs,
        ],
        'documents' => [],
        'image_alt' => "VELP {$model} jar test cihazı ürün görseli",
    ];
};

$products = [
    $makeProduct(
        'JLT4',
        'velp-jlt4-jar-test-cihazi',
        ['velp-jlt4-jar-test-cihazi', 'jlt4-jar-test-cihazi', 'jlt4-flokulator'],
        'Dörtlü dijital, masa üstü',
        '10-300 rpm',
        'Tek seçimli hız; her karıştırma çubuğu için aynı hız',
        '0-999 dk veya 0-99 saat veya sürekli',
        'Sökülebilir şekilde ışıklandırılmış arka panel',
        '13 kg',
        '645 x 347 x 260 mm',
        '11 W',
        [
            'Dört karıştırma pozisyonu ile aynı anda farklı numunelerle çalışma',
            'İki ayrı dijital ekran ile hız ve zamanlayıcı değerlerinin ayarlanıp izlenebilmesi',
            '10 ila 300 rpm arasında dijital olarak seçilebilen karıştırma hızı',
            'Mikroişlemci kontrollü zamanlayıcı ile süre sonunda otomatik durma',
            'Tüm pozisyonlar için aynı hız ve zaman ayarı ile karşılaştırılabilir sonuçlar',
            'Hız ve zaman için yüksek görünürlüklü LED ekranlar',
        ],
        [
            'Hız ayar hassasiyeti' => '1 rpm',
            'Tek seçimli hız' => 'Her karıştırma çubuğu için aynı hız',
        ]
    ),
    $makeProduct(
        'JLT6',
        'velp-jlt6-jar-test-cihazi',
        ['velp-jlt6-jar-test-cihazi', 'jlt6-jar-test-cihazi', 'jlt6-flokulator'],
        'Altılı dijital, masa üstü',
        '10-300 rpm',
        'Tek seçimli hız; her karıştırma çubuğu için aynı hız',
        '0-999 dk veya 0-99 saat veya sürekli',
        'Sökülebilir şekilde ışıklandırılmış arka panel',
        '17 kg',
        '935 x 347 x 260 mm',
        '19 W',
        [
            'Altı karıştırma pozisyonu ile aynı anda farklı numunelerle çalışma',
            'İki ayrı dijital ekran ile hız ve zamanlayıcı değerlerinin ayarlanıp izlenebilmesi',
            '10 ila 300 rpm arasında dijital olarak seçilebilen karıştırma hızı',
            'Mikroişlemci kontrollü zamanlayıcı ile süre sonunda otomatik durma',
            'Tüm pozisyonlar için aynı hız ve zaman ayarı ile karşılaştırılabilir sonuçlar',
            'Hız ve zaman için yüksek görünürlüklü LED ekranlar',
        ],
        [
            'Hız ayar hassasiyeti' => '1 rpm',
            'Tek seçimli hız' => 'Her karıştırma çubuğu için aynı hız',
        ]
    ),
    $makeProduct(
        'FC4S',
        'velp-fc4s-jar-test-cihazi',
        ['velp-fc4s-jar-test-cihazi', 'fc4s-jar-test-cihazi', 'fc4s-flokulator'],
        'Dörtlü analog, masa üstü',
        '10-15-30-45-60-90-120-150-200-300 rpm',
        'Çoklu seçimli hız; her çubuk için farklı ayarlanabilir hız',
        '',
        'Sökülebilir şekilde ışıklandırılmış arka panel',
        '12.5 kg',
        '645 x 347 x 260 mm',
        '18 W',
        [
            'Dört karıştırma pozisyonu ile aynı anda farklı numunelerle çalışma',
            'Her çubuk için bağımsız olarak ayarlanabilen farklı karıştırma hızları',
            '10-15-30-45-60-90-120-150-200-300 rpm karıştırma hızı seçimi',
        ],
        [
            'Çoklu seçimli hız' => 'Her çubuk için farklı ayarlanabilir hız',
        ]
    ),
    $makeProduct(
        'FC6S',
        'velp-fc6s-jar-test-cihazi',
        ['velp-fc6s-jar-test-cihazi', 'fc6s-jar-test-cihazi', 'fc6s-flokulator'],
        'Altılı analog, masa üstü',
        '10-15-30-45-60-90-120-150-200-300 rpm',
        'Çoklu seçimli hız; her çubuk için farklı ayarlanabilir hız',
        '',
        'Sökülebilir şekilde ışıklandırılmış arka panel',
        '18 kg',
        '935 x 347 x 260 mm',
        '23 W',
        [
            'Altı karıştırma pozisyonu ile aynı anda farklı numunelerle çalışma',
            'Her çubuk için bağımsız olarak ayarlanabilen farklı karıştırma hızları',
            '10-15-30-45-60-90-120-150-200-300 rpm karıştırma hızı seçimi',
        ],
        [
            'Çoklu seçimli hız' => 'Her çubuk için farklı ayarlanabilir hız',
        ]
    ),
    $makeProduct(
        'FP4',
        'velp-fp4-portatif-jar-test-cihazi',
        ['velp-fp4-portatif-jar-test-cihazi', 'fp4-portatif-jar-test-cihazi', 'fp4-flokulator'],
        'Dörtlü analog, portatif',
        '20-40-50-100-200 rpm',
        'Tek seçimli hız; her karıştırma çubuğu için aynı hız',
        '0-30 dk veya sürekli',
        'Bağlantısız merkezi ışık',
        '4.8 kg',
        '250 x 330 x 250 mm',
        '6 W',
        [
            'Portatif ve taşınabilir yapı; özel pil veya araç çakmağı ile çalışma',
            'Dört karıştırma pozisyonu ile aynı anda farklı numunelerle çalışma',
            'Yerinde kullanımda hızlı sonuç almak için tasarım',
            '20-40-50-100-200 rpm karıştırma hızı seçimi',
            'Zamanlayıcı ile süre sonunda otomatik durma',
            'Tüm pozisyonlar için aynı hız ve zaman ayarı ile karşılaştırılabilir sonuçlar',
            'Okumayı kolaylaştıran merkezi ışık',
            'Sabit durmasını sağlayan kaymaz taban',
            'Opsiyonel taşıma çantası ile kolay taşıma',
        ],
        [
            'Tek seçimli hız' => 'Her karıştırma çubuğu için aynı hız',
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
