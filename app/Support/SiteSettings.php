<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    private static array $settings = [];

    public static function get(string $key, array $fallback = []): array
    {
        if (app()->runningUnitTests()) {
            return self::read($key) ?: $fallback;
        }

        if (! array_key_exists($key, self::$settings)) {
            self::$settings[$key] = self::read($key);
        }

        return self::$settings[$key] ?: $fallback;
    }

    public static function socialLinks(): array
    {
        $settings = self::get('social_links', ['links' => config('mta.site.social_links', [])]);

        return collect($settings['links'] ?? [])
            ->filter(fn ($link) => filled($link['url'] ?? null))
            ->values()
            ->all();
    }

    public static function tracking(string $key): string
    {
        $settings = self::get('tracking_codes');

        return (string) ($settings[$key] ?? '');
    }

    private static function read(string $key): array
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return [];
            }

            $setting = SiteSetting::query()->where('key', $key)->first();

            return is_array($setting?->value) ? $setting->value : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
