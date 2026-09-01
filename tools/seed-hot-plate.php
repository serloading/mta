<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Hot Plate',
    'slug' => 'hot-plate',
    'summary' => 'Laboratuvar ısıtma uygulamaları için hot plate ve ısıtıcı tabla cihazları.',
    'aliases' => [
        'Hot Plate',
        'Isıtıcı Tabla',
        'Isitici Tabla',
        'Laboratuvar Hotplate',
        'Hotplate',
    ],
];

$aluminumFeatures = [
    'Alüminyum alaşımlı tabla ile tüm yüzey boyunca 370 °C seviyesine kadar sıcaklık homojenliği ve optimum ısı transferi',
    'Kimyasallara karşı mükemmel dayanıklılık',
    '15 litrelik şişelere uygun 155 mm çapında ocak gözü',
    'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
    'Sıcaklığın kolay ayarlanabilmesi',
    'Ergonomik, basit ve pratik tasarım',
    'AluBlock aksesuar kombinasyonları ile farklı tüplerle aynı anda çalışma imkanı',
    'AluBlock ile iyi ısı iletimi, sıcaklık kontrolü ve homojenlik',
    'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
    'Özel oluk sayesinde kontrol panelinin sıvı dökülmelerinden kaynaklanabilecek hasarlara karşı korunması',
];

$products = [
    [
        'name' => 'VELP RC Isıtıcı Tabla Hotplate',
        'slug' => 'velp-rc-isitici-tabla-hotplate',
        'image_slugs' => ['velp-rc-isitici-tabla-hotplate', 'velp-rc-hotplate', 'velp-rc-hot-plate'],
        'model' => 'RC',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-rc-isitici-tabla-hotplate',
        'summary' => 'VELP RC ısıtıcı tabla hotplate; analog masa üstü kullanım, ortam sıcaklığından 370 °C seviyesine ısıtma, özel korumalı alüminyum tabla ve tekli ısıtma pozisyonu ile laboratuvar ısıtma uygulamaları için kullanılır.',
        'body' => 'VELP RC hotplate, kompakt masa üstü yapısı ve özel korumalı alüminyum alaşımlı tablasıyla rutin laboratuvar ısıtma işlemleri için tasarlanmıştır. Eğimli kontrol paneli sıcaklık ayarını kolaylaştırır; özel oluk yapısı da dökülmelerin kontrol paneline zarar verme riskini azaltır.',
        'features' => $aluminumFeatures,
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Hot Plate',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'RC',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Tabla malzemesi' => 'Özel koruma ile kaplamalı alüminyum',
            'Isıtma pozisyon sayısı' => 'Tekli',
            'Kullanım alanı' => 'Hot plate / ısıtıcı tabla',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Tabla malzemesi' => 'Özel koruma ile kaplamalı alüminyum',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı alüminyum',
            'Isıtma pozisyon sayısı' => 'Tekli',
            'Ağırlık' => '1.4 kg',
            'Boyutlar (GxYxD)' => '165 x 115 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '600 W',
        ],
        'documents' => [],
        'image_alt' => 'VELP RC ısıtıcı tabla hotplate ürün görseli',
    ],
    [
        'name' => 'VELP RC2 Isıtıcı Tabla Hotplate',
        'slug' => 'velp-rc2-isitici-tabla-hotplate',
        'image_slugs' => ['velp-rc2-isitici-tabla-hotplate', 'velp-rc2-hotplate', 'velp-rc2-hot-plate'],
        'model' => 'RC2',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-rc2-isitici-tabla-hotplate',
        'summary' => 'VELP RC2 ısıtıcı tabla hotplate; analog masa üstü kullanım, ortam sıcaklığından 370 °C seviyesine ısıtma, özel korumalı alüminyum tabla ve ikili ısıtma pozisyonu ile laboratuvar ısıtma uygulamaları için kullanılır.',
        'body' => 'VELP RC2 hotplate, iki ayrı ısıtma pozisyonuna ihtiyaç duyulan laboratuvar uygulamaları için geliştirilmiş analog masa üstü ısıtıcı tabla modelidir. Alüminyum alaşımlı tabla homojen ısı transferi sağlar; döküntülere karşı korunan kontrol paneli güvenli ve pratik kullanım sunar.',
        'features' => [
            ...$aluminumFeatures,
            'İkili ısıtma pozisyonu',
            'Döküntülere karşı korunan kontrol paneli',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Hot Plate',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'RC2',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Tabla malzemesi' => 'Özel koruma ile kaplamalı alüminyum',
            'Isıtma pozisyon sayısı' => 'İkili',
            'Kullanım alanı' => 'İkili hot plate / ısıtıcı tabla',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Tabla malzemesi' => 'Özel koruma ile kaplamalı alüminyum',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı alüminyum',
            'Isıtma pozisyon sayısı' => 'İkili',
            'Ağırlık' => '3.3 kg',
            'Boyutlar (GxYxD)' => '340 x 90 x 246 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '1200 W',
        ],
        'documents' => [],
        'image_alt' => 'VELP RC2 ısıtıcı tabla hotplate ürün görseli',
    ],
    [
        'name' => 'VELP REC Isıtıcı Tabla Hotplate',
        'slug' => 'velp-rec-isitici-tabla-hotplate',
        'image_slugs' => ['velp-rec-isitici-tabla-hotplate', 'velp-rec-hotplate', 'velp-rec-hot-plate'],
        'model' => 'REC',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-rec-isitici-tabla-hotplate',
        'summary' => 'VELP REC ısıtıcı tabla hotplate; dijital masa üstü kullanım, ortam sıcaklığından 550 °C seviyesine ısıtma, seramik tabla ve 50 °C üzerinde sıcak tabla uyarısı ile laboratuvar ısıtma uygulamaları için kullanılır.',
        'body' => 'VELP REC hotplate, seramik tablası ve teknopolimer gövdesiyle yüksek sıcaklık gerektiren laboratuvar ısıtma işlemleri için tasarlanmıştır. Seramik yüzey asit, baz ve çözücülere karşı direnç sağlarken beyaz tabla renk değişimi gözlemini kolaylaştırır.',
        'features' => [
            'Seramik ısıtma tablası ile asit, baz ve çözücülere karşı direnç',
            'Teknopolimer gövde ile kimyasallara, çiziklere ve yüzey aşınmalarına karşı dayanıklılık',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'Sıcaklığın kolay ayarlanabilmesi',
            'IP 42 ile sızıntılara karşı koruma',
            'Yükseltilmiş kontrol paneli ve oluk yuvası ile kullanıcı güvenliği',
            'Kontrol panelinin ısı kaynaklarıyla güvenli mesafede konumlanması',
            'Tahliye oluğu sayesinde sıvı dökülmelerinden kaynaklanabilecek hasarlara karşı koruma',
            'Basınçlı döküm yapı ile iç parçaların sıvı dökülmelerinden korunması',
            'Renk değişimi gözlemi için ideal beyaz seramik tabaka',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Hot Plate',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'REC',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Tabla malzemesi' => 'Seramik',
            'Tabla sıcak uyarısı' => '50 °C üzerinde',
            'Kullanım alanı' => 'Dijital hot plate / ısıtıcı tabla',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Tabla malzemesi' => 'Seramik',
            'Tabla ölçüleri' => '180 x 180 mm',
            'Gövde malzemesi' => 'Teknopolimer',
            'Tabla sıcak uyarısı' => '50 °C üzerinde',
            'Ağırlık' => '1.4 kg',
            'Boyutlar (GxYxD)' => '203 x 94 x 344 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '800 W',
        ],
        'documents' => [],
        'image_alt' => 'VELP REC ısıtıcı tabla hotplate ürün görseli',
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
