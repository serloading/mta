@extends('layouts.site')

@section('content')
<section class="article-hero">
    <div class="container article-hero-grid">
        <div>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <a href="{{ route('knowledge.index') }}">Bilgi Merkezi</a>
                <a href="{{ route('knowledge.category', $article['category_slug']) }}">{{ $article['category'] }}</a>
                <span>{{ $article['title'] }}</span>
            </nav>
            <a class="eyebrow" href="{{ route('knowledge.category', $article['category_slug']) }}">{{ $article['category'] }}</a>
            <h1>{{ $article['title'] }}</h1>
            <p>{{ $article['excerpt'] }}</p>
            <div class="article-meta hero-meta">
                <span>{{ $article['author'] }}</span>
                <span>Yayın: {{ $article['published_at'] }}</span>
                <span>Güncelleme: {{ $article['updated_at'] }}</span>
                <span>{{ $article['reading_time'] }}</span>
            </div>
        </div>
        <div class="visual-placeholder article-hero-media">
            <span>Makale kapak görseli</span>
            <small>Gerçek görsel yayın öncesi eklenecek</small>
        </div>
    </div>
</section>

<section class="section">
    <div class="container detail-layout">
        <article class="article-body">
            <div class="answer-box">
                <strong>Kısa cevap</strong>
                <p>{{ $article['answer'] }}</p>
            </div>

            <div class="toc-box">
                <strong>İçindekiler</strong>
                @if(! empty($articleSeo['sections']))
                    @foreach($articleSeo['sections'] as $section)
                        <a href="#{{ \Illuminate\Support\Str::slug($section['title']) }}">{{ $section['title'] }}</a>
                    @endforeach
                @else
                    <a href="#detayli-aciklama">Detaylı açıklama</a>
                    <a href="#dikkat-edilecekler">Dikkat edilecek noktalar</a>
                    <a href="#ilgili-hizmetler">İlgili hizmetler</a>
                    <a href="#ilgili-urunler">İlgili ürünler</a>
                @endif
            </div>

            @if(! empty($articleSeo['sections']))
                @foreach($articleSeo['sections'] as $section)
                    <h2 id="{{ \Illuminate\Support\Str::slug($section['title']) }}">{{ $section['title'] }}</h2>
                    @if(! empty($section['text']))
                        <p>{{ $section['text'] }}</p>
                    @endif
                    @if(! empty($section['items']))
                        @if(\Illuminate\Support\Str::contains($section['title'], 'Süreç'))
                            <div class="process-list">
                                @foreach($section['items'] as $item)
                                    <div><span>{{ $loop->iteration }}</span><strong>{{ $item }}</strong></div>
                                @endforeach
                            </div>
                        @else
                            <ul class="check-list two-column">
                                @foreach($section['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                @endforeach
                <h2>İç Linkler</h2>
                <div class="relation-list">
                    @foreach($articleSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            @else
                <h2 id="detayli-aciklama">Detaylı Açıklama</h2>
                <p>Bu makale şablonu yayın öncesinde gerçek uzman içeriği, kaynaklar, ilgili hizmetler, ilgili ürünler ve FAQ ilişkileriyle zenginleştirilecektir. İçerik yapısı kısa cevap, teknik detay ve pratik karar kriterleriyle ilerler.</p>

                <h2 id="dikkat-edilecekler">Dikkat Edilecek Noktalar</h2>
                <ul class="check-list two-column">
                    <li>Gerçek standart ve prosedür bilgisi kullanılmalı</li>
                    <li>İlgili hizmet sayfalarına açıklayıcı anchor ile link verilmeli</li>
                    <li>Ürün ilişkileri kategori ve marka bilgisiyle kurulmalı</li>
                    <li>FAQ alanı yalnızca sayfada gerçekten cevaplanan sorulardan oluşmalı</li>
                </ul>

                <h2 id="ilgili-hizmetler">İlgili Hizmetler</h2>
                <ul class="link-list relation-list">
                    @foreach($services as $service)
                        <li><a href="{{ route('services.show', $service['slug']) }}">{{ $service['title'] }}</a></li>
                    @endforeach
                </ul>

                <h2 id="ilgili-urunler">İlgili Ürünler</h2>
                <div class="relation-list">
                    @foreach($products as $product)
                        <a href="{{ route('products.show', $product['slug']) }}">{{ $product['name'] }} / {{ $product['brand'] }}</a>
                    @endforeach
                </div>
            @endif
        </article>
        <aside class="side-panel article-side">
            <h2>{{ $articleSeo['cta']['title'] ?? 'Bu konuda teknik destek alın' }}</h2>
            <p>{{ $articleSeo['cta']['text'] ?? 'Makale kaynaklı talepler admin fazında içerik URL’si ile lead kaydına bağlanacak.' }}</p>
            @if(! empty($articleSeo['support_links']))
                <div class="relation-list">
                    @foreach($articleSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            @endif
            <a class="button button-primary" href="{{ route('contact') }}">{{ $articleSeo['cta']['button'] ?? 'Teknik Ekibimize Ulaşın' }}</a>
        </aside>
    </div>
</section>
@endsection
