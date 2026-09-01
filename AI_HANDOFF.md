# MTA Endüstri Site Proje Özeti

Son güncelleme: 2026-08-31

Bu dosya, MTA Endüstri web projesinin mevcut durumunu, dosya yapısını, kararları ve yapılan işlemleri diğer yapay zeka araçlarıyla veya geliştiricilerle paylaşmak için tutulur. Yeni önemli işlem yapıldıkça "İşlem Notları" bölümü güncellenmelidir.

## Kısa Durum

Proje `C:\Users\serca\Desktop\MTA\mta-site` klasöründe Laravel tabanlı olarak kuruldu.

Amaç: cPanel/MySQL ortamına uyumlu, SEO odaklı, kurumsal ön yüzü güçlü, sonrasında Filament admin paneliyle yönetilebilir bir katalog ve hizmet sitesi oluşturmak.

Mevcut ön yüz fazında:

- Ana sayfa tasarımı yenilendi.
- Hizmet detay sayfası referans verilen kalibrasyon sitesiyle benzer içerik mimarisine taşındı, fakat MTA tasarım diliyle daha kurumsal hazırlandı.
- Ürün kategori, marka ve ürün detay sayfaları oluşturuldu.
- Blog genel sayfası, blog kategori sayfası ve blog detay sayfası oluşturuldu.
- Teknik servis genel sayfası ve dört teknik servis detay sayfası oluşturuldu.
- Kurumsal sayfalar, sertifikalar, referanslar ve iletişim sayfaları oluşturuldu.
- SEO meta, canonical, Open Graph, robots.txt, sitemap.xml ve JSON-LD schema yapıları eklendi.
- Ürünler sepet/e-ticaret mantığında değil, katalog ve teklif talebi mantığında kurgulandı.
- WordPress XML ürünleri toplu şekilde yayına alınmadı; kullanıcının verdiği kategori + marka izin haritasına göre temizlenip süzülen ürünler geçici JSON kaynağına bağlandı.

## Teknoloji Kararı

Seçilen altyapı:

- Laravel
- Blade
- Vite
- Tailwind giriş dosyası ve özel CSS
- MySQL hedefli yapı
- Filament admin paneli ikinci fazda eklenecek

Neden Laravel:

- cPanel ve MySQL için daha uygun.
- SEO açısından sunucu tarafı render edilmiş HTML üretir.
- Admin panel için Filament ile hızlı ve sürdürülebilir yönetim sağlar.
- Ürün, kategori, marka, sayfa, SEO, FAQ ve lead yönetimi için genişlemeye uygundur.

Şu an temel sözlükler statik config dosyasından, ürün kataloğu ise önce `config/mta.php`, varsa ardından temiz import JSON dosyasından okunuyor. İkinci fazda bunlar database tablolarına taşınacak.

## Ana Dosya Yapısı

Proje kökü:

```text
C:\Users\serca\Desktop\MTA\mta-site
```

Önemli dosyalar:

```text
config/mta.php
app/Http/Controllers/SiteController.php
app/Models/
app/Support/WpProductImport.php
routes/web.php
routes/console.php
database/migrations/2026_08_27_010000_create_mta_content_tables.php
database/migrations/2026_08_27_011000_add_content_detail_fields.php
database/migrations/2026_08_27_012000_add_service_taxonomy_fields.php
database/migrations/2026_08_27_013000_add_article_meta_fields.php
resources/views/layouts/site.blade.php
resources/views/pages/home.blade.php
resources/views/pages/services.blade.php
resources/views/pages/service-detail.blade.php
resources/views/pages/products.blade.php
resources/views/pages/product-category.blade.php
resources/views/pages/product-brand.blade.php
resources/views/pages/product-detail.blade.php
resources/views/pages/knowledge.blade.php
resources/views/pages/knowledge-category.blade.php
resources/views/pages/article-detail.blade.php
resources/views/pages/about.blade.php
resources/views/pages/certificates.blade.php
resources/views/pages/references.blade.php
resources/views/pages/contact.blade.php
resources/views/partials/product-card.blade.php
resources/views/partials/article-card.blade.php
resources/views/partials/cta.blade.php
resources/views/sitemap.blade.php
resources/css/app.css
public/mta-logo.png
public/favicon.png
public/images/site/mta-endustri-logo-2.png
public/images/services/
public/images/brands/
public/images/products/
storage/app/imports/mta-products-normalized.json
storage/app/imports/mta-products-import-report.json
.env
.env.example
```

## Veri Kaynağı

Ana geçici veri dosyası:

```text
config/mta.php
```

Bu dosyada şu veri grupları bulunur:

- `site`: Site adı, açıklama, iletişim placeholder bilgileri.
- `services`: Kalibrasyon hizmetleri.
- `technical_services`: Teknik servis sayfaları.
- `product_categories`: Ürün kategori sözlüğü.
- `product_brands`: Marka sözlüğü.
- `product_category_brands`: Hangi kategoride hangi markaların kullanılacağını belirleyen ilişki haritası.
- `product_category_services`: Ürün kategorilerinin hangi kalibrasyon hizmetleriyle ilişkilendirileceğini belirleyen SEO ve içerik haritası.
- `products`: Örnek ürünler ve import JSON yoksa fallback ürünleri.
- `articles`: Blog/bilgi merkezi örnek içerikleri.
- `faqs`: Genel SSS içerikleri.

Temiz ürün import çıktıları:

```text
storage/app/imports/mta-products-normalized.json
storage/app/imports/mta-products-import-report.json
```

Ön yüz ürün okuma akışı:

```text
SiteController::productsData()
```

Bu method önce `config/mta.php` içindeki örnek ürünleri, sonra varsa `storage/app/imports/mta-products-normalized.json` ürünlerini birleştirir. Aynı `category_slug/slug` ikilisi tekrar ederse ilk kayıt korunur.

## Ürün Import Aracı

Komut:

```bash
php artisan mta:import-products
```

Varsayılan XML kaynağı:

```text
C:\Users\serca\Desktop\MTA\mtaendstri.WordPress.2026-08-26.xml
```

Import mantığı:

- WordPress XML içinden sadece `product` kayıtları okunur.
- Yayında olmayan ürünler varsayılan olarak dışarıda kalır; taslaklar istenirse `--include-drafts` ile rapora dahil edilebilir.
- Demo, tema, Woodmart/Elementor kaynaklı açıklama artıkları temizlenir veya güvenli özet metinle değiştirilir.
- Ürün kategorisi `product_categories` ve alias listesine göre normalize edilir.
- Ürün markası `product_brands`, alias listesi ve çok net model aileleri üzerinden normalize edilir.
- Sadece `product_category_brands` içinde izin verilen kategori + marka eşleşmeleri ürün listesine alınır.
- Görsel eşleşmesi `_thumbnail_id`, medya dosya adı ve slug üzerinden yapılır; WordPress boyut türevleri yerine orijinal görsel adı tercih edilir.

Son import sonucu:

```text
XML içindeki ürün: 509
Alınan ürün: 165
Dışarıda kalan ürün: 344
brand_not_allowed_for_category: 12
unsupported_category: 225
status_not_publish: 16
unsupported_brand_or_missing_brand: 91
```

Not: Bu işlem tüm ürünleri siteye kör şekilde çekmez. Kullanıcının verdiği kategori ve marka eşleşme kuralına uyan ürünleri alır; kalanları rapor dosyasında gerekçesiyle tutar.

## Rota Yapısı

Rotalar:

```text
routes/web.php
```

Ana rotalar:

```text
/                                  Ana sayfa
/hizmetler                         Hizmet liste sayfası
/hizmetler/{slug}                  Hizmet detay sayfası
/teknik-servis                     Teknik servis liste sayfası
/teknik-servis/{slug}              Teknik servis detay sayfası
/urunler                           Ürün liste sayfası
/urunler/marka/{brand}             Marka sayfası
/urunler/{category}                Ürün kategori sayfası
/urunler/{category}/{slug}         Ürün detay sayfası
/bilgi-merkezi                     Blog/bilgi merkezi liste
/bilgi-merkezi/kategori/{category} Blog kategori sayfası
/bilgi-merkezi/{slug}              Blog detay sayfası
/hakkimizda                        Kurumsal sayfa
/sertifikalar                      Sertifika sayfası
/referanslar                       Referans sayfası
/iletisim                          İletişim ve teklif formu
/robots.txt                        Dinamik robots çıktısı
/sitemap.xml                       Dinamik sitemap çıktısı
```

Rota sırası önemlidir:

- `/urunler/marka/{brand}`, `/urunler/{category}` rotasından önce gelmelidir.
- `/bilgi-merkezi/kategori/{category}`, `/bilgi-merkezi/{slug}` rotasından önce gelmelidir.

## Controller Mantığı

Ana controller:

```text
app/Http/Controllers/SiteController.php
```

Görevleri:

- Config verisini okuyup ilgili Blade sayfalarına gönderir.
- Slug ile hizmet, ürün, kategori, marka ve blog içeriği bulur.
- Teknik servis liste ve detay sayfalarını yönetir.
- Ürün listeleme ve filtreleme yapar.
- Kategori sayfasında ilgili markaları gösterir.
- Marka sayfasında ilgili kategorileri gösterir.
- Ürün detay sayfasında ürünün kendi `related_services` alanını ve kategori bazlı `product_category_services` haritasını birleştirerek ilgili kalibrasyon hizmetlerini gösterir.
- Sitemap ve robots.txt üretir.
- Meta title, description, canonical ve OG image üretir.
- JSON-LD schema üretir.

Önemli yardımcı metodlar:

```text
productCategories()
productBrands()
productBrandsForCategory()
productCategoriesForBrand()
relatedServicesForProduct()
technicalServices()
technicalServiceDetail()
findProductCategory()
findProductBrand()
meta()
schemaGraph()
webPageSchema()
breadcrumbSchema()
serviceSchema()
productSchema()
articleSchema()
```

## Tasarım Mantığı

Ana layout:

```text
resources/views/layouts/site.blade.php
```

Burada:

- HTML head
- Meta etiketleri
- Open Graph
- Twitter card
- Canonical
- Favicon
- JSON-LD
- Top header iletişim alanı
- Top header sosyal medya ikonları
- Masaüstü mega menüler
- Header
- Footer
- Mobil menü

bulunur.

Ana CSS:

```text
resources/css/app.css
```

Tasarım yaklaşımı:

- Logo renklerinden türetilmiş teal ve navy ağırlıklı kurumsal palet.
- Site genelinde Poppins font ailesi.
- Açık zemin, temiz kartlar, net CTA alanları.
- Ürünlerde tek ana görsel mantığı.
- Hizmet sayfalarında büyük hero görseli, cihaz kapsamı, teknik tablo, süreç, SSS ve teklif CTA yapısı.
- Blog ve kategori sayfalarında SEO uyumlu bilgi mimarisi.
- Kalibrasyon, teknik servis ve ürün başlıkları masaüstünde mega menü olarak açılır.
- Mega menüler header container referansıyla ortalı açılır; Kalibrasyon, Teknik Servis ve Ürünler aynı hizalama davranışını kullanır.
- Mega menülerde dışarı tıklama, `Escape`, focus çıkışı ve touch cihazlarda tıklama/toggle kapanış davranışları `resources/js/app.js` içinde yönetilir.
- Kalibrasyon ve Teknik Servis mega menülerinde sol intro alanı badge, kısa metin, küçük görsel ve `Tüm hizmetler` butonu içerir; alt menü kartlarında sağ altta `İncele` aksiyonu ve sağ ok vardır.
- Ürünler mega menüsünde sol görselli intro alanı kaldırıldı; sadece ana kategori kartları 4 kolon dizilir. Alt kategori kırılımları kategori sayfaları için veri olarak korunur, fakat mega menüde gösterilmez. `Tüm ürünler` aksiyonu grid içinde sağ alt hücrede küçük, tek satırlık link-button olarak gösterilir.
- Header ana menüsünde Markalar linki kaldırıldı; Ürünler sonrası sıra `Kurumsal - Sertifikalar - Blog - İletişim` olarak düzenlendi.
- Mobil menüde kalibrasyon ve teknik servis alt linkleri sade liste olarak gösterilir.
- İç sayfa hero alanları ana header/logo çizgisiyle aynı container hizasına alındı; `page-hero` içindeki dar/ortalanmış `.narrow` davranışı kaldırıldı.
- Hizmetler ve teknik servis hero ailesinde H1 maksimum değeri 54px'e düşürüldü; ana sayfa hero yapısı ayrı tutuldu.
- Hizmet/teknik servis hero alanlarındaki üst rozet satırı kaldırıldı; kapsam/uyarı bilgisi görsel üzerindeki chip alanında tutulur.

Top header iletişim bilgileri `config/mta.php -> site` alanından gelir:

```text
Telefon: +90 (216) 390 17 78
Mail: info@mtaend.com
Fax: +90 (216) 390 17 88
Adres: Bahçelievler, Köknar Sk. No:15/B, 34890 Pendik/İstanbul
```

## Ana Sayfa

Dosya:

```text
resources/views/pages/home.blade.php
```

Güncel ana sayfa blokları:

- Hero: Kalibrasyon, teknik servis ve laboratuvar cihazları mesajı.
- Öne çıkan güven/akreditasyon bandı.
- Kalibrasyon hizmet kartları.
- Teknik servis kartları.
- Kalibrasyon süreci.
- Laboratuvar altyapısı.
- Ürün kataloğu ön izlemesi.
- Hizmet verilen alanlar.
- CTA bandı.
- Bilgi merkezi/blog kartları.

Referans alınan ekran görüntüsündeki içerik akışı korundu, ancak tasarım birebir kopyalanmadı.

## Hizmet Detay Sayfası

Dosya:

```text
resources/views/pages/service-detail.blade.php
```

Güncel hizmet detay blokları:

- Breadcrumb
- Hero alanı
- Hizmet başlığı
- Hizmet özeti
- Teklif butonu
- Ölçüm aralıkları butonu
- Hizmet görseli
- Kapsam etiketi
- Kalibre edilebilecek cihazlar
- Teknik kapasite tablosu
- Kalibrasyon süreci
- Standartlar ve kullanım alanları
- SSS
- Hizmete özel CTA
- İlgili ürünler

Örnek olarak `Basınç Kalibrasyonu` sayfası daha dolu içerikle tutuldu.

Hizmetlerde detaylı cihaz/aralık verisi `config/mta.php` içindeki `services[*].scope_groups` alanında tutulur. Şu hizmetlere kullanıcıdan gelen gerçek kapsam listeleri eklendi:

```text
Hacim Kalibrasyonu
Sıcaklık Kalibrasyonu
Basınç Kalibrasyonu
Kütle & Terazi Kalibrasyonu
Tork Kalibrasyonu
Devir Kalibrasyonu
```

Bu veriler hizmet iç sayfasında "Detaylı kapsam" bölümünde grup kartları ve cihaz/aralık satırları olarak gösterilir.

## Teknik Servis Sistemi

Teknik servis verileri:

```text
config/mta.php -> technical_services
```

Sayfalar:

```text
resources/views/pages/technical-services.blade.php
resources/views/pages/technical-service-detail.blade.php
```

Eklenen teknik servis sayfaları:

```text
Analiz ve Ölçüm Cihazları Teknik Servis
Laboratuvar Cihazları İçin Teknik Servis
Terazi Teknik Servis
Tork Anahtarları Servisi
```

İçerik omurgası:

```text
Hero
Hizmet tanımı
Servis verilen cihazlar
Verilen hizmetler / işlem adımları
Neden MTA Endüstri
SSS
Teklif CTA
İlgili kalibrasyon hizmetleri
İlgili ürünler
```

Bu içerikler kullanıcının verdiği MTA Endüstri teknik servis URL'leri incelenerek, yeni sitenin kurumsal tasarım diline uygun biçimde düzenlendi.

## Ürün Sistemi

Ürün sistemi e-ticaret değildir. Sepet, ödeme, stok, sahte fiyat ve sahte değerlendirme yoktur.

Mantık:

- Marka
- Kategori
- Ürün
- Model
- SKU
- Teknik özellikler
- Doküman alanları
- Tek görsel
- İlgili hizmetler
- Teklif/bilgi talebi

Ürün-hizmet ilişkilendirmesi iki katmanlıdır:

```text
products[*].related_services
product_category_services
```

Önce ürüne özel `related_services` okunur, sonra kategori bazlı `product_category_services` haritası ile birleştirilir. Böylece XML importundan gelecek ürünlerde kategori doğru eşleşirse ürün detay sayfasında ilgili kalibrasyon hizmeti otomatik gösterilir.

Ürün kartı partial:

```text
resources/views/partials/product-card.blade.php
```

Ürün liste sayfası:

```text
resources/views/pages/products.blade.php
```

Ürün kategori sayfası:

```text
resources/views/pages/product-category.blade.php
```

Ürün marka sayfası:

```text
resources/views/pages/product-brand.blade.php
```

Ürün kategori ve marka sayfalarında yatay kategori/marka chip barları kaldırıldı. Bu sayfalarda masaüstünde sol filtre paneli ve sağ ürün listesi çalışır; mobilde filtre paneli ürün listesinin üstüne gelir.

`/urunler` ana ürün katalog sayfası artık genel ürün listesi/filtre ekranı olarak değil, kategori ve marka yönlendirme sayfası olarak kurgulanır. Hero altındaki ilk SEO metin bloğu ve filtreli tüm ürün listesi kaldırıldı. Kategori kartları her kategoriye ait ilk ürün görselini otomatik çeker, ürün sayısı göstermez ve sağ altta `İncele` aksiyonu kullanır.

Katalog arayüzünde kullanılan ana sınıflar:

```text
catalog-layout
catalog-sidebar
catalog-filter-group
catalog-filter-list
catalog-content
catalog-results-header
catalog-product-grid
```

Ürün detay sayfası:

```text
resources/views/pages/product-detail.blade.php
```

Ürün detayında ilgili hizmetler üç yerde kullanılır:

```text
Ana içerikte "Ürün ve hizmet eşleşmesi" bilgi alanı
Sağ teklif panelinde kısa ilgili hizmet linkleri
Sayfa altında görselli ilgili hizmet kartları
```

Product JSON-LD schema içinde ürün teknik özellikleri `additionalProperty`, ilgili kalibrasyon hizmetleri ise `isRelatedTo` altında Service olarak verilir.

## Kategori ve Marka İlişki Haritası

Kural:

Kullanıcı kategori başlığını ve marka listesini verecek. O kategoride ilgili markadan ne kadar ürün varsa çekilecek ve ürünler ilgili kategori ve markaya yerleştirilecek.

Bu ilişki şurada tutulur:

```text
config/mta.php -> product_category_brands
```

Güncel harita:

```text
Teraziler: A&D, Shimadzu, Ohaus, Weightlab
Nem Tayin: A&D, Ohaus, Weightlab, Shimadzu
Kral Fischer: Mettler Toledo, SI Analitik, TitroLine 7500 KF, Kyoto KEM
Potansiyometrik Titratörler: Mettler Toledo, SI Analitik, Kyoto KEM
Densitometre: Kyoto KEM, Mettler Toledo, Bellingham + Stanley
Refraktometre: Kyoto KEM, Mettler Toledo, Bellingham + Stanley
pH Metre: Mettler Toledo, Ohaus, WTW
pH & İletkenlik: Mettler Toledo, Ohaus, WTW
Viskozimetre: Brookfield, Lamy
Etüv: Weightlab
Balon Isıtıcılar: VELP, Weightlab
Termoreaktör: VELP
Homojenizatör: VELP, Weightlab
Mekanik Karıştırıcı: VELP, Weightlab
Manyetik Karıştırıcı: VELP, Weightlab
Karıştırıcılar: VELP, Weightlab, Ohaus, Cole-Parmer Stuart
Isıtmalı Manyetik Karıştırıcı: VELP, Weightlab, Ohaus, Cole-Parmer Stuart
Isıtmasız Manyetik Karıştırıcı: VELP, Weightlab, Cole-Parmer Stuart
Vorteks Karıştırıcılar: VELP, Weightlab, Cole-Parmer Stuart
Jar Test: VELP
Diğer Çevre Cihazları: VELP, Weightlab
Soğutmalı İnkübatör: Weightlab, Cole-Parmer Stuart
BOİ Ölçüm Cihazı: WTW, VELP
Hot Plate: VELP, Weightlab, Ohaus, Cole-Parmer Stuart
Rotatör Çalkalayıcı: VELP, Weightlab, Cole-Parmer Stuart
Su Banyoları: Weightlab
Su Banyosu: Weightlab
Ultrasonik Banyo: Weightlab
Santrifüjler: Weightlab
İnkübatörler: Weightlab, Cole-Parmer Stuart
Erime Noktası: Cole-Parmer Stuart
Polarimetreler: Bellingham + Stanley
```

Kategori sayfasında sadece o kategoriye bağlı markalar gösterilir.
Marka sayfasında sadece o markanın bağlı olduğu kategoriler gösterilir.
Sayaçlar ürün sayısına göre hesaplanır.

## Ürün ve Hizmet Eşleşmesi

SEO ve kullanıcı niyeti için ürün kategorileri kalibrasyon hizmetlerine bağlandı:

```text
Teraziler: Kütle & Terazi Kalibrasyonu
Nem Tayin: Kütle & Terazi Kalibrasyonu, Sıcaklık Kalibrasyonu
Kral Fischer: Hacim Kalibrasyonu, Sıcaklık Kalibrasyonu
Potansiyometrik Titratörler: Hacim Kalibrasyonu
Densitometre: Sıcaklık Kalibrasyonu, Hacim Kalibrasyonu
Refraktometre: Sıcaklık Kalibrasyonu
pH Metre: Sıcaklık Kalibrasyonu
pH & İletkenlik: Sıcaklık Kalibrasyonu
Viskozimetre: Devir Kalibrasyonu, Sıcaklık Kalibrasyonu
Etüv: Sıcaklık Kalibrasyonu
Balon Isıtıcılar: Sıcaklık Kalibrasyonu
Termoreaktör: Sıcaklık Kalibrasyonu
Homojenizatör: Devir Kalibrasyonu
Mekanik Karıştırıcı: Devir Kalibrasyonu
Manyetik Karıştırıcı: Devir Kalibrasyonu, Sıcaklık Kalibrasyonu
Karıştırıcılar: Devir Kalibrasyonu, Sıcaklık Kalibrasyonu
Isıtmalı Manyetik Karıştırıcı: Devir Kalibrasyonu, Sıcaklık Kalibrasyonu
Isıtmasız Manyetik Karıştırıcı: Devir Kalibrasyonu
Vorteks Karıştırıcılar: Devir Kalibrasyonu
Jar Test: Devir Kalibrasyonu
Diğer Çevre Cihazları: Devir Kalibrasyonu, Sıcaklık Kalibrasyonu
Soğutmalı İnkübatör: Sıcaklık Kalibrasyonu
Hot Plate: Sıcaklık Kalibrasyonu
Rotatör Çalkalayıcı: Devir Kalibrasyonu
Su Banyoları: Sıcaklık Kalibrasyonu
Su Banyosu: Sıcaklık Kalibrasyonu
Ultrasonik Banyo: Sıcaklık Kalibrasyonu
Santrifüjler: Devir Kalibrasyonu
İnkübatörler: Sıcaklık Kalibrasyonu
Erime Noktası: Sıcaklık Kalibrasyonu
```

Bu harita `config/mta.php -> product_category_services` içinde tutulur.

## Normalize Edilen Yazımlar

Kullanıcıdan gelen bazı yazımlar temiz isimlere bağlandı.

Örnekler:

```text
AND, AnD -> A&D
WEİGHTLAB, Weightlab Instruments -> Weightlab
OHAOUS -> Ohaus
KYOTE KEM -> Kyoto KEM
BELİNGHAMSTARLY -> Bellingham + Stanley
SI ANALATİK -> SI Analitik
TİTROLİNA7500KF -> TitroLine 7500 KF
BROKFİELD -> Brookfield
LAMİ -> Lamy
Densto Metre -> Densitometre
Retroktometre -> Refraktometre
Potensiyometre Titratör -> Potansiyometrik Titratörler
Nemtayin -> Nem Tayin
```

Bu alias mantığı şu an config içinde tutuluyor. Import scripti yazılırken bu aliaslar ürünleri doğru kategori/markaya eşlemek için kullanılmalıdır.

## Görseller

Logo:

```text
public/mta-logo.png
public/favicon.png
public/images/site/mta-endustri-logo-2.png
```

Ana logo kaynağı:

```text
C:\Users\serca\Desktop\MTA\mta-endustri-logo-2.png
```

Bu dosya `public/mta-logo.png` olarak ana logoya, `public/favicon.png` olarak favicon'a ve `public/images/site/mta-endustri-logo-2.png` olarak site asset klasörüne kopyalandı.

Hizmet görselleri:

```text
public/images/services/basinc-kalibrasyonu.webp
public/images/services/sicaklik-kalibrasyonu.jpg
public/images/services/tork-kalibrasyonu.png
public/images/services/devir-kalibrasyonu.webp
public/images/services/kutle-terazi-kalibrasyonu.webp
public/images/services/hacim-kalibrasyonu.webp
```

Bu görseller `C:\Users\serca\Desktop\MTA\Banner` klasöründen isim eşleştirmesiyle alındı. Hacim için net isimli görsel olmadığı için jenerik kalibrasyon iç görseli kullanıldı.

Teknik servis görselleri:

```text
public/images/technical-service/analiz-olcum-cihazlari-teknik-servis.webp
public/images/technical-service/laboratuvar-cihazlari-teknik-servis.webp
public/images/technical-service/terazi-teknik-servis.jpg
```

Marka logoları:

```text
public/images/brands/and.png
public/images/brands/bellingham-stanley.png
public/images/brands/kyoto-kem.png
public/images/brands/lamy.png
public/images/brands/mettler-toledo.png
public/images/brands/ohaus.png
public/images/brands/shimadzu.png
public/images/brands/velp.png
public/images/brands/weightlab.png
```

Bu logolar `C:\Users\serca\Desktop\MTA\Marka Logoları` klasöründen alındı ve `config/mta.php -> product_brands` içindeki ilgili markalara `logo` alanı olarak bağlandı. Logosu henüz bulunmayan markalar placeholder gösterir.

Ürün görselleri:

```text
public/images/products/
```

Kaynak:

```text
C:\Users\serca\Desktop\MTA\2025\2025\02
```

Bu klasörden `-150x150`, `-300x300`, `-600x600` gibi ölçeklendirilmiş WordPress türevleri dışarıda bırakılarak 366 orijinal ürün görseli kopyalandı. Mevcut örnek ürünlerden eşleşenler:

```text
public/images/products/fz-500i-hassas-terazi-0-001-g.jpg
public/images/products/mkc-710m-kral-fischer-1.jpg
public/images/products/velp-arex-6-digital-isitmali-manyetik-karistirici-1.jpg
```

Görsel davranışı:

- Ürün görsellerinde `object-fit: contain` kullanılır.
- Hizmet/banner görsellerinde `object-fit: cover` kullanılır.
- Görsel yoksa placeholder alanı gösterilir.

## WordPress XML Export İncelemesi

Dosya:

```text
C:\Users\serca\Downloads\mtaendstri.WordPress.2026-08-26.xml
```

Görülenler:

- WordPress/WooCommerce export dosyası.
- 509 ürün var.
- 493 ürün yayında.
- 16 ürün taslak.
- 394 medya/attachment kaydı var.
- Sayfa ve blog yazısı yok.
- Ürünlerde kategori, marka ve WooCommerce attribute verileri var.
- Yoast/Rank Math SEO meta verisi görünmedi.
- Çok fazla Woodmart/Elementor tema artığı var.
- Demo içerikler ve `demo.eticaretdukkani.com` bağlantıları temizlenmeli.

Önemli sonuç:

XML ürün kataloğu için iyi veri kaynağıdır, fakat direkt içeriği basılmamalıdır. Önce temizlenmeli, sonra kategori/marka ilişki haritasına göre import edilmelidir.

## SEO Yapısı

Mevcut SEO özellikleri:

- Her sayfa için title ve description.
- Canonical URL.
- Open Graph title, description, image.
- Twitter card.
- robots.txt dinamik route.
- sitemap.xml dinamik route.
- JSON-LD schema:
  - Organization
  - WebPage
  - BreadcrumbList
  - Service
  - Product
  - Article

Ürün schema içinde fiyat/offer/rating yoktur. Çünkü ürünler e-ticaret mantığında satılmıyor.

## İletişim ve Lead Mantığı

İletişim sayfası:

```text
resources/views/pages/contact.blade.php
```

Form route:

```text
POST /iletisim
```

Controller:

```text
SiteController::submitLead()
```

Şu an form sadece validation yapar ve success mesajı döner. Admin fazında lead kayıtları database'e yazılacak.

Planlanan lead alanları:

- Ad soyad
- Firma
- Telefon
- E-posta
- Mesaj
- Kaynak URL
- İlgili hizmet
- İlgili ürün
- UTM parametreleri
- Durum
- Notlar

## Admin Panel Planı

İkinci fazda Filament admin paneli öneriliyor.

Yönetilecek ana modüller:

- Hizmetler
- Ürünler
- Ürün kategorileri
- Markalar
- Kategori-marka ilişkileri
- Blog yazıları
- Blog kategorileri
- Sayfalar
- SEO alanları
- FAQ
- Görseller/dokümanlar
- Lead formları
- Trafik/analitik özetleri

Filament yerine hazır admin tema kullanılabilir, ancak Laravel tarafında veri yönetimi ve güvenlik için Filament daha hızlı ve düşük risklidir. Hazır admin tema daha sonra Filament ekranlarının CSS/HTML görünümüne ilham olarak kullanılabilir.

## Ortam Ayarları

`.env` içinde yerel geliştirme ayarları var.

`.env.example` MySQL hedefli olacak şekilde düzenlendi. cPanel canlıya geçerken şu alanlar gerçek bilgilerle doldurulacak:

```text
APP_URL=https://...
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Yerelde şu URL kullanılıyor:

```text
http://127.0.0.1:8000
```

## Kontrol Edilen Komutlar

Başarılı kontroller:

```text
php artisan test
npm run build
php -l config/mta.php
php -l app/Http/Controllers/SiteController.php
```

Canlı URL kontrolleri `200` döndü:

```text
/
/hizmetler/basinc-kalibrasyonu
/hizmetler/sicaklik-kalibrasyonu
/urunler
/urunler/teraziler
/urunler/teraziler?brand=shimadzu
/urunler/etuv
/urunler/marka/weightlab
/urunler/marka/kyoto-kem?category=refraktometre
/urunler/teraziler/and-fz-500i-hassas-terazi
/sitemap.xml
```

Not:

`npm run build` sırasında `fontaine` ile ilgili opsiyonel font fallback uyarısı geliyor. Build'i bozmuyor.

## İşlem Notları

2026-08-30:

- Yeni ürün çalışma kararı alındı: bundan sonra ürünler kategori bazlı, kullanıcının göndereceği tam kategori ürün setleri üzerinden eklenecek.
- Her kategori için ürünler topluca eklendikten sonra, o kategoride genel filtrelemede kullanılacak teknik alanlar ayrıca değerlendirilecek.
- Ürün detay sayfalarında kullanılacak teknik alanlar rakip ürün sayfalarında yaygın görülen alanlara göre belirlenecek; filtre/spec şeması kategori bazında netleştirilecek.
- Mevcut sitedeki ürünler kaldırıldı, ancak marka ve kategori yapısı korundu. Yerel SQLite veritabanında `products` tablosu 171 kayıttan 0 kayda indirildi; `product_brands` 14 ve `product_categories` 16 kayıtla bırakıldı.
- `SiteController::productsData()` güncellendi: `products` tablosu okunabiliyorsa ürün kaynağı olarak veritabanı esas alınır. Böylece veritabanı boşken eski `config/mta.php` örnek ürünleri veya `storage/app/imports/mta-products-normalized.json` import ürünleri otomatik olarak siteye geri düşmez. Config/import dosyaları tarihsel kaynak olarak durur, canlı ürün listesi DB üzerinden yeniden kurulacaktır.
- Kontrol: `php -l app\Http\Controllers\SiteController.php` temiz geçti. `php artisan test` sonucu 2 test / 2 başarılı.
- `Tekstür Analiz Cihazı` kategorisi eklendi. Slug: `/urunler/tekstur-analiz-cihazi`. Kategori Lamy markasıyla ilişkilendirildi; şimdilik doğrudan kalibrasyon hizmeti eşleşmesi boş bırakıldı.
- İlk tekstür analiz ürünü eklendi: `Lamy TX 700 - 250 N Tekstür Analiz Cihazı`. Slug: `/urunler/tekstur-analiz-cihazi/lamy-tx-700-250-n-tekstur-analiz-cihazi`. SKU: `LB.LMY.N151250`. Kaynak/rakip sayfada görülen teknik alanlar ürün detayına işlendi: sensör seçimi, seçili sensör, çözünürlük, hız, hassasiyet, hareket, ekran, sıcaklık, gerilim, dil, emniyet/gizlilik, PC bağlantısı, yazıcı bağlantısı, ölçüler ve ağırlık.
- Her yeni ürün eklemesinde önerilen SEO uyumlu ürün görseli dosya adı ayrıca belirtilecek. Ana görsel için dosya adı ürün slug'ıyla aynı gövdede olmalı; örnek: `lamy-tx-700-250-n-tekstur-analiz-cihazi.webp`.
- Tekstür analiz cihazı kategorisi için genel filtre adayları sonraki ürünler geldikten sonra netleştirilecek. İlk adaylar: kuvvet kapasitesi/sensör, çözünürlük, hız aralığı, hareket mesafesi, sıcaklık probu/aralığı, bağlantılar, ekran ve ağırlık.
- Kontrol: `/urunler/tekstur-analiz-cihazi`, `/urunler/tekstur-analiz-cihazi/lamy-tx-700-250-n-tekstur-analiz-cihazi` ve `/urunler/marka/lamy` yerelde 200 döndü. Ürün detay HTML çıktısında `250 N (25 kg)`, `0.001 N`, `0.1 - 10 mm/s`, `PT100`, `RS232` ve `22 kg` alanları doğrulandı. `php artisan test` sonucu 2 test / 2 başarılı.
- Ürün detay URL standardı değiştirildi: ürün sayfaları artık kategori yoluyla değil, tekil ürün slug'ı ile `/urun/{urun-slug}` formatında açılır. Örnek: `/urun/lamy-tx-700-250-n-tekstur-analiz-cihazi`. Yerel çalışıldığı için eski `/urunler/{kategori}/{urun-slug}` yolu için redirect eklenmedi.
- Ürün sayfa tasarımı sadeleştirildi: hero başlığının üstündeki marka/kategori eyebrow kaldırıldı; kısa açıklama altındaki marka/kategori/model/SKU kutucukları kaldırıldı; sağda sticky duran `Katalog ürünü` teklif paneli ürün detay şablonundan tamamen çıkarıldı.
- `Tekstür Analiz Cihazı`, `Viskozimetre` üst kategorisinin alt kategorisi olarak belirlendi. Viskozimetre kategori sayfası descendant kategori mantığıyla tekstür analiz ürünlerini de kapsar.
- İkinci tekstür analiz ürünü eklendi: `Lamy TX 700 - 500 N Tekstür Analiz Cihazı`. Ürün URL: `/urun/lamy-tx-700-500-n-tekstur-analiz-cihazi`. SKU: `LB.LMY.N151500`. Görsel dosyası: `public/images/products/lamy-tx-700-500-n-tekstur-analiz-cihazi.webp`; DB image yolu: `images/products/lamy-tx-700-500-n-tekstur-analiz-cihazi.webp`.
- Üçüncü tekstür analiz ürünü eklendi: `Lamy TX 700 - 50 N Tekstür Analiz Cihazı`. Ürün URL: `/urun/lamy-tx-700-50-n-tekstur-analiz-cihazi`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür. Önerilen görsel dosyası: `public/images/products/lamy-tx-700-50-n-tekstur-analiz-cihazi.webp`.
- Dördüncü tekstür analiz ürünü eklendi: `Lamy TX 700 - 10 N Tekstür Analiz Cihazı`. Ürün URL: `/urun/lamy-tx-700-10-n-tekstur-analiz-cihazi`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür. Önerilen görsel dosyası: `public/images/products/lamy-tx-700-10-n-tekstur-analiz-cihazi.webp`.
- Beşinci tekstür analiz ürünü eklendi: `Lamy TX 700 - 20 N Tekstür Analiz Cihazı`. Ürün URL: `/urun/lamy-tx-700-20-n-tekstur-analiz-cihazi`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür. Önerilen görsel dosyası: `public/images/products/lamy-tx-700-20-n-tekstur-analiz-cihazi.webp`.
- `tools/seed-tekstur-analiz-cihazi.php` beş TX 700 varyantını idempotent yönetecek ve görsel uzantısını `webp`, `jpg`, `jpeg`, `png` sırasıyla otomatik bulacak şekilde güncellendi.
- `Rotasyonel Viskozimetre`, `Viskozimetre` üst kategorisinin alt kategorisi olarak eklendi. Slug: `/urunler/rotasyonel-viskozimetre`. Kategori Lamy ve Brookfield markalarıyla ilişkilendirildi; ilgili hizmet eşleşmesi `devir-kalibrasyonu` ve `sicaklik-kalibrasyonu` olarak belirlendi.
- İlk rotasyonel viskozimetre ürünü eklendi: `Lamy First Plus Rotasyonel Viskozimetre R2-R7 Spindle Set`. Ürün URL: `/urun/lamy-first-plus-rotasyonel-viskozimetre-r2-r7-spindle-set`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür.
- Bu ürün için önerilen SEO görsel dosyası: `public/images/products/lamy-first-plus-rotasyonel-viskozimetre-r2-r7-spindle-set.webp`. Seed uzantıyı `webp`, `jpg`, `jpeg`, `png` sırasıyla otomatik bulur; dosya henüz bu adla klasörde olmadığı için DB image alanı şu anda boş kaldı.
- Lamy First Plus ürününde rakip/kaynak sayfadan gelen teknik alanlar işlendi: ölçüm prensibi, hız, tork aralığı, doğrusallık, tekrarlanabilirlik, viskozite ölçümü, spindle set, sıcaklık, ölçüm sistemi, sıcaklık kontrolü, PC bağlantısı, yazıcı bağlantısı, ekran, dijital gösterge, gerilim, dil, emniyet/gizlilik, ölçüler ve ağırlık.
- `tools/seed-rotasyonel-viskozimetre.php` eklendi; rotasyonel viskozimetre kategorisini, Lamy ilişki kaydını ve Lamy First Plus ürününü idempotent şekilde oluşturur/günceller.
- Kontrol: `/urun/lamy-first-plus-rotasyonel-viskozimetre-r2-r7-spindle-set`, `/urunler/rotasyonel-viskozimetre`, `/urunler/viskozimetre` ve `/sitemap.xml` yerelde 200 döndü. Ürün detay HTML çıktısında `Lamy First Plus Rotasyonel Viskozimetre`, `100 - 180.000.000 mPa.s` ve `0.3 - 250 rpm` doğrulandı; ürün detayında `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `php artisan test` sonucu 2 test / 2 başarılı.
- İkinci rotasyonel viskozimetre ürünü eklendi: `Lamy First Plus LR Rotasyonel Viskozimetre L1-L4 Spindle Set`. Ürün URL: `/urun/lamy-first-plus-lr-rotasyonel-viskozimetre-l1-l4-spindle-set`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür.
- Bu ürün için önerilen SEO görsel dosyası: `public/images/products/lamy-first-plus-lr-rotasyonel-viskozimetre-l1-l4-spindle-set.webp`. Görsel klasöründe bu adla dosya henüz yok; seed dosyası `webp`, `jpg`, `jpeg`, `png` sırasıyla otomatik bulacak.
- `SiteController::lamyFirstPlusRotationalViscometerSeoContent()` iki varyantı destekleyecek şekilde güncellendi: `r2-r7` ve `l1-l4`. Ayrıca özel ürün SEO içeriği olan sayfalarda `normalizeProductDetailContent()` artık özel H1/hero/spec içeriğini ezmez.
- Kontrol: `/urun/lamy-first-plus-lr-rotasyonel-viskozimetre-l1-l4-spindle-set`, `/urunler/rotasyonel-viskozimetre`, `/urunler/viskozimetre` ve `/sitemap.xml` yerelde 200 döndü. Ürün detay HTML çıktısında `Lamy First Plus LR Rotasyonel Viskozimetre L1-L4 Spindle Set`, `15 - 22.000.000 mPa.s` ve `0.3 - 250 rpm` doğrulandı; ürün detayında `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `php artisan test` sonucu 2 test / 2 başarılı.
- Üçüncü rotasyonel viskozimetre ürünü eklendi: `Lamy B-One Plus Rotasyonel Viskozimetre L1-L4 Spindle Set`. Ürün URL: `/urun/lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür.
- Bu ürün için önerilen SEO görsel dosyası: `public/images/products/lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set.webp`. Görsel klasöründe bu adla dosya henüz yok; seed dosyası `webp`, `jpg`, `jpeg`, `png` sırasıyla otomatik bulacak.
- Lamy B-One Plus ürününde rakip/kaynak sayfadan gelen teknik alanlar işlendi: ölçüm prensibi, hız, tork aralığı, doğrusallık, tekrarlanabilirlik, L1-L4/R2-R7/KU viskozite ölçüm aralıkları, spindle set, ekran, dijital gösterge, sonuç hafızası, USB veri transferi, gerilim, dil, emniyet/gizlilik, ölçüler ve ağırlık.
- `SiteController::lamyBOnePlusRotationalViscometerSeoContent()` eklendi. `tools/seed-rotasyonel-viskozimetre.php` artık üç rotasyonel viskozimetre ürününü idempotent şekilde yönetir.
- Kontrol: `/urun/lamy-b-one-plus-rotasyonel-viskozimetre-l1-l4-spindle-set`, `/urunler/rotasyonel-viskozimetre`, `/urunler/viskozimetre` ve `/sitemap.xml` yerelde 200 döndü. Ürün detay HTML çıktısında `Lamy B-One Plus Rotasyonel Viskozimetre L1-L4 Spindle Set`, `15 - 22.000.000 mPa.s`, `200 - 240.000.000 mPa.s`, `40-141 KU`, `Sonuç hafızası` ve `USB üzerinden veri transferi` doğrulandı; ürün detayında `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `php artisan test` sonucu 2 test / 2 başarılı.
- Ürün detay şablonuna video alanı eklendi. Video varsa `Ürün Videosu` bölümü YouTube privacy embed (`youtube-nocookie.com/embed/...`) ile gösterilir; video yoksa bölüm hiç çıkmaz. CSS tarafında `.product-video-list`, `.product-video-card` ve 16:9 `.product-video-frame` eklendi.
- Video eşleşmeleri: B-One Plus/B-One Touch ailesi için `N0AZOzy5ATo`; `Lamy RM100 Touch Taşınabilir Rotasyonel Viskozimetre` için `s8iVls57iKo`; `Lamy First Touch Rotasyonel Viskozimetre` için `V3Acy6tRzAo`.
- Rotasyonel viskozimetre kategorisine toplu eklenen yeni ürünler:
  - `Lamy B-One Plus Rotasyonel Viskozimetre KU 1-10 Spindle` URL: `/urun/lamy-b-one-plus-rotasyonel-viskozimetre-ku-1-10-spindle`; önerilen görsel: `public/images/products/lamy-b-one-plus-rotasyonel-viskozimetre-ku-1-10-spindle.webp`.
  - `Lamy B-One Plus Rotasyonel Viskozimetre R2-R7 Spindle Set` URL: `/urun/lamy-b-one-plus-rotasyonel-viskozimetre-r2-r7-spindle-set`; önerilen görsel: `public/images/products/lamy-b-one-plus-rotasyonel-viskozimetre-r2-r7-spindle-set.webp`.
  - `Lamy B-One Touch Taşınabilir Rotasyonel Viskozimetre` URL: `/urun/lamy-b-one-touch-tasinabilir-rotasyonel-viskozimetre`; önerilen görsel: `public/images/products/lamy-b-one-touch-tasinabilir-rotasyonel-viskozimetre.webp`.
  - `Lamy B-One Touch Rotasyonel Viskozimetre` URL: `/urun/lamy-b-one-touch-rotasyonel-viskozimetre`; önerilen görsel: `public/images/products/lamy-b-one-touch-rotasyonel-viskozimetre.webp`.
  - `Lamy RM100 Touch Taşınabilir Rotasyonel Viskozimetre` URL: `/urun/lamy-rm100-touch-tasinabilir-rotasyonel-viskozimetre`; önerilen görsel: `public/images/products/lamy-rm100-touch-tasinabilir-rotasyonel-viskozimetre.webp`.
  - `Lamy RM100 Touch Rotasyonel Viskozimetre` URL: `/urun/lamy-rm100-touch-rotasyonel-viskozimetre`; önerilen görsel: `public/images/products/lamy-rm100-touch-rotasyonel-viskozimetre.webp`.
  - `Lamy RM100 Touch CP 2000 Koni Plaka Viskozimetre` URL: `/urun/lamy-rm100-touch-cp-2000-koni-plaka-viskozimetre`; önerilen görsel: `public/images/products/lamy-rm100-touch-cp-2000-koni-plaka-viskozimetre.webp`.
  - `Lamy RM100 L Touch Viskozimetre` URL: `/urun/lamy-rm100-l-touch-viskozimetre`; önerilen görsel: `public/images/products/lamy-rm100-l-touch-viskozimetre.webp`.
  - `Lamy RM100 I Touch Endüstriyel Viskozimetre` URL: `/urun/lamy-rm100-i-touch-endustriyel-viskozimetre`; önerilen görsel: `public/images/products/lamy-rm100-i-touch-endustriyel-viskozimetre.webp`.
  - `Lamy First Prodig CP 1000 Rotasyonel Viskozimetre` URL: `/urun/lamy-first-prodig-cp-1000-rotasyonel-viskozimetre`; önerilen görsel: `public/images/products/lamy-first-prodig-cp-1000-rotasyonel-viskozimetre.webp`.
  - `Lamy RM100 Dokunmatik Jel Zamanlayıcı` URL: `/urun/lamy-rm100-dokunmatik-jel-zamanlayici`; önerilen görsel: `public/images/products/lamy-rm100-dokunmatik-jel-zamanlayici.webp`.
  - `Lamy First Touch Rotasyonel Viskozimetre` URL: `/urun/lamy-first-touch-rotasyonel-viskozimetre`; önerilen görsel: `public/images/products/lamy-first-touch-rotasyonel-viskozimetre.webp`.
- `tools/seed-rotasyonel-viskozimetre.php` artık 15 rotasyonel viskozimetre ürününü idempotent şekilde yönetir. Toplam yerel ürün sayısı 20 oldu: 5 tekstür analiz cihazı + 15 rotasyonel viskozimetre. Yeni rotasyonel ürün görselleri henüz önerilen slug'larla klasörde yok; bu yüzden DB image alanları boş ve sayfalar placeholder gösterir.
- Kontrol: yeni ürün detaylarının tamamı yerelde 200 döndü; örnek doğrulamalarda B-One KU için `40-141 KU`, B-One R2-R7 için `200 - 240.000.000 mPa.s`, RM100 taşınabilir için `40-250 KU`, First Touch için `3 - 180.000.000 mPa.s`, First Prodig için `3.000.000 - 80.000.000 mPa.s` ve RM100 jel zamanlayıcı için `100 - 5.000.000.000 mPa.s` görüldü. `/urunler/rotasyonel-viskozimetre` 15 ürün gösteriyor, `/urunler/viskozimetre` ve `/sitemap.xml` yeni ürünleri kapsıyor. `php artisan test` ve `npm run build` başarılı; build sırasında yalnızca mevcut opsiyonel `fontaine` uyarısı görüldü.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Isıtmalı Manyetik Karıştırıcılar` kategorisidir. Kategori slug'ı `/urunler/isitmali-manyetik-karistirici`; üst yapı config tarafında `Karıştırıcılar > Manyetik Karıştırıcılar > Isıtmalı Manyetik Karıştırıcılar` olarak çalışır.
- `VELP ARE Isıtmalı Manyetik Karıştırıcı` eklendi. Ürün URL: `/urun/velp-are-isitmali-manyetik-karistirici`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür. Önerilen SEO görsel dosyası: `public/images/products/velp-are-isitmali-manyetik-karistirici.webp`.
- ARE ürününde mevcut klasördeki `public/images/products/velp-are-isitmali-manyetik-karistirici-1.jpg` görseli yakalanıp DB image alanına `images/products/velp-are-isitmali-manyetik-karistirici-1.jpg` olarak bağlandı. Seed ideal slug'ı önce, ardından `-1` varyantını `webp`, `jpg`, `jpeg`, `png` sırasıyla arar.
- ARE ürününe `Katalog / Türkçe` dokümanı eklendi: `https://www.sentezgroup.com.tr/img/mc-content/20170717150725_2882velp_heating_magnetic_stirrers_are-arex_comparison_table.pdf`. Ürün detay şablonu artık DB dokümanlarında `url` veya `path` varsa indirilebilir/açılabilir bağlantı gösterir; eski string doküman alanlarıyla da uyumludur.
- ARE teknik alanları işlendi: cihaz tipi, ısıtma aralığı, karıştırma hacmi, karıştırma hızı, tabla malzemesi, tabla ölçüleri, gövde malzemesi, ağırlık, boyutlar, koruma sınıfı ve güç. Öne çıkan özelliklerde SpeedServo, PCM tipi tahrik mıknatısı, AluBlock aksesuar uyumu, LED alarm arayüzü ve IP 42 koruma bilgileri yer alır.
- Kontrol: `/urun/velp-are-isitmali-manyetik-karistirici`, `/urunler/isitmali-manyetik-karistirici`, `/urunler/manyetik-karistirici`, `/urunler/karistiricilar` ve `/sitemap.xml` yerelde 200 döndü. Ürün detay HTML çıktısında `370 °C`, `15 litre`, `1500 rpm`, `IP 42`, `630 W` ve katalog PDF URL'si doğrulandı; ürün detayında `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `php artisan test` sonucu 2 test / 2 başarılı.
- `VELP AREX Isıtmalı Manyetik Karıştırıcı` eklendi. Ürün URL: `/urun/velp-arex-isitmali-manyetik-karistirici`. SKU kullanıcıdan gelmediği için şimdilik boş bırakıldı; ön yüzde `Yayın öncesi netleştirilecek` olarak görünür. Önerilen SEO görsel dosyası: `public/images/products/velp-arex-isitmali-manyetik-karistirici.webp`.
- AREX ürününde mevcut klasördeki `public/images/products/velp-arex-isitmali-manyetik-karistirici-1.jpg` görseli yakalanıp DB image alanına `images/products/velp-arex-isitmali-manyetik-karistirici-1.jpg` olarak bağlandı.
- AREX ürününe `Katalog / Türkçe` dokümanı eklendi: `https://www.sentezgroup.com.tr/img/mc-content/20170717150739_2635velp_heating_magnetic_stirrers_are-arex_comparison_table.pdf`.
- AREX teknik alanları işlendi: cihaz tipi, ısıtma aralığı, karıştırma hacmi, karıştırma hızı, tabla malzemesi, tabla ölçüleri, gövde malzemesi, ağırlık, boyutlar, koruma sınıfı, güç ve opsiyonel aksesuar. Öne çıkan özelliklerde seramik kaplamalı tabla, SpeedServo, PCM tipi tahrik mıknatısı, AluBlock aksesuar uyumu, IP 42 koruma ve VTF Vertex dijital termoregülatör bağlantısı yer alır.
- Kontrol: `/urun/velp-arex-isitmali-manyetik-karistirici`, `/urunler/isitmali-manyetik-karistirici`, `/urunler/manyetik-karistirici` ve `/sitemap.xml` yerelde 200 döndü. Ürün detay HTML çıktısında `370 °C`, `20 litre`, `1500 rpm`, `Seramik kaplamalı`, `VTF Vertex`, `IP 42` ve katalog PDF URL'si doğrulandı; ürün detayında `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 22 oldu.
- `Isıtmalı Manyetik Karıştırıcılar` kategorisine 10 yeni VELP ürünü daha eklendi ve `tools/seed-isitmali-manyetik-karistirici.php` toplam 12 ürünü idempotent yönetecek hale geldi. Yeni eklenen ürünler:
  - `VELP AREX Digital Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-arex-digital-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-arex-digital-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-arex-digital-isitmali-manyetik-karistirici-1.jpg`; video: `naGhESpQXS4`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717145203_2916velp_heating_magnetic_stirrers_are-arex_comparison_table.pdf`.
  - `VELP HSC Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-hsc-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-hsc-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/hsc-isitmali-manyetik-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717150650_2747velp_heating_magnetic_stirrers_arec_comparison_table.pdf`.
  - `VELP AREC Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-arec-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-arec-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-arec-isitmali-manyetik-karistirici-1.jpeg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717150632_2904velp_heating_magnetic_stirrers_arec_comparison_table.pdf`.
  - `VELP AREC.X Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-arec-x-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-arec-x-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-arec-x-isitmali-manyetik-karistirici-1.jpg`; video: `ljX_nPC_S_Y`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717150950_2588velp_heating_magnetic_stirrers_arec_comparison_table.pdf`.
  - `VELP AREC.T Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-arec-t-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-arec-t-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-arec-t-isitmali-manyetik-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717151137_2534velp_heating_magnetic_stirrers_arec_comparison_table.pdf`.
  - `VELP AM4 Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-am4-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-am4-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-am4-isitmali-manyetik-karistirici-1.jpg`.
  - `VELP AM4X Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-am4x-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-am4x-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-am4x-isitmali-manyetik-karistirici-1.jpg`.
  - `VELP ARE-6 Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-are-6-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-are-6-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-are-6-isitmali-manyetik-karistirici-1.jpg`; video: `hmQJRTnwd6s`.
  - `VELP AREX-6 Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-arex-6-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-arex-6-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-arex-6-isitmali-manyetik-karistirici-1.jpeg`; video: `hmQJRTnwd6s`.
  - `VELP AREX-6 Digital Isıtmalı Manyetik Karıştırıcı` URL: `/urun/velp-arex-6-digital-isitmali-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-arex-6-digital-isitmali-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/velp-arex-6-digital-isitmali-manyetik-karistirici-1.jpg`; video: `hmQJRTnwd6s`.
- Kontrol: yeni ürün detaylarının tamamı yerelde 200 döndü; hepsinde ürün görseli bağlı, `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. AREX Digital video/PDF, AREC.X video/PDF ve AREX-6 Digital video/otomatik ters yönde karıştırma/prob algılama alanları ayrıca doğrulandı. `/urunler/isitmali-manyetik-karistirici` ve `/sitemap.xml` yeni ürünleri kapsıyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 32 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Isıtmasız Manyetik Karıştırıcılar` kategorisidir. Kategori slug'ı `/urunler/isitmasiz-manyetik-karistirici`; üst yapı config tarafında `Karıştırıcılar > Manyetik Karıştırıcılar > Isıtmasız Manyetik Karıştırıcılar` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `devir-kalibrasyonu` olarak gelir.
- `tools/seed-isitmasiz-manyetik-karistirici.php` eklendi ve 12 VELP ısıtmasız manyetik karıştırıcı ürünü idempotent yönetecek hale getirildi. Yeni ürünler:
  - `VELP MULTISTIRRER 6 Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-multistirrer-6-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-multistirrer-6-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/multistirrer-6-isitmasiz-manyetik-karistirici-1.webp`.
  - `VELP MULTISTIRRER 15 Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-multistirrer-15-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-multistirrer-15-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/multistirrer-15-isitmasiz-manyetik-karistirici-1.webp`.
  - `VELP MULTISTIRRER 6 Digital Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-multistirrer-6-digital-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-multistirrer-6-digital-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/multistirrer-6-digital-isitmasiz-manyetik-karistirici-1.webp`.
  - `VELP MULTISTIRRER 15 Digital Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-multistirrer-15-digital-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-multistirrer-15-digital-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/multistirrer-15-digital-isitmasiz-manyetik-karistirici-1.webp`.
  - `VELP AMI Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-ami-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-ami-isitmasiz-manyetik-karistirici.webp`; mevcut klasörde görsel bulunamadığı için ürün sayfası placeholder gösterir.
  - `VELP AMI 4 Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-ami-4-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-ami-4-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/ami-4-isitmasiz-manyetik-karistirici-1.jpg`.
  - `VELP MST Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-mst-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-mst-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/mst-isitmasiz-manyetik-karistirici-1.jpg`.
  - `VELP MST Digital Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-mst-digital-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-mst-digital-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/mst-digital-isitmasiz-manyetik-karistirici-1.jpg`.
  - `VELP MICROSTIRRER Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-microstirrer-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-microstirrer-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/micrositter-isitmasiz-manyetik-karistirici-1.jpg`.
  - `VELP ESP Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-esp-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-esp-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/esp-isitmasiz-manyetik-karistirici-1.jpg`.
  - `VELP AGE Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-age-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-age-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/age-isitmasiz-manyetik-karistirici-1.jpg`.
  - `VELP ATE Isıtmasız Manyetik Karıştırıcı` URL: `/urun/velp-ate-isitmasiz-manyetik-karistirici`; önerilen görsel: `public/images/products/velp-ate-isitmasiz-manyetik-karistirici.webp`; bağlı mevcut görsel: `images/products/ate-isitmasiz-manyetik-karistirici-1.jpg`.
- Isıtmasız ürünlerin tümüne kullanıcıdan gelen `Katalog / Türkçe` PDF bağlantıları ProductDocument olarak eklendi. Teknik alanlar kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, karıştırma hacmi, karıştırma hızı, tabla/gövde malzemesi, pozisyon sayısı, zamanlayıcı, ters yönde çalışma, boyut, ağırlık ve güç başlıklarıyla işlendi.
- Kontrol: 12 yeni ürün detayının tamamı yerelde 200 döndü; katalog bağlantıları görünüyor, AMI hariç ürün görselleri bağlı, `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `/urunler/isitmasiz-manyetik-karistirici`, `/urunler/manyetik-karistirici` ve `/sitemap.xml` yeni ürünleri kapsıyor. MULTISTIRRER 15 Digital için `1-900`, otomatik ters yönde çalışma ve `15 x 250 ml`; ATE için `25 litre H2O` ve `1200 rpm` doğrulandı. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 44 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Mekanik Karıştırıcılar` kategorisidir. Kategori slug'ı `/urunler/mekanik-karistirici`; üst yapı config tarafında `Karıştırıcılar > Mekanik Karıştırıcılar` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `devir-kalibrasyonu` olarak gelir.
- `tools/seed-mekanik-karistirici.php` eklendi ve şimdilik 6 VELP mekanik karıştırıcı ürünü idempotent yönetecek hale getirildi. Yeni ürünler:
  - `VELP ES Mekanik Karıştırıcı` URL: `/urun/velp-es-mekanik-karistirici`; önerilen görsel: `public/images/products/velp-es-mekanik-karistirici.webp`; bağlı mevcut görsel: `images/products/es-mekanik-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717154116_2974velp_overhead_stirrers_comparison_table.pdf`.
  - `VELP LS Mekanik Karıştırıcı` URL: `/urun/velp-ls-mekanik-karistirici`; önerilen görsel: `public/images/products/velp-ls-mekanik-karistirici.webp`; bağlı mevcut görsel: `images/products/ls-mekanik-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717154146_2743velp_overhead_stirrers_comparison_table.pdf`.
  - `VELP DLS Mekanik Karıştırıcı` URL: `/urun/velp-dls-mekanik-karistirici`; önerilen görsel: `public/images/products/velp-dls-mekanik-karistirici.webp`; bağlı mevcut görsel: `images/products/dls-mekanik-karistiricilar.jpg`; Türkçe katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170718081207_2501velp_overhead_stirrers_comparison_table.pdf`; İngilizce katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170718081214_2865velp_overhead_stirrers_dls_dijital.pdf`.
  - `VELP LH Mekanik Karıştırıcı` URL: `/urun/velp-lh-mekanik-karistirici`; önerilen görsel: `public/images/products/velp-lh-mekanik-karistirici.webp`; bağlı mevcut görsel: `images/products/lh-mekanik-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717154336_2578velp_overhead_stirrers_comparison_table.pdf`.
  - `VELP PW Mekanik Karıştırıcı` URL: `/urun/velp-pw-mekanik-karistirici`; önerilen görsel: `public/images/products/velp-pw-mekanik-karistirici.webp`; bağlı mevcut görsel: `images/products/pw-mekanik-karistirici-1.jpeg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717154524_2501velp_overhead_stirrers_comparison_table.pdf`.
  - `VELP DLH Mekanik Karıştırıcı` URL: `/urun/velp-dlh-mekanik-karistirici`; önerilen görsel: `public/images/products/velp-dlh-mekanik-karistirici.webp`; bağlı mevcut görsel: `images/products/dlh-mekanik-karistiricilar-1.jpg`; video: `3M2LT766gYo`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717154425_2692velp_overhead_stirrers_comparison_table.pdf`.
- Mekanik karıştırıcı teknik alanları kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, karıştırma hızı, gövde malzemesi, kullanılabilen şaft kalınlığı, maksimum viskozite, maksimum tork, çalışma sıcaklığı, karıştırma hacmi, ağırlık, boyutlar, güç, koruma sınıfı, LCD ekran ve zamanlayıcı başlıklarıyla işlendi.
- Kontrol: 6 mekanik ürün detayının tamamı yerelde 200 döndü; katalog bağlantıları ve ürün görselleri bağlı, `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. DLS için LCD, zamanlayıcı, İngilizce katalog ve `25000 mPa.s`; DLH için video, LCD, `40 litre`, `80 ncm`; PW için `100000 mPa.s`, `120 ncm`, `70 litre` doğrulandı. `/urunler/mekanik-karistirici` ve `/sitemap.xml` yeni ürünleri kapsıyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 50 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Hot Plate` kategorisidir. Kategori slug'ı `/urunler/hot-plate`; üst yapı config tarafında `Karıştırıcılar > Hot Plate` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `sicaklik-kalibrasyonu` olarak gelir.
- `tools/seed-hot-plate.php` eklendi ve 3 VELP hotplate / ısıtıcı tabla ürünü idempotent yönetecek hale getirildi. Yeni ürünler:
  - `VELP RC Isıtıcı Tabla Hotplate` URL: `/urun/velp-rc-isitici-tabla-hotplate`; önerilen görsel: `public/images/products/velp-rc-isitici-tabla-hotplate.webp`; bağlı mevcut görsel: `images/products/velp-rc-isitici-tabla-hotplate-1.avif`.
  - `VELP RC2 Isıtıcı Tabla Hotplate` URL: `/urun/velp-rc2-isitici-tabla-hotplate`; önerilen görsel: `public/images/products/velp-rc2-isitici-tabla-hotplate.webp`; bağlı mevcut görsel: `images/products/velp-rc2-isitici-tabla-hotplate-1.webp`.
  - `VELP REC Isıtıcı Tabla Hotplate` URL: `/urun/velp-rec-isitici-tabla-hotplate`; önerilen görsel: `public/images/products/velp-rec-isitici-tabla-hotplate.webp`; bağlı mevcut görsel: `images/products/velp-rec-isitici-tabla-hotplate-1.webp`.
- Kontrol: 3 hotplate ürün detayının tamamı yerelde 200 döndü; ürün görselleri bağlı, `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. RC için `370 °C` ve AVIF görsel; RC2 için `İkili` ısıtma pozisyonu ve `1200 W`; REC için `550 °C`, seramik tabla ve WEBP görsel doğrulandı. `/urunler/hot-plate` ve `/sitemap.xml` yeni ürünleri kapsıyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 53 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Vorteks Karıştırıcılar` kategorisidir. Kategori slug'ı `/urunler/vorteks-karistiricilar`; üst yapı config tarafında `Karıştırıcılar > Vorteks Karıştırıcılar` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `devir-kalibrasyonu` olarak gelir.
- `tools/seed-vorteks-karistirici.php` eklendi ve 6 VELP vorteks karıştırıcı ürünü idempotent yönetecek hale getirildi. Yeni ürünler:
  - `VELP RX3 Vorteks Karıştırıcı` URL: `/urun/velp-rx3-vorteks-karistirici`; önerilen görsel: `public/images/products/velp-rx3-vorteks-karistirici.webp`; bağlı mevcut görsel: `images/products/r3-vorteks-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717161242_2992velp_vortex_mixers_comparison_table.pdf`.
  - `VELP ZX3 Vorteks Karıştırıcı` URL: `/urun/velp-zx3-vorteks-karistirici`; önerilen görsel: `public/images/products/velp-zx3-vorteks-karistirici.webp`; bağlı mevcut görsel: `images/products/zx3-vorteks-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717161333_2885velp_vortex_mixers_comparison_table.pdf`.
  - `VELP ZX4 Vorteks Karıştırıcı` URL: `/urun/velp-zx4-vorteks-karistirici`; önerilen görsel: `public/images/products/velp-zx4-vorteks-karistirici.webp`; bağlı mevcut görsel: `images/products/zx4-vorteks-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717161408_2693velp_vortex_mixers_comparison_table.pdf`.
  - `VELP TX4 Vorteks Karıştırıcı` URL: `/urun/velp-tx4-vorteks-karistirici`; önerilen görsel: `public/images/products/velp-tx4-vorteks-karistirici.webp`; bağlı mevcut görsel: `images/products/tx4-votek-karistirici-1.jpg`; video: `HVSXHmHHDhs`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717161452_2969velp_vortex_mixers_comparison_table.pdf`.
  - `VELP CLASSIC Vorteks Karıştırıcı` URL: `/urun/velp-classic-vorteks-karistirici`; önerilen görsel: `public/images/products/velp-classic-vorteks-karistirici.webp`; bağlı mevcut görsel: `images/products/classic-vorteks-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717161521_2749velp_vortex_mixers_comparison_table.pdf`.
  - `VELP WIZARD Vorteks Karıştırıcı` URL: `/urun/velp-wizard-vorteks-karistirici`; önerilen görsel: `public/images/products/velp-wizard-vorteks-karistirici.webp`; bağlı mevcut görsel: `images/products/wizard-vorteks-karistirici-1.jpg`; katalog: `https://www.sentezgroup.com.tr/img/mc-content/20170717161550_2649velp_vortex_mixers_comparison_table.pdf`.
- Vorteks teknik alanları kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, orbital çap, karıştırma hızı, çalışma modu, gövde malzemesi, destek sistemi, ağırlık, boyut, koruma sınıfı, güç, LCD ekran ve zamanlayıcı başlıklarıyla işlendi.
- Kontrol: 6 vorteks ürün detayının tamamı yerelde 200 döndü; katalog bağlantıları ve ürün görselleri bağlı, `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. TX4 için video, LCD ve `999 saat 59 dakika`; RX3 için `3000 rpm sabit`; WIZARD için infrared sensörlü çalışma ve 3 ayak doğrulandı. `/urunler/vorteks-karistiricilar`, `/urunler/karistiricilar` ve `/sitemap.xml` yeni ürünleri kapsıyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 59 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Homojenizatör` kategorisidir. Kategori slug'ı `/urunler/homojenizator`; üst yapı config tarafında `Karıştırıcılar > Homojenizatör` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `devir-kalibrasyonu` olarak gelir.
- `tools/seed-homojenizator.php` eklendi ve `VELP OV5 Homojenizatör` ürünü idempotent yönetecek hale getirildi. Ürün URL: `/urun/velp-ov5-homojenizator`; önerilen görsel: `public/images/products/velp-ov5-homojenizator.webp`; bağlı mevcut görsel: `images/products/velp-ov5-homojenizator-cihazi-1.webp`; video: `D8yUsQ9m5vk`.
- Homojenizatör teknik alanları kategori filtresi için ileride kullanılabilecek şekilde karıştırma hızı aralığı, maksimum karıştırma hacmi, maksimum viskozite, gövde malzemesi, ağırlık, boyutlar ve güç başlıklarıyla işlendi.
- Kontrol: `/urun/velp-ov5-homojenizator`, `/urunler/homojenizator`, `/urunler/karistiricilar` ve `/sitemap.xml` yerelde doğrulandı. Ürün detayında `10000-30000 rpm`, `40 litre`, video embed ve WEBP ürün görseli görünüyor; `Katalog ürünü` paneli ve `product-meta-grid` kutucukları yok. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 60 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Jar Test` kategorisidir. Kategori slug'ı `/urunler/jar-test`; üst yapı config tarafında `Karıştırıcılar > Jar Test` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `devir-kalibrasyonu` olarak gelir.
- `tools/seed-jar-test.php` eklendi ve 5 VELP jar test / flokülatör ürünü idempotent yönetecek hale getirildi. Yeni ürünler:
  - `VELP JLT4 Jar Test Cihazı` URL: `/urun/velp-jlt4-jar-test-cihazi`; önerilen görsel: `public/images/products/velp-jlt4-jar-test-cihazi.webp`; bağlı mevcut görsel: `images/products/jlt4-flokulator-1.jpg`.
  - `VELP JLT6 Jar Test Cihazı` URL: `/urun/velp-jlt6-jar-test-cihazi`; önerilen görsel: `public/images/products/velp-jlt6-jar-test-cihazi.webp`; bağlı mevcut görsel: `images/products/jlt6-flokulator-1.jpg`.
  - `VELP FC4S Jar Test Cihazı` URL: `/urun/velp-fc4s-jar-test-cihazi`; önerilen görsel: `public/images/products/velp-fc4s-jar-test-cihazi.webp`; bağlı mevcut görsel: `images/products/fc4s-flokulator-1.jpg`.
  - `VELP FC6S Jar Test Cihazı` URL: `/urun/velp-fc6s-jar-test-cihazi`; önerilen görsel: `public/images/products/velp-fc6s-jar-test-cihazi.webp`; bağlı mevcut görsel: `images/products/fc6s-flokulator-1.jpg`.
  - `VELP FP4 Jar Test Cihazı` URL: `/urun/velp-fp4-portatif-jar-test-cihazi`; önerilen görsel: `public/images/products/velp-fp4-portatif-jar-test-cihazi.webp`; bağlı mevcut görsel: `images/products/fp4-flokulator-1.jpg`.
- Jar Test teknik alanları kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, karıştırma hızı, hız seçimi, zamanlayıcı, paslanmaz çelik karıştırma çubukları, motor tipi, gövde malzemesi, aydınlatma/arka panel, ağırlık, boyutlar ve güç başlıklarıyla işlendi.
- Kontrol: 5 jar test ürün detayının tamamı yerelde 200 döndü; ürün görselleri bağlı, `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. JLT4 için `10-300 rpm` ve `1 rpm`; FC6S için altılı analog yapı, `935 x 347 x 260 mm`, `23 W`; FP4 için portatif yapı ve `0-30 dk` zamanlayıcı doğrulandı. `/urunler/jar-test`, `/urunler/karistiricilar` ve `/sitemap.xml` yeni ürünleri kapsıyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 65 oldu.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Termoreaktör` kategorisidir. Kategori slug'ı `/urunler/termoreaktor`; üst yapı config tarafında `Karıştırıcılar > Termoreaktör` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `sicaklik-kalibrasyonu` olarak gelir.
- `tools/seed-termoreaktor.php` eklendi ve 4 VELP termoreaktör ürünü idempotent yönetecek hale getirildi: `/urun/velp-eco8-termoreaktor`, `/urun/velp-eco25-termoreaktor`, `/urun/velp-eco16-termoreaktor`, `/urun/velp-eco6-termoreaktor`. Önerilen görseller sırasıyla `public/images/products/velp-eco8-termoreaktor.webp`, `public/images/products/velp-eco25-termoreaktor.webp`, `public/images/products/velp-eco16-termoreaktor.webp`, `public/images/products/velp-eco6-termoreaktor.webp`; mevcut klasörde uygun VELP termoreaktör görseli bulunmadığı için sayfalar placeholder gösterir.
- Termoreaktör teknik alanları kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, kapasite, sıcaklık, zamanlayıcı, sıcaklık stabilitesi/homojenliği/doğruluğu, aşırı sıcaklık emniyeti, gövde malzemesi, ağırlık, boyutlar, güç ve LCD/program alanlarıyla işlendi.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `BOİ Ölçüm Cihazı` kategorisidir. Kategori slug'ı `/urunler/boi-olcum-cihazi`; üst yapı config tarafında `Karıştırıcılar > BOİ Ölçüm Cihazı` olarak çalışır.
- `tools/seed-boi-olcum-cihazi.php` eklendi ve 3 VELP BOİ/BOD ürünü idempotent yönetecek hale getirildi: `/urun/velp-bod-sensor-sistem-civasiz-boi-olcum-cihazi`, `/urun/velp-bod-evo-sensor-sistem-civasiz-kablosuz-boi-olcum-cihazi`, `/urun/velp-bms6-civali-manometrik-boi-olcum-cihazi`. Önerilen görseller sırasıyla `public/images/products/velp-bod-sensor-sistem-civasiz-boi-olcum-cihazi.webp`, `public/images/products/velp-bod-evo-sensor-sistem-civasiz-kablosuz-boi-olcum-cihazi.webp`, `public/images/products/velp-bms6-civali-manometrik-boi-olcum-cihazi.webp`; mevcut klasörde uygun ürün görseli bulunmadığı için sayfalar placeholder gösterir.
- BOİ teknik alanları kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, cihaz kapasitesi, numune kapasitesi, ölçüm değeri, ölçüm teknolojisi, veri hafızası, okuma yöntemi, tahmini skalalar, ekran, güvenlik sınıfı, elektronik koruma derecesi, ağırlık, boyutlar ve güç başlıklarıyla işlendi.
- Yeni aktif ürün akışı kullanıcı aksini söyleyene kadar `Soğutmalı İnkübatör` kategorisidir. Kategori slug'ı `/urunler/sogutmali-inkubator`; üst yapı config tarafında `Karıştırıcılar > Soğutmalı İnkübatör` olarak çalışır. İlgili hizmet eşleşmesi kategori haritasında `sicaklik-kalibrasyonu` olarak gelir.
- `tools/seed-sogutmali-inkubator.php` eklendi ve 6 VELP soğutmalı inkübatör ürünü idempotent yönetecek hale getirildi: `/urun/velp-ftc120-sogutmali-inkubator`, `/urun/velp-foc120e-sogutmali-inkubator`, `/urun/velp-foc120i-seffaf-ic-kapili-sogutmali-inkubator`, `/urun/velp-foc215e-sogutmali-inkubator`, `/urun/velp-foc215i-seffaf-ic-kapili-sogutmali-inkubator`, `/urun/velp-foc215il-seffaf-ic-kapili-aydinlatmali-sogutmali-inkubator`. Önerilen görseller ürün slug'larının `.webp` uzantılı halidir; mevcut klasörde uygun VELP FOC/FTC görseli bulunmadığı için sayfalar placeholder gösterir.
- Soğutmalı inkübatör teknik alanları kategori filtresi için ileride kullanılabilecek şekilde cihaz tipi, toplam hacim, BOİ cihaz kapasitesi, sıcaklık, iç sıcaklık kararlılığı/homojenliği, dijital gösterge, ölçüm teknolojisi, raf sayısı, dahili priz, multi soket, şeffaf iç kapı, aydınlatma sistemi, ışık akısı, ağırlık, boyutlar ve güç başlıklarıyla işlendi.
- Kontrol: termoreaktör, BOİ ve soğutmalı inkübatör ürünleri için örnek ürün detayları, kategori sayfaları, `/urunler/karistiricilar` üst kategorisi ve `/sitemap.xml` doğrulandı. ECO6 için `Ortam sıcaklığı - 200 °C`, `18 adet Ø16 mm`, `700 W`; BOD EVO için kablosuz yapı, `80 sensör`, `IP 54`; FOC215IL için `20.000 lux/raf`, LED ve `540 x 1300 x 550 mm` doğrulandı. `Katalog ürünü` paneli ve `product-meta-grid` kutucukları görünmüyor. `php artisan test` sonucu 2 test / 2 başarılı. Toplam yerel ürün sayısı 78 oldu.

2026-08-26:

- Laravel projesi kuruldu.
- cPanel/MySQL uyumlu mimari seçildi.
- MTA logosu public alana alındı.
- Renk sistemi logo renklerinden kurgulandı.
- Ana layout, header ve footer oluşturuldu.
- Ana sayfa ilk versiyon hazırlandı.
- Hizmet liste ve detay sayfaları oluşturuldu.
- Basınç Kalibrasyonu hizmet detay sayfası özel SEO brief'e göre gerçek hizmet içeriğiyle güncellendi.
- Ürün liste, kategori, marka ve detay sayfaları oluşturuldu.
- Ürünler katalog ve teklif talebi mantığında tasarlandı.
- Blog liste, kategori ve detay sayfaları oluşturuldu.
- Kurumsal, sertifikalar, referanslar, iletişim sayfaları oluşturuldu.
- Dinamik sitemap.xml ve robots.txt eklendi.
- JSON-LD schema yapısı eklendi.
- WordPress XML export incelendi ve ürün importu için veri kaynağı olarak değerlendirildi.
- Ürün kategori ve marka sözlükleri oluşturuldu.
- Kategori-marka ilişki haritası eklendi.
- Kullanıcıdan gelen marka/kategori yazım varyasyonları normalize edildi.
- Örnek ürün fotoğrafı `A&D FZ-500i Hassas Terazi` ürününe bağlandı.
- Örnek hizmet görseli `Basınç Kalibrasyonu` sayfasına bağlandı.
- Referans verilen ana sayfa ve hizmet detay görsellerine bakılarak ana sayfa ve hizmet detay tasarımı daha kurumsal şekilde yeniden kurgulandı.
- Bu handoff dosyası oluşturuldu.
- Kullanıcıdan gelen hacim, sıcaklık, basınç, kütle-terazi, tork ve frekans/devir kapsam listeleri ilgili hizmet sayfalarına `scope_groups` alanı ile işlendi.
- Hizmet detay şablonuna "Detaylı kapsam / Cihaz ve ölçüm aralığı listesi" bölümü eklendi.
- Ana logo `mta-endustri-logo-2.png` ile değiştirildi ve favicon olarak da aynı görsel kullanıldı.
- Hizmet görselleri `Banner` klasöründen eşleştirilip `public/images/services` altına alındı.
- Marka logoları `Marka Logoları` klasöründen eşleştirilip `public/images/brands` altına alındı ve marka sayfalarında gösterilecek şekilde bağlandı.
- Ürün görselleri `2025/2025/02` klasöründen ölçeklendirilmiş WordPress türevleri dışarıda bırakılarak `public/images/products` altına kopyalandı.
- Ürün kategori-hizmet eşleşmesi `product_category_services` haritası ile eklendi.
- Ürün detay sayfasına ilgili kalibrasyon hizmeti bilgi alanı, sağ panel hizmet linkleri ve Product schema içinde `isRelatedTo` bağlantısı eklendi.
- Header'a top header iletişim alanı ve sosyal medya ikon bağlantıları eklendi.
- Header menüsü mega menü yapısına geçirildi; Kalibrasyon, Teknik Servis ve Ürünler başlıkları açılır mega menü oldu.
- Header ve görünür blog sayfalarında "Bilgi Merkezi" dili "Blog" olarak değiştirildi.
- Teknik servis genel ve detay sayfaları eklendi.
- Hakkımızda sayfası kullanıcıdan gelen gerçek kurumsal metinle güncellendi.
- İletişim bilgileri site genelinde gerçek telefon, mail, fax ve adres bilgilerine bağlandı.
- Ürünler, ürün kategori ve ürün marka sayfalarındaki yatay filtre barları kaldırıldı; sol sidebar filtre paneli ve tek kolonlu ürün liste düzeni eklendi.
- Ana sayfa hero başlığı, destek metni, güven etiketleri, görsel alt metni ve hero görseli kalibrasyon + teknik servis + ürün kataloğu konumlandırmasına göre güncellendi.
- Site genelinde font Poppins'e alındı; top header sosyal medya kısaltma yazıları yerine ikonlar kullanılacak şekilde güncellendi ve top header/header hizası ortak `--site-container` değişkeniyle eşitlendi.
- Mega menülerin farklı yönlere açılmasına sebep olan özel sağ hizalama kuralı kaldırıldı; tüm mega menüler aynı yönde sağa doğru açılır.
- İç sayfa hero alanları logo/header çizgisine hizalandı, başlık boyutları ve dikey boşlukları düşürüldü.

2026-08-27:

- `app/Support/WpProductImport.php` eklendi; WordPress XML ürünlerini parça parça okuyup temizleyen import destek sınıfı oluşturuldu.
- `php artisan mta:import-products` komutu `routes/console.php` içine eklendi.
- Import tüm ürünleri çekmeyecek şekilde kurgulandı; sadece kullanıcıdan gelen kategori + marka izin haritasına uyan ürünler alınır.
- XML içindeki 509 ürün tarandı; 165 ürün temiz ürün JSON'una alındı, 344 ürün gerekçesiyle rapora yazıldı.
- XML içindeki demo/Woodmart/Elementor artığı açıklamalar güvenli özet metinlerle temizlenir hale getirildi.
- Yayında olmayan 16 ürün varsayılan import dışında bırakıldı.
- Kyoto KEM tarafında XML'de marka etiketi eksik ama model ailesi net olan Karl Fischer, densitometre ve refraktometre ürünleri kontrollü şekilde eşleştirildi.
- Ürün görselleri `_thumbnail_id`, medya dosya adı ve slug üzerinden `public/images/products` klasörüyle eşleştirildi.
- XML içindeki teknik özellik tabloları `specs` alanına ayrıştırıldı; ürün kartlarında tablo dökümü yerine kısa kurumsal özet gösterilecek hale getirildi.
- Ön yüzde ürün verisi `SiteController::productsData()` üzerinden okunacak hale getirildi; temiz import JSON'u varsa kategori, marka, ürün detay ve sitemap sayfalarına bağlanır.
- Ürün liste, kategori, marka, detay ve sitemap URL'leri lokal sunucuda 200 durum koduyla kontrol edildi.
- Ürün liste, kategori ve marka sayfalarına teknik özellik filtresi eklendi; `ozellik[...]` query parametresiyle kategori/marka rotası korunarak filtreleme yapılır.
- Örnek teknik filtre URL'si lokal sunucuda 200 durum kodu ve doğru sonuç listesiyle kontrol edildi.
- Filament v5.7.6 Composer ile kuruldu; admin panel provider dosyası `app/Providers/Filament/AdminPanelProvider.php` altında oluşturuldu.
- Filament `/admin` ve `/admin/login` rotaları oluştu; login sayfası lokal sunucuda 200 durum koduyla kontrol edildi.
- Filament ana rengi varsayılan Amber yerine site paletine daha yakın `Color::Teal` olarak ayarlandı.
- `database/migrations/2026_08_27_010000_create_mta_content_tables.php` eklendi.
- Migration içinde `services`, `technical_services`, `product_categories`, `product_brands`, kategori-marka pivotu, kategori-hizmet pivotu, `products`, `product_documents`, `pages`, `articles`, `faqs`, `leads`, `seo_entries` ve `redirects` tabloları oluşturuldu.
- Migration önce `php artisan migrate --pretend` ile kontrol edildi, sonra lokal SQLite veritabanında `php artisan migrate --force` ile başarıyla uygulandı.
- `Service`, `TechnicalService`, `ProductCategory`, `ProductBrand`, `Product`, `ProductDocument`, `Page`, `Article`, `Faq`, `Lead`, `SeoEntry` ve `Redirect` Eloquent modelleri eklendi.
- `php artisan mta:sync-content` komutu eklendi; statik config verisi ve temiz ürün import JSON'u database tablolarına aktarılır.
- Lokal database sync sonucu: 6 hizmet, 3 teknik servis, 16 kategori, 14 marka, 171 ürün, 2 blog yazısı ve 3 SSS kaydı oluştu.
- `SiteController` DB doluysa modellerden okuyacak, DB yoksa mevcut `config/mta.php` ve import JSON fallback'iyle çalışacak hale getirildi.
- DB geçişinden sonra ana sayfa, hizmet detay, ürün detay, blog ve admin login rotaları lokal sunucuda 200 durum koduyla kontrol edildi.
- `php artisan test` ve `npm run build` başarıyla çalıştırıldı.
- Ürün kataloğu, import JSON dosyası varsa sadece XML'den gelen ve kategori + marka haritasına uyan ürünleri yayınlayacak şekilde netleştirildi.
- Lokal DB'de ürün durumu güncellendi: XML kaynaklı 165 ürün `published`, önceki 6 manuel örnek ürün `draft`.
- İletişim sayfası kullanıcıdan gelen gerçek adres, telefon, mail ve fax bilgilerine göre yeniden düzenlendi.
- Sosyal medya linkleri gerçek LinkedIn, Instagram ve Facebook URL'leriyle güncellendi; gerçek URL verilmediği için YouTube bağlantısı kaldırıldı.
- Sertifika sorgulama şu an kapsam dışı bırakıldı; yalnızca `/sertifikalar` sayfası korunacak ve belge/PDF listesi geldiğinde burada yayınlanacak.
- `MTA_SITE_BRIEF.md` dosyası oluşturuldu; firma, web sitesi, hizmetler, teknik servisler, ürün kataloğu, SEO yaklaşımı, görsel kimlik ve mevcut durum tek dokümanda özetlendi.
- Ana sayfa SEO içerikleri güncellendi: `/` meta title, meta description, H1, hedef H2 başlıkları, hero metni, CTA metinleri ve ana görsel alt textleri verilen brief'e göre düzenlendi.
- Ana sayfa bölüm metinleri ikinci SEO brief'e göre tamamlandı; `/hizmetler`, `/hizmetler/kutle-terazi-kalibrasyonu`, `/hizmetler/sicaklik-kalibrasyonu`, `/teknik-servis`, `/urunler`, `/urunler/teraziler` ve `/iletisim` iç linkleri canlı HTML çıktısında doğrulandı.
- `/hizmetler` ana kalibrasyon hizmetleri liste sayfası için özel SEO landing yapısı eklendi: kalibrasyon hizmetleri odaklı meta title/description, H1, hero açıklaması, CTA'lar, endüstriyel kalibrasyon metin blokları, hizmet kart açıklamaları, cihaz kapsam listesi, süreç adımları, teknik servis/ürün kataloğu/iletişim iç linkleri, SSS ve ana hizmet görsel alt texti doğrulandı. `mta-kalibrasyon-banner-10.webp` görseli `public/images/services` altına kopyalandı.
- `/hizmetler/basinc-kalibrasyonu` hizmet detay sayfası için özel SEO içerik yapısı eklendi: basınç kalibrasyonu ve manometre kalibrasyonu odaklı meta title/description, H1, hero açıklaması, CTA'lar, hizmet metin blokları, cihaz kapsam listesi, basınç aralığı kapsam tablosu, süreç adımları, analiz ve ölçüm cihazları teknik servis/kalibrasyon hizmetleri/iletişim iç linkleri, SSS ve hizmet görsel alt texti doğrulandı. Basınç hizmetinde ilgili ürün olmadığı için ürün liste bloğu boş kategoride gösterilmeyecek şekilde şablon güvenli hale getirildi.
- `/hizmetler/devir-kalibrasyonu` hizmet detay sayfası için özel SEO içerik yapısı eklendi: devir kalibrasyonu, takometre kalibrasyonu ve rpm kalibrasyonu odaklı meta title/description, H1, hero açıklaması, CTA'lar, cihaz kapsam listesi, devir aralığı kapsam tablosu, süreç adımları, manyetik karıştırıcı/mekanik karıştırıcı/homojenizatör/viskozimetre/laboratuvar cihazları teknik servis/iletişim iç linkleri, SSS ve hizmet görsel alt texti doğrulandı.
- `/hizmetler/tork-kalibrasyonu` hizmet detay sayfası için özel SEO içerik yapısı eklendi: tork kalibrasyonu, tork anahtarı kalibrasyonu, torkmetre ve tork ölçer odaklı meta title/description, H1, hero açıklaması, CTA'lar, ekipman kapsam listesi, tork aralığı kapsam tablosu, süreç adımları, kalibrasyon hizmetleri/iletişim iç linkleri, SSS ve hizmet görsel alt texti doğrulandı. Tork hizmetinde ilgili ürün olmadığı için ürün liste bloğu boş kategoride gösterilmeyecek şekilde çalışır.
- `/teknik-servis/laboratuvar-cihazlari-icin-teknik-servis` teknik servis detay sayfası için özel SEO içerik yapısı eklendi: laboratuvar cihazları teknik servis ve bakım odaklı meta title/description, H1, hero açıklaması, CTA'lar, cihaz grupları, arıza listesi, bakım-onarım süreci, sıcaklık kontrollü cihaz/karıştırıcı/ölçüm cihazı destek blokları, ürün ve kalibrasyon iç linkleri, SSS ve teknik servis görsel alt texti doğrulandı.
- `/teknik-servis/analiz-ve-olcum-cihazlari-teknik-servis` teknik servis detay sayfası için özel SEO içerik yapısı eklendi: analiz cihazları teknik servis ve ölçüm cihazları teknik servis odaklı meta title/description, H1, hero açıklaması, CTA'lar, cihaz grupları, arıza listesi, prob/sensör/elektrot kontrolü, pH/iletkenlik/refraktometre/densitometre/viskozimetre/titratör destek blokları, ürün ve kalibrasyon iç linkleri, SSS ve teknik servis görsel alt texti doğrulandı.
- `/urunler/teraziler` kategori sayfası için özel SEO içerik yapısı eklendi: hassas terazi odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, marka linkleri, terazi kalibrasyonu/teknik servis/iletişim iç linkleri ve kategoriye özel ürün/marka alt textleri doğrulandı.
- `/hizmetler/kutle-terazi-kalibrasyonu` hizmet detay sayfası için özel SEO içerik yapısı eklendi: terazi kalibrasyonu odaklı meta title/description, H1, hero açıklaması, CTA'lar, hizmet metin blokları, kapsam tablosu, süreç adımları, teknik servis/ürün/marka/iletişim iç linkleri, SSS ve hizmet görsel alt texti doğrulandı.
- `/hizmetler/sicaklik-kalibrasyonu` hizmet detay sayfası için özel SEO içerik yapısı eklendi: sıcaklık kalibrasyonu odaklı meta title/description, H1, hero açıklaması, CTA'lar, hizmet metin blokları, kapsam tablosu, süreç adımları, etüv/nem tayin/pH metre/refraktometre/laboratuvar cihazları teknik servis iç linkleri, SSS ve hizmet görsel alt texti doğrulandı.
- `/hizmetler/hacim-kalibrasyonu` hizmet detay sayfası için özel SEO içerik yapısı eklendi: hacim kalibrasyonu ve pipet kalibrasyonu odaklı meta title/description, H1, hero açıklaması, CTA'lar, hizmet metin blokları, kapsam tablosu, süreç adımları, Karl Fischer/potansiyometrik titratör/densitometre/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis iç linkleri, SSS ve hizmet görsel alt texti doğrulandı.
- `/teknik-servis/terazi-teknik-servis` teknik servis detay sayfası için özel SEO içerik yapısı eklendi: terazi teknik servis odaklı meta title/description, H1, hero açıklaması, CTA'lar, arıza listesi, bakım-onarım süreci, kalibrasyon ilişkisi, ürün/marka/iletişim iç linkleri, SSS ve teknik servis görsel alt texti doğrulandı.
- `/urunler/ph-metre` kategori sayfası için özel SEO içerik yapısı eklendi: pH metre odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, Mettler Toledo/Ohaus/WTW marka linkleri, pH & iletkenlik/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/iletişim iç linkleri, SSS ve marka logo alt textleri doğrulandı. XML importunda bu kategoride yayındaki ürün olmadığı için ürün listesi şimdilik boş durum ekranıyla çalışır.
- `/urunler/ph-iletkenlik` kategori sayfası için özel SEO içerik yapısı eklendi: iletkenlik ölçer ve pH & iletkenlik odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, WTW/Mettler Toledo/Ohaus marka linkleri, pH metre/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 59 ürün yayındadır.
- `/urunler/densitometre` kategori sayfası için özel SEO içerik yapısı eklendi: densitometre, yoğunluk ölçer ve özgül ağırlık ölçer odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, Kyoto KEM/Mettler Toledo/Bellingham + Stanley marka linkleri, refraktometre/sıcaklık kalibrasyonu/hacim kalibrasyonu/laboratuvar cihazları teknik servis/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 8 ürün yayındadır.
- `/urunler/kral-fischer` kategori sayfası için özel SEO içerik yapısı eklendi: Karl Fischer titratör ve su miktarı tayin cihazları odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, Kyoto KEM/Mettler Toledo/SI Analitik/TitroLine 7500 KF marka linkleri, hacim kalibrasyonu/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/nem tayin/potansiyometrik titratör/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 7 ürün yayındadır.
- `/urunler/potansiyometrik-titratorler` kategori sayfası için özel SEO içerik yapısı eklendi: potansiyometrik titratör ve otomatik titrasyon cihazı odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, Mettler Toledo/Kyoto KEM/SI Analitik marka linkleri, hacim kalibrasyonu/laboratuvar cihazları teknik servis/Karl Fischer/pH metre/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 3 ürün yayındadır; SI Analitik logo dosyası kaynakta bulunmadığı için şimdilik metin fallback çalışır.
- `/urunler/refraktometre` kategori sayfası için özel SEO içerik yapısı eklendi: refraktometre odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, Kyoto KEM/Mettler Toledo/Bellingham + Stanley marka linkleri, sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 4 Kyoto KEM ürünü yayındadır.
- `/urunler/manyetik-karistirici` kategori sayfası için özel SEO içerik yapısı eklendi: manyetik karıştırıcı odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, VELP/Weightlab marka linkleri, devir kalibrasyonu/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/mekanik karıştırıcı/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 15 ürün yayındadır.
- `/urunler/homojenizator` kategori sayfası için özel SEO içerik yapısı eklendi: homojenizatör odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, VELP/Weightlab marka linkleri, devir kalibrasyonu/laboratuvar cihazları teknik servis/mekanik karıştırıcı/manyetik karıştırıcı/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 1 ürün yayındadır.
- `/urunler/viskozimetre` kategori sayfası için özel SEO içerik yapısı eklendi: viskozimetre odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, Brookfield/Lamy marka linkleri, devir kalibrasyonu/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 6 ürün yayındadır; Brookfield logo dosyası kaynak klasörde bulunmadığı için şimdilik metin fallback çalışır.
- `/urunler/nem-tayin` kategori sayfası için özel SEO içerik yapısı eklendi: nem tayin cihazı odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, A&D/Ohaus/Shimadzu/Weightlab marka linkleri, kütle ve terazi kalibrasyonu/sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/hassas terazi/iletişim iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 3 ürün yayındadır.
- `/urunler/etuv` kategori sayfası için özel SEO içerik yapısı eklendi: etüv cihazı odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, Weightlab marka linki, sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/nem tayin iç linkleri, SSS ve ürün/marka alt textleri doğrulandı. XML importunda bu kategoride 4 Weightlab ürünü (Memmert UN1060, WF-HT45, WF-HT65, WF-HT125) yayındadır.
- `/urunler/termoreaktor` kategori sayfası için özel SEO içerik yapısı eklendi: termoreaktör, laboratuvar termoreaktör ve laboratuvar sindirim cihazı odaklı meta title/description, H1, hero açıklaması, CTA'lar, kategori metin blokları, kullanım alanları listesi, VELP marka linki, sıcaklık kalibrasyonu/laboratuvar cihazları teknik servis/balon ısıtıcı/manyetik karıştırıcı/iletişim iç linkleri, SSS ve marka alt texti doğrulandı. XML importunda bu kategoride şu an yayındaki ürün yoktur; boş durum ekranı çalışır ve ürün eklendiğinde termoreaktör ürün alt texti otomatik üretilecektir.
- `/teknik-servis` ana teknik servis sayfası özel SEO brief'e göre yeniden yapılandırıldı: meta title/description, H1, hero, teknik servis kartları, kapsam listesi, süreç adımları, ürün/kalibrasyon iç linkleri, SSS ve CTA alanları eklendi.
- `/urunler` ana ürün katalog sayfası özel SEO brief'e göre genişletildi: katalog konumlandırması, kategori kartları, marka bölümü, sol filtreli ürün listesi, seçim kriterleri, ürün-hizmet/teknik servis iç linkleri, SSS ve CTA alanları eklendi.
- `/hakkimizda`, `/iletisim`, `/sertifikalar` ve `/referanslar` sayfaları verilen brief'lere göre SEO uyumlu kurumsal içeriklerle güncellendi. Sertifikalarda sorgulama modülü eklenmedi; referanslarda gerçek olmayan müşteri adı veya logo üretilmedi.
- `/markalar` route'u ve marka listeleme sayfası eklendi. Laboratuvar cihazları markaları odaklı meta/H1, marka kartları, kategori ilişkileri, seçim kriterleri, iç linkler, SSS ve marka bazlı teklif CTA alanı hazırlandı.
- `/urunler/marka/and` sayfası A&D hassas terazi ve laboratuvar tartım cihazları brief'ine göre özel meta/H1/hero, marka açıklamaları, ürün listesi, terazi kalibrasyonu ve terazi teknik servis iç linkleriyle güncellendi.
- `/urunler/teraziler/and-fz-500i-hassas-terazi` özel ürün detay sayfası brief'e göre hazırlandı: meta title/description, H1, hero açıklaması, 520 g kapasite ve 0.001 g okunabilirlik teknik tablosu, ilgili terazi kalibrasyonu/teknik servis/marka/kategori iç linkleri, SSS ve ürün teklif CTA alanı eklendi.
- Ürün veri okuma sırası güncellendi: veritabanı ürünleri aktif olsa bile `config/mta.php` içindeki özel örnek ürünler korunur, ardından XML import ürünleri eklenir.
- `/bilgi-merkezi` sayfası teknik kütüphane mantığıyla SEO brief'e göre güncellendi; `/blog` ayrı yayın akışı sayfası olarak eklendi.
- `/bilgi-merkezi/kategori/kalibrasyon-rehberleri` kategori sayfası kalibrasyon rehberleri odaklı meta/H1/hero, içerik kapsam listesi, ilgili kalibrasyon hizmet linkleri, destek linkleri ve CTA alanıyla güncellendi.
- `/bilgi-merkezi/basinc-kalibrasyonu-nedir` rehber detayı özel SEO brief'e göre dolduruldu: gerçek H2 bölümleri, cihaz listesi, süreç adımları, iç linkler ve teklif sidebar'ı eklendi.
- Sitemap'e `/markalar` ve `/blog` eklendi; header menüsüne Markalar bağlantısı eklendi ve Blog bağlantısı yeni `/blog` route'una taşındı.

2026-08-29:

- Tasarım fazında ortak sistemler gözden geçirildi: header/footer, hero varyantları, section, card, CTA, ürün kartı, article kartı, katalog filtreleri, SSS ve süreç gridleri aynı CSS sistemi altında belgelenerek sayfa sayfa tasarım ilerleyişi için referans alındı.
- Site font sistemi netleştirildi: tüm sitede `Poppins` kullanılır; body `line-height: 1.6`, H1/H2/H3 boyutları `resources/css/app.css` içindeki hero ve section kurallarından gelir.
- Site genişlik sistemi netleştirildi: ana container `--site-container: min(100% - 2rem, 1180px)`, mobilde `min(100% - 1.25rem, 1180px)`, dar içeriklerde `.narrow` maksimum 900px kullanır.
- Hizmetler ve teknik servis hero ailesinde H1 üst sınırı 54px'e çekildi: `.service-landing-hero h1` artık `clamp(2.25rem, 4.2vw, 3.375rem)` kullanır.
- Hizmet/teknik servis hero görsellerindeki kapsam chip'i cam efekti, daha güçlü border, teal sol vurgu çizgisi ve yüksek metin kontrastıyla iyileştirildi.
- Hizmetler, hizmet detayları, teknik servis ana sayfası ve teknik servis detaylarındaki hero üst rozetleri kaldırıldı; breadcrumb sonrası doğrudan H1 gelir.
- Header mega menü sistemi yeniden düzenlendi: Kalibrasyon, Teknik Servis ve Ürünler menüleri header container içinde ortalı açılır, `1120px` altında mobil menüye geçilir.
- Mega menü davranışı `resources/js/app.js` ile güçlendirildi: dışarı tıklama, `Escape`, focus çıkışı ve touch cihazlarda tıklama/toggle kapanışları yönetilir.
- Kalibrasyon ve Teknik Servis mega menülerine okunur badge, küçük görsel ve `Tüm hizmetler` butonu eklendi; alt menü itemlerinde `İncele` metni ve sağ ok gösterilir.
- Ürünler mega menüsünde sol intro/görsel alanı kaldırıldı; kategori itemleri 4 kolon dizilir. Alt kategori kırılımları mega menüde gösterilmez. `Tüm ürünler` aksiyonu menü grid'inin sağ alt hücresinde küçük ve tek satırlık link-button olarak konumlandırıldı.
- Header menüsünden `Markalar` kaldırıldı. Ürünler sonrası masaüstü ve mobil sıralama `Kurumsal - Sertifikalar - Blog - İletişim` olarak düzenlendi.
- `/urunler` sayfasındaki kategori kartları görselli hale getirildi. `SiteController::productCategories()` her kategori için ilk uygun ürün görselini `image`/`image_alt` olarak üretir.
- `/urunler` kategori kartlarında ürün sayısı kaldırıldı; kart aksiyonu sağ altta `İncele` metni ve sağ ok ile gösterilir.
- `/urunler` ana sayfasından hero altındaki iki kolonlu `Laboratuvar Cihazları Kataloğu / Ürünler Nasıl Listelenir?` SEO section'ı kaldırıldı.
- `/urunler` ana sayfasındaki filtreli tüm ürün listesi ve sol filtre paneli kaldırıldı. Ana ürünler sayfası kategori/marka yönlendirme ve destek içerikleri odaklı kalır.
- `MTA_SON_DURUM_SEO_TEXTLERI.md` dosyasındaki son SEO metinleri siteye uygulandı. Belgedeki metinler içerik kaynağı olarak ele alındı; belge içindeki uyarı ve yönergeler kullanıcı talimatı gibi çalıştırılmadı.
- `app/Http/Controllers/SiteController.php` içinde hizmet, teknik servis, ürün kategori, marka ve ürün detay sayfaları için son SEO metinlerini normalize eden yardımcı akışlar eklendi: `normalizeServiceSeoContent()`, `normalizeTechnicalServiceSeoContent()`, `normalizeProductCategorySeoContent()`, `normalizeBrandSeoContent()`, `normalizeProductDetailContent()` ve `normalizeProductDetailSeoContent()`.
- `/hizmetler` ve hizmet detaylarında meta title, meta description, H1, hero metni, kapsam chip'i, kapsam tabloları, süreç/CTA/SSS metinleri son dokümana göre güncellendi.
- Belgede doğrulama gerektirdiği belirtilen basınç, sıcaklık, kütle/terazi, hacim, tork ve devir teknik aralıkları sitede kesin iddia olarak bırakılmadı. Görünür içerik ve yedek veri kaynaklarında bu alanlar `Belgeyle doğrulanacak` biçimine çekildi.
- `config/mta.php` içindeki hizmet kapasite ve detaylı kapsam satırlarında eski kesin teknik aralıklar kaldırıldı; ileride fallback içerik devreye girse bile doğrulanmamış aralıklar sitede görünmeyecek.
- `/teknik-servis` ve teknik servis detay sayfalarında özellikle `Laboratuvar Cihazları Teknik Servis`, analiz/ölçüm cihazları teknik servis ve terazi teknik servis metinleri son dokümana göre güncellendi.
- Ürün kategori sayfaları için dosyadaki final başlık/H1/CTA seti merkezi olarak uygulandı. `mekanik-karistirici` kategorisi `noindex,follow` olacak şekilde işaretlendi; layout zaten `$meta['robots']` değerini `<meta name="robots">` çıktısına basar. `termal-analiz` ise son kategori revizyonunda tamamen gizlenip kaldırıldı.
- `/urunler` ana katalog CTA metinleri son dokümana göre `Ürün Kataloğunu İncele` odağına çekildi.
- Marka listeleme ve marka detay sayfalarında resmi marka ilişkisi, bayilik, distribütörlük veya servis yetkisi gibi belge gerektiren iddialar üretilmeyecek şekilde metinler nötrleştirildi. Arama kontrolünde yasaklı marka iddiası ifadelerinin site kaynaklarında kalmadığı doğrulandı.
- Ürün detay sayfaları için sahte fiyat, stok, değerlendirme veya gereksiz uzun açıklama üretmeyen kısa ürün açıklaması ve `[Marka] [Model] [Ürün Tipi] | MTA Endüstri` başlık formatı merkezi olarak uygulanır hale getirildi.
- `/referanslar` sayfası son dokümandaki `Referanslar ve Uygulama Alanları` odağına göre meta/H1/hero metniyle güncellendi; gerçek olmayan müşteri adı veya logo üretmeme kararı korunur.
- Ürün kategori sözlüğü kullanıcı isteğine göre güncellendi: `Karıştırıcılar`, `Su Banyoları`, `Santrifüjler`, `İnkübatörler`, `Erime Noktası` ve `Polarimetreler` ana ürün kategorileri olarak eklendi/korundu; ayrı `pH Metre` mega menüden kaldırıldı ve görünür ana başlık `pH İletkenlik & Metreler` olarak kullanılır.
- `Karıştırıcılar` altında veri hiyerarşisi oluşturuldu: `Manyetik Karıştırıcılar` ana alt kategori, onun altında `Isıtmalı Manyetik Karıştırıcılar` ve `Isıtmasız Manyetik Karıştırıcılar`; ayrıca `Mekanik Karıştırıcılar`, `Vorteks Karıştırıcılar`, `Homojenizatör`, `Termoreaktör`, `Jar Test`, `Diğer Çevre Cihazları`, `Soğutmalı İnkübatör`, `BOİ Ölçüm Cihazı`, `Hot Plate` ve `Rotatör Çalkalayıcı` alt kategori olarak tanımlandı.
- `Teraziler` altında `Analitik Teraziler`, `Hassas Teraziler`, `Endüstriyel Teraziler` ve `Mikro Teraziler` alt kategorileri; `Su Banyoları` altında `Su Banyosu` ve `Ultrasonik Banyo` alt kategorileri tanımlandı.
- Parent kategori sayfaları kendi alt kategori ürünlerini de kapsayacak şekilde genişletildi: `SiteController::productCategoryAndDescendantSlugs()` eklendi; kategori ürün listesi ve kategori marka sayaçları descendant slug'ları da dikkate alır.
- `Termal Analiz` kategorisi kullanıcı isteğiyle kaldırıldı/gizlendi; mevcut `Erime Noktası` kategorisi korunur. `termal-analiz` slug'ı ürün kategori akışında gizlenir ve yerel kontrolde `/urunler/termal-analiz` 404 döner.
- Teknik Servis altına `Tork Anahtarları Servisi` eklendi. Slug: `/teknik-servis/tork-anahtarlari-servisi`. Veritabanı teknik servisleri aktif olsa bile config'teki eksik teknik servis kayıtları listeye eklenir; böylece yeni servis mega menü ve sitemap akışında görünür.
- Son menü doğrulaması: Ürünler mega menüsünde ana kategoriler görünür, alt kategoriler görünmez; `Erime Noktası` görünür, `Termal Analiz` görünmez. Teknik Servis mega menüsünde `Tork Anahtarları Servisi` görünür. `Tüm ürünler` butonu tek satır kalacak şekilde CSS ile sabitlendi.
- Son kontrol: `php -l app\Http\Controllers\SiteController.php`, `php -l config\mta.php`, `php -l resources\views\pages\service-detail.blade.php` temiz geçti. `php artisan test` sonucu 2 test / 2 başarılı.
- SEO içerik üretim akışı için yeni çalışma kararı alındı: kalan sayfaları topluca üretmeden önce temsili şablon sayfalar tasarım onayına sunulacak. Öncelikli şablonlar: bir ürün detay sayfası (`/urunler/teraziler/and-fz-500i-hassas-terazi`), bir marka detay sayfası (`/urunler/marka/and`), Bilgi Merkezi ana sayfası (`/bilgi-merkezi`), Bilgi Merkezi kategori sayfası (`/bilgi-merkezi/kategori/kalibrasyon-rehberleri`), blog genel sayfası (`/blog`) ve blog/rehber detay sayfası (`/bilgi-merkezi/basinc-kalibrasyonu-nedir`).
- Kalan ölçekleme sırası kullanıcıyla netleştirildi: önce ürün detay şablonu, sonra marka sayfaları, ardından Bilgi Merkezi ana/kategori sayfaları, en son blog içerikleri. Tasarım onayı alınmadan tüm marka ve blog içerikleri seri şekilde çoğaltılmamalı.
- Marka sayfalarında resmi temsilcilik, distribütörlük, yetkili satıcılık veya yetkili servis iddiası belge yoksa kullanılmayacak. Blog ve Bilgi Merkezi tarafında içerikler teknik rehber tonu taşıyacak; CTA görünür olacak ama içerik reklam metnine dönüştürülmeyecek.

## Yapılacaklar

Öncelik sırasına göre yapılacak işler:

1. [x] WordPress XML için temiz import scripti yazılacak.
2. [x] XML içindeki demo, tema, Woodmart/Elementor artığı ve dış bağlantılar temizlenecek.
3. [x] XML ürünleri kategori, marka ve alias haritalarına göre normalize edilecek.
4. [x] 509 ürün içinden yayında/taslak ayrımı netleştirilecek; yayına alınacak ürün listesi belirlenecek.
5. [x] Ürün görselleri slug, medya adı ve ürün adı eşleşmesine göre ürünlere bağlanacak.
6. [x] Ürünlerde sadece orijinal/büyük görsel kullanılacak; WordPress `150x150`, `300x300`, `600x600` türevleri dışarıda kalacak.
7. [x] Ürün teknik özellikleri düzenli tablo/veri alanına dönüştürülecek.
8. [x] Ürün dokümanları varsa katalog PDF, datasheet ve kullanım kılavuzu alanlarına bağlanacak.
9. [x] Ürün filtre sistemi genişletilecek; kategori ve marka dışında ürün özelliklerine göre filtre alanları belirlenecek.
10. [x] Filament kurulacak.
11. [x] Database migration'ları yazılacak.
12. [x] `services`, `technical_services`, `products`, `product_categories`, `product_brands`, `articles`, `faqs`, `leads`, `seo` gibi tablolar oluşturulacak.
13. [x] Statik `config/mta.php` verisi database modellerine taşınacak.
14. [x] Filament admin kaynakları oluşturulacak: hizmetler, teknik servisler, ürünler, kategoriler, markalar, blog, sayfalar, FAQ, medya/doküman, lead formları.
15. [x] Kategori-marka ve kategori-hizmet ilişki yönetimi admin panelden düzenlenebilir olacak.
16. [x] Ürün-hizmet eşleşmesi admin panelde ürün bazında override edilebilir hale getirilecek.
17. [x] Lead formları database'e kaydedilecek.
18. [x] Lead kayıtlarına kaynak URL, ürün, hizmet, UTM bilgisi ve durum/not alanları eklenecek.
19. [x] Lead geldiğinde e-posta bildirimi veya admin panel uyarısı kurgulanacak.
20. [x] Formlarda rate limit, honeypot ve spam koruması güçlendirilecek.
21. [x] SEO alanları admin panelden yönetilebilir olacak: title, description, canonical, OG image, robots, schema tipi.
22. [x] Eski WordPress URL'leri için 301 redirect haritası çıkarılacak.
23. [ ] Teknik servis sayfalarının eski URL karşılıkları yeni `/teknik-servis/...` rotalarına yönlendirilecek.
24. [ ] Sitemap canlı domain ve gerçek `APP_URL` ile doğrulanacak.
25. [ ] Robots.txt canlı yayın öncesi tekrar kontrol edilecek.
26. [ ] Ürün, hizmet ve teknik servis schema çıktıları gerçek içerikle tekrar gözden geçirilecek.
27. [ ] Blog içerikleri gerçek uzman metinleriyle doldurulacak.
28. [ ] Blog kategori yapısı netleştirilecek.
29. [ ] Hizmet sayfalarındaki akreditasyon/kapsam ifadeleri gerçek belgeye göre kesinleştirilecek.
30. [ ] Teknik servis sayfalarındaki cihaz kapsamları marka/model bazında zenginleştirilecek.
31. [ ] Referanslar ve sertifika belge içerikleri gerçek dosya/görseller geldikten sonra tamamlanacak. Hakkımızda ve iletişim sayfaları verilen metin/bilgilerle güncellendi.
32. [x] Sosyal medya linkleri gerçek URL'lerle değiştirildi: LinkedIn, Instagram, Facebook.
33. [x] Sertifika sorgulama şimdilik kapsam dışı bırakıldı; `/sertifikalar` sayfası belge listeleme için korunacak.
34. [x] Firma, web sitesi, hizmetler ve ürün kataloğunu anlatan paylaşılabilir brief dosyası oluşturulacak.
35. [x] Ana sayfa SEO brief'ine göre `/` meta, H1, H2, hero, CTA ve görsel alt textleri düzenlenecek.
36. [x] `/urunler/teraziler` sayfası hassas terazi SEO brief'ine göre meta, H1, H2, kategori metni, CTA, marka linkleri, iç linkler ve alt textlerle düzenlenecek.
37. [x] `/hizmetler/kutle-terazi-kalibrasyonu` sayfası terazi kalibrasyonu SEO brief'ine göre meta, H1, H2, kapsam tablosu, süreç, SSS, CTA, iç linkler ve alt textlerle düzenlenecek.
38. [x] `/teknik-servis/terazi-teknik-servis` sayfası terazi teknik servis SEO brief'ine göre meta, H1, H2, arıza listesi, süreç, SSS, CTA, iç linkler ve alt textlerle düzenlenecek.
39. [x] `/urunler/ph-metre` sayfası pH metre SEO brief'ine göre meta, H1, H2, kategori metni, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
40. [x] `/urunler/refraktometre` sayfası refraktometre SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
41. [x] `/urunler/manyetik-karistirici` sayfası manyetik karıştırıcı SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
42. [x] `/urunler/homojenizator` sayfası homojenizatör SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
43. [x] `/urunler/viskozimetre` sayfası viskozimetre SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
44. [x] `/urunler/nem-tayin` sayfası nem tayin cihazı SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
45. [x] `/hizmetler/sicaklik-kalibrasyonu` sayfası sıcaklık kalibrasyonu SEO brief'ine göre meta, H1, H2, kapsam tablosu, süreç, SSS, CTA, iç linkler ve alt textlerle düzenlenecek.
46. [x] `/hizmetler/hacim-kalibrasyonu` sayfası hacim kalibrasyonu SEO brief'ine göre meta, H1, H2, kapsam tablosu, süreç, SSS, CTA, iç linkler ve alt textlerle düzenlenecek.
47. [x] `/urunler/ph-iletkenlik` sayfası iletkenlik ölçer SEO brief'ine göre meta, H1, H2, kategori metni, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
48. [x] `/urunler/densitometre` sayfası densitometre SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
49. [x] `/urunler/kral-fischer` sayfası Karl Fischer titratör SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
50. [x] `/urunler/potansiyometrik-titratorler` sayfası potansiyometrik titratör SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, marka linkleri, SSS, iç linkler ve alt textlerle düzenlenecek.
51. [x] `/hizmetler/basinc-kalibrasyonu` sayfası basınç kalibrasyonu SEO brief'ine göre meta, H1, H2, cihaz kapsam listesi, kapsam tablosu, süreç, SSS, CTA, teknik servis iç linkleri ve alt textlerle düzenlenecek.
52. [x] `/hizmetler/devir-kalibrasyonu` sayfası devir kalibrasyonu SEO brief'ine göre meta, H1, H2, cihaz kapsam listesi, kapsam tablosu, süreç, SSS, CTA, ilgili ürün ve teknik servis iç linkleriyle düzenlenecek.
53. [x] `/hizmetler/tork-kalibrasyonu` sayfası tork kalibrasyonu SEO brief'ine göre meta, H1, H2, ekipman kapsam listesi, kapsam tablosu, süreç, SSS, CTA ve iç linklerle düzenlenecek.
54. [x] `/teknik-servis/laboratuvar-cihazlari-icin-teknik-servis` sayfası laboratuvar cihazları teknik servis SEO brief'ine göre meta, H1, H2, cihaz grupları, arıza listesi, süreç, SSS, CTA, ürün ve kalibrasyon iç linkleriyle düzenlenecek.
55. [x] `/teknik-servis/analiz-ve-olcum-cihazlari-teknik-servis` sayfası analiz ve ölçüm cihazları teknik servis SEO brief'ine göre meta, H1, H2, cihaz grupları, arıza listesi, süreç, SSS, CTA, ürün ve kalibrasyon iç linkleriyle düzenlenecek.
56. [x] `/urunler/termoreaktor` sayfası termoreaktör SEO brief'ine göre meta, H1, H2, kategori metni, kullanım alanları, CTA, VELP marka linki, SSS, iç linkler ve alt textlerle düzenlenecek.
57. [x] `/hizmetler` ana kalibrasyon hizmetleri sayfası SEO brief'ine göre meta, H1, H2, hizmet kartları, cihaz kapsamı, süreç, SSS, CTA, iç linkler ve görsel alt textlerle düzenlenecek.
58. [x] `/teknik-servis` ana teknik servis sayfası SEO brief'ine göre düzenlenecek.
59. [x] `/urunler` ana ürün katalog sayfası SEO brief'ine göre düzenlenecek.
60. [x] `/hakkimizda` sayfası SEO ve güven odaklı kurumsal brief'e göre düzenlenecek.
61. [x] `/iletisim` sayfası SEO ve dönüşüm odaklı brief'e göre düzenlenecek.
62. [x] `/sertifikalar` sayfası sorgulama modülü olmadan kurumsal belge sayfası olarak düzenlenecek.
63. [x] `/referanslar` sayfası gerçek olmayan müşteri/logo üretmeden sektör ve uygulama alanı sayfası olarak düzenlenecek.
64. [x] `/markalar` marka listeleme sayfası eklenecek ve SEO brief'e göre düzenlenecek.
65. [x] `/urunler/marka/and` marka detay sayfası A&D hassas terazi brief'ine göre düzenlenecek.
66. [x] `/urunler/teraziler/and-fz-500i-hassas-terazi` ürün detay sayfası özel SEO brief'e göre düzenlenecek.
67. [x] `/bilgi-merkezi` sayfası teknik kütüphane brief'ine göre düzenlenecek.
68. [x] `/blog` sayfası ayrı blog listeleme brief'ine göre eklenecek.
69. [x] `/bilgi-merkezi/basinc-kalibrasyonu-nedir` rehber detayı SEO brief'e göre düzenlenecek.
70. [x] `/bilgi-merkezi/kategori/kalibrasyon-rehberleri` kategori listeleme sayfası SEO brief'e göre düzenlenecek.
70a. [x] `docs/SAYFA_METIN_HARITASI.md` oluşturuldu; sitemap içindeki 230 URL için ortak header/footer, meta title/description ve ana içerik H1-H2-H3/metin akışı sayfa sayfa çıkarıldı.
71. [ ] Site genelinde responsive tasarım QA yapılacak: mobil, tablet, masaüstü ve geniş ekran.
72. [ ] Mega menü taşma/hover davranışı farklı ekran genişliklerinde görsel olarak tekrar test edilecek.
73. [ ] Ürün kategori/marka sayfalarında filtre paneli gerçek ürün sayılarıyla test edilecek; `/urunler` ana sayfasında filtreli tüm ürün listesi artık bulunmaz.
74. [ ] Görseller optimize edilecek; WebP/AVIF, lazy loading ve doğru boyutlandırma uygulanacak.
75. [ ] Kullanılmayan starter font/asset çıktıları temizlenecek veya build uyarısı azaltılacak.
76. [ ] Performans kontrolü yapılacak: Lighthouse, Core Web Vitals, görsel ağırlıkları, cache başlıkları.
77. [ ] Laravel production ayarları hazırlanacak: cache, config cache, route cache, view cache.
78. [ ] cPanel canlı deploy planı hazırlanacak.
79. [ ] Canlı MySQL bilgileri `.env` içine girilecek.
80. [ ] Storage/public symlink ve dosya yükleme dizinleri cPanel ortamına göre ayarlanacak.
81. [ ] Mail gönderimi için SMTP bilgileri ayarlanacak.
82. [ ] Yedekleme planı hazırlanacak: database, görseller, dokümanlar.
83. [ ] Google Search Console ve Analytics/GA4 kurulumu yapılacak.
84. [x] Admin panelde trafik ve lead akışı için dashboard kurgulanacak.
85. [x] Güvenlik kontrolleri yapılacak: admin auth, roller, dosya yükleme güvenliği, CSRF/rate limit.
86. [ ] Test kapsamı genişletilecek: rota testleri, sitemap testleri, lead form testleri, import testleri. Not: Admin, lead, redirect ve ürün medya testleri eklendi; sitemap/import testleri ayrıca eklenmeli.
87. [ ] Canlı yayın öncesi son SEO checklist tamamlanacak.

## 2026-08-29 — Zayıf ve Orta Riskli SEO Sayfa Düzeltmeleri

Kullanıcı isteği üzerine `SEO/MTA_A_Z_SEO_AUDIT_PUANLARI.md` dosyasında zayıf ve orta riskli görünen sayfalar uygulama seviyesinde ele alındı. Bu turdaki amaç yalnızca öneri yazmak değil, Laravel çıktısında sayfaların SEO/AEO/GEO açısından daha güçlü görünmesini sağlamaktı.

### Değiştirilen Dosyalar

- `app/Http/Controllers/SiteController.php`
- `resources/views/pages/product-category.blade.php`
- `resources/views/pages/product-brand.blade.php`
- `resources/views/pages/knowledge-category.blade.php`
- `SEO/MTA_ZAYIF_ORTA_RISK_DUZELTME_RAPORU.md`

### Uygulanan Düzeltmeler

- Ürün kategori sayfalarında SEO içeriği boş döndüğünde sayfanın zayıf/thin kalmasına neden olan yapı düzeltildi. Kategori adı, ürün tipi ve B2B teklif akışından beslenen varsayılan H2/H3, CTA, destek linkleri, SSS, liste açıklaması ve boş durum metni üretimi eklendi.
- `/urunler/mekanik-karistirici` ve `/urunler/termal-analiz` sayfaları güçlendirildi; bu sayfalarda `noindex,follow` kaldırılarak `index,follow` çıktısı verildi.
- Ürün kategori sayfalarında “Katalog hazırlanıyor” ve “ürünleri yakında eklenecek” gibi zayıf sinyaller veren boş durum metinleri kaldırıldı. Yerine ürün/marka/model bilgisiyle teklif talebi oluşturmaya yönlendiren B2B metinleri eklendi.
- Marka detay sayfalarında ortak SEO fallback içeriği güçlendirildi. Marka sayfalarına teknik ürün değerlendirmesi, kategori/teklif bağlantısı, resmi marka ilişkisi notu, FAQ, CTA ve boş durum metni eklendi.
- `normalizeBrandSeoContent()` içinde özel marka SEO içeriğinin ortak fallback tarafından ezilmesine neden olan merge sırası düzeltildi. Artık marka özel içeriği varsa ortak fallback’in üzerine yazıyor.
- Bilgi Merkezi kategori sayfaları için `defaultKnowledgeCategorySeoContent()` eklendi. `/bilgi-merkezi/kategori/laboratuvar-cihazlari`, `/olcum-guvenilirligi`, `/satin-alma-rehberleri`, `/teknik-servis-ve-bakim` sayfaları artık kalibrasyon kategorisinin kopyası gibi değil, kendi bilgi niyetiyle çalışan meta, hero, scope, content items, ilgili link ve CTA yapısıyla çıkıyor.
- Ürün detay sayfalarında `normalizeProductDetailSeoContent()` boş dönmeyecek şekilde genişletildi. Genel ürün detayları artık ürün açıklaması, teknik değerlendirme, teklif süreci, destek linkleri ve ürün odaklı FAQ alıyor.
- Ürün meta title üretimi `productSeoTitle()` ile kısaltıldı; uzun ürün adlarında kelime ortasında kesilme riski azaltıldı. `kΩ/kΩ/kω` gibi değerler title tarafında `kOhm` olarak temizleniyor.
- Ürün meta description üretimi `productMetaDescription()` ile sınırlandı; ilgili hizmet bilgileri description'a kontrollü şekilde ekleniyor.
- DB ürünleri aktifken import dosyasındaki ürünlerin dışarıda kalması nedeniyle oluşan 404/thin page riski azaltıldı. `importedProducts()` helper’ı eklendi ve import ürünleri DB ürünleriyle birlikte ürün datasına dahil edildi.
- Özel karakterli ürün slug’ları normalize edildi. Örneğin `/urunler/ph-iletkenlik/mettler-toledo-inlab-ntc-30-k%cf%89-sicaklik-sensoru-sertifikali` eski URL’si, kanonik `/urunler/ph-iletkenlik/mettler-toledo-inlab-ntc-30-k-ohm-sicaklik-sensoru-sertifikali` URL’sine 301 yönlenecek şekilde düzeltildi.
- Kategori ürün ALT üretimi kapsamına `mekanik-karistirici`, `balon-isiticilar` ve `termal-analiz` da dahil edildi.

### Hedefli Kontrol Edilen URL Grupları

- `/urunler/ph-iletkenlik/mettler-toledo-inlab-ntc-30-k%cf%89-sicaklik-sensoru-sertifikali`
- `/urunler/ph-iletkenlik/mettler-toledo-inlab-ntc-30-k-ohm-sicaklik-sensoru-sertifikali`
- `/urunler/mekanik-karistirici`
- `/urunler/termal-analiz`
- `/urunler/marka/brookfield`
- `/urunler/marka/si-analitik`
- `/urunler/marka/stuart`
- `/urunler/marka/wtw`
- `/bilgi-merkezi/kategori/laboratuvar-cihazlari`
- `/bilgi-merkezi/kategori/olcum-guvenilirligi`
- `/bilgi-merkezi/kategori/satin-alma-rehberleri`
- `/bilgi-merkezi/kategori/teknik-servis-ve-bakim`
- `/urunler/marka/bellingham-stanley`
- `/urunler/marka/titroline-7500-kf`
- `/urunler/marka/kyoto-kem`
- `/urunler/termoreaktor`
- `/urunler/ph-metre`
- `/urunler/marka/shimadzu`
- `/urunler/marka/velp`
- `/urunler/marka/weightlab`
- `/urunler/balon-isiticilar`
- `/urunler/densitometre/da-650`
- `/urunler/densitometre/density-and-refractive-index-all-in-one-analyzer-asca-6400`
- `/urunler/manyetik-karistirici/weightlab-dijital-isitmali-manyetik-karistirici-wn-10h-120`
- `/urunler/manyetik-karistirici/weightlab-dijital-isitmali-manyetik-karistirici-wn-ap550`

### Doğrulama Sonuçları

- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php -l resources/views/pages/product-category.blade.php`: geçti.
- `php -l resources/views/pages/product-brand.blade.php`: geçti.
- `php -l resources/views/pages/knowledge-category.blade.php`: geçti.
- `php artisan test`: geçti, 2 test / 2 assertion.
- `npm run build`: geçti. Build sırasında yalnızca mevcut optional `fontaine` uyarısı görüldü; build başarısız olmadı.
- Hedefli yeniden kontrolde zayıf ve orta riskli URL grubunun tamamı `200` çıktı verdi veya eski slug’dan doğru kanonik URL’ye yönlendi.
- Hedefli kalite kontrolünde bu grup yaklaşık `9.0 / 10` seviyesine çıktı.

### Oluşturulan Rapor

- `SEO/MTA_ZAYIF_ORTA_RISK_DUZELTME_RAPORU.md`

### Kalan Notlar

- Tam `tools/export-page-text-map.php` çalışması 230 URL’yi gezerken uzun sürdüğü için hedefli URL kontrolüyle doğrulama yapıldı. Canlı yayından önce export aracı performans açısından ayrıca gözden geçirilebilir.
- Canlı domain yayını sonrası Google Search Console, Bing Webmaster Tools, sitemap, canonical, robots, Core Web Vitals ve gerçek index kapsamı ayrıca ölçülmelidir.
- Bu turdaki değişiklikler ağırlıklı olarak SEO çıktısı, sayfa metin blokları, URL davranışı ve içerik mimarisi üzerindedir; büyük görsel tasarım değişikliği yapılmamıştır.

## 2026-08-31 — Filament Admin Paneli İlk CMS Fazı

Kullanıcı isteği üzerine `AI_HANDOFF.md` ve mevcut Laravel/Blade/Filament mimarisi okunarak admin panelin ilk uygulanabilir CMS fazı eklendi. Yeni teknoloji dayatılmadı; mevcut Laravel 13, Eloquent, Blade ve Filament yapısıyla devam edildi.

### Değiştirilen / Eklenen Ana Dosyalar

- `database/migrations/2026_08_31_030000_add_admin_cms_fields.php`
- `app/Models/User.php`
- `app/Models/Article.php`
- `app/Models/Product.php`
- `app/Models/Page.php`
- `app/Models/MediaAsset.php`
- `app/Http/Controllers/SiteController.php`
- `resources/views/layouts/site.blade.php`
- `app/Filament/Resources/**`
- `app/Filament/Widgets/ContentOverview.php`
- `app/Filament/Widgets/LatestContent.php`
- `resources/views/filament/widgets/latest-content.blade.php`
- `tests/Feature/AdminPanelTest.php`

### Uygulananlar

- `/admin` altında Filament kaynakları eklendi: Blog içerikleri, Ürün yönetimi, Ürün kategorileri, Ürün markaları, İçerik sayfaları, Sayfa SEO yönetimi, Medya kütüphanesi, Kullanıcılar ve Roller.
- Blog modülüne listeleme, yeni yazı, düzenleme, silme/arşivleme, taslak/yayın/arşiv durumu, slug, özet, kapak görseli, içerik editörü, kategori/etiket, yazar, yayın tarihi ve SEO alanları eklendi.
- Ürün modülüne listeleme, ekleme, düzenleme, silme/arşivleme, kategori/marka ilişkisi, kısa/uzun açıklama, ana görsel, galeri, özellikler, spec tablosu, doküman/PDF alanı, sıralama, aktif/taslak/arşiv, öne çıkan ve ürün özel SEO alanları eklendi.
- Sayfa SEO yönetimi `seo_entries` üzerinden path/route bazlı çalışacak şekilde düzenlendi. Meta title, meta description, canonical, robots, OG title/description/image ve ek schema payload alanları panelden yönetilebilir.
- Mevcut Blade H1/ana içerik yapısı karışık statik şablonlar üzerinde durduğu için H1 değişikliği Sayfa SEO modülüne zorlanmadı; bunun yerine `pages` tablosuna bağlı ayrı İçerik Sayfaları modülüyle yönetilebilir bırakıldı.
- Medya kütüphanesi için `media_assets` tablosu, görsel yükleme, alt text, dosya adı ve metadata alanları eklendi.
- Kullanıcı modeline `role` ve `is_active` alanları eklendi. Filament panel erişimi yalnızca aktif `admin` ve `editor` rolleri için açık. Kullanıcı/rol yönetimi yalnızca Admin rolünde görünür.
- Blog, ürün, sayfa ve medya modellerinde `created_by` / `updated_by` audit alanları otomatik doldurulacak şekilde bağlandı.
- Admin dosya yüklemelerinde görseller için JPG/PNG/WebP/AVIF, dokümanlarda PDF ve dosya boyutu limitleri tanımlandı.
- Dashboard’a blog/ürün/sayfa SEO/medya sayıları ve son düzenlenen blog/ürün içerikleri eklendi.
- `SiteController::meta()` path/route bazlı `seo_entries` kayıtlarını ve ürün/blog kayıt özel SEO alanlarını server-rendered `<head>` çıktısına uygular hale getirildi. `site.blade.php` OG title/description alanları da ayrı değerleri okuyacak şekilde güncellendi.

### Doğrulama

- Tüm değişen PHP dosyaları için `php -l` temiz geçti.
- `php artisan migrate --force`: geçti.
- `php artisan route:list --path=admin`: 27 admin rotası listelendi.
- `php artisan test`: geçti, 4 test / 12 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.

### Kalan Notlar

- Mevcut `test@example.com` kullanıcısı otomatik Admin’e yükseltilmedi. Gerçek Admin hesabı `php artisan mta:create-admin email@domain.com --name="Ad Soyad"` komutuyla bilinçli şekilde oluşturulabilir.
- Hizmetler, teknik servisler, FAQ, lead ve redirect yönetimi 2026-08-31 ikinci turunda eklendi.
- Canlı dosya yükleme için `php artisan storage:link` ve cPanel storage/public symlink yapısı canlı ortamda ayrıca doğrulanmalı.

## 2026-08-31 — Admin Kalan Modüller ve Ürün Medya Genişletmesi

Kullanıcının kalan işleri başlatma isteğiyle admin panel genişletildi ve ürün detay sayfaları çoklu görsel, YouTube video ve PDF katalog/doküman akışına uygun hale getirildi.

### Eklenenler

- Filament kaynakları eklendi: Kalibrasyon Hizmetleri, Teknik Servis, SSS Yönetimi, Teklif Talepleri, 301 Yönlendirmeler.
- Ürün admin formuna ürün videoları eklendi. YouTube linki veya video ID girilebiliyor; video ID model tarafından otomatik çıkarılıyor.
- Ürün admin formuna ürün bazlı ilgili kalibrasyon hizmeti override alanı eklendi. Boş bırakılırsa kategori-hizmet ilişkisi kullanılmaya devam eder.
- `product_videos` tablosu ve `product_service` pivot tablosu eklendi.
- Ürün detay sayfasında ana görselin altında galeri thumbnail şeridi gösteriliyor.
- Ürün detay sayfasında PDF katalog/datasheet/kullanım kılavuzu dokümanları yalnızca kayıt varsa gösteriliyor.
- Ürün detay sayfasındaki YouTube videoları veritabanı kayıtları ve eski kod içi video fallback listesiyle birlikte çalışıyor.
- Product JSON-LD schema artık ana görsel + galeri görsellerini ve varsa YouTube videolarını içeriyor.
- Lead formu artık `leads` tablosuna kayıt oluşturuyor. Ürün/hizmet/teknik servis query parametresi varsa ilgili kayıtla ilişkilendiriliyor; UTM ve payload alanları tutuluyor.
- `/iletisim` POST rotasına rate limit eklendi; mevcut honeypot alanı korunuyor.
- Aktif 301/302 redirect kayıtları için fallback yönlendirme rotası eklendi.
- Dashboard teklif talepleri ve yeni lead sayısını gösteriyor.
- Güvenli Admin hesabı oluşturmak için `php artisan mta:create-admin` komutu eklendi.

### Doğrulama

- Değişen PHP dosyaları için `php -l` temiz geçti.
- `php artisan migrate --force`: geçti.
- `php artisan route:list --path=admin`: 41 admin rotası listelendi.
- `php artisan test`: geçti, 7 test / 26 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.

### Kalan Notlar

- Canlı SMTP bilgileri girilirse lead e-posta bildirimi ayrıca gerçek alıcı adresine bağlanabilir. Şu an admin panelde yeni lead sayısı ve lead listesi uyarı/izleme noktası olarak çalışıyor.
- Eski WordPress URL listesinin tamamı admin Redirect modülüne girilmeli veya toplu import aracıyla aktarılmalı.
- Sitemap ve import testleri hâlâ ayrıca genişletilebilir.

## 2026-08-31 — Admin Schema, Sosyal Medya ve Analitik Ayarları

Kullanıcı isteğiyle admin panel SEO tarafı genişletildi, panel metinleri Türkçeleştirildi ve düzenleme ekranlarından ön yüz sayfalarına hızlı geçiş eklendi.

### Eklenenler

- SEO menüsüne `Schema Yönetimi` eklendi. Varsayılan tanımlar: Kuruluş Bilgisi, Web Sitesi, Web Sayfası, Ürün, Blog Yazısı, Hizmet, Sık Sorulan Sorular, Video, Görsel, Yerel Firma ve Ekmek Kırıntısı.
- Blog, ürün, içerik sayfası, sayfa SEO kaydı, kalibrasyon hizmeti ve teknik servis formlarına çoklu `Schema Yönetimi` alanı eklendi.
- Seçili schema blokları ilgili sayfanın server-rendered JSON-LD çıktısına ekleniyor. Mevcut otomatik Organization/WebPage/Breadcrumb/Product/Service/Article schema yapısı korunuyor.
- Ürün ve blog detay sayfaları kendi kayıtlarındaki `schema_blocks` verisini; hizmet ve teknik servis detayları kendi kayıtlarındaki `schema_blocks` verisini; statik sayfalar ise varsa `pages` ve `seo_entries` kayıtlarındaki schema verisini okuyacak şekilde bağlandı.
- Site Ayarları menüsüne `Sosyal Medya ve Kodlar` eklendi. Sosyal medya linkleri, Google Analytics / GSC / Bing doğrulama meta etiketleri ve head/body kod alanları panelden yönetilebilir.
- Ön yüz layout sosyal medya linklerini artık veritabanındaki site ayarlarından okuyor; boş linkler gösterilmiyor.
- Doğrulama ve analitik kodları `head`, `body` başlangıcı ve `body` sonu için ayrı alanlardan çıktılanıyor.
- Admin panel genişliği `full` yapıldı; geniş tablo ve repeater alanlarında daralma azaltıldı.
- Ürün, blog, içerik sayfası, SEO kaydı, kalibrasyon hizmeti ve teknik servis liste/düzenleme ekranlarına `Ön yüzde gör` butonu eklendi.
- Görünür admin etiketleri Türkçeleştirildi: Meta başlık/açıklama, Canonical adres, Robots etiketi, OG başlık/açıklama/görsel, kısa bağlantı adı, alternatif metin, ürün kodu, düzenle/sil/toplu sil vb.
- `schema_definitions` ve `site_settings` tablolarında `created_by`, `updated_by`, `created_at`, `updated_at` audit alanları bulunuyor.

### Değiştirilen / Eklenen Ana Dosyalar

- `database/migrations/2026_08_31_050000_add_schema_management_and_site_settings.php`
- `app/Models/SchemaDefinition.php`
- `app/Models/SiteSetting.php`
- `app/Support/SiteSettings.php`
- `app/Filament/Support/SchemaBlockFields.php`
- `app/Filament/Resources/SchemaDefinitions/**`
- `app/Filament/Resources/SiteSettings/**`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Http/Controllers/SiteController.php`
- `resources/views/layouts/site.blade.php`
- `tests/Feature/AdminPanelTest.php`

### Doğrulama

- Değişen PHP dosyaları için `php -l` temiz geçti.
- `php artisan migrate --force`: geçti.
- `php artisan route:list --path=admin`: 46 admin rotası listelendi.
- `php artisan test`: geçti, 9 test / 34 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.

### Aktif Admin Girişi

- Admin URL: `http://127.0.0.1:8001/admin/login`
- Kullanıcı: `site@mtaend.com`
- Şifre: `MtaEnd!2026#Q7vR9xL2`

## 2026-08-31 — Teklif Formu ve WhatsApp Teklif Akışı

Kullanıcı isteği üzerine ürün, kalibrasyon hizmeti ve teknik servis sayfalarındaki teklif akışı iletişim sayfasından ayrıldı.

### Eklenen / Değiştirilenler

- Yeni `/teklif-al` sayfası ve `quote` rotası eklendi.
- `/teklif-al` tek sade form sayfasıdır; ürün/hizmet/servis için ayrı teklif sayfası oluşturulmaz.
- Parametreli teklif URL'lerinin SEO/URL sistemini şişirmemesi için teklif sayfası `robots: noindex,follow` ve canonical olarak parametresiz `/teklif-al` üretir.
- Teklif sayfası kullanıcıya yalnızca `Teklif Al` başlığı ve formu gösterir; kaynak ürün/hizmet/servis bilgisi görünür kutu olarak gösterilmez, hidden alanlarla arka planda taşınır.
- `/teklif-al` POST rotası mevcut `SiteController::submitLead()` mantığını kullanır; lead kayıtları yine admin panelde `Teklif Talepleri` altında görünür.
- İletişim formu ortak `resources/views/partials/lead-form.blade.php` partial'ına taşındı; `/iletisim` formu aynı sistemle çalışmaya devam eder.
- Ürün detay sayfasındaki `Teklif Al` butonu `/teklif-al?product={slug}` adresine gider; yanına `WhatsApp ile Teklif Al` butonu eklendi.
- Kalibrasyon hizmet detaylarında `Teklif Al` `/teklif-al?service={slug}` adresine gider; yanına WhatsApp teklif butonu eklendi.
- Teknik servis detaylarında `Teklif Al` `/teklif-al?technical_service={slug}` adresine gider; önceki `service` query kullanımı düzeltilerek teknik servis lead ilişkisi doğru tabloya bağlandı.
- Hizmetler ve teknik servis liste sayfalarındaki kartlara ilgili hizmet/servis için `Teklif Al` ve `WhatsApp` aksiyonları eklendi.
- Ürün katalog, kategori, marka ve genel header/footer teklif CTA'ları iletişim yerine `/teklif-al` formuna yönlenecek şekilde güncellendi.
- WhatsApp linki `config('mta.site.whatsapp')` doluysa onu, boşsa mevcut telefon numarasını kullanır. Şu an fallback numara `+90 (216) 390 17 78` üzerinden `902163901778` olarak üretilir.
- Form hidden alanları `product`, `service`, `technical_service`, `source_type`, `source_name`, `source_url` ve UTM verilerini taşır. Böylece ürün detayından geliyorsa ürün ID/name, hizmetten geliyorsa hizmet ID/name, servisten geliyorsa teknik servis ID/name admin lead kaydında görünür.
- Form label ağırlığı azaltıldı; teklif formu daha hafif ve kolay okunur hale getirildi.

### Doğrulama

- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php -l routes/web.php`: geçti.
- `php -l tests/Feature/AdminPanelTest.php`: geçti.
- `php artisan test`: geçti, 11 test / 43 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.

## 2026-08-31 — Ürünler Sayfası Hero, Marka Kartları ve Admin Görselleri

Kullanıcı isteğiyle `/urunler` sayfasındaki kategori/marka yönlendirme alanı sadeleştirildi ve admin görselleri ön yüze bağlandı.

### Eklenen / Değiştirilenler

- Hero butonları `Kategorileri İncele` ve `Markaları İncele` olarak güncellendi.
- `Markaları İncele` butonu sayfa içindeki `#markalar` bölümüne kaydırır.
- Hero altındaki 3 adet ürün/kategori/marka sayaç kutusu kaldırıldı.
- Marka kartları kategori kartlarıyla aynı `content-card category-card` tasarımına taşındı.
- Marka kartlarında ürün sayısı gibi sayaç bilgileri gösterilmez.
- Admin panelde kategori `Görsel` alanı ve marka `Logo / Marka görseli` alanı katalog kartlarında, kategori detayında ve marka detayında kullanılacak şekilde bağlandı.
- Veritabanındaki kategori kaydı config kategorisiyle aynı slug'a sahipse admin görseli/özeti config kaydının üzerine yazılır; böylece admin değişikliği `/urunler` ve ilgili kategori sayfasına yansır.
- Marka logosu hem marka kartlarında hem marka detay hero görselinde kullanılacak `image` verisi olarak hazırlanır.

### Doğrulama

- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php -l app/Filament/Resources/ProductBrands/ProductBrandResource.php`: geçti.
- `php -l tests/Feature/AdminPanelTest.php`: geçti.
- `php artisan test --filter=admin_category_and_brand_images_render_on_catalog_pages`: geçti, 1 test / 11 assertion.
- `php artisan test`: geçti, 12 test / 54 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.
- `http://127.0.0.1:8001/urunler`: 200 döndü; hero butonları, `#markalar` anchor'ı, marka kartları doğrulandı; eski `taxonomy-stats` bloğu görünmüyor.

## 2026-08-31 — Ürün Kategori Sayfası Yerleşim Güncellemesi

Kullanıcı isteğiyle `/urunler/{kategori}` şablonu viskozimetre örneği üzerinden tüm kategori sayfaları için yeniden düzenlendi.

### Eklenen / Değiştirilenler

- Kategori hero alanındaki ürün sayısı ve marka sayısı kutuları kaldırıldı.
- Hero'dan hemen sonra kategoriye ait marka section'ı gelecek şekilde sayfa sırası değiştirildi.
- Kategori marka kartları daha büyük, görsel odaklı `category-brand-link--showcase` tasarımına alındı; marka kartlarında ürün sayısı gösterilmez.
- Ürün filtreleri kategori sayfalarında sidebar yerine ürünlerin üstünde yatay `catalog-filter-bar` yapısına taşındı.
- Kategori ürün listesi `catalog-product-grid--two` ile masaüstünde iki kolon sıralanır; mobilde tek kolona düşer.
- Ürün kartlarında model ve SKU alanları kaldırıldı.
- Ürün kartı aksiyonu `Ürünü İncele` olarak değiştirildi ve kartın tamamı ürün detay sayfasına tıklanabilir hale getirildi.
- Hero altındaki ilk iki SEO kutusu ürünlerin altına taşındı; kategori kullanım alanları aynı section içinde `device-card-grid compact` kutularıyla gösterilir.
- Teknik özellik filtresi partial'ı hem eski dikey kullanım hem yeni yatay kategori kullanımı için varyant destekler.

### Doğrulama

- `php -l resources/views/pages/product-category.blade.php`: geçti.
- `php -l resources/views/partials/product-card.blade.php`: geçti.
- `php -l resources/views/partials/product-spec-filters.blade.php`: geçti.
- `php -l tests/Feature/AdminPanelTest.php`: geçti.
- `php artisan test --filter=product_category_pages_use_brand_first_horizontal_catalog_layout`: geçti, 1 test / 20 assertion.
- `php artisan test`: geçti, 13 test / 74 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.
- `http://127.0.0.1:8001/urunler/viskozimetre`: 200 döndü; hero marka ürün içerik sırası, yatay filtreler, iki kolon ürün grid'i, yeni `Ürünü İncele` aksiyonu ve kaldırılan model/SKU alanları doğrulandı.

## 2026-08-31 — Ortak Marka ve Kategori Kart Tasarım Dili

Kullanıcı isteğiyle sitedeki ürün kategori ve marka kartları tek ortak tasarım bileşenine bağlandı.

### Eklenen / Değiştirilenler

- `resources/views/partials/taxonomy-card.blade.php` eklendi.
- `/urunler` sayfasındaki kategori ve marka kartları ortak `taxonomy-card` partial'ını kullanır.
- `/urunler/{kategori}` sayfalarındaki marka kartları aynı ortak kart partial'ına ve büyük `taxonomy-card--showcase` varyantına taşındı.
- `/markalar` sayfasındaki marka listesi ve kategori ilişkileri kartları aynı ortak kart partial'ını kullanır.
- Kartlar artık ayrı bir küçük `İncele` linki yerine doğrudan `<a class="content-card category-card taxonomy-card ...">` olarak render edilir; kullanıcı kartın herhangi bir noktasına tıklayınca ilgili kategori/marka sayfasına gider.
- Eski `category-brand-link` ve `category-brand-strip` kart tasarım sınıfları ön yüz kullanımından kaldırıldı.
- Ortak kart hover, görsel alanı, başlık/açıklama ve `İncele` aksiyonu CSS'i `resources/css/app.css` içinde merkezi hale getirildi.

### Doğrulama

- `php -l resources/views/partials/taxonomy-card.blade.php`: geçti.
- `php -l resources/views/pages/products.blade.php`: geçti.
- `php -l resources/views/pages/product-category.blade.php`: geçti.
- `php -l resources/views/pages/brands.blade.php`: geçti.
- `php -l tests/Feature/AdminPanelTest.php`: geçti.
- `php artisan test --filter=admin_category_and_brand_images_render_on_catalog_pages`: geçti, 1 test / 17 assertion.
- `php artisan test --filter=product_category_pages_use_brand_first_horizontal_catalog_layout`: geçti, 1 test / 20 assertion.
- `php artisan test`: geçti, 13 test / 80 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.
- `http://127.0.0.1:8001/urunler`, `/urunler/viskozimetre`, `/markalar`: 200 döndü; ortak `taxonomy-card` sınıfları ve tam kart anchor davranışı doğrulandı; eski `category-brand-link` HTML çıktısı görünmüyor.

## 2026-08-31 — Ürün Detay Katalog Butonu

Kullanıcı isteğiyle ürün detay sayfasına, admin panelde katalog dokümanı eklenmişse görünen koşullu katalog butonu eklendi.

### Eklenen / Değiştirilenler

- `resources/views/pages/product-detail.blade.php` içinde ürün dokümanları arasından ilk `type = catalog` kaydı bulunur.
- Katalog kaydında dosya yolu veya dış URL varsa hero CTA alanında `Kataloğu İncele` butonu gösterilir.
- Aynı koşullu buton ürün detay sayfasının alt CTA alanında da gösterilir.
- Ürüne katalog dokümanı eklenmemişse buton hiç render edilmez.

### Doğrulama

- `php -l resources/views/pages/product-detail.blade.php`: geçti.
- `php -l tests/Feature/AdminPanelTest.php`: geçti.
- `php artisan test --filter=product_page_renders_gallery_video_and_pdf_documents`: geçti, 1 test / 7 assertion.
- `php artisan test --filter=product_page_renders_admin_schema_blocks`: geçti, 1 test / 5 assertion.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `npm run build`: geçti. Mevcut optional `fontaine` uyarısı devam ediyor; build başarısız değil.

## 2026-08-31 — Weightlab ve Ek VELP Ürün Batch Güncellemesi

Kullanıcının sohbet içinde gönderdiği ürün verileri yerel SQLite ürün kataloğuna idempotent seed dosyalarıyla işlendi.

### Eklenen / Değiştirilenler

- `tools/seed-analitik-terazi.php` Weightlab WSA-224 ve WSA-224T analitik terazilerini yönetir.
- `tools/seed-weightlab-products.php` eklendi; Weightlab için hassas teraziler, nem tayin, manyetik karıştırıcılar, hot plate, vorteks, mekanik karıştırıcı, balon ısıtıcılar, su banyosu, santrifüjler, rotatörler, pipetler, çalkalamalı su banyosu, soğutmalı inkübatör, vakumlu etüv, inkübatör, fanlı etüv ve ultrasonik banyo ürünlerini yönetir.
- `tools/seed-velp-extra-products.php` eklendi; VELP ROTAX-6.8 ürününü `rotator-calkalayici`, VELP TB1 Türbidimetre ürününü `ph-metre` kategorisine ekler.
- `pipetler` ürün kategorisi eklendi ve `hacim-kalibrasyonu` hizmetine bağlandı.
- `config/mta.php` içinde `ph-metre` marka izin haritasına `velp` eklendi.
- `SiteController::normalizeProductCategorySeoContent()` içinde `pipetler` kategori SEO varsayılanı eklendi; `/urunler/pipetler` sayfasındaki eksik `primary_cta` hatası giderildi.
- ROTAX-6.8 için mevcut `rotator-calkalayici` kategorisi kullanıldı; bu kategori config tarafında `Karıştırıcılar` altında tanımlı olduğu için üst kategori ilişkisi korunur.
- TB1 Türbidimetre, kullanıcı isteğine göre `pH Metre` kategorisine bağlandı.
- Yerel DB ürün toplamı 155 oldu. Weightlab ürün toplamı 75; bu son ek VELP batch ile 2 ürün daha eklendi.

### Notlar

- Yeni eklenen ROTAX ve TB1 için şu an görsel dosyası bulunamadı; ürünler placeholder ile açılır. Önerilen görsel dosyaları:
  - `public/images/products/velp-rotax-6-8-rotator-calkalayici.webp`
  - `public/images/products/velp-tb1-turbidimetre.webp`
- Weightlab batch içinde görseli henüz bulunmayan başlıca ürünler: `weightlab-wl-303-hassas-terazi`, `weightlab-wa-123m-nem-tayin-cihazi`, `weightlab-wf-od20-mekanik-karistirici`, `weightlab-wn-cl6500-genel-amacli-klinik-santrifuj`, `weightlab-wn-cm4500-cok-yonlu-laboratuvar-santrifuj`, `weightlab-wn-15cmr-sogutmali-mikro-santrifuj`, `weightlab-wn-cmv6000-mikro-santrifuj`, `weightlab-wn-cm15n-yuksek-hizli-mikro-santrifuj`, `weightlab-wf-sbc30-calkalamali-su-banyosu`, `weightlab-wf-ltc70r-calkalamali-sogutmali-inkubator`, `weightlab-wf-htv25-vakumlu-etuv`, `weightlab-wf-htv52-vakumlu-etuv`.
- Kullanıcı metninde su banyosu ikinci model link etiketi `WF-SB36` görünse de tablo ve ürün başlığı `WF-SB30N` dediği için ürün modeli `WF-SB30N` olarak kaydedildi.
- Kullanıcı BDK1000 için ayrı teknik tablo satırı vermediği için `WF-BDK1000` ürününde ortak seri özellikleri ve kapasite bilgisi işlendi; güç/ölçü/ağırlık ayrıca netleştirilebilir.

### Doğrulama

- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php -l config/mta.php`: geçti.
- `php -l tools/seed-velp-extra-products.php`: geçti.
- `php tools/seed-velp-extra-products.php`: geçti; iki ürün eklendi/güncellendi, ürün toplamı 155.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `http://127.0.0.1:8000/urun/velp-rotax-6-8-rotator-calkalayici`: 200 döndü.
- `http://127.0.0.1:8000/urun/velp-tb1-turbidimetre`: 200 döndü.
- `http://127.0.0.1:8000/urunler/rotator-calkalayici`: 200 döndü.
- `http://127.0.0.1:8000/urunler/ph-metre`: 200 döndü.
- `http://127.0.0.1:8000/urunler/pipetler`: 200 döndü.
- `http://127.0.0.1:8000/sitemap.xml`: 200 döndü.

## 2026-08-31 — WTW Çözünmüş Oksijen Ürünleri

Kullanıcının gönderdiği WTW çözünmüş oksijen ölçüm cihazları ve elektrotları `ph-iletkenlik` kategorisine eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-wtw-ph-iletkenlik-products.php` eklendi.
- WTW markası ve `ph-iletkenlik` kategori ilişkisi DB tarafında garanti altına alındı.
- İlk üç cihaz için kullanıcının isteğine uygun şekilde açıklama, özellik ve teknik özellik tablosu üretilmedi; yalnızca ürün adı/model ve temel metadata işlendi.
- Elektrot ürünlerinde kullanıcının verdiği tablo `specs` alanına işlendi: model, kat. no., malzeme, kablo ve sıcaklık sensörü.
- Broşür linkleri henüz verilmediği için ürün dokümanları şimdilik boş bırakıldı; seed içinde her ürün için broşür dizisi hazır tutuldu.

### Eklenen Ürünler

- `WTW Oxi 3205 Set 1 Çözünmüş Oksijen Ölçüm Cihazı` — `/urun/wtw-oxi-3205-set-1-tasinabilir-cozunmus-oksijen-olcum-cihazi`
- `WTW Oxi 3310 Set 1 Çözünmüş Oksijen Ölçüm Cihazı` — `/urun/wtw-oxi-3310-set-1-tasinabilir-cozunmus-oksijen-olcum-cihazi`
- `WTW inoLab Oxi 7310 Set 1 Çözünmüş Oksijen Ölçüm Cihazı` — `/urun/wtw-inolab-oxi-7310-set-1-masa-tipi-cozunmus-oksijen-olcum-cihazi`
- `WTW CellOx 325 Çözünmüş Oksijen Ölçüm Elektrodu` — `/urun/wtw-cellox-325-cozunmus-oksijen-olcum-elektrodu`
- `WTW Durox 325-3 Çözünmüş Oksijen Ölçüm Elektrodu` — `/urun/wtw-durox-325-3-cozunmus-oksijen-olcum-elektrodu`
- `WTW FDO 925 Çözünmüş Oksijen Ölçüm Elektrodu` — `/urun/wtw-fdo-925-cozunmus-oksijen-olcum-elektrodu`

### Notlar

- Bu ürünlere ait görsel dosyaları klasörde bulunmadı; ürünler placeholder ile açılır.
- Önerilen ana görsel dosya adları ürün slug'larıyla aynıdır, örnek: `public/images/products/wtw-oxi-3205-set-1-tasinabilir-cozunmus-oksijen-olcum-cihazi.webp`.
- Yerel DB ürün toplamı 161 oldu; `ph-iletkenlik` kategorisinde WTW için 6 DB ürünü var.

### Doğrulama

- `php -l tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-wtw-ph-iletkenlik-products.php`: geçti; 6 ürün eklendi/güncellendi.
- `php artisan test`: geçti, 13 test / 83 assertion.
- Altı ürün detay URL'si, `/urunler/ph-iletkenlik`, `/urunler/marka/wtw` ve `/sitemap.xml`: 200 döndü.

## 2026-08-31 — WTW inoLab pH 7310P Set 2

Kullanıcının gönderdiği WTW masa tipi pH metre ürün açıklaması `ph-iletkenlik` kategorisine eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-wtw-ph-iletkenlik-products.php` içine `WTW inoLab pH 7310P Set 2 Masa Tipi pH Metre` ürünü eklendi.
- Ürün URL'si: `/urun/wtw-inolab-ph-7310p-set-2-masa-tipi-ph-metre`.
- Ürün WTW markasına ve `ph-iletkenlik` kategorisine bağlıdır.
- Ürün gövdesine set içeriği, cihaz açıklaması, genel özellikler ve SenTix 41 elektrot bilgileri işlendi.
- Teknik özelliklerde cihaz ölçüm aralığı/hassasiyeti/ölçü/ağırlık ile SenTix 41 elektrotun pH aralığı, çalışma sıcaklığı, referans sistemi, membran, diyafram, şaft, sıcaklık sensörü ve bağlantı bilgileri yer alır.
- Broşür linki verilmediği için doküman alanı şimdilik boş bırakıldı.

### Notlar

- Görsel dosyası bulunamadı; ürün placeholder ile açılır.
- Önerilen ana görsel dosyası: `public/images/products/wtw-inolab-ph-7310p-set-2-masa-tipi-ph-metre.webp`.
- Yerel DB ürün toplamı 162 oldu; `ph-iletkenlik` kategorisinde WTW için 7 DB ürünü var.

### Doğrulama

- `php -l tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urun/wtw-inolab-ph-7310p-set-2-masa-tipi-ph-metre`, `/urunler/ph-iletkenlik`, `/urunler/marka/wtw` ve `/sitemap.xml`: 200 döndü.

## 2026-08-31 — WTW pH/ORP/Multi Parametre Ürünleri

Kullanıcının gönderdiği WTW portatif pH metre, ORP metre ve multi parametre cihazları `ph-iletkenlik` kategorisine eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-wtw-ph-iletkenlik-products.php` genişletildi.
- Kullanıcı kararı: Bu ürün batch'lerinde görsel klasörlerinde arama yapılmayacak; tüm ürünler placeholder ile geçilecek. URL yapısı sonra toplu istenecek, görseller kullanıcı tarafından toplu yüklenecek.
- Bu karar doğrultusunda WTW seed dosyasında `$imageFor` fonksiyonu her zaman `null` dönecek şekilde ayarlandı.
- Broşür linkleri henüz verilmediği için doküman alanları boş kaldı.
- Ürün açıklaması verilen ürünlerde açıklama, genel özellikler, set içeriği ve elektrot teknikleri işlendi.

### Eklenen Ürünler

- `WTW MonoLine pH 3310 IDS Portatif pH Metre` — `/urun/wtw-monoline-ph-3310-ids-portatif-ph-metre`
- `WTW ProfiLine pH 3110 Set 1 Sentix 21 Portatif pH Metre` — `/urun/wtw-profiline-ph-3110-set-1-sentix-21-portatif-ph-metre`
- `WTW ProfiLine pH 3110 Set 5 Sentix SP Portatif pH Metre` — `/urun/wtw-profiline-ph-3110-set-5-sentix-sp-portatif-ph-metre`
- `WTW inoLab Multi 9310 IDS Set C Masa Tipi pH/İletkenlik Ölçer` — `/urun/wtw-inolab-multi-9310-ids-set-c-masa-tipi-ph-iletkenlik-olcer`
- `WTW Multi 3510 Set 1 Sentix 940 Portatif pH Metre` — `/urun/wtw-multi-3510-set-1-sentix-940-portatif-ph-metre`
- `WTW ProfiLine pH 3210 Set 3 Sentix 81 Portatif pH Metre` — `/urun/wtw-profiline-ph-3210-set-3-sentix-81-portatif-ph-metre`
- `WTW ProfiLine pH 3110 Set 7 SenTix ORP Redoks Metre` — `/urun/wtw-profiline-ph-3110-set-7-sentix-orp-redoks-metre`
- `WTW ProfiLine pH 3110 Set 3 Sentix 81 Portatif pH Metre` — `/urun/wtw-profiline-ph-3110-set-3-sentix-81-portatif-ph-metre`

### Notlar

- `WTW ProfiLine pH 3210 Set 3` ürününde kullanıcı metnindeki “Üretimden Kalktı pH 3310 Öneriyoruz” bilgisi ürün özellikleri ve teknik tabloda belirtildi.
- `WTW inoLab Multi 9310 IDS Set C` ürününde stok kodu `LB.WTW.1FD56Cc` SKU olarak işlendi.
- Yerel DB ürün toplamı 170 oldu; `ph-iletkenlik` kategorisinde WTW için 15 DB ürünü var.

### Doğrulama

- `php -l tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- Yeni 8 ürün detay URL'si, `/urunler/ph-iletkenlik`, `/urunler/marka/wtw` ve `/sitemap.xml`: 200 döndü.

## 2026-08-31 — WTW pH Metre Set Varyantları

Kullanıcının devamında gönderdiği WTW pH 3310, pH 3110, inoLab pH 7110 ve inoLab pH 7310 varyantları aynı `ph-iletkenlik` seed dosyasına eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-wtw-ph-iletkenlik-products.php` genişletildi.
- Görsel arama yapılmaması kararı korunur; tüm yeni ürünlerde `image = null`, placeholder çalışır.
- Broşür linkleri verilmediği için doküman alanları boş bırakıldı.
- Sentix 41, Sentix 81 ve Sentix SP elektrot bilgileri ilgili set ürünlerinin teknik özelliklerine işlendi.
- Setli ve setsiz varyantlar ayrı slug'larla tutuldu; örneğin `inoLab pH 7310P Set 2` ile setsiz `inoLab pH 7310P` ayrı ürünlerdir.

### Eklenen Ürünler

- `WTW ProfiLine pH 3310 Set 3 Sentix 81 Portatif pH Metre` — `/urun/wtw-profiline-ph-3310-set-3-sentix-81-portatif-ph-metre`
- `WTW ProfiLine pH 3310 Set 2 Sentix 41 Portatif pH Metre` — `/urun/wtw-profiline-ph-3310-set-2-sentix-41-portatif-ph-metre`
- `WTW ProfiLine pH 3310 Set 5 Sentix SP Portatif pH Metre` — `/urun/wtw-profiline-ph-3310-set-5-sentix-sp-portatif-ph-metre`
- `WTW ProfiLine pH 3110 Set 2 Sentix 41 Portatif pH Metre` — `/urun/wtw-profiline-ph-3110-set-2-sentix-41-portatif-ph-metre`
- `WTW inoLab pH 7110 Set 2 Sentix 41 Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7110-set-2-sentix-41-masa-tipi-ph-metre`
- `WTW inoLab pH 7110 Set 4 Sentix 81 Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7110-set-4-sentix-81-masa-tipi-ph-metre`
- `WTW inoLab pH 7310 Set 2 Sentix 41 Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7310-set-2-sentix-41-masa-tipi-ph-metre`
- `WTW inoLab pH 7110 Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7110-masa-tipi-ph-metre`
- `WTW inoLab pH 7310 Set 4 Sentix 81 Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7310-set-4-sentix-81-masa-tipi-ph-metre`
- `WTW inoLab pH 7310P Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7310p-masa-tipi-ph-metre`
- `WTW inoLab pH 7310 Masa Tipi pH Metre` — `/urun/wtw-inolab-ph-7310-masa-tipi-ph-metre`

### Notlar

- `WTW inoLab pH 7110 Set 2` kullanıcı başlığındaki Set 2 / Sentix 41 bilgisinden alınmıştır; tabloda geçen Set 4 ifadesi başlıkla çeliştiği için slug ve ürün adı başlığa göre düzenlendi.
- `WTW inoLab pH 7110` setsiz ürününde kullanıcı notu teknik özelliklere eklendi: uygun elektrot ve tamponlar ayrıca alınmalıdır.
- Yerel DB ürün toplamı 181 oldu; `ph-iletkenlik` kategorisinde WTW için 26 DB ürünü var.

### Doğrulama

- `php -l tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- Yeni 10 ürün detay URL'si, `/urunler/ph-iletkenlik`, `/urunler/marka/wtw` ve `/sitemap.xml`: 200 döndü. `WTW ProfiLine pH 3310 Set 3` de bir önceki doğrulamada 200 dönmüştü.

## 2026-08-31 — WTW inoLab Cond İletkenlik Ölçerler

Kullanıcının gönderdiği WTW masa tipi iletkenlik ölçer ürünleri `ph-iletkenlik` kategorisine eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-wtw-ph-iletkenlik-products.php` genişletildi.
- Görsel arama yapılmaması kararı korunur; yeni Cond ürünleri de placeholder ile açılır.
- Broşür linkleri verilmediği için doküman alanları boş bırakıldı.
- İletkenlik, TDS, tuzluluk, sıcaklık, hücre sabiti ve set içeriği alanları teknik özelliklere işlendi.

### Eklenen Ürünler

- `WTW inoLab Cond 7110 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-cond-7110-masa-tipi-iletkenlik-olcer`
- `WTW inoLab Cond 7310 Set 1 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-cond-7310-set-1-masa-tipi-iletkenlik-olcer`

### Notlar

- Yerel DB ürün toplamı 183 oldu; `ph-iletkenlik` kategorisinde WTW için 28 DB ürünü var.

### Doğrulama

- `php -l tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- İki ürün detay URL'si, `/urunler/ph-iletkenlik`, `/urunler/marka/wtw` ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — WTW İletkenlik Setleri ve SI Analitik Elektrodları

Kullanıcının devamında gönderdiği WTW iletkenlik/multi-parametre setleri ve SI Analitik elektrod listeleri site veritabanına işlendi.

### Eklenen / Değiştirilenler

- `tools/seed-wtw-ph-iletkenlik-products.php` genişletildi.
- Yeni dosya: `tools/seed-si-analitik-elektrodlari.php`.
- `tools/seed-cozunmus-oksijen-elektrodlari.php` OX 1100+ için korunuyor; kapsamlı yeni elektrod seed'i aynı kaydı da idempotent olarak güncelliyor.
- `app/Http/Controllers/SiteController.php` içinde kategori SEO normalizer'ı genelleştirildi; DB'den eklenen yeni kategoriler özel SEO bloğu olmasa da `primary_cta`, `secondary_cta`, liste bölümleri ve boş durum alanlarıyla 500 vermeden açılır.
- Görsel arama yapılmaması kararı korunur; yeni WTW ve SI Analitik ürünlerinin tamamı placeholder görselle açılır.
- Broşür linki verilmediği için doküman alanları boş bırakıldı.

### Eklenen WTW Ürünleri

- `WTW inoLab Cond 7110 Set 1 TetraCon 325 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-cond-7110-set-1-tetracon-325-masa-tipi-iletkenlik-olcer`
- `WTW inoLab Cond 7310 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-cond-7310-masa-tipi-iletkenlik-olcer`
- `WTW inoLab Multi 9310 IDS Set 3 TetraCon 925 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-multi-9310-ids-set-3-tetracon-925-masa-tipi-iletkenlik-olcer`
- `WTW ProfiLine Cond 3310 Set 1 TetraCon 325 Portatif İletkenlik Ölçer` — `/urun/wtw-profiline-cond-3310-set-1-tetracon-325-portatif-iletkenlik-olcer`
- `WTW inoLab Cond 7310P Set 4 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-cond-7310p-set-4-masa-tipi-iletkenlik-olcer`
- `WTW inoLab Cond 7310P Set 1 Masa Tipi İletkenlik Ölçer` — `/urun/wtw-inolab-cond-7310p-set-1-masa-tipi-iletkenlik-olcer`
- `WTW ProfiLine Cond 3110 Set 1 TetraCon 325 Portatif İletkenlik Ölçer` — `/urun/wtw-profiline-cond-3110-set-1-tetracon-325-portatif-iletkenlik-olcer`
- `WTW ProfiLine Cond 3210 Set 1 TetraCon 325 Portatif İletkenlik Ölçer` — `/urun/wtw-profiline-cond-3210-set-1-tetracon-325-portatif-iletkenlik-olcer`
- `WTW Multi 3630 Set G Portatif pH/İletkenlik/Oksijen Ölçer` — `/urun/wtw-multi-3630-set-g-portatif-ph-iletkenlik-oksijen-olcer`
- `WTW ProfiLine Cond 3210 Set 4 Saf Su Portatif İletkenlik Ölçer` — `/urun/wtw-profiline-cond-3210-set-4-saf-su-portatif-iletkenlik-olcer`
- `WTW ProfiLine Cond 3210 Set 3 KLE 325 Portatif İletkenlik Ölçer` — `/urun/wtw-profiline-cond-3210-set-3-kle-325-portatif-iletkenlik-olcer`
- `WTW MonoLine Cond 3310 IDS Portatif İletkenlik Ölçer` — `/urun/wtw-monoline-cond-3310-ids-portatif-iletkenlik-olcer`
- `WTW Multi 3510 Set 3 TetraCon 925 Portatif İletkenlik Ölçer` — `/urun/wtw-multi-3510-set-3-tetracon-925-portatif-iletkenlik-olcer`
- `WTW ProfiLine Cond 3310 IDS Set 3 LR 325/01 Portatif İletkenlik Ölçer` — `/urun/wtw-profiline-cond-3310-ids-set-3-lr-325-01-portatif-iletkenlik-olcer`

### Eklenen SI Analitik Elektrod Kategorileri

- `Çözünmüş Oksijen Ölçüm Elektrodları` — `/urunler/cozunmus-oksijen-olcum-elektrodlari`
- `Fotometrik Ölçüm Elektrodu` — `/urunler/fotometrik-olcum-elektrodu`
- `İletkenlik Ölçüm Elektrodları` — `/urunler/iletkenlik-olcum-elektrodlari`
- `İyon Seçici Elektrodlar` — `/urunler/iyon-secici-elektrodlar`
- `Kombine pH Elektrodları` — `/urunler/kombine-ph-elektrodlari`
- `Metal Kombine Elektrodlar` — `/urunler/metal-kombine-elektrodlar`

### Eklenen SI Analitik Ürünleri

- OX 1100+ ve OptiLine 6 ayrı ürün olarak eklendi.
- İletkenlik Ölçüm Elektrodları altında 6 ürün eklendi: BlueLine 48 LF, LF 1100 T+, LF 413 T, LF 413 T-3, LF 513 T, LF 613 T.
- İyon Seçici Elektrodlar altında 18 ürün eklendi.
- Kombine pH Elektrodları altında BlueLine, TopLine ve Memosens seri ürünleri eklendi.
- Metal Kombine Elektrodlar altında 18 ürün eklendi.

### Sayılar

- Yerel DB ürün toplamı 244 oldu.
- `ph-iletkenlik` kategorisinde WTW için 42 DB ürünü var.
- Yeni SI Analitik elektrod kategorilerinde 47 ürün var.

### Doğrulama

- `php -l tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php -l tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php tools/seed-wtw-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- Yeni WTW ürünlerinden 11 temsilci detay URL'si, SI Analitik ürünlerinden 5 temsilci detay URL'si, yeni 6 kategori URL'si, `/urunler/ph-iletkenlik`, `/urunler/marka/wtw`, `/urunler/marka/si-analitik` ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — SI Analitik Referans Elektrodlar

Kullanıcının gönderdiği referans elektrod listesi SI Analitik elektrod seed dosyasına eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-si-analitik-elektrodlari.php` genişletildi.
- Yeni kategori: `Referans Elektrodlar` — `/urunler/referans-elektrodlar`.
- B 2220+, B 2420+, B 2810+, B 2820+, B 2910+, B 2920+, B 3410+, B 3420+, B 3510+, B 3520+, B 3610+ ve B 3920+ ayrı ürün olarak eklendi.
- Katalog numaraları SKU olarak işlendi.
- Görsel arama yapılmaması kararı korunur; tüm referans elektrod ürünleri placeholder ile açılır.
- Broşür linki verilmediği için doküman alanları boş bırakıldı.

### Sayılar

- Yerel DB ürün toplamı 256 oldu.
- `referans-elektrodlar` kategorisinde 12 ürün var.
- Yeni SI Analitik elektrod kategorilerinde toplam 59 ürün var.

### Doğrulama

- `php -l tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/referans-elektrodlar`, üç temsilci referans elektrod ürün URL'si, `/urunler/marka/si-analitik` ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — SI Analitik Sıcaklık Ölçüm Elektrodları

Kullanıcının gönderdiği sıcaklık ölçüm elektrod listesi SI Analitik elektrod seed dosyasına eklendi.

### Eklenen / Değiştirilenler

- `tools/seed-si-analitik-elektrodlari.php` genişletildi.
- Yeni kategori: `Sıcaklık Ölçüm Elektrodları` — `/urunler/sicaklik-olcum-elektrodlari`.
- W 5780 NN, W 5791 NN, W 5790 NN, W 5790 PP ve W 5980 NN ayrı ürün olarak eklendi.
- Katalog numaraları SKU olarak işlendi.
- Görsel arama yapılmaması kararı korunur; tüm sıcaklık elektrod ürünleri placeholder ile açılır.
- Broşür linki verilmediği için doküman alanları boş bırakıldı.

### Sayılar

- Yerel DB ürün toplamı 261 oldu.
- `sicaklik-olcum-elektrodlari` kategorisinde 5 ürün var.
- Yeni SI Analitik elektrod kategorilerinde toplam 64 ürün var.

### Doğrulama

- `php -l tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/sicaklik-olcum-elektrodlari`, üç temsilci sıcaklık elektrodu ürün URL'si, `/urunler/marka/si-analitik` ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — SI Analitik Elektrod Bağlantı Kabloları

Kullanıcının `100.png` görselinden gönderdiği elektrod bağlantı kabloları tablosu Türkçeleştirilerek ürünleştirildi.

### Eklenen / Değiştirilenler

- `tools/seed-si-analitik-elektrodlari.php` genişletildi.
- Yeni kategori: `Elektrod Bağlantı Kabloları` — `/urunler/elektrod-baglanti-kablolari`.
- B 1 N, L 1 A, L 1 BNC, L 2 N, L 1 N, L 1 NN, L 2 A ve L 2 NN ayrı ürün olarak eklendi.
- Sipariş numaraları SKU olarak işlendi.
- Görseldeki İngilizce alanlar Türkçeleştirildi: elektrod soketi/fişi, cihaz konnektörü/fişi, kablo uzunluğu ve tipi.
- Görseller placeholder olarak bırakıldı; broşür/doküman alanları boş.

### Sayılar

- Yerel DB ürün toplamı 269 oldu.
- `elektrod-baglanti-kablolari` kategorisinde 8 ürün var.
- Yeni SI Analitik elektrod kategorilerinde toplam 72 ürün var.

### Doğrulama

- `php -l tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php tools/seed-si-analitik-elektrodlari.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/elektrod-baglanti-kablolari`, üç temsilci bağlantı kablosu ürün URL'si, `/urunler/marka/si-analitik` ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Titratör Kategori Ağacı ve SI Analitik Titratörler

Kullanıcının kategori yönlendirmesine göre titratör ve elektrod kategori hiyerarşisi kalıcı config tarafında güncellendi; SI Analitik TITRONIC/TitroLine ürünleri eklendi.

### Eklenen / Değiştirilenler

- `config/mta.php` güncellendi.
- `Titratörler` ana kategori yapıldı: `/urunler/titratorler`.
- `Kral Fischer` ve `Potansiyometrik Titratörler`, `Titratörler` altında konumlandırıldı.
- `Piston Büretler`, `Potansiyometrik Titratörler` alt kategorisi olarak eklendi.
- Elektrod üst kategori kuralı işlendi:
  - `Çözünmüş Oksijen Ölçüm Elektrodları`, `İletkenlik Ölçüm Elektrodları`, `İyon Seçici Elektrodlar`, `Kombine pH Elektrodları` ve belirtilmediği için `Elektrod Bağlantı Kabloları` → `pH Metre` altında.
  - `Fotometrik Ölçüm Elektrodu`, `Metal Kombine Elektrodlar`, `Referans Elektrodlar`, `Sıcaklık Ölçüm Elektrodları` → `Potansiyometrik Titratörler` altında.
- Yeni seed dosyası: `tools/seed-si-analitik-titratorler.php`.
- `Piston Büretler` altında TITRONIC 300 ve TITRONIC 500 eklendi.
- `Potansiyometrik Titratörler` altında TitroLine 5000, TitroLine 7000, TitroLine 7750 ve TitroLine 7800 eklendi.
- TitroLine 7000 ve TitroLine 7800 YouTube linkleri ürün videosu olarak işlendi.
- Kullanıcının görsel talimatı korunur: ürün görselleri aranmadan placeholder/boş bırakıldı.

### Sayılar

- Yerel DB ürün toplamı 275 oldu.
- `potansiyometrik-titratorler` kategorisinde 4 yeni TitroLine ürünü var.
- `piston-buretler` kategorisinde 2 TITRONIC ürünü var.
- Yeni ürün videosu sayısı 2.

### Doğrulama

- `php -l config/mta.php`: geçti.
- `php -l tools/seed-si-analitik-titratorler.php`: geçti.
- `php tools/seed-si-analitik-titratorler.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/titratorler`, `/urunler/potansiyometrik-titratorler`, `/urunler/piston-buretler`, `/urun/titroline-5000-potansiyometrik-titrator`, `/urun/titronic-500-piston-buret`, `/urunler/ph-metre`, `/urunler/fotometrik-olcum-elektrodu`, `/urunler/elektrod-baglanti-kablolari` ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Kulometrik Karl Fischer Titratörler

Kullanıcının gönderdiği TitroLine 7500 KF trace modülleri ve ilgili Karl Fischer aksesuar ürünleri titratör ağacına eklendi.

### Eklenen / Değiştirilenler

- `config/mta.php` güncellendi.
- Yeni kategori: `Karl Fischer Titratörler` — `/urunler/karl-fischer-titratorler`; parent: `Titratörler`.
- Yeni kategori: `Kulometrik Karl Fischer Titratörler` — `/urunler/kulometrik-karl-fischer-titratorler`; parent: `Karl Fischer Titratörler`.
- `tools/seed-si-analitik-titratorler.php` genişletildi.
- `Kulometrik Karl Fischer Titratörler` altında 10 ürün eklendi:
  - TitroLine 7500 KF trace, Modül 1
  - TitroLine 7500 KF trace, Modül 2
  - TitroLine 7500 KF trace, Modül 3
  - TitroLine 7500 KF trace, Modül 4
  - TitroLine 7500 KF trace, Modül 5
  - TitroLine 7500 KF trace, Modül 6
  - TitroLine 7500 KF trace, Modül 6, TitriSoft Pharma
  - TO 7280 Kulometrik Karl Fischer Titrasyonu için Fırın
  - TW 7650 Otomatik Numune Değiştirici
  - TW 7650 Otomatik Numune Değiştirici, TitriSoft Yazılımı ile
- TitroLine 7500 KF trace modüllerine `vdfx95qFmDA` YouTube videosu işlendi; TitriSoft Pharma modülüne video verilmediği için video eklenmedi.
- TO 7280 için `skRjUMEprrA`, TW 7650 TitriSoft için `HXGS9Bd6clk` YouTube videosu işlendi.
- TO 7280 ve TW 7650 ürünlerinin detay içeriği kullanıcı görsel gönderdiğinde Türkçeye çevrilerek tamamlanacak şekilde notlandı.
- Görsel arama yapılmaması kararı korunur; tüm yeni ürünlerde görsel alanı placeholder/boş bırakıldı.

### Sayılar

- Yerel DB ürün toplamı 285 oldu.
- `kulometrik-karl-fischer-titratorler` kategorisinde 10 ürün var.
- `potansiyometrik-titratorler` kategorisinde 4 ürün, `piston-buretler` kategorisinde 2 ürün var.
- Ürün videosu toplamı 11 oldu.

### Doğrulama

- `php -l config/mta.php`: geçti.
- `php -l tools/seed-si-analitik-titratorler.php`: geçti.
- `php tools/seed-si-analitik-titratorler.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/titratorler`, `/urunler/karl-fischer-titratorler`, `/urunler/kulometrik-karl-fischer-titratorler`, 10 yeni ürün detay URL'si ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — TO 7280 ve TW 7650 Görselden İçerik Güncellemesi

Kullanıcının gönderdiği `to7280.jpg`, `to7650 otomatik.jpg` ve `to7650.jpg` görsellerindeki İngilizce teknik tablolar Türkçeye çevrilerek ilgili ürünlere işlendi.

### Eklenen / Değiştirilenler

- `tools/seed-si-analitik-titratorler.php` güncellendi.
- TO 7280 ürününe numune dozajlama, ölçüm aralığı, çözünürlük, tekrarlanabilirlik, sıcaklık aralığı, güç, ölçü, ağırlık ve ortam koşulları işlendi.
- TW 7650 ve TW 7650 TitriSoft ürünlerine pozisyon sayısı, TO 7280 üzerinden güç beslemesi/güç girişi, ölçü, ağırlık ve ortam koşulları işlendi.
- Önceki `Görsel bekleniyor` metadata notları bu üç üründe `Görselden Türkçeye çevrildi` olarak güncellendi.
- Görseller ürün görseli olarak kullanılmadı; placeholder/boş görsel talimatı korunur.

### Doğrulama

- `php -l tools/seed-si-analitik-titratorler.php`: geçti.
- `php tools/seed-si-analitik-titratorler.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- TO 7280, TW 7650 ve TW 7650 TitriSoft ürün detay URL'leri 200 döndü ve yeni çevrilmiş içerik sayfalarda göründü.

## 2026-09-01 — Volümetrik Karl Fischer Titratörler

Kullanıcının gönderdiği TitroLine 7500 KF ürünü Karl Fischer titratör ağacına eklendi.

### Eklenen / Değiştirilenler

- `config/mta.php` güncellendi.
- Yeni kategori: `Volümetrik Karl Fischer Titratörler` — `/urunler/volumetrik-karl-fischer-titratorler`; parent: `Karl Fischer Titratörler`.
- `tools/seed-si-analitik-titratorler.php` genişletildi.
- Yeni ürün: `TitroLine 7500 KF Volümetrik Karl Fischer Titratör` — `/urun/titroline-7500-kf-volumetrik-karl-fischer-titrator`.
- Ölçüm aralığı, dozajlama hassasiyeti, metot sayısı, aplikasyonlar, grafik, yazıcı, terazi bağlantısı, akıllı büret ünitesi, arabirimler, TM 235 KF manyetik karıştırıcı ve opsiyonel TitriSoft 3.0 bilgileri işlendi.
- Görsel arama yapılmadı; görsel alanı placeholder/boş bırakıldı.

### Sayılar

- Yerel DB ürün toplamı 286 oldu.
- `volumetrik-karl-fischer-titratorler` kategorisinde 1 ürün var.
- `kulometrik-karl-fischer-titratorler` kategorisinde 10 ürün var.

### Doğrulama

- `php -l config/mta.php`: geçti.
- `php -l tools/seed-si-analitik-titratorler.php`: geçti.
- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php tools/seed-si-analitik-titratorler.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/karl-fischer-titratorler`, `/urunler/volumetrik-karl-fischer-titratorler`, yeni ürün detay URL'si ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Erime Noktası Minimal Ürünleri

Kullanıcının gönderdiği 5 Cole-Parmer Stuart erime noktası cihazı sadece isim/model bazlı minimal ürün olarak eklendi.

### Eklenen / Değiştirilenler

- Yeni seed dosyası: `tools/seed-erime-noktasi-products.php`.
- Kategori: `Erime Noktası` — `/urunler/erime-noktasi`.
- Marka adı `Cole-Parmer Stuart` (slug `stuart`, eski alias `Stuart` korunur); ürün adlarında `Cole-Parmer Stuart` aynen kullanılıyor.
- Eklenen ürünler:
  - Cole-Parmer Stuart MP-200D | Dijital Erime Noktası Tayin Cihazı
  - Cole-Parmer Stuart MP-250D | Dijital Erime Noktası Tayin Cihazı
  - Cole-Parmer Stuart MP-400D | Dijital Erime Noktası Tayin Cihazı
  - Cole-Parmer Stuart SMP50 | Dijital Erime Noktası Tayin Cihazı
  - Cole-Parmer Stuart MP-100 | Analog Erime Noktası Tayin Cihazı
- İçerik, teknik bilgi, doküman ve görsel daha sonra tamamlanacak şekilde minimal bırakıldı.
- Görsel arama yapılmadı; görsel alanları placeholder/boş.

### Sayılar

- Yerel DB ürün toplamı 291 oldu.
- `erime-noktasi` kategorisinde 5 ürün var.

### Doğrulama

- `php -l tools/seed-erime-noktasi-products.php`: geçti.
- `php tools/seed-erime-noktasi-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/erime-noktasi`, iki temsilci ürün detay URL'si ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Polarimetreler

Kullanıcının gönderdiği D7 ve ADP serisi polarimetre ürünleri eklendi.

### Eklenen / Değiştirilenler

- Yeni seed dosyası: `tools/seed-polarimetreler-products.php`.
- Kategori: `Polarimetreler` — `/urunler/polarimetreler`.
- Marka, mevcut kategori eşleşmesine uygun olarak `Bellingham + Stanley` kullanıldı.
- Eklenen ürünler:
  - D7 Polarimetre
  - ADP 430 Dijital Polarimetre
  - ADP 450 Dijital Polarimetre
  - ADP 610 Dijital Polarimetre
  - ADP 620 Dijital Polarimetre
  - ADP 622 Dijital Polarimetre
  - ADP 640 Dijital Polarimetre
  - ADP 650 Dijital Polarimetre
  - ADP 660 Dijital Polarimetre
- Kullanıcının verdiği LED ışık kaynağı, ölçüm aralığı, dalga boyu, Peltier sıcaklık kontrolü, 21 CFR Bölüm 11 uyumu ve 7.4 inç dokunmatik ekran bilgileri ürünlere işlendi.
- ADP 450 için YouTube videosu `WR3Sdb_v7Fk` eklendi.
- Görsel arama yapılmadı; görsel alanları placeholder/boş.

### Sayılar

- Yerel DB ürün toplamı 300 oldu.
- `polarimetreler` kategorisinde 9 ürün var.
- ADP 450 ürününde 1 video var.

### Doğrulama

- `php -l tools/seed-polarimetreler-products.php`: geçti.
- `php tools/seed-polarimetreler-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/polarimetreler`, D7, ADP 450 ve ADP 660 ürün detay URL'leri ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Laboratuvar Tipi Refraktometreler

Kullanıcının gönderdiği RFM ve Abbe refraktometre ürünleri `Refraktometre` ana kategorisinin altına eklendi.

### Eklenen / Değiştirilenler

- `config/mta.php` güncellendi.
- Yeni alt kategori: `Laboratuvar Tipi Refraktometreler` — `/urunler/laboratuvar-tipi-refraktometreler`; parent: `Refraktometre`.
- `app/Http/Controllers/SiteController.php` kategori kartı akışına yeni slug eklendi.
- Yeni seed dosyası: `tools/seed-refraktometreler-products.php`.
- Marka, mevcut refraktometre eşleşmesine uygun olarak `Bellingham + Stanley` kullanıldı.
- Eklenen ürünler:
  - RFM712-M Laboratuvar Tipi Refraktometre
  - RFM732-M Laboratuvar Tipi Refraktometre
  - RFM742-M Laboratuvar Tipi Refraktometre
  - RFM330-M Laboratuvar Tipi Refraktometre
  - RFM330-T Laboratuvar Tipi Refraktometre
  - RFM340-M Laboratuvar Tipi Refraktometre
  - RFM340-T Laboratuvar Tipi Refraktometre
  - RFM960-T Laboratuvar Tipi Refraktometre
  - RFM970-T Laboratuvar Tipi Refraktometre
  - Abbe 5 Laboratuvar Tipi Refraktometre
- Kullanıcının verdiği ölçüm aralığı, çözünürlük ve 21 CFR Bölüm 11 uyumu bilgileri ürünlere işlendi.
- RFM340-M ve RFM340-T için YouTube videosu `Tjcaw7CLN-E` eklendi.
- Görsel arama yapılmadı; görsel alanları placeholder/boş.

### Sayılar

- Yerel DB ürün toplamı 310 oldu.
- `laboratuvar-tipi-refraktometreler` kategorisinde 10 ürün var.
- RFM340-M/RFM340-T için toplam 2 video kaydı var.

### Doğrulama

- `php -l config/mta.php`: geçti.
- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php -l tools/seed-refraktometreler-products.php`: geçti.
- `php tools/seed-refraktometreler-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/refraktometre`, `/urunler/laboratuvar-tipi-refraktometreler`, RFM712-M, RFM340-M, RFM970-T, Abbe 5 ürün detay URL'leri ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Taşınabilir Dijital Refraktometreler ve Yoğunluk Ölçerler

Kullanıcının gönderdiği OPTi taşınabilir dijital refraktometre grupları `Refraktometre` ana kategorisinin altına, DSG serisi yoğunluk ölçerler ise `Densitometre` ana kategorisinin altına eklendi.

### Eklenen / Değiştirilenler

- `config/mta.php` güncellendi.
- `app/Http/Controllers/SiteController.php` kategori kartı akışına `tasinabilir-tip-dijital-refraktometreler` ve `yogunluk-olcerler` slugları eklendi.
- Önceki `tools/seed-tasinabilir-optik-refraktometreler.php` ürün detay hatası düzeltildi: `Katalog seçenekleri` artık iç içe array yerine düz metin olarak saklanıyor.
- Yeni seed dosyası: `tools/seed-tasinabilir-dijital-refraktometreler.php`.
- Yeni alt kategori: `Taşınabilir Tip Dijital Refraktometreler` — `/urunler/tasinabilir-tip-dijital-refraktometreler`; parent: `Refraktometre`.
- Eklenen OPTi ürün grupları:
  - OPTi Endüstriyel Isı Transferi Taşınabilir Dijital Refraktometreler
  - OPTi Endüstriyel Otomotiv Taşınabilir Dijital Refraktometreler
  - OPTi Endüstriyel Genel Kullanım Taşınabilir Dijital Refraktometreler
  - OPTi Yaşam Bilimleri Taşınabilir Dijital Refraktometreler
  - OPTi Çift Skalalı Modeller Taşınabilir Dijital Refraktometreler
  - OPTi Tek Skalalı Modeller Taşınabilir Dijital Refraktometreler
  - OPTi Bira-Şarap Taşınabilir Dijital Refraktometreler
  - OPTi Yaşam Bilimleri-Veterinerlik Taşınabilir Dijital Refraktometreler
  - OPTi Gıda-İçecek Taşınabilir Dijital Refraktometreler
  - OPTi Endüstriyel Taşınabilir Dijital Refraktometreler
  - OPTi Otomotiv Taşınabilir Dijital Refraktometreler
- Yeni seed dosyası: `tools/seed-yogunluk-olcerler-products.php`.
- Yeni alt kategori: `Yoğunluk Ölçerler` — `/urunler/yogunluk-olcerler`; parent: `Densitometre`.
- Eklenen ürün: `Bellingham+Stanley DSG Serisi Yoğunluk Ölçerler`.
- DSG 40 / DSG 50 ölçüm aralığı, çözünürlük, hassasiyet, tekrarlanabilirlik, sıcaklık aralığı ve 21 CFR Bölüm 11 uyumu ürün specs alanına işlendi.
- Görsel arama yapılmadı; görsel alanları placeholder/boş.

### Sayılar

- Yerel DB ürün toplamı 324 oldu.
- `tasinabilir-tip-optik-refraktometreler` kategorisinde 2 ürün var.
- `tasinabilir-tip-dijital-refraktometreler` kategorisinde 11 ürün var.
- `yogunluk-olcerler` kategorisinde 1 ürün var.

### Doğrulama

- `php -l config/mta.php`: geçti.
- `php -l app/Http/Controllers/SiteController.php`: geçti.
- `php -l tools/seed-tasinabilir-optik-refraktometreler.php`: geçti.
- `php -l tools/seed-tasinabilir-dijital-refraktometreler.php`: geçti.
- `php -l tools/seed-yogunluk-olcerler-products.php`: geçti.
- `php tools/seed-tasinabilir-optik-refraktometreler.php`: geçti.
- `php tools/seed-tasinabilir-dijital-refraktometreler.php`: geçti.
- `php tools/seed-yogunluk-olcerler-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/refraktometre`, `/urunler/tasinabilir-tip-optik-refraktometreler`, E-Line, Eclipse, `/urunler/tasinabilir-tip-dijital-refraktometreler`, OPTi temsilci ürünleri, `/urunler/densitometre`, `/urunler/yogunluk-olcerler`, DSG ürün detayı ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Ohaus ve Mettler Toledo pH & İletkenlik Ürünleri

Kullanıcının gönderdiği Ohaus AquaSearcher ve Mettler Toledo SevenDirect ürünleri `pH İletkenlik & Metreler` kategorisine eklendi.

### Eklenen / Değiştirilenler

- Yeni seed dosyası: `tools/seed-ohaus-ph-iletkenlik-products.php`.
- Yeni seed dosyası: `tools/seed-mettler-toledo-ph-iletkenlik-products.php`.
- Kategori: `pH İletkenlik & Metreler` — `/urunler/ph-iletkenlik`.
- Ohaus ürünlerinde XML'den üretilmiş normalize import verisi teknik alanlar için destek olarak kullanıldı; kullanıcıdan gelen son belge URL'leri esas alındı.
- Mettler Toledo SD23 ve SD30 ürünlerinde normalize import verisi teknik alanlar için destek olarak kullanıldı; SD23 Pure H2O kit içeriği kullanıcı verisine göre oluşturuldu.
- Eklenen Ohaus ürünleri:
  - OHAUS a-AB23EC-F Masa Tipi İletkenlik Ölçer
  - OHAUS a-AB23PH-F Masa Tipi pH Metre
  - OHAUS a-AB33EC-F Masa Tipi İletkenlik Ölçer
  - OHAUS a-AB33M1-F Masa Tipi Multiparametre Ölçer
  - OHAUS a-AB33PH-F Masa Tipi pH Metre
  - OHAUS a-AB41PH-F Masa Tipi pH Metre
- Eklenen Mettler Toledo ürünleri:
  - Mettler Toledo SD23-Standart Kit SevenDirect
  - Mettler Toledo SD30-Kit SevenDirect
  - Mettler Toledo SD23 Pure H2O-Kit SevenDirect
- Toplam 21 belge linki eklendi.
- Görsel arama yapılmadı; görsel alanları placeholder/boş.

### Sayılar

- Yerel DB ürün toplamı 333 oldu.
- `ph-iletkenlik` kategorisinde 51 ürün var.

### Doğrulama

- `php -l tools/seed-ohaus-ph-iletkenlik-products.php`: geçti.
- `php -l tools/seed-mettler-toledo-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-ohaus-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-mettler-toledo-ph-iletkenlik-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/ph-iletkenlik` ve 9 yeni ürün detay URL'si 200 döndü; temsilci ürün içerikleri sayfalarda bulundu.

## 2026-09-01 — XML'deki Tüm Ohaus ve Mettler Toledo Ürünleri

Kullanıcının "xmldeki ohaus ve mettler toledo ürünlerinin hepsini sisteme yükle" isteğiyle ham WordPress XML içindeki yayındaki tüm Ohaus ve Mettler Toledo ürünleri veritabanına aktarıldı. Normalize JSON tek başına 62 ürün içeriyordu; ham XML kontrolünde 70 ürün bulundu ve eksik kalan Ohaus karıştırıcı ürünleri de dahil edildi.

### Eklenen / Değiştirilenler

- Yeni toplu import dosyası: `tools/import-ohaus-mettler-from-xml.php`.
- Yeni tamamlayıcı seed dosyası: `tools/seed-ohaus-portatif-ph-products.php`.
- `tools/seed-mettler-toledo-ph-iletkenlik-products.php` genişletildi:
  - Mettler Toledo SD20 Kit-SevenDirect
  - Mettler Toledo SD20 HA Kit SevenDirect
  - Mettler Toledo SD50 HA Kit SevenDirect
  - Mettler Toledo SevenDirect SD50 Kit
  - önceki SD23 Standart, SD30 ve SD23 Pure H2O ürünleri
- XML kaynaklı Ohaus/Mettler ürünleri kendi kategorilerine göre aktarıldı:
  - `ph-iletkenlik`
  - `densitometre`
  - `hassas-teraziler`
  - `mekanik-karistirici`
  - `isitmali-manyetik-karistirici`
- Kullanıcının ayrıca gönderdiği portatif Ohaus ürünleri gerçek belge linkleriyle eklendi/güncellendi:
  - OHAUS ST300-G Portatif pH Metre
  - OHAUS ST400-G Portatif pH Metre
  - OHAUS ST400D-G Portatif Çözünmüş Oksijen Metre
  - OHAUS ST400M-G Portatif Multiparametre Ölçer
  - Ohaus ST 300 C-G Portatif İletkenlik Ölçer
  - Ohaus ST 300 D-G Portatif Oksijen Metre
- Mettler Toledo NTC 30 kΩ sıcaklık sensörü slugı okunabilir canonical biçime alındı:
  - `/urun/mettler-toledo-inlab-ntc-30-k-ohm-sicaklik-sensoru-sertifikali`
- ST400D için uzun XML slugı kısa ürün slugına yönlendirildi:
  - `/urun/ohaus-st400d-g`
- Mevcut gerçek doküman URL'leri korunacak şekilde import yapıldı; yeni bulunan dokümanlar `product_documents` tablosuna eklendi.
- Görsel klasörü aranmadı; ürün görsel alanları kullanıcı notuna uygun olarak boş/placeholder bırakıldı.

### Sayılar

- Ham XML'de bulunan yayındaki marka ürünleri: 70 kayıt.
- Veritabanında yayındaki Ohaus ürünleri: 33.
- Veritabanında yayındaki Mettler Toledo ürünleri: 45.
- Toplam ürün: 402.
- Ohaus/Mettler doküman kaydı: 144.
- Ohaus/Mettler video kaydı: 1.

Not: DB marka toplamı 78; bu sayı XML'deki 70 kayda ek olarak kullanıcının mesajla verdiği tamamlayıcı Ohaus portatif ürünleri ve Mettler Toledo HA/Pure H2O varyantlarını da içerir.

### Doğrulama

- `php -l tools/import-ohaus-mettler-from-xml.php`: geçti.
- `php -l tools/seed-ohaus-portatif-ph-products.php`: geçti.
- `php -l tools/seed-ohaus-ph-iletkenlik-products.php`: geçti.
- `php -l tools/seed-mettler-toledo-ph-iletkenlik-products.php`: geçti.
- `php tools/import-ohaus-mettler-from-xml.php`: geçti, 70 XML kaydı işlendi.
- `php tools/seed-ohaus-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-mettler-toledo-ph-iletkenlik-products.php`: geçti.
- `php tools/seed-ohaus-portatif-ph-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/ph-iletkenlik`, Ohaus AB23EC, Ohaus CX 1201, Mettler S2 Field Kit, Mettler S8 densitometre kaydı, Mettler NTC 30 k-ohm sensör, Mettler SD20 HA, Mettler SD50 HA, Mettler SD23 Pure H2O, Ohaus ST300/ST400 portatif ürünleri ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Shimadzu ve Ohaus Nem Tayin Ürünleri

Kullanıcının gönderdiği Shimadzu MOC-63u ve Ohaus MB/MC serisi nem tayin ürünleri `Nem Tayin` kategorisine eklendi.

### Eklenen / Değiştirilenler

- Yeni seed dosyası: `tools/seed-nem-tayin-ohaus-shimadzu-products.php`.
- Kategori: `Nem Tayin` — `/urunler/nem-tayin`.
- Eklenen ürünler:
  - Shimadzu MOC-63u Nem Tayin Cihazı
  - Ohaus MB32 Nem Tayin Cihazı
  - Ohaus MB62 Nem Tayin Cihazı
  - Ohaus MB92 Nem Tayin Cihazı
  - Ohaus MC 2000 Tahıl Nem Ölçer
- Shimadzu MOC-63u için katalog ve YouTube video kaydı eklendi.
- Ohaus MB32/MB62/MB92 için ürün broşürü ve CE belgesi linkleri eklendi.
- Ohaus MC 2000 için kullanıcı metninde belge linki olmadığı için doküman eklenmedi; teknik özellikler ve tarımsal kullanım açıklaması işlendi.
- Görsel arama yapılmadı; ürün görsel alanları placeholder/boş.

### Sayılar

- Yerel DB ürün toplamı 407 oldu.
- `nem-tayin` kategorisinde 6 ürün var.
- Yayındaki Ohaus ürünleri: 37.
- Yayındaki Mettler Toledo ürünleri: 45.
- Yayındaki Shimadzu nem tayin ürünü: 1.

### Doğrulama

- `php -l tools/seed-nem-tayin-ohaus-shimadzu-products.php`: geçti.
- `php tools/seed-nem-tayin-ohaus-shimadzu-products.php`: geçti.
- `php artisan test`: geçti, 13 test / 83 assertion.
- `/urunler/nem-tayin`, Shimadzu MOC-63u, Ohaus MB32, MB62, MB92, Ohaus MC 2000 ve `/sitemap.xml`: 200 döndü.

## 2026-09-01 — Marka Adı Düzeltmesi: Stuart -> Cole-Parmer Stuart

`Stuart` markası aslında `Cole-Parmer Stuart` olmalı. Marka slug'ı (`stuart`) korundu; sadece görünen ad ve SEO metinleri güncellendi.

### Eklenen / Değiştirilenler

- `config/mta.php` -> `product_brands`: `name` artık `Cole-Parmer Stuart`; `aliases` listesine `Stuart`, `Cole Parmer Stuart`, `Cole-Parmer Stuart` eklendi (XML/seed normalizasyonu bozulmaz).
- `app/Http/Controllers/SiteController.php`: `brand_cards['stuart']` özet/anchor/alt metinleri ve markalar SSS cevabı `Cole-Parmer Stuart` olarak güncellendi.
- `tools/seed-erime-noktasi-products.php`: seed marka bloğu `name` ve ürün `metadata['Marka']` alanı `Cole-Parmer Stuart` yapıldı.
- Dokümanlar (`MTA_SITE_BRIEF.md`, `AI_HANDOFF.md`) marka listelerinde güncellendi.
- `slug` `stuart` sabit kaldığı için `/urunler/marka/stuart` URL'i, `product_category_brands` haritası ve `cole-parmer-stuart-*` ürün slug'ları etkilenmedi.
- `docs/SAYFA_METIN_HARITASI.md` üretilen dosyadır; `tools/export-page-text-map.php` ile yeniden üretildiğinde otomatik güncellenir.

## 2026-09-01 — Admin Görsel Yükleme: Eksik Storage Symlink Düzeltmesi

### Sorun

Admin panelinden ürüne "Ana görsel" yüklendiğinde Filament "görsel yüklendi" gösteriyordu, ancak kaydedip sayfa yenilendiğinde görsel kırık çıkıyordu.

### Kök Neden

`public/storage` sembolik bağlantısı (symlink) yerel ortamda hiç oluşturulmamıştı. `FileUpload` alanları (`ProductResource`, ürün `image`/`gallery`, doküman `path`, `og_image`) `disk('public')` kullanıyor; bu disk `storage/app/public` klasörüne yazıyor ve dosyalar sitede `APP_URL . '/storage/...'` (bkz. `SiteController::publicAssetPath()`) üzerinden çağrılıyor. Bu adresin çalışması `public/storage -> storage/app/public` symlink'ine bağlıdır. Kayıt öncesi Filament kendi geçici önizlemesini gösterdiği için sorun fark edilmiyor, kayıttan sonra gerçek `/storage/...` adresine geçilince symlink yoksa 404 ve kırık görsel oluşuyordu.

### Çözüm

```bash
php artisan storage:link
```

çalıştırılarak `public/storage` symlink'i oluşturuldu. Doğrulama: `storage/app/public/media/products/...` altındaki admin yüklemesi `http://127.0.0.1:8000/storage/media/products/...` üzerinden HTTP 200 döndü ve ürün sayfasında (`/urun/shimadzu-moc-63u-nem-tayin-cihazi`) görsel doğru göründü.

### ⚠️ cPanel Canlı Deploy İçin Kritik Not

Bu symlink dosya sistemine ait, **git'e işlenmez** ve her ortamda ayrı oluşturulması gerekir. cPanel'e ilk deploy sırasında (satır ~1229-1231 ve ~1362'deki genel notlara ek olarak, somut adım):

```bash
php artisan storage:link
```

mutlaka çalıştırılmalı. Bazı paylaşımlı hostinglerde shell/symlink izni kısıtlı olabilir; böyle durumda cPanel dosya yöneticisinden manuel symlink oluşturmak ya da hosting desteğinden izin istemek gerekir. Bu adım atlanırsa admin panelinden yüklenen **tüm** görsel/doküman/OG görselleri canlıda kırık çıkar (WordPress importundan gelen `public/images/products/...` altındaki statik görselleri etkilemez, onlar zaten symlink'e bağımlı değil).

## 2026-09-01 — Büyük UI Revizyonu (Katalog + PDP + Header + Footer + Mega Menü) + GitHub

Uzun bir tur boyunca ön yüzün önemli bölümleri kurumsal B2B/e-ticaret standartlarına göre yeniden tasarlandı. Tüm çalışma **`github.com/serloading/mta`** deposuna (public) push edildi — ilk commit `c38eef2`, dal `main`. 13/13 test geçiyor.

### Tasarım sistemi yaklaşımı

Her yeni bölüm **kendi CSS namespace'i** içinde (`.catalog-ui` / `.pdp-ui` / `.catalog-lower` / `.ft-*` / header `.mb-*` `.top-bar`) — sitenin geri kalan Poppins/teal sistemi bozulmadı. Ortak token: Inter font, Sky-600 `#0284C7` (katalog primary), Teal-700 `#0F766E` / Teal-600 `#0D9488` (PDP + header + kurumsal), Amber-600 `#D97706` (PDP CTA), Slate paleti, `#0F172A` dark surface.

### A. Global container hizalaması

`--site-container` `1180px → 1320px`. `.container` artık `max-width: 1320px; margin-inline:auto; padding-inline: 1rem/1.5rem/2rem` (`.cui-shell` ile birebir). `.header-shell`/`.top-header-shell` kendi `width` override'ları kaldırıldı. `.pdp-shell`/`.pdp-tabs`/`.pdp-panels` `1280→1320`, `.clower-shell` `1180→1320`. Sonuç: header, breadcrumb, hero, filtre paneli, ürün grid'i, alt bölümler, footer içerikleri **1440px'de sol 85 / sağ 1341** ile aynı hizada. Ortalanmış bölüm başlıkları (`.section-header.centered`) bilinçli olarak dar bırakıldı.

### B. Katalog liste / kategori / marka sayfaları — `resources/views/pages/product-{category,brand}.blade.php`

- **`.catalog-ui`** scoped sistem (`resources/css/app.css` sonunda). Yeni partial'lar: `partials/catalog-filters.blade.php`, `partials/catalog-product-card.blade.php`, `partials/catalog-results.blade.php`, `partials/catalog-lower.blade.php`. JS: `resources/js/catalog.js` (grid/list toggle + localStorage, client-side A-Z sıralama, mobil off-canvas filtre drawer).
- **Hero:** breadcrumb bar → logo kutusu (168×100) + H1 + güven chip'leri + açıklama + **2 CTA butonu** ("{Marka/Kategori} Kalibrasyon Hizmeti Al" + "Detaylı Bilgi Al"). Eski kalabalık kategori/marka chip satırı kaldırıldı.
- **2 kolon:** sticky sol filtre paneli (`.cui-panel`, adet/`(N)` rozetleri KALDIRILDI) + sağ toolbar (ürün sayısı + sıralama + grid/list) + `md:2 / xl:3` ürün grid.
- **Ürün kartı:** "Teklife Açık" rozeti YOK, "Teklif Alın" etiketi YOK. Marka · Kategori satırı (ikisi de tıklanabilir link), görsel + başlık + tam genişlik "İncele" butonu (hepsi ürüne gider). Buton yazısı beyaz (`.catalog-ui a:not(.cui-btn):not(.cui-chip)` reset).
- **Alt bölümler (`.catalog-lower` / `.clower-*`):** parçalı kutu yığını → 5 blok: A) tek "Teknik Alım & Seçim Rehberi" (sol intro + 3 numaralı adım), B) "İlgili Ürün Grupları" minimal chip'ler, C) (kategori sayfası) grayscale→renkli marka logo kartları, D) modern minimal SSS akordiyonu, E) **tek butonlu** dark CTA banner.

### C. Ürün detay sayfası (PDP) — `resources/views/pages/product-detail.blade.php`

- **`.pdp-ui`** scoped sistem. JS: `resources/js/pdp.js` (galeri thumb değişimi, lightbox, sekmeler — ok tuşları + `#hash` deep-link).
- **Buy box (`lg:grid 5/7`):** sol media stage (`aspect-square`, zoom → lightbox) + thumb şeridi (`product-gallery-strip` class korundu — test bağımlı). Sağ: breadcrumb → **marka logo + adı** chip'i (tıklanır, marka sayfasına) → H1 (28/22px) → 3'lü "Quick Key Specs" mini-grid → **teal cross-sell kutusu** (`kalibrasyon` checkbox'ı quote form'a taşınır) → action bar: **hayalet** "Ürün İçin Teklif Al" + WhatsApp yeşili `#25D366` "WhatsApp ile Bilgi Al" + outline "Kataloğu İncele" (yalnızca doküman varsa).
- **Sticky tab bar:** Genel Bakış / Teknik Özellikler / Doküman & Video / SSS & Destek. **"Doküman & Video" sekmesi ve "Kataloğu İncele" butonu, üründe hiç doküman VE video yoksa hiç render edilmez.** JS yoksa tüm paneller açık (SEO-safe).
- **Alt banner:** ürüne özel başlık/metin ("{Ürün adı} için teklif alın"), hayalet Teklif + WhatsApp yeşil.

### D. Footer — `resources/views/layouts/site.blade.php` + `.ft-*`

5 sütunlu grid (`#0F172A`): Marka (logo + slogan + TÜRKAK/ISO 17025 rozetleri + sosyal) │ HİZMETLER │ KATEGORİLER │ TEKNİK SERVİS │ İLETİŞİM (ikon'lu adres/tel/mail + tam genişlik "Teklif Formuna Git" teal buton). Alt şerit `#020617`: "© 2026 MTA Endüstri…" + 4 yasal link (KVKK/Gizlilik/Çerez/Kullanım — **şu an hepsi `/iletisim`'e gidiyor, gerçek sayfalar yapılınca güncellenmeli**). "SEO-first Laravel altyapısı" notu silindi. Link hover: renk + `translateX(4px)`.

### E. Header — search-centric yeniden yazım — `resources/views/layouts/site.blade.php`

- **Top utility bar** (`.top-bar`, `#0F172A`, 36px): sol ikon'lu tel + mail, sağ Sertifikalar/Blog/Hakkımızda + ayraç + sosyal. Scroll'da gizlenir (`body.is-scrolled` → `max-height: 0`).
- **Main bar** (`.main-bar`, beyaz, sticky, backdrop-blur): Logo (44px + `border-l` metin) │ **canlı arama** (`.mb-search`, `#F8FAFC`, büyüteç, `Ctrl K` rozeti) │ nav dropdown'ları │ WhatsApp ikon butonu + teal "Teklif Al".
- **Canlı arama backend:** `Route::get('/ara', 'search')->name('search')` (catch-all `/{any}` ÖNCESİNDE eklendi). `SiteController::search()` + `searchCatalog()` — `?format=json` → gruplu öneri JSON; düz → `pages/search.blade.php` sonuç sayfası (`noindex,follow`). Ürün name/model/sku/brand/category araması.
- **Arama JS:** `resources/js/header.js` — debounce 180ms fetch, gruplu dropdown (Kategoriler/Markalar/Ürünler + görsel), klavye (↑↓ Enter Esc), **⌘K / Ctrl+K** odak, "tüm sonuçlar →". Ayrıca scroll-collapse + mobil arama toggle + hamburger drawer.
- **Mobil (<1024px):** nav + inline arama + CTA gizli; arama ikon butonu (genişleyen bar) + hamburger → `.mobile-drawer`.
- `app.js` güncellendi: `setupHeader()` import; eski `[data-mobile-menu]` (`<details>`) mantığı kaldırıldı; mega hover/click/`is-open` mantığı korundu.

### F. Mega menü v3 (full-width) — `.mega-*`

- **Tam genişlik dropdown:** `.mega-menu { position:absolute; left:0; right:0 }`, iç `.mega-panel { max-width:1320px; margin-inline:auto; padding 1/1.5/2rem }` — header container'ıyla aynı hiza. `overflow: hidden` → **yatay scrollbar yok** (eski Teknik Servis taşma hatası çözüldü).
- **Ürünler = 264px + 1fr (3:6:3 hissi):** sol kategori listesi (çizgi ikon + chevron, `max-height:460px` scroll, border-r) │ hover edilen kategorinin `.mega-cat-sub`'ı (absolute overlay) = **3 sütunlu alt-kategori grid'i** + "Tümünü İncele →" başlık + tam yükseklikte teal **promo kartı** ("Kataloğa git →" alta sabit). İlk kategori varsayılan açık (saf CSS `:first-child` + `:not(:hover)` deseni).
- **Kalibrasyon / Teknik Servis = `3fr 9fr`:** sol `#0F172A` dark banner (mono badge + başlık + teal buton) │ sağ **2×2** ikon-kutulu servis kart grid'i. "İncele >" yok, tüm kart tıklanır.
- Blade'de `$megaIcon()` closure'ı slug/başlıktan çizgi-ikon seçiyor (`$mi` dizisi).

### G. Admin — `app/Filament/Resources/Products/ProductResource.php`

- Form **5 sekmeye** bölündü: Genel / İçerik / Görseller / Doküman & Video / SEO & Schema (`Tabs` + `Tab`).
- **OG alanları kaldırıldı** (`og_title`/`og_description`/`og_image`). `SiteController::meta()` zaten OG'yi meta başlık/açıklama + ürün görselinden otomatik türetiyor. Schema repeater (`SchemaBlockFields::section('product')`) "SEO & Schema" sekmesine taşındı.
- **`filter_keys`** (yeni json kolon, migration `2026_09_01_060000`): İçerik sekmesinde **`CheckboxList`** — ürünün specs tablosundaki başlıklardan hangilerinin marka/kategori sayfası filtresinde çıkacağı işaretlenir. `SiteController::productSpecFilters()` yeniden yazıldı: işaretli `filter_keys` birleşimini kullanır; hiç işaretli yoksa eski whitelist heuristiğine (`buildSpecFilters` fallback) düşer.
- **Dosya adı korunması:** `keepUploadedFileName()` helper'ı — `image`/`gallery`/doküman `path` alanlarında Filament'in rastgele hash'i yerine yüklenen dosya adı slug'a çevrilerek kullanılır (aynı ada tekrar yükleme = üzerine yazma). Doküman alanı artık **PDF + .doc/.docx** kabul ediyor.
- Uzun açıklama RichEditor'ü admin temasıyla ~3 satıra indirildi.

### H. Admin teması — `resources/css/filament/admin/theme.css` (YENİ)

`AdminPanelProvider::viteTheme(...)` + `vite.config.js` input'una eklendi. Filament v4 primary/success dolgulu butonları soluk tint + koyu yazı yerine **teal-600 zemin + beyaz yazı** (`:is(.fi-color-primary,.fi-color-success)[class*='fi-bg-color-']`). RichEditor min-height 5rem.

### I. Dashboard performansı

Sorgular hızlıydı (~9ms). 30sn timeout = `php artisan serve` tek-thread + `filament:optimize-clear` sonrası soğuk sınıf keşfi. **Çözüm: `php artisan filament:optimize`** (bileşen + ikon önbelleği; `vendor/filament` kullanıcı düzenlemez). Bonus: `ContentOverview` (5dk) + `LatestContent` (2dk) `Cache::remember` sarıldı, count'lar tek diziye indirildi. Not: `composer update` / cache temizliği sonrası admin yavaşlarsa tekrar `php artisan filament:optimize`.

### J. GitHub

- `.gitignore` sıkılaştırıldı: `/database/*.sqlite*` (admin hash + iş verisi içerir, repo public) ve `/storage/app/public/media` (yüklenen dosyalar) eklendi. **Doğrulandı: `.env`, `.sqlite`, media stage'lenmedi.**
- `git init` → commit `c38eef2` → `origin https://github.com/serloading/mta.git` → `push -u origin main` başarılı.
- Git identity: `serca` / `theprofiterol@gmail.com`.

## 2026-09-01 — Kapsam Sayfası (/kapsam) + Menüye Ekleme

Kullanıcı, referans olarak UMS Ankara `hizmetler.php` HTML'ini vererek 10 ölçüm alanı başlığı altında akredite kalibrasyon kapsam tablolarını içeren bir "Kapsam" sayfası istedi. İçerik (cihaz grupları, ölçüm aralıkları, U (k=2), metot/standart) o koddan alındı; UMS marka/akreditasyon no/PDF/iletişim bilgileri **alınmadı**, MTA tasarım diline uyarlandı.

### Eklenen / Değiştirilen Dosyalar

- `config/mta-scope.php` — **YENİ.** `['note' => ..., 'categories' => [...]]`. 10 kategori, 83 cihaz grubu, ~551 aralık satırı. Her grup: `id`, `title`, `columns[]`, `rows[]` (hücre sayısı `columns` ile birebir; `—` ön yüzde muted). Kategori slug'ları filtre chip'i ile eşleşir: `sicaklik, boyut, basinc, kutle, sertlik, hacim, tork, yogunluk, zaman, malzeme`. **Not: değerler örnek kapsam; laboratuvarın güncel TÜRKAK kapsamıyla teyit edilmeli.**
- `routes/web.php` — `Route::get('/kapsam', [SiteController::class, 'scope'])->name('scope');` (teknik-servis rotalarından hemen sonra, `/{any}` fallback'inden önce).
- `app/Http/Controllers/SiteController.php` — `scope()` metodu (`products()` üstüne eklendi). Meta + breadcrumb/WebPage schema + `genericQuoteCta` (`quoteCta('service', null, ...)`). `scopeStats` (kategori/grup/satır sayısı) view'a geçer. `sitemap()` statik URL listesine `/kapsam` eklendi.
- `resources/views/pages/scope.blade.php` — **YENİ.** Dark hero (`.kapsam-hero`, navy, `radius 0 0 24px 24px`) + breadcrumb + istatistik satırı. `.kapsam-toolbar`: arama input + filtre chip'leri (Tümü + 10 kategori). Her kategori `.kapsam-block[data-scope-block][data-cat]` → `.kapsam-block-head` (emoji + h2 + `summary · N grup`) + `.kapsam-grid` içinde `<details.kapsam-card[data-scope-card]>` (summary: başlık + `N satır` rozeti + caret; body: `.kapsam-table-wrap > table.kapsam-table` + "Bu gruptan teklif iste →" butonu → `route('quote', ['source_type'=>'service','source_name'=>'Kapsam: '.$group['title']])`). Alt: `.kapsam-empty` (arama sonuç yok) + `.kapsam-note` (`{!! $scopeNote !!}`) + `.cta-band`.
- `resources/css/app.css` — dosya sonuna `/* Kapsam sayfası */` bloğu. `.kapsam-toolbar` yalnızca `≥1024px`'te `position:sticky; top:73px` (header yüksekliği 73px; mobilde sticky değil — çift sticky çakışmasını önler). `.kapsam-card[open]` grid'de `1 / -1` (tam genişlik). `.kapsam-table-wrap { overflow-x:auto }` responsive.
- `resources/js/scope.js` — **YENİ**, `app.js` içinde `setupScope()` olarak import edildi. Filtre chip'i: aktif class + blokları `hidden` ile göster/gizle + hedefe smooth scroll (offset = header + toolbar yüksekliği). Arama: 140ms debounce, `data-scope-card` metninde (tr-locale normalize) arar; eşleşen kartları `open=true` yapar, kartsız blokları gizler, hiç sonuç yoksa `.kapsam-empty` gösterir. `#grup-id` hash deep-link ile o kartı açar.
- `resources/views/layouts/site.blade.php` — "Kapsam" linki: desktop nav'da İletişim'den önce; mobil drawer'da Kurumsal'dan önce; footer "Hizmetler" sütununda "Kalibrasyon Kapsamı" olarak.

### Doğrulama

- `php -l` (config + controller) temiz. `php artisan test` → 13/13. `npm run build` → başarılı (yalnızca mevcut `fontaine` uyarısı).
- `/kapsam` → 200, başlık "Kalibrasyon Kapsamımız | MTA Endüstri". Konsol hatası yok. DOM ölçümleri: header 0-73, hero 73-525, toolbar, section 5046px, CTA, footer — sıralı, taşma yok.
- JS ile test edildi: "Basınç" filtresi → sadece `basinc` bloğu; arama "ISO 6789" → sadece Tork kartı (açık), empty gizli; "zzzznomatch" → 0 blok, empty görünür. Teklif linki `/teklif-al?source_type=service&source_name=Kapsam:...` → 200.
- Not: Browser pane'de uzun sayfa scroll-sonrası screenshot yine boş/beyaz döndü (bilinen sorun); doğrulama `javascript_tool` DOM/computed-style ile yapıldı.

### Ek: Vercel build hatası düzeltmesi (`vite.config.js`)

Deploy sırasında Vercel `npm run build` şu hatayı verdi: `Can't resolve '../../../../vendor/filament/filament/resources/css/theme.css'`. Sebep: Vercel frontend-only build yapıyor, `composer install` çalışmadığı için `vendor/` yok; `resources/css/filament/admin/theme.css` bu vendor dosyasını `@import` ediyor. (Bu, Kapsam işiyle ilgisiz, ilk commit'ten beri var olan bir sorun.)

Çözüm: `vite.config.js` artık Filament tema girdisini yalnızca `vendor/filament/filament/resources/css/theme.css` diskte varsa `input`'a ekliyor (`node:fs` `existsSync`). Yerelde (vendor var) → tema derlenir, admin paneli stilli. Vercel'de (vendor yok) → atlanır, build geçer. Zaten Filament admin Vercel'den sunulmuyor. Test: vendor css geçici taşındığında `npm run build` EXIT 0; geri konunca `theme-*.css` yine üretiliyor.

### Kalan / İyileştirme Notları

- `config/mta-scope.php` içindeki U/aralık/metot değerleri referans HTML'den birebir alındı; **MTA'nın gerçek akreditasyon kapsamıyla analist teyidi gerekir.** İkinci fazda bu veri DB'ye (ör. `scope_categories` / `scope_groups` tabloları + Filament resource) taşınabilir.
- İstenirse `/kapsam` için özel SEO meta/H1 + SeoEntry admin kaydı; sayfaya JSON-LD `Service`/`OfferCatalog` şeması eklenebilir.
- Kapsam PDF (TR/EN) linkleri hero'ya eklenebilir (dosyalar gelince).

## 2026-09-02 — Ana Sayfa + Kapsam Sayfası Yeniden Tasarımı (Tailwind)

Kullanıcı iki ayrıntılı B2B/CRO tasarım brief'i verdi (design token'lar + section section layout). İkisi de **Tailwind utility** ile yeniden kodlandı — `@import 'tailwindcss'` + `@source '../views/**/*.blade.php'` zaten aktif, blade içi utility'ler derleniyor (probe ile doğrulandı).

**`resources/views/pages/home.blade.php`** — tam yeniden yazıldı, 7 bölüm:
1. Dark split hero (`bg-slate-900 rounded-3xl`, col-7/col-5), gömülü arama formu (`route('search')`), amber "Hızlı Teklif İste" + outline "Ürün Kataloğu", sağda 3 metrikli güven kartı.
2. 3 akredite hizmet kartı — **tüm kart `<a>` (tam tıklanır)**, ikon + bullet + "Hizmet Kapsamını Gör" (ayrı buton yok).
3. Görselli kategori çip grid (`grid-cols-2 md:4 lg:6`), 80×80 `object-contain`.
4. Grayscale→renkli marka ribbon.
5. `bg-slate-50 rounded-3xl` "Neden MTA" — 4 icon-benefit kartı.
6. 2 kolon: SSS `<details>` akordiyonu + 2 görselli blog kartı.
7. Dark alt banner, **tek** teal CTA.
Eski parçalı SEO metin blokları (`credential-band`, `lab-section`, `sector-grid`, süreç vb.) kaldırıldı.

**`SiteController::home()`** güncellendi: `featuredCategories` (görsel = kategoriye/anahtar kelimeye eşleşen ilk ürün foto; görsel yoksa ikon fallback, görselli olanlar öne alınır, ilk 6) + `partnerBrands` (`public/images/brands/*.png` olanlar) + `genericQuoteCta`.

**`resources/views/pages/scope.blade.php`** — tam yeniden yazıldı:
1. Dark hero içine **gömülü instant search** (56px, beyaz, shadow-2xl) + sağda sonuç sayacı rozeti.
2. **Sticky teal kategori tab bar** (`sticky top-[68px]`) — eski ayrı `.kapsam-toolbar` "ikinci nav şeridi" kaldırıldı, arama hero'ya taşındı.
3. Kategori bölümleri: ikon + "{Kategori} Kalibrasyonları" + emerald "N cihaz grubu kapsamda" rozeti; `grid md:grid-cols-2` genişleyebilir `<details>` kartlar — özet (aralık/belirsizlik + satır sayısı) + açılınca tam tablo (`open:md:col-span-2` tam genişlik) + "Kapsam İçin Teklif Al →".
4. Kapsam-dışı destek bandı: WhatsApp + "Özel Cihaz Listesi İletin".
5. Dark alt CTA, tek teal buton.
`groupSummary()` blade closure: kolon başlıklarından "aralık"/"belirsizlik" sütununu bulup ilk–son satırdan özet üretir.

**`resources/js/scope.js`**: `search` artık `document.querySelector('[data-scope-search]')` (hero'da), `[data-scope-search-count]` rozeti "N grup" / "N sonuç" olarak güncellenir. `data-scope-*` hook'ları ve `is-active` toggle mantığı korundu.

**`resources/css/app.css`**: eski `.kapsam-*` bloğu (267 satır) silindi → `.scope-tab` + `.scope-tab.is-active` (teal-700) bileşeni eklendi.

### Doğrulama
- `php artisan test` 13/13, `npm run build` OK (app.css 135→144 KB). Konsol hatası yok.
- Yerelde DOM/JS ile doğrulandı: home 7 bölüm render, kategori çipleri 4/6 gerçek ürün fotosu (titratör/viskozimetre demo DB'de görselli ürün yok → ikon fallback); kapsam instant search ("manometre" → sadece basınç, kart açılır), tab filtresi, sayaç rozeti, kart genişleme + tablo hepsi çalışıyor.
- **Deploy:** commit `cec817f` → `demo.mtaend.com`'a `git pull` + `public/build` scp + `view/config/route:cache`. Her iki sayfa canlıda HTTP 200.

## YAPILACAKLAR (yeni sohbette devam)

Aşağıdaki iş bu turda **yapılmadı** — kullanıcı ayrıntılı tasarım brief'i verdi, odaklı bir tur gerektirir:

### 1. Kalibrasyon / Teknik Servis Hizmet Detay sayfası yeniden tasarımı
`resources/views/pages/service-detail.blade.php` ve `technical-service-detail.blade.php`. Brief özeti:
- **A. Hero:** `#0F172A` dark, `radius 0 0 24px 24px`, `grid lg:grid-cols-12`. Sol (`col-7`): mono eyebrow badge ("TÜRKAK AKREDİTE KALİBRASYON LAB"), H1, alt metin, 3 güven rozeti (TÜRKAK / ISO 17025 / Hızlı Sertifika). Sağ (`col-5`): beyaz **sabit hızlı teklif formu kartı** (Cihaz Tipi/Adedi, Firma, Telefon/E-posta + "Hızlı Kalibrasyon Teklifi Al" teal buton). **Stok insan fotoğrafları kaldırılacak.**
- **B. Ölçüm kapsamı:** ham `<table>` yerine **aranabilir data-rich tablo** — üstte "Cihaz veya model ara…" input filtresi. Kolonlar: Cihaz/Donanım Tipi | Ölçüm Aralığı | Belirsizlik/Tolerans | Akreditasyon Durumu (emerald "TÜRKAK Kapsamında" tag) | Aksiyon ("Kapsam İçin Teklif İste"). Veri: `config/mta.php -> services[*].scope_groups`.
- **C. 5 adımlı yatay süreç akışı:** `grid md:grid-cols-5`, aralarda ok. 01 Cihaz Kabul → 02 Ön İnceleme → 03 Ölçüm & Analiz → 04 Sertifikalandırma → 05 Teslimat. Dikey alakasız kutular kaldırılacak.
- **D.** Minimal SSS akordiyonu (`max-w-3xl`) + 2'li ilişkili hizmet/bakım-onarım kartı.
- **E.** Dark navy alt banner — **tek buton** ("Listeni Yükle / Teklif Al").
- Tokens: Teal-700 primary, `#0F172A` dark, Amber-600 accent badge, Slate paleti. Çizgi ikonlar (1.5px stroke).
- Not: Ana Sayfa ve Kapsam sayfaları 2026-09-02'de Tailwind ile bu dile geçirildi; hizmet/teknik servis detay bu turda aynı token seti + `pages/home.blade.php` / `pages/scope.blade.php` referans alınarak yapılabilir.

### Genel notlar (yeni sohbet için)
- `php artisan serve` port 8000'de zaten çalışıyor (kullanıcının terminali). Cache'ler: geliştirme için `view:cache` yapılmadı (blade hot-reload); `filament:optimize` aktif tutulmalı.
- Browser pane'de uzun sayfa **scroll sonrası screenshot** güvenilir değil — doğrulamayı `javascript_tool` ile computed-style/DOM üzerinden yapın.
- Değişiklikten sonra: `npm run build` + `php artisan test` (13 test) + commit + `git push`.
