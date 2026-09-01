<?php

$root = dirname(__DIR__);
$db = new PDO('sqlite:' . $root . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s');
$json = fn (array $value): string => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$category = [
    'name' => 'Isıtmalı Manyetik Karıştırıcılar',
    'slug' => 'isitmali-manyetik-karistirici',
    'summary' => 'Karıştırma ile birlikte kontrollü ısıtma sağlayan manyetik karıştırıcılar.',
    'aliases' => [
        'Isıtmalı Manyetik Karıştırıcı',
        'Isitmali Manyetik Karistirici',
        'Isıtıcılı Manyetik Karıştırıcı',
        'Isiticili Manyetik Karistirici',
        'Heating Magnetic Stirrer',
    ],
];

$products = [
    [
        'name' => 'VELP ARE Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-are-isitmali-manyetik-karistirici',
        'model' => 'ARE',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-are-isitmali-manyetik-karistirici',
        'summary' => 'VELP ARE ısıtmalı manyetik karıştırıcı; analog masa üstü kullanım, ortam sıcaklığından 370 °C seviyesine ısıtma, 15 litreye kadar H2O karıştırma hacmi ve 1500 rpm karıştırma hızıyla laboratuvar uygulamaları için kullanılır.',
        'body' => 'VELP ARE ısıtmalı manyetik karıştırıcı, alüminyum alaşımlı tablası ve epoksi kaplı alüminyum gövdesiyle rutin laboratuvar ısıtma-karıştırma işlemleri için tasarlanmıştır. SpeedServo özelliği viskozite değişimlerinde hızın sabit kalmasına yardımcı olur; eğimli kontrol paneli ise sıcaklık ve hız ayarlarını ayrı ayrı yönetmeyi kolaylaştırır.',
        'features' => [
            'Kimyasallara karşı mükemmel dayanıklılık',
            'Alüminyum alaşımlı tabla ile tüm yüzey boyunca 370 °C seviyesine kadar sıcaklık homojenliği ve optimum ısı transferi',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'Sıcaklık ve hızın ayrı ayrı ayarlanabilmesi',
            'Alarmlar için zenginleştirilmiş LED arayüzü',
            'Ergonomik tasarım',
            'AluBlock aksesuar kombinasyonları ile farklı tüplerle aynı anda çalışma imkanı',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'ARE',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Kullanım alanı' => 'Isıtmalı manyetik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Tabla malzemesi' => 'Özel koruma ile kaplamalı alüminyum alaşım',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı alüminyum gövde',
            'Ağırlık' => '2.6 kg',
            'Boyutlar (GxYxD)' => '165 x 115 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '630 W',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717150725_2882velp_heating_magnetic_stirrers_are-arex_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP ARE ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREX Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arex-isitmali-manyetik-karistirici',
        'model' => 'AREX',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arex-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREX ısıtmalı manyetik karıştırıcı; analog masa üstü kullanım, prob ile dijital sıcaklık kontrolü, ortam sıcaklığından 370 °C seviyesine ısıtma, 20 litreye kadar H2O karıştırma hacmi ve 1500 rpm karıştırma hızıyla kullanılır.',
        'body' => 'VELP AREX ısıtmalı manyetik karıştırıcı, seramik kaplamalı alüminyum alaşımlı tablası ve epoksi kaplı alüminyum gövdesiyle ısıtmalı karıştırma uygulamaları için geliştirilmiştir. Kimyasallara, çiziklere ve yüzey aşınmalarına karşı dayanıklı tabla yapısı; SpeedServo hız kontrolü, PCM tipi tahrik mıknatısı ve VTF Vertex veya VTF EVO sıcaklık sensörü bağlantısıyla laboratuvar kullanımını destekler.',
        'features' => [
            'Seramik kaplamalı alüminyum alaşımlı tabla',
            'Seramik kaplamalı alüminyum alaşımlı tabla ile tüm yüzey boyunca 370 °C seviyesine kadar sıcaklık homojenliği ve optimum ısı transferi',
            'Kimyasallara, çiziklere ve yüzey aşınmalarına karşı mükemmel dayanıklılık',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'Sıcaklık ve hızın ayrı ayrı ayarlanabilmesi',
            'Alarmlar için zenginleştirilmiş LED arayüzü',
            'Ergonomik tasarım',
            'AluBlock aksesuar kombinasyonları ile farklı tüplerle aynı anda çalışma imkanı',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
            'Yükseltilmiş kontrol paneli ve oluk yuvası ile kullanıcı güvenliği',
            'Tahliye oluğu sayesinde sıvı dökülmelerinden kaynaklanabilecek hasarlara karşı koruma',
            'Doğrudan sıcaklık kontrolü için zamanlayıcılı VTF Vertex dijital termoregülatör bağlantısı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREX',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, prob ile dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Opsiyonel aksesuar' => 'VTF Vertex veya VTF EVO sıcaklık sensörü',
            'Kullanım alanı' => 'Isıtmalı manyetik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, prob ile dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Tabla malzemesi' => 'Seramik kaplamalı alüminyum alaşım',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı alüminyum gövde',
            'Ağırlık' => '2.6 kg',
            'Boyutlar (GxYxD)' => '165 x 115 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '630 W',
            'Opsiyonel aksesuar' => 'VTF Vertex veya VTF EVO sıcaklık sensörü',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717150739_2635velp_heating_magnetic_stirrers_are-arex_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP AREX ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREX Digital Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arex-digital-isitmali-manyetik-karistirici',
        'model' => 'AREX Digital',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arex-digital-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREX Digital ısıtmalı manyetik karıştırıcı; dijital masa üstü kullanım, ortam sıcaklığından 370 °C seviyesine ısıtma, 20 litreye kadar H2O karıştırma hacmi, 1500 rpm hız ve ayarlanabilir sıcaklık güvenlik noktasıyla kullanılır.',
        'body' => 'VELP AREX Digital ısıtmalı manyetik karıştırıcı, seramik kaplamalı alüminyum alaşımlı tablası, dijital hız/sıcaklık ayarları ve kolay okunabilen çift LED ekranıyla laboratuvar ısıtma-karıştırma uygulamaları için geliştirilmiştir. 50 °C üzerindeki sıcaklık uyarısı ve ısıtmanın durdurulabileceği ayarlanabilir güvenlik sıcaklık noktası, çalışma güvenliğini destekler.',
        'features' => [
            'Seramik kaplamalı alüminyum alaşımlı tabla ile tüm yüzey boyunca 370 °C seviyesine kadar sıcaklık homojenliği ve optimum ısı transferi',
            'Kimyasallara, çiziklere ve yüzey aşınmalarına karşı mükemmel dayanıklılık',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'Sıcaklık ve hızın dijital olarak ayrı ayrı ayarlanabilmesi',
            'Uzaktan bile görülebilen parlak, kolay okunabilen iki LED ekran',
            '50 °C üzerinde kullanıcı sıcaklık uyarısı',
            'Isıtmanın durdurulabileceği ayarlanabilir sıcaklık güvenlik noktası',
            'Ergonomik tasarım, güvenilir ve hassas sonuçlar',
            'AluBlock aksesuar kombinasyonları ile farklı tüplerle aynı anda çalışma imkanı',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
            'Yükseltilmiş kontrol paneli ve oluk yuvası ile kullanıcı güvenliği',
            'Tahliye oluğu sayesinde sıvı dökülmelerinden kaynaklanabilecek hasarlara karşı koruma',
            'Doğrudan sıcaklık kontrolü için zamanlayıcılı VTF Vertex dijital termoregülatör bağlantısı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREX Digital',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Sıcaklık uyarısı' => '50 °C üzerinde kullanıcı uyarılır',
            'Sıcaklık güvenlik noktası' => 'Isıtmanın durdurulabileceği ayarlanabilir sıcaklık güvenlik ayarı',
            'Kullanım alanı' => 'Dijital ısıtmalı manyetik karıştırma',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Sıcaklık uyarısı' => '50 °C üzerinde kullanıcı uyarılır',
            'Sıcaklık noktası' => 'Isıtmanın durdurulabileceği ayarlanabilir sıcaklık güvenlik ayarı',
            'Tabla malzemesi' => 'Seramik kaplamalı alüminyum alaşım',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı alüminyum gövde',
            'Ağırlık' => '2.6 kg',
            'Boyutlar (GxYxD)' => '165 x 115 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '630 W',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717145203_2916velp_heating_magnetic_stirrers_are-arex_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP AREX Digital ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP HSC Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-hsc-isitmali-manyetik-karistirici',
        'image_slugs' => ['velp-hsc-isitmali-manyetik-karistirici', 'hsc-isitmali-manyetik-karistirici'],
        'model' => 'HSC',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-hsc-isitmali-manyetik-karistirici',
        'summary' => 'VELP HSC ısıtmalı manyetik karıştırıcı; analog masa üstü kullanım, ortam sıcaklığından 400 °C seviyesine ısıtma, 15 litreye kadar H2O karıştırma hacmi ve 1300 rpm karıştırma hızıyla seramik tabla isteyen laboratuvar uygulamaları için kullanılır.',
        'body' => 'VELP HSC ısıtmalı manyetik karıştırıcı, beyaz seramik tablası ve teknopolimer gövdesiyle asit, baz, çözücü ve yüzey aşınması riski bulunan laboratuvar çalışmalarında değerlendirilir. Seramik yüzey renk değişimi gözlemini kolaylaştırır ve kalıntıların temizlenmesini pratik hale getirir.',
        'features' => [
            'Seramik tabla asit, baz ve çözücülere karşı mükemmel direnç sağlar',
            'Teknopolimer gövde kimyasallara, çiziklere ve yüzey aşınmalarına karşı dayanıklıdır',
            'Renk değişimi gözlemi için ideal beyaz seramik tabla',
            'Nemlendirilmiş bez ile kalıntıların kolay temizlenebilmesi',
            'Seramik yüzeyin kimyasal ve mekanik koroziflere karşı üstün direnci',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'Sıcaklık ve hızın ayrı ayrı ayarlanabilmesi',
            'Yüksek güçlü PCM tipi tahrik mıknatısı',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
            'Yükseltilmiş kontrol paneli ve oluk yuvası ile kullanıcı güvenliği',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'HSC',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 400 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1300 rpm',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 400 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1300 rpm',
            'Tabla malzemesi' => 'Seramik',
            'Tabla ölçüleri' => '180 x 180 mm',
            'Gövde malzemesi' => 'Teknopolimer',
            'Ağırlık' => '3.3 kg',
            'Boyutlar (GxYxD)' => '203 x 94 x 344 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '800 W',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717150650_2747velp_heating_magnetic_stirrers_arec_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP HSC ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREC Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arec-isitmali-manyetik-karistirici',
        'model' => 'AREC',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arec-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREC ısıtmalı manyetik karıştırıcı; dijital masa üstü kullanım, ortam sıcaklığından 550 °C seviyesine ısıtma, 15 litreye kadar H2O karıştırma hacmi, 1500 rpm hız ve 50 °C sıcaklık uyarısıyla kullanılır.',
        'body' => 'VELP AREC ısıtmalı manyetik karıştırıcı, seramik tabla ve teknopolimer gövde yapısıyla dijital sıcaklık-hız kontrolü gerektiren laboratuvar uygulamaları için tasarlanmıştır. Beyaz seramik tabla renk değişimi gözlemlerini destekler; parlak LED ekran çalışma koşullarının uzaktan okunmasını kolaylaştırır.',
        'features' => [
            'Seramik tabla asit, baz ve çözücülere karşı mükemmel direnç sağlar',
            'Teknopolimer gövde kimyasallara, çiziklere ve yüzey aşınmalarına karşı dayanıklıdır',
            'Renk değişimi gözlemi için ideal beyaz seramik tabla',
            'Nemlendirilmiş bez ile kalıntıların kolay temizlenebilmesi',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'Sıcaklık ve hızın ayrı ayrı ayarlanabilmesi',
            'Yüksek güçlü PCM tipi tahrik mıknatısı',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Uzaktan bile görülebilen parlak, kolay okunabilen LED ekran',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREC',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Sıcaklık uyarısı' => '50 °C sıcaklıkta kullanıcıyı uyarır',
            'Tabla malzemesi' => 'Seramik',
            'Tabla ölçüleri' => '180 x 180 mm',
            'Gövde malzemesi' => 'Teknopolimer',
            'Ağırlık' => '3.3 kg',
            'Boyutlar (GxYxD)' => '203 x 94 x 344 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '800 W',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717150632_2904velp_heating_magnetic_stirrers_arec_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP AREC ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREC.X Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arec-x-isitmali-manyetik-karistirici',
        'model' => 'AREC.X',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arec-x-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREC.X ısıtmalı manyetik karıştırıcı; dijital masa üstü kullanım, 550 °C ısıtma aralığı, 15 litreye kadar H2O karıştırma hacmi, 1500 rpm hız ve prob bağlantısı seçeneğiyle kullanılır.',
        'body' => 'VELP AREC.X, seramik tabla ve dijital kontrol yapısını doğrudan sıcaklık kontrolü için prob/termoregülatör bağlantısı seçenekleriyle birleştirir. VTF Vertex ve kablosuz VTF EVO uyumluluğu, sıcaklık yönetiminin daha hassas ele alınması gereken uygulamalarda avantaj sağlar.',
        'features' => [
            'Seramik tabla asit, baz ve çözücülere karşı mükemmel direnç sağlar',
            'Renk değişimi gözlemi için ideal beyaz seramik tabla',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Uzaktan bile görülebilen parlak, kolay okunabilen LED ekran',
            'Doğrudan sıcaklık kontrolü için zamanlayıcılı VTF Vertex dijital termoregülatör bağlantısı',
            'Kablosuz VTF EVO ile PC üzerinden termoregülasyon yönetimi',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREC.X',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Opsiyonel aksesuar' => 'Prob',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Sıcaklık uyarısı' => '50 °C sıcaklıkta kullanıcıyı uyarır',
            'Tabla malzemesi' => 'Seramik',
            'Tabla ölçüleri' => '180 x 180 mm',
            'Gövde malzemesi' => 'Teknopolimer',
            'Ağırlık' => '3.3 kg',
            'Boyutlar (GxYxD)' => '203 x 94 x 344 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '800 W',
            'Opsiyonel aksesuar' => 'Prob',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717150950_2588velp_heating_magnetic_stirrers_arec_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP AREC.X ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREC.T Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arec-t-isitmali-manyetik-karistirici',
        'model' => 'AREC.T',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arec-t-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREC.T ısıtmalı manyetik karıştırıcı; dijital masa üstü kullanım, 550 °C ısıtma aralığı, 15 litre karıştırma hacmi, 1500 rpm hız, 999 dakikaya kadar zamanlayıcı ve otomatik durdurma özellikleriyle kullanılır.',
        'body' => 'VELP AREC.T, AREC serisinin zamanlayıcı ve otomatik durdurma odaklı dijital modelidir. Isıtma ve karıştırma işlemlerinin süreye bağlı yönetilmesi gereken uygulamalarda 999 dakikaya kadar ayarlanabilen zamanlayıcı ile çalışır.',
        'features' => [
            'Seramik tabla asit, baz ve çözücülere karşı mükemmel direnç sağlar',
            'Renk değişimi gözlemi için ideal beyaz seramik tabla',
            'Kolay erişim ve görünürlük sağlayan eğimli kontrol paneli',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Uzaktan bile görülebilen parlak, kolay okunabilen LED ekran',
            '999 dakikaya kadar ayarlanabilir zamanlayıcı',
            'Otomatik ısıtma durdurma',
            'Otomatik karıştırma durdurma',
            'Düşük hızda bile mükemmel hız kontrolü',
            'IP 42 koruma sınıfı ile dökülmelere karşı koruma',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREC.T',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Zamanlayıcı' => '999 dakikaya kadar',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 550 °C',
            'Karıştırma hacmi' => '15 litreye kadar (H2O)',
            'Karıştırma hızı' => '1500 rpm',
            'Sıcaklık uyarısı' => '50 °C sıcaklıkta kullanıcıyı uyarır',
            'Tabla malzemesi' => 'Seramik',
            'Tabla ölçüleri' => '180 x 180 mm',
            'Gövde malzemesi' => 'Teknopolimer',
            'Zamanlayıcı' => '999 dakikaya kadar ayarlanabilir',
            'Otomatik ısıtma durdurma' => 'Var',
            'Otomatik karıştırma durdurma' => 'Var',
            'Ağırlık' => '3.3 kg',
            'Boyutlar (GxYxD)' => '203 x 94 x 344 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '800 W',
        ],
        'documents' => [
            [
                'title' => 'Katalog / Türkçe',
                'type' => 'catalog',
                'url' => 'https://www.sentezgroup.com.tr/img/mc-content/20170717151137_2534velp_heating_magnetic_stirrers_arec_comparison_table.pdf',
                'path' => null,
            ],
        ],
        'image_alt' => 'VELP AREC.T ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AM4 Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-am4-isitmali-manyetik-karistirici',
        'model' => 'AM4',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-am4-isitmali-manyetik-karistirici',
        'summary' => 'VELP AM4 ısıtmalı manyetik karıştırıcı; dörtlü analog masa üstü kullanım, 4 x 15 litre toplam 60 litre karıştırma hacmi, 370 °C ısıtma aralığı ve 1500 rpm hız ile çoklu karıştırma uygulamaları için kullanılır.',
        'body' => 'VELP AM4, dört bağımsız karıştırma pozisyonu bulunan analog ısıtmalı manyetik karıştırıcıdır. Her göz farklı sıcaklık ve hız değerlerine ayrı ayrı ayarlanabilir; bu yapı paralel numune hazırlama süreçlerinde zaman ve tezgah alanı avantajı sağlar.',
        'features' => [
            '4 göz ayrı ayrı ve birbirinden bağımsız sıcaklık ve hız değerlerine ayarlanabilir',
            'Karıştırma konum merkezleri arasında 186 mm mesafe',
            'Alüminyum alaşımlı tabla ile 370 °C seviyesine kadar sıcaklık homojenliği ve optimum ısı transferi',
            'Kimyasallara karşı mükemmel dayanıklılık',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AM4',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dörtlü analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '4 x 15 litre, toplam 60 litre',
            'Karıştırma hızı' => '1500 rpm',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dörtlü analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '4 x 15 litre, toplam 60 litre',
            'Karıştırma hızı' => '1500 rpm',
            'Tabla malzemesi' => 'Özel koruma alüminyum alaşım',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı metal gövde',
            'Ağırlık' => '8.3 kg',
            'Boyutlar (GxYxD)' => '715 x 115 x 246 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '2550 W',
            'Opsiyonel aksesuar' => 'Prob',
        ],
        'documents' => [],
        'image_alt' => 'VELP AM4 dörtlü ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AM4X Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-am4x-isitmali-manyetik-karistirici',
        'model' => 'AM4X',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-am4x-isitmali-manyetik-karistirici',
        'summary' => 'VELP AM4X ısıtmalı manyetik karıştırıcı; dörtlü analog masa üstü kullanım, toplam 60 litre karıştırma hacmi, 370 °C ısıtma aralığı ve dört VTF Vertex termoregülatör bağlama imkanıyla kullanılır.',
        'body' => 'VELP AM4X, AM4 ailesinin termoregülatör prob bağlantısı destekli dörtlü modelidir. Dört gözün bağımsız sıcaklık ve hız kontrolü ile aynı anda farklı numuneler üzerinde çalışma yapılabilir.',
        'features' => [
            '4 göz ayrı ayrı ve birbirinden bağımsız sıcaklık ve hız değerlerine ayarlanabilir',
            'Karıştırma konum merkezleri arasında 186 mm mesafe',
            'Doğrudan sıcaklık kontrolü için 4 adet VTF Vertex dijital termoregülatör bağlanabilmesi',
            'Alüminyum alaşımlı tabla ile 370 °C seviyesine kadar sıcaklık homojenliği ve optimum ısı transferi',
            'Kimyasallara karşı mükemmel dayanıklılık',
            'Düşük hızlarda dahi mükemmel hız kontrolü',
            'SpeedServo özelliği ile viskozite değiştiğinde bile sabit hız',
            'Sürekli çalışma için tek fazlı motorla çalışan yüksek güçlü PCM tipi tahrik mıknatısı',
            'Aksesuarlarla 50, 100, 250, 500 ve 1000 ml yuvarlak tabanlı balonlarla çalışma imkanı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AM4X',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dörtlü analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '4 x 15 litre, toplam 60 litre',
            'Karıştırma hızı' => '1500 rpm',
            'Opsiyonel aksesuar' => 'Thermoregulator prob',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dörtlü analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '4 x 15 litre, toplam 60 litre',
            'Karıştırma hızı' => '1500 rpm',
            'Tabla malzemesi' => 'Özel koruma alüminyum alaşım',
            'Tabla ölçüleri' => '155 mm çap',
            'Gövde malzemesi' => 'Epoksi kaplı metal gövde',
            'Ağırlık' => '8.3 kg',
            'Boyutlar (GxYxD)' => '715 x 115 x 246 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '2550 W',
            'Opsiyonel aksesuar' => 'Thermoregulator prob',
        ],
        'documents' => [],
        'image_alt' => 'VELP AM4X dörtlü ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP ARE-6 Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-are-6-isitmali-manyetik-karistirici',
        'model' => 'ARE-6',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-are-6-isitmali-manyetik-karistirici',
        'summary' => 'VELP ARE-6 ısıtmalı manyetik karıştırıcı; analog masa üstü kullanım, 370 °C ısıtma aralığı, 20 litre H2O karıştırma hacmi, 30-1700 rpm hız ve fırçasız rotor motoruyla kullanılır.',
        'body' => 'VELP ARE-6, 30-1700 rpm aralığında çalışan güçlü fırçasız motoru, alüminyum alaşımlı tablası ve SpeedServo tork kompenzasyonu ile yeni nesil analog ısıtmalı manyetik karıştırıcıdır. Işıklı sıcak yüzey uyarısı ve aşırı ısıtma önleyici güvenlik anahtarı çalışma güvenliğini artırır.',
        'features' => [
            'Alüminyum alaşım tabla optimum ısı transferi ve kimyasal direnç sağlar',
            '370 °C seviyesine kadar doğru tabla sıcaklığı kontrolü',
            '30-1700 rpm arasında güçlü fırçasız motor',
            'SpeedServo tork kompenzasyonu ile viskozite değişikliklerinde sabit hız',
            'Alnico mıknatıs ile güçlü manyetik eşleşme',
            'Analog çevirme düğmeleriyle hız ve sıcaklık ayarı',
            'Işıklı aç/kapa emniyetli ısıtma anahtarı',
            '50 °C üzerinde ışıklı sıcak yüzey uyarısı',
            'Yavaş hızlanma ile dökülme riskinin azaltılması',
            'Opsiyonel silikon koruyucu kapak',
            'AluBlock aksesuar kombinasyonları ile farklı tüplerle çalışma imkanı',
            'PTFE güvenlik kapakları ile termal dağılımı azaltma',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'ARE-6',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litre H2O',
            'Karıştırma hızı' => '30-1700 rpm',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litre H2O',
            'Karıştırma hızı' => '30-1700 rpm',
            'Tabla malzemesi' => 'Alüminyum alaşım',
            'Tabla ölçüleri' => '135 mm çap',
            'Sıcaklık uyarısı' => '50 °C geçince ışıklı sinyal ile uyarı',
            'Emniyet sıcaklık anahtarı' => 'Aşırı ısıtma önleyici güvenlik anahtarı',
            'Motor tipi' => 'Fırçasız rotor',
            'Tork kompenzasyonu' => 'SpeedServo',
            'Ağırlık' => '2.6 kg',
            'Boyutlar (GxYxD)' => '160 x 105 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '630 W',
            'Çalışma sıcaklığı' => '5-40 °C',
        ],
        'documents' => [],
        'image_alt' => 'VELP ARE-6 ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREX-6 Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arex-6-isitmali-manyetik-karistirici',
        'model' => 'AREX-6',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arex-6-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREX-6 ısıtmalı manyetik karıştırıcı; analog, prob ile dijital masa üstü kullanım, seramik kaplamalı alüminyum tabla, 370 °C ısıtma aralığı, 20 litre H2O hacim ve 30-1700 rpm hız ile kullanılır.',
        'body' => 'VELP AREX-6, CerAlTop seramik kaplamalı alüminyum tablası ve SpeedServo tork kompenzasyonu ile kimyasal dayanım ve hassas hız kontrolünü bir araya getirir. Thermoregulator prob bağlantısı ve PID kontrollü termoregülasyon desteğiyle sıcaklık kontrolü gerektiren uygulamalar için uygundur.',
        'features' => [
            'CerAlTop seramik kaplamalı alüminyum alaşım tabla ile optimum ısı transferi',
            'Kimyasallara ve çizilmelere karşı dayanıklı tabla yüzeyi',
            '370 °C seviyesine kadar doğru tabla sıcaklığı kontrolü',
            '30-1700 rpm arasında güçlü fırçasız motor',
            'SpeedServo tork kompenzasyonu ile viskozite değişikliklerinde sabit hız',
            'Alnico mıknatıs ile güçlü manyetik eşleşme',
            'Analog çevirme düğmeleriyle hız ve sıcaklık ayarı',
            '50 °C üzerinde ışıklı sıcak yüzey uyarısı',
            'Yavaş hızlanma ile dökülme riskinin azaltılması',
            'PID kontrollü hızlı, doğru ve istikrarlı sıcaklık kontrolü',
            'AluBlock aksesuar kombinasyonları ile farklı tüplerle çalışma imkanı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREX-6',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Analog, prob ile dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litre H2O',
            'Karıştırma hızı' => '30-1700 rpm',
            'Opsiyonel aksesuar' => 'Thermoregulator prob',
        ],
        'specs' => [
            'Cihaz tipi' => 'Analog, prob ile dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litre H2O',
            'Karıştırma hızı' => '30-1700 rpm',
            'Tabla malzemesi' => 'Seramik kaplamalı alüminyum alaşım',
            'Tabla ölçüleri' => '135 mm çap',
            'Sıcaklık uyarısı' => '50 °C geçince ışıklı sinyal ile uyarı',
            'Emniyet sıcaklık anahtarı' => 'Aşırı ısıtma önleyici güvenlik anahtarı',
            'Motor tipi' => 'Fırçasız rotor',
            'Tork kompenzasyonu' => 'SpeedServo',
            'Ağırlık' => '2.6 kg',
            'Boyutlar (GxYxD)' => '160 x 105 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '630 W',
            'Çalışma sıcaklığı' => '5-40 °C',
            'Opsiyonel aksesuar' => 'Thermoregulator prob',
        ],
        'documents' => [],
        'image_alt' => 'VELP AREX-6 ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
    [
        'name' => 'VELP AREX-6 Digital Isıtmalı Manyetik Karıştırıcı',
        'slug' => 'velp-arex-6-digital-isitmali-manyetik-karistirici',
        'model' => 'AREX-6 Digital',
        'sku' => null,
        'old_url' => 'https://www.labor.com.tr/urun/velp-arex-6-digital-isitmali-manyetik-karistirici',
        'summary' => 'VELP AREX-6 Digital ısıtmalı manyetik karıştırıcı; dijital masa üstü kullanım, 1 °C sıcaklık ayar hassasiyeti, 1-99 dakika zamanlayıcı, 20 litre H2O hacim ve 30-1700 rpm hız ile kullanılır.',
        'body' => 'VELP AREX-6 Digital, yeni LED arayüz, otomatik ters yönde karıştırma, sıcaklık probu algılama ve gelişmiş menü seçenekleriyle AREX-6 ailesinin dijital kontrol odaklı modelidir. PID kontrollü termoregülasyon ve SpeedServo teknolojisi, hassas sıcaklık ve hız kontrolünü destekler.',
        'features' => [
            'CerAlTop seramik kaplamalı alüminyum alaşım tabla ile hızlı ve homojen ısı aktarımı',
            'Kimyasallara ve çizilmelere karşı dayanıklı tabla yüzeyi',
            '370 °C seviyesine kadar doğru tabla sıcaklığı kontrolü',
            '30-1700 rpm arasında güçlü fırçasız motor',
            'SpeedServo tork kompenzasyonu ile viskozite değişikliklerinde sabit hız',
            'Siyah zeminli beyaz LED ekran ve ikonlu akıllı kullanıcı arayüzü',
            'Prob bağlantısı, ısı aktivasyonu, ayar noktası sıcaklığı, zamanlayıcı ve otomatik geri seçenek ikonları',
            '1-99 dakika arası zamanlayıcı',
            'Otomatik ters yönde karıştırma',
            'Sıcaklık probu algılama sensörü',
            'PID kontrollü hızlı, doğru ve istikrarlı sıcaklık kontrolü',
            'AluBlock aksesuar kombinasyonları ile farklı tüplerle çalışma imkanı',
        ],
        'metadata' => [
            'Marka' => 'VELP',
            'Kategori' => 'Isıtmalı Manyetik Karıştırıcılar',
            'Üst kategori' => 'Manyetik Karıştırıcılar / Karıştırıcılar',
            'Model' => 'AREX-6 Digital',
            'SKU' => 'Yayın öncesi netleştirilecek',
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Karıştırma hacmi' => '20 litre H2O',
            'Karıştırma hızı' => '30-1700 rpm',
            'Zamanlayıcı' => '1-99 dakika',
            'Opsiyonel aksesuar' => 'Thermoregulator prob',
        ],
        'specs' => [
            'Cihaz tipi' => 'Dijital, masa üstü',
            'Isıtma aralığı' => 'Ortam sıcaklığı - 370 °C',
            'Sıcaklık ayar hassasiyeti' => '1 °C',
            'Zamanlayıcı süresi' => '1-99 dakika arası ayarlanabilir, istenildiğinde durdurulabilir',
            'Karıştırma hacmi' => '20 litre H2O',
            'Karıştırma hızı' => '30-1700 rpm',
            'Tabla malzemesi' => 'Alüminyum alaşım',
            'Tabla ölçüleri' => '135 mm çap',
            'Sıcaklık uyarısı' => '50 °C geçince ışıklı sinyal ile uyarı',
            'Emniyet sıcaklık anahtarı' => 'Aşırı ısıtma önleyici güvenlik anahtarı',
            'Motor tipi' => 'Fırçasız rotor',
            'Tork kompenzasyonu' => 'SpeedServo',
            'Otomatik ters yönde karıştırma' => 'Var',
            'Sıcaklık probu algılama' => 'Probun batırıldığını anlayan sensör',
            'Ağırlık' => '2.6 kg',
            'Boyutlar (GxYxD)' => '160 x 105 x 280 mm',
            'Koruma sınıfı' => 'IP 42',
            'Güç' => '630 W',
            'Çalışma sıcaklığı' => '5-40 °C',
            'Opsiyonel aksesuar' => 'Thermoregulator prob',
        ],
        'documents' => [],
        'image_alt' => 'VELP AREX-6 Digital ısıtmalı manyetik karıştırıcı ürün görseli',
    ],
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
