<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$imageFor = fn (array|string $slugs): ?string => null;

$brand = [
    'name' => 'SI Analitik',
    'slug' => 'si-analitik',
    'summary' => 'Elektrokimya, titrasyon ve laboratuvar ölçüm ekipmanları.',
    'logo' => 'images/brands/si-analitik.png',
    'aliases' => ['SI Analytics', 'SI Analytic', 'SI Analitik'],
];

$categories = [
    'cozunmus-oksijen-olcum-elektrodlari' => [
        'name' => 'Çözünmüş Oksijen Ölçüm Elektrodları',
        'summary' => 'Çözünmüş oksijen ölçümleri için galvanik ve optik sensörlü elektrodlar.',
        'aliases' => ['Elektrodlar', 'Elektrotlar', 'Oksijen Elektrodu', 'Çözünmüş Oksijen Elektrodu', 'DO Elektrodu'],
    ],
    'fotometrik-olcum-elektrodu' => [
        'name' => 'Fotometrik Ölçüm Elektrodu',
        'summary' => 'Fotometrik titrasyon uygulamaları için ölçüm elektrodları.',
        'aliases' => ['Fotometrik Elektrod', 'Fotometrik Elektrot', 'Titrasyon Elektrodu'],
    ],
    'iletkenlik-olcum-elektrodlari' => [
        'name' => 'İletkenlik Ölçüm Elektrodları',
        'summary' => 'Laboratuvar ve saha iletkenlik ölçümleri için elektrod ve hücreler.',
        'aliases' => ['İletkenlik Elektrodu', 'İletkenlik Elektrotları', 'Conductivity Electrode'],
    ],
    'iyon-secici-elektrodlar' => [
        'name' => 'İyon Seçici Elektrodlar',
        'summary' => 'Belirli iyon parametrelerinin ölçümü için iyon seçici elektrodlar.',
        'aliases' => ['ISE Elektrod', 'İyon Elektrodu', 'İyon Seçici Elektrotlar'],
    ],
    'kombine-ph-elektrodlari' => [
        'name' => 'Kombine pH Elektrodları',
        'summary' => 'BlueLine, TopLine ve Memosens serisi kombine pH elektrodları.',
        'aliases' => ['pH Elektrodu', 'pH Elektrotları', 'Kombine pH Elektrotları'],
    ],
    'metal-kombine-elektrodlar' => [
        'name' => 'Metal Kombine Elektrodlar',
        'summary' => 'Redoks, Karl Fischer ve metal ölçüm uygulamaları için kombine elektrodlar.',
        'aliases' => ['Metal Elektrod', 'Metal Elektrot', 'Redoks Elektrodu', 'Karl Fischer Elektrodu'],
    ],
    'referans-elektrodlar' => [
        'name' => 'Referans Elektrodlar',
        'summary' => 'Elektrokimyasal ölçümlerde kullanılan Ag/AgCl, kalomel ve cıva/cıva sülfat referans sistemli elektrodlar.',
        'aliases' => ['Referans Elektrotlar', 'Referans Elektrodu', 'Reference Electrode'],
    ],
    'sicaklik-olcum-elektrodlari' => [
        'name' => 'Sıcaklık Ölçüm Elektrodları',
        'summary' => 'Laboratuvar ölçüm sistemlerinde kullanılan cam ve paslanmaz çelik gövdeli sıcaklık ölçüm elektrodları.',
        'aliases' => ['Sıcaklık Elektrodları', 'Sıcaklık Elektrotları', 'Sıcaklık Probu', 'Temperature Electrode'],
    ],
    'elektrod-baglanti-kablolari' => [
        'name' => 'Elektrod Bağlantı Kabloları',
        'summary' => 'pH, redoks, iyon seçici ve referans elektrodlar için elektrod bağlantı kabloları.',
        'aliases' => ['Elektrot Bağlantı Kabloları', 'Elektrod Kablosu', 'Elektrot Kablosu', 'Bağlantı Kablosu', 'L Soket', 'BNC Kablo', 'DIN Kablo', 'Banana Kablo'],
    ],
];

function electrode_slug(string $model, string $suffix): string
{
    $value = strtr($model, [
        'Ç' => 'C', 'ç' => 'c', 'Ğ' => 'G', 'ğ' => 'g', 'İ' => 'I', 'ı' => 'i',
        'Ö' => 'O', 'ö' => 'o', 'Ş' => 'S', 'ş' => 's', 'Ü' => 'U', 'ü' => 'u',
        '®' => '', '+' => ' plus ', '/' => ' ', '.' => '-', ',' => '-', '(' => ' ', ')' => ' ',
    ]);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = trim($value, '-');

    return 'si-analitik-' . $value . '-' . $suffix;
}

$products = [];

$addProduct = function (string $categorySlug, array $product) use (&$products): void {
    $products[] = [
        'category_slug' => $categorySlug,
        'documents' => [],
        ...$product,
    ];
};

$addProduct('cozunmus-oksijen-olcum-elektrodlari', [
    'name' => 'OX 1100+ Çözünmüş Oksijen Ölçüm Elektrodu',
    'slug' => 'ox-1100-plus-cozunmus-oksijen-olcum-elektrodu',
    'image_slugs' => ['ox-1100-plus-cozunmus-oksijen-olcum-elektrodu'],
    'model' => 'OX 1100+',
    'summary' => 'OX 1100+; 0-60 mg/L çözünmüş oksijen ölçüm aralığına sahip, NTC sıcaklık kompanzasyonlu galvanik oksijen elektrodudur.',
    'body' => 'OX 1100+ çözünmüş oksijen ölçüm elektrodu; galvanik sensör yapısı, Pt katot, Ag anot, SMEK tipi kafa bağlantısı ve NTC sıcaklık kompanzasyonu ile çözünmüş oksijen ölçümlerinde kullanılır. Uygun bağlantı kablosu LS 1 ST4 OX olarak belirtilmiştir.',
    'features' => [
        'Galvanik sensör yapısı',
        'Pt katot ve Ag anot',
        'SMEK tipi kafa bağlantısı',
        'NTC sıcaklık kompanzasyonu',
        'LS 1 ST4 OX bağlantı kablosu ile uyumluluk',
    ],
    'specs' => [
        'Ürün tipi' => 'Çözünmüş oksijen ölçüm elektrodu',
        'Model' => 'OX 1100+',
        'Uzunluk' => '120 mm',
        'Çalışma sıcaklığı' => '0-45 °C',
        'Ölçüm aralığı' => '0-60 mg/L',
        'Sensör tipi' => 'Galvanik sensör',
        'Katot' => 'Pt',
        'Anot' => 'Ag',
        'Kafa bağlantısı' => 'SMEK tipi',
        'Sıcaklık kompanzasyonu' => 'NTC',
        'Uygun bağlantı kablosu' => 'LS 1 ST4 OX',
    ],
    'metadata' => [
        'Marka' => 'SI Analitik',
        'Kategori' => 'Çözünmüş Oksijen Ölçüm Elektrodları',
        'Model' => 'OX 1100+',
        'SKU' => 'Yayın öncesi netleştirilecek',
        'Ürün tipi' => 'Çözünmüş oksijen ölçüm elektrodu',
    ],
    'image_alt' => 'OX 1100+ çözünmüş oksijen ölçüm elektrodu ürün görseli',
]);

$addProduct('fotometrik-olcum-elektrodu', [
    'name' => 'OptiLine 6 Fotometrik Ölçüm Elektrodu',
    'slug' => 'optiline-6-fotometrik-olcum-elektrodu',
    'image_slugs' => ['optiline-6-fotometrik-olcum-elektrodu'],
    'model' => 'OptiLine 6',
    'summary' => 'OptiLine 6, fotometrik titrasyonlar için 470-625 nm arasında ayarlanabilir dalga boyu seçenekleri sunan ölçüm elektrodudur.',
    'body' => 'OptiLine 6 fotometrik ölçüm elektrodu; fotometrik titrasyonlar, farmakope uygulamaları, kondroitin sülfat-sodyum titrasyonu, PET numunelerde karboksil uç grup belirleme, ASTM D974 TAN/TBN analizi, sülfat titrasyonu ve Ca/Mg/toplam sertlik gibi kompleksometrik titrasyonlarda kullanılabilir.',
    'features' => [
        'Fotometrik titrasyonlar için kullanım',
        'Avrupa ve Amerikan Farmakopisine göre indikatör kullanılan titrasyonlara uygunluk',
        'Kondroitin sülfat-sodyum titrasyonu için kullanım',
        'PET numunelerde karboksil uç grupların belirlenmesi',
        'ASTM D974’e göre TAN/TBN analizi',
        'Sülfat titrasyonu ve kompleksometrik titrasyonlarda kullanım',
        '470, 520, 570, 590, 605 ve 625 nm ayarlanabilir dalga boyu',
        'Tanıtım videosu: https://www.youtube.com/watch?v=dVHBmLQ_QKI',
    ],
    'specs' => [
        'Ürün tipi' => 'Fotometrik ölçüm elektrodu',
        'Model' => 'OptiLine 6',
        'Mil çapı' => '12 mm',
        'Mil uzunluğu' => '132 mm',
        'Minimum daldırma derinliği' => '25 mm',
        'Mil malzemesi' => 'Titanyum',
        'Kablo' => '2 m',
        'Bağlantı' => 'USB fişi A, BNC-DIN adaptörlü BNC fişi',
        'Güç kaynağı' => 'USB',
        'Ölçüm aralığı' => '0-2000 mV',
        'Sıcaklık aralığı' => '0-50 °C',
        'pH aralığı' => '0-14',
        'Ayarlanabilir dalga boyu' => '470, 520, 570, 590, 605 ve 625 nm',
    ],
    'metadata' => [
        'Marka' => 'SI Analitik',
        'Kategori' => 'Fotometrik Ölçüm Elektrodu',
        'Model' => 'OptiLine 6',
        'SKU' => 'Yayın öncesi netleştirilecek',
        'Ürün tipi' => 'Fotometrik ölçüm elektrodu',
    ],
    'image_alt' => 'OptiLine 6 fotometrik ölçüm elektrodu ürün görseli',
]);

$conductivityElectrodes = [
    ['BlueLine 48 LF', '285129488', 'Paslanmaz çelik', '1 m', 'NTC'],
    ['LF 1100 T +', '1069977', 'Cam', '-', 'Pt 1000'],
    ['LF 413 T', '285106172', 'Noryl', '1.5 m', 'NTC'],
    ['LF 413 T-3', '285106148', 'Noryl', '3 m', 'NTC'],
    ['LF 513 T', '285106037', 'Noryl', '1 m', 'NTC'],
    ['LF 613 T', '285106131', 'Noryl', '1 m', 'NTC'],
];

foreach ($conductivityElectrodes as [$model, $catalogNo, $material, $cable, $temperatureSensor]) {
    $slug = electrode_slug($model, 'iletkenlik-olcum-elektrodu');
    $addProduct('iletkenlik-olcum-elektrodlari', [
        'name' => $model . ' İletkenlik Ölçüm Elektrodu',
        'slug' => $slug,
        'image_slugs' => [$slug],
        'model' => $model,
        'summary' => $model . ' iletkenlik ölçüm elektrodu; ' . $material . ' malzeme ve ' . $temperatureSensor . ' sıcaklık sensörü bilgisiyle listelenir.',
        'body' => $model . ' iletkenlik ölçüm elektrodu, laboratuvar ve saha iletkenlik ölçüm uygulamalarında kullanılmak üzere listelenmiştir.',
        'features' => [
            'İletkenlik ölçüm uygulamaları için elektrod',
            $material . ' malzeme',
            $temperatureSensor === '-' ? 'Sıcaklık sensörü belirtilmemiştir' : $temperatureSensor . ' sıcaklık sensörü',
            $cable === '-' ? 'Kablo bilgisi belirtilmemiştir' : $cable . ' kablo',
        ],
        'specs' => [
            'Ürün tipi' => 'İletkenlik ölçüm elektrodu',
            'Model' => $model,
            'Katalog no' => $catalogNo,
            'Malzeme' => $material,
            'Kablo' => $cable,
            'Sıcaklık sensörü' => $temperatureSensor,
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'İletkenlik Ölçüm Elektrodları',
            'Model' => $model,
            'SKU' => $catalogNo,
            'Ürün tipi' => 'İletkenlik ölçüm elektrodu',
        ],
        'image_alt' => $model . ' iletkenlik ölçüm elektrodu ürün görseli',
    ]);
}

$ionSelectiveElectrodes = [
    ['TEN 1100', '285096980', 'Yüzey aktif madde analizi için', '-', ''],
    ['Ca 1100 A', '285216314', 'Kalsiyum', '0.02-40000', 'DIN'],
    ['Cu 1100 A', '285216312', 'Bakır', '0.0006-6400', 'DIN'],
    ['F 1100 A', '285216313', 'Florür', '0.02-Doygun', 'DIN'],
    ['Pb 1100 A', '285216315', 'Kurşun', '0.1-20000', 'DIN'],
    ['F 60', '285130340', 'Florür', '0.02-Doygun', '-'],
    ['Cl 60', '285130350', 'Klorür', '2-35000', '-'],
    ['NO 60', '285130360', 'Nitrat', '0.4-62000', '-'],
    ['K 60', '285130370', 'Potasyum', '0.04-39000', '-'],
    ['CA 60', '285130380', 'Kalsiyum', '0.02-40000', '-'],
    ['CN 60', '285130390', 'Siyanür', '0.2-260', '-'],
    ['AG-S 60', '285130400', 'Sülfür', '0.003-32000', '-'],
    ['I 60', '285130410', 'İyodür', '0.006-127000', '-'],
    ['BR 60', '285130420', 'Bromür', '0.4-79000', '-'],
    ['CU 60', '285130430', 'Bakır', '0.0006-6400', '-'],
    ['PB 60', '285130440', 'Kurşun', '0.2-20000', '-'],
    ['NH 1100', '285102808', 'Amonyak', '0.1-1000', '-'],
    ['Na 61', '285100026', 'Sodyum', '0-6 pNa', '-'],
];

foreach ($ionSelectiveElectrodes as [$model, $catalogNo, $parameter, $range, $cable]) {
    $slug = electrode_slug($model, 'iyon-secici-elektrod');
    $addProduct('iyon-secici-elektrodlar', [
        'name' => $model . ' İyon Seçici Elektrod',
        'slug' => $slug,
        'image_slugs' => [$slug],
        'model' => $model,
        'summary' => $model . ' iyon seçici elektrod, ' . $parameter . ' parametresi için kullanılır.',
        'body' => $model . ' iyon seçici elektrod, ' . $parameter . ' ölçüm uygulamaları için listelenmiştir.',
        'features' => [
            $parameter . ' parametresi için iyon seçici elektrod',
            $range === '-' ? 'Ölçüm aralığı ayrıca netleştirilecek' : $range . ' mg/L ölçüm aralığı',
            $cable === '-' || $cable === '' ? 'Kablo bilgisi belirtilmemiştir' : $cable . ' bağlantı',
        ],
        'specs' => [
            'Ürün tipi' => 'İyon seçici elektrod',
            'Model' => $model,
            'Katalog no' => $catalogNo,
            'Parametre' => $parameter,
            'Ölçüm aralığı' => $range,
            'Kablo' => $cable === '' ? '-' : $cable,
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'İyon Seçici Elektrodlar',
            'Model' => $model,
            'SKU' => $catalogNo,
            'Parametre' => $parameter,
        ],
        'image_alt' => $model . ' iyon seçici elektrod ürün görseli',
    ]);
}

$seriesProducts = [
    [
        'name' => 'BlueLine Serisi pH Elektrodları',
        'slug' => 'si-analitik-blueline-serisi-ph-elektrodlari',
        'model' => 'BlueLine Serisi',
        'summary' => 'BlueLine serisi pH elektrodları; cam ve Noryl gövde, DIN/BNC/Metrohm bağlantı ve NTC/PT1000 sensör seçenekleriyle sunulur.',
        'body' => 'BlueLine serisi kombine pH elektrodları, farklı gövde ve bağlantı seçenekleriyle laboratuvar pH ve redoks uygulamalarında kullanılır. Seri içinde BlueLine 11 pH, 12 pH, 13 pH, 14 pH, 15 pH, 15 pH Chinch, 16 pH, 17 pH, 17 pH-R, 18 pH, 19 pH, 21 pH, 22 pH, 23 pH, 24 pH, 25 pH, 26 pH, 26 pH Chinch, 27 pH, 28 pH, 28 pH-5, 28 pH-10, 29 pH, 31 RX ve 32 RX modelleri yer alır.',
        'features' => [
            'Cam ve Noryl gövde seçenekleri',
            'DIN, BNC ve Metrohm bağlantı seçenekleri',
            'NTC ve PT 1000 sıcaklık sensörü seçenekleri',
            'pH ve RX model seçenekleri',
        ],
        'specs' => [
            'Ürün tipi' => 'Kombine pH elektrod serisi',
            'Seri' => 'BlueLine',
            'Modeller' => 'BlueLine 11 pH (285129114), BlueLine 12 pH (285129122), BlueLine 13 pH (285129139), BlueLine 14 pH (285129147), BlueLine 15 pH (285129155), BlueLine 15 pH Chinch (285095730), BlueLine 16 pH (285129163), BlueLine 17 pH (285129171), BlueLine 17 pH-R (1064746), BlueLine 18 pH (285129188), BlueLine 19 pH (285129190), BlueLine 21 pH (285129217), BlueLine 22 pH (285129225), BlueLine 23 pH (285129233), BlueLine 24 pH (285129241), BlueLine 25 pH (285129258), BlueLine 26 pH (285129266), BlueLine 26 pH Chinch (285095712), BlueLine 27 pH (285129274), BlueLine 28 pH (285129282), BlueLine 28 pH-5 (285129570), BlueLine 28 pH-10 (285129620), BlueLine 29 pH (1065895), BlueLine 31 RX (285129311), BlueLine 32 RX (285129320)',
        ],
    ],
    [
        'name' => 'TopLine Serisi pH Elektrotları',
        'slug' => 'si-analitik-topline-serisi-ph-elektrotlari',
        'model' => 'TopLine Serisi',
        'summary' => 'TopLine serisi pH elektrotları; PEEK malzeme, DIN/BNC bağlantı ve NTC/PT1000 sensör seçenekleriyle sunulur.',
        'body' => 'TopLine serisi kombine pH elektrotları, PEEK malzeme yapısı ve farklı bağlantı/sıcaklık sensörü seçenekleriyle laboratuvar pH ölçümleri için listelenmiştir.',
        'features' => [
            'PEEK malzeme',
            'DIN ve BNC bağlantı seçenekleri',
            'NTC ve PT 1000 sıcaklık sensörü seçenekleri',
            'TopLine 22-32 pH model aralığı',
        ],
        'specs' => [
            'Ürün tipi' => 'Kombine pH elektrot serisi',
            'Seri' => 'TopLine',
            'Modeller' => 'TopLine 25 pH (285111155), TopLine 23 pH (285111140), TopLine 32 pH (285111180), TopLine 26 pH (285111160), TopLine 28 pH (285111170), TopLine 29 pH (285111175), TopLine 22 pH (285111135), TopLine 24 pH (285111145), TopLine 26 pH Chinch (285111165)',
        ],
    ],
    [
        'name' => 'Memosens Serisi pH Elektrotları',
        'slug' => 'si-analitik-memosens-serisi-ph-elektrotlari',
        'model' => 'Memosens Serisi',
        'summary' => 'Memosens serisi pH elektrotları; cam gövde, platin diyafram ve NTC sıcaklık sensörüyle farklı uzunluk ve sıcaklık seçenekleri sunar.',
        'body' => 'Memosens serisi kombine pH elektrotları, cam gövde, platin diyafram ve NTC sıcaklık sensörü ile listelenmiştir. Seri içinde 12 cm, 22.5 cm, 28 cm ve 38 cm gövde uzunlukları ile -30...+100 °C ve +10...+135 °C çalışma sıcaklığı seçenekleri bulunur.',
        'features' => [
            'Cam gövde seçenekleri',
            'Platin diyafram',
            'NTC sıcaklık sensörü',
            '12 cm, 22.5 cm, 28 cm ve 38 cm uzunluk seçenekleri',
            '-30...+100 °C ve +10...+135 °C çalışma sıcaklığı seçenekleri',
        ],
        'specs' => [
            'Ürün tipi' => 'Kombine pH elektrot serisi',
            'Seri' => 'Memosens',
            'Modeller' => 'FL A 93-120 MF NMSN (285118180), FL S 93-120 MF NMSN (285118200), FL A 93-225 MF NMSN (285118185), FL S 93-225 MF NMSN (285118210), FL A 93-280 MF NMSN (285118190), FL S 93-280 MF NMSN (285118220), FL A 93-380 MF NMSN (285118195), FL S 93-380 MF NMSN (285118230)',
        ],
    ],
];

foreach ($seriesProducts as $series) {
    $addProduct('kombine-ph-elektrodlari', [
        'name' => $series['name'],
        'slug' => $series['slug'],
        'image_slugs' => [$series['slug']],
        'model' => $series['model'],
        'summary' => $series['summary'],
        'body' => $series['body'],
        'features' => $series['features'],
        'specs' => $series['specs'],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Kombine pH Elektrodları',
            'Model' => $series['model'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Kombine pH elektrod serisi',
        ],
        'image_alt' => $series['name'] . ' ürün görseli',
    ]);
}

$metalElectrodes = [
    ['KF 1100', '285102030', 'Cam', '2 x 4 mm', '-'],
    ['KF 1150', '285102060', 'Cam', '2 x 4 mm', '-'],
    ['AgCl 62', '285102413', 'Cam', '-', '-'],
    ['AgCl 62 RG', '285102100', 'Cam', '-', '-'],
    ['AgCl 6280', '285102351', 'Cam', '-', '-'],
    ['Ag 6280', '285102343', 'Cam', '-', '-'],
    ['Au 6280', '285102121', 'Cam', '-', '-'],
    ['Pt 6180', '285102232', 'Cam', '-', '-'],
    ['Pt 61', '285102002', 'Cam', '-', '-'],
    ['Pt 62', '285102019', 'Cam', '-', '-'],
    ['Pt 62 RG', '285102070', 'Cam', '-', '-'],
    ['Pt 62 RG IDS', '285102140', 'Cam', 'DIN', '-'],
    ['Pt 6280', '285102249', 'Cam', '-', '-'],
    ['Ag 42 A', '285102051', 'Cam', 'DIN', '-'],
    ['Pt 42 A', '285102302', 'Cam', 'DIN', '-'],
    ['Pt 1200', '285103512', 'Cam', '-', '-'],
    ['Pt 1400', '285103537', 'Cam', '-', '-'],
    ['AgS 62 RG', '285102110', 'Cam', '-', '-'],
];

foreach ($metalElectrodes as [$model, $catalogNo, $material, $cable, $temperatureSensor]) {
    $slug = electrode_slug($model, 'metal-kombine-elektrod');
    $addProduct('metal-kombine-elektrodlar', [
        'name' => $model . ' Metal Kombine Elektrod',
        'slug' => $slug,
        'image_slugs' => [$slug],
        'model' => $model,
        'summary' => $model . ' metal kombine elektrod; ' . $material . ' gövde ve ' . ($cable === '-' ? 'kablo bilgisi belirtilmemiş' : $cable . ' bağlantı') . ' yapısıyla listelenir.',
        'body' => $model . ' metal kombine elektrod, redoks, Karl Fischer veya metal elektrod uygulamalarında kullanılmak üzere listelenmiştir.',
        'features' => [
            'Metal kombine elektrod uygulamaları için kullanım',
            $material . ' malzeme',
            $cable === '-' ? 'Kablo bilgisi belirtilmemiştir' : $cable . ' kablo/bağlantı',
            $temperatureSensor === '-' ? 'Sıcaklık sensörü belirtilmemiştir' : $temperatureSensor . ' sıcaklık sensörü',
        ],
        'specs' => [
            'Ürün tipi' => 'Metal kombine elektrod',
            'Model' => $model,
            'Katalog no' => $catalogNo,
            'Malzeme' => $material,
            'Kablo' => $cable,
            'Sıcaklık sensörü' => $temperatureSensor,
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Metal Kombine Elektrodlar',
            'Model' => $model,
            'SKU' => $catalogNo,
            'Ürün tipi' => 'Metal kombine elektrod',
        ],
        'image_alt' => $model . ' metal kombine elektrod ürün görseli',
    ]);
}

$referenceElectrodes = [
    ['B 2220 +', '1069994', 'Cam', 'Ag/AgCl', 'Platin', '-'],
    ['B 2420 +', '1070028', 'Cam', 'Ag/AgCl', 'Şilifli', '-'],
    ['B 2810 +', '1070029', 'Cam', 'Kalomel', 'Seramik', '-'],
    ['B 2820 +', '1070044', 'Cam', 'Ag/AgCl', 'Seramik', '-'],
    ['B 2910 +', '1070077', 'Cam', 'Kalomel', 'Platin', '-'],
    ['B 2920 +', '1070046', 'Cam', 'Ag/AgCl', 'Platin', '-'],
    ['B 3410 +', '1070048', 'Cam', 'Kalomel', 'Seramik', '-'],
    ['B 3420 +', '1070070', 'Cam', 'Ag/AgCl', 'Seramik', '-'],
    ['B 3510 +', '1070100', 'Cam', 'Kalomel', 'Platin', '-'],
    ['B 3520 +', '1070073', 'Cam', 'Ag/AgCl', 'Platin', '-'],
    ['B 3610 +', '1070074', 'Cam', 'Cıva/Cıva Sülfat', 'Seramik', '-'],
    ['B 3920 +', '1070075', 'Cam', 'Ag/AgCl', 'Şilifli', '-'],
];

foreach ($referenceElectrodes as [$model, $catalogNo, $material, $referenceSystem, $diaphragm, $cable]) {
    $slug = electrode_slug($model, 'referans-elektrod');
    $addProduct('referans-elektrodlar', [
        'name' => $model . ' Referans Elektrodu',
        'slug' => $slug,
        'image_slugs' => [$slug],
        'model' => $model,
        'summary' => $model . ' referans elektrodu; ' . $referenceSystem . ' referans sistemi, ' . $diaphragm . ' diyafram ve ' . $material . ' gövde yapısıyla listelenir.',
        'body' => $model . ' referans elektrodu, elektrokimyasal ölçüm uygulamalarında referans elektrod olarak kullanılmak üzere listelenmiştir.',
        'features' => [
            $referenceSystem . ' referans sistemi',
            $diaphragm . ' diyafram',
            $material . ' malzeme',
            $cable === '-' ? 'Kablo bilgisi belirtilmemiştir' : $cable . ' kablo',
        ],
        'specs' => [
            'Ürün tipi' => 'Referans elektrod',
            'Model' => $model,
            'Katalog no' => $catalogNo,
            'Malzeme' => $material,
            'Referans sistemi' => $referenceSystem,
            'Diyafram' => $diaphragm,
            'Kablo' => $cable,
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Referans Elektrodlar',
            'Model' => $model,
            'SKU' => $catalogNo,
            'Ürün tipi' => 'Referans elektrod',
            'Referans sistemi' => $referenceSystem,
        ],
        'image_alt' => $model . ' referans elektrodu ürün görseli',
    ]);
}

$temperatureElectrodes = [
    ['W 5780 NN', '285105221', 'Cam', '2x, 4 mm'],
    ['W 5791 NN', '285105262', 'Paslanmaz çelik, 17 cm', '2x, 4 mm'],
    ['W 5790 NN', '285105254', 'Paslanmaz çelik, 12 cm', '2x, 4 mm'],
    ['W 5790 PP', '285105776', 'Paslanmaz çelik, 12 cm', '2x, 2 mm'],
    ['W 5980 NN', '285105287', 'Cam', '2x, 4 mm'],
];

foreach ($temperatureElectrodes as [$model, $catalogNo, $material, $cable]) {
    $slug = electrode_slug($model, 'sicaklik-olcum-elektrodu');
    $addProduct('sicaklik-olcum-elektrodlari', [
        'name' => $model . ' Sıcaklık Ölçüm Elektrodu',
        'slug' => $slug,
        'image_slugs' => [$slug],
        'model' => $model,
        'summary' => $model . ' sıcaklık ölçüm elektrodu; ' . $material . ' malzeme ve ' . $cable . ' kablo bağlantısı ile listelenir.',
        'body' => $model . ' sıcaklık ölçüm elektrodu, laboratuvar ölçüm sistemlerinde sıcaklık ölçüm uygulamaları için listelenmiştir.',
        'features' => [
            'Sıcaklık ölçüm uygulamaları için elektrod',
            $material . ' malzeme',
            $cable . ' kablo bağlantısı',
        ],
        'specs' => [
            'Ürün tipi' => 'Sıcaklık ölçüm elektrodu',
            'Model' => $model,
            'Katalog no' => $catalogNo,
            'Malzeme' => $material,
            'Kablo' => $cable,
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Sıcaklık Ölçüm Elektrodları',
            'Model' => $model,
            'SKU' => $catalogNo,
            'Ürün tipi' => 'Sıcaklık ölçüm elektrodu',
        ],
        'image_alt' => $model . ' sıcaklık ölçüm elektrodu ürün görseli',
    ]);
}

$electrodeConnectionCables = [
    ['B 1 N', '285121916', 'Referans elektrod fişi (B)', 'Banana fiş (N)', '1 m tek iletkenli kablo'],
    ['L 1 A', '285122456', 'Elektrod fişi (L)', 'DIN cihaz fişi (A)', '1 m koaksiyel kablo'],
    ['L 1 BNC', '285122497', 'Elektrod fişi (L)', 'BNC cihaz fişi', '1 m koaksiyel kablo'],
    ['L 2 N', '285122550', 'Elektrod fişi (L)', 'Banana fiş (N)', '2 m koaksiyel kablo'],
    ['L 1 N', '285122457', 'Elektrod fişi (L)', 'Banana fiş (N)', '1 m koaksiyel kablo'],
    ['L 1 NN', '285122489', 'Elektrod fişi (L)', '2 x banana fiş (N)', '1 m koaksiyel kablo'],
    ['L 2 A', '285122464', 'Elektrod fişi (L)', 'DIN cihaz fişi (A)', '2 m koaksiyel kablo'],
    ['L 2 NN', '285122448', 'Elektrod fişi (L)', '2 x 4 mm banana fiş (N)', '2 m koaksiyel kablo'],
];

foreach ($electrodeConnectionCables as [$model, $catalogNo, $electrodePlug, $instrumentPlug, $cableLengthAndType]) {
    $slug = electrode_slug($model, 'elektrod-baglanti-kablosu');
    $addProduct('elektrod-baglanti-kablolari', [
        'name' => $model . ' Elektrod Bağlantı Kablosu',
        'slug' => $slug,
        'image_slugs' => [$slug],
        'model' => $model,
        'summary' => $model . ' elektrod bağlantı kablosu; ' . $electrodePlug . ' ile ' . $instrumentPlug . ' bağlantısı ve ' . $cableLengthAndType . ' yapısıyla listelenir.',
        'body' => $model . ' elektrod bağlantı kablosu; pH, redoks, amonyak ve sodyum kombine elektrodları, pH/redoks tekli elektrodları ve Plus serisi referans elektrodları için kullanılan L soket, DIN, BNC veya banana fiş bağlantı seçenekleri arasında yer alır.',
        'features' => [
            'pH, redoks, amonyak ve sodyum kombine elektrodları için bağlantı kablosu',
            'pH ve redoks tekli elektrodları için bağlantı desteği',
            'Plus serisi referans elektrodlarla kullanım',
            $electrodePlug,
            $instrumentPlug,
            $cableLengthAndType,
        ],
        'specs' => [
            'Ürün tipi' => 'Elektrod bağlantı kablosu',
            'Tip no' => $model,
            'Sipariş no' => $catalogNo,
            'Elektrod soketi/fişi' => $electrodePlug,
            'Cihaz konnektörü/fişi' => $instrumentPlug,
            'Kablo uzunluğu ve tipi' => $cableLengthAndType,
            'Uyumlu elektrodlar' => 'pH, redoks, amonyak ve sodyum kombine elektrodları; pH/redoks tekli elektrodları; Plus serisi referans elektrodlar',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Elektrod Bağlantı Kabloları',
            'Model' => $model,
            'SKU' => $catalogNo,
            'Ürün tipi' => 'Elektrod bağlantı kablosu',
        ],
        'image_alt' => $model . ' elektrod bağlantı kablosu ürün görseli',
    ]);
}

$db->beginTransaction();

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

$categoryIds = [];

foreach ($categories as $slug => $category) {
    $stmt = $db->prepare('select id from product_categories where slug = :slug');
    $stmt->execute(['slug' => $slug]);
    $categoryId = $stmt->fetchColumn();

    if (! $categoryId) {
        $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_categories')->fetchColumn();
        $stmt = $db->prepare('insert into product_categories (name, slug, summary, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :aliases, 1, :sort_order, :created_at, :updated_at)');
        $stmt->execute([
            'name' => $category['name'],
            'slug' => $slug,
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

    $categoryIds[$slug] = $categoryId;

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
}

$selectProduct = $db->prepare('select id from products where product_category_id = :category_id and slug = :slug');
$insertProduct = $db->prepare("insert into products (product_category_id, product_brand_id, name, slug, model, sku, old_url, summary, body, image, image_alt, features, metadata, specs, status, is_featured, sort_order, published_at, created_at, updated_at) values (:category_id, :brand_id, :name, :slug, :model, :sku, :old_url, :summary, :body, :image, :image_alt, :features, :metadata, :specs, 'published', 0, :sort_order, :published_at, :created_at, :updated_at)");
$updateProduct = $db->prepare("update products set product_brand_id = :brand_id, name = :name, model = :model, sku = :sku, old_url = :old_url, summary = :summary, body = :body, image = :image, image_alt = :image_alt, features = :features, metadata = :metadata, specs = :specs, status = 'published', is_featured = 0, sort_order = :sort_order, published_at = :published_at, updated_at = :updated_at where id = :id");
$deleteDocuments = $db->prepare('delete from product_documents where product_id = :product_id');
$insertDocument = $db->prepare('insert into product_documents (product_id, title, type, path, url, sort_order, created_at, updated_at) values (:product_id, :title, :type, :path, :url, :sort_order, :created_at, :updated_at)');

foreach ($products as $index => $product) {
    $categoryId = $categoryIds[$product['category_slug']];
    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => $product['metadata']['SKU'] === 'Yayın öncesi netleştirilecek' ? null : $product['metadata']['SKU'],
        'old_url' => null,
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['image_slugs']),
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
            'title' => $document['title'] ?? 'Broşür',
            'type' => $document['type'] ?? 'catalog',
            'path' => $document['path'] ?? null,
            'url' => $document['url'] ?? null,
            'sort_order' => ($documentIndex + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

$db->commit();

echo 'brand_id=' . $brandId . PHP_EOL;
foreach ($categoryIds as $slug => $id) {
    echo 'category=' . $slug . ':' . $id . PHP_EOL;
}
foreach ($products as $product) {
    echo 'product_slug=' . $product['slug'] . PHP_EOL;
    echo 'image=' . ($imageFor($product['image_slugs']) ?: 'missing') . PHP_EOL;
    echo 'documents=' . count($product['documents']) . PHP_EOL;
}
echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
