@extends('layouts.site')

@section('content')
<section class="product-taxonomy-hero">
    <div class="container taxonomy-hero-grid">
        <div>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <span>Markalar</span>
            </nav>
            <span class="eyebrow">Marka bazlı katalog</span>
            <h1>{{ $brandsSeo['h1'] }}</h1>
            <p>{{ $brandsSeo['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#marka-listesi">{{ $brandsSeo['primary_cta'] }}</a>
                <a class="button button-secondary" href="{{ route('products.index') }}">{{ $brandsSeo['secondary_cta'] }}</a>
            </div>
            <div class="taxonomy-stats">
                <div><strong>{{ $brands->count() }}</strong><span>marka</span></div>
                <div><strong>{{ $categories->count() }}</strong><span>kategori</span></div>
            </div>
        </div>
        <div class="visual-placeholder taxonomy-media">
            <span>Marka logoları alanı</span>
            <small>Gerçek logolar katalogdan gösterilir</small>
        </div>
    </div>
</section>

<section class="section section-muted category-seo-section">
    <div class="container category-seo-grid">
        @foreach(array_slice($brandsSeo['sections'], 0, 2) as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section" id="marka-listesi">
    <div class="container section-header centered">
        <span class="eyebrow">Markalar</span>
        <h2>MTA Endüstri ürün kataloğunda yer alan markalar</h2>
        <p>Marka kartlarından ilgili ürün listesine, kategori ilişkilerine ve teklif talebine geçiş yapılabilir.</p>
    </div>
    <div class="container card-grid three">
        @foreach($brands as $brand)
            @php($card = $brandsSeo['brand_cards'][$brand['slug']] ?? null)
            @include('partials.taxonomy-card', [
                'type' => 'brand',
                'href' => route('products.brand', $brand['slug']),
                'name' => $brand['name'],
                'title' => $card['anchor'] ?? $brand['name'] . ' ürünleri',
                'summary' => $card['summary'] ?? $brand['summary'],
                'image' => $brand['image'] ?? $brand['logo'] ?? null,
                'alt' => $card['alt'] ?? $brand['name'] . ' laboratuvar cihazları marka logosu',
            ])
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Kategori ilişkileri</span>
            <h2>{{ $brandsSeo['sections'][2]['title'] }}</h2>
            <p>{{ $brandsSeo['sections'][2]['text'] }}</p>
        </div>
        <div class="card-grid two">
            @foreach($categories as $category)
                @php($card = $brandsSeo['category_cards'][$category['slug']] ?? null)
                @include('partials.taxonomy-card', [
                    'type' => 'category',
                    'href' => route('products.category', $category['slug']),
                    'name' => $category['name'],
                    'title' => $card['title'] ?? $category['name'],
                    'summary' => $card['summary'] ?? $category['summary'],
                    'image' => $category['image'] ?? null,
                    'alt' => $category['image_alt'] ?? $category['name'] . ' kategorisi ürün görseli',
                ])
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container category-seo-grid">
        @foreach(array_slice($brandsSeo['sections'], 3) as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
                @if($loop->last)
                    <div class="section-link-row align-left">
                        @foreach($brandsSeo['support_links'] as $link)
                            <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                        @endforeach
                    </div>
                @endif
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Seçim kriterleri</span>
            <h2>Laboratuvar cihazı markası seçerken nelere dikkat edilmeli?</h2>
            <p>Marka adı tek başına yeterli değildir; teknik ihtiyaç, kullanım ortamı ve servis bağlantısı birlikte değerlendirilmelidir.</p>
        </div>
        <ul class="check-list two-column">
            @foreach($brandsSeo['selection_items'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">SSS</span>
        <h2>Sık sorulan sorular</h2>
    </div>
    <div class="container faq-accordion">
        @foreach($brandsSeo['faq'] as $faq)
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
            <span class="eyebrow">Marka bazlı teklif</span>
            <h2>{{ $brandsSeo['cta']['title'] }}</h2>
            <p>{{ $brandsSeo['cta']['text'] }}</p>
        </div>
        <div class="hero-actions">
            <a class="button button-light" href="{{ route('quote', ['source_type' => 'product', 'source_name' => 'Marka bazlı ürün teklifi']) }}">{{ $brandsSeo['cta']['button'] }}</a>
            <a class="button button-outline-light" href="{{ route('products.index') }}">{{ $brandsSeo['cta']['secondary_button'] }}</a>
        </div>
    </div>
</section>
@endsection
