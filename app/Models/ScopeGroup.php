<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScopeGroup extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'rows' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ScopeCategory::class, 'scope_category_id');
    }
}
