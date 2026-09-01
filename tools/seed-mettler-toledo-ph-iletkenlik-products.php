<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$category = [
    'name' => 'pH İletkenlik & Metreler',
    'slug' => 'ph-iletkenlik',
    'summary' => 'pH metre, iletkenlik ve çok parametreli ölçüm cihazları.',
    'aliases' => ['pH&İletkenlik', 'pH-İletkenlik Ölçerler', 'pH ve İletkenlik', 'pH Metre'],
];

$brand = [
    'name' => 'Mettler Toledo',
    'slug' => 'mettler-toledo',
    'summary' => 'pH metre, iletkenlik, titrasyon, yoğunluk ve laboratuvar ölçüm cihazları.',
    'logo' => 'images/brands/mettler-toledo.png',
    'aliases' => ['Mettler Toledo', 'METTLER TOLEDO', 'Mettler Toledo Türkiye'],
];

$importedProducts = [];
$importPath = $root . '/storage/app/imports/mta-products-normalized.json';
if (is_file($importPath)) {
    foreach (json_decode(file_get_contents($importPath), true) ?: [] as $importedProduct) {
        if (! empty($importedProduct['slug'])) {
            $importedProducts[$importedProduct['slug']] = $importedProduct;
        }
    }
}

$importedSpecs = fn (string $slug): array => $importedProducts[$slug]['specs'] ?? [];

$sd23StandardSpecs = $importedSpecs('mettler-toledo-sd23-standart-kit-sevendirect');

$products = [
    [
        'name' => 'Mettler Toledo SD20 Kit-SevenDirect',
        'slug' => 'mettler-toledo-sd20-kit-sevendirect',
        'model' => 'SD20-Kit',
        'sku' => '30671554',
        'summary' => 'Mettler Toledo SD20 Kit-SevenDirect; pH ve ORP ölçümü için masa tipi pH-ORP metredir.',
        'body' => 'Mettler Toledo SD20 Kit-SevenDirect masa tipi pH-ORP metre; pH ve ORP ölçümleri için kullanılır. Set içeriğinde SD20 pH metre, elektrot tutucu, koruma kılıfı, kullanım dokümanları, CE belgesi ve test sertifikaları, pH tampon çözeltileri, pH ölçüm teori kitabı ve InLab Expert Pro-ISM pH elektrodu bulunur.',
        'features' => [
            'Masa tipi pH-ORP metre',
            'pH ve ORP ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            '0.001 / 0.01 / 0.1 pH çözünürlük',
            '-20.000-20.000 mV ölçüm aralığı',
            '1000 ölçüm veri depolama',
            'RS232, USB-A ve USB-B bağlantı arayüzleri',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD20-Kit',
            'SKU' => '30671554',
            'Ürün tipi' => 'Masa tipi pH-ORP metre',
        ],
        'specs' => array_replace($importedSpecs('mettler-toledo-sd20-kit-sevendirect'), [
            'Ürün ismi' => 'SD20-Kit SevenDirect',
            'Ürün kodu' => '30671554',
            'Set içeriği' => 'SD20 pH metre, elektrot tutucu, koruma kılıfı, kullanma kılavuzu, kullanma talimatı, CE belgesi ve test sertifikaları, 2 x pH 4.01, 7.00, 9.21, 10.00 tampon çözelti, pH ölçüm teori kitabı',
            'Set elektrotları' => 'InLab Expert Pro-ISM pH elektrodu (30014096)',
            'Cihaz tipi' => 'Masa tipi pH metre',
            'Ölçüm parametreleri' => 'pH; ORP',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN-2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2019/03/SD20-K-SARTNAME.docx'],
        ],
    ],
    [
        'name' => 'Mettler Toledo SD20 HA Kit SevenDirect',
        'slug' => 'mettler-toledo-sd20-ha-kit-sevendirect',
        'model' => 'SD20 HA Kit',
        'sku' => '30671556',
        'summary' => 'Mettler Toledo SD20 HA Kit SevenDirect; pH ve ORP ölçümü için masa tipi pH-ORP metredir.',
        'body' => 'Mettler Toledo SD20 HA Kit SevenDirect masa tipi pH-ORP metre; pH ve ORP ölçümleri için kullanılır. Set içeriğinde SD20 pH metre, elektrot tutucu, koruma kılıfı, kullanım dokümanları, CE belgesi ve test sertifikaları, pH tampon çözeltileri, pH ölçüm teori kitabı ve InLab Routine Pro-ISM pH elektrodu bulunur.',
        'features' => [
            'Masa tipi pH-ORP metre',
            'pH ve ORP ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            '0.001 / 0.01 / 0.1 pH çözünürlük',
            '-20.000-20.000 mV ölçüm aralığı',
            '1000 ölçüm veri depolama',
            'InLab Routine Pro-ISM pH elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD20 HA Kit',
            'SKU' => '30671556',
            'Ürün tipi' => 'Masa tipi pH-ORP metre',
        ],
        'specs' => array_replace($importedSpecs('mettler-toledo-sd20-kit-sevendirect'), [
            'Ürün ismi' => 'SD20 HA Kit SevenDirect',
            'Ürün kodu' => '30671556',
            'Set içeriği' => 'SD20 pH metre, elektrot tutucu, koruma kılıfı, kullanma kılavuzu, kullanma talimatı, CE belgesi ve test sertifikaları, 2 x pH 4.01, 7.00, 9.21, 10.00 tampon çözelti, pH ölçüm teori kitabı',
            'Set elektrotları' => 'InLab Routine Pro-ISM pH elektrodu (51344055) ve kablosu',
            'Cihaz tipi' => 'Masa tipi pH metre',
            'Ölçüm parametreleri' => 'pH; ORP',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN-2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SD20-HA-K-SARTNAME.docx'],
        ],
    ],
    [
        'name' => 'Mettler Toledo SD50 HA Kit SevenDirect',
        'slug' => 'mettler-toledo-sd50-ha-kit-sevendirect',
        'model' => 'SD50 HA Kit',
        'sku' => '30671546',
        'summary' => 'Mettler Toledo SD50 HA Kit SevenDirect; pH, ORP ve iyon ölçümü için masa tipi pH/iyon metredir.',
        'body' => 'Mettler Toledo SD50 HA Kit SevenDirect masa tipi pH/iyon metre; pH, ORP ve iyon konsantrasyonu ölçümleri için kullanılır. Set içeriğinde SD50 pH metre, elektrot tutucu, koruma kılıfı, kullanım dokümanları, CE belgesi ve test sertifikaları, pH tampon çözeltileri, pH ölçüm teori kitabı ve InLab Routine Pro-ISM pH elektrodu bulunur.',
        'features' => [
            'Masa tipi pH/iyon metre',
            'pH, ORP ve iyon ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            'İyon konsantrasyonu ölçümü',
            '-20.000-20.000 mV ölçüm aralığı',
            '1000 ölçüm veri depolama',
            'InLab Routine Pro-ISM pH elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD50 HA Kit',
            'SKU' => '30671546',
            'Ürün tipi' => 'Masa tipi pH-ORP-iyon metre',
        ],
        'specs' => array_replace($importedSpecs('mettler-toledo-sd50-kit-sevendirect'), [
            'Ürün ismi' => 'SD50 HA Kit SevenDirect',
            'Ürün kodu' => '30671546',
            'Set içeriği' => 'SD50 pH metre, elektrot tutucu, koruma kılıfı, kullanma kılavuzu, kullanma talimatı, CE belgesi ve test sertifikaları, 2 x pH 4.01, 7.00, 9.21, 10.00 tampon çözelti, pH ölçüm teori kitabı',
            'Set elektrotları' => 'InLab Routine Pro-ISM pH elektrodu (51344055) ve kablosu',
            'Cihaz tipi' => 'Masa tipi pH/iyon metre',
            'Ölçüm parametreleri' => 'pH; ORP; İyon (Ion)',
            'İyon konsantrasyonu' => '0.000-1000.0%; 0000-10000.0 ppm; 1.00E-9-9.99E+9 mg/L | mmol/L | mol/L; -2.000-20.000 px',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN-2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2019/03/SD50-HA-K-SARTNAMESI.docx'],
        ],
    ],
    [
        'name' => 'Mettler Toledo SevenDirect SD50 Kit',
        'slug' => 'mettler-toledo-sd50-kit-sevendirect',
        'model' => 'SD50-Kit',
        'sku' => '30671544',
        'summary' => 'Mettler Toledo SevenDirect SD50 Kit; pH, ORP ve iyon ölçümü için masa tipi pH/iyon metredir.',
        'body' => 'Mettler Toledo SevenDirect SD50 Kit masa tipi pH/iyon metre; pH, ORP ve iyon konsantrasyonu ölçümleri için kullanılır. Set içeriğinde SD50 pH metre, elektrot tutucu, koruma kılıfı, kullanım dokümanları, CE belgesi ve test sertifikaları, pH tampon çözeltileri, pH ölçüm teori kitabı ve InLab Expert Pro-ISM pH elektrodu bulunur.',
        'features' => [
            'Masa tipi pH/iyon metre',
            'pH, ORP ve iyon ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            'İyon konsantrasyonu ölçümü',
            '-20.000-20.000 mV ölçüm aralığı',
            '1000 ölçüm veri depolama',
            'InLab Expert Pro-ISM pH elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD50-Kit',
            'SKU' => '30671544',
            'Ürün tipi' => 'Masa tipi pH-ORP-iyon metre',
        ],
        'specs' => array_replace($importedSpecs('mettler-toledo-sd50-kit-sevendirect'), [
            'Ürün ismi' => 'SD 50 KİT',
            'Ürün kodu' => '30671544',
            'Set içeriği' => 'SD50 pH metre, elektrot tutucu, koruma kılıfı, kullanma kılavuzu, kullanma talimatı, CE belgesi ve test sertifikaları, 2 x pH 4.01, 7.00, 9.21, 10.00 tampon çözelti, pH ölçüm teori kitabı',
            'Set elektrotları' => 'InLab Expert Pro-ISM pH elektrodu (30014096)',
            'Cihaz tipi' => 'Masa tipi pH/iyon metre',
            'Ölçüm parametreleri' => 'pH; ORP; İyon (Ion)',
            'İyon konsantrasyonu' => '0.000-1000.0%; 0000-10000.0 ppm; 1.00E-9-9.99E+9 mg/L | mmol/L | mol/L; -2.000-20.000 px',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN-2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2019/03/SD50-K-SARTNAMESI.docx'],
        ],
    ],
    [
        'name' => 'Mettler Toledo SD23-Standart Kit SevenDirect',
        'slug' => 'mettler-toledo-sd23-standart-kit-sevendirect',
        'model' => 'SD23-K',
        'sku' => '30671567',
        'summary' => 'Mettler Toledo SD23 Standart Kit SevenDirect; pH, ORP, iletkenlik, TDS, tuzluluk ve dirençlilik ölçümü için masa tipi multiparametre cihazıdır.',
        'body' => 'Mettler Toledo SD23-Standart Kit SevenDirect masa tipi multiparametre cihazı; pH, ORP, iletkenlik, TDS, tuzluluk, dirençlilik ve iletkenlik külü ölçümleri için kullanılır. Kit içeriğinde SD23 multiparametre cihazı, elektrot tutucu, koruma kılıfı, kullanım dokümanları, tampon ve iletkenlik kalibrasyon çözeltileri, InLab Expert Pro-ISM pH elektrodu ve InLab 731-ISM iletkenlik elektrodu bulunur.',
        'features' => [
            'Masa tipi multiparametre cihazı',
            'pH, ORP, iletkenlik, TDS, tuzluluk ve dirençlilik ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            '0.001 µS/cm-1000 mS/cm EC ölçüm aralığı',
            '2000 ölçüm veri depolama',
            'TFT dokunmatik renkli ekran',
            'RS232, USB-A ve USB-B bağlantı arayüzleri',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD23-K',
            'SKU' => '30671567',
            'Ürün tipi' => 'Masa tipi multiparametre cihazı',
        ],
        'specs' => array_replace($sd23StandardSpecs, [
            'Ürün ismi' => 'SD23-K (Kit)',
            'Ürün kodu' => '30671567',
            'Cihaz tipi' => 'Masa tipi multiparametre cihazı',
            'Set elektrotları' => 'InLab Expert Pro-ISM pH elektrodu (30014096) ve InLab 731-ISM iletkenlik elektrodu (30014092)',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN-2.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SD23-K-SARTNAME-1.docx'],
        ],
    ],
    [
        'name' => 'Mettler Toledo SD30-Kit SevenDirect',
        'slug' => 'mettler-toledo-sd30-kit-sevendirect',
        'model' => 'SD30-Kit',
        'sku' => '30671562',
        'summary' => 'Mettler Toledo SD30-Kit SevenDirect; iletkenlik, TDS, tuzluluk, dirençlilik ve iletkenlik külü ölçümü için masa tipi iletkenlik ölçerdir.',
        'body' => 'Mettler Toledo SD30-Kit SevenDirect masa tipi iletkenlik ölçer; iletkenlik, TDS, tuzluluk, dirençlilik ve iletkenlik külü ölçümleri için kullanılır. Kit içeriğinde SD30 iletkenlik ölçer, elektrot tutucu, koruma kılıfı, kullanım dokümanları, kalibrasyon çözeltileri ve InLab 731-ISM iletkenlik elektrodu yer alır.',
        'features' => [
            'Masa tipi iletkenlik ölçer',
            'İletkenlik, TDS, tuzluluk, dirençlilik ve iletkenlik külü ölçümü',
            '0.001 µS/cm-1000 mS/cm EC ölçüm aralığı',
            '-5.0-130.0 °C sıcaklık kompanzasyonu aralığı',
            '1000 ölçüm veri depolama',
            'USB bellek ve EasyDirect pH yazılımı ile veri dışa aktarma',
            'RS232, USB-A ve USB-B bağlantı arayüzleri',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD30-Kit',
            'SKU' => '30671562',
            'Ürün tipi' => 'Masa tipi iletkenlik ölçer',
        ],
        'specs' => array_replace($importedSpecs('mettler-toledo-sd30-kit-sevendirect'), [
            'Ürün ismi' => 'SD30-Kit',
            'Ürün kodu' => '30671562',
            'Cihaz tipi' => 'Masa tipi iletkenlik ölçer',
            'Set elektrotları' => 'InLab 731-ISM iletkenlik elektrodu (30014092)',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN-1.pdf'],
        ],
    ],
    [
        'name' => 'Mettler Toledo SD23 Pure H2O-Kit SevenDirect',
        'slug' => 'mettler-toledo-sd23-pure-h2o-kit-sevendirect',
        'model' => 'SD23-Water Kit',
        'sku' => '30671568',
        'summary' => 'Mettler Toledo SD23 Pure H2O-Kit SevenDirect; saf su uygulamaları için pH ve iletkenlik ölçümü yapan masa tipi multiparametre cihazıdır.',
        'body' => 'Mettler Toledo SD23 Pure H2O-Kit SevenDirect masa tipi pH-iletkenlik ölçer; pH, ORP, iletkenlik, TDS, tuzluluk, dirençlilik ve iletkenlik külü ölçümleri için kullanılır. Pure H2O kit içeriğinde SD23 multiparametre cihazı, elektrot tutucu, koruma kılıfı, kullanım dokümanları, tampon ve iletkenlik kalibrasyon çözeltileri, InLab Pure Pro-ISM pH elektrodu ve InLab 741-ISM iletkenlik elektrodu bulunur.',
        'features' => [
            'Masa tipi pH-iletkenlik ölçer',
            'Saf su uygulamalarına uygun kit içeriği',
            'pH, ORP, iletkenlik, TDS, tuzluluk ve dirençlilik ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            '0.001 µS/cm-1000 mS/cm EC ölçüm aralığı',
            '2000 ölçüm veri depolama',
            'InLab Pure Pro-ISM ve InLab 741-ISM elektrotları',
        ],
        'metadata' => [
            'Marka' => 'Mettler Toledo',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'SD23-Water Kit',
            'SKU' => '30671568',
            'Ürün tipi' => 'Masa tipi pH-iletkenlik ölçer',
        ],
        'specs' => array_replace($sd23StandardSpecs, [
            'Ürün ismi' => 'SD23-Water Kit',
            'Ürün kodu' => '30671568',
            'Set içeriği' => 'SD23 multiparametre, elektrot tutucu, koruma kılıfı, kullanma kılavuzu, kullanma talimatı, CE belgesi ve test sertifikaları, 2 x pH 4.01, 7.00, 9.21, 10.00 tampon çözelti, 2 x 1413 µS/cm, 12.88 mS/cm kalibrasyon çözeltisi, pH-iletkenlik ölçüm teori kitabı',
            'Set elektrotları' => 'InLab Pure Pro-ISM pH elektrodu (51344172) ve InLab 741-ISM iletkenlik elektrodu (30014094)',
            'Cihaz tipi' => 'Masa tipi multiparametre cihazı',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SevenDirect-pH-Datasheet_EN.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2019/05/SD23-Pure-H2O-K-SARTNAME.docx'],
        ],
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
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, :path, :url, :sort_order, :created_at, :updated_at)');

foreach ($products as $index => $product) {
    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => $product['sku'],
        'old_url' => null,
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['slug']),
        'image_alt' => $product['name'] . ' ürün görseli',
        'features' => $json($product['features']),
        'metadata' => $json($product['metadata']),
        'specs' => $json($product['specs']),
        'sort_order' => 2200 + (($index + 1) * 10),
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

    foreach ($product['documents'] as $documentIndex => $document) {
        $insertDocument->execute([
            'product_id' => $productId,
            'title' => $document['title'],
            'type' => $document['type'],
            'path' => null,
            'url' => $document['url'],
            'sort_order' => ($documentIndex + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

$db->commit();

echo "Seeded " . count($products) . " Mettler Toledo pH and conductivity products.\n";
