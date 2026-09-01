<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Lead;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\Product;
use App\Models\SeoEntry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ContentOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $counts = Cache::remember('admin.dashboard.overview', now()->addMinutes(5), function (): array {
            return [
                'articles' => Article::query()->count(),
                'articles_published' => Article::query()->where('status', 'published')->count(),
                'products' => Product::query()->count(),
                'products_published' => Product::query()->where('status', 'published')->count(),
                'seo_entries' => SeoEntry::query()->count(),
                'pages' => Page::query()->count(),
                'media' => MediaAsset::query()->count(),
                'leads' => Lead::query()->count(),
                'leads_new' => Lead::query()->where('status', 'new')->count(),
            ];
        });

        return [
            Stat::make('Blog yazıları', $counts['articles'])
                ->description($counts['articles_published'] . ' yayında'),
            Stat::make('Ürünler', $counts['products'])
                ->description($counts['products_published'] . ' aktif'),
            Stat::make('Sayfa SEO kayıtları', $counts['seo_entries'])
                ->description($counts['pages'] . ' içerik sayfası'),
            Stat::make('Medya', $counts['media'])
                ->description('Yüklenen görseller'),
            Stat::make('Teklif talepleri', $counts['leads'])
                ->description($counts['leads_new'] . ' yeni talep'),
        ];
    }
}
