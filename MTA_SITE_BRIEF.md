# MTA Endüstri Web Sitesi Brief Dosyası

Bu doküman, MTA Endüstri web sitesinin firma anlatımı, hizmet kapsamı, ürün yapısı, SEO yaklaşımı ve mevcut içerik mimarisini özetler. Tasarımcı, yazılımcı, SEO uzmanı veya başka bir yapay zeka ile paylaşılmak üzere hazırlanmıştır.

Son güncelleme: 2026-08-29

## 1. Proje Özeti

MTA Endüstri web sitesi; laboratuvar cihazları, ölçüm ekipmanları, kalibrasyon hizmetleri ve teknik servis hizmetlerini kurumsal bir yapı içinde sunan SEO odaklı bir Laravel projesidir.

Sitenin temel amacı:

- MTA Endüstri'nin kurumsal güvenini ve teknik uzmanlığını anlatmak.
- Kalibrasyon hizmetleri için ayrı, SEO uyumlu hizmet sayfaları oluşturmak.
- Teknik servis hizmetlerini ayrı bir başlık altında konumlandırmak.
- Ürünleri kategori, marka ve teknik metadata yapısıyla katalog mantığında sergilemek.
- Lead / teklif talebi formlarını ileride admin panel ve CRM benzeri bir akışa bağlamak.
- Blog, kategori, marka, ürün ve hizmet sayfalarıyla organik arama görünürlüğünü artırmak.

## 2. Firma Özeti

Firma adı: MTA Endüstri

MTA Endüstri Ürünleri, 2010 yılında kurulmuştur. Firma; laboratuvar cihazı, ekipman ve sarf malzeme tedariği alanında faaliyet gösterir. Kimya, gıda, ilaç, akademik, plastik, petrokimya ve medikal sektörleri başta olmak üzere kalite kontrol ve AR-GE laboratuvarlarına sürdürülebilir teknik destek sunmayı hedefler.

MTA Endüstri'nin yaklaşımı:

- Laboratuvar ve kalite kontrol süreçlerine uygun cihaz tedariği sağlamak.
- Dünya markalarıyla kurumsal iş birlikleri kurmak.
- Satış sonrası destek, teknik servis ve müşteri eğitimlerini önemsemek.
- İş ortaklarına daha iyi ve kaliteli hizmet anlayışıyla yaklaşmak.
- Güvenilir, teknik ve çözüm odaklı bir marka algısı oluşturmak.

## 3. İletişim Bilgileri

- Adres: Bahçelievler, Köknar Sk. No:15/B, 34890 Pendik/İstanbul
- E-posta: info@mtaend.com
- Telefon: +90 (216) 390 17 78
- Fax: +90 (216) 390 17 88

Sosyal medya:

- LinkedIn: https://www.linkedin.com/company/mtaendustri/
- Facebook: https://www.facebook.com/mtaend/
- Instagram: https://www.instagram.com/mtaendustri/

## 4. Web Sitesi Konumlandırması

Site, klasik bir ürün satış e-ticaret sitesi gibi değil; kurumsal katalog + hizmet odaklı lead toplama sitesi olarak kurgulanmaktadır.

Ürünlerde doğrudan sepet / ödeme akışı yoktur. Ürün sayfalarında teknik bilgiler, marka, kategori, görsel, özellikler, ilgili kalibrasyon/hizmet bağlantıları ve teklif talebi yönlendirmeleri bulunur.

Ana odak:

- Kalibrasyon hizmetleri
- Laboratuvar cihazları teknik servis
- Terazi teknik servis
- Analiz ve ölçüm cihazları teknik servis
- Marka ve kategori bazlı ürün katalogları
- SEO uyumlu blog / bilgi merkezi yapısı
- Sertifikalar sayfası
- İletişim ve teklif talebi

## 5. Site Altyapısı

Proje Laravel tabanlıdır.

Teknik yapı:

- Backend: Laravel
- Veritabanı: MySQL hedeflenmektedir; geliştirme ortamında Laravel database yapısı hazırlanmıştır.
- Admin panel: Filament kuruludur, admin kaynakları ilerleyen aşamada oluşturulacaktır.
- Frontend: Blade template yapısı ve Vite asset build sistemi kullanılır.
- Stil: Poppins font ailesi ve logo renklerinden türetilen kurumsal renk sistemi kullanılır.
- SEO: Sayfa bazlı title, meta description, canonical, schema ve sitemap mimarisi düşünülmüştür.

Ana proje dizini:

- `config/mta.php`: Firma bilgileri, hizmetler, teknik servisler, ürün kategori/marka haritaları ve fallback içerikler.
- `app/Support/WpProductImport.php`: WordPress XML ürün import/normalizasyon sınıfı.
- `routes/console.php`: Ürün import ve içerik senkronizasyon komutları.
- `app/Http/Controllers/SiteController.php`: Ön yüz sayfa verilerini hazırlayan ana controller.
- `resources/views/pages`: Sayfa Blade dosyaları.
- `resources/views/layouts/site.blade.php`: Ana site layout/header/footer yapısı.
- `resources/css/app.css`: Site genel tasarım stilleri.
- `public/images`: Site içinde kullanılan logo, hizmet, teknik servis, marka ve ürün görselleri.
- `storage/app/imports`: XML'den normalize edilen ürün JSON çıktıları ve import raporları.

## 6. Ana Sayfa Yapısı

Ana sayfa, kurumsal ve hizmet odaklı bir ilk izlenim vermek üzere hazırlanmıştır.

Ana sayfada olması gereken ana bölümler:

- Logo ve kurumsal header
- Top header: telefon, e-posta ve sosyal medya ikonları
- Mega menü: kalibrasyon hizmetleri, teknik servis ve ürün yapısı
- Kurumsal hero alanı
- Kalibrasyon hizmetleri özet blokları
- Dijital / teknik altyapı güven mesajları
- Öne çıkan hizmet veya cihaz grupları
- Kalibrasyon süreci
- Teknik servis hizmetleri
- Ürün kategori/marka yönlendirmeleri
- Teklif talebi CTA alanı
- İletişim bloğu
- Blog / teknik içerik önizlemeleri
- Footer: hizmetler, teknik servis, ürünler, kurumsal ve iletişim

## 7. Kalibrasyon Hizmetleri

Sitede 6 ana kalibrasyon hizmeti bulunmaktadır:

1. Basınç Kalibrasyonu
2. Sıcaklık Kalibrasyonu
3. Tork Kalibrasyonu
4. Devir Kalibrasyonu
5. Kütle & Terazi Kalibrasyonu
6. Hacim Kalibrasyonu

Her hizmet sayfası şu yapıda kurgulanır:

- SEO uyumlu hero alanı
- Hizmet özeti
- Kalibre edilen cihazlar
- Ölçüm aralığı / kapsam tablosu
- Süreç anlatımı
- Sık sorulan sorular
- İlgili ürün/kategori yönlendirmeleri
- Teklif talebi CTA alanı

Örnek hizmet detayları:

Basınç Kalibrasyonu:

- Analog manometre
- Sayısal manometre
- Basınç transduseri
- Basınç transmitteri
- Fark basınç ölçer
- Aralık örneği: -0,8 bar ile 700 bar; fark basınç için -0,8 bar ile 5000 bar

`/hizmetler/basinc-kalibrasyonu` sayfası özel SEO brief'ine göre hazırlanmıştır. Sayfa basınç kalibrasyonu, manometre kalibrasyonu, dijital manometre kalibrasyonu, basınç transmitter kalibrasyonu, basınç sensörü kalibrasyonu, vakum ölçer kalibrasyonu ve fark basınç ölçer kalibrasyonu aramalarını hedefler. Meta alanları, H1/H2 yapısı, cihaz kapsam listesi, ölçüm aralığı tablosu, süreç adımları, teknik servis yönlendirmesi, teklif CTA'sı, SSS ve görsel alt metni uygulanmıştır.

Sıcaklık Kalibrasyonu:

- Direnç ve ısıl çift sensörleri
- Pirometre ve IR termometre
- Sıcaklık göstergeleri
- Kontrollü hacimler
- Sıvılı cam termometreler
- Higrometre ve bağıl nem ölçerler
- PRT ve termistör grupları

Kütle & Terazi Kalibrasyonu:

- M1, M2, M3 sınıfı kütleler
- Standart olmayan kütleler
- Teraziler
- Otomatik ağırlık kontrol terazileri

Hacim Kalibrasyonu:

- Cam mezür
- Pipet
- Büret
- Balon joje
- Piknometre
- Pistonlu pipet
- Pistonlu büret
- Dispenser
- Plastik mezür

Tork Kalibrasyonu:

- Referans tork anahtarı
- Tork el aletleri
- Tork büyütücü
- Aralık örneği: 0,1 N·m ile 1000 N·m

`/hizmetler/tork-kalibrasyonu` sayfası özel SEO brief'ine göre hazırlanmıştır. Sayfa tork kalibrasyonu, tork anahtarı kalibrasyonu, torkmetre kalibrasyonu, tork ölçer kalibrasyonu, tork el aletleri kalibrasyonu ve tork büyütücü kalibrasyonu aramalarını hedefler. Meta alanları, H1/H2 yapısı, ekipman kapsam listesi, tork aralığı tablosu, süreç adımları, kalibrasyon hizmetleri yönlendirmesi, teklif CTA'sı, SSS ve görsel alt metni uygulanmıştır.

Devir Kalibrasyonu:

- Frekans kaynakları
- Frekans standardı
- Santrifüj / karıştırıcı cihazlar
- Aralık örneği: 60 rpm ile 60000 rpm

`/hizmetler/devir-kalibrasyonu` sayfası özel SEO brief'ine göre hazırlanmıştır. Sayfa devir kalibrasyonu, takometre kalibrasyonu, rpm kalibrasyonu, frekans kalibrasyonu, santrifüj kalibrasyonu ve karıştırıcı devir kalibrasyonu aramalarını hedefler. Meta alanları, H1/H2 yapısı, cihaz kapsam listesi, devir aralığı tablosu, süreç adımları, manyetik karıştırıcı, mekanik karıştırıcı, homojenizatör, viskozimetre ve laboratuvar cihazları teknik servis iç linkleri, teklif CTA'sı, SSS ve görsel alt metni uygulanmıştır.

## 8. Teknik Servis Hizmetleri

Sitede teknik servis başlığı ayrı bir ana menü olarak konumlandırılmıştır.

Teknik servis sayfaları:

1. Analiz ve Ölçüm Cihazları Teknik Servis
2. Laboratuvar Cihazları İçin Teknik Servis
3. Terazi Teknik Servis

`/teknik-servis/analiz-ve-olcum-cihazlari-teknik-servis` sayfası özel SEO brief'ine göre hazırlanmıştır. Sayfa analiz cihazları teknik servis, ölçüm cihazları teknik servis, analiz cihazı tamiri, ölçüm cihazı bakım, pH metre teknik servis, iletkenlik ölçer teknik servis, refraktometre teknik servis, densitometre teknik servis, viskozimetre teknik servis ve titratör teknik servis aramalarını hedefler. Prob, sensör, elektrot, ölçüm stabilitesi, arıza tespiti, bakım-onarım, servis sonrası performans kontrolü ve kalibrasyon öncesi hazırlık akışıyla kurgulanmıştır.

`/teknik-servis/laboratuvar-cihazlari-icin-teknik-servis` sayfası özel SEO brief'ine göre hazırlanmıştır. Sayfa laboratuvar cihazları teknik servis, laboratuvar cihazları servis, laboratuvar cihazı tamiri, laboratuvar cihazları bakım ve laboratuvar ekipmanları servis aramalarını hedefler. Etüv, nem tayin, pH metre, iletkenlik ölçer, refraktometre, densitometre, viskozimetre, karıştırıcı, homojenizatör, titratör ve laboratuvar terazisi cihaz grupları için arıza tespiti, bakım, onarım ve kalibrasyon öncesi teknik hazırlık içerikleri uygulanmıştır.

Teknik servis içerik yaklaşımı:

- Arıza tespiti
- Periyodik bakım
- Yedek parça tespiti
- Elektronik kart, ekran, sensör ve prob kontrolleri
- Ölçüm stabilitesi kontrolü
- Kalibrasyon öncesi teknik hazırlık
- Yerinde veya laboratuvar ortamında servis
- Servis sonrası teklif ve raporlama süreci

Bu sayfalar hem teknik servis aramalarını yakalamak hem de ürün ve kalibrasyon sayfalarıyla iç bağlantı kurmak için önemlidir.

## 9. Ürün Katalog Yapısı

Ürünler WordPress XML dosyasından alınmıştır. XML'de toplam 509 ürün görülmüştür. Bu ürünlerden yalnızca MTA Endüstri için belirlenen kategori ve marka eşleşmesine uyan ürünler yayına alınmıştır.

Mevcut yayın verisi:

- Yayındaki ürün: 165
- Ürün kategorisi: 16
- Marka: 14
- Eski manuel örnek ürünler: taslakta tutulmuştur

Ürün sayfalarında hedeflenen yapı:

- Tek ana ürün görseli
- Ürün adı
- Marka
- Kategori
- Model / SKU / metadata alanları
- Teknik özellikler
- Kısa açıklama
- SEO title ve meta description
- İlgili kalibrasyon hizmeti alanı
- Teklif talebi butonu

Ürünlerde satın alma yerine teklif toplama mantığı kullanılmaktadır.

## 10. Ürün Kategorileri

Sitedeki ürün kategorileri:

- Teraziler
- Nem Tayin
- Kral Fischer
- Potansiyometrik Titratörler
- Densitometre
- Refraktometre
- pH Metre
- pH & İletkenlik
- Viskozimetre
- Etüv
- Balon Isıtıcılar
- Termoreaktör
- Termal Analiz
- Homojenizatör
- Mekanik Karıştırıcı
- Manyetik Karıştırıcı

Kategori sayfaları normal bir katalog/e-ticaret listeleme mantığında çalışmalıdır:

- Sol veya düzgün tasarlanmış filtre paneli
- Kategori açıklaması
- Marka filtresi
- Teknik özellik filtreleri
- Ürün kartları
- SEO uyumlu kategori metni
- İlgili hizmet bağlantıları

Özel SEO içeriği tamamlanan ürün kategori sayfaları:

- `/urunler/teraziler`: Hassas terazi ve analitik terazi odağında; A&D, Ohaus, Shimadzu ve Weightlab marka linkleri, terazi kalibrasyonu ve terazi teknik servis iç linkleriyle hazırlanmıştır.
- `/urunler/nem-tayin`: Nem tayin cihazı ve nem analiz cihazı odağında; A&D, Ohaus, Shimadzu ve Weightlab marka linkleri, kütle-terazi kalibrasyonu, sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis iç linkleriyle hazırlanmıştır. XML importunda 3 ürün yayındadır.
- `/urunler/ph-metre`: pH metre ve laboratuvar pH ölçüm cihazları odağında; Mettler Toledo, Ohaus ve WTW marka linkleri, pH & iletkenlik, sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis iç linkleriyle hazırlanmıştır. XML importunda şu an yayındaki ürün yoktur, boş durum ekranı çalışır.
- `/urunler/ph-iletkenlik`: İletkenlik ölçer, pH ve iletkenlik ölçer ve çok parametreli ölçüm cihazı odağında; WTW, Mettler Toledo ve Ohaus marka linkleri, pH metre, sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis iç linkleriyle hazırlanmıştır. XML importunda 59 ürün yayındadır.
- `/urunler/kral-fischer`: Karl Fischer titratör ve su miktarı tayin cihazları odağında; Kyoto KEM, Mettler Toledo, SI Analitik ve TitroLine 7500 KF marka linkleri, hacim kalibrasyonu, sıcaklık kalibrasyonu, laboratuvar cihazları teknik servis, nem tayin ve potansiyometrik titratör iç linkleriyle hazırlanmıştır. XML importunda 7 ürün yayındadır.
- `/urunler/potansiyometrik-titratorler`: Potansiyometrik titratör, otomatik titratör ve laboratuvar titrasyon cihazı odağında; Mettler Toledo, Kyoto KEM ve SI Analitik marka linkleri, hacim kalibrasyonu, laboratuvar cihazları teknik servis, Karl Fischer ve pH metre iç linkleriyle hazırlanmıştır. XML importunda 3 ürün yayındadır. SI Analitik logo dosyası kaynak klasörde bulunmadığı için marka alanı şimdilik metin fallback ile çalışır.
- `/urunler/densitometre`: Densitometre, yoğunluk ölçer ve özgül ağırlık ölçer odağında; Kyoto KEM, Mettler Toledo ve Bellingham + Stanley marka linkleri, refraktometre, sıcaklık kalibrasyonu, hacim kalibrasyonu ve laboratuvar cihazları teknik servis iç linkleriyle hazırlanmıştır. XML importunda 8 ürün yayındadır.
- `/urunler/refraktometre`: Refraktometre ve dijital refraktometre odağında; Kyoto KEM, Mettler Toledo ve Bellingham + Stanley marka linkleri, sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis iç linkleriyle hazırlanmıştır. XML importunda 4 ürün yayındadır.
- `/urunler/viskozimetre`: Viskozimetre ve viskozite ölçüm cihazları odağında; Brookfield ve Lamy marka linkleri, devir kalibrasyonu, sıcaklık kalibrasyonu ve laboratuvar cihazları teknik servis iç linkleriyle hazırlanmıştır. XML importunda 6 ürün yayındadır. Brookfield logo dosyası kaynak klasörde bulunmadığı için marka alanı şimdilik metin fallback ile çalışır.
- `/urunler/homojenizator`: Homojenizatör ve laboratuvar numune hazırlama cihazı odağında; VELP ve Weightlab marka linkleri, devir kalibrasyonu, laboratuvar cihazları teknik servis, mekanik karıştırıcı ve manyetik karıştırıcı iç linkleriyle hazırlanmıştır. XML importunda 1 ürün yayındadır.
- `/urunler/manyetik-karistirici`: Manyetik karıştırıcı, ısıtmalı manyetik karıştırıcı ve laboratuvar karıştırıcı odağında; VELP ve Weightlab marka linkleri, devir kalibrasyonu, sıcaklık kalibrasyonu, laboratuvar cihazları teknik servis ve mekanik karıştırıcı iç linkleriyle hazırlanmıştır. XML importunda 15 ürün yayındadır.
- `/urunler/etuv`: Etüv cihazı, laboratuvar etüvü ve kurutma etüvü odağında; Weightlab marka linki, sıcaklık kalibrasyonu, laboratuvar cihazları teknik servis ve nem tayin iç linkleriyle hazırlanmıştır. XML importunda 4 ürün yayındadır.
- `/urunler/termoreaktor`: Termoreaktör, laboratuvar termoreaktör ve laboratuvar sindirim cihazı odağında; VELP marka linki, sıcaklık kalibrasyonu, laboratuvar cihazları teknik servis, balon ısıtıcı ve manyetik karıştırıcı iç linkleriyle hazırlanmıştır. XML importunda şu an yayındaki ürün yoktur, boş durum ekranı çalışır.

Bu özel kategori sayfalarında meta title, meta description, H1, hero açıklaması, CTA metinleri, H2 içerik blokları, kullanım alanları, SSS, marka logo alt textleri ve ürün/görsel alanı alt metinleri kategori brief'lerine göre düzenlenmiştir.

## 11. Markalar

Sitedeki markalar:

- VELP
- Weightlab
- A&D
- Cole-Parmer Stuart
- Brookfield
- Lamy
- Mettler Toledo
- Ohaus
- WTW
- Shimadzu
- Kyoto KEM
- Bellingham + Stanley
- SI Analitik
- TitroLine 7500 KF

Marka sayfaları şu şekilde kurgulanmalıdır:

- Marka adı ve kısa açıklaması
- Marka logosu
- Markaya ait ürünler
- Markanın yer aldığı kategoriler
- İlgili hizmet / teknik servis yönlendirmeleri
- SEO uyumlu marka açıklaması

## 12. Kategori - Marka Eşleşme Mantığı

Tüm XML ürünleri alınmamıştır. Ürünler, belirlenen kategori ve marka ilişkisine göre filtrelenmiştir.

Örnek eşleşmeler:

- Teraziler: A&D, Shimadzu, Ohaus, Weightlab
- Nem Tayin: A&D, Ohaus, Weightlab, Shimadzu
- Kral Fischer: Mettler Toledo, SI Analitik, TitroLine 7500 KF, Kyoto KEM
- Potansiyometrik Titratörler: Mettler Toledo, SI Analitik, Kyoto KEM
- Densitometre: Kyoto KEM, Mettler Toledo, Bellingham + Stanley
- Refraktometre: Kyoto KEM, Mettler Toledo, Bellingham + Stanley
- pH Metre: Mettler Toledo, Ohaus, WTW
- pH & İletkenlik: Mettler Toledo, Ohaus, WTW
- Viskozimetre: Brookfield, Lamy
- Etüv: Weightlab
- Balon Isıtıcılar: VELP, Weightlab
- Termoreaktör: VELP
- Termal Analiz: Cole-Parmer Stuart
- Homojenizatör: VELP, Weightlab
- Mekanik Karıştırıcı: VELP, Weightlab
- Manyetik Karıştırıcı: VELP, Weightlab

## 13. Ürün - Hizmet SEO Eşleşmesi

SEO açısından ürün sayfaları yalnızca katalog sayfası olarak düşünülmemelidir. Ürün kategorileri ilgili kalibrasyon hizmetleriyle ilişkilendirilir.

Örnek ilişki:

- Teraziler → Kütle & Terazi Kalibrasyonu
- Nem Tayin → Kütle & Terazi Kalibrasyonu, Sıcaklık Kalibrasyonu
- Kral Fischer → Hacim Kalibrasyonu, Sıcaklık Kalibrasyonu
- Potansiyometrik Titratörler → Hacim Kalibrasyonu
- Densitometre → Sıcaklık Kalibrasyonu, Hacim Kalibrasyonu
- Refraktometre → Sıcaklık Kalibrasyonu
- pH Metre → Sıcaklık Kalibrasyonu
- Viskozimetre → Devir Kalibrasyonu, Sıcaklık Kalibrasyonu
- Etüv → Sıcaklık Kalibrasyonu
- Karıştırıcı ve homojenizatör grupları → Devir Kalibrasyonu

Bu yapı sayesinde ürün sayfaları, ilgili hizmet sayfalarına iç link verir. Hizmet sayfaları da ilgili ürün kategorilerine yönlenebilir. Böylece hem kullanıcı akışı hem de SEO iç bağlantı yapısı güçlenir.

## 14. Blog ve İçerik Yapısı

Blog, sitede "Blog" başlığı altında konumlandırılır.

Blog hedefleri:

- Kalibrasyon periyotları
- Cihaz bakım önerileri
- Laboratuvar cihazı seçim rehberleri
- Terazi, pH metre, viskozimetre, titratör gibi ürün grupları için satın alma rehberleri
- Teknik servis öncesi kontrol listeleri
- SEO ve AEO/GEO odaklı soru-cevap içerikleri

Blog genel sayfası, blog detay sayfası ve kategori sayfaları tasarım olarak hazırlanmıştır. İçerikler ileride uzman metinleriyle zenginleştirilecektir.

## 15. Sertifikalar Sayfası

Sertifika sorgulama özelliği şimdilik kapsam dışıdır. Site üzerinde sertifika sorgulama modülü bulunmamalıdır.

Buna rağmen `/sertifikalar` sayfası kesinlikle korunmalıdır. Bu sayfa ileride MTA Endüstri'ye ait sertifika, yetkinlik ve kurumsal belge dosyalarının listeleneceği alan olarak kullanılacaktır.

## 16. SEO Yaklaşımı

Site SEO uyumlu yapı için hazırlanmıştır.

Önemli SEO başlıkları:

- Her hizmet için ayrı URL
- Her teknik servis için ayrı URL
- Her kategori için ayrı URL
- Her marka için ayrı URL
- Her ürün için ayrı URL
- Blog detay ve blog kategori sayfaları
- Meta title ve meta description alanları
- Canonical URL mantığı
- Schema altyapısı
- Sitemap yapısı
- Eski WordPress URL'leri için 301 redirect planı
- Görsellerde açıklayıcı alt metinler
- Ürün-hizmet iç link ilişkisi

SEO tonu teknik, güven veren ve net olmalıdır. Abartılı satış dili yerine ölçüm güvenilirliği, teknik yetkinlik, izlenebilirlik, bakım, servis ve süreç yönetimi vurgulanmalıdır.

Tamamlanan SEO içerik paketleri:

- Ana sayfa `/`: "kalibrasyon firmaları" ana hedefiyle; kalibrasyon hizmetleri, teknik servis ve laboratuvar cihazları konumlandırması tamamlandı.
- `/hizmetler`: "kalibrasyon hizmetleri" ana hedefiyle; kalibrasyon hizmet alanları, süreç, cihaz kapsamı, teknik servis bağlantısı, ürün kataloğu bağlantısı, CTA ve SSS yapısı tamamlandı.
- `/urunler/teraziler`
- `/urunler/nem-tayin`
- `/urunler/ph-metre`
- `/urunler/ph-iletkenlik`
- `/urunler/kral-fischer`
- `/urunler/potansiyometrik-titratorler`
- `/urunler/densitometre`
- `/urunler/refraktometre`
- `/urunler/viskozimetre`
- `/urunler/homojenizator`
- `/urunler/manyetik-karistirici`
- `/urunler/etuv`
- `/urunler/termoreaktor`
- `/hizmetler/basinc-kalibrasyonu`
- `/hizmetler/devir-kalibrasyonu`
- `/hizmetler/tork-kalibrasyonu`
- `/hizmetler/kutle-terazi-kalibrasyonu`
- `/hizmetler/sicaklik-kalibrasyonu`
- `/hizmetler/hacim-kalibrasyonu`
- `/teknik-servis/laboratuvar-cihazlari-icin-teknik-servis`
- `/teknik-servis/analiz-ve-olcum-cihazlari-teknik-servis`
- `/teknik-servis/terazi-teknik-servis`

Bu sayfalarda hedef anahtar kelimeye uygun meta alanları, H1/H2 mimarisi, iç linkler, CTA alanları, SSS ve görsel alt metinleri uygulanmıştır.

## 17. Görsel Kimlik

Görsel dil, MTA Endüstri logosu ve logo renkleri üzerinden kurgulanır.

Tasarım beklentisi:

- Kurumsal
- Temiz
- Teknik
- Güven veren
- Modern ama fazla gösterişli olmayan
- Laboratuvar / endüstri alanına uygun
- Ürün ve hizmet sayfalarında okunabilir, düzenli ve SEO dostu

Kullanılan görsel kaynakları:

- Ana logo ve favicon: `public/images/mta-endustri-logo-2.png`
- Hizmet görselleri: `public/images/services`
- Teknik servis görselleri: `public/images/technical-service`
- Marka logoları: `public/images/brands`
- Ürün görselleri: `public/images/products`

## 18. Lead ve Teklif Akışı

Site şu anda lead toplama mantığına göre ilerlemektedir.

Hedef lead kaynakları:

- Ürün detay sayfaları
- Hizmet detay sayfaları
- Teknik servis sayfaları
- İletişim sayfası
- Genel teklif al CTA alanları

İleride formlarda tutulması gereken bilgiler:

- Ad soyad
- Firma
- Telefon
- E-posta
- Talep konusu
- İlgili ürün / hizmet
- Kaynak URL
- UTM bilgisi
- Lead durumu
- Admin notları

## 19. Admin Panel Hedefi

Filament admin panel ilerleyen aşamada içerik yönetimi için kullanılacaktır.

Admin panelde yönetilmesi planlanan alanlar:

- Hizmetler
- Teknik servis sayfaları
- Ürünler
- Ürün kategorileri
- Markalar
- Blog yazıları
- Sayfalar
- SSS
- SEO alanları
- Lead formları
- Medya ve dokümanlar
- Trafik / lead akışı dashboard ekranları

## 20. Kısa Marka Mesajı

MTA Endüstri, kalite kontrol ve AR-GE laboratuvarları için cihaz tedariği, teknik servis ve kalibrasyon hizmetlerini bir arada sunan teknik çözüm ortağıdır. Firma; laboratuvar cihazları, ölçüm ekipmanları, tartım sistemleri ve analiz cihazlarında güvenilir tedarik, satış sonrası destek ve ölçüm güvenilirliği odağıyla konumlandırılır.

## 21. Kısa Site Mesajı

MTA Endüstri web sitesi; kalibrasyon hizmetleri, teknik servis çözümleri ve laboratuvar cihazları ürün kataloğunu SEO uyumlu bir kurumsal yapı içinde birleştirir. Kullanıcılar hizmetleri inceleyebilir, ürünleri kategori ve markaya göre filtreleyebilir, ilgili ürün-hizmet bağlantılarını görebilir ve teklif talebi oluşturabilir.

## 22. Mevcut Durum Özeti

Tamamlanan ana işler:

- Laravel site altyapısı kuruldu.
- Ön yüz sayfa yapıları oluşturuldu.
- Logo, favicon, marka logoları, hizmet görselleri ve ürün görselleri site klasörlerine taşındı.
- 6 kalibrasyon hizmeti oluşturuldu.
- 3 teknik servis sayfası oluşturuldu.
- Ürün kategori ve marka yapısı oluşturuldu.
- WordPress XML'den 165 uygun ürün yayına alındı.
- Ürünlerde kategori, marka, görsel, özellik ve metadata yapısı oluşturuldu.
- Ürün-hizmet SEO ilişki mantığı kuruldu.
- Hakkımızda ve iletişim bilgileri güncellendi.
- Sosyal medya linkleri eklendi.
- Sertifikalar sayfası korundu, sertifika sorgulama kaldırıldı.
- Mega menü ve kurumsal header/footer yapısı geliştirildi.
- Ana sayfa SEO içeriği brief'e göre güncellendi.
- Ana kalibrasyon hizmetleri `/hizmetler` sayfası özel SEO brief'e göre geliştirildi.
- Ana teknik servis `/teknik-servis` sayfası özel SEO brief'e göre geliştirildi.
- Ana ürün katalog `/urunler` sayfası özel SEO brief'e göre geliştirildi.
- `/markalar` marka listeleme sayfası eklendi ve laboratuvar cihazları markaları SEO brief'ine göre hazırlandı.
- `/urunler/marka/and` marka detay sayfası A&D hassas terazi ve laboratuvar tartım cihazları hedefiyle özel SEO içeriğe bağlandı.
- `/urunler/teraziler/and-fz-500i-hassas-terazi` örnek ürün detay sayfası özel SEO brief'e göre dolduruldu.
- `/bilgi-merkezi` teknik kütüphane mantığıyla güncellendi; `/blog` ayrı yayın akışı sayfası olarak eklendi.
- `/bilgi-merkezi/basinc-kalibrasyonu-nedir` rehber detayı ve `/bilgi-merkezi/kategori/kalibrasyon-rehberleri` kategori sayfası özel SEO brief'lere göre geliştirildi.
- `/hakkimizda`, `/iletisim`, `/sertifikalar` ve `/referanslar` sayfaları verilen kurumsal brief'lere göre güncellendi. Sertifikalarda sorgulama modülü yoktur; referanslarda doğrulanmamış müşteri adı veya logo kullanılmaz.
- `docs/SAYFA_METIN_HARITASI.md` oluşturuldu. Bu dosya sitemap içindeki 230 URL için ortak header/footer metinlerini, meta title/description alanlarını ve her sayfanın ana içerik H1-H2-H3/metin akışını sayfa sayfa listeler.
- Özel SEO kategori içerikleri tamamlandı: teraziler, nem tayin, pH metre, pH & iletkenlik, Kral Fischer, potansiyometrik titratörler, densitometre, refraktometre, viskozimetre, etüv, termoreaktör, homojenizatör ve manyetik karıştırıcı.
- Basınç Kalibrasyonu hizmet sayfası özel SEO brief'e göre geliştirildi.
- Devir Kalibrasyonu ve Tork Kalibrasyonu hizmet sayfaları özel SEO brief'lere göre geliştirildi.
- Kütle & Terazi Kalibrasyonu hizmet sayfası özel SEO brief'e göre geliştirildi.
- Sıcaklık Kalibrasyonu ve Hacim Kalibrasyonu hizmet sayfaları özel SEO brief'lere göre geliştirildi.
- pH & İletkenlik, Densitometre, Kral Fischer, Potansiyometrik Titratörler ve Termoreaktör ürün kategori sayfaları özel SEO brief'lere göre geliştirildi.
- Terazi Teknik Servis, Laboratuvar Cihazları Teknik Servis ve Analiz/Ölçüm Cihazları Teknik Servis sayfaları özel SEO brief'lere göre geliştirildi.
- Ürün kategori sayfalarında kategoriye özel ürün görsel alt metni / görsel alanı metni üretimi eklendi.

Devam edecek ana işler:

- Filament admin kaynaklarının oluşturulması
- Lead form kayıt ve bildirim akışı
- SEO yönetim ekranları
- Redirect haritası
- Canlı domain sitemap/robots kontrolleri
- Gerçek sertifika ve referans içerikleri
- Mobil/tablet/desktop responsive QA
- Performans ve görsel optimizasyon
- cPanel canlı yayın hazırlığı

## 23. Yeni Sayfa Mimarisinde Öne Çıkan Noktalar

- `/markalar`: Ürün kataloğundaki gerçek marka verileriyle çalışan marka listeleme sayfasıdır. Marka logoları varsa gösterilir; logo yoksa metin tabanlı kart yapısı korunur.
- `/urunler/marka/and`: A&D marka detay sayfasıdır. A&D hassas terazi, analitik terazi ve laboratuvar tartım cihazları hedefiyle özel meta, H1, açıklama, ürün listesi ve iç linkler içerir.
- `/urunler/teraziler/and-fz-500i-hassas-terazi`: A&D FZ-500i için özel ürün detay sayfasıdır. 520 g kapasite, 0.001 g okunabilirlik, harici kalibrasyon, LCD ekran ve laboratuvar tartım kullanım bilgileri tabloya işlenmiştir.
- `/bilgi-merkezi`: Kalibrasyon, ürün seçimi, teknik servis ve bakım rehberleri için teknik kütüphane sayfasıdır.
- `/blog`: Aynı içerik havuzunu daha yayın/dergi akışı gibi sunan blog ana sayfasıdır.
- `/bilgi-merkezi/kategori/kalibrasyon-rehberleri`: Basınç, sıcaklık, terazi, hacim, devir ve tork kalibrasyonu rehberlerine ve ilgili hizmet sayfalarına bağlanan kategori sayfasıdır.
- `/bilgi-merkezi/basinc-kalibrasyonu-nedir`: Basınç kalibrasyonu, manometre kalibrasyonu, basınç ölçer kalibrasyonu ve kalibrasyon raporu odaklı rehber detay sayfasıdır.
- `routes/web.php`: `/markalar` ve `/blog` route'ları eklendi.
- `app/Http/Controllers/SiteController.php`: Marka listeleme, marka özel SEO, ürün özel SEO, bilgi merkezi, blog ve rehber kategori içerikleri merkezi helper metodlarla yönetilir.
- `resources/views/pages/brands.blade.php` ve `resources/views/pages/blog.blade.php`: Yeni sayfa şablonlarıdır.
- `docs/SAYFA_METIN_HARITASI.md`: Güncel sitedeki tüm sitemap URL'lerinin sayfa metni envanteridir. İçerik revizyonu, SEO kontrolü ve dış yapay zeka brief paylaşımı için kullanılabilir.
- `tools/export-page-text-map.php`: Canlı yerel siteyi okuyarak `docs/SAYFA_METIN_HARITASI.md` dosyasını yeniden üreten yardımcıdır.
