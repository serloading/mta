@extends('layouts.site')

@section('content')
@php
    $total = count($results['products']) + count($results['categories']) + count($results['brands']);
@endphp

<div class="catalog-ui" data-catalog>
    <div class="cui-crumbbar">
        <div class="cui-shell">
            <nav class="cui-crumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                <span aria-current="page">Arama</span>
            </nav>
        </div>
    </div>

    <div class="cui-shell cui-page">
        <section class="cui-hero" style="display:block">
            <h1 style="margin:0;font-size:24px;font-weight:700">
                @if($query !== '')
                    "{{ $query }}" için {{ $total }} sonuç
                @else
                    Ürün ve hizmet arama
                @endif
            </h1>
            <form action="{{ route('search') }}" method="get" role="search" style="margin-top:16px;display:flex;gap:10px;max-width:560px">
                <input type="search" name="q" value="{{ $query }}" placeholder="Cihaz, model kodu veya kategori ara…"
                       style="flex:1;height:44px;padding:0 14px;border:1px solid #cbd5e1;border-radius:8px;font:inherit;font-size:14px" autofocus>
                <button type="submit" class="cui-btn" style="height:44px;padding:0 20px">Ara</button>
            </form>
        </section>

        @if($query === '')
            <p style="color:#64748b;font-size:14px">Aramak istediğiniz cihaz adını, model kodunu, kategoriyi veya markayı yazın.</p>
        @elseif($total === 0)
            <div class="cui-empty">
                <h2>Sonuç bulunamadı</h2>
                <p>"{{ $query }}" için eşleşme yok. Farklı bir anahtar kelime deneyin ya da tüm kataloğa göz atın.</p>
                <a class="cui-btn" href="{{ route('products.index') }}">Ürün Kataloğu</a>
            </div>
        @else
            @if(! empty($results['categories']) || ! empty($results['brands']))
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:28px">
                    @foreach(array_merge($results['categories'], $results['brands']) as $item)
                        <a href="{{ $item['url'] }}" class="cui-chip" aria-current="false">{{ $item['label'] }} <span style="color:#94a3b8">· {{ $item['sub'] }}</span></a>
                    @endforeach
                </div>
            @endif

            @if(! empty($results['products']))
                <h2 style="font-size:15px;font-weight:700;margin:0 0 14px">Ürünler</h2>
                <div class="cui-grid">
                    @foreach($results['products'] as $p)
                        <article class="cui-card">
                            <div class="cui-card-body">
                                <a class="cui-card-media" href="{{ $p['url'] }}" tabindex="-1" aria-hidden="true">
                                    @if($p['image'])
                                        <img src="{{ $p['image'] }}" alt="{{ $p['label'] }}" loading="lazy">
                                    @else
                                        <span class="cui-card-ph">{{ $p['label'] }}</span>
                                    @endif
                                </a>
                                <p class="cui-card-meta">{{ $p['sub'] }}</p>
                                <h3 class="cui-card-title"><a href="{{ $p['url'] }}">{{ $p['label'] }}</a></h3>
                            </div>
                            <div class="cui-card-foot">
                                <a class="cui-btn" href="{{ $p['url'] }}">İncele</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
