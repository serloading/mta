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
    'titratorler' => [
        'name' => 'Titratörler',
        'summary' => 'Laboratuvar titrasyon analizleri için otomatik titratör, Karl Fischer ve büret çözümleri.',
        'aliases' => ['Titratörler', 'Titratorler', 'Titrasyon Cihazları'],
    ],
    'karl-fischer-titratorler' => [
        'name' => 'Karl Fischer Titratörler',
        'summary' => 'Karl Fischer yöntemiyle su tayini için titratör ve modül çözümleri.',
        'aliases' => ['Karl Fischer Titratör', 'Karl Fischer Titratorler', 'KF Titratörler', 'KF Titratorler'],
    ],
    'kulometrik-karl-fischer-titratorler' => [
        'name' => 'Kulometrik Karl Fischer Titratörler',
        'summary' => 'Düşük su miktarı ve iz nem analizleri için kulometrik Karl Fischer titratör sistemleri.',
        'aliases' => ['Kulometrik Karl Fischer', 'Kulometrik KF Titratör', 'KF Trace', 'TitroLine 7500 KF trace'],
    ],
    'volumetrik-karl-fischer-titratorler' => [
        'name' => 'Volümetrik Karl Fischer Titratörler',
        'summary' => 'Numunelerdeki su miktarının volümetrik Karl Fischer yöntemiyle belirlenmesi için titratörler.',
        'aliases' => ['Volümetrik Karl Fischer', 'Volumetrik Karl Fischer', 'Volümetrik KF Titratör', 'TitroLine 7500 KF'],
    ],
    'potansiyometrik-titratorler' => [
        'name' => 'Potansiyometrik Titratörler',
        'summary' => 'Laboratuvar titrasyon analizleri için potansiyometrik titratörler.',
        'aliases' => ['Potensiyometre Titratör', 'Potansiyometre Titratör', 'Potansiyomerik Titratörler', 'Potansyometrik Titratörler ve Kral Fischer'],
    ],
    'piston-buretler' => [
        'name' => 'Piston Büretler',
        'summary' => 'Dozajlama, solüsyon hazırlama ve manuel titrasyon işlemleri için piston büret sistemleri.',
        'aliases' => ['Piston Büret', 'Piston Buret', 'Büretler', 'Buretler', 'TITRONIC'],
    ],
];

$products = [
    [
        'category_slug' => 'piston-buretler',
        'name' => 'TITRONIC 300 Piston Büret',
        'slug' => 'titronic-300-piston-buret',
        'image_slugs' => ['titronic-300-piston-buret'],
        'model' => 'TITRONIC 300',
        'summary' => 'TITRONIC 300; dozajlama, solüsyon hazırlama ve manuel titrasyon uygulamaları için kullanılan piston bürettir.',
        'body' => 'TITRONIC 300 piston büret; laboratuvarlarda dozajlama, solüsyon hazırlama ve manuel titrasyon işlemlerinde kullanılmak üzere tasarlanmıştır. SI Analitik titrasyon ürün grubu içinde pratik ve kontrollü sıvı dozajlama ihtiyaçları için konumlandırılır.',
        'features' => [
            'Dozajlama işlemleri için kullanım',
            'Solüsyon hazırlama uygulamalarına uygunluk',
            'Manuel titrasyonlarda kontrollü dozajlama',
            'Masa tipi laboratuvar kullanımı',
        ],
        'specs' => [
            'Ürün tipi' => 'Piston büret',
            'Model' => 'TITRONIC 300',
            'Kullanım alanı' => 'Dozajlama, solüsyon hazırlama ve manuel titrasyon',
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Piston Büretler',
            'Model' => 'TITRONIC 300',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Piston büret',
        ],
        'documents' => [],
        'videos' => [],
        'image_alt' => 'TITRONIC 300 piston büret ürün görseli',
    ],
    [
        'category_slug' => 'piston-buretler',
        'name' => 'TITRONIC 500 Piston Büret',
        'slug' => 'titronic-500-piston-buret',
        'image_slugs' => ['titronic-500-piston-buret'],
        'model' => 'TITRONIC 500',
        'summary' => 'TITRONIC 500; değiştirilebilir büret üniteli, dozajlama, solüsyon hazırlama ve manuel titrasyonlar için piston bürettir.',
        'body' => 'TITRONIC 500 piston büret; dozajlama, solüsyon hazırlama ve manuel titrasyon uygulamalarında değiştirilebilir akıllı dozajlama üniteleriyle kullanılır. Bilgisayar, analitik terazi ve çoklu büret bağlantısı gerektiren laboratuvar iş akışlarına uygundur.',
        'features' => [
            'Dozajlama, solüsyon hazırlama ve manuel titrasyonlar için kullanım',
            '5, 10, 20 ve 50 ml hacminde değiştirilebilir akıllı dozajlama üniteleri',
            'Bilgisayar ve analitik terazi bağlantısı',
            'RS232 veya USB arabirimi ile uzaktan kontrol',
            '16 bürete kadar bağlantı olanağı',
            'Türkçe menü',
        ],
        'specs' => [
            'Ürün tipi' => 'Piston büret',
            'Model' => 'TITRONIC 500',
            'Dozajlama ünitesi hacimleri' => '5, 10, 20 ve 50 ml',
            'Bağlantı' => 'Bilgisayar ve analitik terazi bağlantısı',
            'Arabirim' => 'RS232 veya USB',
            'Çoklu bağlantı' => '16 bürete kadar',
            'Menü' => 'Türkçe',
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Piston Büretler',
            'Model' => 'TITRONIC 500',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Değiştirilebilir büret üniteli piston büret',
        ],
        'documents' => [],
        'videos' => [],
        'image_alt' => 'TITRONIC 500 piston büret ürün görseli',
    ],
    [
        'category_slug' => 'potansiyometrik-titratorler',
        'name' => 'TitroLine 5000 Potansiyometrik Titratör',
        'slug' => 'titroline-5000-potansiyometrik-titrator',
        'image_slugs' => ['titroline-5000-potansiyometrik-titrator'],
        'model' => 'TitroLine 5000',
        'summary' => 'TitroLine 5000, gıda ve çevre analizleri için ideal potansiyometrik titratördür.',
        'body' => 'TitroLine 5000 potansiyometrik titratör; gıda, çevre ve yapı kimyasalları analizlerinde kullanılmak üzere hazırlanmış titrasyon sistemidir. Tuz içeriği, pH değeri, toplam asitlik, askorbik asit, protein tayini, iyot/peroksit sayısı, alkalinite, FOS/TAC, toplam Kjeldahl azotu, permanganat indeksi, KOİ ve klorür gibi analizlerde kullanıma uygundur.',
        'features' => [
            'Gıda ve çevre analizleri için ideal titratör',
            'Tuz içeriği, pH değeri ve toplam asitlik analizleri',
            'Askorbik asit, protein tayini, iyot ve peroksit sayısı uygulamaları',
            'Alkalinite, FOS/TAC, KOİ ve atık suda klorür analizleri',
            'Yapı kimyasallarında suda çözünebilir klorür muhtevası tayini',
            '20 ml ve 50 ml dozajlama ünitesi seçenekleri',
            'Asitlik, tuzluluk/klorür ve redoks set seçenekleri',
        ],
        'specs' => [
            'Ürün tipi' => 'Potansiyometrik titratör',
            'Model' => 'TitroLine 5000',
            'Gıda analizleri' => 'Tuz içeriği, pH değeri, toplam asitlik, askorbik asit, protein tayini, iyot ve peroksit sayısı',
            'Çevre analizleri' => 'pH değeri, alkalinite, FOS/TAC, toplam Kjeldahl azotu, permanganat indeksi, KOİ, atık suda klorür',
            'Yapı kimyasalları' => 'Agregalarda ve beton katkılarında suda çözünebilir klorür muhtevası tayini',
            'TL 5000/20' => 'Kat.No. 285225740 - temel ünite, 20 ml dozajlama ünitesi, hortum, titrasyon ucu, 100-240 V güç kaynağı',
            'TL 5000/50' => 'Kat.No. 285225750 - temel ünite, 50 ml dozajlama ünitesi, hortum, titrasyon ucu, 100-240 V güç kaynağı',
            'TL 5000/20 M1' => 'Kat.No. 285225760 - modül 1, 20 ml dozajlama ünitesi, manyetik karıştırıcı, el kontrol ünitesi, stand çubuğu',
            'TL 5000/50 M1' => 'Kat.No. 285225770 - modül 1, 50 ml dozajlama ünitesi, manyetik karıştırıcı, el kontrol ünitesi, stand çubuğu',
            'TL 5000/20 M2' => 'Kat.No. 285225780 - pH titrasyonları için pH elektrodu ve tampon çözelti setli 20 ml modül',
            'TL 5000/50 M2' => 'Kat.No. 285225790 - pH titrasyonları için pH elektrodu ve tampon çözelti setli 50 ml modül',
            'TL 5000/20 M3' => 'Kat.No. 285225850 - halojen titrasyonları için gümüş elektrodu ve elektrod kablolu 20 ml modül',
            'TL 5000/20 Asitlik Seti' => 'Kat.No. 285227750 - asit-baz titrasyonları için 20 ml set',
            'TL 5000/50 Asitlik Seti' => 'Kat.No. 285227780 - asit-baz titrasyonları için 50 ml set',
            'TL 5000/20 Tuzluluk Seti' => 'Kat.No. 285227760 - tuzluluk/klorür titrasyonları için 20 ml set',
            'TL 5000/20 Redoks Seti' => 'Kat.No. 285227770 - redoks titrasyonları için platin elektrodlu 20 ml set',
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Potansiyometrik Titratörler',
            'Model' => 'TitroLine 5000',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Potansiyometrik titratör',
        ],
        'documents' => [],
        'videos' => [],
        'image_alt' => 'TitroLine 5000 potansiyometrik titratör ürün görseli',
    ],
    [
        'category_slug' => 'potansiyometrik-titratorler',
        'name' => 'TitroLine 7000 Potansiyometrik Titratör',
        'slug' => 'titroline-7000-potansiyometrik-titrator',
        'image_slugs' => ['titroline-7000-potansiyometrik-titrator'],
        'model' => 'TitroLine 7000',
        'summary' => 'TitroLine 7000; değiştirilebilir akıllı dozajlama üniteleri, çoklu arabirimleri ve 50 metoda kadar kullanıcı tanımlı analiz desteği sunan otomatik titratördür.',
        'body' => 'TitroLine 7000 potansiyometrik titratör; su ve atıksu, gıda, endüstriyel ürün ve diğer titrasyon analizleri için geliştirilmiş otomatik titrasyon sistemidir. pH stat titrasyonu, susuz çözelti titrasyonları, TAN/TBN analizleri, sertlik tayinleri, klorür, asitlik, redoks ve kompleksometrik uygulamalarda kullanılabilir.',
        'features' => [
            '5, 10, 20 ve 50 ml hacminde değiştirilebilir akıllı dozajlama üniteleri',
            '3 adet USB ve 2 adet RS232 arabirimi',
            'Yazıcı, barkod okuyucu, USB bellek, terazi, bilgisayar, piston büret ve numune değiştirici bağlantısı',
            'Kullanıcı tarafından oluşturulabilen 50 farklı metod',
            'SCHOTT Instruments ID elektrodlarını kablosuz tanıma',
            'Enzim aktivitesi ve toprak numuneleri için pH stat titrasyonu',
            'TAN ve TBN için susuz çözelti titrasyonları',
            'Kalsiyum, magnezyum ve toplam sertlik tayini',
        ],
        'specs' => [
            'Ürün tipi' => 'Potansiyometrik titratör',
            'Model' => 'TitroLine 7000',
            'Dozajlama üniteleri' => '5, 10, 20 ve 50 ml',
            'Arabirim' => '3 USB, 2 RS232',
            'Metod hafızası' => 'Kullanıcı tarafından oluşturulabilen 50 metod',
            'Elektrod tanıma' => 'SCHOTT Instruments ID elektrodlarını kablosuz tanıma',
            'Su ve atıksu analizleri' => 'Alkalinite, KOİ, permanganat indeksi, FOS/TAC, azot/amonyak, klorür, klor, sertlik',
            'Gıda analizleri' => 'Toplam asitlik, klorür/tuz, SO2, uçucu asitler, askorbik asit, kalsiyum, iyot sayısı, peroksit sayısı, yağlarda asitlik',
            'Endüstriyel analizler' => 'Kuvvetli asit/baz, fosforik asit, hidroksil sayısı, izosiyanat sayısı, epoksi sayısı, TAN, TBN',
            'Diğer analizler' => 'Sürfaktanlar, metaller, perklorik asit ile susuz titrasyon, agregalarda klorür',
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Potansiyometrik Titratörler',
            'Model' => 'TitroLine 7000',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Otomatik potansiyometrik titratör',
        ],
        'documents' => [],
        'videos' => [
            ['title' => 'TitroLine 7000 tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=8_Z7m3s802U', 'youtube_id' => '8_Z7m3s802U'],
        ],
        'image_alt' => 'TitroLine 7000 potansiyometrik titratör ürün görseli',
    ],
    [
        'category_slug' => 'potansiyometrik-titratorler',
        'name' => 'TitroLine 7750 Potansiyometrik ve Karl Fischer Titratör',
        'slug' => 'titroline-7750-potansiyometrik-karl-fischer-titrator',
        'image_slugs' => ['titroline-7750-potansiyometrik-karl-fischer-titrator'],
        'model' => 'TitroLine 7750',
        'summary' => 'TitroLine 7750, hem potansiyometrik titrasyon hem de volümetrik Karl Fischer titrasyonu için kullanılabilen titratördür.',
        'body' => 'TitroLine 7750; potansiyometrik titrasyon ve volümetrik Karl Fischer titrasyonu uygulamalarını tek cihazda destekler. Akıllı değiştirilebilir dozajlama üniteleri ve çoklu bağlantı arabirimleriyle laboratuvar otomasyonu gerektiren analiz düzeneklerinde kullanılabilir.',
        'features' => [
            'Potansiyometrik titrasyon uygulamalarına uygunluk',
            'Volümetrik Karl Fischer titrasyonu için kullanım',
            '5, 10 veya 20 ml hacimli akıllı değiştirilebilir dozajlama üniteleri',
            '3 adet USB ve 2 adet RS232 arabirimi',
            'Yazıcı, barkod okuyucu, USB bellek, terazi, bilgisayar, piston büret ve numune değiştirici bağlantısı',
        ],
        'specs' => [
            'Ürün tipi' => 'Potansiyometrik ve Karl Fischer titratör',
            'Model' => 'TitroLine 7750',
            'Titrasyon türleri' => 'Potansiyometrik titrasyon ve volümetrik Karl Fischer titrasyonu',
            'Dozajlama üniteleri' => '5, 10 veya 20 ml akıllı değiştirilebilir üniteler',
            'Arabirim' => '3 USB, 2 RS232',
            'Bağlanabilir ekipmanlar' => 'Yazıcı, barkod okuyucu, USB bellek, terazi, bilgisayar, piston büret, numune değiştirici',
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Potansiyometrik Titratörler',
            'Model' => 'TitroLine 7750',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Potansiyometrik ve Karl Fischer titratör',
        ],
        'documents' => [],
        'videos' => [],
        'image_alt' => 'TitroLine 7750 potansiyometrik ve Karl Fischer titratör ürün görseli',
    ],
    [
        'category_slug' => 'potansiyometrik-titratorler',
        'name' => 'TitroLine 7800 Potansiyometrik ve Karl Fischer Titratör',
        'slug' => 'titroline-7800-potansiyometrik-karl-fischer-titrator',
        'image_slugs' => ['titroline-7800-potansiyometrik-karl-fischer-titrator'],
        'model' => 'TitroLine 7800',
        'summary' => 'TitroLine 7800; analog ve dijital elektrod girişleriyle pH, iletkenlik ölçümleri ve Karl Fischer yöntemiyle su tayini yapabilen titratördür.',
        'body' => 'TitroLine 7800; 1 analog ve 1 dijital elektrod girişiyle pH, iletkenlik ölçümleri ve Karl Fischer yöntemi ile su tayini yapabilir. Sulu ve susuz çözeltilerde pH/mV titrasyonları, redoks, halojen, hidrojen sülfür/merkaptan, brom sayısı, pH stat ve volümetrik Karl Fischer titrasyonlarında kullanılabilir.',
        'features' => [
            'Türkçe menü',
            'Dijital IDS ve analog elektrod bağlantısı',
            'Eş zamanlı pH ve iletkenlik ölçümü',
            'Sulu çözeltilerde pH/mV titrasyonları',
            'Susuz çözeltilerde pH/mV titrasyonları',
            'Redoks ve halojen titrasyonları',
            'Hidrojen sülfür ve merkaptan titrasyonları',
            'pH stat titrasyonları',
            'Volümetrik Karl Fischer titrasyonu',
            'Ethernet girişi',
            'USB ile ölçüm sonuçlarının taşınabilir belleğe kaydı',
        ],
        'specs' => [
            'Ürün tipi' => 'Potansiyometrik ve Karl Fischer titratör',
            'Model' => 'TitroLine 7800',
            'Elektrod girişleri' => '1 analog, 1 dijital',
            'Ölçüm' => 'pH ve iletkenlik',
            'Karl Fischer aralığı' => '10 ppm - %100',
            'Sulu titrasyonlar' => 'Alkalinite, hidroklorik asit, sitrik asit, Kjeldahl',
            'Susuz titrasyonlar' => 'TAN, TBN, FFA, perklorik asit titrasyonları',
            'Redoks titrasyonları' => 'İyodometri, permanganometri, KOİ',
            'Halojen titrasyonları' => 'Tuz ve klorür tayini',
            'Bağlantı' => 'Ethernet ve USB',
            'Menü' => 'Türkçe',
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Potansiyometrik Titratörler',
            'Model' => 'TitroLine 7800',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Potansiyometrik ve Karl Fischer titratör',
        ],
        'documents' => [],
        'videos' => [
            ['title' => 'TitroLine 7800 tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=G5bh9ez93EA', 'youtube_id' => 'G5bh9ez93EA'],
        ],
        'image_alt' => 'TitroLine 7800 potansiyometrik ve Karl Fischer titratör ürün görseli',
    ],
];

$products[] = [
    'category_slug' => 'volumetrik-karl-fischer-titratorler',
    'name' => 'TitroLine 7500 KF Volümetrik Karl Fischer Titratör',
    'slug' => 'titroline-7500-kf-volumetrik-karl-fischer-titrator',
    'image_slugs' => ['titroline-7500-kf-volumetrik-karl-fischer-titrator'],
    'model' => 'TitroLine 7500 KF',
    'summary' => 'TitroLine 7500 KF, numunelerdeki su miktarının volümetrik Karl Fischer yöntemiyle belirlenmesi için kullanılan titratördür.',
    'body' => 'TitroLine 7500 KF volümetrik Karl Fischer titratör, numunelerdeki su miktarının 100 ppm ile %100 aralığında volümetrik yöntemle belirlenmesi için kullanılır. Volümetrik Karl Fischer ve brom sayısı uygulamalarını destekler; eşzamanlı grafik gösterimi, değiştirilebilir akıllı büret ünitesi, RS-232 terazi bağlantısı ve USB/RS-232 arabirimleriyle laboratuvar titrasyon süreçlerine uyum sağlar.',
    'features' => [
        'Numunelerdeki su miktarının volümetrik Karl Fischer yöntemiyle belirlenmesi',
        '100 ppm - %100 ölçüm aralığı',
        '<0.15 % dozajlama hassasiyeti',
        '50 metot kapasitesi',
        'Volümetrik Karl Fischer ve brom sayısı uygulamaları',
        'Eşzamanlı grafik gösterimi',
        'Değiştirilebilir akıllı büret ünitesi',
        'RS-232 terazi bağlantısı',
        '2 x USB-A, 1 x USB-B ve RS-232 arabirimleri',
        'Entegre pompalı TM 235 KF manyetik karıştırıcı',
        'Opsiyonel TitriSoft 3.0 bilgisayar yazılımı',
    ],
    'specs' => [
        'Ürün tipi' => 'Volümetrik Karl Fischer titratör',
        'Model' => 'TitroLine 7500 KF',
        'Ölçüm aralığı' => '100 ppm - %100',
        'Dozajlama hassasiyeti' => '<0.15 %',
        'Metot sayısı' => '50',
        'Aplikasyonlar' => 'Volümetrik Karl Fischer, brom sayısı',
        'Eşzamanlı grafik' => 'Var',
        'Yazıcı' => 'HP PLC, Seiko DPU S 445, PDF',
        'Terazi bağlantısı' => 'RS-232',
        'Değiştirilebilir akıllı büret ünitesi' => 'Var',
        'Arabirimler' => '2 x USB-A, 1 x USB-B, RS-232',
        'TM 235 KF manyetik karıştırıcı' => 'Entegre pompa ile var',
        'Bilgisayar yazılımı' => 'TitriSoft 3.0 (opsiyonel)',
        'Görsel durumu' => 'Placeholder',
    ],
    'metadata' => [
        'Marka' => 'SI Analitik',
        'Kategori' => 'Volümetrik Karl Fischer Titratörler',
        'Model' => 'TitroLine 7500 KF',
        'SKU' => 'Yayın öncesi netleştirilecek',
        'Ürün tipi' => 'Volümetrik Karl Fischer titratör',
    ],
    'documents' => [],
    'videos' => [],
    'image_alt' => 'TitroLine 7500 KF volümetrik Karl Fischer titratör ürün görseli',
];

$kfTraceModules = [
    [
        'module' => 'Modül 1',
        'slug' => 'titroline-7500-kf-trace-modul-1-kulometrik-karl-fischer-titrator',
        'description' => 'Manyetik karıştırıcı, diyaframsız jeneratör elektrod, titrasyon kabı ve platin elektrod ile sunulan kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Manyetik karıştırıcı', 'Diyaframsız jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod'],
    ],
    [
        'module' => 'Modül 2',
        'slug' => 'titroline-7500-kf-trace-modul-2-kulometrik-karl-fischer-titrator',
        'description' => 'Dahili pompalı manyetik karıştırıcı, diyaframsız jeneratör elektrod, titrasyon kabı ve platin elektrod ile sunulan kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Dahili pompalı manyetik karıştırıcı', 'Diyaframsız jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod'],
    ],
    [
        'module' => 'Modül 3',
        'slug' => 'titroline-7500-kf-trace-modul-3-kulometrik-karl-fischer-titrator',
        'description' => 'Manyetik karıştırıcı, diyaframlı jeneratör elektrod, titrasyon kabı ve platin elektrod ile sunulan kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Manyetik karıştırıcı', 'Diyaframlı jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod'],
    ],
    [
        'module' => 'Modül 4',
        'slug' => 'titroline-7500-kf-trace-modul-4-kulometrik-karl-fischer-titrator',
        'description' => 'Dahili pompalı manyetik karıştırıcı, diyaframlı jeneratör elektrod, titrasyon kabı ve platin elektrod ile sunulan kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Dahili pompalı manyetik karıştırıcı', 'Diyaframlı jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod'],
    ],
    [
        'module' => 'Modül 5',
        'slug' => 'titroline-7500-kf-trace-modul-5-kulometrik-karl-fischer-titrator',
        'description' => 'Manyetik karıştırıcı, diyaframsız jeneratör elektrod, titrasyon kabı ve platin elektrod ile sunulan, fırına bağlanabilen kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Manyetik karıştırıcı', 'Diyaframsız jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod', 'Fırına bağlanabilme'],
    ],
    [
        'module' => 'Modül 6',
        'slug' => 'titroline-7500-kf-trace-modul-6-kulometrik-karl-fischer-titrator',
        'description' => 'Manyetik karıştırıcı, diyaframsız jeneratör elektrod, titrasyon kabı ve platin elektrod ile sunulan, fırına ve otomatik numune değiştiriciye bağlanabilen kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Manyetik karıştırıcı', 'Diyaframsız jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod', 'Fırına bağlanabilme', 'Otomatik numune değiştiriciye bağlanabilme'],
    ],
    [
        'module' => 'Modül 6, TitriSoft Pharma',
        'slug' => 'titroline-7500-kf-trace-modul-6-titrisoft-pharma-kulometrik-karl-fischer-titrator',
        'description' => 'Manyetik karıştırıcı, diyaframsız jeneratör elektrod, titrasyon kabı, platin elektrod ve TitriSoft 3.5 P yazılımı ile sunulan; fırına ve otomatik numune değiştiriciye bağlanabilen, ilaç sanayi için uygun kulometrik Karl Fischer titratör modülüdür.',
        'features' => ['Manyetik karıştırıcı', 'Diyaframsız jeneratör elektrod', 'Titrasyon kabı', 'Platin elektrod', 'TitriSoft 3.5 P yazılımı', 'Fırına bağlanabilme', 'Otomatik numune değiştiriciye bağlanabilme', 'İlaç sanayi için uygunluk'],
        'video' => null,
    ],
];

foreach ($kfTraceModules as $module) {
    $products[] = [
        'category_slug' => 'kulometrik-karl-fischer-titratorler',
        'name' => 'TitroLine 7500 KF trace, ' . $module['module'],
        'slug' => $module['slug'],
        'image_slugs' => [$module['slug']],
        'model' => 'TitroLine 7500 KF trace ' . $module['module'],
        'summary' => 'TitroLine 7500 KF trace ' . $module['module'] . ', iz nem ve düşük su miktarı analizleri için kulometrik Karl Fischer titratör modülüdür.',
        'body' => $module['description'],
        'features' => array_merge([
            'Kulometrik Karl Fischer titrasyonu için kullanım',
            'İz nem ve düşük su miktarı analizleri için uygunluk',
        ], $module['features']),
        'specs' => [
            'Ürün tipi' => 'Kulometrik Karl Fischer titratör',
            'Model' => 'TitroLine 7500 KF trace',
            'Modül' => $module['module'],
            'Set içeriği' => implode(', ', $module['features']),
            'Görsel durumu' => 'Placeholder',
        ],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Kulometrik Karl Fischer Titratörler',
            'Model' => 'TitroLine 7500 KF trace ' . $module['module'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => 'Kulometrik Karl Fischer titratör',
        ],
        'documents' => [],
        'videos' => ($module['video'] ?? true) ? [
            ['title' => 'TitroLine 7500 KF trace tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=vdfx95qFmDA', 'youtube_id' => 'vdfx95qFmDA'],
        ] : [],
        'image_alt' => 'TitroLine 7500 KF trace ' . $module['module'] . ' ürün görseli',
    ];
}

$pendingContentProducts = [
    [
        'name' => 'TO 7280 Kulometrik Karl Fischer Titrasyonu için Fırın',
        'slug' => 'to-7280-kulometrik-karl-fischer-titrasyonu-icin-firin',
        'model' => 'TO 7280',
        'summary' => 'TO 7280 headspace fırın; 1 µg-100 mg mutlak ölçüm aralığı, 0.1 µg çözünürlük ve 35-280 °C izotermal sıcaklık aralığıyla kulometrik Karl Fischer titrasyonunda kullanılır.',
        'body' => 'TO 7280 headspace fırın, kulometrik Karl Fischer titrasyonu için headspace vial numune dozajlama sistemiyle kullanılır. 1 µg ile 100 mg mutlak ölçüm aralığı, 0.1 µg çözünürlük ve 35-280 °C izotermal sıcaklık aralığı sunar. 115-230 V, 50/60 Hz güç beslemesiyle çalışır ve 300 x 450 x 240 mm ölçülerinde kompakt bir fırın çözümüdür.',
        'features' => [
            'Kulometrik Karl Fischer titrasyonu için headspace fırın',
            'Headspace vial numune dozajlama sistemi',
            '1 µg-100 mg mutlak ölçüm aralığı',
            '0.1 µg çözünürlük',
            '35-280 °C izotermal sıcaklık aralığı',
            '115-230 V, 50/60 Hz güç beslemesi',
        ],
        'specs' => [
            'Ürün tipi' => 'Kulometrik Karl Fischer titrasyonu için fırın',
            'Model' => 'TO 7280',
            'Numune dozajlama' => 'Headspace vialleri (5 ml...)',
            'Ölçüm aralığı' => '1 µg-100 mg mutlak',
            'Çözünürlük' => '0.1 µg',
            'Tekrarlanabilirlik' => '10-1000 µg için ±3 µg; >1 mg için %0.33',
            'Sıcaklık aralığı' => '35-280 °C (izotermal)',
            'Sıcaklık çözünürlüğü' => '1 K',
            'Güç beslemesi' => '115-230 V, 50/60 Hz',
            'Güç girişi' => '250 W',
            'Ölçüler' => '300 x 450 x 240 mm (G x Y x D)',
            'Ağırlık' => '7 kg',
            'Ortam koşulları' => 'Çalışma ve depolama için ortam sıcaklığı +10...+40 °C',
            'İçerik durumu' => 'Görselden Türkçeye çevrildi',
            'Görsel durumu' => 'Placeholder',
        ],
        'content_status' => 'Görselden Türkçeye çevrildi',
        'video' => ['title' => 'TO 7280 tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=skRjUMEprrA', 'youtube_id' => 'skRjUMEprrA'],
    ],
    [
        'name' => 'TW 7650 Otomatik Numune Değiştirici',
        'slug' => 'tw-7650-otomatik-numune-degistirici',
        'model' => 'TW 7650',
        'summary' => 'TW 7650 otomatik numune değiştirici; 49 numune + 1 boş vial pozisyonu ve TO 7280 üzerinden besleme ile kulometrik Karl Fischer iş akışlarını destekler.',
        'body' => 'TW 7650 otomatik numune değiştirici, kulometrik Karl Fischer titrasyonu iş akışlarında TO 7280 ile birlikte kullanılmak üzere tasarlanmıştır. 49 numune ve 1 boş vial pozisyonu sunar. Güç beslemesi ve güç girişi TO 7280 üzerinden sağlanır; TO 7280 dahil ölçüleri 420 x 450 x 460 mm’dir.',
        'features' => [
            'Otomatik numune değiştirici',
            '49 numune + 1 boş vial pozisyonu',
            'TO 7280 üzerinden güç beslemesi',
            'TO 7280 ile entegre kullanım',
            'Çalışma ve depolama için +10...+40 °C ortam sıcaklığı',
        ],
        'specs' => [
            'Ürün tipi' => 'Otomatik numune değiştirici',
            'Model' => 'TW 7650',
            'Pozisyon sayısı' => '49 numune + 1 boş vial',
            'Güç beslemesi' => 'TO 7280 üzerinden',
            'Güç girişi' => 'TO 7280 üzerinden',
            'Ölçüler' => '420 x 450 x 460 mm (G x Y x D), TO 7280 dahil',
            'Ağırlık' => 'TO 7280 olmadan 10 kg; TO 7280 ile 17 kg',
            'Ortam koşulları' => 'Çalışma ve depolama için ortam sıcaklığı +10...+40 °C',
            'İçerik durumu' => 'Görselden Türkçeye çevrildi',
            'Görsel durumu' => 'Placeholder',
        ],
        'content_status' => 'Görselden Türkçeye çevrildi',
        'video' => null,
    ],
    [
        'name' => 'TW 7650 Otomatik Numune Değiştirici, TitriSoft Yazılımı ile',
        'slug' => 'tw-7650-otomatik-numune-degistirici-titrisoft-yazilimi-ile',
        'model' => 'TW 7650 TitriSoft',
        'summary' => 'TW 7650 otomatik numune değiştirici TitriSoft yazılımı ile; 49 numune + 1 boş vial pozisyonu ve TO 7280 entegrasyonu sunar.',
        'body' => 'TW 7650 otomatik numune değiştirici, TitriSoft yazılımı ile kulometrik Karl Fischer titrasyonu iş akışlarında TO 7280 ile birlikte kullanılmak üzere tasarlanmıştır. 49 numune ve 1 boş vial pozisyonu sunar. Güç beslemesi ve güç girişi TO 7280 üzerinden sağlanır; TO 7280 dahil ölçüleri 420 x 450 x 460 mm’dir.',
        'features' => [
            'Otomatik numune değiştirici',
            'TitriSoft yazılımı ile kullanım',
            '49 numune + 1 boş vial pozisyonu',
            'TO 7280 üzerinden güç beslemesi',
            'TO 7280 ile entegre kullanım',
            'Çalışma ve depolama için +10...+40 °C ortam sıcaklığı',
        ],
        'specs' => [
            'Ürün tipi' => 'Otomatik numune değiştirici',
            'Model' => 'TW 7650 TitriSoft',
            'Yazılım' => 'TitriSoft',
            'Pozisyon sayısı' => '49 numune + 1 boş vial',
            'Güç beslemesi' => 'TO 7280 üzerinden',
            'Güç girişi' => 'TO 7280 üzerinden',
            'Ölçüler' => '420 x 450 x 460 mm (G x Y x D), TO 7280 dahil',
            'Ağırlık' => 'TO 7280 olmadan 10 kg; TO 7280 ile 17 kg',
            'Ortam koşulları' => 'Çalışma ve depolama için ortam sıcaklığı +10...+40 °C',
            'İçerik durumu' => 'Görselden Türkçeye çevrildi',
            'Görsel durumu' => 'Placeholder',
        ],
        'content_status' => 'Görselden Türkçeye çevrildi',
        'video' => ['title' => 'TW 7650 TitriSoft tanıtım videosu', 'youtube_url' => 'https://www.youtube.com/watch?v=HXGS9Bd6clk', 'youtube_id' => 'HXGS9Bd6clk'],
    ],
];

foreach ($pendingContentProducts as $product) {
    $products[] = [
        'category_slug' => 'kulometrik-karl-fischer-titratorler',
        'name' => $product['name'],
        'slug' => $product['slug'],
        'image_slugs' => [$product['slug']],
        'model' => $product['model'],
        'summary' => $product['summary'],
        'body' => $product['body'],
        'features' => $product['features'],
        'specs' => $product['specs'],
        'metadata' => [
            'Marka' => 'SI Analitik',
            'Kategori' => 'Kulometrik Karl Fischer Titratörler',
            'Model' => $product['model'],
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Ürün tipi' => $product['specs']['Ürün tipi'],
            'İçerik durumu' => $product['content_status'] ?? 'Görsel bekleniyor',
        ],
        'documents' => [],
        'videos' => $product['video'] ? [$product['video']] : [],
        'image_alt' => $product['name'] . ' ürün görseli',
    ];
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
$deleteVideos = $db->prepare('delete from product_videos where product_id = :product_id');
$insertVideo = $db->prepare('insert into product_videos (product_id, title, youtube_url, youtube_id, sort_order, created_at, updated_at) values (:product_id, :title, :youtube_url, :youtube_id, :sort_order, :created_at, :updated_at)');

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

    $deleteVideos->execute(['product_id' => $productId]);

    foreach ($product['videos'] as $videoIndex => $video) {
        $insertVideo->execute([
            'product_id' => $productId,
            'title' => $video['title'],
            'youtube_url' => $video['youtube_url'],
            'youtube_id' => $video['youtube_id'],
            'sort_order' => ($videoIndex + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

echo 'seeded=' . count($products) . PHP_EOL;
echo 'categories=' . implode(',', array_keys($categories)) . PHP_EOL;
