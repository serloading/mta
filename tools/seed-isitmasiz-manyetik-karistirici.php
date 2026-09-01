<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Isıtmasız Manyetik Karıştırıcılar',
    'slug' => 'isitmasiz-manyetik-karistirici',
    'summary' => 'Isıtma gerektirmeyen rutin karıştırma işlemleri için kullanılan manyetik karıştırıcı modelleri.',
    'aliases' => [
        'Isıtmasız Manyetik Karıştırıcı',
        'Isitmasiz Manyetik Karistirici',
        'Manyetik Karıştırıcı',
        'Magnetic Stirrer',
    ],
];

$commonFeatures = [
    'Sürekli günlerce kullanıldıktan sonra bile soğuk kalır',
    'Düşük hızlarda dahi elektronik hız kontrolü',
    'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
    'Uzun ömürlü çalışma',
    'Kontrol paneli sıvı dökülmelerine karşı korunmuştur',
    'Paslanmaz çelik kapak',
    'Parçacıkların ve renk değişimlerinin görülebilmesi için renksiz yüzey',
    'Mikrotitrasyon, mikrobiyoloji ve biyokimya uygulamaları için ideal',
    'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
    'Sirkülasyonlu su banyosu ile kullanıldığında termostat görevi görebilme',
    'Ergonomik, ince ve hafif tasarım',
];

$makeProduct = function (
    string $model,
    int $positions,
    string $centerDistance,
    string $weight,
    string $power,
    string $positionDescription,
    string $catalogUrl
) use ($commonFeatures): array {
    $modelSlug = strtolower(str_replace(' ', '-', $model));
    $slug = "velp-{$modelSlug}-isitmasiz-manyetik-karistirici";

    return [
        'name' => "VELP {$model} Isıtmasız Manyetik Karıştırıcı",
        'slug' => $slug,
        'image_slugs' => [$slug, "{$modelSlug}-isitmasiz-manyetik-karistirici"],
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} ısıtmasız manyetik karıştırıcı; çoklu analog masa üstü kullanım, {$positions} karıştırma pozisyonu, 5 litre H2O karıştırma hacmi ve 80-1500 rpm hız aralığıyla mikrotitrasyon, mikrobiyoloji ve biyokimya uygulamaları için kullanılır.",
        'body' => "VELP {$model}, birden fazla numunenin aynı anda karıştırılması için tasarlanmış çoklu analog ısıtmasız manyetik karıştırıcıdır. Paslanmaz çelik tabla, epoksi kaplı sert çelik gövde ve dökülmeye karşı korunan kontrol paneliyle sürekli çalışmaya uygun bir laboratuvar cihazı olarak konumlanır.",
        'features' => [
            ...$commonFeatures,
            "{$model}: {$positionDescription}",
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmasız Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Çoklu analog, masa üstü',
            'Karıştırma hacmi' => '5 litre H2O',
            'Karıştırma hızı' => '80-1500 rpm',
            'Pozisyon sayısı' => (string) $positions,
            'Kullanım alanı' => 'Isıtmasız çoklu manyetik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Çoklu analog, masa üstü',
            'Karıştırma hacmi' => '5 litre H2O',
            'Karıştırma hızı' => '80-1500 rpm',
            'Tabla malzemesi' => 'Paslanmaz çelik',
            'Gövde malzemesi' => 'Epoksi kaplı sert çelik gövde',
            'Karıştırma pozisyon sayısı' => (string) $positions,
            'Karıştırma pozisyon merkezleri arası mesafe' => $centerDistance,
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => '230 x 51.5 x 370 mm',
            'Güç' => $power,
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => $catalogUrl,
                'path' => null,
            ],
        ],
        'image_alt' => "VELP {$model} ısıtmasız manyetik karıştırıcı ürün görseli",
    ];
};

$makeDigitalMultistirrer = function (
    string $model,
    int $positions,
    string $volume,
    string $centerDistance,
    string $weight,
    string $power,
    string $positionDescription,
    string $catalogUrl
): array {
    $modelSlug = strtolower(str_replace(' ', '-', $model));
    $slug = "velp-{$modelSlug}-isitmasiz-manyetik-karistirici";

    return [
        'name' => "VELP {$model} Isıtmasız Manyetik Karıştırıcı",
        'slug' => $slug,
        'image_slugs' => [$slug, "{$modelSlug}-isitmasiz-manyetik-karistirici"],
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} ısıtmasız manyetik karıştırıcı; çoklu dijital masa üstü kullanım, {$positions} karıştırma pozisyonu, {$volume} karıştırma hacmi, 80-1500 rpm hız aralığı ve otomatik ters yönde çalışma özelliğiyle kullanılır.",
        'body' => "VELP {$model}, çoklu numune karıştırma uygulamaları için dijital hız kontrolü, zamanlayıcı ve otomatik ters yönde çalışma fonksiyonlarını bir araya getirir. Paslanmaz çelik üst plaka, kompakt gövde ve senkronize karıştırma yapısı mikrotitrasyon, mikrobiyoloji ve biyokimya uygulamalarını destekler.",
        'features' => [
            'Düşük hızlarda dahi elektronik hız kontrolü',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Uzun ömürlü çalışma',
            'Birkaç günlük sürekli çalışmanın ardından bile soğuk kalır',
            '5-900 saniye arası ayarlanabilir otomatik ters yönde karıştırma',
            'Yüksek kimyasal direnç sağlayan ve kolay temizlenen paslanmaz çelik üst plaka',
            "{$model}: {$positionDescription}",
            'Viskozite değiştiğinde bile sabit hız sağlayan SpeedServo teknolojisi',
            'Mikrotitrasyon, mikrobiyoloji ve biyokimya için ideal',
            'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
            'Sirkülasyonlu su banyosu ile kullanıldığında termostat görevi görebilme',
            'Ergonomik, ince ve hafif tasarım',
            'Doğru hız denetimi için parlak dijital ekran',
            'Senkronize karıştırma ile aynı hızda çalışma',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmasız Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Çoklu dijital, masa üstü',
            'Karıştırma hacmi' => $volume,
            'Karıştırma hızı' => '80-1500 rpm, 10 rpm adımlarla',
            'Pozisyon sayısı' => (string) $positions,
            'Zamanlayıcı' => '1-900 dakika veya süresiz çalışma',
            'Kullanım alanı' => 'Dijital çoklu manyetik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Çoklu dijital, masa üstü',
            'Karıştırma hızı' => '80-1500 rpm, 10 rpm adımlarla',
            'Tabla malzemesi' => 'Paslanmaz çelik',
            'Gövde malzemesi' => 'Epoksi kaplı sert çelik gövde',
            'Karıştırma pozisyon sayısı' => (string) $positions,
            'Zamanlayıcı süresi' => '1-900 dakikaya kadar veya süresiz çalışma',
            'Otomatik ters yönde çalışma' => '5-900 saniye arası ayarlanabilir',
            'Karıştırma hacmi' => $volume,
            'Karıştırma pozisyon merkezleri arası mesafe' => $centerDistance,
            'Ağırlık' => $weight,
            'Boyutlar (GxYxD)' => '230 x 51.5 x 370 mm',
            'Güç' => $power,
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => $catalogUrl,
                'path' => null,
            ],
        ],
        'image_alt' => "VELP {$model} ısıtmasız manyetik karıştırıcı ürün görseli",
    ];
};

$makeSimpleProduct = function (
    string $model,
    string $slug,
    array $imageSlugs,
    string $deviceType,
    string $volume,
    string $speed,
    ?string $plateMaterial,
    string $bodyMaterial,
    ?string $positions,
    ?string $centerDistance,
    string $weight,
    string $dimensions,
    ?string $power,
    string $summaryUse,
    array $features,
    string $catalogUrl
): array {
    $specs = [
        'Cihaz tipi' => $deviceType,
        'Karıştırma hacmi' => $volume,
        'Karıştırma hızı' => $speed,
    ];

    if ($plateMaterial !== null) {
        $specs['Tabla malzemesi'] = $plateMaterial;
    }

    $specs['Gövde malzemesi'] = $bodyMaterial;

    if ($positions !== null) {
        $specs['Karıştırma pozisyon sayısı'] = $positions;
    }

    if ($centerDistance !== null) {
        $specs['Karıştırma pozisyon merkezleri arası mesafe'] = $centerDistance;
    }

    $specs['Ağırlık'] = $weight;
    $specs['Boyutlar (GxYxD)'] = $dimensions;

    if ($power !== null) {
        $specs['Güç'] = $power;
    }

    return [
        'name' => "VELP {$model} Isıtmasız Manyetik Karıştırıcı",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "VELP {$model} ısıtmasız manyetik karıştırıcı; {$deviceType} kullanım, {$volume} karıştırma hacmi ve {$speed} karıştırma hızıyla {$summaryUse} için kullanılır.",
        'body' => "VELP {$model}, ısıtma gerektirmeyen manyetik karıştırma uygulamaları için tasarlanmış masa üstü laboratuvar cihazıdır. Kompakt yapı, düşük hızlarda kararlı kontrol ve uzun süreli çalışma özellikleri rutin numune hazırlama süreçlerini destekler.",
        'features' => $features,
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmasız Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => $deviceType,
            'Karıştırma hacmi' => $volume,
            'Karıştırma hızı' => $speed,
            'Kullanım alanı' => 'Isıtmasız manyetik karıştırma',
        ],
        'specs' => $specs,
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => $catalogUrl,
                'path' => null,
            ],
        ],
        'image_alt' => "VELP {$model} ısıtmasız manyetik karıştırıcı ürün görseli",
    ];
};

$products = [
    $makeProduct(
        'MULTISTIRRER 6',
        6,
        '100 mm',
        '1.75 kg',
        '3.6 W',
        '6 yer, maksimum Ø 85 mm - 400 ml karıştırma hacmi',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172429_2608velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeProduct(
        'MULTISTIRRER 15',
        15,
        '74 mm',
        '2.1 kg',
        '9 W',
        '15 yer, maksimum Ø 64 mm - 250 ml karıştırma hacmi',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172457_2500velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeDigitalMultistirrer(
        'MULTISTIRRER 6 Digital',
        6,
        '6 x 400 ml',
        '100 mm',
        '1.75 kg',
        '3.6 W',
        '6 yer, maksimum Ø 85 mm - 400 ml karıştırma hacmi',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172512_2924velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeDigitalMultistirrer(
        'MULTISTIRRER 15 Digital',
        15,
        '15 x 250 ml',
        '74 mm',
        '2.1 kg',
        '9 W',
        '15 yer, maksimum Ø 64 mm - 250 ml karıştırma hacmi',
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172526_2568velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'AMI',
        'velp-ami-isitmasiz-manyetik-karistirici',
        ['velp-ami-isitmasiz-manyetik-karistirici', 'ami-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '1 x 5 litre',
        '1100 rpm',
        'Paslanmaz çelik',
        'Epoksi kaplı sert çelik gövde',
        'Tekli',
        '150 mm',
        '1.2 kg',
        '150 x 55 x 270 mm',
        '1.2 W',
        'mikrotitrasyon ve ince renk değişimi gözlemi',
        [
            'Kontrol paneli sıvı dökülmelerine karşı korunmuştur',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Uzun ömürlü çalışma',
            'Parçacıkları ve renk değişikliklerini görebilmek için ışıklandırılmış yüzey',
            'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
            'Ergonomik, ince ve hafif tasarım',
            'Mikrotitrasyon ve ince renk değişiklikleri için ideal',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172542_2944velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'AMI 4',
        'velp-ami-4-isitmasiz-manyetik-karistirici',
        ['velp-ami-4-isitmasiz-manyetik-karistirici', 'ami-4-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '4 x 5 litre',
        '1100 rpm',
        'Paslanmaz çelik',
        'Epoksi kaplı sert çelik gövde',
        'Dörtlü',
        '150 mm',
        '4 kg',
        '600 x 55 x 270 mm',
        '4.8 W',
        'dörtlü mikrotitrasyon ve renk değişimi gözlemi',
        [
            'Kontrol paneli sıvı dökülmelerine karşı korunmuştur',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Uzun ömürlü çalışma',
            '4 konumlu modelde bağımsız hız regülasyonu',
            'Parçacıkları ve renk değişikliklerini görebilmek için ışıklandırılmış yüzey',
            'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
            'Ergonomik, ince ve hafif tasarım',
            'Mikrotitrasyon ve ince renk değişiklikleri için ideal',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172557_2815velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'MST',
        'velp-mst-isitmasiz-manyetik-karistirici',
        ['velp-mst-isitmasiz-manyetik-karistirici', 'mst-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '5 litre H2O',
        '1100 rpm',
        null,
        'Teknopolimer',
        null,
        null,
        '0.4 kg',
        '120 x 50 x 145 mm',
        null,
        'mikrotitrasyon, BOD, mikrobiyoloji ve biyokimya uygulamaları',
        [
            'Kimyasallara karşı dayanıklılık',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Uzun ömürlü çalışma',
            'Güvenilir uygulamalar',
            'Sürekli günlerce kullanıldıktan sonra bile soğuk kalır',
            'Mikrotitrasyon, BOD, mikrobiyoloji ve biyokimya için ideal',
            'Kompakt tasarım ile sınırlı tezgah alanlarında yer tasarrufu',
            'Beyaz yüzey ile parçacık ve renk değişimi gözlemini kolaylaştırma',
            'Saatlerce sürekli kullanımdan sonra bile aşırı ısınmama',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717171814_2577velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'MST Digital',
        'velp-mst-digital-isitmasiz-manyetik-karistirici',
        ['velp-mst-digital-isitmasiz-manyetik-karistirici', 'mst-digital-isitmasiz-manyetik-karistirici'],
        'Dijital, masa üstü',
        '5 litre H2O',
        '1500 rpm, 10 rpm adımlarla',
        null,
        'Teknopolimer',
        null,
        null,
        '0.6 kg',
        '120 x 50 x 145 mm',
        null,
        'BOD, mikrobiyoloji, biyokimya ve uzun süreli karıştırma uygulamaları',
        [
            'Teknopolimer yapısı ile kimyasallara karşı yüksek direnç',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'Doğru hız denetimi için parlak dijital ekran',
            '5-900 saniye arası ayarlanabilir otomatik ters yönde karıştırma',
            'Sürekli kullanımdan sonra bile soğuk kalır ve aşırı ısınmaz',
            'Viskozite değiştiğinde bile sabit hız sağlayan SpeedServo teknolojisi',
            'Kompakt tasarım ile sınırlı tezgah alanlarında yer tasarrufu',
            'Beyaz yüzey ile parçacık ve renk değişimi gözlemini kolaylaştırma',
            'Sağlam yapı ve kompakt tasarım',
            'Fırçasız motor',
            'Mikrotitrasyon, BOD, mikrobiyoloji ve biyokimya için ideal',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717171841_2906velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'MICROSTIRRER',
        'velp-microstirrer-isitmasiz-manyetik-karistirici',
        ['velp-microstirrer-isitmasiz-manyetik-karistirici', 'microstirrer-isitmasiz-manyetik-karistirici', 'micrositter-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '5 litre H2O',
        '1100 rpm',
        null,
        'Epoksi kaplı sert çelik',
        null,
        null,
        '0.55 kg',
        '120 x 48 x 128 mm',
        '0.6 W',
        'mikrotitrasyon odaklı kompakt karıştırma uygulamaları',
        [
            'Kontrol paneli sıvı dökülmelerine karşı korunmuştur',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'Sürekli günlerce kullanıldıktan sonra bile soğuk kalır',
            'Uzun ömürlü çalışma',
            'Mikrotitrasyon için özel tasarım',
            'Parçacıkların ve renk değişimlerinin görülebilmesi için renksiz yüzey',
            'Hafif yapı',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717171916_2764velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'ESP',
        'velp-esp-isitmasiz-manyetik-karistirici',
        ['velp-esp-isitmasiz-manyetik-karistirici', 'esp-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '5 litre H2O',
        '1100 rpm',
        null,
        'Teknopolimer',
        null,
        null,
        '0.9 kg',
        '160 x 33 x 230 mm',
        '5 W',
        'BOD, mikrotitrasyon, mikrobiyoloji ve biyokimya uygulamaları',
        [
            'Teknopolimer yapısı ile kimyasallara karşı direnç',
            'Ergonomik, çok ince ve hafif tasarım',
            'Hareketli mekanik bileşen olmadığı için bakım gerektirmez',
            'Özel kaçak oluk ile kontrol panelinin sıvı dökülmesine karşı korunması',
            'Uzun ömürlü çalışma',
            'Güvenilir uygulamalar',
            'Parçacıkların ve renk değişimlerinin görülebilmesi için ideal yüzey',
            'Mikrotitrasyon, BOD, mikrobiyoloji ve biyokimya için ideal',
            'Dayanıklı tasarım',
            'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717171857_2808velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'AGE',
        'velp-age-isitmasiz-manyetik-karistirici',
        ['velp-age-isitmasiz-manyetik-karistirici', 'age-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '8 litre H2O',
        '1200 rpm',
        null,
        'Epoksi kaplı sert çelik',
        null,
        null,
        '1.8 kg',
        '171 x 75 x 190 mm',
        '40 W',
        'yapışkan sıvılar ve yüksek güç gerektiren karıştırma uygulamaları',
        [
            'Kontrol paneli sıvı dökülmelerine karşı korunmuştur',
            'Yapışkan sıvılar için çok yüksek güç',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Uzun ömürlü çalışma',
            'Parçacıkların ve renk değişimlerinin görülebilmesi için renksiz yüzey',
            'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
            'Hafif yapı',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717171946_2879velp_magnetic_stirrers_comparison_table.pdf'
    ),
    $makeSimpleProduct(
        'ATE',
        'velp-ate-isitmasiz-manyetik-karistirici',
        ['velp-ate-isitmasiz-manyetik-karistirici', 'ate-isitmasiz-manyetik-karistirici'],
        'Analog, masa üstü',
        '25 litre H2O',
        '1200 rpm',
        null,
        'Epoksi kaplı sert çelik',
        null,
        null,
        '3.7 kg',
        '250 x 120 x 285 mm',
        '25 W',
        'yüksek hacimli ve yapışkan sıvı karıştırma uygulamaları',
        [
            'Kontrol paneli sıvı dökülmelerine karşı korunmuştur',
            'Yapışkan sıvılar için çok yüksek güç',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Uzun ömürlü çalışma',
            'Parçacıkların ve renk değişimlerinin görülebilmesi için renksiz yüzey',
            'Yumuşak başlangıç ve belirlenen hıza hızlı erişim',
        ],
        'https://www.sentezgroup.com.tr/img/mc-content/20170717172415_2795velp_magnetic_stirrers_comparison_table.pdf'
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
