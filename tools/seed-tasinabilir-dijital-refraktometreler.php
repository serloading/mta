<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;
$optionText = fn (array $options): string => implode(' | ', array_map(
    fn (string $catalogNo, string $description): string => $catalogNo . ': ' . $description,
    array_keys($options),
    $options,
));

$category = [
    'name' => 'Taşınabilir Tip Dijital Refraktometreler',
    'slug' => 'tasinabilir-tip-dijital-refraktometreler',
    'summary' => 'Saha, proses ve hızlı kontrol uygulamaları için taşınabilir dijital refraktometre modelleri.',
    'aliases' => ['Taşınabilir Dijital Refraktometreler', 'Dijital El Tipi Refraktometre', 'OPTi Refraktometre', 'OPTi Dijital Refraktometre'],
];

$brand = [
    'name' => 'Bellingham + Stanley',
    'slug' => 'bellingham-stanley',
    'summary' => 'Refraktometre ve polarimetre çözümleri.',
    'logo' => 'images/brands/bellingham-stanley.png',
    'aliases' => ['BELİNGHAMSTARLY', 'Bellingham and Stanley', 'Bellingham + Stanley'],
];

$groups = [
    [
        'name' => 'OPTi Endüstriyel Isı Transferi Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-endustriyel-isi-transferi-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Endüstriyel Isı Transferi',
        'heading' => 'Endüstriyel Isı Transferi',
        'summary' => 'OPTi endüstriyel ısı transferi modelleri, PG/EG ısı transferi ve soğutma sıvısı ölçümleri için taşınabilir dijital refraktometre seçenekleri sunar.',
        'body' => 'OPTi endüstriyel ısı transferi serisi; propilen glikol, etilen glikol, hacim yüzdesi, °C koruma değeri, °Brix ve RI ölçümlerine göre seçilebilen taşınabilir dijital refraktometre modellerinden oluşur.',
        'options' => [
            '38-70' => 'OPTi Heat Transfer PG/C: Isı transferi, % vol 0-60 PG ve 0...-50 °C',
            '38-71' => 'OPTi Heat Transfer EG/C: Isı transferi, % vol 0-60 EG ve 0...-50 °C',
            '38-72' => 'OPTi Heat Transfer %: Isı transferi, % vol 0-60 EG ve % vol 0-60 PG',
            '38-81' => 'OPTi Duo Coolant: Soğutma sıvısı, 0-18 °Brix ve 1.33-1.38 RI',
        ],
    ],
    [
        'name' => 'OPTi Endüstriyel Otomotiv Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-endustriyel-otomotiv-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Endüstriyel Otomotiv',
        'heading' => 'Endüstriyel - Otomotiv',
        'summary' => 'OPTi endüstriyel otomotiv modelleri, antifriz, AdBlue/DEF ve glikol ölçümleri için taşınabilir dijital refraktometre seçenekleri sunar.',
        'body' => 'OPTi endüstriyel otomotiv serisi; EG, PG, DEF/AdBlue ve °Brix ölçümleri için sahada hızlı kontrol yapılmasını sağlayan taşınabilir dijital refraktometre modellerinden oluşur.',
        'options' => [
            '38-61' => 'OPTi Auto A1: 0-54 °Brix ve 0...-50 EG °C',
            '38-62' => 'OPTi Auto A2: 0-54 °Brix DEF ve 0-40 AdBlue',
            '38-63' => 'OPTi Auto A3: 0-54 °Brix ve 0...-50 PG °C',
            '38-65' => 'OPTi Auto A4: 0-40 DEF AdBlue ve 0...-50 EG °C',
            '38-67' => 'OPTi Auto Antifreeze C: Antifriz, 0...-50 EG °C ve 0...-50 PG °C',
        ],
    ],
    [
        'name' => 'OPTi Endüstriyel Genel Kullanım Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-endustriyel-genel-kullanim-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Endüstriyel Genel Kullanım',
        'heading' => 'Endüstriyel - Genel Kullanım',
        'summary' => 'OPTi endüstriyel genel kullanım modelleri; Brix, RI, kimya, klorür, biyodizel ve PG hacim ölçümleri için dijital refraktometre seçenekleri sunar.',
        'body' => 'OPTi endüstriyel genel kullanım serisi; genel amaçlı laboratuvar, proses, kimya, klorür, biyodizel ve PG hacim uygulamaları için seçilebilen taşınabilir dijital refraktometre modellerinden oluşur.',
        'options' => [
            '38-13' => 'OPTi Hi Brix: °Brix ve RI',
            '38-31' => 'OPTi C1: Genel amaçlı, 0-54 °Brix ve 1.33-1.42 RI',
            '38-36' => 'OPTi: Klorür, kalsiyum klorür ve NaCl',
            '38-37' => 'OPTi C2: Kimya, 1.33-1.42 RI ve 5-40 °C',
            '38-38' => 'OPTi C3: Kimya, Brix ve RI',
            '38-39' => 'OPTi Biofuel: Biyodizel, 0-54 °Brix ve 0-20 etanol',
            '38-75' => 'OPTi D1: Bx/PG Vol',
        ],
    ],
    [
        'name' => 'OPTi Yaşam Bilimleri Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-yasam-bilimleri-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Yaşam Bilimleri',
        'heading' => 'Yaşam Bilimleri için',
        'summary' => 'OPTi yaşam bilimleri modelleri; deniz suyu, üre SG, veteriner SG, Brix, tuzluluk ve serum proteini ölçümleri için taşınabilir dijital refraktometre seçenekleri sunar.',
        'body' => 'OPTi yaşam bilimleri serisi; akuatik, spor, veterinerlik, tuzluluk, Brix ve serum proteini uygulamalarında hızlı dijital refraktometre ölçümü için kullanılır.',
        'options' => [
            '38-51' => 'OPTi Duo Aquatic: %0-180 deniz suyu (PPT) ve 1.000-1.070 deniz suyu SG',
            '38-52' => 'OPTi Duo Sport: 1.000-1.050 insan üre SG ve %0-30 şeker °Brix',
            '38-53' => 'OPTi Duo Vet: 1.000-1.050 SG (SM) ve 1.000-1.050 SG (LM)',
            '38-54' => 'OPTi Brix/Saline: Brix ve tuzluluk (Bx/%NaCl)',
            '38-57' => 'OPTi: Serum proteini g/100 ml ve Brix',
        ],
    ],
    [
        'name' => 'OPTi Çift Skalalı Modeller Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-cift-skalali-modeller-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Çift Skalalı Modeller',
        'heading' => 'Çift Skalalı Modeller',
        'summary' => 'OPTi çift skalalı modeller, şarap ve bira uygulamaları için iki ölçüm skalasını birlikte sunan taşınabilir dijital refraktometre seçenekleridir.',
        'body' => 'OPTi çift skalalı modeller; şarap ve bira uygulamalarında °Brix, °AP, Oechsle, Babo, Baume, °Zeiss, Plato ve SG skalalarına göre seçilebilen taşınabilir dijital refraktometrelerdir.',
        'options' => [
            '38-41' => 'OPTi Wine AP: Şarap için, %0-35 °Brix ve 0-22 °AP',
            '38-42' => 'OPTi Wine OE-D: Şarap için, %0-35 °Brix ve 30-130 Oechsle D',
            '38-44' => 'OPTi Wine KMW: Şarap için, %0-35 °Brix ve 0-25 Babo',
            '38-45' => 'OPTi Wine Baume: Şarap için, %0-35 °Brix ve 0-28 Baume',
            '38-46' => 'OPTi Wine Baume/AP: Şarap için, 0-28 Baume ve 0-22 °AP',
            '38-47' => 'OPTi ABV: Şarap için, %0-35 °Brix ve 10-135 °Zeiss',
            '38-48' => 'OPTi Brew: Bira mayası, SG (AB/SG Wort)',
            '38-49' => 'OPTi Brew: Bira, 10-135 °Zeiss ve 0-30 °Plato',
        ],
    ],
    [
        'name' => 'OPTi Tek Skalalı Modeller Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-tek-skalali-modeller-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Tek Skalalı Modeller',
        'heading' => 'Tek Skalalı Modeller',
        'summary' => 'OPTi tek skalalı modeller; Brix, balda su, RI, nişasta, üre, deniz suyu, tuzluluk, AdBlue, sodyum sülfat ve kalsiyum klorür ölçümleri için seçenekler sunar.',
        'body' => 'OPTi tek skalalı modeller; tek bir ölçüm skalasına odaklanan taşınabilir dijital refraktometre seçenekleridir. Şeker, balda su, RI, nişasta, üre, deniz suyu, bira mayası, tuzluluk, AdBlue, sodyum sülfat ve kalsiyum klorür uygulamalarında kullanılır.',
        'options' => [
            '38-02' => 'OPTi: 0-54 şeker (°Brix)',
            '38-05' => 'OPTi Hi: 50-95 °Brix',
            '38-06' => 'OPTi: Balda su tayini için %10-30',
            '38-20' => 'OPTi: 1.33-1.42 RI',
            '38-22' => 'OPTi: Nişasta %0-30',
            '38-23' => 'OPTi: Üre %0-40',
            '38-26' => 'OPTi: Deniz suyu PPT',
            '38-27' => 'OPTi: Bira mayası SG',
            '38-28' => 'OPTi: Tuzluluk %0-28 (NaCl)',
            '38-29' => 'OPTi: AdBlue %0-40',
            '38-30' => 'OPTi: Sodyum sülfat 0-22',
            '38-34' => 'OPTi: 0-85 °Brix',
            '38-35' => 'OPTi: Kalsiyum klorür %0-40',
            '38-A1' => 'OPTi: 0-95 şeker (°Brix)',
        ],
    ],
    [
        'name' => 'OPTi Bira-Şarap Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-bira-sarap-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Bira-Şarap',
        'heading' => 'Bira-Şarap',
        'summary' => 'OPTi bira-şarap grubu, bira ve şarap uygulamaları için taşınabilir dijital refraktometre ürün grubudur.',
        'body' => 'OPTi bira-şarap grubu için ürün içeriği yayın öncesi netleştirilecektir. Görsel placeholder olarak bırakılmıştır.',
        'options' => [],
    ],
    [
        'name' => 'OPTi Yaşam Bilimleri-Veterinerlik Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-yasam-bilimleri-veterinerlik-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Yaşam Bilimleri-Veterinerlik',
        'heading' => 'Yaşam Bilimleri-Veterinerlik',
        'summary' => 'OPTi yaşam bilimleri-veterinerlik grubu, veteriner ve yaşam bilimleri uygulamaları için taşınabilir dijital refraktometre ürün grubudur.',
        'body' => 'OPTi yaşam bilimleri-veterinerlik grubu için ürün içeriği yayın öncesi netleştirilecektir. Görsel placeholder olarak bırakılmıştır.',
        'options' => [],
    ],
    [
        'name' => 'OPTi Gıda-İçecek Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-gida-icecek-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Gıda-İçecek',
        'heading' => 'Gıda-İçecek',
        'summary' => 'OPTi gıda-içecek grubu, gıda ve içecek kalite kontrol uygulamaları için taşınabilir dijital refraktometre ürün grubudur.',
        'body' => 'OPTi gıda-içecek grubu için ürün içeriği yayın öncesi netleştirilecektir. Görsel placeholder olarak bırakılmıştır.',
        'options' => [],
    ],
    [
        'name' => 'OPTi Endüstriyel Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-endustriyel-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Endüstriyel',
        'heading' => 'Endüstriyel',
        'summary' => 'OPTi endüstriyel grubu, genel endüstriyel uygulamalar için taşınabilir dijital refraktometre ürün grubudur.',
        'body' => 'OPTi endüstriyel grubu için ürün içeriği yayın öncesi netleştirilecektir. Görsel placeholder olarak bırakılmıştır.',
        'options' => [],
    ],
    [
        'name' => 'OPTi Otomotiv Taşınabilir Dijital Refraktometreler',
        'slug' => 'opti-otomotiv-tasinabilir-dijital-refraktometre',
        'model' => 'OPTi Otomotiv',
        'heading' => 'Otomotiv',
        'summary' => 'OPTi otomotiv grubu, otomotiv sıvıları için taşınabilir dijital refraktometre ürün grubudur.',
        'body' => 'OPTi otomotiv grubu için ürün içeriği yayın öncesi netleştirilecektir. Görsel placeholder olarak bırakılmıştır.',
        'options' => [],
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

foreach ($groups as $index => $product) {
    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();
    $hasOptions = count($product['options']) > 0;

    $features = [
        'Taşınabilir tip dijital refraktometre ürün grubu',
        $product['heading'] . ' uygulamaları',
    ];

    if ($hasOptions) {
        $features[] = count($product['options']) . ' katalog seçeneği';
    } else {
        $features[] = 'İçerik yayın öncesi netleştirilecek';
    }

    $specs = [
        'Ürün tipi' => 'Taşınabilir tip dijital refraktometre',
        'Seri' => 'OPTi',
        'Grup' => $product['heading'],
        'Katalog seçenekleri' => $hasOptions ? $optionText($product['options']) : 'Yayın öncesi netleştirilecek',
        'Görsel durumu' => 'Placeholder',
    ];

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
        'features' => $json($features),
        'metadata' => $json([
            'Marka' => 'Bellingham + Stanley',
            'Kategori' => 'Taşınabilir Tip Dijital Refraktometreler',
            'Üst kategori' => 'Refraktometre',
            'Model' => $product['model'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Taşınabilir tip dijital refraktometre',
        ]),
        'specs' => $json($specs),
        'sort_order' => ($index + 1) * 10,
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
}

echo "Seeded " . count($groups) . " portable digital refractometer product groups.\n";
