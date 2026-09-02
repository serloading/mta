<?php

/**
 * public/images/products/ altında hiçbir ürüne bağlı OLMAYAN görselleri
 * public/images/_orphan/ klasörüne taşır (webp eşleri dahil).
 *
 *   php artisan tinker --execute="require 'tools/move-orphan-images.php';"          # dry-run
 *   APPLY=1 php artisan tinker --execute="require 'tools/move-orphan-images.php';"  # taşı
 *   RESTORE=1 php artisan tinker --execute="require 'tools/move-orphan-images.php';" # geri al
 */

use App\Models\Product;
use Illuminate\Support\Str;

$src = public_path('images/products');
$dst = public_path('images/_orphan');
$apply = (bool) (getenv('APPLY') ?: ($_SERVER['APPLY'] ?? false));
$restore = (bool) (getenv('RESTORE') ?: ($_SERVER['RESTORE'] ?? false));

if ($restore) {
    if (! is_dir($dst)) {
        echo "_orphan klasörü yok.\n";

        return;
    }
    $n = 0;
    foreach (scandir($dst) as $f) {
        if (in_array($f, ['.', '..'], true)) {
            continue;
        }
        rename("$dst/$f", "$src/$f");
        $n++;
    }
    @rmdir($dst);
    echo "GERİ ALINDI: $n dosya products/ altına döndü.\n";

    return;
}

// Kullanımda olan görsel base'leri (uzantısız, sondaki -N atılmış)
$usedBase = Product::query()
    ->whereNotNull('image')->where('image', '!=', '')
    ->pluck('image')
    ->map(fn ($p) => preg_replace('/(-\d+)?\.[a-z0-9]+$/i', '', basename($p)))
    ->unique()
    ->flip();

$files = collect(scandir($src))
    ->reject(fn ($f) => in_array($f, ['.', '..'], true))
    ->reject(fn ($f) => is_dir("$src/$f"));

// Bir base kullanımdaysa o base'in TÜM varyantlarını (webp, -1, -2 …) koru
$orphans = $files->reject(function ($f) use ($usedBase) {
    $base = preg_replace('/(-\d+)?\.[a-z0-9]+$/i', '', $f);

    return $usedBase->has($base);
})->values();

echo 'products/ toplam dosya: ' . $files->count() . "\n";
echo 'sahipsiz (taşınacak): ' . $orphans->count() . "\n\n";

// Marka/hat grupları
$groups = $orphans->groupBy(function ($f) {
    return preg_match('/^([a-z]+)[-_]/i', $f, $m) ? Str::lower($m[1]) : 'diğer';
})->map->count()->sortDesc();
foreach ($groups as $g => $c) {
    printf("  %-24s %d\n", $g, $c);
}

if ($apply) {
    if (! is_dir($dst)) {
        mkdir($dst, 0775, true);
    }
    $n = 0;
    foreach ($orphans as $f) {
        rename("$src/$f", "$dst/$f");
        $n++;
    }
    // taşınanların listesini bırak
    file_put_contents("$dst/_TASINANLAR.txt", $orphans->implode("\n") . "\n");
    echo "\nTAŞINDI: $n dosya -> public/images/_orphan/\n";
    echo "Geri almak için: RESTORE=1 php artisan tinker --execute=\"require 'tools/move-orphan-images.php';\"\n";
} else {
    echo "\n(dry-run — taşımak için APPLY=1 ...)\n";
}
