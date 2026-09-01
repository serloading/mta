@extends('layouts.site')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-10">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <a href="{{ route('products.index') }}" class="text-teal-400 hover:underline">Ürünler</a><span>/</span>
            <span>{{ $category['name'] }}</span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            @if(! empty($category['image']))
                <span class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-white p-1.5">
                    <img src="{{ asset($category['image']) }}" alt="{{ $category['image_alt'] ?? $category['name'] }}" class="h-auto max-h-full w-auto max-w-full object-contain" loading="lazy">
                </span>
            @endif
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Ürün Kategorisi</p>
                <h1 class="mt-1 text-2xl font-extrabold lg:text-4xl">{{ $categorySeo['h1'] ?? ($category['name'] . ' Kataloğu') }}</h1>
            </div>
        </div>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $categorySeo['hero_text'] ?? $category['summary'] ?? ($category['name'] . ' kategorisindeki cihazları marka ve teknik özelliğe göre filtreleyin.') }}</p>
    </section>

    {{-- ============ KATALOG ============ --}}
    <div class="my-8">
        @include('partials.product-catalog', ['clearUrl' => route('products.category', $category['slug'])])
    </div>

    {{-- ============ KATEGORİ BİLGİ KARTLARI ============ --}}
    @if(! empty($categorySeo['sections']))
        <section class="my-12 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach(collect($categorySeo['sections'])->take(2) as $sec)
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $sec['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ Str::limit($sec['text'], 420) }}</p>
                </div>
            @endforeach
        </section>
    @endif

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-center justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">{{ $category['name'] }} için Teklif Alın</h2>
            <p class="mt-1 text-sm text-slate-300">İhtiyacınız olan model ve adet bilgisini iletin, teknik ekibimiz 24 saat içinde fiyatlandırsın.</p>
        </div>
        <a href="{{ $genericQuoteCta['quote_url'] }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            Teklif Talebi Oluştur
        </a>
    </section>

</div>
</div>
@endsection
