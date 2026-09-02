@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="relative my-6 overflow-hidden rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-teal-500/10 blur-3xl"></div>
        <div class="relative grid grid-cols-1 items-center gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
                    <span>Kalibrasyon Hizmetleri</span>
                </nav>
                <span class="mb-3 inline-block rounded-full border border-teal-500/30 bg-teal-500/20 px-3 py-1 font-mono text-xs font-bold text-teal-300">TÜRKAK AKREDİTE / ISO 17025 İZLENEBİLİR</span>
                <h1 class="text-3xl font-extrabold leading-tight lg:text-[2.6rem]">{{ $servicesSeo['h1'] }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300">{{ $servicesSeo['hero_text'] }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ $genericQuoteCta['quote_url'] }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-amber-600 px-6 text-sm font-bold text-white shadow-lg transition hover:bg-amber-500">{{ $servicesSeo['primary_cta'] }}</a>
                    <a href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener" class="inline-flex h-11 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">WhatsApp ile Teklif Al</a>
                    <a href="#hizmet-alanlari" class="inline-flex h-11 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">{{ $servicesSeo['secondary_cta'] }}</a>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-2">
                    <div class="flex h-44 items-center justify-center overflow-hidden rounded-xl bg-white p-4">
                        <img src="{{ img_url($servicesSeo['image']) }}" alt="{{ $servicesSeo['image_alt'] }}" class="max-h-full w-auto max-w-full object-contain">
                    </div>
                    <div class="px-3 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-teal-300">Kapsam</p>
                        <p class="mt-1 text-sm text-slate-300">Basınç, sıcaklık, tork, devir, kütle-terazi, hacim</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SEO GİRİŞ ============ --}}
    @if(! empty($servicesSeo['sections']))
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($servicesSeo['sections'] as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ HİZMET KARTLARI ============ --}}
    <section class="my-12" id="hizmet-alanlari">
        <div class="mb-6 text-center">
            <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Hizmet Alanları</p>
            <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Kalibrasyon Hizmet Alanları</h2>
            <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600">Her hizmet sayfası cihaz kapsamı, ölçüm aralığı, süreç ve teklif yönlendirmesiyle ayrı yapılandırılmıştır.</p>
        </div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $service)
                @php($cardSeo = $servicesSeo['service_cards'][$service['slug']] ?? null)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-lg">
                    @if(! empty($service['image']))
                        <div class="flex h-40 items-center justify-center overflow-hidden border-b border-slate-100 bg-white p-5">
                            <img src="{{ img_url($service['image']) }}" alt="{{ $service['image_alt'] ?? $service['title'] }}" class="max-h-full w-auto max-w-full object-contain" loading="lazy">
                        </div>
                    @endif
                    <div class="flex flex-1 flex-col p-5">
                        <span class="text-xs font-bold uppercase tracking-wide text-teal-700">{{ $service['category'] }}</span>
                        <h3 class="mt-1 text-base font-bold leading-snug text-slate-900 group-hover:text-teal-700">{{ $service['title'] }}</h3>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $cardSeo['summary'] ?? $service['summary'] }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-bold">
                            <a href="{{ route('services.show', $service['slug']) }}" class="text-teal-700 hover:underline">{{ $cardSeo['anchor'] ?? 'İncele' }}</a>
                            <a href="{{ $quoteCtas[$service['slug']]['quote_url'] }}" class="text-amber-700 hover:underline">Teklif Al</a>
                            <a href="{{ $quoteCtas[$service['slug']]['whatsapp_url'] }}" target="_blank" rel="noopener" class="text-slate-500 hover:underline">WhatsApp</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- ============ CİHAZ KAPSAMI ============ --}}
    @if(! empty($servicesSeo['device_section']))
        <section class="my-12 grid grid-cols-1 gap-8 rounded-3xl border border-slate-200 bg-white p-8 lg:grid-cols-2 lg:p-10">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Cihaz Kapsamı</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">{{ $servicesSeo['device_section']['title'] }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $servicesSeo['device_section']['text'] }}</p>
            </div>
            <ul class="grid grid-cols-1 gap-2.5 text-sm text-slate-700 sm:grid-cols-2 sm:content-start">
                @foreach($servicesSeo['device_section']['items'] as $item)
                    <li class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ============ SÜREÇ ============ --}}
    @if(! empty($servicesSeo['process']['steps']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Süreç</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $servicesSeo['process']['title'] }}</h2>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600">{{ $servicesSeo['process']['text'] }}</p>
            </div>
            <div class="relative grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                <div class="pointer-events-none absolute left-0 right-0 top-8 hidden h-px bg-slate-200 lg:block"></div>
                @foreach($servicesSeo['process']['steps'] as $step)
                    <div class="relative rounded-xl border border-slate-200 bg-white p-4 text-center transition hover:border-teal-500">
                        <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-teal-600 text-xs font-extrabold text-white">{{ $loop->iteration }}</span>
                        <p class="mt-3 text-sm font-semibold text-slate-900">{{ $step }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ BAĞLANTILI SÜREÇLER ============ --}}
    @if(! empty($servicesSeo['support_section']))
        <section class="my-12 grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Bağlantılı Süreçler</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">{{ $servicesSeo['support_section']['title'] }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $servicesSeo['support_section']['text'] }}</p>
            </div>
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-5">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Teknik servis ve katalog</p>
                <div class="mt-3 space-y-2">
                    @foreach($servicesSeo['support_section']['links'] as $link)
                        <a href="{{ $link['url'] }}" class="block text-sm font-medium text-teal-700 hover:underline">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            </aside>
        </section>
    @endif

    {{-- ============ SSS ============ --}}
    @if(! empty($servicesSeo['faq']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">SSS</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Sık sorulan sorular</h2>
            </div>
            <div class="mx-auto max-w-3xl space-y-3">
                @foreach($servicesSeo['faq'] as $faq)
                    <details class="group rounded-2xl border border-slate-200 bg-white p-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-slate-900">
                            {{ $faq['question'] }}
                            <svg class="h-4 w-4 shrink-0 text-slate-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-start justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row md:items-center lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">{{ $servicesSeo['cta']['title'] }}</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $servicesSeo['cta']['text'] }}</p>
            @if(! empty($servicesSeo['cta']['note']))
                <p class="mt-1 max-w-xl text-xs text-slate-400">{{ $servicesSeo['cta']['note'] }}</p>
            @endif
        </div>
        <div class="flex shrink-0 flex-wrap gap-3">
            <a href="{{ $genericQuoteCta['quote_url'] }}" class="inline-flex h-12 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">{{ $servicesSeo['cta']['button'] }}</a>
            <a href="{{ route('technical-services.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">Teknik Servis Hizmetleri</a>
        </div>
    </section>

</div>
</div>
@endsection
