<?php

/**
 * Görselsiz ürünleri, public/images/products/ altındaki dosya adlarıyla eşleştirir.
 *
 *   php artisan tinker --execute="require 'tools/match-product-images.php';"          # dry-run
 *   APPLY=1 php artisan tinker --execute="require 'tools/match-product-images.php';"  # yaz
 *
 * Eşleşme stratejisi (yüksekten düşüğe güven):
 *  1) {slug}[-1].{ext}
 *  2) {marka öneki atılmış slug}[-1].{ext}
 *  3) model kodu dosya adında geçiyor + kategori token'ı örtüşüyor
 *  4) token Jaccard >= 0.6
 */

use App\Models\Product;
use Illuminate\Support\Str;

$dir = public_path('images/products');
$apply = (bool) (getenv('APPLY') ?: ($_SERVER['APPLY'] ?? false));

$files = collect(scandir($dir))
    ->reject(fn ($f) => in_array($f, ['.', '..'], true))
    ->reject(fn ($f) => Str::endsWith($f, ['.webp', '.avif']))
    ->values();

// base (uzantısız, sondaki -1/-2 atılmış) => gerçek dosya adı
$byBase = [];
foreach ($files as $f) {
    $base = preg_replace('/\.[a-z0-9]+$/i', '', $f);
    $baseNoIdx = preg_replace('/-\d+$/', '', $base);
    $byBase[$base] = $f;
    $byBase[$baseNoIdx] = $byBase[$baseNoIdx] ?? $f;
}

$norm = function (string $s): array {
    $s = Str::lower(Str::ascii($s));
    $s = preg_replace('/[^a-z0-9]+/', ' ', $s);
    $stop = ['ve', 'ile', 'the', 'cihazi', 'cihaz', 'model', 'tip', 'serisi', 'seri', 'g', 'mg'];

    return collect(preg_split('/\s+/', trim($s)))
        ->filter(fn ($t) => strlen($t) > 1 && ! in_array($t, $stop, true))
        ->unique()
        ->values()
        ->all();
};

$fileTokens = [];
foreach ($byBase as $base => $f) {
    $fileTokens[$f] = $fileTokens[$f] ?? $norm(str_replace('-', ' ', $base));
}

$targets = Product::query()
    ->where(fn ($q) => $q->whereNull('image')->orWhere('image', ''))
    ->orderBy('status', 'desc')
    ->get(['id', 'name', 'slug', 'model', 'sku', 'status']);

$hit = 0;
$miss = 0;
$rows = [];

foreach ($targets as $p) {
    $cands = [];

    // 1 & 2: slug varyasyonları
    $slugVariants = [$p->slug, preg_replace('/^[a-z0-9]+-/', '', $p->slug)];
    foreach ($slugVariants as $sv) {
        foreach (["$sv-1", $sv, "$sv-2"] as $key) {
            if (isset($byBase[$key])) {
                $cands[] = [$byBase[$key], 100, 'slug'];
                break 2;
            }
        }
    }

    // 3 & 4: token benzerliği
    if (! $cands) {
        $pt = $norm($p->name . ' ' . $p->model);
        $modelKey = Str::lower(Str::ascii((string) $p->model));
        $modelKey = preg_replace('/[^a-z0-9]+/', '', $modelKey);

        $best = null;
        foreach ($fileTokens as $file => $ft) {
            if (! $ft) {
                continue;
            }
            $inter = count(array_intersect($pt, $ft));
            $union = count(array_unique(array_merge($pt, $ft)));
            $jac = $union ? $inter / $union : 0;

            $fileKey = preg_replace('/[^a-z0-9]+/', '', Str::lower(Str::ascii(str_replace('-', '', $file))));
            $modelInFile = ($modelKey && strlen($modelKey) >= 3 && str_contains($fileKey, $modelKey));

            $score = $jac * 100 + ($modelInFile ? 30 : 0);
            if ($score > ($best[1] ?? 0)) {
                $best = [$file, $score, $modelInFile ? 'model+jac' : 'jac'];
            }
        }
        if ($best && $best[1] >= 55) {
            $cands[] = $best;
        }
    }

    if ($cands) {
        [$file, $score, $how] = $cands[0];
        $rows[] = [$p, $file, round($score), $how];
        $hit++;
    } else {
        $miss++;
    }
}

echo "Görselsiz ürün: " . $targets->count() . "  ·  eşleşen: $hit  ·  eşleşmeyen: $miss\n";
echo str_repeat('-', 100) . "\n";
foreach ($rows as [$p, $file, $score, $how]) {
    printf("[%3d %-9s] %-52s  <-  %-44s  (%s)\n", $score, $how, Str::limit($p->name, 50), $file, $p->status);
}

// Bir dosya kaç ürüne önerildi? (>2 ise jenerik/paylaşımlı say, atlama için)
$fileCount = [];
foreach ($rows as [$p, $file]) {
    $fileCount[$file] = ($fileCount[$file] ?? 0) + 1;
}

if ($apply) {
    $n = 0;
    $skippedShared = 0;
    foreach ($rows as [$p, $file, $score, $how]) {
        $safe = ($how === 'slug')
            || (str_contains($how, 'model') && $score >= 78)
            || ($score >= 88);
        if (! $safe) {
            continue;
        }
        if (($fileCount[$file] ?? 0) > 2) {
            $skippedShared++;
            continue; // aynı dosya 3+ ürüne öneriliyor -> jenerik, elle bak
        }
        $p->image = 'images/products/' . $file;
        $p->save();
        $n++;
        printf("  ✓ %-52s <- %s\n", Str::limit($p->name, 50), $file);
    }
    echo "\nUYGULANDI: $n ürüne görsel atandı.  (paylaşımlı dosya nedeniyle atlanan: $skippedShared)\n";
} else {
    echo "\n(dry-run — yüksek güvenli atama için: APPLY=1 ... )\n";
}
