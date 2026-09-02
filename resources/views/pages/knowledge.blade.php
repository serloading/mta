@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Bilgi Merkezi</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Teknik Kütüphane</p>
        <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $knowledgeSeo['h1'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $knowledgeSeo['hero_text'] }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="#one-cikanlar" class="inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">Öne çıkan içerikler</a>
            <a href="{{ route('contact') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">{{ $knowledgeSeo['cta']['button'] }}</a>
        </div>
    </section>

    {{-- ============ KATEGORİLER ============ --}}
    @if(! empty($categories))
        <section class="my-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 text-xs font-bold uppercase tracking-wide text-slate-400">Kategoriler:</span>
                @foreach($categories as $category)
                    <a href="{{ route('knowledge.category', $category['slug']) }}"
                       class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:border-teal-600 hover:text-teal-700">
                        {{ $category['name'] }}
                        <span class="rounded-full bg-slate-100 px-1.5 text-[11px] text-slate-500">{{ $category['count'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ SEO METİNLERİ ============ --}}
    @if(! empty($knowledgeSeo['sections']))
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($knowledgeSeo['sections'] as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ ÖNE ÇIKAN ============ --}}
    @php($featured = collect($articles)->first())
    @if($featured)
        <section class="my-8" id="one-cikanlar">
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white lg:grid lg:grid-cols-12">
                <div class="flex min-h-[200px] items-end bg-gradient-to-br from-teal-700 to-slate-900 p-8 lg:col-span-5">
                    <span class="rounded-md bg-white/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-teal-200">Öne Çıkan</span>
                </div>
                <div class="p-8 lg:col-span-7 lg:p-10">
                    <a href="{{ route('knowledge.category', $featured['category_slug']) }}" class="text-xs font-bold uppercase tracking-wide text-teal-700 hover:underline">{{ $featured['category'] }}</a>
                    <h2 class="mt-2 text-2xl font-extrabold leading-tight text-slate-900">{{ $featured['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $featured['excerpt'] }}</p>
                    <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                        <span>{{ $featured['author'] }}</span><span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span>{{ $featured['reading_time'] }}</span><span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span>Güncelleme: {{ $featured['updated_at'] }}</span>
                    </div>
                    <a href="{{ route('knowledge.show', $featured['slug']) }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-lg bg-slate-900 px-6 text-sm font-bold text-white transition hover:bg-slate-800">Öne çıkan içeriği oku</a>
                </div>
            </section>
        </section>
    @endif

    {{-- ============ TÜM İÇERİKLER ============ --}}
    <section class="my-8">
        <h2 class="mb-5 text-xl font-extrabold text-slate-900">Kalibrasyon, ürün seçimi ve bakım rehberleri</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                @include('partials.article-card', ['article' => $article])
            @endforeach
        </div>
    </section>

    {{-- ============ İÇ LİNKLER ============ --}}
    @if(! empty($knowledgeSeo['support_links']))
        <section class="my-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">İç Linkler</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">Hizmet ve katalog bağlantıları</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">Rehber içeriklerini ürün, kalibrasyon ve teknik servis sayfalarıyla birlikte değerlendirebilirsiniz.</p>
            </div>
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-5">
                <div class="space-y-2">
                    @foreach($knowledgeSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}" class="block text-sm font-medium text-teal-700 hover:underline">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            </aside>
        </section>
    @endif

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-start justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row md:items-center lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">{{ $knowledgeSeo['cta']['title'] }}</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $knowledgeSeo['cta']['text'] }}</p>
        </div>
        <a href="{{ route('contact') }}" class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">{{ $knowledgeSeo['cta']['button'] }}</a>
    </section>

</div>
</div>
@endsection
