# cPanel Deploy — MTA Endüstri

## Demo / staging: `demo.mtaend.com`

Kuruldu: 2026-09-01. Sunucu `65.109.68.25` (server.navvo.com), SSH port **2220**, kullanıcı `mtaend`.

| | |
|---|---|
| Uygulama kökü | `/home/mtaend/public_html/mta-demo` |
| DocumentRoot (cPanel subdomain) | `/home/mtaend/public_html/mta-demo/public` |
| PHP | `ea-php83` (8.3) — CLI'da `ea-php83 artisan ...` (varsayılan `php` = 8.1) |
| DB | MySQL/MariaDB `mtaend_newdb11` / kullanıcı `mtaend_newus11` (şifre `.env` içinde) |
| Kod | GitHub `serloading/mta` (public) `main`, sunucuda `git` checkout |
| Asset | Lokalde `npm run build` → `public/build/` sunucuya `scp` (sunucu Node 18, Vite 8'e yetmez) |
| `.env` | `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://demo.mtaend.com`, `MAIL_MAILER=log` |
| Admin | `admin@mtaend.com` / `MtaPanel!2026x` — `/admin` (Filament) |
| Erişim koruması | HTTP Basic (`mtademo` / `MTAdemo!2026`) — `public/.htaccess` içinde, `.htpasswd` uygulama kökünde |
| Arama motoru | `X-Robots-Tag: noindex` header (staging `.htaccess`) |

### Server ön koşulları (yapıldı)
- EasyApache 4 → `ea-php83-php-intl` + `ea-php83-php-fileinfo` kuruldu (WHM Terminal: `dnf install -y ea-php83-php-intl ea-php83-php-fileinfo`). **Filament `intl` olmadan çalışmaz.**
- `demo.mtaend.com` cPanel subdomain'i + `/scripts/rebuildhttpdconf` + `/scripts/restartsrv_httpd` + `/scripts/restartsrv_apache_php_fpm`.

### DNS (KULLANICI — Cloudflare)
`mtaend.com` NS'leri Cloudflare (`olga/terin.ns.cloudflare.com`). cPanel subdomain **public DNS kaydı oluşturmaz.**
Cloudflare → DNS → **A** kaydı: `demo` → `65.109.68.25`, **Proxy: DNS only (gri bulut)**. Yayılma ~1-5 dk.
Kayıt gelmeden site yalnızca `--resolve` / hosts ile erişilir.

### `public/.htaccess` git koruması
`public/.htaccess` repo'da tracked ama sunucuda staging bloğu (noindex + basic auth) eklenmiş.
`git update-index --skip-worktree public/.htaccess` set edildi → normal `git pull` dokunmaz.
Yedek: `public/.htaccess.demo` (untracked). `git reset --hard` YAPMA (skip-worktree'yi ezebilir); güncellemede `git pull` kullan.

## Güncelleme runbook (demo)

Lokalde:
```bash
npm run build
git push
```
Sunucuda (`ssh -p 2220 mtaend@65.109.68.25`, `cd ~/public_html/mta-demo`):
```bash
git pull --ff-only
# asset değiştiyse: lokalden scp -P 2220 -r public/build mtaend@65.109.68.25:~/public_html/mta-demo/public/
ea-php83 artisan migrate --force          # yeni migration varsa
ea-php83 artisan config:cache
ea-php83 artisan route:cache
ea-php83 artisan view:cache
ea-php83 artisan filament:optimize
```

İçeriği (ürün/kategori/…) lokal DB'den yeniden taşımak gerekirse: lokalde tablo satırlarını JSON'a dök,
sunucuda `SET FOREIGN_KEY_CHECKS=0` + `DB::table()->truncate()` + chunk `insert()` (bkz. 2026-09-01 kurulum).
`tools/seed-*.php` scriptleri **SQLite'a hardcoded** — sunucuda çalışmaz.

## Bilinen açık iş
- `robots.txt` route'u hâlâ `Allow: /` döndürüyor (staging'de `noindex` header + basic auth kapsıyor). Canlıya geçişte önemsiz.
- Lead formu `MAIL_MAILER=log` — e-posta gitmiyor; canlıda cPanel SMTP gir.
- `MAIL`, gerçek `robots`, sitemap Search Console — canlı cutover checklist'i.

## Ana domaine geçiş (`mtaend.com`) — İLERİDE

1. **WordPress'i tam yedekle** (cPanel full backup + `public_html` + WP DB export). `gunerkan_mtaendistanbuldb.sql` gibi eski dump'lar home'da mevcut.
2. `mtaend.com` DocumentRoot'unu `.../mta-demo/public` (ya da yeni bir `mta-live/public`) yap; WP dosyalarını `public_html/_wp-backup/`'a taşı.
3. `.env`: `APP_URL=https://mtaend.com`; `public/.htaccess`'ten staging bloğunu (noindex + basic auth) çıkar (`cp public/.htaccess.demo`… değil — repo default'a dön: `git update-index --no-skip-worktree public/.htaccess && git checkout -- public/.htaccess`).
4. `ea-php83 artisan config:cache route:cache view:cache filament:optimize`
5. Eski WP permalink'leri → yeni URL 301 haritası: admin `redirects` kaynağı + `redirectFallback`.
6. Cloudflare: `mtaend.com` / `www` A kaydı sunucuya; sitemap'i Search Console'a gönder.
7. cPanel Directory Privacy / basic auth kaldır.
