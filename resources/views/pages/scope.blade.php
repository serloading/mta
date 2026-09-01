@extends('layouts.site')

@section('content')
<section class="kapsam-hero">
    <div class="container">
        <nav class="breadcrumb dark-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Ana Sayfa</a>
            <span>Kapsam</span>
        </nav>
        <span class="kapsam-eyebrow">KALİBRASYON KAPSAMI</span>
        <h1>Kalibrasyon Kapsamımız</h1>
        <p>
            Akredite laboratuvar altyapımızda; cihazlarınızı uluslararası izlenebilirlikle kalibre ediyor,
            kalite güvencenizi güçlendiriyoruz. Aşağıda cihaz grupları, ölçüm aralıkları, genişletilmiş
            ölçüm belirsizlikleri (U, k=2) ve uygulanan metot/standartlar yer alır.
        </p>
        <div class="kapsam-stats">
            <span><strong>{{ $scopeStats['categories'] }}</strong> ölçüm alanı</span>
            <span><strong>{{ $scopeStats['groups'] }}</strong> cihaz grubu</span>
            <span><strong>{{ $scopeStats['rows'] }}+</strong> aralık satırı</span>
        </div>
    </div>
</section>

<div class="kapsam-toolbar" data-scope-toolbar>
    <div class="container kapsam-toolbar-inner">
        <label class="kapsam-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" placeholder="Cihaz, aralık veya metot ara… (ör. manometre, Pt100, ISO 6789)" aria-label="Kapsam içinde ara" data-scope-search>
        </label>
        <div class="kapsam-chips" role="tablist" aria-label="Ölçüm alanları">
            <button type="button" class="kapsam-chip is-active" data-scope-filter="all">Tümü</button>
            @foreach($scopeCategories as $cat)
                <button type="button" class="kapsam-chip" data-scope-filter="{{ $cat['slug'] }}">{{ $cat['icon'] }} {{ $cat['title'] }}</button>
            @endforeach
        </div>
    </div>
</div>

<section class="section kapsam-section">
    <div class="container">
        @foreach($scopeCategories as $cat)
            <div class="kapsam-block" id="{{ $cat['slug'] }}" data-scope-block data-cat="{{ $cat['slug'] }}">
                <div class="kapsam-block-head">
                    <span class="kapsam-block-ico" aria-hidden="true">{{ $cat['icon'] }}</span>
                    <div>
                        <h2>{{ $cat['title'] }}</h2>
                        <p>{{ $cat['summary'] }} · {{ count($cat['groups']) }} grup</p>
                    </div>
                </div>

                <div class="kapsam-grid">
                    @foreach($cat['groups'] as $group)
                        <details class="kapsam-card" id="{{ $group['id'] }}" data-scope-card>
                            <summary>
                                <span class="kapsam-card-title">{{ $group['title'] }}</span>
                                <span class="kapsam-card-meta">{{ count($group['rows']) }} satır</span>
                                <svg class="kapsam-card-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </summary>
                            <div class="kapsam-card-body">
                                <div class="kapsam-table-wrap">
                                    <table class="kapsam-table">
                                        <thead>
                                            <tr>
                                                @foreach($group['columns'] as $col)
                                                    <th>{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['rows'] as $row)
                                                <tr>
                                                    @foreach($row as $cell)
                                                        <td>@if($cell === '—')<span class="kapsam-muted">—</span>@else{{ $cell }}@endif</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <a class="button button-primary kapsam-card-quote" href="{{ route('quote', ['source_type' => 'service', 'source_name' => 'Kapsam: ' . $group['title']]) }}">
                                    Bu gruptan teklif iste →
                                </a>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach

        <p class="kapsam-empty" data-scope-empty hidden>Aramanızla eşleşen cihaz grubu bulunamadı. Farklı bir terim deneyin.</p>

        <p class="kapsam-note">
            <span aria-hidden="true">*</span> {!! $scopeNote !!}
            Kapsam dışı bir cihazınız için <a href="{{ $genericQuoteCta['quote_url'] }}">teklif talebinde</a> bulunabilirsiniz.
        </p>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-shell centered-cta">
        <div>
            <span class="eyebrow">Teklif alın</span>
            <h2>İhtiyacınız olan kalibrasyonu buldunuz mu?</h2>
            <p>Cihaz listenizi iletin; kapsam, süre ve ücret bilgisini hızlıca hazırlayalım.</p>
        </div>
        <div class="hero-actions">
            <a class="button button-light" href="{{ $genericQuoteCta['quote_url'] }}">Teklif Al</a>
            <a class="button button-outline-light" href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Teklif Al</a>
            <a class="button button-outline-light" href="{{ route('services.index') }}">Kalibrasyon Hizmetleri</a>
        </div>
    </div>
</section>
@endsection
