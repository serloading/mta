<?php

namespace App\Support;

class Img
{
    /** İstek içi hafıza: aynı yol için tekrar tekrar is_file() çağrılmaz. */
    private static array $existsCache = [];

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

        $path = ltrim($path, '/');

        // Admin yüklemeleri "media/..." -> public disk (storage/ ile servis edilir)
        if (str_starts_with($path, 'media/')) {
            $path = 'storage/' . $path;
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
        return self::$existsCache[$relative] ??= is_file(public_path($relative));
    }
}
