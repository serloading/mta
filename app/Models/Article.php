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
