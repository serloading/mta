<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Page extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'schema_blocks' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Page $page): void {
            $page->created_by ??= Auth::id();
            $page->updated_by ??= Auth::id();
        });

        static::updating(function (Page $page): void {
            $page->updated_by = Auth::id();
        });
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoEntry::class, 'seoable');
    }
}
