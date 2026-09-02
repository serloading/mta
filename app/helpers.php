<?php

use App\Support\Img;

if (! function_exists('img_url')) {
    /**
     * Görsel için WebP sürümü diskte varsa onun URL'ini, yoksa orijinali döndürür.
     * Blade: <img src="{{ img_url($product['image']) }}">
     */
    function img_url(?string $path): ?string
    {
        return Img::url($path);
    }
}
