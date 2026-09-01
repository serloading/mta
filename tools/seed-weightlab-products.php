<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$categories = [
    'hassas-teraziler' => [
        'name' => 'Hassas Teraziler',
        'summary' => 'Rutin laboratuvar tartımları için hassas terazi modelleri.',
        'aliases' => ['Hassas Terazi', 'Hassas Teraziler', 'Precision Balance'],
    ],
    'nem-tayin' => [
        'name' => 'Nem Tayin',
        'summary' => 'Numune nem oranı ölçümü için nem tayin cihazları.',
        'aliases' => ['Nemtayin', 'Nem Tayin Cihazları', 'Nem Tayin Cihazı'],
    ],
    'isitmasiz-manyetik-karistirici' => [
        'name' => 'Isıtmasız Manyetik Karıştırıcılar',
        'summary' => 'Yalnızca karıştırma işlemi için kullanılan manyetik karıştırıcılar.',
        'aliases' => ['Isıtmasız Manyetik Karıştırıcı', 'Isitmasiz Manyetik Karistirici'],
    ],
    'isitmali-manyetik-karistirici' => [
        'name' => 'Isıtmalı Manyetik Karıştırıcılar',
        'summary' => 'Karıştırma ile birlikte kontrollü ısıtma sağlayan manyetik karıştırıcılar.',
        'aliases' => ['Isıtmalı Manyetik Karıştırıcı', 'Isitmali Manyetik Karistirici'],
    ],
    'hot-plate' => [
        'name' => 'Hot Plate',
        'summary' => 'Laboratuvar ısıtma uygulamaları için hot plate cihazları.',
        'aliases' => ['Hot Plate', 'Isıtıcı Tabla', 'Isitici Tabla'],
    ],
    'vorteks-karistiricilar' => [
        'name' => 'Vorteks Karıştırıcılar',
        'summary' => 'Tüp ve küçük hacimli numuneler için vorteks karıştırıcılar.',
        'aliases' => ['Vorteks Karıştırıcı', 'Vorteks Karıştırıcılar'],
    ],
    'mekanik-karistirici' => [
        'name' => 'Mekanik Karıştırıcılar',
        'summary' => 'Yüksek hacimli veya yoğun numuneler için mekanik karıştırıcılar.',
        'aliases' => ['Mekanik Karıştırıcı', 'Mekanik Karıştırıcılar'],
    ],
    'balon-isiticilar' => [
        'name' => 'Balon Isıtıcılar',
        'summary' => 'Laboratuvar cam balonları için kontrollü ısıtma çözümleri.',
        'aliases' => ['Balon Isıtıcı', 'Mantle Heater'],
    ],
    'su-banyosu' => [
        'name' => 'Su Banyosu',
        'summary' => 'Sıcaklık kontrollü klasik laboratuvar su banyosu cihazları.',
        'aliases' => ['Su Banyosu', 'Water Bath'],
    ],
    'ultrasonik-banyo' => [
        'name' => 'Ultrasonik Banyo',
        'summary' => 'Temizleme ve numune hazırlama süreçleri için ultrasonik banyolar.',
        'aliases' => ['Ultrasonik Banyo', 'Ultrasonik Banyolar'],
    ],
    'santrifujler' => [
        'name' => 'Santrifüjler',
        'summary' => 'Numune ayırma süreçleri için laboratuvar santrifüjleri.',
        'aliases' => ['Santrifüjler', 'Santrifujler', 'Santrifüj', 'Centrifuge'],
    ],
    'rotator-calkalayici' => [
        'name' => 'Rotatör Çalkalayıcı',
        'summary' => 'Rotasyon ve çalkalama uygulamaları için laboratuvar çalkalayıcı cihazları.',
        'aliases' => ['Rotatör Çalkalayıcı', 'Rotator Calkalayici', 'Rotator'],
    ],
    'sogutmali-inkubator' => [
        'name' => 'Soğutmalı İnkübatör',
        'summary' => 'Soğutmalı inkübasyon uygulamaları için sıcaklık kontrollü cihazlar.',
        'aliases' => ['Soğutmalı İnkübatör', 'Sogutmali Inkubator'],
    ],
    'inkubatorler' => [
        'name' => 'İnkübatörler',
        'summary' => 'Sıcaklık kontrollü inkübasyon uygulamaları için cihazlar.',
        'aliases' => ['İnkübatörler', 'Inkubatorler', 'İnkübatör', 'Incubator'],
    ],
    'etuv' => [
        'name' => 'Etüv',
        'summary' => 'Laboratuvar kurutma ve ısıtma süreçleri için etüv cihazları.',
        'aliases' => ['Etuv', 'Vakumlu Etüv', 'Vacuum Oven'],
    ],
    'pipetler' => [
        'name' => 'Pipetler',
        'summary' => 'Laboratuvar sıvı hacim aktarımı için otomatik ve elektronik pipetler.',
        'aliases' => ['Pipetler', 'Otomatik Pipetler', 'Elektronik Pipetler', 'Pipette'],
    ],
];

$docs = [
    'wl' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WL-603.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WL%200,001.pdf', 'path' => null],
    ],
    'wll' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WL-2002L.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WL%200,001%20g_merged.pdf', 'path' => null],
    ],
    'wa123m' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WA-123M.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WA-123%20M%20Nem%20tayin%20cihaz%C4%B1.pdf', 'path' => null],
    ],
    'wfma1' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-MA1.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-MA1.pdf', 'path' => null],
    ],
    'wfmia1' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-MIA1_1.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-H380a%20&%20WF-MIA1%20&%20WF-MA1.pdf', 'path' => null],
    ],
    'wn10h120' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-10H120.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-10H-120.pdf', 'path' => null],
    ],
    'wnh320' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-H320.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-AP%20&%20WN-H320.pdf', 'path' => null],
    ],
    'wnap550' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-AP550.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-AP%20&%20WN-H320_2.pdf', 'path' => null],
    ],
    'wfhd1' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-HD1.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-HD1%20yeni.pdf', 'path' => null],
    ],
    'wnv2800' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-V2800.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-OD%20&%20WN-V_1.pdf', 'path' => null],
    ],
    'wfod' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-OD20.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-OD%20&%20WN-V.pdf', 'path' => null],
    ],
    'wfbdk' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-BDK250.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/Balon%20Is%C4%B1t%C4%B1c%C4%B1lar.pdf', 'path' => null],
    ],
    'wfbak' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-BAK250.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/Balon%20Is%C4%B1t%C4%B1c%C4%B1lar_1.pdf', 'path' => null],
    ],
    'wfba' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-BA250%20Balon%20%C4%B1s%C4%B1t%C4%B1c%C4%B1.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/Balon%20Is%C4%B1t%C4%B1c%C4%B1lar_2.pdf', 'path' => null],
    ],
    'wfsb' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-SB30N.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-SB%20N%20yeni_1.pdf', 'path' => null],
    ],
    'wfud' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-UD2%20%C5%9Eartnamesi.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/Ultrasonik%20banyo.pdf', 'path' => null],
    ],
    'wncl6500' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CL6500%20%C5%9EARTNAME.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CL6500.pdf', 'path' => null],
    ],
    'wn15cmr' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-15CMR.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CM15R.pdf', 'path' => null],
    ],
    'wncmv6000' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CMV6000%20Santrifuj.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CMV6000_1.pdf', 'path' => null],
    ],
    'wncm15n' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CM15%20Mikro%20Santrifuj.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-CM15N.pdf', 'path' => null],
    ],
    'wnrd' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-RD.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-RD.pdf', 'path' => null],
    ],
    'wnrp' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-RP.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WN-RP.pdf', 'path' => null],
    ],
    'wmmp' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WMMP-10%20Pipet%20%C5%9Eartnamesi.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WMMP%20Serisi%20Otomatik%20Pipet%20yeni.pdf', 'path' => null],
    ],
    'wap' => [
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WAP%20ELEKTRON%C4%B0K%20P%C4%B0PET.pdf', 'path' => null],
    ],
    'wfsbc30' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-SBC30.doc', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-SBC30%20%C3%87alkalamal%C4%B1%20Su%20banyosu.pdf', 'path' => null],
    ],
    'wfltc70r' => [
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-LTC70R%20%C3%87alkalamal%C4%B1%20So%C4%9Futmal%C4%B1%20%C4%B0nk%C3%BCbat%C3%B6r.pdf', 'path' => null],
    ],
    'wfhtv' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-HTV25.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-HTV%20Bro%C5%9F%C3%BCr.pdf', 'path' => null],
    ],
    'wflt' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-LT65.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-LT.pdf', 'path' => null],
    ],
    'wfht' => [
        ['title' => 'Şartname', 'type' => 'specification', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-HT45%20%282%29.docx', 'path' => null],
        ['title' => 'Tanıtım Dosyası', 'type' => 'catalog', 'url' => 'https://www.weightlabinstruments.com/assets/dokumanlar/WF-HT%20Bro%C5%9F%C3%BCr.pdf', 'path' => null],
    ],
];

$balanceFeatures = [
    'Yüksek çözünürlüklü yük hücresi',
    'ABS plastik gövde',
    'Paslanmaz çelik tava',
    'Arkadan aydınlatmalı LCD ekran',
    'Aşırı yük koruması',
    'Tam kapasiteli dara',
    'Su terazisi ve ayarlanabilir ayaklar',
    '10 farklı birimde tartım',
    'Parça sayma fonksiyonu',
    'Harici kalibrasyon ağırlığı',
    'Hızlı ve stabil tartım',
    'Adaptörsüz kullanım için dahili pil',
    'Ekranda ve tuş takımında IP-42 koruması',
    'Opsiyonel RS-232C çıkışıyla yazıcıya ve bilgisayara bağlanabilme',
];

$balanceLFeatures = [
    'Yüksek çözünürlüklü, arkadan aydınlatmalı LCD ekran',
    'Yüksek çözünürlüklü 1/300.000 yük hücresi',
    'Harici kalibrasyon',
    'Aşırı yük koruması',
    'Rüzgar kabini',
    'Tam kapasiteli dara',
    'Hızlı tartım ve filtre ayarları',
    'Aşırı yük ve alarm fonksiyonu',
    '10 adet çoklu tartım ünitesi',
    'Sıfırlama ayarı',
    'Yüzde tartım ve parça sayma modu',
    'Ruh seviyesi',
    'AC-DC adaptörüyle sabit kullanım ve dahili pille taşınabilirlik',
    'Harici kalibrasyon ağırlığıyla birlikte sunulur',
    'Ekranda ve tuş takımında IP-42 koruması',
    'Opsiyonel RS-232C çıkışı ile bilgisayara ve yazıcıya bağlanabilme',
];

$products = [
    [
        'category_slug' => 'hassas-teraziler',
        'name' => 'Weightlab WL-303 Hassas Terazi',
        'slug' => 'weightlab-wl-303-hassas-terazi',
        'image_slugs' => ['weightlab-wl-303-hassas-terazi', 'wl-303-hassas-terazi'],
        'model' => 'WL-303',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wl-serisi/#product-desc-content-47',
        'summary' => 'Weightlab WL-303 hassas terazi; 300 g kapasite, 0.001 g hassasiyet, harici kalibrasyon ve IP-42 ekran korumasıyla rutin laboratuvar tartımları için kullanılır.',
        'body' => 'Weightlab WL-303, arkadan aydınlatmalı LCD ekranı, paslanmaz çelik tavası, dahili pil kullanımı ve parça sayma fonksiyonuyla kompakt hassas tartım uygulamaları için geliştirilmiştir.',
        'features' => $balanceFeatures,
        'specs' => ['Kapasite' => '300 g', 'Hassasiyet' => '0.001 g', 'Tekrarlanabilirlik' => '0.001 g', 'Doğrusallık' => '0.002 g', 'Stabilite süresi' => '1.5 saniye', 'Kalibrasyon' => 'Harici kalibrasyon', 'Çalışma ortamı' => '5-40 °C, 85% RH', 'Kefe boyutu' => '90 mm', 'Koruma sınıfı' => 'IP-42 ekran', 'Cihaz boyutları' => '305 x 203 x 290 mm'],
        'documents' => $docs['wl'],
        'image_alt' => 'Weightlab WL-303 hassas terazi ürün görseli',
    ],
    [
        'category_slug' => 'hassas-teraziler',
        'name' => 'Weightlab WL-603 Hassas Terazi',
        'slug' => 'weightlab-wl-603-hassas-terazi',
        'image_slugs' => ['weightlab-wl-603-hassas-terazi', 'weightlab-wl-603-hassas-laboratuvar-terazisi'],
        'model' => 'WL-603',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wl-serisi/#product-desc-content-78',
        'summary' => 'Weightlab WL-603 hassas terazi; 600 g kapasite, 0.001 g hassasiyet, harici kalibrasyon ve hızlı stabil tartım yapısıyla laboratuvar tartım işlemleri için kullanılır.',
        'body' => 'Weightlab WL-603, WL serisinin yüksek kapasiteli 0.001 g çözünürlüklü modelidir. Tam kapasiteli dara, parça sayma, çoklu tartım birimi ve opsiyonel RS-232C bağlantısıyla günlük laboratuvar kullanımına uygundur.',
        'features' => $balanceFeatures,
        'specs' => ['Kapasite' => '600 g', 'Hassasiyet' => '0.001 g', 'Tekrarlanabilirlik' => '0.002 g', 'Doğrusallık' => '0.003 g', 'Stabilite süresi' => '1.5 saniye', 'Kalibrasyon' => 'Harici kalibrasyon', 'Çalışma ortamı' => '5-40 °C, 85% RH', 'Kefe boyutu' => 'Ø 90 mm', 'Koruma sınıfı' => 'IP-42 ekran', 'Cihaz boyutları' => '305 x 203 x 290 mm'],
        'documents' => $docs['wl'],
        'image_alt' => 'Weightlab WL-603 hassas terazi ürün görseli',
    ],
    [
        'category_slug' => 'hassas-teraziler',
        'name' => 'Weightlab WL-6002 Hassas Terazi',
        'slug' => 'weightlab-wl-6002-hassas-terazi',
        'image_slugs' => ['weightlab-wl-6002-hassas-terazi', 'weightlab-wl-6002-hassas-laboratuvar-terazisi'],
        'model' => 'WL-6002',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wl-serisi/#product-desc-content-79',
        'summary' => 'Weightlab WL-6002 hassas terazi; 6000 g kapasite, 0.01 g hassasiyet, geniş 168 x 168 mm kefe ve harici kalibrasyon yapısıyla yüksek kapasiteli hassas tartımlar için kullanılır.',
        'body' => 'Weightlab WL-6002, 6 kg kapasite ve 0.01 g hassasiyet isteyen laboratuvar ve kalite kontrol tartımları için tasarlanmış WL serisi hassas terazidir. Dahili pil, hızlı stabil tartım ve IP-42 ekran korumasıyla esnek kullanım sağlar.',
        'features' => $balanceFeatures,
        'specs' => ['Kapasite' => '6000 g', 'Hassasiyet' => '0.01 g', 'Tekrarlanabilirlik' => '0.02 g', 'Doğrusallık' => '0.03 g', 'Stabilite süresi' => '1.5 saniye', 'Kalibrasyon' => 'Harici kalibrasyon', 'Çalışma ortamı' => '5-40 °C, 85% RH', 'Kefe boyutu' => '168 x 168 mm', 'Koruma sınıfı' => 'IP-42 ekran', 'Cihaz boyutları' => '305 x 203 x 80 mm'],
        'documents' => $docs['wl'],
        'image_alt' => 'Weightlab WL-6002 hassas terazi ürün görseli',
    ],
    [
        'category_slug' => 'hassas-teraziler',
        'name' => 'Weightlab WL-303L Hassas Terazi',
        'slug' => 'weightlab-wl-303l-hassas-terazi',
        'image_slugs' => ['weightlab-wl-303l-hassas-terazi', 'weightlab-wl-303l-dijital-hassas-terazi'],
        'model' => 'WL-303L',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wl-l-serisi/#product-desc-content-48',
        'summary' => 'Weightlab WL-303L hassas terazi; 300 g kapasite, 0.001 g hassasiyet, rüzgar kabini ve harici kalibrasyon yapısıyla hassas laboratuvar tartımları için kullanılır.',
        'body' => 'Weightlab WL-303L, yüksek çözünürlüklü yük hücresi, LCD ekran, rüzgar kabini ve IP-42 ekran/tuş takımı korumasıyla WL-L serisinin kompakt hassas terazi modelidir.',
        'features' => $balanceLFeatures,
        'specs' => ['Kapasite' => '300 g', 'Hassasiyet' => '0.001 g', 'Tekrarlanabilirlik' => '0.002 g', 'Doğrusallık' => '0.002 g', 'Stabilite süresi' => '1.5 saniye', 'Kalibrasyon' => 'Harici kalibrasyon', 'Çalışma ortamı' => '5-40 °C, 85% RH', 'Kefe boyutu' => '90 mm', 'Koruma sınıfı' => 'IP-42 ekran', 'Cihaz boyutları' => '260 x 188 x 200 mm'],
        'documents' => $docs['wll'],
        'image_alt' => 'Weightlab WL-303L hassas terazi ürün görseli',
    ],
    [
        'category_slug' => 'hassas-teraziler',
        'name' => 'Weightlab WL-2002L Hassas Terazi',
        'slug' => 'weightlab-wl-2002l-hassas-terazi',
        'image_slugs' => ['weightlab-wl-2002l-hassas-terazi', 'weightlab-wl-2002l-dijital-hassas-terazi'],
        'model' => 'WL-2002L',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wl-l-serisi/#product-desc-content-83',
        'summary' => 'Weightlab WL-2002L hassas terazi; 2000 g kapasite, 0.01 g hassasiyet, 132 mm kefe ve harici kalibrasyon özellikleriyle günlük laboratuvar tartımlarında kullanılır.',
        'body' => 'Weightlab WL-2002L, WL-L serisinin 2 kg kapasiteli hassas terazi modelidir. Çoklu tartım birimi, yüzde tartım, parça sayma, dahili pil ve opsiyonel RS-232C bağlantısıyla pratik kullanım sunar.',
        'features' => $balanceLFeatures,
        'specs' => ['Kapasite' => '2000 g', 'Hassasiyet' => '0.01 g', 'Tekrarlanabilirlik' => '0.02 g', 'Doğrusallık' => '0.02 g', 'Stabilite süresi' => '1.5 saniye', 'Kalibrasyon' => 'Harici kalibrasyon', 'Çalışma ortamı' => '5-40 °C, 85% RH', 'Kefe boyutu' => '132 mm', 'Koruma sınıfı' => 'IP-42 ekran', 'Cihaz boyutları' => '260 x 188 x 200 mm'],
        'documents' => $docs['wll'],
        'image_alt' => 'Weightlab WL-2002L hassas terazi ürün görseli',
    ],
    [
        'category_slug' => 'hassas-teraziler',
        'name' => 'Weightlab WL-3002L Hassas Terazi',
        'slug' => 'weightlab-wl-3002l-hassas-terazi',
        'image_slugs' => ['weightlab-wl-3002l-hassas-terazi', 'weightlab-wl-3002l-dijital-hassas-terazi'],
        'model' => 'WL-3002L',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wl-l-serisi/#product-desc-content-84',
        'summary' => 'Weightlab WL-3002L hassas terazi; 3000 g kapasite, 0.01 g hassasiyet, harici kalibrasyon ve IP-42 ekran korumasıyla hassas laboratuvar tartımlarına uygundur.',
        'body' => 'Weightlab WL-3002L, 3 kg kapasite ve 0.01 g hassasiyet isteyen rutin laboratuvar tartımlarında hızlı, stabil ve taşınabilir kullanım sunan WL-L serisi hassas terazidir.',
        'features' => $balanceLFeatures,
        'specs' => ['Kapasite' => '3000 g', 'Hassasiyet' => '0.01 g', 'Tekrarlanabilirlik' => '0.02 g', 'Doğrusallık' => '0.02 g', 'Stabilite süresi' => '1.5 saniye', 'Kalibrasyon' => 'Harici kalibrasyon', 'Çalışma ortamı' => '5-40 °C, 85% RH', 'Kefe boyutu' => '132 mm', 'Koruma sınıfı' => 'IP-42 ekran', 'Cihaz boyutları' => '260 x 188 x 200 mm'],
        'documents' => $docs['wll'],
        'image_alt' => 'Weightlab WL-3002L hassas terazi ürün görseli',
    ],
    [
        'category_slug' => 'nem-tayin',
        'name' => 'Weightlab WA-123M Nem Tayin Cihazı',
        'slug' => 'weightlab-wa-123m-nem-tayin-cihazi',
        'image_slugs' => ['weightlab-wa-123m-nem-tayin-cihazi', 'wa-123m-nem-tayin-cihazi'],
        'model' => 'WA-123M',
        'old_url' => 'https://www.weightlabinstruments.com/urun/nem-tayin-cihazi/#product-desc-content-49',
        'summary' => 'Weightlab WA-123M nem tayin cihazı; 120 g kapasite, 0.001 g tartım hassasiyeti, %0.001 nem hassasiyeti, halojen ısıtıcı ünite ve 40 metod hafızasıyla nem analizi için kullanılır.',
        'body' => 'Weightlab WA-123M, halojen ısıtma ve elektromanyetik tartım sistemiyle hızlı ve tekrarlanabilir nem tayini sağlar. GLP/ISO uyumlu yazdırma formatı, 4 kullanıcı kimliği, şifre koruması, metod hafızası ve USB/PS2 çıkışlarıyla laboratuvar kayıt süreçlerini destekler.',
        'features' => [
            'Dairesel halojen lambalar ve reflektörlerle hızlı ısıtma',
            'Elektromanyetik tartım sistemiyle hızlı ölçüm ve yüksek tekrarlanabilirlik',
            '40 adet programlanabilir kullanıcı yöntemi',
            'Her yöntem için 1000 ölçümde standart sapma hesaplama',
            'Metod ve ayarlar için şifre koruması',
            'GLP/ISO düzenlemelerine uygun yazdırma formatı',
            '4 kullanıcı kimliği',
            'USB, RS232 ve Ethernet arayüzleriyle veri aktarımı',
            'Numune miktarı yönlendirmesi',
            'Farklı ısıtma profilleri',
            '6 farklı kullanıcı dili',
            '4 farklı medya filtresi',
            'Bekleme sıcaklığı ile cihazı kullanıma hazır tutma',
        ],
        'specs' => ['Model' => 'WA-123M', 'Tartım kapasitesi' => '120 g', 'Tartım hassasiyeti' => '0.001 g', 'Nem hassasiyeti' => '% 0.001', 'Tekrarlanabilirlik (sd) = 2 g' => '% 0.05', 'Tekrarlanabilirlik (sd) = 10 g' => '% 0.01', 'Kefe çapı' => 'Ø 95 mm', 'Isıtıcı ünite' => 'Infra Red Halojen', 'Isıtma aralığı' => '30 °C - 175 °C (opsiyonel 250 °C)', 'Sıcaklık artırım değeri' => '1 °C', 'Isıtma profilleri' => 'Standard, Gentle, Rapid, Steps, High Temperature', 'Ölçüm bitirme ayarları' => 'Auto, Manual, User Def Weight / Time, User Def % unit / time, Intelligent', 'Metod hafızası' => '40', 'Batch hafızası' => '100', 'Ekran' => 'Aydınlatmalı grafik ekran', 'Çıkışlar' => 'USB, PS2', 'Güç kaynağı' => '230 V / 50 Hz veya 115 V / 60 Hz', 'Güç tüketimi' => 'Maksimum ısıtmada 415 W', 'Boyutlar' => '211 x 342 x 187 mm', 'Ağırlık' => '5 kg'],
        'documents' => $docs['wa123m'],
        'image_alt' => 'Weightlab WA-123M nem tayin cihazı ürün görseli',
    ],
    [
        'category_slug' => 'isitmasiz-manyetik-karistirici',
        'name' => 'Weightlab WF-MA1 Isıtmasız Manyetik Karıştırıcı',
        'slug' => 'weightlab-wf-ma1-isitmasiz-manyetik-karistirici',
        'image_slugs' => ['weightlab-wf-ma1-isitmasiz-manyetik-karistirici', 'wf-ma1-isitmasiz-manyetik-karistirici'],
        'model' => 'WF-MA1',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wf-ma1/#product-desc-content-50',
        'summary' => 'Weightlab WF-MA1 ısıtmasız manyetik karıştırıcı; 0-2000 rpm hız aralığı, 1 litre H2O kapasite ve 120 mm tabla çapıyla küçük hacimli karıştırma işlemleri için kullanılır.',
        'body' => 'Weightlab WF-MA1, ABS plastik gövdesiyle hafif ve dayanıklı bir masa üstü manyetik karıştırıcıdır. Kademesiz hız ayarı, kompakt ölçüleri ve kolay kullanımıyla rutin laboratuvar karıştırma uygulamalarını destekler.',
        'features' => ['ABS plastik gövde', 'Hafif ve dayanıklı yapı', 'Kademesiz ayarlanabilir karıştırma hızı', 'Sorunsuz ve hızlı karıştırma', 'Bilimsel tasarım ve kompakt görünüm'],
        'specs' => ['Cihaz tipi' => 'Analog, masa üstü', 'Karıştırma hızı aralığı' => '0-2000 rpm', 'Maksimum karıştırma kapasitesi' => '1 litre (H2O)', 'Tabla ölçüleri' => '120 mm çap', 'Ağırlık' => '0.7 kg', 'Boyutlar' => '138 x 138 x 60 mm', 'Güç' => '10 W'],
        'documents' => $docs['wfma1'],
        'image_alt' => 'Weightlab WF-MA1 ısıtmasız manyetik karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'isitmali-manyetik-karistirici',
        'name' => 'Weightlab WF-MIA1 Analog Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'weightlab-wf-mia1-isitmali-manyetik-karistirici',
        'image_slugs' => ['weightlab-wf-mia1-isitmali-manyetik-karistirici', 'weightlab-analog-isitmali-manyetik-karistirici-wf-mia1'],
        'model' => 'WF-MIA1',
        'old_url' => 'https://www.weightlabinstruments.com/urun/analog-isitmali-manyetik-karistirici-wf-mia1/#product-desc-content-345',
        'summary' => 'Weightlab WF-MIA1 analog ısıtmalı manyetik karıştırıcı; oda sıcaklığından 380 °C’ye kadar ısıtma, 100-2000 rpm hız aralığı ve 2 litre H2O kapasiteyle kullanılır.',
        'body' => 'Weightlab WF-MIA1, ısıtma ve karıştırmayı aynı anda yapabilen analog masa üstü manyetik karıştırıcıdır. Kapalı ısıtma plakası, kademesiz sıcaklık/hız ayarı ve aşırı sıcaklık korumasıyla güvenli kullanım sağlar.',
        'features' => ['Isıtma ve karıştırmayı aynı anda yapabilme', 'Alev korumalı, hızlı ısınan kapalı ısıtma plakası', 'Kademesiz sıcaklık ve karıştırma hızı ayarı', 'Eşit sıcaklık dağılımı', 'Hızlı ve güvenli ısıtma', 'Aşırı sıcaklık koruması'],
        'specs' => ['Cihaz tipi' => 'Analog, masa üstü', 'Isıtma aralığı' => 'Oda sıcaklığından 380 °C’ye kadar', 'Karıştırma hızı aralığı' => '100-2000 rpm', 'Maksimum karıştırma kapasitesi' => '2 litre (H2O)', 'Masa ölçüleri' => '120 x 120 mm', 'Ağırlık' => '1.9 kg', 'Boyutlar' => '200 x 120 x 90 mm', 'Güç' => '180 W'],
        'documents' => $docs['wfmia1'],
        'image_alt' => 'Weightlab WF-MIA1 ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'isitmali-manyetik-karistirici',
        'name' => 'Weightlab WN-10H-120 Dijital Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'weightlab-wn-10h-120-dijital-isitmali-manyetik-karistirici',
        'image_slugs' => ['weightlab-wn-10h-120-dijital-isitmali-manyetik-karistirici', 'weightlab-dijital-isitmali-manyetik-karistirici-wn-10h-120'],
        'model' => 'WN-10H-120',
        'old_url' => 'https://www.weightlabinstruments.com/urun/dijital-isitmali-manyetik-karistirici/#product-desc-content-52',
        'summary' => 'Weightlab WN-10H-120 dijital ısıtmalı manyetik karıştırıcı; 10 x 400 ml pozisyon, 300-1500 rpm hız aralığı ve ortam sıcaklığından 120 °C’ye kadar ısıtma özelliğiyle çok pozisyonlu karıştırma sağlar.',
        'body' => 'Weightlab WN-10H-120, 10 pozisyonlu dijital ısıtmalı manyetik karıştırıcıdır. Zamanlayıcı, sabit hız sağlayan BLDC motor, IP42 koruma ve yön değiştirme özellikli programlanabilir pals modu ile çoklu numune karıştırma uygulamalarında kullanılır.',
        'features' => ['10 x 400 ml karıştırma pozisyonlu çok pozisyonlu yapı', '300-1500 rpm arasında 10 rpm adımlı değişken hız', '1-999 dakika zamanlı veya sürekli çalışma', 'Aşırı ışımaya karşı koruma', 'Son ayarları hafızada tutan mikroprosesör işlemci', 'Ortam sıcaklığı ile 120 °C’ye kadar ısıtma', 'Kapalı tasarım ile kolay temizlik', 'IP42 koruma', 'Bakım gerektirmeyen BLDC motor', 'Yön değiştirme özellikli programlanabilir pals modu'],
        'specs' => ['Model' => 'WN-10H-120', 'Cihaz tipi' => 'Dijital, masa üstü', 'Isıtma aralığı' => 'Ortam sıcaklığı ila 120 °C, 1 °C adımlarla', 'Zamanlayıcı süresi' => '1-999 dakika veya süresiz sürekli çalışma', 'Maksimum karıştırma' => '400 ml x 10 (4 litre)', 'Hız aralığı' => '300-1500 rpm, 10 rpm adımlarla', 'Tabla boyutu' => '460 x 178 mm', 'Boyutlar (W x D x H)' => '565 x 196 x 75 mm', 'DIN EN koruma sınıfı' => 'IP 42', 'İzin verilen ortam sıcaklığı' => '5-40 °C', 'İzin verilen bağıl nem' => '80%', 'Ağırlık' => '5 kg', 'Güç tüketimi' => '400 W'],
        'documents' => $docs['wn10h120'],
        'image_alt' => 'Weightlab WN-10H-120 dijital ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'isitmali-manyetik-karistirici',
        'name' => 'Weightlab WN-H320 Dijital Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'weightlab-wn-h320-dijital-isitmali-manyetik-karistirici',
        'image_slugs' => ['weightlab-wn-h320-dijital-isitmali-manyetik-karistirici', 'weightlab-dijital-isitmali-manyetik-karistirici-wn-h320'],
        'model' => 'WN-H320',
        'old_url' => 'https://www.weightlabinstruments.com/urun/dijital-isitmali-manyetik-karistirici-wn-h320/#product-desc-content-53',
        'summary' => 'Weightlab WN-H320 dijital ısıtmalı manyetik karıştırıcı; 320 °C’ye kadar ısıtma, 200-2200 rpm hız aralığı, 10 litre H2O kapasite ve PT1000 prob ile kullanılır.',
        'body' => 'Weightlab WN-H320, dijital ekran üzerinden tabla veya harici PT sensörle numune sıcaklığı, karıştırma hızı ve çalışma süresinin izlenmesini sağlar. Seramik kaplı paslanmaz çelik tablası, pulse modu, tuş kilidi, ATS modu ve BLDC motoruyla güvenli laboratuvar karıştırma işlemlerine uygundur.',
        'features' => ['Tabla veya numune sıcaklığı, hız ve süreyi dijital ekrandan izleme', 'Harici sensörsüz ±1 °C, sensörle ±0.5 °C sıcaklık sabitleme', 'Seramik kaplı paslanmaz çelik tabla', 'Aşırı ışımaya karşı koruma', 'Son ayarları hafızada tutma', '50 °C üzerinde HOT ikaz lambası', 'PT1000 sıcaklık probu ile numune sıcaklığı ölçümü', '30 saniyede bir yeniden karıştırma sağlayan pulse modu', 'Bakım gerektirmeyen BLDC motor', '1-999 dakika zamanlayıcı', 'Tuş kilidi ve ATS modu', 'IP21 koruma'],
        'specs' => ['Cihaz tipi' => 'Dijital, masa üstü', 'Isıtma aralığı' => 'Ortam sıcaklığı ila 320 °C, 1 °C adımlarla', 'Karıştırma hızı aralığı' => '200-2200 rpm, 10 rpm adımlarla', 'Zamanlayıcı süresi' => '1-999 dakika veya süresiz sürekli çalışma', 'Maksimum karıştırma kapasitesi' => '10 litre (H2O)', 'Tabla malzemesi' => 'Seramik kaplı paslanmaz çelik', 'Tabla ölçüleri' => '140 mm çap', 'Sıcaklık probu algılama' => 'PT1000 prob takıldığında Probe ışığı yanar ve prob çözelti içinde olmadığında uyarı verir', 'Tabla sıcak uyarısı' => 'Yüzey 50 °C üzerinde olduğunda kullanıcıyı uyarır', 'Ağırlık' => '2.2 kg', 'Boyutlar (UxGxY)' => '156 x 248 x 104 mm', 'Koruma sınıfı' => 'IP-21', 'Motor tipi' => 'BLDC', 'Güç' => '610 W', 'Birlikte verilenler' => 'PT1000 sıcaklık probu ve bağlantı standı'],
        'documents' => $docs['wnh320'],
        'image_alt' => 'Weightlab WN-H320 dijital ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'isitmali-manyetik-karistirici',
        'name' => 'Weightlab WN-AP550 Dijital Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'weightlab-wn-ap550-dijital-isitmali-manyetik-karistirici',
        'image_slugs' => ['weightlab-wn-ap550-dijital-isitmali-manyetik-karistirici', 'weightlab-dijital-isitmali-manyetik-karistirici-wn-ap550'],
        'model' => 'WN-AP550',
        'old_url' => 'https://www.weightlabinstruments.com/urun/dijital-isitmali-manyetik-karistirici-wn-ap550/#product-desc-content-54',
        'summary' => 'Weightlab WN-AP550 dijital ısıtmalı manyetik karıştırıcı; 550 °C’ye kadar ısıtma, 200-2200 rpm hız aralığı, 20 litre H2O kapasite, nano kristal cam seramik tabla ve PT1000 prob ile kullanılır.',
        'body' => 'Weightlab WN-AP550, yüksek sıcaklık ve yüksek hacim isteyen laboratuvar karıştırma uygulamaları için geliştirilmiş dijital ısıtmalı manyetik karıştırıcıdır. Üç farklı ısıtma modu, otomatik başlatma, manyetik balık kırılma/kayma tespiti, emniyet sıcaklığı noktası ve RS232 arayüzüyle gelişmiş kontrol sunar.',
        'features' => ['Dijital ekrandan tabla veya numune sıcaklığı, hız ve süre izleme', 'Harici sensörsüz ±1 °C, sensörle ±0.5 °C sıcaklık sabitleme', 'Nano kristal cam seramik tabla', 'Aşırı ışımaya karşı koruma', 'Son ayarları hafızada tutma', '50 °C üzerinde HOT ikaz lambası', 'PT1000 sıcaklık probu ile numune sıcaklığı ölçümü', 'Pulse modu ile çift yönlü güçlü karıştırma', 'Bakım gerektirmeyen BLDC motor', 'Üç farklı ısıtma modu', '1-999 dakika zamanlayıcı', 'Tuş kilidi ve ATS modu', 'Manyetik balık kırılma ve kayma tespiti', 'RS232 arayüzü', 'IP21 koruma'],
        'specs' => ['Cihaz tipi' => 'Dijital, masa üstü', 'Isıtma aralığı' => 'Ortam sıcaklığı ila 550 °C, 1 °C adımlarla', 'Karıştırma hızı aralığı' => '200-2200 rpm, 10 rpm adımlarla', 'Zamanlayıcı süresi' => '1-999 dakika veya süresiz sürekli çalışma', 'Maksimum karıştırma kapasitesi' => '20 litre (H2O)', 'Tabla malzemesi' => 'Nano kristal cam seramik', 'Tabla ölçüleri' => '180 x 180 mm', 'Pulse modu' => 'Çift yönlü güçlü karıştırma sağlar', '3 farklı ısıtma modu' => 'Yüksek hassasiyette kademeli, yüksek hassasiyette hızlı ve düşük hassasiyette çok hızlı', 'Otomatik başlatma modu' => 'Elektrik kesintisi sonrasında kaldığı süreden ve en son çalıştığı değerlerden başlar', 'Manyetik balık kırılma tespiti' => 'Var', 'Manyetik balık kayma tespiti' => 'Yörüngeden kaydığını tespit eder ve optimum sınırlara getirip düzeltir', 'Sıcaklık probu algılama' => 'PT1000 prob takıldığında Probe ışığı yanar ve prob çözelti içinde olmadığında uyarı verir', 'Tuş takımı kilidi' => 'Var', 'Emniyet sıcaklığı noktası' => 'Isıtmanın durdurulacağı programlanabilir üst güvenli sıcaklık değeri vardır', 'Tabla sıcak uyarısı' => 'Yüzey 50 °C üzerinde olduğunda kullanıcıyı uyarır', 'RS232 arayüzü' => 'Var', 'Ağırlık' => '4.9 kg', 'Boyutlar (DxGxY)' => '313 x 205 x 110 mm', 'Koruma sınıfı' => 'IP 21', 'Motor tipi' => 'BLDC', 'Güç' => '1000 W', 'Birlikte verilenler' => 'PT1000 sıcaklık probu ve bağlantı standı'],
        'documents' => $docs['wnap550'],
        'image_alt' => 'Weightlab WN-AP550 dijital ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'hot-plate',
        'name' => 'Weightlab WF-HD1 Dijital Hotplate',
        'slug' => 'weightlab-wf-hd1-dijital-hotplate',
        'image_slugs' => ['weightlab-wf-hd1-dijital-hotplate', 'weightlab-isitici-tabla-hotplate'],
        'model' => 'WF-HD1',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wf-hd1-dijital-hotplate/#product-desc-content-55',
        'summary' => 'Weightlab WF-HD1 dijital hotplate; ortam sıcaklığından 350 °C’ye kadar ısıtma, 400 x 400 mm tabla ve 2000 W güç ile laboratuvar ısıtma uygulamalarında kullanılır.',
        'body' => 'Weightlab WF-HD1, geniş alüminyum ısıtma tablası ve dijital masa üstü yapısıyla hızlı, eşit ve güvenli ısıtma sağlar. Yüksek sıcaklıkta şekil değişimine dirençli yüzeyi rutin laboratuvar ısıtma işlemlerine uygundur.',
        'features' => ['Yüzey sıcaklığı 350 °C’ye kadar ulaşabilir', 'Yüksek sıcaklıkta şekil değişikliği yapmaz', 'Alüminyum tepsi pürüzsüz ve eşit sıcaklıklı yüzey sağlar', 'Sıcaklık hızlı ve eşit şekilde yükselir', 'Kullanımı kolay ve güvenli'],
        'specs' => ['Cihaz tipi' => 'Dijital, masa üstü', 'Isıtma aralığı' => 'Ortam sıcaklığı ila 350 °C', 'Tabla ölçüleri' => '400 x 400 mm', 'Ağırlık' => '8.5 kg', 'Boyutlar' => '470 x 470 x 260 mm', 'Güç' => '2000 W'],
        'documents' => $docs['wfhd1'],
        'image_alt' => 'Weightlab WF-HD1 dijital hotplate ürün görseli',
    ],
    [
        'category_slug' => 'vorteks-karistiricilar',
        'name' => 'Weightlab WN-V2800 Vorteks Karıştırıcı',
        'slug' => 'weightlab-wn-v2800-vorteks-karistirici',
        'image_slugs' => ['weightlab-wn-v2800-vorteks-karistirici', 'wn-v2800-vorteks-karistirici', 'wn-n2800-vorteks-karistirici'],
        'model' => 'WN-V2800',
        'old_url' => 'https://www.weightlabinstruments.com/urun/wn-v-2800-vorteks2/#product-desc-content-57',
        'summary' => 'Weightlab WN-V2800 vorteks karıştırıcı; 2800 rpm ayarlanabilir maksimum hız, 4 mm orbital çap, 500 g yükleme kapasitesi ve üç çalışma modu ile kullanılır.',
        'body' => 'Weightlab WN-V2800, dokunma, stand-by ve sürekli çalışma modlarıyla tüp, mikroplaka veya şişe karıştırma uygulamaları için tasarlanmış analog masa üstü vorteks karıştırıcıdır. Dahili denge özelliği ve sessiz çalışma yapısı güvenli kullanım sağlar.',
        'features' => ['Dahili denge özelliği ile stabil çalışma', 'Hafif basınçla kolay ve düzgün çalışma', 'Sessiz çalışma', 'Mikroplaka, mikrotüp veya şişe için opsiyonel platformlar', '500 g yükleme kapasitesi', 'Dokunma, stand-by ve sürekli çalışma modu'],
        'specs' => ['Cihaz tipi' => 'Analog, masa üstü', 'Ayarlanabilen maksimum hız' => '2800 rpm', 'Orbital çap' => '4 mm', 'Maksimum yükleme' => '500 g', '3 farklı çalışma modu' => 'Dokunma, stand-by ve sürekli çalışma modu', 'Ağırlık' => '3 kg', 'Boyutlar' => '205 x 136 x 138.5 mm', 'Koruma sınıfı' => 'IP 21', 'Güç' => '70 W', 'Opsiyonel aparatlar' => 'Mikroplate, mikrotüp veya erlen çalkalama platformu'],
        'documents' => $docs['wnv2800'],
        'image_alt' => 'Weightlab WN-V2800 vorteks karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'mekanik-karistirici',
        'name' => 'Weightlab WF-OD20 Mekanik Karıştırıcı',
        'slug' => 'weightlab-wf-od20-mekanik-karistirici',
        'image_slugs' => ['weightlab-wf-od20-mekanik-karistirici', 'wf-od20-mekanik-karistirici'],
        'model' => 'WF-OD20',
        'old_url' => 'https://www.weightlabinstruments.com/urun/mekanik-karistiricilar2/#product-desc-content-56',
        'summary' => 'Weightlab WF-OD20 mekanik karıştırıcı; 20 litre H2O kapasite, 10000 mPas maksimum viskozite, 40 Ncm tork ve 100-2500 rpm hız aralığıyla kullanılır.',
        'body' => 'Weightlab WF-OD20, yüksek viskoziteli sıvı veya katı-sıvı karışımları için uygun dijital mekanik karıştırıcıdır. BLDC motor, LCD ekran, aşırı yük koruması ve paslanmaz çelik başlıklarla güvenilir karıştırma sağlar.',
        'features' => ['Yüksek viskoziteli sıvı veya katı-sıvı karışımları için uygun', 'Düşük gürültülü ve bakım gerektirmeyen BLDC motor', 'Sürekli aşırı yükte otomatik durma', 'Numunelerin taşmasını ve dökülmesini önler', 'Dayanıklı paslanmaz çelik başlıklar'],
        'specs' => ['Karıştırma kapasitesi' => '20 litre (H2O)', 'Viskozite (maks.)' => '10000 mPas', 'Motor tipi' => 'BLDC', 'Tork' => '40 Ncm', 'Karıştırma hız aralığı' => '100-2500 rpm', 'Hız doğruluğu' => '±1 rpm', 'Şaft çapı' => 'Ø 0.5-10 mm', 'Ekran tipi' => 'LCD', 'Koruma sınıfı' => 'IP42', 'Güç' => '60 W', 'Çalışma sıcaklığı ve nem' => '5-40 °C, 80% RH', 'Boyutlar' => '83 x 220 x 186 mm', 'Ağırlık' => '7 kg'],
        'documents' => $docs['wfod'],
        'image_alt' => 'Weightlab WF-OD20 mekanik karıştırıcı ürün görseli',
    ],
    [
        'category_slug' => 'mekanik-karistirici',
        'name' => 'Weightlab WF-OD40 Mekanik Karıştırıcı',
        'slug' => 'weightlab-wf-od40-mekanik-karistirici',
        'image_slugs' => ['weightlab-wf-od40-mekanik-karistirici', 'wdod40-mekanik-karistirici', 'wf-od40-mekanik-karistirici'],
        'model' => 'WF-OD40',
        'old_url' => 'https://www.weightlabinstruments.com/urun/mekanik-karistiricilar2/#product-desc-content-148',
        'summary' => 'Weightlab WF-OD40 mekanik karıştırıcı; 40 litre H2O kapasite, 50000 mPas maksimum viskozite, 60 Ncm tork ve 100-2500 rpm hız aralığıyla kullanılır.',
        'body' => 'Weightlab WF-OD40, daha yüksek hacim ve viskozite gerektiren mekanik karıştırma işlemleri için geliştirilmiştir. BLDC motor, LCD ekran, aşırı yük koruması ve IP42 koruma sınıfıyla laboratuvar karıştırma süreçlerini destekler.',
        'features' => ['Yüksek viskoziteli sıvı veya katı-sıvı karışımları için uygun', 'Düşük gürültülü ve bakım gerektirmeyen BLDC motor', 'Sürekli aşırı yükte otomatik durma', 'Numunelerin taşmasını ve dökülmesini önler', 'Dayanıklı paslanmaz çelik başlıklar'],
        'specs' => ['Karıştırma kapasitesi' => '40 litre (H2O)', 'Viskozite (maks.)' => '50000 mPas', 'Motor tipi' => 'BLDC', 'Tork' => '60 Ncm', 'Karıştırma hız aralığı' => '100-2500 rpm', 'Hız doğruluğu' => '±1 rpm', 'Şaft çapı' => 'Ø 0.5-10 mm', 'Ekran tipi' => 'LCD', 'Koruma sınıfı' => 'IP42', 'Güç' => '80 W', 'Çalışma sıcaklığı ve nem' => '5-40 °C, 80% RH', 'Boyutlar' => '83 x 220 x 186 mm', 'Ağırlık' => '7 kg'],
        'documents' => $docs['wfod'],
        'image_alt' => 'Weightlab WF-OD40 mekanik karıştırıcı ürün görseli',
    ],
];

$bdkFeatures = [
    'Dokunmatik tuş takımı ile kademesiz sıcaklık ayarı',
    'Eşit sıcaklık, hızlı ve güvenli ısıtma',
    'Korozyona ve aşınmaya dayanıklı gövde',
    'PID ile yüksek hassasiyetli sıcaklık kontrolü',
    'PT100 sıcaklık sensörü ve standıyla birlikte verilir',
    '50 ml’den 20.000 ml’ye kadar yuvarlak tabanlı balon seçenekleri',
    '400 °C’ye kadar kontrol sağlayan zaman orantılı ısıtma kontrol sistemi',
    'Cam elyaf kaplamalı esnek ısıtıcı eleman',
    'Gövde havalandırma kanalları ve kalın cam yünü yalıtım',
    'Kaymayı önleyen lastik ayaklar',
    'Isıtma ve güç fonksiyonlarını gösteren kontrol lambaları',
    '1400 rpm hıza kadar dahili manyetik karıştırıcı sistemi',
];

foreach ([
    ['WF-BDK50', '50', '80 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BDK100', '100', '100 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BDK250', '250', '150 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BDK500', '500', '250 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BDK1000', '1000', null, null, null],
    ['WF-BDK2000', '2000', '450 W', 'φ30 x 23 cm', '4 kg'],
] as [$model, $capacity, $power, $dimensions, $weight]) {
    $modelSlug = strtolower($model);
    $specs = [
        'Model' => $model,
        'Cihaz tipi' => 'Dijital',
        'Kapasite' => $capacity . ' ml',
        'Yüzey maks. sıcaklık' => '400 °C',
        'Sıvı maks. sıcaklık' => '300 °C',
        'Karıştırma hızı' => '0-1400 rpm',
        'Çalışma süresi' => 'Sürekli',
    ];

    if ($power !== null) {
        $specs['Güç'] = $power;
    }

    if ($dimensions !== null) {
        $specs['Dış ölçüler'] = $dimensions;
    }

    if ($weight !== null) {
        $specs['Net ağırlık'] = $weight;
    }

    $products[] = [
        'category_slug' => 'balon-isiticilar',
        'name' => "Weightlab {$model} Dijital Karıştırıcılı Balon Isıtıcı",
        'slug' => "weightlab-{$modelSlug}-dijital-karistiricili-balon-isitici",
        'image_slugs' => ["weightlab-{$modelSlug}-dijital-karistiricili-balon-isitici", 'wf-bdk-serisi-balon-istici'],
        'model' => $model,
        'old_url' => 'https://www.weightlabinstruments.com/urun/dijital-karistiricili-balon-isitici/',
        'summary' => "Weightlab {$model} dijital karıştırıcılı balon ısıtıcı; {$capacity} ml kapasite, 400 °C yüzey sıcaklığı ve 0-1400 rpm manyetik karıştırma hızıyla yuvarlak tabanlı balon uygulamaları için kullanılır.",
        'body' => "Weightlab {$model}, dijital sıcaklık kontrolü ve dahili manyetik karıştırıcı sistemiyle ısıtma ve karıştırmayı aynı gövdede birleştirir. PT100 sıcaklık sensörü, PID kontrol ve esnek cam elyaf ısıtıcı eleman yapısı güvenli laboratuvar ısıtma işlemlerini destekler.",
        'features' => $bdkFeatures,
        'specs' => $specs,
        'documents' => $docs['wfbdk'],
        'image_alt' => "Weightlab {$model} dijital karıştırıcılı balon ısıtıcı ürün görseli",
    ];
}

$bakFeatures = [
    'Sıcaklık ve karıştırma hızı iki ayrı düğme ile kademesiz ayarlanır',
    'Isıtma ve karıştırma aynı anda yapılabilir',
    '1400 rpm hıza kadar karıştırma sağlayan dahili manyetik karıştırıcı sistemi',
    '50 ml’den 20.000 ml’ye kadar yuvarlak tabanlı balon seçenekleri',
    '450 °C’ye kadar hassas sıcaklık ayarı ve kontrolü',
    'Homojen ısı dağılımı sağlayan cam elyaf kaplı esnek ısıtıcı eleman',
    'Esnek ısıtma elemanı darbeleri emerek cam kırılma riskini azaltır',
    'Kolay temizlenen emaye boyalı metal dış kasa',
    'Dış kasayı serin tutan gövde havalandırma kanalları ve cam yünü izolasyonu',
    'Kaymayı önleyen lastik ayaklar',
    'Isıtma ve güç fonksiyonlarını gösteren kontrol lambaları',
];

foreach ([
    ['WF-BAK50', '50', '80 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BAK100', '100', '100 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BAK250', '250', '150 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BAK500', '500', '250 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BAK1000', '1000', '350 W', 'φ26 x 20 cm', '3.5 kg'],
    ['WF-BAK2000', '2000', '450 W', 'φ30 x 23 cm', '4 kg'],
] as [$model, $capacity, $power, $dimensions, $weight]) {
    $modelSlug = strtolower($model);

    $products[] = [
        'category_slug' => 'balon-isiticilar',
        'name' => "Weightlab {$model} Karıştırıcılı Balon Isıtıcı",
        'slug' => "weightlab-{$modelSlug}-karistiricili-balon-isitici",
        'image_slugs' => ["weightlab-{$modelSlug}-karistiricili-balon-isitici", 'wf-bak-serisi-balon-istici', 'wf-ba-serisi-balon-isitici'],
        'model' => $model,
        'old_url' => 'https://www.weightlabinstruments.com/urun/karistiricili-balon-isitici/',
        'summary' => "Weightlab {$model} karıştırıcılı balon ısıtıcı; {$capacity} ml kapasite, 450 °C yüzey sıcaklığı ve 0-1400 rpm manyetik karıştırma hızıyla yuvarlak tabanlı balon uygulamaları için kullanılır.",
        'body' => "Weightlab {$model}, analog sıcaklık ve hız kontrolünü dahili manyetik karıştırma sistemiyle birleştiren karıştırıcılı balon ısıtıcıdır. Cam elyaf kaplı esnek ısıtıcı elemanı, havalandırmalı emaye metal gövdesi ve kaymaz ayaklarıyla güvenli laboratuvar ısıtma-karıştırma işlemlerini destekler.",
        'features' => $bakFeatures,
        'specs' => [
            'Model' => $model,
            'Cihaz tipi' => 'Analog',
            'Kapasite' => $capacity . ' ml',
            'Yüzey maks. sıcaklık' => '450 °C',
            'Sıvı maks. sıcaklık' => '300 °C',
            'Güç' => $power,
            'Karıştırma hızı' => '0-1400 rpm',
            'Çalışma süresi' => 'Sürekli',
            'Dış ölçüler' => $dimensions,
            'Net ağırlık' => $weight,
        ],
        'documents' => $docs['wfbak'],
        'image_alt' => "Weightlab {$model} karıştırıcılı balon ısıtıcı ürün görseli",
    ];
}

$baFeatures = [
    '50 ml ile 20.000 ml arasında yuvarlak tabanlı balonlar için tasarlanmıştır',
    '450 °C’ye kadar hassas sıcaklık ayarı ve kontrolü',
    'Homojen ısı dağılımı sağlayan cam elyaf kaplı esnek ısıtıcı eleman',
    'Esnek ısıtma elemanı darbeleri emerek cam kırılma riskini azaltır',
    'Dış kasayı serin tutan havalandırma kanalları ve cam yünü izolasyonu',
    'Kolay temizlenen emaye boyalı metal dış kasa',
    'Kaymayı önleyen lastik ayaklar',
    'Isıtma ve güç fonksiyonlarını gösteren kontrol lambaları',
];

foreach ([
    ['WF-BA50', '50', '80 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BA100', '100', '100 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BA250', '250', '150 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BA500', '500', '250 W', 'φ20 x 16 cm', '2 kg'],
    ['WF-BA1000', '1000', '350 W', 'φ26 x 20 cm', '3.5 kg'],
    ['WF-BA2000', '2000', '450 W', 'φ30 x 23 cm', '4 kg'],
] as [$model, $capacity, $power, $dimensions, $weight]) {
    $modelSlug = strtolower($model);

    $products[] = [
        'category_slug' => 'balon-isiticilar',
        'name' => "Weightlab {$model} Balon Isıtıcı",
        'slug' => "weightlab-{$modelSlug}-balon-isitici",
        'image_slugs' => ["weightlab-{$modelSlug}-balon-isitici", 'wf-ba-serisi-balon-isitici'],
        'model' => $model,
        'old_url' => 'https://www.weightlabinstruments.com/urun/wf-ba-balon-isitici-2/',
        'summary' => "Weightlab {$model} balon ısıtıcı; {$capacity} ml kapasite, 450 °C yüzey sıcaklığı ve yuvarlak tabanlı balonlar için kontrollü ısıtma yapısıyla kullanılır.",
        'body' => "Weightlab {$model}, yuvarlak tabanlı balonlarda güvenli ve homojen ısıtma için tasarlanmış analog balon ısıtıcıdır. Cam elyaf kaplı esnek ısıtıcı eleman, emaye boyalı metal gövde ve cam yünü izolasyon laboratuvar ısıtma işlemlerinde kararlı kullanım sağlar.",
        'features' => $baFeatures,
        'specs' => [
            'Model' => $model,
            'Cihaz tipi' => 'Analog',
            'Kapasite' => $capacity . ' ml',
            'Yüzey maks. sıcaklık' => '450 °C',
            'Sıvı maks. sıcaklık' => '300 °C',
            'Güç' => $power,
            'Karıştırma hızı' => '0-1400 rpm',
            'Çalışma süresi' => 'Sürekli',
            'Dış ölçüler' => $dimensions,
            'Net ağırlık' => $weight,
        ],
        'documents' => $docs['wfba'],
        'image_alt' => "Weightlab {$model} balon ısıtıcı ürün görseli",
    ];
}

$waterBathFeatures = [
    'Paslanmaz çelik kaynaksız iç hazne ve ABS plastik şeffaf kapak',
    'PID programlı akıllı sıcaklık kontrolü',
    'Otomatik durma, aşırı sıcaklık alarmı, elektrik kesintisi belleği, hata düzeltme ve menü kilidi',
    'Soğuk haddeleme çelik malzeme',
    '0-9999 dakika arasında ayarlanabilen zamanlayıcı',
    'Dijital ekran ve dokunmatik tuşlar ile sıcaklık ayarı',
    'Su seviye kontrolü ve suyun uçması durumunda otomatik durma',
    'Solenoid valf ile kolay su tahliyesi',
    'Ayarlanan sıcaklıkta sabit ve kararlı çalışma',
    'Yüksek hassasiyetli sıcaklık kontrolü',
];

foreach ([
    ['WS-SB11N', 'weightlab-ws-sb11n-su-banyosu', ['weightlab-ws-sb11n-su-banyosu', 'ws-sb11-paslanmaz-celik-kapakli-su-banyolari'], '11 litre', '4 kg', '295 x 235 x 150 mm', '360 x 297 x 345 mm', '600 W', 'https://www.weightlabinstruments.com/urun/paslanmaz-celik-kapakli-su-banyolari/#product-desc-content-343'],
    ['WF-SB30N', 'weightlab-wf-sb30n-su-banyosu', ['weightlab-wf-sb30n-su-banyosu', 'wf-sb30n-paslanmaz-celik-kapakli-su-banyolari', 'ws-sb36-paslanmaz-celik-kapakli-su-banyolari'], '30 litre', '7.5 kg', '500 x 296 x 200 mm', '565 x 358 x 395 mm', '1500 W', 'https://www.weightlabinstruments.com/urun/paslanmaz-celik-kapakli-su-banyolari/#product-desc-content-344'],
] as [$model, $slug, $imageSlugs, $capacity, $weight, $innerDimensions, $outerDimensions, $power, $oldUrl]) {
    $products[] = [
        'category_slug' => 'su-banyosu',
        'name' => "Weightlab {$model} Su Banyosu",
        'slug' => $slug,
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'old_url' => $oldUrl,
        'summary' => "Weightlab {$model} su banyosu; RT+5-100 °C ısıtma aralığı, ±0.1 °C sıcaklık hassasiyeti, {$capacity} kapasite ve 0-9999 dakika zamanlayıcıyla sıcaklık kontrollü laboratuvar uygulamaları için kullanılır.",
        'body' => "Weightlab {$model}, dijital ekran ve dokunmatik tuşlarla sıcaklık ayarı yapılabilen, paslanmaz çelik iç hazneli laboratuvar su banyosudur. PID sıcaklık kontrolü, su seviye emniyeti, aşırı sıcaklık alarmı ve solenoid valfli tahliye yapısı güvenli kullanım sağlar.",
        'features' => $waterBathFeatures,
        'specs' => [
            'Model' => $model,
            'Cihaz tipi' => 'Dijital',
            'Isıtma aralığı' => 'RT+5-100 °C',
            'Sıcaklık hassasiyeti' => '±0.1 °C',
            'Zamanlayıcı süresi' => '0-9999 dakika',
            'Kapasite' => $capacity,
            'Ağırlık' => $weight,
            'İç boyutlar (UxGxY)' => $innerDimensions,
            'Dış boyutlar (UxGxY)' => $outerDimensions,
            'Güç' => $power,
        ],
        'documents' => $docs['wfsb'],
        'image_alt' => "Weightlab {$model} su banyosu ürün görseli",
    ];
}

$centrifugeFeatures = [
    'Hızlı ve rahat ayarlama için sezgisel arayüz',
    'Güvenli çalışma için otomatik kesmeli dengesizlik dedektörü',
    'Bakım gerektirmeyen fırçasız DC motor',
    'Kapak kilidi güvenliği',
    'Hız ve zaman göstergeli dijital ekran',
    'RPM / RCF dönüşümü',
    'Dijital geri sayım sayacı',
    'Elektrik kesintisi sırasında acil durum kapağı açma',
    'Otomatik dahili teşhis ve hata göstergesi',
    'Sessiz ve güvenli çalışma',
];

$products[] = [
    'category_slug' => 'santrifujler',
    'name' => 'Weightlab WN-CL6500 Genel Amaçlı Klinik Santrifüj',
    'slug' => 'weightlab-wn-cl6500-genel-amacli-klinik-santrifuj',
    'image_slugs' => ['weightlab-wn-cl6500-genel-amacli-klinik-santrifuj', 'wn-cl6500-klinik-santrifuj'],
    'model' => 'WN-CL6500',
    'old_url' => 'https://www.weightlabinstruments.com/urun/genel-amacli-santrifuj-klinik-santrifuj/#product-desc-content-67',
    'summary' => 'Weightlab WN-CL6500 genel amaçlı klinik santrifüj; 8 x 15 ml rotor kapasitesi, 500-6500 rpm hız ayarı, 3873 x g maksimum RCF ve fırçasız DC motor ile kullanılır.',
    'body' => 'Weightlab WN-CL6500, klinik ve genel amaçlı laboratuvar santrifüj uygulamaları için kompakt dijital masa üstü çözüm sunar. Otomatik dengesizlik algılama, kapak kilidi, RPM/RCF dönüşümü ve sessiz çalışma yapısı güvenli analiz süreçlerini destekler.',
    'features' => $centrifugeFeatures,
    'specs' => ['Model' => 'WN-CL6500', 'Cihaz tipi' => 'Dijital, masa üstü', 'Motor tipi' => 'Fırçasız DC motor', 'Rotor kapasitesi' => '8 x 15 ml', 'Hız ayarı' => '500-6500 rpm', 'Maksimum RCF' => '3873 x g', 'Hız doğruluğu' => '±100 rpm', 'Çalışma süresi' => '1-30 dakika ve süresiz mod', 'Hızlanma süresi' => '43 ± 2 saniye', 'Yavaşlama süresi' => '40 ± 2 saniye', 'Gürültü seviyesi' => '< 60 dB', 'Boyut (L x W x H)' => '260 x 244 x 203 mm', 'Ağırlık' => '4 kg (rotorla birlikte)', 'Güç tüketimi' => '120 W'],
    'documents' => $docs['wncl6500'],
    'image_alt' => 'Weightlab WN-CL6500 genel amaçlı klinik santrifüj ürün görseli',
];

$products[] = [
    'category_slug' => 'santrifujler',
    'name' => 'Weightlab WN-CM4500 Çok Yönlü Laboratuvar Santrifüj',
    'slug' => 'weightlab-wn-cm4500-cok-yonlu-laboratuvar-santrifuj',
    'image_slugs' => ['weightlab-wn-cm4500-cok-yonlu-laboratuvar-santrifuj', 'wn-cm4500-laboratuvar-santrifuj'],
    'model' => 'WN-CM4500',
    'old_url' => 'https://www.weightlabinstruments.com/urun/cok-yonlu-laboratuvar-santifuj/#product-desc-content-66',
    'summary' => 'Weightlab WN-CM4500 çok yönlü laboratuvar santrifüj; 4 x 100 ml maksimum kapasite, 500-4500 rpm hız ayarı, 99 program ve BLDC motor ile kullanılır.',
    'body' => 'Weightlab WN-CM4500, farklı rotor uyumlulukları için 4500 rpm’ye kadar hız sunan çok yönlü dijital laboratuvar santrifüjüdür. Program modu, RPM/RCF hız düzenleme, son çalıştırma hafızası, kapak kilidi ve otomatik dengesizlik tespitiyle güvenli çalışma sağlar.',
    'features' => ['Tüm uyumlu rotorlar için 4500 rpm’ye kadar hız', '99 farklı program özelliği', 'Bakım gerektirmeyen BLDC motor sürücüsü', 'Otomatik kesmeli dengesizlik tespit güvenliği', 'Kapak kilidi güvenlik özelliği', 'Özelleştirilmiş çalışma için program modu', 'RPM / RCF moduyla hız düzenleme', '1-99 dakika geri sayım sayacı', 'Son çalıştırma hafızası', 'Rahat ve kolay kullanıcı arayüzü', 'Elektrik kesintisi sırasında acil durum kapısı açma', 'Otomatik dahili teşhis ve hata göstergesi'],
    'specs' => ['Model' => 'WN-CM4500', 'Cihaz tipi' => 'Dijital, masa üstü', 'Motor tipi' => 'Fırçasız DC motor', 'Maksimum kapasite' => '4 x 100 ml', 'Hız ayarı' => '500-4500 rpm, 100 rpm adımlarla', 'Ekran' => 'Arkadan aydınlatmalı LED ekran', 'Çalışma süresi' => '1-99 dakika ve sonsuz mod', 'Minimum hızlanma süresi' => '30 saniye (9 farklı hızlanma ayarı)', 'Minimum yavaşlama süresi' => '30 saniye (9 farklı yavaşlama ayarı)', 'Gürültü seviyesi' => '65 dB', 'Ortam sıcaklığı' => '5-40 °C', 'İzin verilen bağıl nem' => '80%', 'Boyutlar (L x B x H)' => '475 x 585 x 325 mm', 'Ağırlık' => '23 kg (rotorsuz)', 'Güç tüketimi' => '460 W'],
    'documents' => [],
    'image_alt' => 'Weightlab WN-CM4500 çok yönlü laboratuvar santrifüj ürün görseli',
];

$products[] = [
    'category_slug' => 'santrifujler',
    'name' => 'Weightlab WN-15CMR Soğutmalı Mikro Santrifüj',
    'slug' => 'weightlab-wn-15cmr-sogutmali-mikro-santrifuj',
    'image_slugs' => ['weightlab-wn-15cmr-sogutmali-mikro-santrifuj', 'wn-15cmr-sogutmali-mikro-santrifuj', 'wn-cm15r-sogutmali-mikro-santrifuj'],
    'model' => 'WN-15CMR',
    'old_url' => 'https://www.weightlabinstruments.com/urun/sogutmali-mikro-santrifuj/#product-desc-content-68',
    'summary' => 'Weightlab WN-15CMR soğutmalı mikro santrifüj; 24 x 1.5/2 ml rotor kapasitesi, 500-15000 rpm hız aralığı, 22388 g maksimum RCF ve -20 °C ile +40 °C sıcaklık ayarıyla kullanılır.',
    'body' => 'Weightlab WN-15CMR, mikro tüp uygulamaları için soğutmalı dijital santrifüj çözümüdür. 15.000 rpm’de 4 °C sabit sıcaklık, hızlı soğutma modu, 99 programa kadar kullanıcı programı, otomatik dengesizlik algılama ve BLDC motor yapısıyla güvenli çalışma sağlar.',
    'features' => ['Sezgisel basit arayüz', 'Opsiyonel rotorla maksimum 44 x 1.5/2 ml kapasite', 'Mikroişlemci kontrollü değişken hız ve zaman ayarı', 'Son çalıştırma hafızası', '30 saniyeden 999 dakikaya kadar zamanlayıcı ve süresiz çalışma', '-20 °C ile +40 °C arasında sıcaklık ayarı', '15.000 rpm maksimum hızda 4 °C sabit sıcaklık', 'Rotor bölmesini ortam sıcaklığından 4 °C’ye 16 dakikada indiren hızlı soğutma modu', 'Geniş arkadan aydınlatmalı LCD ekran', '99 programa kadar kullanıcı programı', 'Otomatik kesmeli dengesizlik dedektörü', 'Bakım gerektirmeyen fırçasız DC motor'],
    'specs' => ['Rotor kapasitesi' => '24 x 1.5/2 ml mikro tüp; 0.1/0.2 ve 0.5 ml tüpler için redüksiyon adaptörleri', 'Opsiyonel rotor seçenekleri' => '44 x 1.5/2 ml mikro tüp, 4 PCR strips rotor, 8 x 5 ml', 'Maksimum RPM / RCF' => '500-15000 / 22388 g (24 x 1.5-2 ml rotor ile)', 'Zamanlama ayarı' => '30 saniye ile 999 dakika arası veya süresiz mod', 'Ekran' => 'Dijital ekran', 'Dengesizlik algılama' => 'Dengeyi bozacak numune yerleşiminde hatayı algılayıp işlemi otomatik kapatır', 'Gürültü seviyesi' => '< 60 dB', 'Rotor' => 'Kapaklı rotor', 'Motor tipi' => 'BLDC DC motor', 'Hızlanma süresi' => '< 72 saniye', 'Yavaşlama süresi' => '< 74 saniye', 'Boyutlar' => '452 x 314 x 278 mm', 'Adaptörler' => '0.1/0.2 ml mikro tüp adaptörü; 0.4/0.5 ml mikro tüp adaptörü'],
    'documents' => $docs['wn15cmr'],
    'image_alt' => 'Weightlab WN-15CMR soğutmalı mikro santrifüj ürün görseli',
];

$products[] = [
    'category_slug' => 'santrifujler',
    'name' => 'Weightlab WN-CMV6000 Mikro Santrifüj',
    'slug' => 'weightlab-wn-cmv6000-mikro-santrifuj',
    'image_slugs' => ['weightlab-wn-cmv6000-mikro-santrifuj', 'wn-cmv6000-mikro-santrifuj'],
    'model' => 'WN-CMV6000',
    'old_url' => 'https://www.weightlabinstruments.com/urun/wn-cmv6000-mikro-santrifuj/#product-desc-content-69',
    'summary' => 'Weightlab WN-CMV6000 mikro santrifüj; 8 x 1.5/2 ml açılı rotor, 1000-6000 rpm hız aralığı, 2000 g maksimum RCF ve sessiz çalışma yapısıyla mikro tüp uygulamaları için kullanılır.',
    'body' => 'Weightlab WN-CMV6000, bakım gerektirmeyen DC motor sistemi, dengesizlik emniyeti, elektronik emniyet freni ve değiştirilebilir rotorlarıyla kompakt dijital mikro santrifüj çözümüdür. Küçük tasarımı çeker ocak altında ve soğuk odalarda kullanım için uygundur.',
    'features' => [
        'Bakım gerektirmeyen DC motor sistemi',
        '8 x 1.5/2 ml kapasiteli açılı rotor',
        'Düşük ısı üretimi ile sıcaklığa duyarlı numuneleri stabil tutma',
        '6000 dev/dakikaya kadar hız ve zaman ayarı',
        '100 rpm adımlarla değişken hız ayarı',
        'Dijital zamanlayıcı programlama',
        'Son çalışılan ayarları hafızada tutma',
        'Dengesizlik emniyeti kontrolü',
        '< 55 dB sessiz ve titreşimsiz çalışma',
        'Dijital kalibrasyon fonksiyonu',
        'Kapak açıklığında elektronik emniyet freni',
        'Kauçuk vakumlu ayaklar',
        'Değiştirilebilir rotorlar ve PCR şerit rotoru',
        'Kompakt tasarım',
        'Uzun ömürlü sağlam yapı',
    ],
    'specs' => ['Cihaz tipi' => 'Dijital, masa üstü', 'Hız aralığı' => '1000-6000 rpm, 100 rpm adımlarla; doğruluk ±100 rpm', 'Maksimum RCF' => '2000 g', 'Rotor ve adaptörler' => '8 x 1.5/2 ml mikrotüp açılı rotor; PCR strip rotor (2 x 8 x 0.2 ml); 0.2 ve 0.4/0.5 ml tüpler için adaptörler', 'Zamanlayıcı süresi' => '1-25 dakika veya süresiz sürekli çalışma', 'Dengesizlik tespiti' => 'Dengeyi bozacak numune yerleşiminde algılayıp otomatik kapatır', 'Otomatik emniyet freni' => 'Çalışırken kapak açıldığında elektronik frenle durur', 'Gürültü seviyesi' => '< 55 dB', 'Hızlı döndürme' => 'Kapağı kapatarak hızlı çalışma', 'Kauçuk vakumlu ayaklar' => 'Bulunduğu yerde kaymasını engeller', 'Ağırlık' => '1.1 kg', 'Boyutlar (GxDxY)' => '162 x 157 x 115 mm'],
    'documents' => $docs['wncmv6000'],
    'image_alt' => 'Weightlab WN-CMV6000 mikro santrifüj ürün görseli',
];

$products[] = [
    'category_slug' => 'santrifujler',
    'name' => 'Weightlab WN-CM15N Yüksek Hızlı Mikro Santrifüj',
    'slug' => 'weightlab-wn-cm15n-yuksek-hizli-mikro-santrifuj',
    'image_slugs' => ['weightlab-wn-cm15n-yuksek-hizli-mikro-santrifuj', 'wn-cm15n-yuksek-hizli-mikro-santrifuj'],
    'model' => 'WN-CM15N',
    'old_url' => 'https://www.weightlabinstruments.com/urun/yuksek-hizli-mikro-santrifuj/#product-desc-content-70',
    'summary' => 'Weightlab WN-CM15N yüksek hızlı mikro santrifüj; 12 x 1.5/2 ml rotor kapasitesi, 500-15000 rpm hız ayarı, 15596 x g maksimum RCF ve fırçasız DC motor ile kullanılır.',
    'body' => 'Weightlab WN-CM15N, yüksek hızlı mikro tüp santrifüj uygulamaları için dijital masa üstü yapı sunar. RPM/RCF dönüşümü, otomatik dengesizlik dedektörü, kapak kilidi ve opsiyonel PCR şerit rotoru ile güvenli ve esnek kullanım sağlar.',
    'features' => [
        'Sezgisel ve basit arayüz',
        'Maksimum 12 x 1.5/2 ml kapasite',
        'Otomatik kesmeli dengesizlik dedektörü',
        'Bakım gerektirmeyen fırçasız DC motor',
        'İşlem tamamlandıktan sonra otomatik açılan kapak kilidi',
        'Hız ve zaman göstergeli dijital ekran',
        '500-15000 rpm hız ayarı',
        'Tek dokunuşla RPM / RCF dönüşümü',
        'Dijital geri sayım sayacı',
        'Tüp redüksiyon adaptörleri',
        'Elektrik kesintisinde acil durum kapağı açma',
        'Otomatik dahili teşhis ve hata göstergesi',
        'Sessiz ve güvenli çalışma',
        'Opsiyonel mini hematokrit rotoru ve PCR şerit rotoru',
        'Davlumbazlarda veya soğuk odalarda kullanıma uygun',
    ],
    'specs' => ['Model' => 'WN-CM15N', 'Cihaz tipi' => 'Dijital, masa üstü', 'Motor tipi' => 'Fırçasız DC motor', 'Rotor kapasitesi' => '12 x 1.5/2.0 ml mikro tüp', 'Hız ayarı' => '500-15000 rpm', 'Maksimum RCF' => '15596 x g', 'Hız doğruluğu' => '±100 rpm', 'Çalışma süresi' => '1-99 dakika ve süresiz mod', 'Hızlanma süresi' => '65 ± 2 saniye', 'Yavaşlama süresi' => '55 ± 2 saniye', 'Gürültü seviyesi' => '< 60 dB', 'Boyut (L x W x H)' => '190 x 120 x 270 mm', 'Ağırlık' => '2450 g', 'Güç tüketimi' => '72 W', 'Opsiyonel rotor seçenekleri' => '2 PCR strips rotor'],
    'documents' => $docs['wncm15n'],
    'image_alt' => 'Weightlab WN-CM15N yüksek hızlı mikro santrifüj ürün görseli',
];

foreach ([
    ['WN-RD', 'Dijital Disk Rotator', 'weightlab-wn-rd-dijital-disk-rotator', ['dijital-disk-rotator'], '12 x 50 ml veya 24 x 15 ml veya 44 x 5 ml', '383 x 325 x 325 mm', $docs['wnrd'], 'https://www.weightlabinstruments.com/urun/dijital-disk-rotator/#product-desc-content-74'],
    ['WN-RP', 'Dijital Plate Rotator', 'weightlab-wn-rp-dijital-plate-rotator', ['dijital-plate-rotator'], '16 x 50 ml veya 35 x 15 ml veya 55 x 2 ml', '540 x 274 x 186 mm', $docs['wnrp'], 'https://www.weightlabinstruments.com/urun/dijital-plate-rotator/#product-desc-content-75'],
] as [$model, $nameSuffix, $slug, $imageSlugs, $capacity, $dimensions, $documents, $oldUrl]) {
    $products[] = [
        'category_slug' => 'rotator-calkalayici',
        'name' => "Weightlab {$nameSuffix}",
        'slug' => $slug,
        'image_slugs' => [$slug, ...$imageSlugs],
        'model' => $model,
        'old_url' => $oldUrl,
        'summary' => "Weightlab {$model} {$nameSuffix}; 10-80 rpm hız aralığı, 1 rpm hassasiyet, 9 program hafızası, pulse modu ve {$capacity} plate kapasitesiyle rotasyon/çalkalama uygulamalarında kullanılır.",
        'body' => "Weightlab {$model}, değiştirilebilir plate seçenekleri, mikroprosesör kontrollü ayarlar ve bakım gerektirmeyen BLDC motoruyla dijital masa üstü rotatör çözümü sunar. Plate açısı 0-90 derece arasında ayarlanabilir.",
        'features' => [
            '50, 15 ve 5 ml için farklı plate seçenekleri',
            'Seçilmiş bir plate ile birlikte gelir',
            'Diğer plate seçenekleri ayrıca siparişe eklenebilir',
            'Mikroprosesör işlemci ile son ayarları hafızada tutma',
            'Değiştirme ve temizlik için kolay sökülüp takılan plakalar',
            'Sessiz ve bakım gerektirmeyen BLDC motor',
        ],
        'specs' => ['Model adı' => $model, 'Cihaz tipi' => 'Dijital, masa üstü', 'Kapasite' => $capacity, 'Hız aralığı' => '10-80 rpm, 1 rpm adımlarla; doğruluk ±1 rpm', 'Programlama modu' => 'Ayarlanabilen 9 program hafızası', 'Pulse modu' => '2 sn duraklamalarla çift yönlü güçlü çalkalama', 'Plate açısı' => '0-90 derece arası ayarlanabilir', 'Zamanlayıcı süresi' => '1-99 dakika veya süresiz sürekli çalışma', 'Motor tipi' => 'BLDC', 'Koruma sınıfı' => 'IP 21', 'Otomatik başlatma modu' => 'Elektrik kesintisi sonrasında kaldığı süreden ve en son çalıştığı değerlerden başlar', 'Birlikte verilenler' => 'Seçilmiş bir plate ile gelir, diğerleri ayrıca siparişe eklenebilir', 'Boyutlar (GxDxY)' => $dimensions, 'Ağırlık' => '2 kg'],
        'documents' => $documents,
        'image_alt' => "Weightlab {$model} {$nameSuffix} ürün görseli",
    ];
}

$wmmpFeatures = [
    'Tüm parçaları sökmeden tamamen otoklavlanabilir',
    'ISO 17025 akredite laboratuvarda ISO 8655 standardına göre kalibrasyon',
    'ISO 8655 kalibrasyon raporuyla birlikte gelir',
    'Kullanıcı tarafından kolay yeniden kalibrasyon',
    'Yüksek kaliteli yay mekanizması',
    '0.2 µl’den 10 ml’ye kadar geniş hacim seçeneği',
    'Eldivenle kullanılabilir pistonla hacim ayarı',
    'Ergonomik ve rahat tutuş',
    'Standart pipet uçlarıyla uyumluluk',
    'Yumuşak tıklamalı hacim ayarı',
    '4 haneli ekran',
    'Renk kodlu hacim aralıkları',
    'Entegre aerodinamik uç çıkarıcı',
];

foreach ([
    ['WMMP-10', '0.5-10 µl', '0.02 µl', '1%', '0.5%'],
    ['WMMP-20', '2-20 µl', '0.02 µl', '0.8%', '0.4%'],
    ['WMMP-50', '5-50 µl', '0.1 µl', '0.8%', '0.4%'],
    ['WMMP-100', '10-100 µl', '0.2 µl', '0.6%', '0.2%'],
    ['WMMP-200', '20-200 µl', '0.2 µl', '0.6%', '0.2%'],
    ['WMMP-1000', '100-1000 µl', '1 µl', '0.6%', '0.2%'],
    ['WMMP-5000', '500-5000 µl', '10 µl', '0.6%', '0.2%'],
    ['WMMP-10000', '1000-10000 µl', '20 µl', '0.6%', '0.2%'],
] as [$model, $volume, $increment, $error, $uncertainty]) {
    $modelSlug = strtolower($model);
    $products[] = [
        'category_slug' => 'pipetler',
        'name' => "Weightlab {$model} Komple Otoklavlanabilir Otomatik Pipet",
        'slug' => "weightlab-{$modelSlug}-komple-otoklavlanabilir-otomatik-pipet",
        'image_slugs' => ["weightlab-{$modelSlug}-komple-otoklavlanabilir-otomatik-pipet", strtolower(str_replace('-', '-', $model)) . '-komple-otoklavlanabilir-otomatik-pipetler'],
        'model' => $model,
        'old_url' => 'https://www.weightlabinstruments.com/urun/wmmp-serisi-komple-otoklavlanabilir-otomatik-pipetler/#product-desc-content-76',
        'summary' => "Weightlab {$model} otomatik pipet; {$volume} hacim aralığı, {$increment} artış adımı, {$error} hata payı ve {$uncertainty} belirsizlik değeriyle sıvı hacim aktarımı için kullanılır.",
        'body' => "Weightlab {$model}, tamamen otoklavlanabilir WMMP serisi otomatik pipettir. Ergonomik gövde, 4 haneli ekran, renk kodlu hacim aralığı ve ISO 8655 kalibrasyon raporu ile laboratuvar pipetleme işlemlerini destekler.",
        'features' => $wmmpFeatures,
        'specs' => ['Hacim aralığı' => $volume, 'Kat. No.' => $model, 'Artış adımı' => $increment, 'Hata payı (±)' => $error, 'Belirsizlik (±)' => $uncertainty],
        'documents' => $docs['wmmp'],
        'image_alt' => "Weightlab {$model} komple otoklavlanabilir otomatik pipet ürün görseli",
    ];
}

$wapFeatures = [
    'Çok hafif elektronik pipet',
    'Ultra ergonomik tasarım',
    'Yüksek performanslı kademesiz motor',
    '3 nokta hassas otomatik kalibrasyon',
    'ISO 17025 akredite laboratuvarda ISO 8655 kalibrasyonu',
    '5 farklı pipetleme modu',
    'Dahili güç tasarrufu ve Intelli şarj bataryası',
    'Renk kodlu tanımlama',
    'Parola korumalı kalibrasyon',
    'Sık kullanılan pipetleme modlarını hafızada saklama',
    '3 yıl garanti',
    'Evrensel uyumlu pipet uçları',
    'Sezgisel grafik arayüz',
    'Otoklavlanabilir alt takım',
    'Pipetleme, ters pipetleme, çoklu dispense, seyreltme ve mixing modları',
];

foreach ([
    ['WAP10', '0.5-10 µl', '0.01 µl'],
    ['WAP100', '5-100 µl', '0.1 µl'],
    ['WAP200', '10-200 µl', '0.1 µl'],
    ['WAP1000', '50-1000 µl', '1 µl'],
    ['WAP5000', '250-5000 µl', '5 µl'],
] as [$model, $volume, $increment]) {
    $modelSlug = strtolower($model);
    $products[] = [
        'category_slug' => 'pipetler',
        'name' => "Weightlab {$model} Elektronik Pipet",
        'slug' => "weightlab-{$modelSlug}-elektronik-pipet",
        'image_slugs' => ["weightlab-{$modelSlug}-elektronik-pipet", "{$modelSlug}-elektronik-pipet"],
        'model' => $model,
        'old_url' => 'https://www.weightlabinstruments.com/urun/elektronik-pipetler/#product-desc-content-77',
        'summary' => "Weightlab {$model} elektronik pipet; {$volume} hacim aralığı, {$increment} artış adımı, elektronik motor kontrolü ve çoklu pipetleme modlarıyla hassas sıvı aktarımı için kullanılır.",
        'body' => "Weightlab {$model}, ergonomik elektronik pipetleme için geliştirilmiş WAP serisi modeldir. Otomatik kalibrasyon, parola koruması, dahili batarya, grafik arayüz ve farklı pipetleme modlarıyla tekrarlanabilir sıvı aktarımı sağlar.",
        'features' => $wapFeatures,
        'specs' => ['Hacim aralığı' => $volume, 'Kat. No.' => $model, 'Artış adımı' => $increment, 'Pipetleme modları' => 'Pipetleme, ters pipetleme, çoklu dispense, seyreltme, mixing', 'Kalibrasyon' => 'ISO 8655 normlarına göre kalibre edilmiştir', 'Batarya' => '2 saatte şarj, 5 saat sürekli pipetleme', 'Uyku modu' => '60 saniyede uyku moduna geçerek %20 pil tasarrufu'],
        'documents' => $docs['wap'],
        'image_alt' => "Weightlab {$model} elektronik pipet ürün görseli",
    ];
}

$products[] = [
    'category_slug' => 'su-banyosu',
    'name' => 'Weightlab WF-SBC30 Çalkalamalı Su Banyosu',
    'slug' => 'weightlab-wf-sbc30-calkalamali-su-banyosu',
    'image_slugs' => ['weightlab-wf-sbc30-calkalamali-su-banyosu', 'wf-sbc30-calkalamali-su-banyosu'],
    'model' => 'WF-SBC30',
    'old_url' => 'https://www.weightlabinstruments.com/urun/calkalamali-su-banyosu/#product-desc-content-81',
    'summary' => 'Weightlab WF-SBC30 çalkalamalı su banyosu; RT-100 °C sıcaklık aralığı, 20-180 rpm pistonlu frekans, 31 litre iç hacim ve 0-9999 dakika zamanlayıcı ile kullanılır.',
    'body' => 'Weightlab WF-SBC30, pistonlu salınımlı termostatik su banyosu olarak sabit sıcaklıkta homojen çalkalama sağlar. LCD PID kontrol, paslanmaz çelik su tankı, PT100 sensör ve çatı tipi üst kapak sistemiyle güvenli kullanım sunar.',
    'features' => ['Geniş LCD ekranda sıcaklık, set değeri, süre ve kalan süre gösterimi', 'PID kontrollü yüksek hassasiyetli hız kontrolü', 'Ayarlanabilir başlangıç hızı ile sıçramayı önleme', 'RT-100 °C sıcaklık kontrolü', '20-180 rpm hız aralığı', 'Korozyona dayanıklı paslanmaz çelik yüzeyler', 'Standart paslanmaz çelik yaylı ataçman', 'Farklı erlen ve balonlar için aksesuar seçenekleri', 'Aşırı hızda otomatik durma', 'Sıvı kaybını ve damlacık dağılımını önleyen çatı tipi üst kapak'],
    'specs' => ['Model' => 'WF-SBC30', 'Mod' => 'Pistonlu salınımlı + termostatik su banyosu', 'Sıcaklık aralığı' => 'RT-100 °C', 'Sıcaklık hassasiyet oranı' => '0.1 °C', 'Sıcaklık artışı' => '±1 °C', 'Sıcaklık üniformasyonu' => '±2 °C', 'Karşılıklı salınım genliği' => '16 veya 24 mm (fabrika çıkışı 16 mm)', 'Pistonlu frekans aralığı' => '20-180 r/min', 'Pistonlu frekans hassasiyeti' => '±1 rpm', 'Su tankı' => 'Ayna paslanmaz çelik', 'Güç derecesi' => '1.5 kW', 'Kontrol' => 'LCD PID akıllı kontrol', 'Zamanlayıcı' => '0-9999 dakika', 'Sıcaklık sensörü' => 'PT100', 'Su tankı boyutu' => '500 x 310 x 200 mm', 'Dış boyut' => '828 x 360 x 425 mm', 'Sepet hacmi' => '395 x 250 mm', 'Ölçülere göre hacim' => '100 ml x 12, 250 ml x 8, 500 ml x 6', 'İç hacim' => '31 litre', 'Sepet yatağı' => '5 kg/katman', 'Güç kaynağı' => 'AC220V/6.8A', 'NW/GW' => '20/30 kg'],
    'documents' => $docs['wfsbc30'],
    'image_alt' => 'Weightlab WF-SBC30 çalkalamalı su banyosu ürün görseli',
];

$products[] = [
    'category_slug' => 'sogutmali-inkubator',
    'name' => 'Weightlab WF-LTC70R Çalkalamalı Soğutmalı İnkübatör',
    'slug' => 'weightlab-wf-ltc70r-calkalamali-sogutmali-inkubator',
    'image_slugs' => ['weightlab-wf-ltc70r-calkalamali-sogutmali-inkubator', 'wf-ltc70r-calkalamali-sogutmali-inkubator'],
    'model' => 'WF-LTC70R',
    'old_url' => 'https://www.weightlabinstruments.com/urun/Caklamali-sogutmali-inkubator/#product-desc-content-342',
    'summary' => 'Weightlab WF-LTC70R çalkalamalı soğutmalı inkübatör; 70 litre iç hacim, 4-65 °C sıcaklık aralığı, 30-300 rpm döner hız ve 0-9999 dakika zamanlayıcıyla kullanılır.',
    'body' => 'Weightlab WF-LTC70R, soğutmalı inkübasyon ve çalkalama işlemlerini tek cihazda birleştirir. LCD PID akıllı kontrol, fırçasız DC motor, geniş gözlem penceresi, soğutma sistemi ve UV güvenlik kesme fonksiyonu laboratuvar uygulamalarını destekler.',
    'features' => ['LCD ekranda sıcaklık, hız ve çalışma süresi gösterimi', 'Kullanımı kolay menü arayüzü', 'Mikrobilgisayar kontrollü sıcaklık, zaman ve salınım frekansı', 'Dahili kapanma koruması ve güç sonrası otomatik devam', 'Hava kanalı ile sirkülasyon', 'Büyük gözlem penceresi', 'Akıllı fırçasız DC motor', 'Yavaş başlangıç tasarımı', 'Hız kontrolden çıkarsa otomatik kilitleme', 'Sıçramayı önleyen ayarlanabilir başlangıç hızı', 'Kapı açıkken UV lambasını kesme fonksiyonu'],
    'specs' => ['Çalkalama modu' => 'Siklotron titreşim', 'Genlik' => 'Φ20 mm', 'Sıcaklık kararlılığı' => '0.1 °C', 'Sıcaklık hassasiyeti' => '±0.1 °C', 'Sıcaklık doğruluğu' => '±1 °C', 'Sıcaklık aralığı' => '4-65 °C', 'Döner hız' => '30-300 rpm', 'Döner hız hassasiyeti' => '±1 rpm', 'Bölme malzemesi' => 'Paslanmaz çelik', 'Dış gövde' => 'ABS', 'Soğutma sistemi' => 'Var', 'Soğutucu' => 'R134a', 'Isıtıcı' => 'Paslanmaz çelik ısıtma tüpü', 'Güç' => '0.8 kW', 'Kontrol' => 'LCD PID akıllı kontrol', 'Zamanlayıcı' => '0-9999 dakika', 'Sıcaklık sensörü' => 'PT100', 'İç hazne boyutu' => '560 x 390 x 320 mm', 'Dış ölçüler' => '600 x 770 x 500 mm', 'Raf boyutu' => '480 x 315 mm', 'Maksimum raf kapasitesi' => '100 ml x 24 / 250 ml x 12 / 500 ml x 6 / 1000 ml x 6', 'Standart şişe kıskacı' => '250 ml x 12', 'İç hacim' => '70 L', 'Raf taşıyıcı' => '≤15 kg/katman', 'Raf katmanı' => '1', 'Güç kaynağı' => 'AC220V/3.5A', 'Net/paket ağırlığı' => '65/85 kg'],
    'documents' => $docs['wfltc70r'],
    'image_alt' => 'Weightlab WF-LTC70R çalkalamalı soğutmalı inkübatör ürün görseli',
];

$vacuumOvenFeatures = [
    'Dört taraftan ısıtma ile raflara eşit sıcaklık dağılımı',
    'Paslanmaz çelik iç hazne ve kolay temizlenen kavisli köşeler',
    'Silikon contalı kapak ile sızdırmazlık ve vakum',
    'PID mikroişlemci kontrolü',
    '0-9999 dakika zamanlama',
    'Aşırı sıcaklık koruma fonksiyonu',
    'Kullanıcı dostu dijital gösterge sistemi',
    'Sabitleme kolu ile pratik kapak kullanımı',
    'Çift katmanlı gözetleme camı ve dış reçine koruma plakası',
    'Aşırı sıcaklıkta sesli ve ışıklı alarm',
];

foreach ([
    ['WF-HTV25', '24 L', '0.8 kW', '80 dak.', '525 x 480 x 620 mm', '480 x 480 x 606 mm', '590 x 550 x 750 mm', '100 mm', 'AC220V/3.6A', '42/52 kg', 'https://www.weightlabinstruments.com/urun/vakumlu-etuvler/#product-desc-content-73'],
    ['WF-HTV52', '52 L', '1.4 kW', '100 dak.', '415 x 370 x 340 mm', '560 x 540 x 680 mm', '704 x 620 x 814 mm', '140 mm', 'AC220V/6.3A', '67/92 kg', 'https://www.weightlabinstruments.com/urun/vakumlu-etuvler/#product-desc-content-201'],
] as [$model, $volume, $power, $heatingTime, $innerDimensions, $outerDimensions, $packageDimensions, $shelfArea, $current, $weight, $oldUrl]) {
    $modelSlug = strtolower($model);
    $products[] = [
        'category_slug' => 'etuv',
        'name' => "Weightlab {$model} Vakumlu Etüv",
        'slug' => "weightlab-{$modelSlug}-vakumlu-etuv",
        'image_slugs' => ["weightlab-{$modelSlug}-vakumlu-etuv", "{$modelSlug}-vakumlu-etuv"],
        'model' => $model,
        'old_url' => $oldUrl,
        'summary' => "Weightlab {$model} vakumlu etüv; {$volume} hacim, oda sıcaklığı +10 ile 250 °C sıcaklık aralığı, <133 Pa vakum derecesi ve PID kontrol sistemiyle kullanılır.",
        'body' => "Weightlab {$model}, vakum altında kurutma ve ısıtma uygulamaları için dört duvardan ısı iletimi sağlayan vakumlu etüvdür. Paslanmaz çelik iç hazne, silikon contalı kapak, PT100 sensör ve aşırı sıcaklık alarmı ile güvenli çalışma sağlar.",
        'features' => $vacuumOvenFeatures,
        'specs' => ['Model' => $model, 'Isıtma modu' => 'Dört duvardan ısı iletimi ve vakum', 'Sıcaklık aralığı' => 'Oda sıcaklığı +10, maksimum etüv sıcaklığı +250 °C', 'Uygun vakum derecesi aralığı' => '< 133 Pa', 'Sıcaklık hassasiyet oranı' => '0.1 °C', 'Gösterge artışı' => '±1 °C', 'Isıtma maksimum' => $heatingTime, 'İç oda' => 'Yüksek kaliteli ince paslanmaz çelik sac', 'Dış kabuk' => 'Soğuk hadde çeliği', 'Yalıtım katmanı' => 'Alüminyum silikat elyaf', 'Isıtıcı' => 'Paslanmaz çelik elektrikli rezistans', 'Gözlem penceresi' => 'Temperli cam, akrilik dış koruma', 'Vakum ölçer' => 'Doğruluk sınıfı 2.5', 'Nozul çapı' => '10 mm', 'Güç' => $power, 'Sıcaklık kontrol modu' => 'PID sıcaklık kontrolü', 'Sıcaklık ayar modu' => 'Dokunmatik kontrol', 'Zamanlayıcı' => '0-9999 dakika', 'Sensör' => 'PT100', 'Güvenlik' => 'Aşırı sıcaklıkta sesli ve ışıklı alarm', 'İç hazne boyutu' => $innerDimensions, 'Dış boyut' => $outerDimensions, 'Paket boyutu' => $packageDimensions, 'Hacim' => $volume, 'Raf sayısı' => '2', 'Raf başına yük' => '15 kg', 'Raf alanı' => $shelfArea, '50/60 Hz mevcut derece' => $current, 'NW/GW' => $weight, 'Aksesuar' => '2 raf, 4 raf çerçevesi', 'İsteğe bağlı aksesuarlar' => 'Silikon hortum ve vakum pompası'],
        'documents' => $docs['wfhtv'],
        'image_alt' => "Weightlab {$model} vakumlu etüv ürün görseli",
    ];
}

$incubatorFeatures = [
    'PID sıcaklık kontrol teknolojisi',
    'Oda sıcaklığı +5 °C ile 70 °C’ye kadar sıcaklık kontrolü',
    'Paslanmaz çelik iç hazne ve soğuk haddelenmiş dış gövde',
    'Dijital ekran ve dokunmatik tuşlar',
    '5 derece açılı panel',
    'Manyetik şeritli kolay kapanan kapı',
    'Sabit sıcaklık, zamanlama ve otomatik durdurma fonksiyonları',
    'Çift kapılı yapı ile numune gözleminde düşük sıcaklık değişimi',
];

foreach ([
    ['WF-LT45', '45 L', '0.35 kW', '350 x 350 x 350 mm', '525 x 480 x 620 mm', '605 x 572 x 775 mm', '7', 'AC220V/1.1A', '27/30 kg', ['weightlab-wf-lt45-inkubator', 'weightlab-wf-lt-45-sogutmasiz-inkubator'], 'https://www.weightlabinstruments.com/urun/inkubatorler/#product-desc-content-72'],
    ['WF-LT65', '65 L', '0.45 kW', '400 x 350 x 450 mm', '575 x 480 x 720 mm', '655 x 572 x 875 mm', '9', 'AC220V/1.1A', '32/35 kg', ['weightlab-wf-lt65-inkubator', 'weightlab-wf-lt-65-sogutmasiz-inkubator'], 'https://www.weightlabinstruments.com/urun/inkubatorler/#product-desc-content-196'],
    ['WF-LT125', '125 L', '0.60 kW', '500 x 450 x 550 mm', '675 x 580 x 820 mm', '755 x 672 x 975 mm', '13', 'AC220V/2.3A', '45/49 kg', ['weightlab-wf-lt125-inkubator', 'weightlab-wf-lt125-sogutmasiz-inkubator'], 'https://www.weightlabinstruments.com/urun/inkubatorler/#product-desc-content-199'],
] as [$model, $volume, $power, $innerDimensions, $outerDimensions, $packageDimensions, $shelfCount, $current, $weight, $imageSlugs, $oldUrl]) {
    $modelSlug = strtolower($model);
    $products[] = [
        'category_slug' => 'inkubatorler',
        'name' => "Weightlab {$model} İnkübatör",
        'slug' => "weightlab-{$modelSlug}-inkubator",
        'image_slugs' => $imageSlugs,
        'model' => $model,
        'old_url' => $oldUrl,
        'summary' => "Weightlab {$model} inkübatör; {$volume} hacim, doğal konveksiyon, ortam sıcaklığı +5 ile 70 °C sıcaklık aralığı ve PID kontrol sistemiyle kullanılır.",
        'body' => "Weightlab {$model}, sabit sıcaklık inkübasyon uygulamaları için doğal konveksiyonlu dijital inkübatördür. Paslanmaz çelik iç hazne, çift kapılı yapı, dokunmatik kontrol ve PT100 sensörle kararlı çalışma sağlar.",
        'features' => $incubatorFeatures,
        'specs' => ['Model' => $model, 'Çevrim modu' => 'Doğal konveksiyon', 'Sıcaklık aralığı' => 'Ortam sıcaklığı +5 °C, maksimum inkübatör sıcaklığı +70 °C', 'Sıcaklık hassasiyet oranı' => '0.1 °C', 'Gösterge artışı' => '±0.5 °C', 'Sıcaklık homojenliği' => '% ±2.5', 'İç oda' => 'Paslanmaz çelik; dışında yüksek mukavemetli galvaniz levha', 'Dış kabin' => 'Soğuk hadde çeliği', 'Yalıtım katmanı' => 'Yüksek kalite taş yünü (CE)', 'Isıtıcı' => 'Mika elektrotermal film', 'Güç' => $power, 'Sıcaklık kontrol modu' => 'PID sıcaklık kontrolü', 'Sıcaklık ayar modu' => 'Dokunmatik kontrol', 'Zamanlayıcı' => '0-9999 dakika', 'Sensör' => 'PT100', 'Güvenlik' => 'Aşırı sıcaklıkta sesli ve ışıklı alarm', 'İç hazne boyutu' => $innerDimensions, 'Dış boyut' => $outerDimensions, 'Paket boyutu' => $packageDimensions, 'Hacim' => $volume, 'Raf sayısı' => $shelfCount, 'Raf başına yük' => '15 kg', 'Raf alanı' => '35 mm', 'Güç aralığı (50/60 Hz)' => $current, 'NW/GW' => $weight, 'Aksesuar' => '2 raf', 'İsteğe bağlı aksesuarlar' => 'Yedek raf'],
        'documents' => $docs['wflt'],
        'image_alt' => "Weightlab {$model} inkübatör ürün görseli",
    ];
}

$forcedOvenFeatures = [
    'Yeni dikey tasarım ve elektrostatik püskürtme dış yüzey',
    'Paslanmaz çelik iç hazne',
    'Ayarlanabilir raf aralığı',
    'Çift taraflı ısıtma hava geçişi',
    'PID kontrollü akıllı sıcaklık kontrolü',
    'Bağımsız sesli ve görsel sıcaklık sınırlamalı alarm sistemi',
    '0-9999 dakika zaman modu veya sonsuz çalışma',
    'Dikey çift kanal kaplama ve santrifüj fanı',
    'Türbin santrifüj fanı',
    'Temperli cam gözlem penceresi',
    'Ayarlanabilir hava geçirmez kilit ve silikon kauçuk conta',
];

foreach ([
    ['WF-HT45', '45 L', '1.2 kW', '350 x 350 x 350 mm', '490 x 540 x 730 mm', '590 x 625 x 885 mm', '7', 'AC220V/5.5A', '37/43 kg', ['WF-HT45-etuv-cihazi'], 'https://www.weightlabinstruments.com/urun/fanli-etuvler/#product-desc-content-71'],
    ['WF-HT65', '65 L', '1.6 kW', '400 x 360 x 450 mm', '540 x 550 x 830 mm', '690 x 695 x 985 mm', '9', 'AC220V/7.2A', '44/49 kg', ['WF-HT65-etuv-cihazi'], 'https://www.weightlabinstruments.com/urun/fanli-etuvler/#product-desc-content-193'],
    ['WF-HT125', '125 L', '2.3 kW', '500 x 450 x 550 mm', '640 x 640 x 930 mm', '740 x 725 x 108 mm', '13', 'AC220V/10.5A', '60/66 kg', ['WF-HT125-etuv-cihazi'], 'https://www.weightlabinstruments.com/urun/fanli-etuvler/#product-desc-content-194'],
] as [$model, $volume, $power, $innerDimensions, $outerDimensions, $packageDimensions, $shelfCount, $current, $weight, $imageSlugs, $oldUrl]) {
    $modelSlug = strtolower($model);
    $products[] = [
        'category_slug' => 'etuv',
        'name' => "Weightlab {$model} Fanlı Etüv",
        'slug' => "weightlab-{$modelSlug}-fanli-etuv",
        'image_slugs' => ["weightlab-{$modelSlug}-fanli-etuv", ...$imageSlugs],
        'model' => $model,
        'old_url' => $oldUrl,
        'summary' => "Weightlab {$model} fanlı etüv; {$volume} hacim, kuvvetli konveksiyon, ortam sıcaklığı +10 ile 300 °C sıcaklık aralığı ve PID kontrol sistemiyle kullanılır.",
        'body' => "Weightlab {$model}, forced-air/fanlı etüv uygulamaları için kuvvetli konveksiyonlu dijital laboratuvar etüvüdür. Paslanmaz çelik iç hazne, santrifüj fan, sıcaklık sınırlamalı alarm ve dokunmatik kontrol ile güvenli kurutma/ısıtma sağlar.",
        'features' => $forcedOvenFeatures,
        'specs' => ['Model' => $model, 'Çevrim modu' => 'Kuvvetli konveksiyon', 'Sıcaklık aralığı' => 'Ortam sıcaklığı +10 °C, maksimum etüv sıcaklığı +300 °C', 'Sıcaklık hassasiyet oranı' => '0.1 °C', 'Gösterge artışı' => '±1 °C', 'Sıcaklık homojenliği' => '% ±2.5', 'İç oda' => 'Paslanmaz çelik', 'Dış kabin' => 'Soğuk hadde çeliği', 'Yalıtım katmanı' => 'Yüksek kalite taş yünü (CE)', 'Isıtıcı' => 'Paslanmaz çelik', 'Güç' => $power, 'Çıkış hortumu' => 'Ø28 mm', 'Sıcaklık kontrol modu' => 'PID sıcaklık kontrolü', 'Sıcaklık ayar modu' => 'Dokunmatik kontrol', 'Zamanlayıcı' => '0-9999 dakika', 'Sensör' => 'PT100', 'İç hazne boyutu' => $innerDimensions, 'Dış boyut' => $outerDimensions, 'Paket boyutu' => $packageDimensions, 'Hacim' => $volume, 'Raf sayısı' => $shelfCount, 'Raf başına yük' => '15 kg', 'Raf alanı' => '35 mm', 'Güç aralığı (50/60 Hz)' => $current, 'NW/GW' => $weight, 'Aksesuar' => '2 raf, 4 raf çerçevesi'],
        'documents' => $docs['wfht'],
        'image_alt' => "Weightlab {$model} fanlı etüv ürün görseli",
    ];
}

$ultrasonicFeatures = [
    'Dijital zamanlayıcı ve ısıtıcı',
    'Dokunmatik kontrol ekranı',
    '20 derece eğimli tasarım',
    'Gaz giderme işlevi',
    'Kesintisiz ve etkili ultrasonik dalga boyu',
    'Kaymaz lastik ayaklar',
    'Paslanmaz çelik gövde',
    '20-80 °C ayarlanabilir sıcaklık',
    'Ultrasonik temizleme ve ısıtmanın aynı anda çalışması',
    'Paslanmaz çelik temizleme sepeti',
    '6.5 litre üzeri modellerde sıvı tahliye vanası',
];

foreach ([
    ['WF-UD2', '2 L', '80 W', '100 W', 'Yok', '150 x 135 x 100 mm', '175 x 160 x 210 mm', '2.4 kg', ['wf-ud2-ultrasonik-banyo-21', 'wf-ud2-ultrasonik-banyo-22']],
    ['WF-UD4', '4.5 L', '180 W', '150 W', 'Yok', '300 x 150 x 100 mm', '325 x 180 x 220 mm', '4.6 kg', ['wf-ud4-ultrasonik-banyo-54', 'wf-ud4-ultrasonik-banyo-55']],
    ['WF-UD6', '6.5 L', '180 W', '150 W', 'Var', '300 x 150 x 150 mm', '325 x 180 x 280 mm', '5.4 kg', ['wf-ud6-ultrasonik-banyo-64', 'wf-ud6-ultrasonik-banyo-65']],
    ['WF-UD10', '10 L', '240 W', '200 W', 'Var', '300 x 240 x 150 mm', '325 x 265 x 280 mm', '7.3 kg', ['wf-ud10-ultrasonik-banyo']],
    ['WF-UD15', '15 L', '360 W', '300 W', 'Var', '300 x 300 x 150 mm', '360 x 325 x 285 mm', '9.1 kg', ['wf-ud15-ultrasonik-banyo-11', 'wf-ud15-ultrasonik-banyo-12']],
    ['WF-UD22', '22 L', '480 W', '400 W', 'Var', '500 x 300 x 150 mm', '530 x 325 x 285 mm', '12.6 kg', ['wf-ud22-ultrasonik-banyo-31', 'wf-ud22-ultrasonik-banyo-32']],
    ['WF-UD30', '30 L', '600 W', '500 W', 'Var', '500 x 300 x 200 mm', '530 x 325 x 325 mm', '14.4 kg', ['wf-ud30-ultrasonik-banyo-44', 'wf-ud30-ultrasonik-banyo-45']],
] as [$model, $capacity, $ultrasonicPower, $heatingPower, $drain, $innerDimensions, $outerDimensions, $weight, $modelImageSlugs]) {
    $modelSlug = strtolower($model);
    $products[] = [
        'category_slug' => 'ultrasonik-banyo',
        'name' => "Weightlab {$model} Ultrasonik Banyo",
        'slug' => "weightlab-{$modelSlug}-ultrasonik-banyo",
        'image_slugs' => ["weightlab-{$modelSlug}-ultrasonik-banyo", "{$modelSlug}-ultrasonik-banyo", ...$modelImageSlugs],
        'model' => $model,
        'old_url' => 'https://www.weightlabinstruments.com/urun/wf-ud-serisi-ultrasonik-banyolar/',
        'summary' => "Weightlab {$model} ultrasonik banyo; 40 kHz frekans, {$capacity} kapasite, 20-80 °C sıcaklık aralığı ve 1-99 dakika zamanlayıcıyla temizleme ve numune hazırlama uygulamalarında kullanılır.",
        'body' => "Weightlab {$model}, dijital zamanlayıcı, ısıtıcı ve gaz giderme fonksiyonuyla ultrasonik temizleme işlemlerinde kullanılır. Paslanmaz çelik gövde, dokunmatik kontrol ekranı ve standart kapak/sepet aksesuarlarıyla pratik laboratuvar kullanımı sağlar.",
        'features' => $ultrasonicFeatures,
        'specs' => ['Frekans' => '40 kHz', 'Sıcaklık aralığı' => '20-80 °C', 'Kapasite' => $capacity, 'Ultrasonik güç' => $ultrasonicPower, 'Isıtma gücü' => $heatingPower, 'Cihaz tipi' => 'Dijital', 'Zamanlayıcı süresi' => '1-99 dakika', 'Tahliye musluğu' => $drain, 'İç boyutu' => $innerDimensions, 'Dış boyutu' => $outerDimensions, 'Ağırlık' => $weight, 'Standart aksesuarlar' => 'Kapak ve sepet'],
        'documents' => $docs['wfud'],
        'image_alt' => "Weightlab {$model} ultrasonik banyo ürün görseli",
    ];
}

$imageFor = function (array|string $slugs) use ($root): ?string {
    foreach ((array) $slugs as $slug) {
        foreach (['', '-1', '-2'] as $suffix) {
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

$stmt = $db->prepare('select id from product_brands where slug = :slug');
$stmt->execute(['slug' => 'weightlab']);
$brandId = $stmt->fetchColumn();

if (! $brandId) {
    $stmt = $db->prepare('insert into product_brands (name, slug, summary, logo, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :logo, :aliases, 1, :sort_order, :created_at, :updated_at)');
    $stmt->execute([
        'name' => 'Weightlab',
        'slug' => 'weightlab',
        'summary' => 'Tartım ve laboratuvar cihazı çözümleri.',
        'logo' => 'images/brands/weightlab.png',
        'aliases' => $json(['WEİGHTLAB', 'Weightlab Instruments']),
        'sort_order' => 20,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $brandId = $db->lastInsertId();
}

$categoryIds = [];
$selectCategory = $db->prepare('select id from product_categories where slug = :slug');
$insertCategory = $db->prepare('insert into product_categories (name, slug, summary, aliases, is_active, sort_order, created_at, updated_at) values (:name, :slug, :summary, :aliases, 1, :sort_order, :created_at, :updated_at)');
$updateCategory = $db->prepare('update product_categories set name = :name, summary = :summary, aliases = :aliases, is_active = 1, updated_at = :updated_at where id = :id');
$selectCategoryBrand = $db->prepare('select count(*) from product_category_brand where product_category_id = :category_id and product_brand_id = :brand_id');
$insertCategoryBrand = $db->prepare('insert into product_category_brand (product_category_id, product_brand_id, created_at, updated_at) values (:category_id, :brand_id, :created_at, :updated_at)');

foreach ($categories as $slug => $category) {
    $selectCategory->execute(['slug' => $slug]);
    $categoryId = $selectCategory->fetchColumn();

    if (! $categoryId) {
        $sortOrder = (int) $db->query('select coalesce(max(sort_order), 0) + 10 from product_categories')->fetchColumn();
        $insertCategory->execute([
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
        $updateCategory->execute([
            'name' => $category['name'],
            'summary' => $category['summary'],
            'aliases' => $json($category['aliases']),
            'updated_at' => $now,
            'id' => $categoryId,
        ]);
    }

    $selectCategoryBrand->execute(['category_id' => $categoryId, 'brand_id' => $brandId]);

    if ((int) $selectCategoryBrand->fetchColumn() === 0) {
        $insertCategoryBrand->execute([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $categoryIds[$slug] = $categoryId;
}

$stmt = $db->prepare('select id from services where slug = :slug');
$stmt->execute(['slug' => 'hacim-kalibrasyonu']);
$volumeServiceId = $stmt->fetchColumn();

if ($volumeServiceId && isset($categoryIds['pipetler'])) {
    $stmt = $db->prepare('select count(*) from product_category_service where product_category_id = :category_id and service_id = :service_id');
    $stmt->execute(['category_id' => $categoryIds['pipetler'], 'service_id' => $volumeServiceId]);

    if ((int) $stmt->fetchColumn() === 0) {
        $stmt = $db->prepare('insert into product_category_service (product_category_id, service_id, created_at, updated_at) values (:category_id, :service_id, :created_at, :updated_at)');
        $stmt->execute([
            'category_id' => $categoryIds['pipetler'],
            'service_id' => $volumeServiceId,
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

$sortCounters = [];

foreach ($products as $product) {
    $categorySlug = $product['category_slug'];
    $categoryId = $categoryIds[$categorySlug];
    $sortCounters[$categorySlug] = ($sortCounters[$categorySlug] ?? 0) + 10;

    $selectProduct->execute(['category_id' => $categoryId, 'slug' => $product['slug']]);
    $productId = $selectProduct->fetchColumn();

    $metadata = [
        'Marka' => 'Weightlab',
        'Kategori' => $categories[$categorySlug]['name'],
        'Model' => $product['model'],
        'SKU' => 'Yayın öncesi netleştirilecek',
    ];

    foreach (array_slice($product['specs'], 0, 5, true) as $key => $value) {
        $metadata[$key] = $value;
    }

    $payload = [
        'brand_id' => $brandId,
        'name' => $product['name'],
        'model' => $product['model'],
        'sku' => null,
        'old_url' => $product['old_url'],
        'summary' => $product['summary'],
        'body' => $product['body'],
        'image' => $imageFor($product['image_slugs'] ?? $product['slug']),
        'image_alt' => $product['image_alt'],
        'features' => $json($product['features']),
        'metadata' => $json($metadata),
        'specs' => $json($product['specs']),
        'sort_order' => $sortCounters[$categorySlug],
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

echo 'brand_id=' . $brandId . PHP_EOL;
foreach ($categories as $slug => $category) {
    echo 'category=' . $slug . ':' . $categoryIds[$slug] . PHP_EOL;
}

foreach ($products as $product) {
    echo 'product_slug=' . $product['slug'] . PHP_EOL;
    echo 'image=' . ($imageFor($product['image_slugs'] ?? $product['slug']) ?: 'missing') . PHP_EOL;
}

echo 'products=' . $db->query('select count(*) from products')->fetchColumn() . PHP_EOL;
