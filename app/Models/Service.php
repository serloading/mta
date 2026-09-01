<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Service extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scope' => 'array',
            'scope_groups' => 'array',
            'devices' => 'array',
            'applications' => 'array',
            'standards' => 'array',
            'capacity' => 'array',
            'process_steps' => 'array',
            'faq' => 'array',
            'schema_blocks' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_service')->withTimestamps();
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoEntry::class, 'seoable');
    }
}
