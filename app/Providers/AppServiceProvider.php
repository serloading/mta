<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mega menü "Ürünler" — her kategori için temsili ürün (görsel + ad).
        // Kategori slug'ı => ['image' => ..., 'name' => ...]
        View::composer('layouts.site', function ($view) {
            $features = [];

            try {
                if (Schema::hasTable('products') && Schema::hasTable('product_categories')) {
                    $rows = DB::table('products')
                        ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
                        ->whereNotNull('products.image')
                        ->where('products.image', '!=', '')
                        ->orderByDesc('products.is_featured')
                        ->orderBy('products.sort_order')
                        ->get(['product_categories.slug', 'products.name', 'products.image']);

                    foreach ($rows as $row) {
                        if (isset($features[$row->slug])) {
                            continue;
                        }
                        $image = str_starts_with($row->image, 'media/') ? 'storage/' . $row->image : $row->image;
                        $features[$row->slug] = ['image' => $image, 'name' => $row->name];
                    }
                }
            } catch (\Throwable $e) {
                $features = [];
            }

            $view->with('megaFeatureProducts', $features);

            // Top bar — yetkili / merkez servis rozetleri (config/mta.php).
            $badges = [];
            foreach ((array) config('mta.authorized_services', []) as $entry) {
                if (empty($entry['show_in_topbar'])) {
                    continue;
                }
                $target = $entry['primary_target'] ?? null;
                $url = url('/');
                if (is_array($target)) {
                    try {
                        $url = match ($target['type'] ?? '') {
                            'service' => route('services.show', $target['slug']),
                            'technical_service' => route('technical-services.show', $target['slug']),
                            'product' => route('products.show', $target['slug']),
                            default => url('/'),
                        };
                    } catch (\Throwable $e) {
                        $url = url('/');
                    }
                }
                $logo = ! empty($entry['logo']) && is_file(public_path($entry['logo'])) ? $entry['logo'] : null;
                $badges[] = [
                    'short' => $entry['short'] ?? (($entry['brand'] ?? '') . ' Yetkili Servis'),
                    'role' => $entry['role'] ?? 'Yetkili Servis',
                    'brand' => $entry['brand'] ?? '',
                    'header_note' => $entry['header_note'] ?? null,
                    'logo' => $logo,
                    'url' => $url,
                ];
            }

            $view->with('topbarAuthorizedServices', $badges);
        });
    }
}
