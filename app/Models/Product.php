<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'gallery' => 'array',
            'metadata' => 'array',
            'specs' => 'array',
            'filter_keys' => 'array',
            'schema_blocks' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $product->created_by ??= Auth::id();
            $product->updated_by ??= Auth::id();
        });

        static::updating(function (Product $product): void {
            $product->updated_by = Auth::id();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProductDocument::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ProductVideo::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'product_service')->withTimestamps();
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoEntry::class, 'seoable');
    }
}
