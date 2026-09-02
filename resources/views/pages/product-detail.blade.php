@extends('layouts.site')

@section('content')
@php
    use Illuminate\Support\Str;

    // --- documents & videos -------------------------------------------------
    $linkableDocuments = collect($product['documents'] ?? [])->filter(fn ($document) => is_array($document)
        && (filled($document['url'] ?? null) || filled($document['path'] ?? null)));
    $hasDocuments = ! empty($product['documents']);
    $hasVideos = ! empty($product['videos']);
    $showDocVideoTab = $hasDocuments || $hasVideos;

    // PDF/Word button in the action bar: prefer a "catalog" doc, else any linkable doc.
    $catalogDocument = $linkableDocuments->firstWhere('type', 'catalog') ?? $linkableDocuments->first();
    $catalogHref = $catalogDocument
        ? ($catalogDocument['url'] ?? asset($catalogDocument['path']))
        : null;

    // --- gallery images (main + gallery strip) ------------------------------
    $galleryImages = collect();
    if (! empty($product['image'])) {
        $galleryImages->push(img_url($product['image']));
    }
    foreach (($product['gallery'] ?? []) as $galleryImage) {
        $galleryImages->push(img_url($galleryImage));
    }
    $galleryImages = $galleryImages->filter()->unique()->values();
    $mainImage = $galleryImages->first();
    $imageAlt = $product['image_alt'] ?? $product['name'];

    // --- spec set ----------------------------------------------------------
    $allSpecs = collect($product['specs'] ?? [])
        ->reject(fn ($value, $key) => trim((string) $value) === '' || Str::slug((string) $key) === 'gorsel-durumu');

    // --- quick key specs (up to 3, priority first) ------------------------
    $keySpecPriority = ['hassasiyet', 'okunabilirlik', 'kapasite', 'sicaklik-araligi', 'calisma-sicaklik-araligi', 'olcum-araligi', 'ph-araligi', 'donme-hizi', 'cozunurluk'];
    $genericKeys = ['marka', 'model', 'kategori', 'urun-grubu', 'sku', 'seri'];
    $keySpecs = [];
    foreach ($allSpecs as $key => $value) {
        if (in_array(Str::slug((string) $key), $keySpecPriority, true)) {
            $keySpecs[$key] = $value;
        }
        if (count($keySpecs) >= 3) break;
    }
    if (count($keySpecs) < 3) {
        foreach ($allSpecs as $key => $value) {
            if (! isset($keySpecs[$key]) && ! in_array(Str::slug((string) $key), $genericKeys, true)) {
                $keySpecs[$key] = $value;
            }
            if (count($keySpecs) >= 3) break;
        }
    }

    // --- quick facts ------------------------------------------------------
    $modelFact = ! empty($product['model']) && $product['model'] !== $product['name'] ? $product['model'] : null;
    $skuRaw = trim((string) ($product['sku'] ?? ''));
    $skuFact = $skuRaw !== '' && ! Str::startsWith($skuRaw, 'MTA-') && ! Str::contains(Str::lower($skuRaw), 'netleşt') ? $skuRaw : null;

    // --- cross-sell calibration service --------------------------------
    $crossSellService = $relatedServices->first();

    $primaryCtaLabel = $productSeo['primary_cta'] ?? 'Teklif Alın / Fiyat İste';
@endphp

<div class="pdp-ui" data-pdp>
    <div class="pdp-shell">
        <div class="pdp-buybox">
            {{-- ============ LEFT: media gallery ============ --}}
            <div class="pdp-gallery">
                <div class="pdp-stage">
                    @if($mainImage)
                        <img data-pdp-main-img src="{{ $mainImage }}" alt="{{ $imageAlt }}">
                        <button type="button" class="pdp-zoom" data-pdp-zoom aria-label="Görseli büyüt">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        </button>
                    @else
                        <span class="pdp-stage-ph">{{ $product['image_label'] ?? $product['name'] }}<br><small>Gerçek ürün fotoğrafı geldiğinde tek ana görsel olarak kullanılacak.</small></span>
                    @endif
                </div>

                @if($galleryImages->count() > 1)
                    <div class="pdp-thumbs product-gallery-strip" aria-label="Ürün galerisi">
                        @foreach($galleryImages as $galleryImage)
                            <button type="button" class="pdp-thumb" data-pdp-thumb data-full="{{ $galleryImage }}" @if($loop->first) aria-current="true" @endif aria-label="Görsel {{ $loop->iteration }}">
                                <img src="{{ $galleryImage }}" alt="{{ $imageAlt }} galeri görseli {{ $loop->iteration }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ============ RIGHT: buy box ============ --}}
            <div class="pdp-buybox-main">
                <nav class="pdp-crumbs" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Ana Sayfa</a>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <a href="{{ route('products.category', $product['category_slug']) }}">{{ $product['category'] }}</a>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <a href="{{ route('products.brand', $product['brand_slug']) }}" aria-current="page">{{ $product['brand'] }}</a>
                </nav>

                <a class="pdp-brandmark" href="{{ route('products.brand', $product['brand_slug']) }}"
                   aria-label="{{ $product['brand'] }} markasının tüm ürünleri">
                    @if($brandLogo)
                        <img src="{{ img_url($brandLogo) }}" alt="">
                    @endif
                    <span>{{ $product['brand'] }}</span>
                </a>

                <h1 class="pdp-title">{{ $product['name'] }}</h1>

                @if(! empty($product['summary']))
                    <p class="pdp-lead">{{ Str::limit($product['summary'], 260) }}</p>
                @endif

                @if(count($keySpecs) >= 2)
                    <dl class="pdp-keyspecs">
                        @foreach($keySpecs as $key => $value)
                            <div>
                                <dt>{{ $key }}</dt>
                                <dd>{{ Str::limit(trim((string) $value), 40) }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif

                <form method="get" action="{{ route('quote') }}" class="pdp-quote-form">
                    <input type="hidden" name="product" value="{{ $product['slug'] }}">

                    @if($crossSellService)
                        <div class="pdp-crosssell">
                            <label>
                                <input type="checkbox" name="kalibrasyon" value="{{ $crossSellService['slug'] }}">
                                <span>
                                    <strong>Akredite kalibrasyon hizmeti eklensin mi?</strong>
                                    Cihazınız için <a href="{{ route('services.show', $crossSellService['slug']) }}">{{ $crossSellService['title'] }}</a> hizmetini teklif talebinize ekleyebiliriz.
                                </span>
                            </label>
                        </div>
                    @endif

                    <div class="pdp-actions">
                        <button type="submit" class="pdp-btn pdp-btn--cta">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            {{ $primaryCtaLabel }}
                        </button>
                        <a class="pdp-btn pdp-btn--wa" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.8 4.9-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.9.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.6-6c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2a.4.4 0 0 0 0-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3A2.8 2.8 0 0 0 6.7 10a4.9 4.9 0 0 0 1 2.6 11.2 11.2 0 0 0 4.3 3.8c2 .8 2 .5 2.4.5a2.5 2.5 0 0 0 1.6-1.2 2 2 0 0 0 .2-1.2c-.1-.1-.3-.2-.5-.3z"/></svg>
                            WhatsApp ile Bilgi Al
                        </a>
                        @if($catalogHref)
                            <a class="pdp-btn pdp-btn--ghost" href="{{ $catalogHref }}" target="_blank" rel="noopener">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Kataloğu İncele
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ STICKY TAB BAR ============ --}}
    <div class="pdp-tabsbar" data-pdp-tabsbar>
        <div class="pdp-tabs" role="tablist" aria-label="Ürün bilgileri">
            <button type="button" class="pdp-tab" role="tab" id="tab-genel-bakis" data-pdp-tab="genel-bakis" aria-controls="panel-genel-bakis" aria-selected="true">Genel Bakış</button>
            <button type="button" class="pdp-tab" role="tab" id="tab-teknik-ozellikler" data-pdp-tab="teknik-ozellikler" aria-controls="panel-teknik-ozellikler" aria-selected="false" tabindex="-1">Teknik Özellikler</button>
            @if($showDocVideoTab)
                <button type="button" class="pdp-tab" role="tab" id="tab-dokuman-video" data-pdp-tab="dokuman-video" aria-controls="panel-dokuman-video" aria-selected="false" tabindex="-1">Doküman &amp; Video</button>
            @endif
            <button type="button" class="pdp-tab" role="tab" id="tab-sss-destek" data-pdp-tab="sss-destek" aria-controls="panel-sss-destek" aria-selected="false" tabindex="-1">SSS &amp; Destek</button>
        </div>
    </div>

    <div class="pdp-panels">
        {{-- Tab 1: Genel Bakış --}}
        <section class="pdp-panel" id="panel-genel-bakis" role="tabpanel" aria-labelledby="tab-genel-bakis" data-pdp-panel="genel-bakis">
            <div class="pdp-overview">
                <div class="pdp-prose">
                    @if(! empty($productSeo['sections']))
                        <h2 class="pdp-h2">{{ $productSeo['sections'][0]['title'] }}</h2>
                        <p>{{ $productSeo['sections'][0]['text'] }}</p>
                        @foreach(array_slice($productSeo['sections'], 1) as $section)
                            <h3>{{ $section['title'] }}</h3>
                            <p>{{ $section['text'] }}</p>
                        @endforeach
                    @else
                        <h2 class="pdp-h2">Ürün Açıklaması</h2>
                        <p>{{ $product['summary'] }}</p>
                    @endif

                    @if($crossSellService)
                        <h3>{{ $product['name'] }} için ilgili kalibrasyon hizmeti</h3>
                        <p>{{ $product['brand'] }} {{ $modelFact ?? $product['category'] }} ürünü, {{ $product['category'] }} kategorisindeki kullanım senaryoları nedeniyle
                            <a href="{{ route('services.show', $crossSellService['slug']) }}">{{ $crossSellService['title'] }}</a> başta olmak üzere ilgili kalibrasyon hizmetleriyle birlikte değerlendirilebilir.</p>
                    @endif
                </div>

                <div class="pdp-advantages">
                    <h3>Öne Çıkan Avantajlar</h3>
                    <ul>
                        @foreach(($product['features'] ?? []) as $feature)
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        {{-- Tab 2: Teknik Özellikler --}}
        <section class="pdp-panel" id="panel-teknik-ozellikler" role="tabpanel" aria-labelledby="tab-teknik-ozellikler" data-pdp-panel="teknik-ozellikler" hidden>
            <h2 class="pdp-h2">Teknik Özellikler</h2>
            @if($allSpecs->isNotEmpty())
                <div class="pdp-tablewrap">
                    <table class="pdp-table">
                        <tbody>
                            @foreach($allSpecs as $key => $value)
                                <tr>
                                    <th scope="row">{{ $key }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="pdp-prose">Bu ürün için teknik özellik tablosu hazırlanıyor. Detaylı teknik veri için teklif talebi oluşturabilirsiniz.</p>
            @endif
        </section>

        {{-- Tab 3: Doküman & Video (only when there is at least one document or video) --}}
        @if($showDocVideoTab)
        <section class="pdp-panel" id="panel-dokuman-video" role="tabpanel" aria-labelledby="tab-dokuman-video" data-pdp-panel="dokuman-video" hidden>
            <div class="pdp-media-grid @if(! ($hasDocuments && $hasVideos)) pdp-media-grid--single @endif">
                @if($hasDocuments)
                    <div>
                        <h2 class="pdp-h2">Dokümanlar ve PDF Kataloglar</h2>
                        <div class="pdp-doclist">
                            @foreach($product['documents'] as $document)
                                @php
                                    $documentTitle = is_array($document) ? ($document['title'] ?? 'Ürün dokümanı') : $document;
                                    $documentType = is_array($document) ? ($document['type'] ?? 'catalog') : 'catalog';
                                    $documentHref = is_array($document) ? ($document['url'] ?? (! empty($document['path']) ? asset($document['path']) : null)) : null;
                                @endphp
                                <div class="pdp-doc">
                                    <span class="pdp-doc-ico">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </span>
                                    <span class="pdp-doc-main">
                                        <strong>{{ $documentTitle }}</strong>
                                        <span>@if($documentHref){{ strtoupper($documentType) }} dosyası yeni sekmede açılır@else Dosya yüklendiğinde indirilebilir bağlantı olacak @endif</span>
                                    </span>
                                    @if($documentHref)
                                        <a class="pdp-doc-go" href="{{ $documentHref }}" target="_blank" rel="noopener" aria-label="{{ $documentTitle }} dosyasını aç">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($hasVideos)
                    <div>
                        <h2 class="pdp-h2">Ürün Videosu</h2>
                        <div class="pdp-videos">
                            @foreach($product['videos'] as $video)
                                <article>
                                    <div class="pdp-video-frame">
                                        <iframe
                                            src="https://www.youtube-nocookie.com/embed/{{ $video['youtube_id'] }}"
                                            title="{{ $video['title'] }}"
                                            loading="lazy"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            allowfullscreen></iframe>
                                    </div>
                                    <p class="pdp-video-cap">{{ $video['title'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Tab 4: SSS & Destek --}}
        <section class="pdp-panel" id="panel-sss-destek" role="tabpanel" aria-labelledby="tab-sss-destek" data-pdp-panel="sss-destek" hidden>
            <h2 class="pdp-h2">Sık Sorulan Sorular</h2>
            @if(! empty($productSeo['faq']))
                <div class="pdp-faq">
                    @foreach($productSeo['faq'] as $faq)
                        <details @if($loop->first) open @endif>
                            <summary>
                                <span>{{ $faq['question'] }}</span>
                                <svg class="pdp-faq-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            @endif

            @if(! empty($productSeo['support_links']))
                <div class="pdp-support-links">
                    @foreach($productSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ============ RELATED SERVICES ============ --}}
        <section class="pdp-related" aria-label="İlişkili hizmetler">
            <h2 class="pdp-h2">Bu Ürünle İlişkilendirilen Hizmetler</h2>
            <div class="pdp-related-grid">
                @forelse($relatedServices as $service)
                    <article class="pdp-service-card">
                        @if(! empty($service['image']))
                            <img src="{{ img_url($service['image']) }}" alt="{{ $service['image_alt'] ?? $service['title'] }}" loading="lazy">
                        @else
                            <span class="pdp-service-ph">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/></svg>
                            </span>
                        @endif
                        <div class="pdp-service-body">
                            <span class="pdp-service-kicker">{{ $service['category'] ?? 'Hizmet' }}</span>
                            <h3>{{ $service['title'] }}</h3>
                            <p>{{ $service['summary'] }}</p>
                            <a href="{{ route('services.show', $service['slug']) }}">İncele →</a>
                        </div>
                    </article>
                @empty
                    <p class="pdp-related-empty">Bu ürün için ilişkili hizmet bağlantıları hazırlanıyor. <a href="{{ route('services.index') }}">Tüm kalibrasyon hizmetlerini inceleyin.</a></p>
                @endforelse
            </div>
        </section>

        {{-- ============ BOTTOM BANNER CTA ============ --}}
        <section class="pdp-banner">
            <div>
                <h2>{{ $product['name'] }} için teklif alın</h2>
                <p>{{ $product['brand'] }} {{ $modelFact ? $modelFact . ' ' : '' }}{{ $product['category'] }} grubundaki bu ürün için teknik detay, fiyat ve teslimat bilgisi almak üzere ihtiyaç duyduğunuz adet ve kullanım alanını paylaşın; teknik ekibimiz en uygun çözümle dönsün.</p>
            </div>
            <div class="pdp-banner-actions">
                <a class="pdp-btn pdp-btn--ghost" href="{{ $quoteCta['quote_url'] }}">{{ $productSeo['cta']['button'] ?? 'Ürün İçin Teklif Al' }}</a>
                <a class="pdp-btn pdp-btn--wa" href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener">WhatsApp ile Bilgi Al</a>
            </div>
        </section>
    </div>

    {{-- Lightbox --}}
    <div class="pdp-lightbox" data-pdp-lightbox aria-hidden="true">
        <button type="button" class="pdp-lightbox-close" data-pdp-lightbox-close aria-label="Kapat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <img src="{{ $mainImage ?? '' }}" alt="{{ $imageAlt }}">
    </div>
</div>
@endsection
