@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="container narrow">
        <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Ana Sayfa</a><span>Referanslar</span></nav>
        <span class="eyebrow">{{ $pageSeo['eyebrow'] }}</span>
        <h1>{{ $pageSeo['h1'] }}</h1>
        <p>{{ $pageSeo['hero_text'] }}</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('contact') }}">{{ $pageSeo['primary_cta'] }}</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container category-seo-grid">
        @foreach($pageSeo['sections'] as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">Sektörler</span>
        <h2>Hizmet verilen sektörler ve uygulama alanları</h2>
        <p>Bu bölüm gerçek müşteri adı üretmeden, hizmet verilen sektörleri ve teknik uygulama alanlarını anlatır.</p>
    </div>
    <div class="container card-grid three">
        @foreach($pageSeo['sectors'] as $sector)
            <article class="content-card">
                <span class="card-kicker">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h2>{{ $sector['name'] }}</h2>
                <p>{{ $sector['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Bağlantılı sayfalar</span>
            <h2>Hizmet ve ürün yapısını inceleyin</h2>
            <p>Sektör ihtiyaçları ürün kataloğu, teknik servis ve kalibrasyon hizmetleriyle birlikte değerlendirilir.</p>
        </div>
        <aside class="side-panel">
            <div class="relation-list">
                @foreach($pageSeo['support_links'] as $link)
                    <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                @endforeach
            </div>
        </aside>
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">SSS</span>
        <h2>Sık sorulan sorular</h2>
    </div>
    <div class="container faq-accordion">
        @foreach($pageSeo['faq'] as $faq)
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
            <span class="eyebrow">İş birliği</span>
            <h2>{{ $pageSeo['cta']['title'] }}</h2>
            <p>{{ $pageSeo['cta']['text'] }}</p>
        </div>
        <a class="button button-light" href="{{ route('contact') }}">{{ $pageSeo['cta']['button'] }}</a>
    </div>
</section>
@endsection
