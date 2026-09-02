# MTA Endüstri — AI Handoff (özet)

> Bu dosya **kısa tutulur**. Ayrıntılı tarihsel kayıtlar: [`docs/HANDOFF-ARCHIVE-2026-09.md`](docs/HANDOFF-ARCHIVE-2026-09.md).
> Deploy/güncelleme runbook'u ve cutover planı: [`docs/DEPLOY-CPANEL.md`](docs/DEPLOY-CPANEL.md).
> Son güncelleme: 2026-09-02.

## Kısa durum

Laravel 13 + Blade + Vite + Tailwind v4 + Filament v4. cPanel/MySQL hedefli, SEO odaklı kalibrasyon & teknik ürün kataloğu sitesi. Sepet yok — katalog + teklif talebi mantığı.

- **Ön yüz:** ana sayfa, `/kapsam`, `/urunler` (filtreli katalog), hizmet & teknik servis detay, kategori/marka sayfaları, blog, kurumsal sayfalar, dinamik `robots.txt` + `sitemap.xml`, JSON-LD.
- **Yeni tasarım dili (Tailwind, teal-700 / slate-900 / amber-600) TAMAM:** ana sayfa, `/kapsam`, `/urunler` + kategori + marka, kalibrasyon + teknik servis detay, ürün detay (PDP kendi scoped sistemi), blog + blog detay, teklif-al, iletişim, yasal sayfalar, header/footer/mega menü.
- **Hâlâ eski `app.css` semantik sınıflarında (9 sayfa):** `/markalar`, `/hizmetler` liste, `/teknik-servis` liste, `/bilgi-merkezi` + kategori, `/hakkimizda`, `/sertifikalar`, `/referanslar`, `/ara`.
- **Admin (Filament, `/admin`):** hizmet, teknik servis, ürün, kategori, marka, blog, sayfa, SSS, lead, redirect, SEO, schema, site ayarları + **Kapsam Kategorileri / Kapsam Grupları**.
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
| Layout | `resources/views/layouts/site.blade.php` (head/meta/OG/JSON-LD, top bar, mega menü v3, footer, mobil drawer) |
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
  ea-php83 artisan migrate --force              # yeni migration varsa
  ea-php83 artisan mta:sync-scope               # yeni kapsam config'i varsa
  ea-php83 artisan optimize:clear
  ea-php83 artisan view:cache && ea-php83 artisan config:cache && ea-php83 artisan route:cache && ea-php83 artisan filament:optimize
```
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

### Kalite / polish
- [ ] Responsive QA (mobil/tablet/geniş ekran) (arşiv #71).
- [ ] Görsel optimizasyonu: WebP/AVIF, boyutlandırma (arşiv #74).
- [ ] Kullanılmayan starter font/asset temizliği; `fontaine` build uyarısı (arşiv #75).
- [ ] Lighthouse / Core Web Vitals (arşiv #76).
- [ ] Kalan 9 sayfayı yeni Tailwind tasarım diline geçir: `/markalar`, `/hizmetler` liste, `/teknik-servis` liste, `/bilgi-merkezi` + kategori, `/hakkimizda`, `/sertifikalar`, `/referanslar`, `/ara`.
- [ ] Teknik servis eski URL redirect'leri; schema çıktıları gerçek içerikle gözden geçirme (arşiv #23, #26).
- [ ] Test kapsamı: sitemap + import + lead form testleri (arşiv #86). (`PublicPagesTest` eklendi — yasal/teklif/blog/iletişim smoke.)
- [ ] GSC + GA4 kurulumu (arşiv #83); yedekleme planı (arşiv #82); canlı SEO checklist (arşiv #87).
