<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Product;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LatestContent extends Widget
{
    protected string $view = 'filament.widgets.latest-content';

    protected int|string|array $columnSpan = 'full';

    public function latestItems(): Collection
    {
        return Cache::remember('admin.dashboard.latest-content', now()->addMinutes(2), function (): Collection {
            return collect()
                ->merge(Article::query()
                    ->latest('updated_at')
                    ->limit(5)
                    ->get(['title', 'status', 'updated_at'])
                    ->map(fn (Article $article) => [
                        'type' => 'Blog',
                        'title' => $article->title,
                        'status' => $article->status,
                        'updated_at' => $article->updated_at,
                    ]))
                ->merge(Product::query()
                    ->latest('updated_at')
                    ->limit(5)
                    ->get(['name', 'status', 'updated_at'])
                    ->map(fn (Product $product) => [
                        'type' => 'Ürün',
                        'title' => $product->name,
                        'status' => $product->status,
                        'updated_at' => $product->updated_at,
                    ]))
                ->sortByDesc('updated_at')
                ->take(8)
                ->values();
        });
    }
}
