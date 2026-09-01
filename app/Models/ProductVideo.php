<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductVideo extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (ProductVideo $video): void {
            $video->youtube_id = $video->youtube_id ?: static::extractYoutubeId($video->youtube_url);
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function extractYoutubeId(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim((string) $url);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }

        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', $url, $matches)) {
            return $matches[1];
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return isset($query['v']) && Str::length((string) $query['v']) === 11 ? (string) $query['v'] : null;
    }
}
