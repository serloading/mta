@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Referanslar</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">{{ $pageSeo['eyebrow'] }}</p>
        <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $pageSeo['h1'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $pageSeo['hero_text'] }}</p>
        <div class="mt-6">
            <a href="{{ route('contact') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">{{ $pageSeo['primary_cta'] }}</a>
        </div>
    </section>

    {{-- ============ SEO METİNLERİ ============ --}}
    @if(! empty($pageSeo['sections']))
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($pageSeo['sections'] as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ SEKTÖRLER ============ --}}
    @if(! empty($pageSeo['sectors']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Sektörler</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Hizmet verilen sektörler ve uygulama alanları</h2>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600">Bu bölüm, hizmet verilen sektörleri ve teknik uygulama alanlarını anlatır.</p>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($pageSeo['sectors'] as $sector)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6">
                        <span class="font-mono text-sm font-bold text-teal-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-2 text-base font-bold text-slate-900">{{ $sector['name'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $sector['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ BAĞLANTILI SAYFALAR ============ --}}
    @if(! empty($pageSeo['support_links']))
        <section class="my-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Bağlantılı Sayfalar</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">Hizmet ve ürün yapısını inceleyin</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">Sektör ihtiyaçları ürün kataloğu, teknik servis ve kalibrasyon hizmetleriyle birlikte değerlendirilir.</p>
            </div>
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-5">
                <div class="space-y-2">
                    @foreach($pageSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}" class="block text-sm font-medium text-teal-700 hover:underline">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            </aside>
        </section>
    @endif

    {{-- ============ SSS ============ --}}
    @if(! empty($pageSeo['faq']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">SSS</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Sık sorulan sorular</h2>
            </div>
            <div class="mx-auto max-w-3xl space-y-3">
                @foreach($pageSeo['faq'] as $faq)
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
            <h2 class="text-2xl font-bold">{{ $pageSeo['cta']['title'] }}</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $pageSeo['cta']['text'] }}</p>
        </div>
        <a href="{{ route('contact') }}" class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">{{ $pageSeo['cta']['button'] }}</a>
    </section>

</div>
</div>
@endsection
