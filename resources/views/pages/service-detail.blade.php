@extends('layouts.site')

@section('content')
<section class="service-landing-hero">
    <div class="container service-landing-grid">
        <div>
            <nav class="breadcrumb dark-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <a href="{{ route('services.index') }}">Kalibrasyon Hizmetleri</a>
                <span>{{ $service['title'] }}</span>
            </nav>
            <h1>{{ $serviceSeo['h1'] ?? $service['title'] }}</h1>
            <p>{{ $serviceSeo['hero_text'] ?? $service['summary'] . ' ' . $service['answer'] }}</p>
            <div class="hero-actions">
                <a class="button button-light" href="{{ $quoteCta['quote_url'] }}">{{ $serviceSeo['primary_cta'] ?? $service['cta'] }}</a>
                <a class="button button-whatsapp" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                @if(! empty($serviceSeo))
                    <a class="button button-outline-light" href="{{ $serviceSeo['secondary_cta_url'] ?? route('products.category', 'teraziler') }}">{{ $serviceSeo['secondary_cta'] }}</a>
                @else
                    <a class="button button-outline-light" href="#teknik-kapasite">Ölçüm aralıkları</a>
                @endif
            </div>
        </div>
        <div class="service-hero-media-card">
            @if(! empty($service['image']))
                <img src="{{ asset($service['image']) }}" alt="{{ $serviceSeo['image_alt'] ?? $service['image_alt'] ?? $service['title'] }}">
            @else
                <div class="visual-placeholder service-hero-placeholder">
                    <span>Hizmet görseli alanı</span>
                    <small>Laboratuvar, cihaz veya saha fotoğrafı burada kullanılacak</small>
                </div>
            @endif
            <div class="service-range-chip">
                <strong>Kapsam</strong>
                <span>{{ $serviceSeo['scope_chip'] ?? 'Yayın öncesi eklenecek' }}</span>
            </div>
        </div>
    </div>
</section>

@if(! empty($serviceSeo))
    <section class="section section-muted category-seo-section">
        <div class="container category-seo-grid">
            @foreach(array_slice($serviceSeo['sections'], 0, 4) as $section)
                <article class="category-seo-copy">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if(! empty($serviceSeo['device_list']))
        <section class="section">
            <div class="container section-header centered">
                <span class="eyebrow">Cihaz kapsamı</span>
                <h2>{{ $serviceSeo['device_list']['title'] }}</h2>
                <p>{{ $serviceSeo['device_list']['text'] }}</p>
            </div>
            <div class="container device-card-grid compact">
                @foreach($serviceSeo['device_list']['items'] as $item)
                    <article>
                        <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $item }}</h3>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="section" id="teknik-kapasite">
        <div class="container section-header centered">
            <span class="eyebrow">Teknik kapsam</span>
            <h2>{{ $serviceSeo['scope']['title'] }}</h2>
            <p>{{ $serviceSeo['scope']['text'] }}</p>
        </div>
        <div class="container capacity-table-card">
            <table>
                <thead>
                <tr>
                    @foreach($serviceSeo['scope']['headings'] as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($serviceSeo['scope']['rows'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="section section-muted">
        <div class="container section-header centered">
            <span class="eyebrow">Kalibrasyon süreci</span>
            <h2>{{ $serviceSeo['process']['title'] }}</h2>
            <p>{{ $serviceSeo['process']['text'] }}</p>
        </div>
        <div class="container horizontal-process">
            @foreach($serviceSeo['process']['steps'] as $step)
                <article>
                    <span>{{ $loop->iteration }}</span>
                    <strong>{{ $step }}</strong>
                    <p>{{ $serviceSeo['process']['descriptions'][$loop->index] ?? ($loop->iteration === 1 ? 'Cihaz tipi, marka, model ve kullanım alanı bilgisi alınır.' : ($loop->iteration === 2 ? 'Kapasite, okunabilirlik ve fiziksel durum ön kontrolle değerlendirilir.' : ($loop->iteration === 3 ? 'Belirlenen noktalarda referans kütlelerle karşılaştırma yapılır.' : ($loop->iteration === 4 ? 'Sapma ve uygunluk bilgileri teknik ekip tarafından incelenir.' : 'Kalibrasyon raporu ve teslim bilgileri kullanıcıya paylaşılır.')))) }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section category-seo-section">
        <div class="container category-seo-grid">
            @foreach($serviceSeo['support_sections'] as $section)
                <article class="category-seo-copy">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['text'] }}</p>
                    <div class="section-link-row align-left">
                        @if(! empty($section['links']))
                            @foreach($section['links'] as $link)
                                <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                            @endforeach
                        @elseif($loop->first)
                            <a href="{{ route('technical-services.show', 'terazi-teknik-servis') }}">terazi teknik servis</a>
                        @else
                            <a href="{{ route('products.category', 'teraziler') }}">hassas terazi ve analitik terazi modelleri</a>
                            <a href="{{ route('products.brand', 'and') }}">A&amp;D hassas terazi modelleri</a>
                            <a href="{{ route('products.brand', 'ohaus') }}">Ohaus terazi modelleri</a>
                            <a href="{{ route('products.brand', 'shimadzu') }}">Shimadzu analitik terazi modelleri</a>
                            <a href="{{ route('products.brand', 'weightlab') }}">Weightlab laboratuvar terazileri</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if(! empty($serviceSeo['list_section']))
        <section class="section section-muted">
            <div class="container split-section">
                <div>
                    <span class="eyebrow">Kullanım alanları</span>
                    <h2>{{ $serviceSeo['list_section']['title'] }}</h2>
                    <p>{{ $serviceSeo['list_section']['text'] }}</p>
                </div>
                <div>
                    <ul class="check-list two-column">
                        @foreach($serviceSeo['list_section']['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

    <section class="section section-muted">
        <div class="container section-header centered">
            <span class="eyebrow">SSS</span>
            <h2>Sık sorulan sorular</h2>
        </div>
        <div class="container faq-accordion">
            @foreach($serviceSeo['faq'] as $faq)
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
                <h2>{{ $serviceSeo['cta']['title'] }}</h2>
                <p>{{ $serviceSeo['cta']['text'] }}</p>
                <p>{{ $serviceSeo['cta']['note'] }}</p>
                <div class="section-link-row align-left">
                    <a href="{{ $quoteCta['quote_url'] }}">{{ $serviceSeo['cta']['anchor'] ?? 'terazi kalibrasyonu teklif talebi' }}</a>
                </div>
            </div>
            <div class="hero-actions">
                <a class="button button-light" href="{{ $quoteCta['quote_url'] }}">{{ $serviceSeo['cta']['button'] }}</a>
                <a class="button button-outline-light" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
                <a class="button button-outline-light" href="{{ $serviceSeo['related_products']['url'] ?? route('products.category', 'teraziler') }}">{{ $serviceSeo['related_products']['button'] ?? 'Hassas Teraziler' }}</a>
            </div>
        </div>
    </section>

    @if($products->isNotEmpty())
        <section class="section section-muted">
            <div class="container section-header centered">
                <span class="eyebrow">İlgili ürünler</span>
                <h2>{{ $serviceSeo['related_products']['title'] ?? 'Hassas terazi ve analitik terazi modelleri' }}</h2>
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
        <span class="eyebrow">Hizmet kapsamı</span>
        <h2>Kalibre edilebilecek cihazlar</h2>
        <p>{{ $service['body'] }}</p>
    </div>
    <div class="container device-card-grid">
        @foreach($service['devices'] as $device)
            <article>
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $device }}</h3>
                <p>{{ $service['title'] }} kapsamında ölçüm, doğrulama ve raporlama sürecine dahil edilebilir.</p>
            </article>
        @endforeach
    </div>
</section>

@if(! empty($service['scope_groups']))
    <section class="section scope-detail-section">
        <div class="container section-header centered">
            <span class="eyebrow">Detaylı kapsam</span>
            <h2>Cihaz ve ölçüm aralığı listesi</h2>
            <p>Bu liste hizmet kapsamını cihaz tipi ve çalışma aralığıyla birlikte gösterir. Nihai akreditasyon/kapsam bilgileri yayın öncesi onaylanacaktır.</p>
        </div>
        <div class="container scope-group-grid">
            @foreach($service['scope_groups'] as $group)
                <article class="scope-group-card">
                    <h3>{{ $group['title'] }}</h3>
                    <div class="scope-table">
                        @foreach($group['items'] as $item)
                            <div>
                                <strong>{{ $item['name'] }}</strong>
                                <span>{{ $item['range'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

<section class="section section-muted" id="teknik-kapasite">
    <div class="container section-header centered">
        <span class="eyebrow">Teknik kapasite</span>
        <h2>Ölçüm aralıkları ve belirsizlik</h2>
        <p>Gerçek kapsam, referans ekipman ve prosedür bilgileri yayın öncesinde net değerlerle güncellenecek.</p>
    </div>
    <div class="container capacity-table-card">
        <table>
            <thead>
            <tr>
                <th>Parametre</th>
                <th>Aralık / Bilgi</th>
                <th>Not</th>
            </tr>
            </thead>
            <tbody>
            @foreach($service['capacity'] as $row)
                <tr>
                    <td>{{ $row['parameter'] }}</td>
                    <td>{{ $row['range'] }}</td>
                    <td>{{ $row['note'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Nasıl çalışır?</span>
        <h2>Kalibrasyon süreci</h2>
        <p>Cihaz kabulünden rapor teslimine kadar her adım ölçüm güvenliği ve izlenebilirlik odağıyla yürütülür.</p>
    </div>
    <div class="container horizontal-process">
        @foreach($service['process'] as $step)
            <article>
                <span>{{ $loop->iteration }}</span>
                <strong>{{ $step }}</strong>
                <p>{{ $loop->iteration === 1 ? 'Cihaz ve hizmet ihtiyacı belirlenir.' : ($loop->iteration === 2 ? 'Cihaz kabulü, kimlik bilgisi ve ön kontrol yapılır.' : ($loop->iteration === 3 ? 'Referans ekipmanlarla ölçüm gerçekleştirilir.' : ($loop->iteration === 4 ? 'Sonuçlar değerlendirilir ve rapor hazırlanır.' : 'Teslim ve sonraki periyot bilgisi paylaşılır.'))) }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Standartlar ve kullanım alanları</span>
            <h2>Hangi süreçlerde kullanılır?</h2>
            <p>{{ $service['title'] }}; ölçüm güvenliği, kalite kontrol, üretim sürekliliği ve bakım planlaması için kritik cihazlarda tercih edilir.</p>
            <div class="badge-row">
                @foreach($service['standards'] as $standard)
                    <span>{{ $standard }}</span>
                @endforeach
            </div>
        </div>
        <div class="device-card-grid compact">
            @foreach($service['applications'] as $application)
                <article>
                    <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $application }}</h3>
                    <p>Bu alandaki cihazlar için teklif ve kapsam değerlendirmesi yapılabilir.</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

@if(! empty($service['faq']))
    <section class="section">
        <div class="container section-header centered">
            <span class="eyebrow">SSS</span>
            <h2>Sık sorulan sorular</h2>
        </div>
        <div class="container faq-accordion">
            @foreach($service['faq'] as $faq)
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
            <h2>{{ $service['title'] }} için teknik ekibe ulaşın.</h2>
            <p>Cihaz türünü, adet bilgisini ve ölçüm ihtiyacınızı paylaşın; en uygun hizmet kapsamı için dönüş yapalım.</p>
        </div>
        <div class="hero-actions">
            <a class="button button-light" href="{{ $quoteCta['quote_url'] }}">Teklif Talebi Gönder</a>
            <a class="button button-outline-light" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
            <a class="button button-outline-light" href="{{ route('products.index') }}">İlgili Ürünler</a>
        </div>
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">İlgili ürünler</span>
        <h2>Bu hizmetle ilişkilendirilebilecek katalog ürünleri</h2>
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
@endsection
