@extends('layouts.site')

@section('content')
<section class="service-landing-hero technical-service-hero">
    <div class="container service-landing-grid">
        <div>
            <nav class="breadcrumb dark-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <span>Teknik Servis</span>
            </nav>
            <h1>{{ $technicalServicesSeo['h1'] }}</h1>
            <p>{{ $technicalServicesSeo['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="button button-light" href="{{ $genericQuoteCta['quote_url'] }}">{{ $technicalServicesSeo['primary_cta'] }}</a>
                <a class="button button-whatsapp" href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                <a class="button button-outline-light" href="{{ route('services.index') }}">{{ $technicalServicesSeo['secondary_cta'] }}</a>
            </div>
        </div>
        <div class="service-hero-media-card">
            <img src="{{ asset($technicalServicesSeo['image']) }}" alt="{{ $technicalServicesSeo['image_alt'] }}">
            <div class="service-range-chip">
                <strong>Kapsam</strong>
                <span>Laboratuvar, analiz, ölçüm ve tartım cihazları</span>
            </div>
        </div>
    </div>
</section>

<section class="section section-muted category-seo-section">
    <div class="container category-seo-grid">
        @foreach($technicalServicesSeo['sections'] as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Servis alanları</span>
        <h2>Teknik servis hizmet alanları</h2>
        <p>Cihaz grubuna göre arıza tespiti, bakım, onarım ve servis sonrası değerlendirme başlıkları ayrı sayfalarda sunulur.</p>
    </div>
    <div class="container card-grid three">
        @foreach($technicalServices as $item)
            @php($cardSeo = $technicalServicesSeo['service_cards'][$item['slug']] ?? null)
            <article class="content-card service-list-card">
                @if(! empty($item['image']))
                    <img class="content-card-media object-image" src="{{ asset($item['image']) }}" alt="{{ $item['image_alt'] ?? $item['title'] }}">
                @endif
                <span class="card-kicker">{{ $item['category'] }}</span>
                <h2>{{ $item['title'] }}</h2>
                <p>{{ $cardSeo['summary'] ?? $item['summary'] }}</p>
                <div class="content-card-actions">
                    <a href="{{ route('technical-services.show', $item['slug']) }}">{{ $cardSeo['anchor'] ?? 'İncele' }}</a>
                    <a href="{{ $quoteCtas[$item['slug']]['quote_url'] }}">Teklif Al</a>
                    <a href="{{ $quoteCtas[$item['slug']]['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Kapsam</span>
            <h2>{{ $technicalServicesSeo['scope_section']['title'] }}</h2>
            <p>{{ $technicalServicesSeo['scope_section']['text'] }}</p>
        </div>
        <div>
            <ul class="check-list two-column">
                @foreach($technicalServicesSeo['scope_section']['items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Servis akışı</span>
        <h2>{{ $technicalServicesSeo['process']['title'] }}</h2>
        <p>{{ $technicalServicesSeo['process']['text'] }}</p>
    </div>
    <div class="container horizontal-process">
        @foreach($technicalServicesSeo['process']['steps'] as $step)
            <article>
                <span>{{ $loop->iteration }}</span>
                <strong>{{ $step }}</strong>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Bağlantılı sayfalar</span>
            <h2>{{ $technicalServicesSeo['support_section']['title'] }}</h2>
            <p>{{ $technicalServicesSeo['support_section']['text'] }}</p>
        </div>
        <aside class="side-panel">
            <span class="eyebrow">İç linkler</span>
            <h2>Ürün ve kalibrasyon bağlantıları</h2>
            <div class="relation-list">
                @foreach($technicalServicesSeo['support_section']['links'] as $link)
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
        @foreach($technicalServicesSeo['faq'] as $faq)
            <details>
                <summary>{{ $faq['question'] }}</summary>
                <p>{{ $faq['answer'] }}</p>
            </details>
        @endforeach
    </div>
</section>

<section class="cta-band service-cta">
    <div class="container cta-shell centered-cta">
        <div>
            <span class="eyebrow">Teklif ve servis</span>
            <h2>{{ $technicalServicesSeo['cta']['title'] }}</h2>
            <p>{{ $technicalServicesSeo['cta']['text'] }}</p>
            <p>{{ $technicalServicesSeo['cta']['note'] }}</p>
            <div class="section-link-row align-left">
                <a href="{{ $genericQuoteCta['quote_url'] }}">{{ $technicalServicesSeo['cta']['anchor'] }}</a>
            </div>
        </div>
        <div class="hero-actions">
            <a class="button button-light" href="{{ $genericQuoteCta['quote_url'] }}">{{ $technicalServicesSeo['cta']['button'] }}</a>
            <a class="button button-outline-light" href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
        </div>
    </div>
</section>
@endsection
