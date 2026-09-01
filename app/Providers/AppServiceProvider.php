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
        });
    }
}
