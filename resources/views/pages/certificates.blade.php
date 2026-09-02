@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Sertifikalar</span>
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

    {{-- ============ BELGE TÜRLERİ ============ --}}
    @if(! empty($pageSeo['document_types']))
        <section class="my-12 grid grid-cols-1 gap-8 rounded-3xl border border-slate-200 bg-white p-8 lg:grid-cols-12 lg:p-10">
            <div class="lg:col-span-4">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Belge Türleri</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">Yayınlanabilecek kurumsal belgeler</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">Gerçek belge dosyaları geldiğinde bu alan belge adı, açıklama, kapsam ve dosya bağlantısıyla doldurulacak.</p>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:col-span-8">
                @foreach($pageSeo['document_types'] as $item)
                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $item }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">Dosya ve açıklama alanı yayın onayı sonrası eklenecek.</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ BAĞLANTILI SAYFALAR ============ --}}
    @if(! empty($pageSeo['support_links']))
        <section class="my-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Bağlantılı Sayfalar</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">Sertifika ve kurumsal bilgi bağlantıları</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">Sertifika içerikleri, hizmet kapsamları ve kurumsal bilgi sayfalarıyla birlikte değerlendirilir.</p>
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
