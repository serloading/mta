<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $meta['title'] ?? config('mta.site.name') }}</title>
    <meta name="description" content="{{ $meta['description'] ?? config('mta.site.description') }}">
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['og_title'] ?? $meta['title'] ?? config('mta.site.name') }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? $meta['description'] ?? config('mta.site.description') }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? asset('mta-logo.png') }}">
    <meta property="og:url" content="{{ $meta['canonical'] ?? url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="robots" content="{{ $meta['robots'] ?? 'index,follow' }}">
    {!! \App\Support\SiteSettings::tracking('verification_meta') !!}
    {!! \App\Support\SiteSettings::tracking('head_scripts') !!}
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @isset($schema)
        <script type="application/ld+json">@json($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    @endisset
</head>
<body>
{!! \App\Support\SiteSettings::tracking('body_start_scripts') !!}
<a class="skip-link" href="#icerik">İçeriğe geç</a>

@php
    use Illuminate\Support\Str;
    $ct_phone = config('mta.site.phone');
    $ct_phone_raw = preg_replace('/\D+/', '', $ct_phone);
    $ct_email = config('mta.site.email');
@endphp
<header class="site-header" data-header>
    <div class="top-bar" data-topbar>
        <div class="container top-bar-inner">
            <div class="tb-left">
                @forelse($topbarAuthorizedServices ?? [] as $tbAuth)
                    <a class="tb-badge" href="{{ $tbAuth['url'] }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/></svg>
                        {{ $tbAuth['short'] }}
                    </a>
                @empty
                    <span class="tb-tagline">Akredite Kalibrasyon &amp; Teknik Servis</span>
                @endforelse
            </div>
            <div class="tb-right">
                <div class="tb-contact">
                    <a href="tel:{{ $ct_phone_raw }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/></svg>
                        {{ $ct_phone }}
                    </a>
                    <a href="mailto:{{ $ct_email }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        {{ $ct_email }}
                    </a>
                </div>
                <span class="tb-sep" aria-hidden="true"></span>
                <div class="tb-social" aria-label="Sosyal medya">
                    @foreach(\App\Support\SiteSettings::socialLinks() as $social)
                        <a href="{{ $social['url'] }}" aria-label="{{ $social['name'] }}" target="_blank" rel="noopener">
                            @switch($social['name'])
                                @case('LinkedIn')
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.3 8.8h3.2v10H5.3v-10Zm1.6-4.9a1.8 1.8 0 1 1 0 3.6 1.8 1.8 0 0 1 0-3.6Zm4.1 4.9h3.1v1.4c.4-.8 1.5-1.7 3.1-1.7 3.3 0 3.9 2.2 3.9 5v5.3h-3.2v-4.7c0-1.1 0-2.6-1.6-2.6s-1.9 1.2-1.9 2.5v4.8H11v-10Z"/></svg>
                                    @break
                                @case('Instagram')
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 3.8h9A3.7 3.7 0 0 1 20.2 7.5v9a3.7 3.7 0 0 1-3.7 3.7h-9a3.7 3.7 0 0 1-3.7-3.7v-9a3.7 3.7 0 0 1 3.7-3.7Zm0 2A1.7 1.7 0 0 0 5.8 7.5v9a1.7 1.7 0 0 0 1.7 1.7h9a1.7 1.7 0 0 0 1.7-1.7v-9a1.7 1.7 0 0 0-1.7-1.7h-9Zm4.5 3.1a3.1 3.1 0 1 1 0 6.2 3.1 3.1 0 0 1 0-6.2Zm0 2a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2Zm4.2-2.5a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6Z"/></svg>
                                    @break
                                @case('Facebook')
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.7 20.3v-7.5h2.5l.4-2.9h-2.9V8.1c0-.8.2-1.4 1.4-1.4h1.6V4.1c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v1.9H8v2.9h2.5v7.5h3.2Z"/></svg>
                                    @break
                                @case('YouTube')
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8.2a3 3 0 0 0-.5-1.3 2 2 0 0 0-1.4-.6C17.2 6.1 12 6.1 12 6.1s-5.2 0-7.1.2a2 2 0 0 0-1.4.6A3 3 0 0 0 3 8.2 31 31 0 0 0 2.8 12c0 1.3.1 2.6.2 3.8a3 3 0 0 0 .5 1.3 2.3 2.3 0 0 0 1.5.6c1.1.1 7 .2 7 .2s5.2 0 7.1-.2a2 2 0 0 0 1.4-.6 3 3 0 0 0 .5-1.3c.1-1.2.2-2.5.2-3.8s-.1-2.6-.2-3.8ZM10.2 14.6V9.9l4.7 2.4-4.7 2.3Z"/></svg>
                                    @break
                            @endswitch
                            <span class="sr-only">{{ $social['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="main-bar">
        <div class="container main-bar-inner">
            <a class="mb-logo" href="{{ route('home') }}" aria-label="MTA Endüstri ana sayfa">
                <img src="{{ asset('mta-logo.png') }}" width="74" height="58" alt="MTA Endüstri logosu">
                <span class="mb-logo-text"><strong>MTA Endüstri</strong><small>Akredite Kalibrasyon Hizmeti</small></span>
            </a>

            @php
                $mi = [
                    'gauge'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="1.6"/><path d="m13.4 11.6 4-3.6"/><path d="M4 20a8 8 0 1 1 16 0"/></svg>',
                    'thermo'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.8V5a2 2 0 0 0-4 0v9.8a4 4 0 1 0 4 0z"/></svg>',
                    'wrench'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/></svg>',
                    'rotate'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></svg>',
                    'scale'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M6 7h12"/><path d="m6 7-3.2 6.4A3 3 0 0 0 9 13.4z"/><path d="m18 7-3.2 6.4A3 3 0 0 0 21 13.4z"/><path d="M8 21h8"/></svg>',
                    'flask'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6"/><path d="M10 3v6l-4.5 8.1A2 2 0 0 0 7.3 20h9.4a2 2 0 0 0 1.8-2.9L14 9V3"/><path d="M7.5 14h9"/></svg>',
                    'droplet' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-3-3-7-7-11-4 4-7 8-7 11a7 7 0 0 0 7 7z"/></svg>',
                    'wave'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12c2 0 3-4 5-4s3 8 5 8 3-8 5-8 3 4 5 4"/></svg>',
                    'zap'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h8l-1 8 11-13h-8z"/></svg>',
                    'oven'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M7 6h.01M11 6h.01"/></svg>',
                    'box'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m21 8-9-5-9 5v8l9 5 9-5z"/><path d="m3.3 7.5 8.7 5 8.7-5"/><path d="M12 12.5V22"/></svg>',
                    'chev'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>',
                    'cdown'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>',
                ];
                $megaIcon = function (string $text) use ($mi) {
                    $t = Str::lower($text);
                    return match (true) {
                        Str::contains($t, ['basınç', 'basinc', 'manometre']) => $mi['gauge'],
                        Str::contains($t, ['sıcaklık', 'sicaklik', 'etüv', 'etuv', 'banyo', 'termoreakt', 'balon', 'inkübat', 'inkubat']) => $mi['thermo'],
                        Str::contains($t, ['tork', 'servis', 'onarım', 'onarim', 'bakım', 'bakim']) => $mi['wrench'],
                        Str::contains($t, ['devir', 'karıştır', 'karistir', 'santrifüj', 'santrifuj', 'rotator', 'çalkala', 'calkala', 'homojen']) => $mi['rotate'],
                        Str::contains($t, ['kütle', 'kutle', 'terazi', 'tartım', 'tartim']) => $mi['scale'],
                        Str::contains($t, ['hacim', 'titr', 'fischer', 'büret', 'buret', 'refrakto', 'jar test']) => $mi['flask'],
                        Str::contains($t, ['nem', 'densito', 'oksijen', 'iletkenlik']) || $t === 'ph metre' || Str::startsWith($t, 'ph ') => $mi['droplet'],
                        Str::contains($t, ['visko', 'tekstür', 'tekstur', 'polari']) => $mi['wave'],
                        Str::contains($t, ['elektrod', 'elektrot', 'kablo']) => $mi['zap'],
                        default => $mi['box'],
                    };
                };
                $megaProductCats = collect(config('mta.product_categories'))
                    ->reject(fn ($c) => in_array($c['slug'] ?? null, ['ph-metre', 'termal-analiz'], true));
                $megaRootCats = $megaProductCats->filter(fn ($c) => empty($c['parent_slug']))->values();
                $megaChildrenBy = $megaProductCats->filter(fn ($c) => ! empty($c['parent_slug']))->groupBy('parent_slug');
                $megaBrandBySlug = collect(config('mta.product_brands'))->keyBy('slug');
                $megaCatBrandSlugs = config('mta.product_category_brands', []);
                $megaBrandChips = function (string $slug) use ($megaBrandBySlug, $megaCatBrandSlugs) {
                    return collect($megaCatBrandSlugs[$slug] ?? [])
                        ->map(fn ($s) => $megaBrandBySlug->get($s))
                        ->filter()
                        ->take(6)
                        ->values();
                };
                // Ürünler mega — alt şeritteki marka logoları (diskte .png olanlar)
                $megaBrandLogos = collect(config('mta.product_brands'))
                    ->map(fn ($b) => $b + ['logo' => 'images/brands/' . ($b['slug'] ?? '') . '.png'])
                    ->filter(fn ($b) => is_file(public_path($b['logo'])))
                    ->values();
            @endphp

            <nav class="mb-nav desktop-nav" aria-label="Ana navigasyon">
                <div class="mega-nav-item">
                    <a class="mega-trigger" href="{{ route('services.index') }}" aria-haspopup="true" aria-expanded="false" data-mega-trigger>Kalibrasyon {!! $mi['cdown'] !!}</a>
                    <div class="mega-menu mega-menu--svc">
                        <div class="mega-panel">
                            <div class="mega-grid mega-grid--svc">
                                <aside class="mega-hero">
                                    <div>
                                        <img src="{{ asset('images/services/mta-kalibrasyon-banner-10.webp') }}" alt="Akredite kalibrasyon laboratuvarı" class="mb-4 h-32 w-full rounded-lg object-cover" loading="lazy">
                                        <span class="mega-hero-badge">KALİBRASYON</span>
                                        <h3 class="mega-hero-title">İzlenebilir Kalibrasyon Güvencesi</h3>
                                        <p class="mt-1 text-xs text-slate-400">TÜRKAK akreditasyon kapsamında, izlenebilir referanslarla sertifikalı ölçüm.</p>
                                    </div>
                                    <a class="mega-hero-btn" href="{{ route('quote', ['source_type' => 'service', 'source_name' => 'Kalibrasyon hizmeti']) }}">Teklif Al</a>
                                </aside>
                                <div class="mega-svc-list">
                                    @foreach(config('mta.services') as $service)
                                        <a class="mega-svc-item" href="{{ route('services.show', $service['slug']) }}">
                                            <span class="mega-svc-ico">{!! $megaIcon($service['title']) !!}</span>
                                            <span class="mega-svc-body">
                                                <strong>{{ $service['title'] }}</strong>
                                                <span>{{ Str::limit($service['summary'], 150) }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mega-nav-item">
                    <a class="mega-trigger" href="{{ route('technical-services.index') }}" aria-haspopup="true" aria-expanded="false" data-mega-trigger>Teknik Servis {!! $mi['cdown'] !!}</a>
                    <div class="mega-menu mega-menu--svc">
                        <div class="mega-panel">
                            <div class="mega-grid mega-grid--svc">
                                <aside class="mega-hero">
                                    <div>
                                        <img src="{{ asset('images/technical-service/laboratuvar-cihazlari-teknik-servis.webp') }}" alt="Laboratuvar cihazları teknik servis" class="mb-4 h-32 w-full rounded-lg object-cover" loading="lazy">
                                        <span class="mega-hero-badge">BAKIM &amp; ONARIM</span>
                                        <h3 class="mega-hero-title">Yetkili Servis &amp; Teknik Destek</h3>
                                        <p class="mt-1 text-xs text-slate-400">Arıza tespiti, bakım, onarım ve orijinal yedek parça ile 48 saatte müdahale.</p>
                                    </div>
                                    <a class="mega-hero-btn" href="{{ route('quote', ['source_type' => 'technical_service', 'source_name' => 'Teknik servis talebi']) }}">Servis Talebi Oluştur</a>
                                </aside>
                                <div class="mega-svc-list">
                                    @foreach(config('mta.technical_services') as $item)
                                        <a class="mega-svc-item" href="{{ route('technical-services.show', $item['slug']) }}">
                                            <span class="mega-svc-ico">{!! $megaIcon($item['title']) !!}</span>
                                            <span class="mega-svc-body">
                                                <strong>{{ $item['title'] }}</strong>
                                                <span>{{ Str::limit($item['summary'], 150) }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mega-nav-item">
                    <a class="mega-trigger" href="{{ route('products.index') }}" aria-haspopup="true" aria-expanded="false" data-mega-trigger>Ürünler {!! $mi['cdown'] !!}</a>
                    <div class="mega-menu mega-menu--products !border-t-0 !bg-transparent !shadow-none" data-product-mega>
                        <div class="mx-auto mt-1 w-full max-w-[1320px] overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">
                            {{-- Kategoriler: 5 sütun x 3 sıra --}}
                            <div class="p-5">
                                <div class="mb-3 flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-sm font-bold text-slate-900">Ürün Kategorileri</span>
                                    <a href="{{ route('products.index') }}" class="text-xs font-medium text-teal-600 hover:underline">Tüm Kataloğu Gör →</a>
                                </div>
                                <div class="grid grid-cols-5 gap-2">
                                    @foreach($megaRootCats as $cat)
                                        <a href="{{ route('products.category', $cat['slug']) }}"
                                           class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:border-teal-600 hover:bg-teal-50 hover:text-teal-700">
                                            <span class="shrink-0 text-slate-400 [&>svg]:h-4 [&>svg]:w-4">{!! $megaIcon($cat['name']) !!}</span>
                                            <span class="truncate">{{ $cat['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ALT ŞERİT: marka logoları --}}
                            @if($megaBrandLogos->isNotEmpty())
                                <div class="flex items-center gap-5 border-t border-slate-200 bg-slate-50 px-5 py-3">
                                    <span class="shrink-0 text-[11px] font-bold uppercase tracking-wide text-slate-400">Markalar</span>
                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                        @foreach($megaBrandLogos as $b)
                                            <a href="{{ route('products.brand', $b['slug']) }}" aria-label="{{ $b['name'] }}"
                                               class="opacity-60 grayscale transition hover:opacity-100 hover:grayscale-0">
                                                <img src="{{ asset($b['logo']) }}" alt="{{ $b['name'] }}" class="h-6 w-auto max-w-[92px] object-contain" loading="lazy">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <a class="mb-nav-link" href="{{ route('scope') }}">Kapsam</a>

                <div class="mega-nav-item nav-has-dropdown">
                    <a class="mega-trigger" href="{{ route('about') }}" aria-haspopup="true" aria-expanded="false" data-mega-trigger>Kurumsal {!! $mi['cdown'] !!}</a>
                    <div class="nav-dropdown">
                        <a href="{{ route('about') }}">Hakkımızda</a>
                        <a href="{{ route('certificates') }}">Sertifikalar</a>
                        <a href="{{ route('blog.index') }}">Blog</a>
                        <a href="{{ route('contact') }}">İletişim</a>
                    </div>
                </div>
            </nav>

            <form class="mb-search" action="{{ route('search') }}" method="get" role="search" data-search>
                <svg class="mb-search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" name="q" placeholder="Cihaz, model kodu veya kategori ara…" autocomplete="off" aria-label="Katalogda ara" data-search-input>
                <kbd class="mb-search-kbd" aria-hidden="true">Ctrl K</kbd>
                <div class="mb-search-panel" data-search-panel role="listbox" hidden></div>
            </form>

            <div class="mb-actions">
                <a class="mb-icon-btn" href="{{ route('quote') }}" aria-label="Hızlı iletişim">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.8 4.9-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-2.9.8.8-2.8-.2-.3A8 8 0 1 1 12 20zm4.6-6c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.5 6.5 0 0 1-3.2-2.8c-.2-.4.2-.4.6-1.2a.4.4 0 0 0 0-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3A2.8 2.8 0 0 0 6.7 10a4.9 4.9 0 0 0 1 2.6 11.2 11.2 0 0 0 4.3 3.8c2 .8 2 .5 2.4.5a2.5 2.5 0 0 0 1.6-1.2 2 2 0 0 0 .2-1.2c-.1-.1-.3-.2-.5-.3z"/></svg>
                </a>
                <a class="mb-cta" href="{{ route('quote') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    Teklif Al
                </a>
                <button type="button" class="mb-search-toggle" data-search-toggle aria-label="Aramayı aç">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
                <button type="button" class="mb-burger" data-burger aria-label="Menü" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="mobile-drawer" data-mobile-drawer hidden>
        <div class="container mobile-drawer-inner">
            <a href="{{ route('services.index') }}">Kalibrasyon</a>
            @foreach(config('mta.services') as $service)
                <a class="md-sub" href="{{ route('services.show', $service['slug']) }}">{{ $service['title'] }}</a>
            @endforeach
            <a href="{{ route('technical-services.index') }}">Teknik Servis</a>
            @foreach(config('mta.technical_services') as $item)
                <a class="md-sub" href="{{ route('technical-services.show', $item['slug']) }}">{{ $item['title'] }}</a>
            @endforeach
            <a href="{{ route('products.index') }}">Ürünler</a>
            @foreach($megaRootCats->take(8) as $cat)
                <a class="md-sub" href="{{ route('products.category', $cat['slug']) }}">{{ $cat['name'] }}</a>
            @endforeach
            <a href="{{ route('scope') }}">Kapsam</a>
            <a href="{{ route('about') }}">Kurumsal</a>
            <a class="md-sub" href="{{ route('about') }}">Hakkımızda</a>
            <a class="md-sub" href="{{ route('certificates') }}">Sertifikalar</a>
            <a class="md-sub" href="{{ route('blog.index') }}">Blog</a>
            <a class="md-sub" href="{{ route('contact') }}">İletişim</a>
        </div>
    </div>
</header>

<main id="icerik">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container ft-grid">
        <div class="ft-brand">
            <img class="ft-logo" src="{{ asset('mta-logo.png') }}" width="64" height="50" alt="MTA Endüstri">
            <p class="ft-slogan">Endüstriyel kalibrasyon hizmetleri ve teknik cihaz çözümlerinde akredite güven.</p>
            <div class="ft-badges" aria-label="Akreditasyon">
                <span class="ft-badge">TÜRKAK</span>
                <span class="ft-badge">ISO 17025</span>
            </div>
            <div class="ft-social" aria-label="Sosyal medya">
                @foreach(\App\Support\SiteSettings::socialLinks() as $social)
                    <a href="{{ $social['url'] }}" aria-label="{{ $social['name'] }}" target="_blank" rel="noopener">
                        @switch($social['name'])
                            @case('LinkedIn')
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.3 8.8h3.2v10H5.3v-10Zm1.6-4.9a1.8 1.8 0 1 1 0 3.6 1.8 1.8 0 0 1 0-3.6Zm4.1 4.9h3.1v1.4c.4-.8 1.5-1.7 3.1-1.7 3.3 0 3.9 2.2 3.9 5v5.3h-3.2v-4.7c0-1.1 0-2.6-1.6-2.6s-1.9 1.2-1.9 2.5v4.8H11v-10Z"/></svg>
                                @break
                            @case('Instagram')
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 3.8h9A3.7 3.7 0 0 1 20.2 7.5v9a3.7 3.7 0 0 1-3.7 3.7h-9a3.7 3.7 0 0 1-3.7-3.7v-9a3.7 3.7 0 0 1 3.7-3.7Zm0 2A1.7 1.7 0 0 0 5.8 7.5v9a1.7 1.7 0 0 0 1.7 1.7h9a1.7 1.7 0 0 0 1.7-1.7v-9a1.7 1.7 0 0 0-1.7-1.7h-9Zm4.5 3.1a3.1 3.1 0 1 1 0 6.2 3.1 3.1 0 0 1 0-6.2Zm0 2a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2Zm4.2-2.5a.8.8 0 1 1 0-1.6.8.8 0 0 1 0 1.6Z"/></svg>
                                @break
                            @case('Facebook')
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.7 20.3v-7.5h2.5l.4-2.9h-2.9V8.1c0-.8.2-1.4 1.4-1.4h1.6V4.1c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v1.9H8v2.9h2.5v7.5h3.2Z"/></svg>
                                @break
                            @case('YouTube')
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8.2a3 3 0 0 0-.5-1.3 2 2 0 0 0-1.4-.6C17.2 6.1 12 6.1 12 6.1s-5.2 0-7.1.2a2 2 0 0 0-1.4.6A3 3 0 0 0 3 8.2 31 31 0 0 0 2.8 12c0 1.3.1 2.6.2 3.8a3 3 0 0 0 .5 1.3 2.3 2.3 0 0 0 1.5.6c1.1.1 7 .2 7 .2s5.2 0 7.1-.2a2 2 0 0 0 1.4-.6 3 3 0 0 0 .5-1.3c.1-1.2.2-2.5.2-3.8s-.1-2.6-.2-3.8ZM10.2 14.6V9.9l4.7 2.4-4.7 2.3Z"/></svg>
                                @break
                        @endswitch
                        <span class="sr-only">{{ $social['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="ft-col">
            <h3>Hizmetler</h3>
            <ul>
                @foreach(config('mta.services') as $service)
                    <li><a href="{{ route('services.show', $service['slug']) }}">{{ $service['title'] }}</a></li>
                @endforeach
                <li><a href="{{ route('scope') }}">Kalibrasyon Kapsamı</a></li>
            </ul>
        </div>

        <div class="ft-col">
            <h3>Kategoriler</h3>
            <ul>
                <li><a href="{{ route('products.category', 'teraziler') }}">Teraziler &amp; Analiz Cihazları</a></li>
                <li><a href="{{ route('products.category', 'nem-tayin') }}">Nem Tayin Cihazları</a></li>
                <li><a href="{{ route('products.category', 'viskozimetre') }}">Viskozimetre &amp; Karıştırıcılar</a></li>
                <li><a href="{{ route('products.category', 'titratorler') }}">Titratörler &amp; pH Metreler</a></li>
                <li><a href="{{ route('brands.index') }}">Tüm Markalar</a></li>
            </ul>
        </div>

        <div class="ft-col">
            <h3>Teknik Servis</h3>
            <ul>
                @foreach(config('mta.technical_services') as $item)
                    <li><a href="{{ route('technical-services.show', $item['slug']) }}">{{ $item['title'] }}</a></li>
                @endforeach
                <li><a href="{{ route('knowledge.index') }}">Sıkça Sorulan Sorular</a></li>
            </ul>
        </div>

        <div class="ft-col ft-contact">
            <h3>İletişim</h3>
            <ul>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>{{ config('mta.site.address') }}</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.7 2z"/></svg>
                    <a href="tel:{{ preg_replace('/\D+/', '', config('mta.site.phone')) }}">{{ config('mta.site.phone') }}</a>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <a href="mailto:{{ config('mta.site.email') }}">{{ config('mta.site.email') }}</a>
                </li>
            </ul>
            <a class="ft-cta" href="{{ route('quote') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                Teklif Formuna Git
            </a>
        </div>
    </div>

    <div class="ft-bottom">
        <div class="container ft-bottom-shell">
            <span>© {{ date('Y') }} MTA Endüstri. Tüm hakları saklıdır.</span>
            <nav class="ft-legal" aria-label="Yasal">
                <a href="{{ route('legal.kvkk') }}">KVKK Aydınlatma Metni</a>
                <a href="{{ route('legal.privacy') }}">Gizlilik Politikası</a>
                <a href="{{ route('legal.cookies') }}">Çerez Politikası</a>
            </nav>
        </div>
    </div>
</footer>
{!! \App\Support\SiteSettings::tracking('body_end_scripts') !!}
</body>
</html>
