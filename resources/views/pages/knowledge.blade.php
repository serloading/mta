@extends('layouts.site')

@section('content')
<section class="knowledge-hero">
    <div class="container knowledge-hero-grid">
        <div>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Ana Sayfa</a><span>Bilgi Merkezi</span></nav>
            <span class="eyebrow">Teknik kütüphane</span>
            <h1>{{ $knowledgeSeo['h1'] }}</h1>
            <p>{{ $knowledgeSeo['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#one-cikanlar">Öne çıkan içerikler</a>
                <a class="button button-secondary" href="{{ route('contact') }}">{{ $knowledgeSeo['cta']['button'] }}</a>
            </div>
        </div>
        <div class="visual-placeholder knowledge-hero-media">
            <span>Bilgi merkezi görseli</span>
            <small>Kalibrasyon, cihaz seçimi ve teknik servis rehberleri</small>
        </div>
    </div>
</section>

<section class="section section-muted">
    <div class="container category-strip">
        <strong>Kategoriler</strong>
        <div class="filter-row">
            @foreach($categories as $category)
                <a href="{{ route('knowledge.category', $category['slug']) }}">{{ $category['name'] }} <span>{{ $category['count'] }}</span></a>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container category-seo-grid">
        @foreach($knowledgeSeo['sections'] as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container section-header centered" id="one-cikanlar">
        <span class="eyebrow">Öne çıkanlar</span>
        <h2>Öne çıkan içerikler</h2>
    </div>
    <div class="container featured-article">
        @php($featured = collect($articles)->first())
        <div class="visual-placeholder featured-article-media">
            <span>Öne çıkan makale görseli</span>
            <small>Yayın öncesi değiştirilecek</small>
        </div>
        <article>
            <a class="card-kicker" href="{{ route('knowledge.category', $featured['category_slug']) }}">{{ $featured['category'] }}</a>
            <h2>{{ $featured['title'] }}</h2>
            <p>{{ $featured['excerpt'] }}</p>
            <div class="article-meta">
                <span>{{ $featured['author'] }}</span>
                <span>{{ $featured['reading_time'] }}</span>
                <span>Güncelleme: {{ $featured['updated_at'] }}</span>
            </div>
            <a class="button button-primary" href="{{ route('knowledge.show', $featured['slug']) }}">Öne çıkan içeriği oku</a>
        </article>
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header">
        <span class="eyebrow">Teknik raf</span>
        <h2>Kalibrasyon, ürün seçimi ve bakım rehberleri</h2>
    </div>
    <div class="container article-grid">
        @foreach($articles as $article)
            @include('partials.article-card', ['article' => $article])
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container split-section">
        <div>
            <span class="eyebrow">İç linkler</span>
            <h2>Hizmet ve katalog bağlantıları</h2>
            <p>Rehber içerikleri ürün, kalibrasyon ve teknik servis sayfalarıyla birlikte değerlendirebilirsiniz.</p>
        </div>
        <aside class="side-panel">
            <div class="relation-list">
                @foreach($knowledgeSeo['support_links'] as $link)
                    <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                @endforeach
            </div>
        </aside>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-shell">
        <div>
            <span class="eyebrow">Teknik destek</span>
            <h2>{{ $knowledgeSeo['cta']['title'] }}</h2>
            <p>{{ $knowledgeSeo['cta']['text'] }}</p>
        </div>
        <a class="button button-light" href="{{ route('contact') }}">{{ $knowledgeSeo['cta']['button'] }}</a>
    </div>
</section>
@endsection
