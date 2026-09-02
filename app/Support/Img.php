<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class Img
{
    /**
     * Verilen görsel yolu için (public/ göreli) WebP sürümü diskte varsa onun
     * asset URL'ini, yoksa orijinalin asset URL'ini döndürür.
     *
     * WebP tarayıcı desteği ~%97; orijinal dosyalar diskte fallback olarak durur.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        // Zaten webp/avif/svg ise dokunma
        if (preg_match('/\.(webp|avif|svg)$/i', $path)) {
            return asset($path);
        }

        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);

        if ($webp !== $path && self::exists($webp)) {
            return asset($webp);
        }

        return asset($path);
    }

    private static function exists(string $relative): bool
    {
        $key = 'img.webp.' . md5($relative);

        return Cache::remember($key, now()->addHour(), fn () => is_file(public_path($relative)));
    }
}
