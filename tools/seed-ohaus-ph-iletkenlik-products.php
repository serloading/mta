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
    'name' => 'Ohaus',
    'slug' => 'ohaus',
    'summary' => 'Terazi, nem tayin, pH metre, iletkenlik ölçer ve laboratuvar cihazları.',
    'logo' => 'images/brands/ohaus.png',
    'aliases' => ['OHAUS', 'Ohaus Türkiye'],
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

$products = [
    [
        'name' => 'OHAUS a-AB23EC-F Masa Tipi İletkenlik Ölçer',
        'slug' => 'ohaus-a-ab23ec-f',
        'model' => 'a-AB23EC-F',
        'sku' => '30589823',
        'summary' => 'OHAUS AquaSearcher a-AB23EC-F; iletkenlik, TDS ve tuzluluk ölçümü için 99 ölçüm hafızalı, geniş LCD ekranlı masa tipi iletkenlik ölçerdir.',
        'body' => 'OHAUS AquaSearcher serisi a-AB23EC-F masa tipi iletkenlik ölçer; kolay kullanıcı arayüzü, geniş LCD ekran, 99 adet ölçüm hafızası, otomatik tampon çözelti tanıma ve otomatik sıcaklık kompanzasyonu ile laboratuvar iletkenlik, TDS ve tuzluluk ölçümleri için kullanılır. Set içeriğinde a-AB23EC masa tipi iletkenlik ölçer, STCON3 iletkenlik elektrodu ve 1413 µS/cm iletkenlik standart solüsyon seti yer alır.',
        'features' => [
            'İletkenlik (EC), TDS ve tuzluluk ölçümü',
            '0.01 µS/cm-199.9 mS/cm iletkenlik ölçüm aralığı',
            '0.01 µS/cm minimum çözünürlük ve oto-aralık',
            '0.0-100.0 °C sıcaklık aralığı',
            '99 set ölçüm hafızası',
            '5 inç aydınlatmalı LCD ekran',
            'OHAUS STCON3 set elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'a-AB23EC-F',
            'SKU' => '30589823',
            'Ürün tipi' => 'Masa tipi iletkenlik ölçer',
        ],
        'specs' => [
            'Ürün ismi' => 'a-AB23EC-F',
            'Ürün kodu' => '30589823',
            'Seri' => 'OHAUS AquaSearcher',
            'Cihaz tipi' => 'Masa tipi iletkenlik ölçer',
            'Fonksiyon' => 'İletkenlik (EC); TDS; Tuzluluk',
            'Set içeriği' => 'a-AB23EC masa tipi iletkenlik ölçer, STCON3 iletkenlik elektrodu, iletkenlik standart solüsyon seti (1413 µS/cm, 4x50 ml)',
            'Set elektrotları' => 'OHAUS STCON3 iletkenlik elektrodu (83033972)',
            'İletkenlik ölçüm aralığı' => '0.01 µS/cm-19.99 µS/cm; 20 µS/cm-199.9 µS/cm; 200 µS/cm-1999 µS/cm; 2.00 mS/cm-19.99 mS/cm; 20.0 mS/cm-199.9 mS/cm',
            'İletkenlik ölçüm çözünürlüğü' => '0.01 µS/cm minimum; oto-aralık',
            'İletkenlik ölçüm doğrusallığı' => '+/- 1% okunan, +/- 3 son hane (LSD)',
            'Referans sıcaklık' => '20 °C; 25 °C',
            'Hücre sabiti' => '0.08-2.0 cm-1',
            'Sıcaklık kompanzasyonu' => 'Lineer (0-10%/°C), off',
            'TDS ölçüm aralığı' => '0.1 mg/L-199.9 g/L',
            'TDS ölçüm çözünürlüğü' => '0.01 mg/L minimum, oto-aralık',
            'TDS ölçüm doğrusallığı' => '+/- 1% okunan, +/- son hane (LSD)',
            'TDS faktör aralığı' => 'Lineer, 0.04-10.00, varsayılan 0.5',
            'Tuzluluk ölçüm aralığı' => '0-99.9 psu',
            'Tuzluluk ölçüm çözünürlüğü' => '0.01 psu minimum, oto-aralık',
            'Tuzluluk ölçüm doğrusallığı' => '+/- 1% okunan, +/- 2 son hane (LSD)',
            'Sıcaklık ölçüm aralığı' => '0.0-100.0 °C',
            'Sıcaklık ölçüm çözünürlüğü' => '0.1 °C',
            'Sıcaklık ölçüm doğrusallığı' => '+/- 0.5 °C',
            'Kalibrasyon' => '1 noktalı hücre sabiti kalibrasyonu; 4 iletkenlik solüsyonu seçeneği; yüz işareti; lineer mod',
            'Ekran tipi' => '5 inç aydınlatmalı LCD ekran',
            'Ölçüm bitirme modları' => 'Otomatik durma, sürekli',
            'Ölçüm hafızası' => '99 set',
            'Kalibrasyon hafızası' => 'Son kalibrasyon',
            'Tuş takımı' => 'Membran tip',
            'Elektrot bağlantısı' => 'Mini-DIN',
            'Sıcaklık bağlantısı' => 'Cinch, NTC 30 kΩ',
            'Güç' => 'Evrensel, 100-240 VAC, 50-60 Hz',
            'Çalışma ortam koşulları' => '5-40 °C; 5-80% yoğunlaşmayan',
            'Garanti' => '1 yıl',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/Datasheet_AB23EC_EN.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/a-AB23EC-F-Iletkenlik-Sartnamesi.doc'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/30645887_EU-DoC_A_AB23EC_2021-02-08.pdf'],
        ],
    ],
    [
        'name' => 'OHAUS a-AB23PH-F Masa Tipi pH Metre',
        'slug' => 'ohaus-a-ab23ph-f',
        'model' => 'a-AB23PH-F',
        'sku' => '30589823',
        'summary' => 'OHAUS AquaSearcher a-AB23PH-F; pH, ORP ve Redox ölçümü için 99 ölçüm hafızalı, geniş LCD ekranlı masa tipi pH metredir.',
        'body' => 'OHAUS AquaSearcher serisi a-AB23PH-F masa tipi pH metre; kolay kullanıcı arayüzü, geniş LCD ekran, 99 adet ölçüm hafızası, otomatik tampon çözelti tanıma ve otomatik sıcaklık kompanzasyonu ile laboratuvar pH, ORP ve Redox ölçümleri için kullanılır. Set içeriğinde a-AB23PH masa tipi pH metre, ST320 pH elektrodu ve pH tampon çözeltileri mini seti yer alır.',
        'features' => [
            'pH, ORP ve Redox ölçümü',
            '0.00-14.00 pH ölçüm aralığı',
            '0.01 pH ölçüm çözünürlüğü',
            '+/- 1999 mV ORP/Redox ölçüm aralığı',
            '0.0-100.0 °C sıcaklık aralığı',
            '99 set ölçüm hafızası',
            'OHAUS ST320 set elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'a-AB23PH-F',
            'SKU' => '30589823',
            'Ürün tipi' => 'Masa tipi pH metre',
        ],
        'specs' => [
            'Ürün ismi' => 'a-AB23PH-F',
            'Ürün kodu' => '30589823',
            'Seri' => 'OHAUS AquaSearcher',
            'Cihaz tipi' => 'Masa tipi pH metre',
            'Fonksiyon' => 'pH; ORP; Redox ölçümü',
            'Set içeriği' => 'a-AB23PH masa tipi pH metre, ST320 pH elektrodu, pH tampon çözeltileri mini set (4x50 ml)',
            'Set elektrotları' => 'OHAUS ST320 pH elektrodu (83033967)',
            'pH ölçüm aralığı' => '0.00-14.00 pH',
            'pH ölçüm çözünürlüğü' => '0.01 pH',
            'Seçilebilir çözünürlük' => 'Yok',
            'pH ölçüm doğrusallığı' => '+/- 0.01 pH',
            'Tanımlı tampon çözelti grubu' => '2',
            'ORP/Redox ölçüm aralığı' => '+/- 1999 mV',
            'ORP/Redox ölçüm çözünürlüğü' => '1 mV',
            'ORP/Redox ölçüm doğrusallığı' => '+/- 1 mV',
            'ORP/Redox birimleri' => 'mV',
            'Sıcaklık ölçüm aralığı' => '0.0-100.0 °C',
            'Sıcaklık ölçüm çözünürlüğü' => '0.1 °C',
            'Sıcaklık ölçüm doğrusallığı' => '+/- 0.5 °C',
            'Kalibrasyon' => '3 noktaya kadar, 6 pH çözeltisi; yüz işareti; lineer mod',
            'Ekran tipi' => '5 inç aydınlatmalı LCD ekran',
            'Ölçüm bitirme modları' => 'Otomatik durma, sürekli',
            'Ölçüm hafızası' => '99 set',
            'Kalibrasyon hafızası' => 'Son kalibrasyon',
            'Tuş takımı' => 'Membran tip',
            'pH elektrot bağlantısı' => 'BNC',
            'Sıcaklık bağlantısı' => 'Cinch, NTC 30 kΩ',
            'Güç' => 'Evrensel, 100-240 VAC, 50-60 Hz',
            'Çalışma ortam koşulları' => '5-40 °C; 5-80% yoğunlaşmayan',
            'Garanti' => '1 yıl',
            'Görsel durumu' => 'Placeholder',
        ],
        'documents' => [
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/a-AB23PH-F-Masaustu-pHmetre-Sartnamesi.doc'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/30645886_EU-DoC_B_AB23PH_2021-04-19.pdf'],
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/AB23PH_AB23EC_Turkce_Katalog.pdf'],
        ],
    ],
    [
        'name' => 'OHAUS a-AB33EC-F Masa Tipi İletkenlik Ölçer',
        'slug' => 'ohaus-a-ab33ec-f',
        'model' => 'a-AB33EC-F',
        'sku' => '30589829',
        'summary' => 'OHAUS AquaSearcher a-AB33EC-F; iletkenlik, TDS, tuzluluk ve dirençlilik ölçümü için Türkçe menülü, 1000 ölçüm hafızalı masa tipi iletkenlik ölçerdir.',
        'body' => 'OHAUS AquaSearcher serisi a-AB33EC-F masa tipi iletkenlik ölçer; Türkçe menü, dokunmatik tuş takımı, 6.5 inç geniş LCD ekran, 1000 adet ölçüm hafızası, otomatik tampon çözelti tanıma ve otomatik sıcaklık kompanzasyonu ile laboratuvar iletkenlik, TDS, tuzluluk ve dirençlilik ölçümleri için kullanılır. Set içeriğinde a-AB33EC masa tipi iletkenlik ölçer, STCON7 iletkenlik elektrodu ve 84 µS/cm iletkenlik standart solüsyon seti yer alır.',
        'features' => [
            'İletkenlik, TDS, tuzluluk ve dirençlilik ölçümü',
            '0.001 µS/cm-1000 mS/cm ölçüm aralığı',
            '0.001 µS/cm minimum çözünürlük',
            '-5.0-110.0 °C sıcaklık aralığı',
            'Türkçe menü ve kapasitif dokunmatik tuş takımı',
            '1000 adet veri seti hafızası',
            'OHAUS STCON7 set elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'a-AB33EC-F',
            'SKU' => '30589829',
            'Ürün tipi' => 'Masa tipi iletkenlik ölçer',
        ],
        'specs' => array_replace($importedSpecs('ohaus-a-ab33ec-f'), [
            'Ürün ismi' => 'a-AB33EC-F',
            'Ürün kodu' => '30589829',
            'Seri' => 'OHAUS AquaSearcher',
            'Cihaz tipi' => 'Masa tipi iletkenlik ölçer',
            'Fonksiyon' => 'İletkenlik, TDS, Tuzluluk, Dirençlilik',
            'Ölçüm aralığı özeti' => '0.001 µS/cm-1000 mS/cm',
            'Ölçüm çözünürlüğü özeti' => '0.001 µS/cm',
            'Sıcaklık aralığı özeti' => '-5.0-110.0 °C',
            'Set elektrodu' => 'OHAUS STCON7',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/Datasheet_AB33EC_EN.pdf'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/30645887_EU-DoC_A_AB23EC_2021-02-08-1.pdf'],
        ],
    ],
    [
        'name' => 'OHAUS a-AB33M1-F Masa Tipi Multiparametre Ölçer',
        'slug' => 'ohaus-a-ab33m1-f',
        'model' => 'a-AB33M1-F',
        'sku' => '30589825',
        'summary' => 'OHAUS AquaSearcher a-AB33M1-F; pH ve iletkenlik ölçümü için Türkçe menülü, 1000 ölçüm hafızalı masa tipi multiparametre ölçerdir.',
        'body' => 'OHAUS AquaSearcher serisi a-AB33M1-F masa tipi multiparametre ölçer; pH, ORP, Redox, iletkenlik, TDS, tuzluluk ve dirençlilik ölçümleri için kullanılır. Türkçe menü, dokunmatik tuş takımı, 6.5 inç geniş LCD ekran, otomatik tampon tanıma ve otomatik sıcaklık kompanzasyonu ile pH/EC laboratuvar uygulamalarına uygundur. Set içeriğinde ST310 pH elektrodu ve STCON3 iletkenlik elektrodu bulunur.',
        'features' => [
            'pH ve iletkenlik multiparametre ölçümü',
            '-2.00-20.00 pH ölçüm aralığı',
            '0.001 µS/cm-500 mS/cm iletkenlik ölçüm aralığı',
            '-5.0-110.0 °C sıcaklık aralığı',
            'Türkçe menü ve dokunmatik tuş takımı',
            '1000 adet veri seti hafızası',
            'OHAUS ST310 ve STCON3 set elektrotları',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'a-AB33M1-F',
            'SKU' => '30589825',
            'Ürün tipi' => 'Masa tipi multiparametre ölçer',
        ],
        'specs' => array_replace($importedSpecs('ohaus-a-ab33m1-f'), [
            'Ürün ismi' => 'a-AB33M1-F',
            'Ürün kodu' => '30589825',
            'Seri' => 'OHAUS AquaSearcher',
            'Cihaz tipi' => 'Masa tipi multiparametre ölçer',
            'Fonksiyon' => 'pH, İletkenlik',
            'pH ölçüm aralığı özeti' => '-2.00-20.00 pH',
            'pH ölçüm çözünürlüğü özeti' => '0.1 / 0.01 pH',
            'İletkenlik ölçüm aralığı özeti' => '0.001 µS/cm-500 mS/cm',
            'İletkenlik ölçüm çözünürlüğü özeti' => '0.001 µS/cm',
            'Sıcaklık aralığı özeti' => '-5.0-110.0 °C',
            'Set elektrodu' => 'OHAUS ST310, OHAUS STCON3',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/Datasheet_AB33EC_EN.pdf'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/30661421_EU_DoC_A_AB33M_2021-02-08_pdf.pdf'],
        ],
    ],
    [
        'name' => 'OHAUS a-AB33PH-F Masa Tipi pH Metre',
        'slug' => 'ohaus-a-ab33ph-f',
        'model' => 'a-AB33PH-F',
        'sku' => '30589827',
        'summary' => 'OHAUS AquaSearcher a-AB33PH-F; pH, ORP ve Redox ölçümü için Türkçe menülü, 1000 ölçüm hafızalı masa tipi pH metredir.',
        'body' => 'OHAUS AquaSearcher serisi a-AB33PH-F masa tipi pH metre; Türkçe menü, dokunmatik tuş takımı, 6.5 inç geniş LCD ekran, 1000 adet ölçüm hafızası, otomatik tampon çözelti tanıma ve otomatik sıcaklık kompanzasyonu ile laboratuvar pH, ORP ve Redox ölçümleri için kullanılır. Set içeriğinde a-AB33PH masa tipi pH metre, ST310 pH elektrodu ve pH tampon çözeltileri mini seti yer alır.',
        'features' => [
            'pH, ORP ve Redox ölçümü',
            '-2.00-16.00 pH ölçüm aralığı',
            '0.1 / 0.01 pH ölçüm çözünürlüğü',
            '-5.0-110.0 °C sıcaklık aralığı',
            'Türkçe menü ve dokunmatik tuş takımı',
            '1000 adet veri seti hafızası',
            'OHAUS ST310 set elektrodu',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'a-AB33PH-F',
            'SKU' => '30589827',
            'Ürün tipi' => 'Masa tipi pH metre',
        ],
        'specs' => array_replace($importedSpecs('ohaus-a-ab33ph-f'), [
            'Ürün ismi' => 'a-AB33PH-F',
            'Ürün kodu' => '30589827',
            'Seri' => 'OHAUS AquaSearcher',
            'Cihaz tipi' => 'Masa tipi pH metre',
            'Fonksiyon' => 'pH; ORP; Redox ölçümü',
            'pH ölçüm aralığı özeti' => '-2.00-16.00 pH',
            'pH ölçüm çözünürlüğü özeti' => '0.1 / 0.01 pH',
            'Sıcaklık aralığı özeti' => '-5.0-110.0 °C',
            'Set elektrodu' => 'OHAUS ST310',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/a-AB33PH-F-Masaustu-pHmetre-Sartnamesi.doc'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/30645886_EU-DoC_B_AB23PH_2021-04-19.pdf'],
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/AB33EC_AB33PH_Turkce_Katalog.pdf'],
        ],
    ],
    [
        'name' => 'OHAUS a-AB41PH-F Masa Tipi pH Metre',
        'slug' => 'ohaus-a-ab41ph-f',
        'model' => 'a-AB41PH-F',
        'sku' => '30589831',
        'summary' => 'OHAUS AquaSearcher a-AB41PH-F; yüksek çözünürlüklü pH, ORP ve Redox ölçümü için Türkçe menülü masa tipi pH metredir.',
        'body' => 'OHAUS AquaSearcher serisi a-AB41PH-F masa tipi pH metre; yüksek çözünürlükte tutarlı ölçümler, Türkçe menü, dokunmatik tuş takımı, 6.5 inç geniş LCD ekran, 1000 adet ölçüm hafızası, otomatik tampon çözelti tanıma ve otomatik sıcaklık kompanzasyonu ile laboratuvar pH, ORP ve Redox ölçümleri için kullanılır. Set içeriğinde ST410 pH elektrodu, STTEMP sıcaklık probu, pH tampon çözeltileri ve AS20 kompakt karıştırıcı bulunur.',
        'features' => [
            'pH, ORP ve Redox ölçümü',
            '-2.000-20.000 pH ölçüm aralığı',
            '0.1 / 0.01 / 0.001 pH ölçüm çözünürlüğü',
            '-10.0-125.0 °C sıcaklık aralığı',
            'Türkçe menü ve kapasitif dokunmatik tuş takımı',
            '10 set kalibrasyon hafızası',
            'OHAUS ST410 ve STTEMP set elektrotları',
        ],
        'metadata' => [
            'Marka' => 'Ohaus',
            'Kategori' => 'pH & İletkenlik',
            'Alt kategori' => 'Masa Tipi Cihazlar',
            'Model' => 'a-AB41PH-F',
            'SKU' => '30589831',
            'Ürün tipi' => 'Masa tipi pH metre',
        ],
        'specs' => array_replace($importedSpecs('ohaus-a-ab41ph-f'), [
            'Ürün ismi' => 'a-AB41PH-F',
            'Ürün kodu' => '30589831',
            'Seri' => 'OHAUS AquaSearcher',
            'Cihaz tipi' => 'Masa tipi pH metre',
            'Fonksiyon' => 'pH; ORP; Redox ölçümü',
            'pH ölçüm aralığı özeti' => '-2.000-20.000 pH',
            'pH ölçüm çözünürlüğü özeti' => '0.1 / 0.01 / 0.001 pH',
            'Sıcaklık aralığı özeti' => '-10.0-125.0 °C',
            'Set elektrodu' => 'OHAUS ST410, OHAUS STTEMP',
            'Görsel durumu' => 'Placeholder',
        ]),
        'documents' => [
            ['title' => 'Broşür', 'type' => 'catalog', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/Datasheet_AB41PH_EN.pdf'],
            ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/a-AB41PH-F-Masaustu-pHmetre-Sartnamesi.doc'],
            ['title' => 'CE Belgesi', 'type' => 'certificate', 'url' => 'https://biltekas.com/wp-content/uploads/2021/08/30645886_EU-DoC_B_AB23PH_2021-04-19-1.pdf'],
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
        'sort_order' => 2100 + (($index + 1) * 10),
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

echo "Seeded " . count($products) . " Ohaus pH and conductivity products.\n";
