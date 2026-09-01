@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="container narrow">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Ana Sayfa</a>
            <a href="{{ route('knowledge.index') }}">Bilgi Merkezi</a>
            <span>{{ $category['name'] }}</span>
        </nav>
        <span class="eyebrow">Bilgi merkezi kategorisi</span>
        <h1>{{ $categorySeo['h1'] ?? $category['name'] }}</h1>
        <p>{{ $categorySeo['hero_text'] ?? 'Bu kategorideki içerikler, kullanıcı sorusuna kısa cevapla başlayıp teknik detay, ilgili hizmet ve ürün bağlantılarıyla devam edecek şekilde yapılandırılır.' }}</p>
    </div>
</section>

@if(! empty($categorySeo))
    <section class="section section-muted category-seo-section">
        <div class="container category-seo-grid">
            @foreach(array_slice($categorySeo['sections'], 0, 2) as $section)
                <article class="category-seo-copy">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="container split-section">
            <div>
                <span class="eyebrow">İçerik kapsamı</span>
                <h2>{{ $categorySeo['scope_title'] ?? 'Bu kategoride hangi içerikler yer alır?' }}</h2>
                <p>{{ $categorySeo['scope_text'] ?? 'Bu kategorideki rehberler kullanıcı sorusu, teknik açıklama ve ilgili hizmet/ürün bağlantıları etrafında yapılandırılır.' }}</p>
            </div>
            <ul class="check-list two-column">
                @foreach($categorySeo['content_items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

<section class="section">
    <div class="container category-layout">
        <aside class="category-side">
            <h2>Kategoriler</h2>
            @foreach($categories as $item)
                <a @class(['active' => $item['slug'] === $category['slug']]) href="{{ route('knowledge.category', $item['slug']) }}">
                    {{ $item['name'] }}
                    <span>{{ $item['count'] }}</span>
                </a>
            @endforeach
            @if(! empty($categorySeo['service_links']))
                <h2>İlgili hizmetler</h2>
                @foreach($categorySeo['service_links'] as $link)
                    <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                @endforeach
            @endif
        </aside>
        <div class="article-grid single-column">
            @foreach($articles as $article)
                @include('partials.article-card', ['article' => $article])
            @endforeach
        </div>
    </div>
</section>

@if(! empty($categorySeo))
    <section class="section section-muted category-seo-section">
        <div class="container category-seo-grid">
            @foreach(array_slice($categorySeo['sections'], 2) as $section)
                <article class="category-seo-copy">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['text'] }}</p>
                    <div class="section-link-row align-left">
                        @foreach(($loop->first ? $categorySeo['service_links'] : $categorySeo['support_links']) as $link)
                            <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="cta-band">
        <div class="container cta-shell">
            <div>
                <span class="eyebrow">{{ $categorySeo['cta']['eyebrow'] ?? 'Teknik destek' }}</span>
                <h2>{{ $categorySeo['cta']['title'] }}</h2>
                <p>{{ $categorySeo['cta']['text'] }}</p>
            </div>
            <a class="button button-light" href="{{ route('contact') }}">{{ $categorySeo['cta']['button'] }}</a>
        </div>
    </section>
@endif
@endsection
