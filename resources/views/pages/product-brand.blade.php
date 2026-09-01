@extends('layouts.site')

@section('content')
@php
    $activeSpecFilters = $activeSpecFilters ?? [];

    $primaryGroup = [
        'title' => 'Kategoriler',
        'options' => collect([[
            'label' => 'Tüm kategoriler',
            'count' => $brand['count'],
            'url' => route('products.brand', $brand['slug']),
            'active' => empty($category),
        ]])->concat($categories->map(fn ($item) => [
            'label' => $item['name'],
            'count' => $item['count'],
            'url' => route('products.brand', ['brand' => $brand['slug'], 'category' => $item['slug']]),
            'active' => $category === $item['slug'],
        ]))->all(),
    ];

    $clearUrl = route('products.brand', $brand['slug']);
    $activeCount = (int) ! empty($category) + count($activeSpecFilters);
@endphp

<div class="catalog-ui" data-catalog>
    <div class="cui-crumbbar">
        <div class="cui-shell">
            <nav class="cui-crumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('brands.index') }}">Markalar</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                <span aria-current="page">{{ $brand['name'] }}</span>
            </nav>
        </div>
    </div>

    <div class="cui-shell cui-page">
        <section class="cui-hero">
            <div class="cui-hero-logo">
                @if(! empty($brand['logo']))
                    <img src="{{ asset($brand['logo']) }}" alt="{{ $brandSeo['logo_alt'] ?? $brand['name'] . ' logosu' }}">
                @else
                    <span>{{ $brand['name'] }}</span>
                @endif
            </div>
            <div class="cui-hero-main">
                <h1>{{ $brandSeo['h1'] ?? $brand['name'] . ' ürünleri' }}</h1>
                <div class="cui-hero-badges">
                    <span class="cui-trust">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        Kurumsal Tedarik
                    </span>
                    <span class="cui-trust cui-trust--muted">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/></svg>
                        Teknik Servis Desteği
                    </span>
                </div>
                <p class="cui-hero-lead">
                    {{ $brandSeo['hero_text'] ?? $brand['summary'] ?? $brand['name'] . ' markasına ait ürünler kategori, model, SKU ve teknik özellik bilgileriyle teklif odaklı katalog yapısında listelenir.' }}
                </p>
                <div class="cui-hero-actions">
                    <a class="cui-btn" href="{{ route('quote', ['source_type' => 'service', 'source_name' => $brand['name'] . ' kalibrasyon hizmeti']) }}">
                        {{ $brand['name'] }} Kalibrasyon Hizmeti Al
                    </a>
                    <a class="cui-btn cui-btn--ghost" href="{{ route('contact') }}">Detaylı Bilgi Al</a>
                </div>
            </div>
        </section>

        <div class="cui-2col">
            <aside class="cui-aside" aria-label="{{ $brand['name'] }} filtreleri">
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
                    'resultsLabel' => $brand['name'] . ' markasına ait',
                    'primaryGroup' => $primaryGroup,
                    'specFilters' => $specFilters,
                    'clearUrl' => $clearUrl,
                    'activeCount' => $activeCount,
                    'emptyTitle' => $brandSeo['empty_state']['title'] ?? $brand['name'] . ' ürünleri için teknik talep oluşturun',
                    'emptyText' => $brandSeo['empty_state']['text'] ?? 'Aradığınız marka, ürün grubu veya modeli paylaşarak ürün bilgisi ve teklif talebi oluşturabilirsiniz.',
                    'emptyCtaUrl' => route('quote', ['source_type' => 'product', 'source_name' => $brand['name']]),
                    'emptyCtaLabel' => $brandSeo['primary_cta'] ?? 'Bu Marka İçin Teklif Al',
                ])
            </div>
        </div>
    </div>
</div>

@php
    $lowerChips = $categories->map(fn ($c) => [
        'label' => $c['name'],
        'url' => route('products.brand', ['brand' => $brand['slug'], 'category' => $c['slug']]),
    ]);
@endphp

@include('partials.catalog-lower', [
    'seo' => $brandSeo ?: [],
    'entityName' => $brand['name'],
    'quoteUrl' => route('quote', ['source_type' => 'product', 'source_name' => $brand['name']]),
    'chips' => $lowerChips,
    'brandCards' => null,
])
@endsection
