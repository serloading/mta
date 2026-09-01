<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Homojenizatör',
    'slug' => 'homojenizator',
    'summary' => 'Numune hazırlama, yüksek hızlı karıştırma ve homojenizasyon uygulamaları için homojenizatör cihazları.',
    'aliases' => [
        'Homojenizatör',
        'Homojenizator',
        'Homogenizer',
        'Yüksek Hızlı Mixer',
    ],
];

$products = [
    [
        'name' => 'VELP OV5 Homojenizatör',
        'slug' => 'velp-ov5-homojenizator',
        'image_slugs' => ['velp-ov5-homojenizator-cihazi', 'velp-ov5-homojenizator'],
        'model' => 'OV5',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-ov5-homojenizator',
        'summary' => 'VELP OV5 homojenizatör; 10000-30000 rpm hız aralığı, 8 litreye kadar homojenizatör hacmi, 40 litreye kadar yüksek hızlı mixer hacmi, 10000 mPa.s maksimum viskozite ve 500 W güç ile numune hazırlama uygulamaları için kullanılır.',
        'body' => 'VELP OV5 homojenizatör, rotor-stator konfigürasyonu ve uygulamaya özel şaft seçenekleriyle laboratuvar numune hazırlama, homojenizasyon ve yüksek hızlı karıştırma işlemleri için geliştirilmiştir. Hızlı aksesuar montajı, aşırı yük koruması, emniyet anahtarı ve PTFE contalı paslanmaz çelik şaft yapısıyla yoğun laboratuvar kullanımını destekler.',
        'features' => [
            'Aksesuarların hızlı şekilde monte edilip sökülebilmesi',
            'Aşırı yükleme koruması',
            'Kolay ve pürüzsüz çalıştırma',
            'Güvenlik için emniyet anahtarı',
            'Çok yönlü homojenizatör kullanımı',
            'Tüm uygulamalar için farklı şaft seçenekleri',
            'Uygulamaya özel rotor ve stator seçimi',
            'Homojenleştirici olarak 8 litreye kadar su hacminde kullanım',
            'Yüksek hızlı karıştırıcı olarak 40 litreye kadar su hacminde kullanım',
            '10000 mPa.s seviyesine kadar akışkanlarla kullanım',
            'Şaftın takma kancası ile kolay ve hızlı bağlanması',
            'Rotor / stator konfigürasyonunun saniyeler içinde monte edilebilmesi',
            'Sağlam tasarım',
            'PTFE contalı paslanmaz çelik şaft',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Homojenizatör',
            'Üst kategori' => 'Karıştırıcılar',
            'Model' => 'OV5',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Karıştırma hızı aralığı' => '10000-30000 rpm',
            'Maksimum karıştırma hacmi' => '8 litre homojenizatör / 40 litre yüksek hızlı mixer',
            'Maksimum viskozite' => '10000 mPa.s',
            'Kullanım alanı' => 'Homojenizasyon ve yüksek hızlı karıştırma',
        ],
        'specs' => [
            'Karıştırma hızı aralığı' => '10000-30000 rpm',
            'Maksimum karıştırma hacmi' => '8 litre homojenizatör / 40 litre yüksek hızlı mixer',
            'Maksimum viskozite' => '10000 mPa.s',
            'Gövde malzemesi' => 'Teknopolimer',
            'Ağırlık' => '1.3 kg',
            'Boyutlar (GxYxD)' => '70 x 255 x 70 mm',
            'Güç' => '500 W',
        ],
        'documents' => [],
        'image_alt' => 'VELP OV5 homojenizatör ürün görseli',
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
