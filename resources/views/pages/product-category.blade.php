@extends('layouts.site')

@section('content')
@php
    $activeSpecFilters = $activeSpecFilters ?? [];

    $primaryGroup = [
        'title' => 'Markalar',
        'options' => collect([[
            'label' => 'Tüm markalar',
            'count' => $category['count'],
            'url' => route('products.category', $category['slug']),
            'active' => empty($brand),
        ]])->concat($brands->map(fn ($item) => [
            'label' => $item['name'],
            'count' => $item['count'],
            'url' => route('products.category', ['category' => $category['slug'], 'brand' => $item['slug']]),
            'active' => $brand === $item['slug'],
        ]))->all(),
    ];

    $clearUrl = route('products.category', $category['slug']);
    $activeCount = (int) ! empty($brand) + count($activeSpecFilters);
@endphp

<div class="catalog-ui" data-catalog>
    <div class="cui-crumbbar">
        <div class="cui-shell">
            <nav class="cui-crumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('products.index') }}">Ürünler</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                <span aria-current="page">{{ $category['name'] }}</span>
            </nav>
        </div>
    </div>

    <div class="cui-shell cui-page">
        <section class="cui-hero">
            <div class="cui-hero-logo">
                @if(! empty($category['image']))
                    <img src="{{ asset($category['image']) }}" alt="{{ $category['image_alt'] ?? $category['name'] . ' kategorisi görseli' }}">
                @else
                    <span>{{ $category['name'] }}</span>
                @endif
            </div>
            <div class="cui-hero-main">
                <h1>{{ $categorySeo['h1'] ?? $category['name'] . ' ürünleri' }}</h1>
                <div class="cui-hero-badges">
                    <span class="cui-trust">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        Teknik Katalog
                    </span>
                    <span class="cui-trust cui-trust--muted">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/></svg>
                        Kalibrasyon & Servis
                    </span>
                </div>
                <p class="cui-hero-lead">
                    {{ $categorySeo['hero_text'] ?? $category['summary'] ?? $category['name'] . ' kategorisindeki ürünler marka, model, SKU ve teknik özellik bilgileriyle katalog mantığında listelenir.' }}
                </p>
                <div class="cui-hero-actions">
                    <a class="cui-btn" href="{{ route('quote', ['source_type' => 'service', 'source_name' => $category['name'] . ' kalibrasyon hizmeti']) }}">
                        {{ $category['name'] }} Kalibrasyon Hizmeti Al
                    </a>
                    <a class="cui-btn cui-btn--ghost" href="{{ route('contact') }}">Detaylı Bilgi Al</a>
                </div>
            </div>
        </section>

        <div class="cui-2col">
            <aside class="cui-aside" aria-label="{{ $category['name'] }} filtreleri">
                @include('partials.catalog-filters', [
                    'primaryGroup' => $primaryGroup,
                    'specFilters' => $specFilters,
                    'clearUrl' => $clearUrl,
                    'hasActive' => $activeCount > 0,
                ])
            </aside>

            <div>
                @include('partials.catalog-results', [
                    'products' => $products,
                    'resultsLabel' => $category['name'] . ' kategorisinde',
                    'primaryGroup' => $primaryGroup,
                    'specFilters' => $specFilters,
                    'clearUrl' => $clearUrl,
                    'activeCount' => $activeCount,
                    'emptyTitle' => $categorySeo['empty_state']['title'] ?? $category['name'] . ' için teknik ürün talebi oluşturun',
                    'emptyText' => $categorySeo['empty_state']['text'] ?? 'Aradığınız marka veya modeli paylaşarak bu kategori için ürün bilgisi ve teklif talebi oluşturabilirsiniz.',
                    'emptyCtaUrl' => route('quote', ['source_type' => 'product', 'source_name' => $category['name']]),
                    'emptyCtaLabel' => $categorySeo['primary_cta'] ?? 'Bu Kategori İçin Teklif Al',
                ])
            </div>
        </div>
    </div>
</div>

@php
    $lowerChips = collect($categorySeo['support_links'] ?? [])
        ->reject(fn ($l) => \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($l['anchor'] ?? ''), ['teklif', 'iletişim', 'iletisim']))
        ->map(fn ($l) => ['label' => $l['anchor'], 'url' => $l['url']]);

    $lowerBrandCards = $brands->map(fn ($b) => [
        'name' => $b['name'],
        'slug' => $b['slug'],
        'logo' => $b['image'] ?? $b['logo'] ?? null,
        'url' => route('products.category', ['category' => $category['slug'], 'brand' => $b['slug']]),
    ]);
@endphp

@include('partials.catalog-lower', [
    'seo' => $categorySeo,
    'entityName' => $category['name'],
    'quoteUrl' => route('quote', ['source_type' => 'product', 'source_name' => $category['name']]),
    'chips' => $lowerChips,
    'brandCards' => $lowerBrandCards,
    'brandCardsTitle' => ($categorySeo['brand_section']['title'] ?? $category['name'] . ' İçin Öne Çıkan Markalar'),
])
@endsection
