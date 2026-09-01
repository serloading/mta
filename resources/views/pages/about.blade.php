@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="container narrow">
        <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Ana Sayfa</a><span>Hakkımızda</span></nav>
        <span class="eyebrow">{{ $pageSeo['eyebrow'] }}</span>
        <h1>{{ $pageSeo['h1'] }}</h1>
        <p>{{ $pageSeo['hero_text'] }}</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('contact') }}">{{ $pageSeo['primary_cta'] }}</a>
            <a class="button button-secondary" href="{{ route('services.index') }}">{{ $pageSeo['secondary_cta'] }}</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container editorial-panel">
        <article class="article-body">
            <h2>2010'dan Bugüne Teknik Tedarik ve Destek</h2>
            <p>2010 yılında kurulan MTA Endüstri Ürünleri; laboratuvar, cihaz, ekipman ve sarf malzeme tedariğinde sektörün aranılan kuruluşlarından biri olma yönünde ilerlemektedir.</p>
            <p>Firmamız; kimya, gıda, ilaç, akademik, plastik, petrokimya ve medikal sektörleri başta olmak üzere kalite kontrol ve AR-GE laboratuvarlarında faaliyet gösteren iş ortaklarına sürdürülebilir destek sağlamayı ilke edinmiştir.</p>

            <h2>Markalarla Güçlü İş Birlikleri</h2>
            <p>İşbirliği içerisinde olduğu tedarikçilerine her geçen gün yenilerini ekleyen MTA Endüstri, iş ortaklarına daha iyi ve kaliteli hizmet sunmayı amaçlar.</p>
            <p>Müşteri memnuniyetini en üst noktaya taşımak, dünya markalarıyla kurumsal işbirlikleri yapmak ve sektördeki son gelişmeleri ülkemize taşımak en önemli hedeflerimiz arasındadır.</p>

            <h2>Satış Sonrası Destek Yaklaşımı</h2>
            <p>Ar-Ge ve kalite kontrol laboratuvarları bünyesinde bulunan test cihazları ve bu cihazlara ait sarf malzemeleri temini alanında faaliyet göstermeye başlayan firmamız; güvenilirliği, satış sonrası desteği ve müşteri eğitim faaliyetlerine verdiği önemle alanında bir marka haline gelmeyi hedeflemektedir.</p>

            @foreach($pageSeo['sections'] as $section)
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            @endforeach

            <div class="section-link-row align-left">
                @foreach($pageSeo['support_links'] as $link)
                    <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                @endforeach
            </div>
        </article>

        <aside class="side-panel">
            <span class="eyebrow">Uzmanlık</span>
            <h2>Çalışma alanları</h2>
            <ul class="check-list">
                @foreach($pageSeo['expertise'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
            <hr>
            <span class="eyebrow">İletişim</span>
            <h2>MTA Endüstri ile iletişime geçin</h2>
            <dl class="contact-list">
                <div><dt>Adres</dt><dd>{{ config('mta.site.address') }}</dd></div>
                <div><dt>Mail</dt><dd><a href="mailto:{{ config('mta.site.email') }}">{{ config('mta.site.email') }}</a></dd></div>
                <div><dt>Telefon</dt><dd><a href="tel:{{ preg_replace('/\D+/', '', config('mta.site.phone')) }}">{{ config('mta.site.phone') }}</a></dd></div>
                <div><dt>Fax</dt><dd>{{ config('mta.site.fax') }}</dd></div>
            </dl>
            <a class="button button-primary" href="{{ route('quote') }}">Teklif ve bilgi talebi</a>
        </aside>
    </div>
</section>

<section class="section section-muted">
    <div class="container section-header centered">
        <span class="eyebrow">Çalıştığımız alanlar</span>
        <h2>Kalite kontrol ve AR-GE laboratuvarlarına odaklanan yapı</h2>
    </div>
    <div class="container sector-grid">
        @foreach($pageSeo['sectors'] as $sector)
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
            <span class="eyebrow">Kurumsal iletişim</span>
            <h2>{{ $pageSeo['cta']['title'] }}</h2>
            <p>{{ $pageSeo['cta']['text'] }}</p>
        </div>
        <a class="button button-light" href="{{ route('contact') }}">{{ $pageSeo['cta']['button'] }}</a>
    </div>
</section>
@endsection
