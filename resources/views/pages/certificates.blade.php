@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="container narrow">
        <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Ana Sayfa</a><span>Sertifikalar</span></nav>
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
    <div class="container split-section">
        <div>
            <span class="eyebrow">Belge türleri</span>
            <h2>Yayınlanabilecek kurumsal belgeler</h2>
            <p>Gerçek belge dosyaları geldiğinde bu alan belge adı, açıklama, kapsam ve dosya bağlantısıyla doldurulacak.</p>
        </div>
        <div class="document-list">
            @foreach($pageSeo['document_types'] as $item)
                <div>
                    <strong>{{ $item }}</strong>
                    <span>Dosya ve açıklama alanı yayın onayı sonrası eklenecek.</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Bağlantılı sayfalar</span>
            <h2>Sertifika ve kurumsal bilgi bağlantıları</h2>
            <p>Sertifika içerikleri, hizmet kapsamları ve kurumsal bilgi sayfalarıyla birlikte değerlendirilir.</p>
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
            <span class="eyebrow">Belge bilgisi</span>
            <h2>{{ $pageSeo['cta']['title'] }}</h2>
            <p>{{ $pageSeo['cta']['text'] }}</p>
        </div>
        <a class="button button-light" href="{{ route('contact') }}">{{ $pageSeo['cta']['button'] }}</a>
    </div>
</section>
@endsection
