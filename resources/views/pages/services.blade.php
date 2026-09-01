@extends('layouts.site')

@section('content')
<section class="service-landing-hero">
    <div class="container service-landing-grid">
        <div>
            <nav class="breadcrumb dark-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <span>Kalibrasyon Hizmetleri</span>
            </nav>
            <h1>{{ $servicesSeo['h1'] }}</h1>
            <p>{{ $servicesSeo['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="button button-light" href="{{ $genericQuoteCta['quote_url'] }}">{{ $servicesSeo['primary_cta'] }}</a>
                <a class="button button-whatsapp" href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                <a class="button button-outline-light" href="#hizmet-alanlari">{{ $servicesSeo['secondary_cta'] }}</a>
            </div>
        </div>
        <div class="service-hero-media-card">
            <img src="{{ asset($servicesSeo['image']) }}" alt="{{ $servicesSeo['image_alt'] }}">
            <div class="service-range-chip">
                <strong>Kapsam</strong>
                <span>Basınç, sıcaklık, tork, devir, kütle-terazi, hacim</span>
            </div>
        </div>
    </div>
</section>

<section class="section section-muted category-seo-section">
    <div class="container category-seo-grid">
        @foreach($servicesSeo['sections'] as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section" id="hizmet-alanlari">
    <div class="container section-header centered">
        <span class="eyebrow">Hizmet alanları</span>
        <h2>Kalibrasyon hizmet alanları</h2>
        <p>Her hizmet sayfası cihaz kapsamı, ölçüm aralığı, süreç ve teklif yönlendirmesiyle ayrı yapılandırılmıştır.</p>
    </div>
    <div class="container card-grid three">
        @foreach($services as $service)
            @php($cardSeo = $servicesSeo['service_cards'][$service['slug']] ?? null)
            <article class="content-card">
                @if(! empty($service['image']))
                    <img class="content-card-media object-image" src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] ?? $service['title'] }}">
                @endif
                <span class="card-kicker">{{ $service['category'] }}</span>
                <h2>{{ $service['title'] }}</h2>
                <p>{{ $cardSeo['summary'] ?? $service['summary'] }}</p>
                <div class="content-card-actions">
                    <a href="{{ route('services.show', $service['slug']) }}">{{ $cardSeo['anchor'] ?? 'İncele' }}</a>
                    <a href="{{ $quoteCtas[$service['slug']]['quote_url'] }}">Teklif Al</a>
                    <a href="{{ $quoteCtas[$service['slug']]['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Cihaz kapsamı</span>
            <h2>{{ $servicesSeo['device_section']['title'] }}</h2>
            <p>{{ $servicesSeo['device_section']['text'] }}</p>
        </div>
        <div>
            <ul class="check-list two-column">
                @foreach($servicesSeo['device_section']['items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Süreç</span>
        <h2>{{ $servicesSeo['process']['title'] }}</h2>
        <p>{{ $servicesSeo['process']['text'] }}</p>
    </div>
    <div class="container horizontal-process">
        @foreach($servicesSeo['process']['steps'] as $step)
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
            <span class="eyebrow">Bağlantılı süreçler</span>
            <h2>{{ $servicesSeo['support_section']['title'] }}</h2>
            <p>{{ $servicesSeo['support_section']['text'] }}</p>
        </div>
        <aside class="side-panel">
            <span class="eyebrow">İç linkler</span>
            <h2>Teknik servis ve katalog</h2>
            <div class="relation-list">
                @foreach($servicesSeo['support_section']['links'] as $link)
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
        @foreach($servicesSeo['faq'] as $faq)
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
            <span class="eyebrow">Teklif alın</span>
            <h2>{{ $servicesSeo['cta']['title'] }}</h2>
            <p>{{ $servicesSeo['cta']['text'] }}</p>
            <p>{{ $servicesSeo['cta']['note'] }}</p>
            <div class="section-link-row align-left">
                <a href="{{ $genericQuoteCta['quote_url'] }}">{{ $servicesSeo['cta']['anchor'] }}</a>
            </div>
        </div>
        <div class="hero-actions">
            <a class="button button-light" href="{{ $genericQuoteCta['quote_url'] }}">{{ $servicesSeo['cta']['button'] }}</a>
            <a class="button button-outline-light" href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
            <a class="button button-outline-light" href="{{ route('technical-services.index') }}">Teknik Servis Hizmetleri</a>
        </div>
    </div>
</section>
@endsection
