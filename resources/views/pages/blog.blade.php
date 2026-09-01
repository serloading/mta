@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Blog</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Yayın Akışı</p>
        <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $blogSeo['h1'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $blogSeo['hero_text'] }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="#yazilar" class="inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">Yazıları İncele</a>
            <a href="{{ route('knowledge.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">Bilgi Merkezi</a>
        </div>
    </section>

    {{-- ============ ÖNE ÇIKAN YAZI ============ --}}
    @php($featured = collect($articles)->first())
    @if($featured)
        <section class="my-8 overflow-hidden rounded-3xl border border-slate-200 bg-white lg:grid lg:grid-cols-12">
            <div class="flex min-h-[220px] items-end bg-gradient-to-br from-teal-700 to-slate-900 p-8 lg:col-span-5">
                <span class="rounded-md bg-white/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-teal-200">Öne Çıkan</span>
            </div>
            <div class="p-8 lg:col-span-7 lg:p-10">
                <a href="{{ route('knowledge.category', $featured['category_slug']) }}" class="text-xs font-bold uppercase tracking-wide text-teal-700 hover:underline">{{ $featured['category'] }}</a>
                <h2 class="mt-2 text-2xl font-extrabold leading-tight text-slate-900">{{ $featured['title'] }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $featured['excerpt'] }}</p>
                <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                    <span>{{ $featured['author'] }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    <span>{{ $featured['reading_time'] }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    <span>Güncelleme: {{ $featured['updated_at'] }}</span>
                </div>
                <a href="{{ route('knowledge.show', $featured['slug']) }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-lg bg-slate-900 px-6 text-sm font-bold text-white transition hover:bg-slate-800">Öne çıkan yazıyı oku</a>
            </div>
        </section>
    @endif

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

    {{-- ============ YAZI LİSTESİ ============ --}}
    <section class="my-8" id="yazilar">
        <h2 class="mb-5 text-xl font-extrabold text-slate-900">Laboratuvar cihazları blog içerikleri</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                @include('partials.article-card', ['article' => $article])
            @endforeach
        </div>
    </section>

    {{-- ============ SEO METİNLERİ ============ --}}
    @if(! empty($blogSeo['sections']))
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($blogSeo['sections'] as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-start justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row md:items-center lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">Teknik ekibe danışın</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $blogSeo['cta']['text'] ?? 'Cihaz seçimi, kalibrasyon kapsamı veya servis planlaması için uzman ekibimize yazın.' }}</p>
            @if(! empty($blogSeo['support_links']))
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($blogSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}" class="inline-flex rounded-lg bg-white/10 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-white/20">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            @endif
        </div>
        <a href="{{ route('contact') }}" class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            {{ $blogSeo['cta']['button'] ?? 'Teknik Ekibe Ulaş' }}
        </a>
    </section>

</div>
</div>
@endsection
