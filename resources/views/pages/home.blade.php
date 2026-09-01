@extends('layouts.site')

@section('content')
<section class="home-hero">
    <div class="container home-hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">Kalibrasyon, teknik servis ve laboratuvar cihazları</span>
            <h1>Kalibrasyon Hizmetleri, Teknik Servis ve Laboratuvar Cihazları</h1>
            <p>MTA Endüstri; basınç, sıcaklık, tork, devir, kütle-terazi ve hacim kalibrasyonu ile laboratuvar cihazları teknik servis ve ürün tedariğini tek çatı altında sunar. Kalite kontrol, üretim ve AR-GE süreçlerinde kullanılan cihazlar için ölçüm güvenilirliği, teknik destek ve teklif odaklı katalog yapısıyla çözüm sağlar.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="{{ route('quote') }}">Teklif Talebi Oluştur</a>
                <a class="button button-secondary" href="{{ route('services.index') }}">Kalibrasyon Hizmetlerini İncele</a>
            </div>
            <div class="hero-proof-strip">
                <span>2010'dan beri teknik tedarik</span>
                <span>Kalibrasyon + teknik servis</span>
                <span>Marka bazlı ürün kataloğu</span>
            </div>
        </div>
        <div class="hero-lab-card">
            <img src="{{ asset('images/technical-service/analiz-olcum-cihazlari-teknik-servis.webp') }}" alt="Laboratuvar analiz ve ölçüm cihazları için teknik servis ve kalibrasyon desteği">
            <div class="floating-certificate">
                <strong>Tek noktadan teknik destek</strong>
                <span>Ürün, servis ve kalibrasyon akışı</span>
            </div>
        </div>
    </div>
</section>

<section class="credential-band">
    <div class="container credential-grid">
        @foreach([
            ['title' => 'İzlenebilir Ölçüm', 'text' => 'Cihaz, referans, ölçüm noktası ve sonuç bilgisi rapor akışında birlikte değerlendirilir.'],
            ['title' => 'Teknik Kapsam', 'text' => 'Her hizmet alanı cihaz tipi, ölçüm aralığı, uygulama ve süreç bilgisiyle ayrı anlatılır.'],
            ['title' => 'Teklif Odaklı Akış', 'text' => 'Hizmet ve ürün talepleri doğru kategoriyle ilişkilendirilerek teknik ekibe yönlendirilir.'],
        ] as $item)
            <article>
                <strong>{{ $item['title'] }}</strong>
                <p>{{ $item['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">Kalibrasyon hizmetleri</span>
        <h2>Endüstriyel Kalibrasyon Hizmetleri</h2>
        <p>Kalibrasyon hizmetleri, ölçüm cihazlarının güvenilir referanslarla karşılaştırılması, sonuçların değerlendirilmesi ve raporlanması sürecidir. MTA Endüstri; manometre, termometre, terazi, tork ekipmanı, devir ölçüm cihazı ve hacim ekipmanları gibi farklı cihaz grupları için teknik kapsamı netleştirilmiş hizmet sayfaları sunar.</p>
        <div class="section-link-row">
            <a href="{{ route('services.index') }}">Tüm kalibrasyon hizmetleri</a>
            <a href="{{ route('services.show', 'kutle-terazi-kalibrasyonu') }}">Kütle & terazi kalibrasyonu</a>
            <a href="{{ route('services.show', 'sicaklik-kalibrasyonu') }}">Sıcaklık kalibrasyonu</a>
        </div>
    </div>
    <div class="container calibration-card-grid">
        @foreach($services as $service)
            <article class="calibration-card">
                <div class="calibration-icon">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                <h3>{{ $service['title'] }}</h3>
                <p>{{ $service['summary'] }}</p>
                <a href="{{ route('services.show', $service['slug']) }}">Detaylı incele</a>
            </article>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Teknik servis</span>
        <h2>Laboratuvar ve Ölçüm Cihazları İçin Teknik Servis</h2>
        <p>Laboratuvar ve analiz cihazlarında arıza tespiti, bakım, onarım, yedek parça değerlendirmesi ve kalibrasyon öncesi teknik hazırlık süreçleri doğru cihaz performansı için kritik öneme sahiptir. MTA Endüstri, cihaz tipine ve kullanım alanına göre teknik servis taleplerini değerlendirir.</p>
        <div class="section-link-row">
            <a href="{{ route('technical-services.index') }}">Teknik servis hizmetleri</a>
        </div>
    </div>
    <div class="container card-grid three">
        @foreach($technicalServices as $item)
            <article class="content-card service-list-card">
                @if(! empty($item['image']))
                    <img class="content-card-media object-image" src="{{ asset($item['image']) }}" alt="{{ $item['image_alt'] ?? $item['title'] }}">
                @endif
                <span class="card-kicker">{{ $item['category'] }}</span>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['summary'] }}</p>
                <a href="{{ route('technical-services.show', $item['slug']) }}">Servisi incele</a>
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container split-section">
        <div>
            <span class="eyebrow">Ürün kataloğu</span>
            <h2>Marka ve Kategori Bazlı Laboratuvar Cihazları</h2>
            <p>Laboratuvar cihazları kataloğunda teraziler, pH metreler, refraktometreler, viskozimetreler, nem tayin cihazları, titratörler ve karıştırıcılar marka, kategori ve teknik özellik bilgileriyle listelenir. Ürün sayfaları satın alma yerine bilgi ve teklif talebi oluşturacak şekilde kurgulanır.</p>
            <a class="button button-secondary" href="{{ route('products.index') }}">Ürünleri İncele</a>
            <div class="section-link-row align-left">
                <a href="{{ route('products.index') }}">Tüm laboratuvar cihazları</a>
                <a href="{{ route('products.category', 'teraziler') }}">Teraziler</a>
            </div>
        </div>
        <div class="product-preview-list">
            @foreach(collect($products)->take(4) as $product)
                <article class="mini-product">
                    @if(! empty($product['image']))
                        <img class="product-thumb object-image" src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] ?? $product['name'] }}">
                    @else
                        <div class="visual-placeholder product-thumb"><span>{{ $product['image_label'] }}</span></div>
                    @endif
                    <div>
                        <span>{{ $product['brand'] }} / {{ $product['category'] }}</span>
                        <h3>{{ $product['name'] }}</h3>
                        <a href="{{ route('products.show', $product['slug']) }}">Ürün sayfası</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Çalışma akışı</span>
        <h2>Kalibrasyon Süreci Nasıl İlerler?</h2>
        <p>Talep edilen cihaz veya hizmet bilgisi alınır, uygun kapsam değerlendirilir, ölçüm ve kontrol süreci planlanır. Sonuçlar teknik ekip tarafından raporlanır ve kullanıcıya teklif, servis veya kalibrasyon süreci hakkında net bilgi verilir.</p>
    </div>
    <div class="container horizontal-process">
        @foreach(['Teklif ve planlama', 'Cihaz kabulü', 'Ölçüm ve kontrol', 'Raporlama', 'Teslim ve takip'] as $step)
            <article>
                <span>{{ $loop->iteration }}</span>
                <strong>{{ $step }}</strong>
                <p>{{ $loop->iteration === 1 ? 'Talep edilen hizmet ve cihaz bilgisi netleştirilir.' : ($loop->iteration === 2 ? 'Cihaz kimlik bilgileri ve fiziksel durumu kayıt altına alınır.' : ($loop->iteration === 3 ? 'Referans ekipmanlarla belirlenen noktalarda ölçüm yapılır.' : ($loop->iteration === 4 ? 'Sonuçlar raporlanır ve uygunluk bilgileri hazırlanır.' : 'Rapor ve cihaz teslimi sonrası tekrar periyodu takip edilir.'))) }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="section lab-section">
    <div class="container lab-showcase">
        <div>
            <span class="eyebrow">Laboratuvar altyapısı</span>
            <h2>Kalite Kontrol ve AR-GE Laboratuvarları İçin Teknik Destek</h2>
            <p>MTA Endüstri; kimya, gıda, ilaç, akademik, plastik, petrokimya ve medikal sektörlerde kalite kontrol ve AR-GE laboratuvarlarının cihaz tedariği, bakım, teknik servis ve kalibrasyon ihtiyaçlarına çözüm sunar.</p>
            <div class="metric-grid">
                <div><strong>6</strong><span>kalibrasyon hizmeti</span></div>
                <div><strong>16</strong><span>ürün kategorisi</span></div>
                <div><strong>14</strong><span>marka grubu</span></div>
            </div>
        </div>
        <div class="lab-media-grid">
            <img src="{{ asset('images/services/basinc-kalibrasyonu.webp') }}" alt="Basınç ölçüm cihazları için kalibrasyon hizmeti">
            <img src="{{ asset('images/services/kutle-terazi-kalibrasyonu.webp') }}" alt="Hassas terazi ve kütle ekipmanları için kalibrasyon hizmeti">
            <img src="{{ asset('images/services/sicaklik-kalibrasyonu.jpg') }}" alt="Sıcaklık ölçüm cihazları ve termometreler için kalibrasyon hizmeti">
        </div>
    </div>
</section>

<section class="section">
    <div class="container section-header centered">
        <span class="eyebrow">Hizmet verdiğimiz alanlar</span>
        <h3 class="section-title">Laboratuvar, üretim ve kalite süreçleri için ölçüm güveni.</h3>
    </div>
    <div class="container sector-grid">
        @foreach(['Kalite kontrol', 'Üretim tesisleri', 'Laboratuvarlar', 'Bakım ekipleri', 'AR-GE birimleri', 'Teknik satın alma'] as $sector)
            <article>
                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <strong>{{ $sector }}</strong>
            </article>
        @endforeach
    </div>
</section>

<section class="cta-band">
    <div class="container cta-shell">
        <div>
            <span class="eyebrow">Teklif ve teknik bilgi</span>
            <h2>MTA Endüstri’den Teklif Alın</h2>
            <p>Cihazınız, hizmet ihtiyacınız veya ürün talebiniz için teknik ekibe ulaşın. Talebinizi doğru kategoriyle eşleştirip teklif sürecini başlatalım.</p>
        </div>
        <a class="button button-light" href="{{ route('quote') }}">Teklif Talebi Oluştur</a>
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">Blog</span>
        <h3 class="section-title">Kalibrasyon ve ürün seçimi hakkında teknik içerikler.</h3>
    </div>
    <div class="container card-grid two">
        @foreach($articles as $article)
            <article class="content-card">
                <span class="card-kicker">{{ $article['category'] }}</span>
                <h3>{{ $article['title'] }}</h3>
                <p>{{ $article['excerpt'] }}</p>
                <a href="{{ route('knowledge.show', $article['slug']) }}">İçeriği oku</a>
            </article>
        @endforeach
    </div>
</section>
@endsection
