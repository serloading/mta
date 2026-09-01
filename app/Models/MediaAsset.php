<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MediaAsset extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MediaAsset $asset): void {
            $asset->created_by ??= Auth::id();
            $asset->updated_by ??= Auth::id();
        });

        static::saving(function (MediaAsset $asset): void {
            $asset->file_name = $asset->file_name ?: basename((string) $asset->path);
            $asset->updated_by = Auth::id();
        });
    }
}
