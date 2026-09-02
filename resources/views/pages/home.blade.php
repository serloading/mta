@extends('layouts.site')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ================= SECTION 1 · HERO SPLIT ================= --}}
    <section class="relative my-6 grid grid-cols-1 items-center gap-8 overflow-hidden rounded-3xl bg-slate-900 p-8 text-white lg:grid-cols-12 lg:p-12">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-teal-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 left-1/4 h-72 w-72 rounded-full bg-teal-700/10 blur-3xl"></div>

        <div class="relative lg:col-span-7">
            <span class="mb-4 inline-block rounded-full border border-teal-500/30 bg-teal-500/20 px-3 py-1 font-mono text-xs font-bold text-teal-300">
                TÜRKAK AKREDİTE KALİBRASYON &amp; YETKİLİ SERVİS
            </span>
            <h1 class="text-3xl font-extrabold leading-tight lg:text-5xl">
                Endüstriyel Ölçüm Cihazları, Kalibrasyon ve Teknik Servis Çözümleri
            </h1>
            <p class="mt-4 max-w-2xl text-sm text-slate-300 lg:text-base">
                Kalibrasyon, yetkili teknik servis ve marka bazlı laboratuvar cihazı tedariği tek noktada.
                İhtiyacınız olan cihazı, hizmeti veya ölçüm aralığını aşağıdan arayın.
            </p>

            <form action="{{ route('search') }}" method="get" role="search"
                  class="mt-6 flex items-center gap-2 rounded-xl bg-white p-2 shadow-2xl shadow-slate-950/40">
                <svg class="ml-2 h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" name="q" autocomplete="off"
                       class="h-10 w-full min-w-0 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                       placeholder="Cihaz adı, model kodu veya kalibrasyon türü ara...">
                <button type="submit" class="h-10 shrink-0 rounded-lg bg-slate-900 px-4 text-sm font-bold text-white transition hover:bg-slate-800">Ara</button>
            </form>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ $genericQuoteCta['quote_url'] }}"
                   class="inline-flex h-12 items-center justify-center rounded-lg bg-amber-600 px-6 text-sm font-bold text-white shadow-lg shadow-amber-900/30 transition hover:bg-amber-500">
                    Hızlı Teklif İste
                </a>
                <a href="{{ route('products.index') }}"
                   class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-700 px-6 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Ürün Kataloğu
                </a>
            </div>
        </div>

        <div class="relative lg:col-span-5">
            <div class="rounded-2xl border border-slate-700 bg-slate-800/80 p-6 backdrop-blur-md">
                <p class="text-xs font-semibold uppercase tracking-wider text-teal-300">Kurumsal güven</p>
                <p class="mt-1 text-sm text-slate-300">Ölçüm güvenilirliği ve teknik destekte kanıtlanmış deneyim.</p>
                <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                    @foreach([
                        ['15.000+', 'Kalibre Cihaz'],
                        ['500+', 'Kurumsal Referans'],
                        ['%99.8', 'Müşteri Memnuniyeti'],
                    ] as [$value, $label])
                        <div class="rounded-xl bg-slate-900/60 p-3">
                            <div class="text-lg font-extrabold text-white lg:text-xl">{{ $value }}</div>
                            <div class="mt-1 text-[11px] leading-tight text-slate-400">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex items-center gap-2 border-t border-slate-700 pt-4 text-xs text-slate-400">
                    <svg class="h-4 w-4 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    TS EN ISO/IEC 17025 izlenebilir kalibrasyon
                </div>
            </div>
        </div>
    </section>

    {{-- ================= SECTION 2 · SERVICE AREAS ================= --}}
    <section class="py-14 lg:py-20">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl">Ana Hizmet Kategorilerimiz</h2>
            <p class="mt-3 text-sm text-slate-600">
                Akredite kalibrasyon, yetkili teknik servis ve ölçüm ekipmanı desteği — her biri kapsam,
                süreç ve raporlama bilgisiyle ayrı yapılandırılmıştır.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
            @php
                $svcIco = [
                    'gauge' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="m14 12 4-4"/><path d="M4 20a8 8 0 1 1 16 0"/></svg>',
                    'wrench' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/></svg>',
                    'scale' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M6 7h12"/><path d="m6 7-3.2 6.4A3 3 0 0 0 9 13.4z"/><path d="m18 7-3.2 6.4A3 3 0 0 0 21 13.4z"/><path d="M8 21h8"/></svg>',
                ];
                $serviceCards = [
                    [
                        'icon' => $svcIco['gauge'],
                        'title' => 'Basınç &amp; Sıcaklık Kalibrasyonu',
                        'text' => 'Manometre, transmitter, termometre, ısılçift, etüv ve fırınlar için izlenebilir kalibrasyon.',
                        'points' => ['DWT ve blok kalibratör altyapısı', 'Lab &amp; müşteri yerinde ölçüm', 'Sertifikalı raporlama'],
                        'url' => route('services.show', 'basinc-kalibrasyonu'),
                    ],
                    [
                        'icon' => $svcIco['wrench'],
                        'title' => 'Laboratuvar Cihazları Teknik Servisi',
                        'text' => 'Analiz ve ölçüm cihazlarında arıza tespiti, bakım, onarım ve kalibrasyon öncesi hazırlık.',
                        'points' => ['Orijinal yedek parça', 'Prob / sensör / elektrot kontrolü', '48 saat içinde müdahale'],
                        'url' => route('technical-services.index'),
                    ],
                    [
                        'icon' => $svcIco['scale'],
                        'title' => 'Tork &amp; Kütle Kalibrasyonu',
                        'text' => 'Tork anahtarları, torkmetreler, teraziler ve E2–M3 sınıfı kütle standartları.',
                        'points' => ['TÜRKAK akreditasyon kapsamı', 'ISO 6789 / OIML R-111', 'Periyot ve izlenebilirlik takibi'],
                        'url' => route('services.show', 'tork-kalibrasyonu'),
                    ],
                ];
            @endphp

            @foreach($serviceCards as $card)
                <a href="{{ $card['url'] }}"
                   class="group flex cursor-pointer flex-col rounded-2xl border border-slate-200 bg-white p-6 transition-all hover:border-teal-500 hover:shadow-lg">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition group-hover:bg-teal-700 group-hover:text-white">
                        {!! $card['icon'] !!}
                    </span>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">{!! $card['title'] !!}</h3>
                    <p class="mt-2 text-sm text-slate-600">{!! $card['text'] !!}</p>
                    <ul class="mt-4 space-y-2 text-sm text-slate-600">
                        @foreach($card['points'] as $point)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                <span>{!! $point !!}</span>
                            </li>
                        @endforeach
                    </ul>
                    <span class="mt-6 inline-flex items-center gap-1 text-sm font-bold text-teal-600 transition group-hover:gap-2">
                        Hizmet Kapsamını Gör
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= SECTION 3 · FEATURED CATEGORIES ================= --}}
    <section class="py-14 lg:py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl">Öne Çıkan Cihaz Kataloğu</h2>
                <p class="mt-2 text-sm text-slate-600">Marka ve teknik özellik bilgisiyle listelenen laboratuvar cihazı grupları.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-teal-600 hover:gap-2">
                Tüm Ürünleri İncele
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
            @foreach($featuredCategories as $cat)
                <a href="{{ $cat['url'] }}"
                   class="group flex cursor-pointer flex-col items-center rounded-2xl border border-slate-200 bg-white p-4 text-center transition-all hover:border-teal-500 hover:shadow-lg">
                    <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-white p-1.5">
                        @if(! empty($cat['image']))
                            <img src="{{ asset($cat['image']) }}" alt="{{ $cat['name'] }}" class="h-auto max-h-full w-auto max-w-full object-contain" loading="lazy">
                        @else
                            <svg class="h-9 w-9 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21 8-9-5-9 5v8l9 5 9-5z"/><path d="m3.3 7.5 8.7 5 8.7-5"/><path d="M12 12.5V22"/></svg>
                        @endif
                    </div>
                    <span class="mt-3 text-xs font-semibold text-slate-800">{{ $cat['name'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= SECTION 4 · BRAND RIBBON ================= --}}
    @if(! empty($partnerBrands))
        <section class="py-10">
            <h2 class="text-center text-sm font-semibold uppercase tracking-wider text-slate-500">
                Tedarikçi ve Servis Ortağı Olduğumuz Markalar
            </h2>
            <div class="mt-6 flex flex-wrap items-center justify-between gap-x-8 gap-y-6 border-y border-slate-200/60 py-6">
                @foreach($partnerBrands as $brand)
                    <a href="{{ $brand['url'] }}"
                       class="group flex flex-col items-center gap-1.5 {{ empty($brand['authorized']) ? 'opacity-80 grayscale transition-all hover:opacity-100 hover:grayscale-0' : 'transition-all' }}">
                        <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" class="h-8 w-auto object-contain lg:h-10" loading="lazy">
                        @if(! empty($brand['authorized']))
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">
                                <svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                {{ $brand['role'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ================= SECTION 5 · WHY MTA ================= --}}
    <section class="my-6 rounded-3xl bg-slate-50 py-16 ring-1 ring-slate-200/70">
        <div class="mx-auto max-w-2xl px-6 text-center">
            <h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl">Neden MTA Endüstri?</h2>
            <p class="mt-3 text-sm text-slate-600">Ölçüm güvenilirliğini, hızlı servisi ve akredite süreçleri tek çatı altında topluyoruz.</p>
        </div>
        <div class="mx-auto mt-10 grid max-w-[1200px] grid-cols-1 gap-6 px-6 md:grid-cols-4">
            @php
                $benefits = [
                    ['t' => 'TÜRKAK Akredite', 'd' => 'ISO 17025 standartlarında izlenebilir kalibrasyon ve sertifikalı raporlama.',
                     'i' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>'],
                    ['t' => 'Hızlı Servis', 'd' => '48 saat içinde yerinde veya laboratuvar bünyesinde teknik müdahale.',
                     'i' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>'],
                    ['t' => 'Orijinal Yedek Parça', 'd' => 'Garanti kapsamı altında değişim, onarım ve uzun ömürlü çözümler.',
                     'i' => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.7 2.7-2-2 2.7-2.7z"/>'],
                    ['t' => 'Uzman Kadro', 'd' => 'Sertifikalı mühendis ve teknik ekip ile güvenilir ölçüm desteği.',
                     'i' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                ];
            @endphp
            @foreach($benefits as $b)
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-teal-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $b['i'] !!}</svg>
                    </span>
                    <h3 class="mt-4 text-base font-bold text-slate-900">{{ $b['t'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $b['d'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= SECTION 6 · FAQ + BLOG ================= --}}
    <section class="py-14 lg:py-20">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl">Kalibrasyon ve Servis Süreçleri SSS</h2>
                <div class="mt-6 space-y-3">
                    @foreach(collect($faqs)->take(4) as $faq)
                        <details class="group rounded-xl border border-slate-200 bg-white p-4">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-slate-900">
                                {{ $faq['question'] }}
                                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </summary>
                            <p class="mt-3 text-sm text-slate-600">{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl">Sektörel Blog &amp; Rehberler</h2>
                <div class="mt-6 space-y-4">
                    @foreach(collect($articles)->take(2) as $article)
                        <a href="{{ route('knowledge.show', $article['slug']) }}"
                           class="group flex cursor-pointer gap-4 overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-teal-500 hover:shadow-lg">
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
                                @if(! empty($article['image']))
                                    <img src="{{ asset($article['image']) }}" alt="{{ $article['title'] }}" class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wide text-teal-600">{{ $article['category'] }}</span>
                                <h3 class="mt-1 text-sm font-bold text-slate-900">{{ $article['title'] }}</h3>
                                <p class="mt-1 text-xs text-slate-600">{{ Str::limit($article['excerpt'], 110) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ================= SECTION 7 · BOTTOM LEAD BANNER ================= --}}
    <section class="my-10 flex flex-col items-center justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">Özel Tesisiniz İçin Toplu Kalibrasyon Teklifi Alın</h2>
            <p class="mt-1 text-sm text-slate-300">Cihaz listenizi yükleyin, 24 saat içinde fiyatlandırıp iletelim.</p>
        </div>
        <a href="{{ $genericQuoteCta['quote_url'] }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            Toplu Teklif Talebi Oluştur
        </a>
    </section>

</div>
</div>
@endsection
