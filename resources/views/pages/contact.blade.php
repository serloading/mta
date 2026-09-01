@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="container narrow">
        <nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Ana Sayfa</a><span>İletişim</span></nav>
        <span class="eyebrow">{{ $pageSeo['eyebrow'] }}</span>
        <h1>{{ $pageSeo['h1'] }}</h1>
        <p>{{ $pageSeo['hero_text'] }}</p>
    </div>
</section>

<section class="section">
    <div class="container contact-grid">
        @include('partials.lead-form', ['leadContext' => $leadContext ?? null, 'formAction' => $formAction ?? route('leads.store')])

        <aside class="side-panel">
            <h2>İletişim bilgileri</h2>
            <p>MTA Endüstri Ürünleri; laboratuvar, cihaz, ekipman ve sarf malzeme tedariğiyle birlikte kalibrasyon ve teknik servis talepleriniz için Pendik/İstanbul adresinde hizmet verir.</p>
            <dl class="contact-list">
                <div><dt>Telefon</dt><dd><a href="tel:{{ preg_replace('/\D+/', '', config('mta.site.phone')) }}">{{ config('mta.site.phone') }}</a></dd></div>
                <div><dt>E-posta</dt><dd><a href="mailto:{{ config('mta.site.email') }}">{{ config('mta.site.email') }}</a></dd></div>
                <div><dt>Fax</dt><dd>{{ config('mta.site.fax') }}</dd></div>
                <div><dt>Adres</dt><dd>{{ config('mta.site.address') }}</dd></div>
            </dl>
            <div class="contact-social">
                @foreach(config('mta.site.social_links') as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['name'] }}">
                        {{ $social['name'] }}
                    </a>
                @endforeach
            </div>
        </aside>
    </div>
</section>

<section class="section section-muted category-seo-section">
    <div class="container category-seo-grid">
        @foreach($pageSeo['sections'] as $section)
            <article class="category-seo-copy">
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
                @if($loop->first)
                    <ul class="check-list">
                        @foreach($pageSeo['request_info'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="section-link-row align-left">
                        @foreach($pageSeo['support_links'] as $link)
                            <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                        @endforeach
                    </div>
                @endif
            </article>
        @endforeach
    </div>
</section>

<section class="section section-muted">
    <div class="container contact-location-panel">
        <div>
            <span class="eyebrow">Adres</span>
            <h2>Bahçelievler, Köknar Sk. No:15/B, Pendik/İstanbul</h2>
            <p>Harita entegrasyonu canlı yayın öncesinde gerçek işletme konumu ve Google Business profiliyle bağlanacak.</p>
        </div>
        <a class="button button-secondary" href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('mta.site.address')) }}" target="_blank" rel="noopener">Haritada Aç</a>
    </div>
</section>

<section class="section">
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
@endsection
