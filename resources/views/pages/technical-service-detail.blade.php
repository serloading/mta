@extends('layouts.site')

@section('content')
<section class="service-landing-hero technical-service-hero">
    <div class="container service-landing-grid">
        <div>
            <nav class="breadcrumb dark-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <a href="{{ route('technical-services.index') }}">Teknik Servis</a>
                <span>{{ $technicalService['title'] }}</span>
            </nav>
            <h1>{{ $technicalServiceSeo['h1'] ?? $technicalService['title'] }}</h1>
            <p>{{ $technicalServiceSeo['hero_text'] ?? $technicalService['summary'] . ' ' . $technicalService['answer'] }}</p>
            <div class="hero-actions">
                <a class="button button-light" href="{{ $quoteCta['quote_url'] }}">{{ $technicalServiceSeo['primary_cta'] ?? $technicalService['cta'] }}</a>
                <a class="button button-whatsapp" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                @if(! empty($technicalServiceSeo))
                    <a class="button button-outline-light" href="{{ $technicalServiceSeo['secondary_cta_url'] ?? route('services.show', 'kutle-terazi-kalibrasyonu') }}">{{ $technicalServiceSeo['secondary_cta'] }}</a>
                @else
                    <a class="button button-outline-light" href="#servis-islemleri">Servis kapsamı</a>
                @endif
            </div>
        </div>
        <div class="service-hero-media-card">
            @if(! empty($technicalService['image']))
                <img src="{{ asset($technicalService['image']) }}" alt="{{ $technicalServiceSeo['image_alt'] ?? $technicalService['image_alt'] ?? $technicalService['title'] }}">
            @else
                <div class="visual-placeholder service-hero-placeholder">
                    <span>Teknik servis görseli</span>
                    <small>Gerçek servis fotoğrafı burada kullanılacak</small>
                </div>
            @endif
            <div class="service-range-chip">
                <strong>Servis tipi</strong>
                <span>Yerinde veya laboratuvar ortamında değerlendirme</span>
            </div>
        </div>
    </div>
</section>

@if(! empty($technicalServiceSeo))
    <section class="section section-muted category-seo-section">
        <div class="container category-seo-grid">
            @foreach($technicalServiceSeo['sections'] as $section)
                <article class="category-seo-copy">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if(! empty($technicalServiceSeo['device_list']))
        <section class="section">
            <div class="container section-header centered">
                <span class="eyebrow">Cihaz grupları</span>
                <h2>{{ $technicalServiceSeo['device_list']['title'] }}</h2>
                <p>{{ $technicalServiceSeo['device_list']['text'] }}</p>
            </div>
            <div class="container device-card-grid compact">
                @foreach($technicalServiceSeo['device_list']['items'] as $item)
                    <article>
                        <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $item }}</h3>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container section-header centered">
            <span class="eyebrow">Arıza tespiti</span>
            <h2>{{ $technicalServiceSeo['faults']['title'] }}</h2>
            <p>{{ $technicalServiceSeo['faults']['text'] }}</p>
        </div>
        <div class="container fault-grid">
            @foreach($technicalServiceSeo['faults']['items'] as $fault)
                <article>
                    <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <strong>{{ $fault }}</strong>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section section-muted">
        <div class="container section-header centered">
            <span class="eyebrow">Bakım ve onarım</span>
            <h2>{{ $technicalServiceSeo['process']['title'] }}</h2>
            <p>{{ $technicalServiceSeo['process']['text'] }}</p>
        </div>
        <div class="container technical-step-grid">
            @foreach($technicalServiceSeo['process']['steps'] as $step)
                <article>
                    <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $step }}</h3>
                    <p>{{ $technicalServiceSeo['process']['descriptions'][$loop->index] ?? 'Servis adımı cihaz tipi ve arıza belirtisine göre teknik ekip tarafından değerlendirilir.' }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section category-seo-section">
        <div class="container category-seo-grid">
            @foreach($technicalServiceSeo['support_sections'] as $section)
                <article class="category-seo-copy">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['text'] }}</p>
                    <div class="section-link-row align-left">
                        @if(! empty($section['links']))
                            @foreach($section['links'] as $link)
                                <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                            @endforeach
                        @elseif($loop->iteration <= 2)
                            <a href="{{ route('services.show', 'kutle-terazi-kalibrasyonu') }}">terazi kalibrasyonu</a>
                        @else
                            <a href="{{ route('products.category', 'teraziler') }}">hassas terazi ve analitik terazi modelleri</a>
                            <a href="{{ route('products.brand', 'and') }}">A&amp;D hassas terazi modelleri</a>
                            <a href="{{ route('products.brand', 'ohaus') }}">Ohaus terazi modelleri</a>
                            <a href="{{ route('products.brand', 'shimadzu') }}">Shimadzu analitik terazi modelleri</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section section-muted">
        <div class="container section-header centered">
            <span class="eyebrow">SSS</span>
            <h2>Sık sorulan sorular</h2>
        </div>
        <div class="container faq-accordion">
            @foreach($technicalServiceSeo['faq'] as $faq)
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
                <h2>{{ $technicalServiceSeo['cta']['title'] }}</h2>
                <p>{{ $technicalServiceSeo['cta']['text'] }}</p>
                <p>{{ $technicalServiceSeo['cta']['note'] }}</p>
                <div class="section-link-row align-left">
                    <a href="{{ $quoteCta['quote_url'] }}">{{ $technicalServiceSeo['cta']['anchor'] ?? 'terazi teknik servis talebi' }}</a>
                </div>
            </div>
            <div class="hero-actions">
                <a class="button button-light" href="{{ $quoteCta['quote_url'] }}">{{ $technicalServiceSeo['cta']['button'] }}</a>
                <a class="button button-outline-light" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                <a class="button button-outline-light" href="{{ $technicalServiceSeo['related_products']['url'] ?? route('services.show', 'kutle-terazi-kalibrasyonu') }}">{{ $technicalServiceSeo['related_products']['button'] ?? 'Terazi Kalibrasyonu' }}</a>
            </div>
        </div>
    </section>

    @if($products->isNotEmpty())
        <section class="section section-muted">
            <div class="container section-header centered">
                <span class="eyebrow">İlgili ürünler</span>
                <h2>{{ $technicalServiceSeo['related_products']['title'] ?? 'Hassas terazi ve analitik terazi modelleri' }}</h2>
            </div>
            <div class="container card-grid two">
                @foreach($products as $product)
                    <article class="product-card compact-product-card">
                        @if(! empty($product['image']))
                            <img class="product-thumb object-image" src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] ?? $product['name'] }}">
                        @else
                            <div class="visual-placeholder product-thumb"><span>{{ $product['image_label'] }}</span></div>
                        @endif
                        <div>
                            <span>{{ $product['brand'] }} / {{ $product['category'] }}</span>
                            <h3>{{ $product['name'] }}</h3>
                            <p>{{ $product['summary'] }}</p>
                            <a href="{{ route('products.show', $product['slug']) }}">Ürün bilgisi al</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@else
<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Hizmetin tanımı</span>
        <h2>Cihazınızın teknik durumunu kalibrasyon ihtiyacıyla birlikte ele alın.</h2>
        <p>{{ $technicalService['body'] }}</p>
    </div>
    <div class="container device-card-grid">
        @foreach($technicalService['devices'] as $device)
            <article>
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $device }}</h3>
                <p>{{ $technicalService['title'] }} kapsamında ön kontrol, bakım, onarım veya servis değerlendirmesine alınabilir.</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted" id="servis-islemleri">
    <div class="container section-header centered">
        <span class="eyebrow">Verilen hizmetler</span>
        <h2>Teknik servis sürecinde yapılan işlemler.</h2>
        <p>Her cihaz marka, model, kullanım yoğunluğu ve arıza belirtisine göre ayrı değerlendirilir.</p>
    </div>
    <div class="container technical-step-grid">
        @foreach($technicalService['service_steps'] as $step)
            <article>
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Neden MTA Endüstri?</span>
            <h2>Servis ve kalibrasyon süreçlerini aynı teknik bakışla yönetin.</h2>
            <p>Teknik servis sonrası cihazın kalibrasyon ihtiyacı daha sağlıklı değerlendirilir; özellikle tartım, sıcaklık ve analiz cihazlarında bu ilişki toplam ölçüm güvenilirliğini artırır.</p>
            <ul class="check-list two-column">
                @foreach($technicalService['advantages'] as $advantage)
                    <li>{{ $advantage }}</li>
                @endforeach
            </ul>
        </div>
        <aside class="side-panel">
            <span class="eyebrow">İlgili kalibrasyonlar</span>
            <h2>Servis sonrası değerlendirilebilecek hizmetler</h2>
            <div class="relation-list">
                @foreach($services as $service)
                    <a href="{{ route('services.show', $service['slug']) }}">{{ $service['title'] }}</a>
                @endforeach
            </div>
        </aside>
    </div>
</section>

@if(! empty($technicalService['faq']))
    <section class="section section-muted">
        <div class="container section-header centered">
            <span class="eyebrow">SSS</span>
            <h2>Sık sorulan sorular</h2>
        </div>
        <div class="container faq-accordion">
            @foreach($technicalService['faq'] as $faq)
                <details>
                    <summary>{{ $faq['question'] }}</summary>
                    <p>{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endif

<section class="cta-band service-cta">
    <div class="container cta-shell centered-cta">
        <div>
            <span class="eyebrow">Teklif alın</span>
            <h2>{{ $technicalService['title'] }} için teknik ekibe ulaşın.</h2>
            <p>Marka, model, arıza belirtisi ve varsa cihaz fotoğrafını paylaşın; servis kapsamı için dönüş yapalım.</p>
        </div>
        <div class="hero-actions">
            <a class="button button-light" href="{{ $quoteCta['quote_url'] }}">Teklif Talebi Gönder</a>
            <a class="button button-outline-light" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
            <a class="button button-outline-light" href="{{ route('products.index') }}">Ürünleri İncele</a>
        </div>
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">İlgili ürünler</span>
        <h2>Servis kapsamıyla ilişkili ürün grupları.</h2>
    </div>
    <div class="container card-grid two">
        @foreach(collect($products)->take(4) as $product)
            <article class="product-card compact-product-card">
                @if(! empty($product['image']))
                    <img class="product-thumb object-image" src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] ?? $product['name'] }}">
                @else
                    <div class="visual-placeholder product-thumb"><span>{{ $product['image_label'] }}</span></div>
                @endif
                <div>
                    <span>{{ $product['brand'] }} / {{ $product['category'] }}</span>
                    <h3>{{ $product['name'] }}</h3>
                    <p>{{ $product['summary'] }}</p>
                    <a href="{{ route('products.show', $product['slug']) }}">Ürün bilgisi al</a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif
@endsection
