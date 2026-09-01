@extends('layouts.site')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <a href="{{ route('knowledge.index') }}" class="text-teal-400 hover:underline">Bilgi Merkezi</a><span>/</span>
            <a href="{{ route('knowledge.category', $article['category_slug']) }}" class="text-teal-400 hover:underline">{{ $article['category'] }}</a><span>/</span>
            <span>{{ Str::limit($article['title'], 40) }}</span>
        </nav>
        <a href="{{ route('knowledge.category', $article['category_slug']) }}" class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400 hover:underline">{{ $article['category'] }}</a>
        <h1 class="mt-1 max-w-3xl text-3xl font-extrabold leading-tight lg:text-4xl">{{ $article['title'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $article['excerpt'] }}</p>
        <div class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
            <span>{{ $article['author'] }}</span>
            <span class="h-1 w-1 rounded-full bg-slate-600"></span>
            <span>Yayın: {{ $article['published_at'] }}</span>
            <span class="h-1 w-1 rounded-full bg-slate-600"></span>
            <span>Güncelleme: {{ $article['updated_at'] }}</span>
            <span class="h-1 w-1 rounded-full bg-slate-600"></span>
            <span>{{ $article['reading_time'] }}</span>
        </div>
    </section>

    {{-- ============ İÇERİK ============ --}}
    <div class="my-8 grid grid-cols-1 gap-8 lg:grid-cols-12">

        {{-- Makale gövdesi --}}
        <div class="lg:col-span-8">
            <article class="legal-prose rounded-2xl border border-slate-200 bg-white p-6 lg:p-10">

                @if(! empty($article['answer']))
                    <div class="mb-6 rounded-xl border border-teal-100 bg-teal-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-teal-700">Kısa cevap</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $article['answer'] }}</p>
                    </div>
                @endif

                @if(! empty($articleSeo['sections']))
                    <div class="mb-8 rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">İçindekiler</p>
                        <ul class="mt-2 space-y-1.5 text-sm">
                            @foreach($articleSeo['sections'] as $section)
                                <li><a href="#{{ Str::slug($section['title']) }}" class="text-teal-700 hover:underline">{{ $section['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    @foreach($articleSeo['sections'] as $section)
                        <h2 id="{{ Str::slug($section['title']) }}">{{ $section['title'] }}</h2>
                        @if(! empty($section['text']))
                            <p>{{ $section['text'] }}</p>
                        @endif
                        @if(! empty($section['items']))
                            @if(Str::contains($section['title'], 'Süreç'))
                                <ol class="my-4 space-y-3">
                                    @foreach($section['items'] as $item)
                                        <li class="flex gap-3">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-600 text-xs font-bold text-white">{{ $loop->iteration }}</span>
                                            <span class="text-sm leading-relaxed text-slate-700">{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <ul>
                                    @foreach($section['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif
                    @endforeach

                    @if(! empty($articleSeo['support_links']))
                        <h2>İlgili Sayfalar</h2>
                        <div class="not-prose flex flex-wrap gap-2">
                            @foreach($articleSeo['support_links'] as $link)
                                <a href="{{ $link['url'] }}" class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 no-underline transition hover:bg-teal-50 hover:text-teal-700">{{ $link['anchor'] }}</a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <h2>Detaylı Açıklama</h2>
                    <p>Bu makale yayın öncesinde gerçek uzman içeriği, kaynaklar, ilgili hizmet ve ürün ilişkileriyle zenginleştirilecektir.</p>
                    <h2>İlgili Hizmetler</h2>
                    <ul>
                        @foreach($services as $service)
                            <li><a href="{{ route('services.show', $service['slug']) }}">{{ $service['title'] }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </article>
        </div>

        {{-- Yan panel --}}
        <aside class="lg:col-span-4">
            <div class="sticky top-24 space-y-6">
                <div class="rounded-2xl bg-slate-900 p-6 text-white">
                    <h2 class="text-lg font-bold">{{ $articleSeo['cta']['title'] ?? 'Bu konuda teknik destek alın' }}</h2>
                    <p class="mt-2 text-sm text-slate-300">{{ $articleSeo['cta']['text'] ?? 'Cihaz seçimi ve kalibrasyon kapsamı için uzman ekibimize yazın.' }}</p>
                    <a href="{{ route('contact') }}" class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">
                        {{ $articleSeo['cta']['button'] ?? 'Teknik Ekibe Ulaş' }}
                    </a>
                </div>

                @if(! empty($articleSeo['support_links']))
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">İç Linkler</p>
                        <div class="mt-3 space-y-2">
                            @foreach($articleSeo['support_links'] as $link)
                                <a href="{{ $link['url'] }}" class="block text-sm font-medium text-teal-700 hover:underline">{{ $link['anchor'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    {{-- ============ İLGİLİ ÜRÜNLER ============ --}}
    @if(! empty($products))
        <section class="my-12">
            <h2 class="mb-5 text-xl font-extrabold text-slate-900">İlgili Ürünler</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(collect($products)->take(6) as $product)
                    <a href="{{ route('products.show', $product['slug']) }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-lg">
                        <p class="text-xs font-bold uppercase tracking-wide text-teal-700">{{ $product['brand'] }}</p>
                        <p class="mt-1 text-sm font-semibold leading-snug text-slate-900 line-clamp-2 group-hover:text-teal-700">{{ $product['name'] }}</p>
                        <span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-teal-700">İncele
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.3 4.3a1 1 0 011.4 0l5 5a1 1 0 010 1.4l-5 5a1 1 0 01-1.4-1.4L11.6 10 7.3 5.7a1 1 0 010-1.4z" clip-rule="evenodd"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>
</div>
@endsection
