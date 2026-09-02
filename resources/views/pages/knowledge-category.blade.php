@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-10">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <a href="{{ route('knowledge.index') }}" class="text-teal-400 hover:underline">Bilgi Merkezi</a><span>/</span>
            <span>{{ $category['name'] }}</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Bilgi Merkezi Kategorisi</p>
        <h1 class="mt-1 text-2xl font-extrabold lg:text-4xl">{{ $categorySeo['h1'] ?? $category['name'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $categorySeo['hero_text'] ?? 'Bu kategorideki içerikler kısa cevap, teknik detay ve ilgili hizmet/ürün bağlantılarıyla yapılandırılır.' }}</p>
    </section>

    {{-- ============ SEO GİRİŞ + KAPSAM ============ --}}
    @if(! empty($categorySeo))
        @if(! empty($categorySeo['sections']))
            <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach(array_slice($categorySeo['sections'], 0, 2) as $section)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                    </article>
                @endforeach
            </section>
        @endif

        @if(! empty($categorySeo['content_items']))
            <section class="my-10 grid grid-cols-1 gap-8 rounded-3xl border border-slate-200 bg-white p-8 lg:grid-cols-2 lg:p-10">
                <div>
                    <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">İçerik Kapsamı</p>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-900">{{ $categorySeo['scope_title'] ?? 'Bu kategoride hangi içerikler yer alır?' }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $categorySeo['scope_text'] ?? 'Rehberler kullanıcı sorusu, teknik açıklama ve ilgili hizmet/ürün bağlantıları etrafında yapılandırılır.' }}</p>
                </div>
                <ul class="grid grid-cols-1 gap-2.5 text-sm text-slate-700 sm:content-start">
                    @foreach($categorySeo['content_items'] as $item)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif

    {{-- ============ LİSTE + YAN MENÜ ============ --}}
    <section class="my-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <aside class="lg:col-span-3">
            <nav class="sticky top-24 space-y-1 rounded-2xl border border-slate-200 bg-white p-3 text-sm">
                <p class="px-2 pb-2 pt-1 text-xs font-bold uppercase tracking-wide text-slate-400">Kategoriler</p>
                @foreach($categories as $item)
                    <a href="{{ route('knowledge.category', $item['slug']) }}"
                       class="flex items-center justify-between rounded-lg px-3 py-2 font-medium transition {{ $item['slug'] === $category['slug'] ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        {{ $item['name'] }}
                        <span class="rounded-full bg-slate-100 px-1.5 text-[11px] text-slate-500">{{ $item['count'] }}</span>
                    </a>
                @endforeach
                @if(! empty($categorySeo['service_links']))
                    <p class="px-2 pb-2 pt-4 text-xs font-bold uppercase tracking-wide text-slate-400">İlgili hizmetler</p>
                    @foreach($categorySeo['service_links'] as $link)
                        <a href="{{ $link['url'] }}" class="block rounded-lg px-3 py-2 font-medium text-teal-700 hover:bg-slate-50">{{ $link['anchor'] }}</a>
                    @endforeach
                @endif
            </nav>
        </aside>

        <div class="lg:col-span-9">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach($articles as $article)
                    @include('partials.article-card', ['article' => $article])
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ EK SEO METİNLERİ ============ --}}
    @if(! empty($categorySeo['sections']) && count($categorySeo['sections']) > 2)
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach(array_slice($categorySeo['sections'], 2) as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                    @php($links = $loop->first ? ($categorySeo['service_links'] ?? []) : ($categorySeo['support_links'] ?? []))
                    @if(! empty($links))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($links as $link)
                                <a href="{{ $link['url'] }}" class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-teal-50 hover:text-teal-700">{{ $link['anchor'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ ALT CTA ============ --}}
    @if(! empty($categorySeo['cta']))
        <section class="my-12 flex flex-col items-start justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row md:items-center lg:p-12">
            <div>
                <h2 class="text-2xl font-bold">{{ $categorySeo['cta']['title'] }}</h2>
                <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $categorySeo['cta']['text'] }}</p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">{{ $categorySeo['cta']['button'] }}</a>
        </section>
    @endif

</div>
</div>
@endsection
