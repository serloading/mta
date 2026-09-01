@extends('layouts.site')

@section('content')
<section class="product-taxonomy-hero">
    <div class="container taxonomy-hero-grid">
        <div>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <span>Ürünler</span>
            </nav>
            <span class="eyebrow">Teknik ürün kataloğu</span>
            <h1>{{ $productsSeo['h1'] }}</h1>
            <p>{{ $productsSeo['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#kategoriler">Kategorileri İncele</a>
                <a class="button button-secondary" href="#markalar">Markaları İncele</a>
            </div>
        </div>
        <div class="visual-placeholder taxonomy-media">
            <span>Ürün kataloğu görseli</span>
            <small>Laboratuvar cihazları ve ölçüm ekipmanları</small>
        </div>
    </div>
</section>

<section class="section" id="kategoriler">
    <div class="container section-header centered">
        <span class="eyebrow">Kategoriler</span>
        <h2>Laboratuvar cihazı kategorileri</h2>
        <p>Ürün grupları marka, kategori ve teknik özellik bilgileriyle teklif odaklı katalog yapısında listelenir.</p>
    </div>
    <div class="container card-grid three">
        @foreach($categories as $item)
            @php($cardSeo = $productsSeo['category_cards'][$item['slug']] ?? null)
            @include('partials.taxonomy-card', [
                'type' => 'category',
                'href' => route('products.category', $item['slug']),
                'name' => $item['name'],
                'title' => $cardSeo['title'] ?? $item['name'],
                'summary' => $cardSeo['summary'] ?? $item['summary'],
                'image' => $item['image'] ?? null,
                'alt' => $item['image_alt'] ?? $item['name'] . ' kategorisi ürün görseli',
            ])
        @endforeach
    </div>
</section>

<section class="section section-muted category-brand-section" id="markalar">
    <div class="container section-header centered">
        <span class="eyebrow">Markalar</span>
        <h2>{{ $productsSeo['brand_section']['title'] }}</h2>
        <p>{{ $productsSeo['brand_section']['text'] }}</p>
    </div>
    <div class="container card-grid three">
        @foreach($brands as $brandItem)
            @include('partials.taxonomy-card', [
                'type' => 'brand',
                'href' => route('products.brand', $brandItem['slug']),
                'name' => $brandItem['name'],
                'title' => $brandItem['name'] . ' ürünleri',
                'summary' => $brandItem['summary'] ?? $brandItem['name'] . ' markasına ait teknik ürünler.',
                'image' => $brandItem['image'] ?? $brandItem['logo'] ?? null,
                'alt' => $brandItem['name'] . ' laboratuvar cihazları marka görseli',
            ])
        @endforeach
    </div>
</section>

<section class="section section-muted category-seo-section">
    <div class="container category-seo-grid">
        @foreach(array_slice($productsSeo['sections'], 2) as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
                @if($loop->last)
                    <div class="section-link-row align-left">
                        @foreach($productsSeo['support_section']['links'] as $link)
                            <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                        @endforeach
                    </div>
                @endif
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Seçim kriterleri</span>
            <h2>{{ $productsSeo['selection_section']['title'] }}</h2>
            <p>{{ $productsSeo['selection_section']['text'] }}</p>
        </div>
        <div>
            <ul class="check-list two-column">
                @foreach($productsSeo['selection_section']['items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Bağlantılı süreçler</span>
            <h2>{{ $productsSeo['support_section']['title'] }}</h2>
            <p>{{ $productsSeo['support_section']['text'] }}</p>
        </div>
        <aside class="side-panel">
            <span class="eyebrow">İç linkler</span>
            <h2>Hizmet ve destek sayfaları</h2>
            <div class="relation-list">
                @foreach($productsSeo['support_section']['links'] as $link)
                    <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                @endforeach
            </div>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">SSS</span>
        <h2>Sık sorulan sorular</h2>
    </div>
    <div class="container faq-accordion">
        @foreach($productsSeo['faq'] as $faq)
            <details>
                <summary>{{ $faq['question'] }}</summary>
                <p>{{ $faq['answer'] }}</p>
            </details>
        @endforeach
    </div>
</section>

<section class="cta-band">
    <div class="container cta-shell">
        <div>
            <span class="eyebrow">Teklif ve teknik bilgi</span>
            <h2>{{ $productsSeo['cta']['title'] }}</h2>
            <p>{{ $productsSeo['cta']['text'] }}</p>
            <p>{{ $productsSeo['cta']['note'] }}</p>
            <div class="section-link-row align-left">
                <a href="{{ route('quote', ['source_type' => 'product', 'source_name' => 'Ürün kataloğu']) }}">{{ $productsSeo['cta']['anchor'] }}</a>
            </div>
        </div>
        <a class="button button-light" href="{{ route('quote', ['source_type' => 'product', 'source_name' => 'Ürün kataloğu']) }}">{{ $productsSeo['cta']['button'] }}</a>
    </div>
</section>
@endsection
