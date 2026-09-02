<?php

/**
 * public/images altındaki .jpg/.jpeg/.png dosyaları için yanına .webp üretir.
 * Orijinaller fallback olarak korunur. Tekrar çalıştırılabilir (webp güncelse atlar).
 *
 * Kullanım:  php tools/optimize-images.php [--quality=80] [--max=2000] [--force]
 */

$root = dirname(__DIR__) . '/public/images';
$quality = 80;
$maxDim = 2000;
$force = false;

foreach ($argv as $arg) {
    if (preg_match('/^--quality=(\d+)$/', $arg, $m)) {
        $quality = (int) $m[1];
    } elseif (preg_match('/^--max=(\d+)$/', $arg, $m)) {
        $maxDim = (int) $m[1];
    } elseif ($arg === '--force') {
        $force = true;
    }
}

if (! is_dir($root)) {
    fwrite(STDERR, "Klasör yok: $root\n");
    exit(1);
}
if (! function_exists('imagewebp')) {
    fwrite(STDERR, "GD WebP desteği yok.\n");
    exit(1);
}

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$made = 0;
$skipped = 0;
$savedBytes = 0;
$origBytes = 0;

foreach ($it as $file) {
    /** @var SplFileInfo $file */
    $ext = strtolower($file->getExtension());
    if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        continue;
    }

    $src = $file->getPathname();
    $dst = preg_replace('/\.(jpe?g|png)$/i', '.webp', $src);

    if (! $force && is_file($dst) && filemtime($dst) >= filemtime($src)) {
        $skipped++;
        continue;
    }

    $img = match ($ext) {
        'png' => @imagecreatefrompng($src),
        default => @imagecreatefromjpeg($src),
    };
    if (! $img) {
        fwrite(STDERR, "Okunamadı: $src\n");
        continue;
    }

    if ($ext === 'png') {
        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);
    }

    $w = imagesx($img);
    $h = imagesy($img);
    if ($w > $maxDim || $h > $maxDim) {
        $scale = $maxDim / max($w, $h);
        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);
        $resized = imagecreatetruecolor($nw, $nh);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);
        $img = $resized;
    }

    imagewebp($img, $dst, $quality);
    imagedestroy($img);

    $o = filesize($src);
    $n = filesize($dst);
    $origBytes += $o;
    $savedBytes += max(0, $o - $n);
    $made++;
    printf("  %-60s %6.1fKB -> %6.1fKB\n", substr($file->getFilename(), 0, 60), $o / 1024, $n / 1024);
}

printf(
    "\nÜretilen: %d  ·  Atlanan: %d  ·  Toplam kazanç: %.1f MB (%.0f%%)\n",
    $made,
    $skipped,
    $savedBytes / 1048576,
    $origBytes > 0 ? ($savedBytes / $origBytes) * 100 : 0,
);
