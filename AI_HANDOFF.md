# MTA Endüstri — AI Handoff (özet)

> Bu dosya **kısa tutulur**. Ayrıntılı tarihsel kayıtlar: [`docs/HANDOFF-ARCHIVE-2026-09.md`](docs/HANDOFF-ARCHIVE-2026-09.md).
> Deploy/güncelleme runbook'u ve cutover planı: [`docs/DEPLOY-CPANEL.md`](docs/DEPLOY-CPANEL.md).
> Son güncelleme: 2026-09-02.

## Kısa durum

Laravel 13 + Blade + Vite + Tailwind v4 + Filament v4. cPanel/MySQL hedefli, SEO odaklı kalibrasyon & teknik ürün kataloğu sitesi. Sepet yok — katalog + teklif talebi mantığı.

- **Ön yüz:** ana sayfa, `/kapsam`, `/urunler` (filtreli katalog), hizmet & teknik servis detay, kategori/marka sayfaları, blog, kurumsal sayfalar, dinamik `robots.txt` + `sitemap.xml`, JSON-LD.
- **Yeni tasarım dili (Tailwind, teal-700 / slate-900 / amber-600) — TÜM SAYFALAR TAMAM:** ana sayfa, `/kapsam`, `/urunler` + kategori + marka, kalibrasyon + teknik servis liste + detay, ürün detay (PDP kendi scoped sistemi), blog + blog detay, `/markalar`, `/bilgi-merkezi` + kategori, `/hakkimizda`, `/sertifikalar`, `/referanslar`, `/ara`, teklif-al, iletişim, yasal sayfalar, header/footer/mega menü. Eski `.cui-*`/`.taxonomy-card`/`.catalog-*` partial'ları artık kullanılmıyor (silinebilir).
- **Yetkili servis rozeti:** `config/mta.php` → `authorized_services` (Bahco = tork kalibrasyon + tork anahtarları servisi). Top bar linki + hizmet/teknik servis detayda hero rozeti + "Yetkili Merkez Servisi" bloğu + ana sayfa marka şeridi etiketi + teknik servis listesinde amber ring'li kart. `SiteController::authorizedServiceFor()`.
- **Admin (Filament, `/admin`):** hizmet, teknik servis, ürün, kategori, marka, blog, sayfa, SSS, lead, redirect, SEO, schema, site ayarları + Kapsam Kategorileri / Kapsam Grupları. Menü grupları `AdminPanelProvider::navigationGroups()` ile sıralı (Satış > Katalog > Hizmetler > İçerik > SEO > Site Ayarları > Güvenlik); her resource'ta `navigationSort`.
- **Canlı demo:** `https://demo.mtaend.com` (HTTP Basic: `mtademo` / `MTAdemo!2026`; `X-Robots-Tag: noindex`). Admin: `admin@mtaend.com` / `MtaPanel!2026x`.

## Mimari / dosya haritası

| Alan | Yer |
|---|---|
| Rotalar | `routes/web.php` (statik path'ler `/{any}` fallback'inden önce) |
| Ana controller | `app/Http/Controllers/SiteController.php` — config/DB'den okur, Blade'e verir; `meta()`, `schemaGraph()`, `quoteCta()`, `*Data()` helper'ları |
| Statik sözlük | `config/mta.php` (site, services, technical_services, product_categories/brands, product_category_brands, products fallback, articles, faqs) |
| Kapsam verisi | `config/mta-scope.php` → `php artisan mta:sync-scope` ile `scope_categories` / `scope_groups` tablolarına. DB doluysa oradan, yoksa config. **Varsayılan sync sadece eksik ekler; `--force` ezer.** |
| İçerik DB'ye | `php artisan mta:sync-content` (config + import JSON → tablolar). Kapsam'ı etkilemez. |
| Ürün import | `php artisan mta:import-products` (WordPress XML → normalize JSON). `tools/seed-*.php` **SQLite'a hardcoded**, sunucuda çalışmaz — ürün düzenleme panelden. |
| Layout | `resources/views/layouts/site.blade.php`. Top bar: sol Bahco rozeti, sağ telefon+e-posta+sosyal. Ana menü sırası: Kalibrasyon > Teknik Servis > Ürünler > Kapsam > Kurumsal (açılır: Hakkımızda/Sertifikalar/Blog/İletişim); arama en sonda. Ürünler mega: 15 kategori 5×3 grid + alt marka logo şeridi (`$megaBrandLogos`, diskteki `.png`'ler). Kalibrasyon/Teknik mega açıklamaları `Str::limit(...,150)` + `.mega-svc-body span` 3-satır clamp. `.mb-cta` turuncu, `.mb-icon-btn` WhatsApp yeşili. |
| Mega menü verisi | `AppServiceProvider` view composer `megaFeatureProducts` (kategori → temsili ürün foto+ad) |
| CSS | `resources/css/app.css` (`@import 'tailwindcss'` + `@source '../views/**'` → blade içi utility'ler derlenir) + eski semantik sınıflar |
| JS | `resources/js/app.js` → `setupCatalog` (grid/list toggle, `catalog.js`), `setupHeader`, `setupPdp`, `setupScope`, `setupProductMega`, `setupCatalogPage` |
| Lead formu | `POST /iletisim` + `POST /teklif-al` → `SiteController::submitLead` → `session('lead_success')`. Alanlar: name, phone, message zorunlu; company, email opsiyonel; honeypot `website`. |

## Nasıl çalışılır

```bash
php artisan serve            # :8000 (kullanıcının terminalinde zaten açık olabilir)
npm run build                # değişiklik sonrası (Vite 8, Node 20+ ister)
php artisan test             # 13 test
```
- Değişiklik sonrası: `npm run build` + `php artisan test` + commit + `git push` (`origin https://github.com/serloading/mta.git`, branch `main`).
- Browser pane'de uzun sayfa **scroll sonrası screenshot güvenilmez** — doğrulamayı `javascript_tool` ile DOM/computed-style üzerinden yap.

### Demo sunucuya deploy (SSH)
Host `65.109.68.25` port **2220** kullanıcı `mtaend`, key `~/.ssh/mtaend_deploy`. PHP CLI = **`ea-php83`** (varsayılan `php` 8.1). App: `~/public_html/mta-demo/` (docroot `.../public`).
```bash
# lokal: npm run build
scp -P 2220 -i ~/.ssh/mtaend_deploy -r public/build mtaend@65.109.68.25:~/public_html/mta-demo/public/
ssh -i ~/.ssh/mtaend_deploy -p 2220 mtaend@65.109.68.25
  cd ~/public_html/mta-demo && git pull --ff-only
  composer dump-autoload -o          # composer.json autoload değiştiyse (ör. app/helpers.php)
  ea-php83 artisan migrate --force              # yeni migration varsa
  ea-php83 artisan mta:sync-scope               # yeni kapsam config'i varsa
  ea-php83 artisan optimize:clear
  ea-php83 artisan view:cache && ea-php83 artisan config:cache && ea-php83 artisan route:cache && ea-php83 artisan filament:optimize
```
- Sunucuda PHP CLI `ea-php83` = `/usr/local/bin/ea-php83`; `composer` = `/usr/local/bin/composer`.
- `public/images/**/*.webp` git'te tutuluyor (`tools/optimize-images.php` üretir) → `git pull` ile gelir, ayrı scp gerekmez.
- `public/.htaccess` sunucuda staging bloğu (noindex + basic auth) içerir → `git update-index --skip-worktree` set edilmiş, `git reset --hard` YAPMA. Yedek: `public/.htaccess.demo`.

## Açık işler

### Yayın öncesi zorunlu
- [ ] **AutoSSL** — `demo.mtaend.com` self-signed sertifikada. WHM Terminal: `/usr/local/cpanel/bin/autossl_check_user mtaend` (`.well-known/` şifreden muaf, hazır).
- [ ] **SMTP** — demo `MAIL_MAILER=log`; lead formu kimseye e-posta atmıyor. Canlıda cPanel SMTP gir.
- [ ] **`config/mta-scope.php` değerleri** (U / aralık / metot) referans HTML'den alındı — laboratuvarın gerçek TÜRKAK kapsamıyla **analist teyidi** gerekli.
- [ ] Hizmet sayfalarındaki akreditasyon/kapsam ifadeleri gerçek belgeye göre kesinleştirilecek (arşiv #29).
- [ ] Blog içerikleri gerçek uzman metinleriyle doldurulacak; blog kategori yapısı netleştirilecek (arşiv #27-28).
- [ ] Referans / sertifika belge içerikleri gerçek dosyalar gelince (arşiv #31).

### Ana domaine geçiş (`mtaend.com`)
- [ ] WordPress tam yedek → docroot'u Laravel `public/`'e çevir → `.env` `APP_URL` + staging `.htaccess` bloğunu kaldır → cache → eski WP URL 301 haritası (`redirects` kaynağı) → sitemap Search Console. Detay: `docs/DEPLOY-CPANEL.md`.

### SEO — sonraki tur
- [ ] Kalan generic-içerikli kategoriler için özel SEO arm'ı: `titratorler` (root), `pipetler`, `rotasyonel-viskozimetre`, alt kategoriler.
- [ ] `terazi-kalibrasyonu-nedir`, `sicaklik-kalibrasyonu-nedir` bilgi yazıları (uzun kuyruk hizmet kelimeleri).
- [ ] İş kararı: otoklav / NIR / kül fırını / brookfield / memmert tedariki → sayfa açılır mı? (bkz. `SEO/MTA_SEO_AKSIYON_RAPORU_2026-09.md`)
- [ ] Slug 301'leri: `/urunler/polarimetre`→`polarimetreler`, `vorteks-karistirici`→`vorteks-karistiricilar`, `hotplate`→`hot-plate` (admin → 301 Yönlendirmeler).
- [ ] Eski WP URL → yeni URL 301 haritası (cutover öncesi tam liste).

### Kalite / polish
- [ ] Responsive QA (mobil/tablet/geniş ekran) (arşiv #71).
- [x] ~~Görsel optimizasyonu: WebP~~ — `tools/optimize-images.php` + `img_url()` helper; 307 webp, %76 küçülme (2026-09-02). AVIF ve responsive `srcset` hâlâ yapılabilir.
- [ ] Kullanılmayan starter font/asset temizliği; `fontaine` build uyarısı (arşiv #75).
- [ ] Lighthouse / Core Web Vitals (arşiv #76).
- [x] ~~Kalan 9 sayfayı yeni tasarıma geçir~~ — tamamlandı (2026-09-02).
- [x] ~~Kullanılmayan blade partial temizliği~~ — 8 partial silindi (2026-09-02).
- [~] Ölü legacy CSS: `.cui-*` / `.clower-*` silindi (-1019 satır). `.section` / `.cta-band` / `.ft-*` blokları hâlâ app.css'te (`.desktop-nav` + `.product-gallery-strip` ile iç içe — satır satır temizlik gerekiyor).
- [x] ~~FAQPage + GeoCoordinates JSON-LD~~ — eklendi (2026-09-02). `Product` schema PDP'de hâlâ yok.
- [ ] Test kapsamı: sitemap + import + lead form testleri (arşiv #86). (`PublicPagesTest` var — 19/19.)
- [ ] GSC + GA4 kurulumu (arşiv #83); yedekleme planı (arşiv #82); canlı SEO checklist (arşiv #87).
