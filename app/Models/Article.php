<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'schema_blocks' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            $article->created_by ??= Auth::id();
            $article->updated_by ??= Auth::id();
        });

        static::updating(function (Article $article): void {
            $article->updated_by = Auth::id();
        });

        static::saving(function (Article $article): void {
            // Okuma süresi boşsa içerik uzunluğundan hesapla (~180 kelime/dk).
            if (blank($article->reading_time) && filled($article->body)) {
                $words = str_word_count(strip_tags((string) $article->body));
                $article->reading_time = max(1, (int) ceil($words / 180)) . ' dk';
            }
        });
    }

    /** Yayında ve yayın tarihi gelmiş yazılar. */
    public function scopeLive($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoEntry::class, 'seoable');
    }
}
