<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class TechnicalService extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'devices' => 'array',
            'service_steps' => 'array',
            'advantages' => 'array',
            'faq' => 'array',
            'schema_blocks' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoEntry::class, 'seoable');
    }
}
