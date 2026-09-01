@extends('layouts.site')

@section('content')
<section class="knowledge-hero">
    <div class="container knowledge-hero-grid">
        <div>
            <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Ana Sayfa</a><span>Blog</span></nav>
            <span class="eyebrow">Yayın akışı</span>
            <h1>{{ $blogSeo['h1'] }}</h1>
            <p>{{ $blogSeo['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#yazilar">Yazıları İncele</a>
                <a class="button button-secondary" href="{{ route('knowledge.index') }}">Bilgi Merkezi</a>
            </div>
        </div>
        <div class="visual-placeholder knowledge-hero-media">
            <span>Blog kapak görseli</span>
            <small>Gerçek teknik görsel veya editoryal kapak eklenecek</small>
        </div>
    </div>
</section>

<section class="section">
    <div class="container featured-article">
        @php($featured = collect($articles)->first())
        <div class="visual-placeholder featured-article-media">
            <span>Öne çıkan yazı görseli</span>
            <small>{{ $featured['title'] }}</small>
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
            <a class="button button-primary" href="{{ route('knowledge.show', $featured['slug']) }}">Öne çıkan yazıyı oku</a>
        </article>
    </div>
</section>

<section class="section section-muted category-seo-section">
    <div class="container category-seo-grid">
        @foreach($blogSeo['sections'] as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Kategoriler</span>
        <h2>Kategorilere göre blog içerikleri</h2>
    </div>
    <div class="container category-strip">
        <strong>Blog kategorileri</strong>
        <div class="filter-row">
            @foreach($categories as $category)
                <a href="{{ route('knowledge.category', $category['slug']) }}">{{ $category['name'] }} <span>{{ $category['count'] }}</span></a>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-muted" id="yazilar">
    <div class="container section-header">
        <span class="eyebrow">Öne çıkan yazılar</span>
        <h2>Laboratuvar cihazları blog içerikleri</h2>
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
            <span class="eyebrow">Bağlantılı sayfalar</span>
            <h2>Teknik ekibe danışın</h2>
            <p>{{ $blogSeo['cta']['text'] }}</p>
        </div>
        <aside class="side-panel">
            <div class="relation-list">
                @foreach($blogSeo['support_links'] as $link)
                    <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                @endforeach
            </div>
            <a class="button button-primary" href="{{ route('contact') }}">{{ $blogSeo['cta']['button'] }}</a>
        </aside>
    </div>
</section>
@endsection
