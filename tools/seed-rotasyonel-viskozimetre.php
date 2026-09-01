<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Rotasyonel Viskozimetre',
    'slug' => 'rotasyonel-viskozimetre',
    'summary' => 'Sıvı ve yarı akışkan numunelerde rotasyonel ölçüm prensibiyle viskozite ölçümü yapan laboratuvar cihazları.',
    'aliases' => ['Rotasyonel Viskozimetre', 'Rotasyonal Viskometre', 'Rotational Viscometer'],
];

$makeProduct = function (string $name, string $slug, string $model, string $system, string $range, string $spindleSet): array {
    return [
        'name' => $name,
        'slug' => $slug,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => "Lamy First Plus rotasyonel viskozimetre; {$system} spindle set ile {$range} viskozite aralığında, 0.3 - 250 rpm hız ve 7 inç dokunmatik ekranla viskozite ölçümü için kullanılır.",
        'body' => 'Lamy First Plus rotasyonel viskozimetre, modern yaysız ölçüm teknolojisi ve sağlam tasarımıyla hassas ve güvenilir viskozite ölçümü için geliştirilmiştir. 7 inç dokunmatik ekranı tüm parametrelerin aynı anda takip edilmesini kolaylaştırır. Eğitim, gıda, kozmetik, eczane, kimya, petrol ürünleri ve kalite kontrol laboratuvarlarında farklı numune tipleri için değerlendirilebilir.',
        'features' => [
            'Modern yaysız ölçüm teknolojisi',
            "{$system} spindle set ile geniş viskozite aralığı",
            '7 inç dokunmatik renkli ekran',
            'RS232 ve USB bağlantısı',
            'PT100 sıcaklık sensörü',
            'Kullanıcı şifresi ve ölçüm parametresi koruma modu',
        ],
        'metadata' => [
            'Marka' => 'Lamy',
            'Kategori' => 'Rotasyonel Viskozimetre',
            'Üst kategori' => 'Viskozimetre',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Viskozite aralığı' => $range,
            'Spindle set' => $system,
            'Kullanım alanı' => 'Viskozite ölçümü',
        ],
        'specs' => [
            'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
            'Hız' => '0.3 - 250 rpm limitsiz',
            'Tork aralığı' => 'Standart versiyon: 0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçümü' => "{$system} sistemi: {$range}",
            'Spindle set' => $spindleSet,
            'Sıcaklık' => '-50...300 °C arası sıcaklık değeri gösteren PT100 sensör',
            'Ölçüm sistemi' => 'MS DIN, MS ASTM, MS BV, MS VANE, MS SV, MS CP',
            'Sıcaklık kontrolü' => 'EVA DIN, EVA LR-BV, RT1, CP1',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu, PCL/5 uyumlu',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce, Rusça, İspanyolca',
            'Emniyet ve gizlilik' => 'Kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
            'Ağırlık' => '6.7 kg',
        ],
        'image_alt' => "{$name} ürün görseli",
    ];
};

$productRecord = function (string $name, string $slug, string $model, string $summary, string $body, array $features, array $metadata, array $specs, string $imageAlt): array {
    return [
        'name' => $name,
        'slug' => $slug,
        'model' => $model,
        'sku' => null,
        'old_url' => "https://www.labor.com.tr/urun/{$slug}",
        'summary' => $summary,
        'body' => $body,
        'features' => $features,
        'metadata' => [
            'Marka' => 'Lamy',
            'Kategori' => 'Rotasyonel Viskozimetre',
            'Üst kategori' => 'Viskozimetre',
            'Model' => $model,
            'SKU' => 'Yayın öncesi netleştirilecek',
            ...$metadata,
        ],
        'specs' => $specs,
        'image_alt' => $imageAlt,
    ];
};

$bOneBody = 'Lamy B-One Plus rotasyonel viskozimetre, modern yaysız ölçüm teknolojisi ve sağlam tasarımıyla hassas ve güvenilir viskozite ölçümü için geliştirilmiştir. 7 inç dokunmatik ekranı tüm parametrelerin aynı anda takip edilmesini kolaylaştırır. Ayrıca sonuç hafızası ve USB üzerinden veri transferi özellikleri vardır. Eğitim, gıda, kozmetik, eczane, kimya, petrol ürünleri ve kalite kontrol laboratuvarlarında farklı numune tipleri için değerlendirilebilir.';
$bOneFeatures = [
    'Modern yaysız ölçüm teknolojisi',
    '7 inç dokunmatik renkli ekran',
    'Sonuç hafızası',
    'USB üzerinden veri transferi',
    'Operatör fonksiyonu ve ölçüm parametresi koruma modu',
];
$bOneSpecs = [
    'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
    'Hız' => '0.3 - 250 rpm limitsiz',
    'Tork aralığı' => 'Standart versiyon: 0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
    'Doğrusallık' => 'Tam skalada +/- %1',
    'Tekrarlanabilirlik' => '+/- %0.2',
    'Ekran' => '7 inç dokunmatik renkli ekran',
    'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
    'Veri aktarımı' => 'USB üzerinden veri transferi',
    'Sonuç hafızası' => 'Sonuç hafızası özelliği',
    'Gerilim' => '90-240 VAC, 50/60 Hz',
    'Dil' => 'Türkçe, Fransızca, İngilizce, Rusça, İspanyolca, Almanca',
    'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
    'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
    'Ağırlık' => '6.7 kg',
];

$products = [
    $makeProduct(
        'Lamy First Plus Rotasyonel Viskozimetre R2-R7 Spindle Set',
        'lamy-first-plus-rotasyonel-viskozimetre-r2-r7-spindle-set',
        'First Plus R2-R7',
        'R2 - R7',
        '100 - 180.000.000 mPa.s',
        'R-2 to R-7 spindle set ile'
    ),
    $makeProduct(
        'Lamy First Plus LR Rotasyonel Viskozimetre L1-L4 Spindle Set',
        'lamy-first-plus-lr-rotasyonel-viskozimetre-l1-l4-spindle-set',
        'First Plus LR L1-L4',
        'L1 - L4',
        '15 - 22.000.000 mPa.s',
        'L1 - L4 spindle set ile'
    ),
    [
        'name' => 'Lamy B-One Plus Rotasyonel Viskozimetre L1-L4 Spindle Set',
        'slug' => 'lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set',
        'model' => 'B-One Plus L1-L4',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set',
        'summary' => 'Lamy B-One Plus rotasyonel viskozimetre; L1-L4 spindle set ile 15 - 22.000.000 mPa.s viskozite aralığında, 0.3 - 250 rpm hız, 7 inç dokunmatik ekran, sonuç hafızası ve USB veri transferiyle viskozite ölçümü için kullanılır.',
        'body' => 'Lamy B-One Plus rotasyonel viskozimetre, modern yaysız ölçüm teknolojisi ve sağlam tasarımıyla hassas ve güvenilir viskozite ölçümü için geliştirilmiştir. 7 inç dokunmatik ekranı tüm parametrelerin aynı anda takip edilmesini kolaylaştırır. Ayrıca sonuç hafızası ve USB üzerinden veri transferi özellikleri vardır. Eğitim, gıda, kozmetik, eczane, kimya, petrol ürünleri ve kalite kontrol laboratuvarlarında farklı numune tipleri için değerlendirilebilir.',
        'features' => [
            'Modern yaysız ölçüm teknolojisi',
            'L1-L4 spindle set ile 15 - 22.000.000 mPa.s aralık',
            '7 inç dokunmatik renkli ekran',
            'Sonuç hafızası',
            'USB üzerinden veri transferi',
            'Operatör fonksiyonu ve ölçüm parametresi koruma modu',
        ],
        'metadata' => [
            'Marka' => 'Lamy',
            'Kategori' => 'Rotasyonel Viskozimetre',
            'Üst kategori' => 'Viskozimetre',
            'Model' => 'B-One Plus L1-L4',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Viskozite aralığı' => '15 - 22.000.000 mPa.s',
            'Spindle set' => 'L1 - L4',
            'Kullanım alanı' => 'Viskozite ölçümü',
        ],
        'specs' => [
            'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
            'Hız' => '0.3 - 250 rpm limitsiz',
            'Tork aralığı' => 'Standart versiyon: 0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'L1 - L4 sistemi: 15 - 22.000.000 mPa.s; R2 - R7 sistemi: 200 - 240.000.000 mPa.s; KU sistemi: 40-141 KU',
            'Spindle set' => 'L-1 to L-4 spindle set ile',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
            'Veri aktarımı' => 'USB üzerinden veri transferi',
            'Sonuç hafızası' => 'Sonuç hafızası özelliği',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Türkçe, Fransızca, İngilizce, Rusça, İspanyolca, Almanca',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
            'Ağırlık' => '6.7 kg',
        ],
        'image_alt' => 'Lamy B-One Plus rotasyonel viskozimetre L1 L4 spindle set ürün görseli',
    ],
    $productRecord(
        'Lamy B-One Plus Rotasyonel Viskozimetre KU 1-10 Spindle',
        'lamy-b-one-plus-rotasyonel-viskozimetre-ku-1-10-spindle',
        'B-One Plus KU 1-10',
        'Lamy B-One Plus rotasyonel viskozimetre; KU 1-10 spindle ile 40-141 KU aralığında, 0.3 - 250 rpm hız, sonuç hafızası ve USB veri transferiyle viskozite ölçümü için kullanılır.',
        $bOneBody,
        [
            ...$bOneFeatures,
            'KU 1-10 spindle ile 40-141 KU ölçüm aralığı',
        ],
        [
            'Viskozite aralığı' => '40-141 KU',
            'Spindle set' => 'KU 1-10',
            'Kullanım alanı' => 'KU viskozite ölçümü',
        ],
        [
            ...$bOneSpecs,
            'Viskozite ölçüm aralığı' => 'L1 - L4 sistemi: 15 - 6.000.000 mPa.s; R2 - R7 sistemi: 100 - 180.000.000 mPa.s; KU sistemi: 40-141 KU',
            'Spindle set' => 'KU 1-10 spindle ile',
        ],
        'Lamy B-One Plus rotasyonel viskozimetre KU 1-10 spindle ürün görseli'
    ),
    $productRecord(
        'Lamy B-One Plus Rotasyonel Viskozimetre R2-R7 Spindle Set',
        'lamy-b-one-plus-rotasyonel-viskozimetre-r2-r7-spindle-set',
        'B-One Plus R2-R7',
        'Lamy B-One Plus rotasyonel viskozimetre; R2-R7 spindle set ile 200 - 240.000.000 mPa.s aralığında, 0.3 - 250 rpm hız, sonuç hafızası ve USB veri transferiyle viskozite ölçümü için kullanılır.',
        $bOneBody,
        [
            ...$bOneFeatures,
            'R2-R7 spindle set ile 200 - 240.000.000 mPa.s aralık',
        ],
        [
            'Viskozite aralığı' => '200 - 240.000.000 mPa.s',
            'Spindle set' => 'R2 - R7',
            'Kullanım alanı' => 'Viskozite ölçümü',
        ],
        [
            ...$bOneSpecs,
            'Viskozite ölçüm aralığı' => 'L1 - L4 sistemi: 15 - 22.000.000 mPa.s; R2 - R7 sistemi: 200 - 240.000.000 mPa.s; KU sistemi: 40-141 KU',
            'Spindle set' => 'R-2 to R-7 spindle set ile',
        ],
        'Lamy B-One Plus rotasyonel viskozimetre R2 R7 spindle set ürün görseli'
    ),
    $productRecord(
        'Lamy B-One Touch Taşınabilir Rotasyonel Viskozimetre',
        'lamy-b-one-touch-tasinabilir-rotasyonel-viskozimetre',
        'B-One Touch Taşınabilir',
        'Lamy B-One Touch taşınabilir rotasyonel viskozimetre; ASTM / ISO 2555 ve ASTM D 562 / TS 5809 normları doğrultusunda doğrudan kazandan ölçüm yapmaya imkan veren portatif viskozimetredir.',
        'Lamy B-One Touch taşınabilir rotasyonel viskozimetre, doğrudan kazandan ölçüm yapılması gereken saha ve üretim uygulamalarında kullanılabilir. Cihaz boyun askısı ve taşıma çantası ile birlikte gelir; ölçüm sistemine uygun spindle/ölçüm uçları isteğe göre ayrıca sipariş edilmelidir.',
        [
            'Taşınabilir rotasyonel viskozimetre',
            'Doğrudan kazandan ölçüm imkanı',
            'Boyun askısı ve taşıma çantası dahildir',
            'ASTM / ISO 2555 ve ASTM D 562 / TS 5809 uygulamaları',
            'Spindle seti dahil değildir',
        ],
        [
            'Viskozite aralığı' => 'L1-L4: 15 - 6.000.000 mPa.s; R2-R7: 100 - 180.000.000 mPa.s; KU: 40-141 KU',
            'Spindle set' => 'Dahil değildir',
            'Kullanım alanı' => 'Taşınabilir viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'ASTM ya da KU sistemli portatif rotasyonel viskozimetre',
            'Hız' => '0.3 - 250 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => '0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'L1 - L4 sistemi: 15 - 6.000.000 mPa.s; R2 - R7 sistemi: 100 - 180.000.000 mPa.s; KU sistemi: 40-141 KU',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, hız, tork, ölçüm süresi, ölçüm geometrisi, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Taşıma çantası' => 'Dahildir',
            'Spindle seti' => 'Dahil değildir',
            'Ölçüler' => 'Kafa: 85 x 310 mm; kutu: 265 x 125 x 65 mm',
            'Ağırlık' => '2 kg',
        ],
        'Lamy B-One Touch taşınabilir rotasyonel viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy B-One Touch Rotasyonel Viskozimetre',
        'lamy-b-one-touch-rotasyonel-viskozimetre',
        'B-One Touch',
        'Lamy B-One Touch rotasyonel viskozimetre; modern yaysız ölçüm teknolojisi, sağlam tasarım ve 7 inç dokunmatik ekran ile laboratuvar viskozite ölçümleri için kullanılır.',
        'Lamy B-One Touch rotasyonel viskozimetre; eğitim, gıda, kozmetik, eczane, kimya ve petrol ürünleri alanlarında viskozite ölçümü için değerlendirilebilir. Spindle seti ürün ihtiyacına göre ayrıca belirlenmelidir.',
        [
            'Modern yaysız ölçüm teknolojisi',
            '7 inç dokunmatik renkli ekran',
            'L1-L4, R2-R7 ve KU sistemleriyle uyumlu ölçüm aralığı',
            'Operatör fonksiyonu ve kilitli mod',
            'Spindle seti dahil değildir',
        ],
        [
            'Viskozite aralığı' => 'L1-L4: 15 - 6.000.000 mPa.s; R2-R7: 100 - 180.000.000 mPa.s; KU: 40-141 KU',
            'Spindle set' => 'Dahil değildir',
            'Kullanım alanı' => 'Laboratuvar viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
            'Hız' => '0.3 - 250 rpm',
            'Tork aralığı' => 'Standart versiyon: 0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'L1 - L4 sistemi: 15 - 6.000.000 mPa.s; R2 - R7 sistemi: 100 - 180.000.000 mPa.s; KU sistemi: 40-141 KU',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Spindle seti' => 'Dahil değildir',
            'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
            'Ağırlık' => '6.7 kg',
        ],
        'Lamy B-One Touch rotasyonel viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy RM100 Touch Taşınabilir Rotasyonel Viskozimetre',
        'lamy-rm100-touch-tasinabilir-rotasyonel-viskozimetre',
        'RM100 Touch Taşınabilir',
        'Lamy RM100 Touch taşınabilir rotasyonel viskozimetre; ISO 3219 ve farklı ölçüm sistemleriyle yüksek kesme hızlarında gerçek viskozite ölçümü için tasarlanmış portatif viskozimetredir.',
        'Lamy RM100 Touch taşınabilir rotasyonel viskozimetre, modüler tasarımı sayesinde ISO 3219, ASTM / ISO 2555, ASTM D 562 / TS 5809, Krebs-Stormer ve MS-BV 1-1000 ölçüm sistemleriyle çalıştırılabilir. Entegre sıcaklık sensörü ve boyun askısı ile portatif kullanım için uygundur.',
        [
            'Taşınabilir RM100 Touch viskozimetre',
            '0.3 - 1500 rpm hız seçimi',
            'PT100 sıcaklık sensörü',
            'Taşıma çantası dahildir',
            'Spindle seti dahil değildir',
        ],
        [
            'Viskozite aralığı' => '1 - 540.000.000 mPa.s; KU: 40-250 KU',
            'Spindle set' => 'Dahil değildir',
            'Kullanım alanı' => 'Taşınabilir yüksek kesme viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'ISO 3219, ASTM-ISO2555, BV1-1000 ya da KU sistemli taşınabilir rotasyonel viskozimetre',
            'Hız' => '0.3 - 1500 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => 'Standart versiyon: 0.05 - 30 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'Seçilen ölçüm sistemine bağlı olarak 1 - 540.000.000 mPa.s; KU sistemi: 40-250 KU',
            'Sıcaklık' => 'PT100 sensör ile -50...300 °C',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, sıcaklık, tork, hız, ölçüm süresi, ölçüm geometrisi, hassasiyet seviyesi, tarih/saat; cP veya mPa.s',
            'Analog çıkış' => '4-20 mA, tork aralığı kullanıcı tarafından belirlenir',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Taşıma çantası' => 'Dahildir',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Spindle seti' => 'Dahil değildir',
            'Ölçüler' => 'Kafa: Ø80 mm; yükseklik: 310 mm; kutu: 265 x 125 x 65 mm',
            'Ağırlık' => '2 kg',
        ],
        'Lamy RM100 Touch taşınabilir rotasyonel viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy RM100 Touch Rotasyonel Viskozimetre',
        'lamy-rm100-touch-rotasyonel-viskozimetre',
        'RM100 Touch',
        'Lamy RM100 Touch rotasyonel viskozimetre; ISO 3219 normuna göre yüksek kesme hızlarında gerçek viskozite ölçümü için tasarlanmış modüler laboratuvar viskozimetresidir.',
        'Lamy RM100 Touch rotasyonel viskozimetre, ISO 3219 ve farklı ölçüm sistemleriyle çalışabilir. Modüler tasarım ASTM D 562 / TS 5809, ASTM / ISO 2555, MS-BV 1-1000 ve EVA-CP sıcaklık kontrol standı ile koni-plaka uygulamalarına uyarlanabilir.',
        [
            'ISO 3219 yüksek kesme hızlarında ölçüm',
            '0.3 - 1500 rpm hız seçimi',
            'Modüler ölçüm sistemi yapısı',
            'PT100 sıcaklık sensörü',
            'Analog çıkış, RS232 ve USB bağlantısı',
        ],
        [
            'Viskozite aralığı' => '1 - 540.000.000 mPa.s; KU: 40-250 KU',
            'Kullanım alanı' => 'Yüksek kesme hızlarında viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'ISO 3219, ISO 2884-1, ASTM-ISO2555, BV1-1000 ya da KU sistemli rotasyonel viskozimetre',
            'Hız' => '0.3 - 1500 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => 'Standart versiyon: 0.05 - 30 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'Seçilen ölçüm sistemine bağlı olarak 1 - 540.000.000 mPa.s; KU sistemi: 40-250 KU',
            'Sıcaklık' => 'PT100 sensör ile -50...300 °C',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, sıcaklık, tork, hız, ölçüm süresi, ölçüm geometrisi, hassasiyet seviyesi, tarih/saat; cP veya mPa.s',
            'Analog çıkış' => '4-20 mA, tork aralığı kullanıcı tarafından belirlenir',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; sertleştirilmiş çelik stand: 280 x 200 x 30 mm; paslanmaz çelik direk: 500 mm',
            'Ağırlık' => '6.7 kg',
        ],
        'Lamy RM100 Touch rotasyonel viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy RM100 Touch CP 2000 Koni Plaka Viskozimetre',
        'lamy-rm100-touch-cp-2000-koni-plaka-viskozimetre',
        'RM100 Touch CP 2000',
        'Lamy RM100 Touch CP 2000 koni-plaka viskozimetre; seçilen ölçüm sistemine bağlı olarak 1 - 500.000 mPa.s aralığında viskozite ölçümü için kullanılan cone-plate viscometer modelidir.',
        'Lamy RM100 Touch CP 2000 koni-plaka viskozimetre, koni-plaka ölçüm geometrisiyle hassas viskozite ölçümleri için kullanılır. CP2000 standı Peltier ile 5 - 80 °C, CP2000H standı ise elektrikli ısıtma ile oda sıcaklığından 300 °C seviyesine kadar sıcaklık uygulamaları için değerlendirilebilir.',
        [
            'Koni-plaka ölçüm geometrisi',
            '0.3 - 1500 rpm hız seçimi',
            'CP2000 ve CP2000H stand seçenekleri',
            'Analog çıkış, RS232 ve USB bağlantısı',
            '7 inç dokunmatik renkli ekran',
        ],
        [
            'Viskozite aralığı' => '1 - 500.000 mPa.s',
            'Sıcaklık aralığı' => 'CP2000: 5 - 80 °C; CP2000H: oda sıcaklığı - 300 °C',
            'Kullanım alanı' => 'Koni-plaka viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'Koni-plaka rotasyonel viskozimetre',
            'Hız' => '0.3 - 1500 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => '0.05 - 30 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'Seçilen ölçüm sistemine bağlı olarak 1 - 500.000 mPa.s',
            'Sıcaklık' => 'CP2000: Peltier ile 5 - 80 °C; CP2000H: elektrikli ısıtma ile oda sıcaklığı - 300 °C',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, sıcaklık, tork, hız, ölçüm süresi, ölçüm geometrisi, hassasiyet seviyesi, tarih/saat; cP veya mPa.s',
            'Analog çıkış' => '4-20 mA, tork aralığı kullanıcı tarafından belirlenir',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; CP 2000 standı: 300 x 490 x 630 mm',
            'Ağırlık' => '22 kg',
        ],
        'Lamy RM100 Touch CP 2000 koni plaka viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy RM100 L Touch Viskozimetre',
        'lamy-rm100-l-touch-viskozimetre',
        'RM100 L Touch',
        'Lamy RM100 L Touch viskozimetre; online akış uygulamalarında yüksek akış hızları için boru üzerinde veya düşük akış hızları için dirsek üzerinde çalışabilen daldırma tipi döner viskozimetredir.',
        'Lamy RM100 L Touch viskozimetre, gerçek zamanlı viskozite değerlerinin izlenmesi gereken online proses uygulamalarında kullanılır. Manyetik rakor yapısı mükemmel sızdırmazlık için tasarlanmıştır.',
        [
            'Online proses viskozite ölçümü',
            'Boru veya dirsek üzerinde çalışma',
            'Manyetik rakor ile sızdırmazlık',
            'Gerçek zamanlı viskozite takibi',
            'Analog çıkış, RS232 ve USB bağlantısı',
        ],
        [
            'Viskozite aralığı' => '1 - 500.000 mPa.s',
            'Sıcaklık aralığı' => '-20...100 °C',
            'Kullanım alanı' => 'Online proses viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'Daldırma tipi döner viskozimetre',
            'Hız' => '5 - 600 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => '0.05 - 20 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'Seçilen ölçüm sistemine bağlı olarak 1 - 500.000 mPa.s',
            'Sıcaklık' => 'PT100 sensör ile -20...100 °C',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, sıcaklık, tork, hız, ölçüm süresi, ölçüm geometrisi, hassasiyet seviyesi, tarih/saat; cP veya mPa.s',
            'Analog çıkış' => '4-20 mA, tork aralığı kullanıcı tarafından belirlenir',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Ölçüler' => 'Kafa: 85 x 312 mm; kutu: 120 x 145 x 261 mm',
            'Ağırlık' => '4 kg',
        ],
        'Lamy RM100 L Touch viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy RM100 I Touch Endüstriyel Viskozimetre',
        'lamy-rm100-i-touch-endustriyel-viskozimetre',
        'RM100 I Touch',
        'Lamy RM100 I Touch endüstriyel viskozimetre; sabit seviyeli tanklarda daldırma metoduyla gerçek zamanlı viskozite ve opsiyonel PT100 sensörle sıcaklık takibi için kullanılır.',
        'Lamy RM100 I Touch endüstriyel viskozimetre, sabit seviyeli tank uygulamalarında daldırma tipi döner viskozite ölçümü için tasarlanmıştır. Viskozite ve opsiyonel PT100 sensörü ile sıcaklık değerleri gerçek zamanlı olarak izlenebilir.',
        [
            'Endüstriyel daldırma tipi viskozimetre',
            'Sabit seviyeli tank uygulamaları',
            'Gerçek zamanlı viskozite takibi',
            'Opsiyonel PT100 sıcaklık takibi',
            'Analog çıkış, RS232 ve USB bağlantısı',
        ],
        [
            'Viskozite aralığı' => '1 - 540.000.000 mPa.s',
            'Sıcaklık aralığı' => '-50...300 °C',
            'Kullanım alanı' => 'Endüstriyel proses viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'Daldırma tipi döner viskozimetre',
            'Hız' => '0.3 - 1500 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => '0.05 - 20 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite ölçüm aralığı' => 'Seçilen ölçüm sistemine bağlı olarak 1 - 540.000.000 mPa.s',
            'Sıcaklık' => 'PT100 sensör ile -50...300 °C',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, sıcaklık, tork, hız, ölçüm süresi, ölçüm geometrisi, hassasiyet seviyesi, tarih/saat; cP veya mPa.s',
            'Analog çıkış' => '4-20 mA, tork aralığı kullanıcı tarafından belirlenir',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Ölçüler' => 'Kafa: 85 x 180 mm; kutu: 120 x 145 x 261 mm',
            'Ağırlık' => '3 kg',
        ],
        'Lamy RM100 I Touch endüstriyel viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy First Prodig CP 1000 Rotasyonel Viskozimetre',
        'lamy-first-prodig-cp-1000-rotasyonel-viskozimetre',
        'First Prodig CP 1000',
        'Lamy First Prodig CP 1000 rotasyonel viskozimetre; koni-plaka ölçümleri için CP1000 stand, 3.000.000 - 80.000.000 mPa.s viskozite aralığı ve Türkçe menü seçeneğiyle listelenir.',
        'Lamy First Prodig CP1000, koni-plaka ölçümleri için destek içeren modüler bir rotasyonel viskozimetredir. Ortam sıcaklığındaki ölçümler için uygundur ve sirkülasyon banyosu ile birleştirildiğinde numune sıcaklığının kontrol edilmesine imkan tanır. MS-RV, MS KREBS ve MS VANE gibi farklı ölçüm sistemleriyle kullanılabilir.',
        [
            'CP1000, koni plaka ölçümü için stand',
            'AC265 kaplinli hızlı bağlantı',
            'Boşluk ayarı için manuel kaldırma',
            'Hız veya kesme hızı kontrolü',
            'Programlama ve kayıt yöntemi',
            'Sabit, adım adım veya rampa yöntemleri',
            'Ekranda doğrudan eğri gösterimi',
            'Regresyon ile doğrudan analiz',
            'Doğrudan rapor düzenleme',
            'Durma zamanı ile doğrudan ölçüm',
            'Kullanıcı ve kilitli mod',
            'Veri kaydı ve USB aktarımı',
            'Ekranda tork ölçer',
            'Entegre sıcaklık probu',
            'Yazıcı bağlantısı',
            'RheoTex yazılımı ile uyumluluk',
            'Mobil cihazlara göre viskozite limitleri ve hız gösterimi',
        ],
        [
            'Viskozite aralığı' => '3.000.000 - 80.000.000 mPa.s',
            'Sıcaklık aralığı' => '+5...+65 °C uygulama bilgisi; PT100 ile -50...300 °C gösterim',
            'Dil' => 'Türkçe menü seçeneği',
            'Kullanım alanı' => 'Koni-plaka viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'ASTM ya da KU sistemli rotasyonel viskozimetre',
            'Hız' => '0.3 - 250 rpm limitsiz',
            'Tork aralığı' => '0.05 - 13 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Viskozite aralığı' => '3.000.000 - 80.000.000 mPa.s',
            'Sıcaklık' => 'PT100 sensör ile -50...300 °C sıcaklık gösterimi',
            'Standartlar' => 'ASTM D4287; BS 3900; DIN 3219; DIN 52007-1; DIN 53019-1; DIN 54453; ISO 2884; ISO 3219; ISO 10364-12',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, hız, tork, spindle, hassasiyet seviyesi, tarih/saat; viskozite birimi cP veya mPa.s',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Türkçe, Fransızca, İngilizce, Almanca',
            'Emniyet ve gizlilik' => '4 haneli kullanıcı kodu ve ölçüm koşullarını kilitleyen korumalı mod',
            'Ölçüler' => '320 x 550 x 200 mm',
            'Ağırlık' => '14 kg',
        ],
        'Lamy First Prodig CP 1000 rotasyonel viskozimetre ürün görseli'
    ),
    $productRecord(
        'Lamy RM100 Dokunmatik Jel Zamanlayıcı',
        'lamy-rm100-dokunmatik-jel-zamanlayici',
        'RM100 Dokunmatik Jel Zamanlayıcı',
        'Lamy RM100 dokunmatik jel zamanlayıcı; seçilen reçine ve ölçeklenebilir jel ürünlerinin sürelerini belirlemede kullanılan viskozimetre/jel zamanlayıcıdır.',
        'Lamy RM100 dokunmatik jel zamanlayıcı, reçine ve jel ürünlerinin çalışma sürelerinin belirlenmesi gereken laboratuvar ve kalite kontrol uygulamalarında kullanılır. 0.3 - 1500 rpm hız aralığı, 0.05 - 40 mNm tork aralığı ve PT100 sıcaklık sensörüyle listelenir.',
        [
            'Viskozimetre / jel zamanlayıcısı',
            '0.3 - 1500 rpm hız seçimi',
            '0.05 - 40 mNm tork aralığı',
            'PT100 sıcaklık sensörü',
            'Analog çıkış, RS232 ve USB bağlantısı',
        ],
        [
            'Viskozite aralığı' => '100 - 5.000.000.000 mPa.s',
            'Kullanım alanı' => 'Jel zamanı ve reçine süre ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'Viskozimetre / jel zamanlayıcısı',
            'Hız' => '0.3 - 1500 rpm arasında sınırsız hız seçimi',
            'Tork aralığı' => '0.05 - 40 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Sıcaklık' => 'PT100 sensör ile -50...300 °C',
            'Viskozite ölçüm aralığı' => 'Seçilen ölçüm sistemine bağlı olarak 100 - 5.000.000.000 mPa.s',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Viskozite, hız, tork, sıcaklık, zaman, geometri, hassasiyet seviyesi, tarih/saat; cP veya mPa.s',
            'Analog çıkış' => '4-20 mA, tork aralığı kullanıcı tarafından belirlenir',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'Emniyet ve gizlilik' => 'Operatör fonksiyonu, kullanıcı şifresi ve ölçüm parametrelerini kilitleyen koruma modu',
            'Ölçüler' => 'Kafa: 180 x 135 x 250 mm; jel zamanlayıcı stand: 280 x 200 x 630 mm; paslanmaz çelik direk: 500 mm',
            'Ağırlık' => '15 kg',
        ],
        'Lamy RM100 dokunmatik jel zamanlayıcı ürün görseli'
    ),
    $productRecord(
        'Lamy First Touch Rotasyonel Viskozimetre',
        'lamy-first-touch-rotasyonel-viskozimetre',
        'First Touch',
        'Lamy First Touch rotasyonel viskozimetre; ultra hassas ölçümler için 0.005 - 0.8 mNm LR tork aralığı, PT100 sıcaklık sensörü ve USB bağlantısıyla kullanılır.',
        'Lamy First Touch rotasyonel viskozimetre; gıda, otomotiv, boya, kaplama, ilaç ve kimya sektörlerinde ultra hassas viskozite ölçümleri için değerlendirilebilir. Kullanılan spindle setine bağlı olarak 3 - 180.000.000 mPa.s aralığında çalışır.',
        [
            'Ultra hassas rotasyonel viskozimetre',
            'LR versiyonda 0.005 - 0.8 mNm tork aralığı',
            'PT100 sıcaklık sensörü',
            'RS232 ve USB bağlantısı',
            'FirstRM tiksotropik örnek ölçüm zamanı düzenleme',
        ],
        [
            'Viskozite aralığı' => '3 - 180.000.000 mPa.s',
            'Kullanım alanı' => 'Ultra hassas viskozite ölçümü',
        ],
        [
            'Ölçüm prensibi' => 'Rotasyonel viskozimetre',
            'Hız' => '0.3 - 250 rpm',
            'Tork aralığı' => '0.05 - 13 mNm; LR versiyonu: 0.005 - 0.8 mNm',
            'Doğrusallık' => 'Tam skalada +/- %1',
            'Tekrarlanabilirlik' => '+/- %0.2',
            'Sıcaklık' => 'PT100 sensör ile -50...300 °C',
            'Ekran' => '7 inç dokunmatik renkli ekran',
            'Dijital gösterge' => 'Sıcaklık, hız, tork, MS, viskozite, zaman, tarih ve saat',
            'Viskozite aralığı' => 'Kullanılan spindle setine bağlı olarak 3 - 180.000.000 mPa.s',
            'Gerilim' => '90-240 VAC, 50/60 Hz',
            'Dil' => 'Fransızca, İngilizce',
            'PC bağlantısı' => 'RS232 portu ve USB',
            'Yazıcı bağlantısı' => 'USB host portu',
            'Diğer özellikler' => 'FirstRM tiksotropik örnekler için ölçüm zamanını düzenlemeyi sağlar; yazıcı aralığı ilave edilebilir',
            'Ölçüler' => '122 x 135 x 660 mm',
            'Ağırlık' => '6 kg',
        ],
        'Lamy First Touch rotasyonel viskozimetre ürün görseli'
    ),
];

$imageFor = function (string $slug) use ($root): ?string {
    foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
        $relative = "images/products/{$slug}.{$extension}";

        if (is_file($root . '/public/' . $relative)) {
            return $relative;
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
$stmt->execute(['slug' => 'lamy']);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    throw new RuntimeException('Lamy markası bulunamadı.');
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
        'image' => $imageFor($product['slug']),
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

        continue;
    }

    $insertProduct->execute([
        'category_id' => $categoryId,
        'slug' => $product['slug'],
        'created_at' => $now,
        ...$payload,
    ]);
}

$db->commit();

echo 'category_id=' . $categoryId . PHP_EOL;
echo 'brand_id=' . $brandId . PHP_EOL;
foreach ($products as $product) {
    echo 'product_slug=' . $product['slug'] . PHP_EOL;
    echo 'image=' . ($imageFor($product['slug']) ?: 'missing') . PHP_EOL;
}
echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
