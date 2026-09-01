<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SchemaDefinition extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'applicable_to' => 'array',
            'default_payload' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SchemaDefinition $definition): void {
            $definition->created_by ??= Auth::id();
            $definition->updated_by ??= Auth::id();
        });

        static::updating(function (SchemaDefinition $definition): void {
            $definition->updated_by = Auth::id();
        });
    }

    public static function optionsFor(?string $area = null): array
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(function (SchemaDefinition $definition) use ($area): bool {
                if ($area === null) {
                    return true;
                }

                $applicableTo = $definition->applicable_to ?? [];

                return $applicableTo === []
                    || in_array($area, $applicableTo, true)
                    || in_array('global', $applicableTo, true);
            })
            ->mapWithKeys(fn (SchemaDefinition $definition) => [
                $definition->key => $definition->name . ' (' . $definition->schema_type . ')',
            ])
            ->all();
    }
}
