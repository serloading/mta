<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SiteSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SiteSetting $setting): void {
            $setting->created_by ??= Auth::id();
            $setting->updated_by ??= Auth::id();
        });

        static::updating(function (SiteSetting $setting): void {
            $setting->updated_by = Auth::id();
        });
    }
}
