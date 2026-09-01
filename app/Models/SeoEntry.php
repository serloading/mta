<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoEntry extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'schema_payload' => 'array',
            'schema_blocks' => 'array',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
