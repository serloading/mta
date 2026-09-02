@extends('layouts.site')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-10">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <a href="{{ route('brands.index') }}" class="text-teal-400 hover:underline">Markalar</a><span>/</span>
            <span>{{ $brand['name'] }}</span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            @if(! empty($brand['logo']))
                <span class="flex h-14 items-center justify-center rounded-xl bg-white px-4">
                    <img src="{{ img_url($brand['logo']) }}" alt="{{ $brand['name'] }}" class="h-8 w-auto object-contain" loading="lazy">
                </span>
            @endif
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Yetkili Tedarikçi</p>
                <h1 class="mt-1 text-2xl font-extrabold lg:text-4xl">{{ $brandSeo['h1'] ?? ($brand['name'] . ' Ürünleri') }}</h1>
            </div>
        </div>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $brandSeo['hero_text'] ?? $brand['summary'] ?? ($brand['name'] . ' markasına ait cihazları kategori ve teknik özelliğe göre filtreleyin.') }}</p>
    </section>

    {{-- ============ KATALOG ============ --}}
    <div class="my-8">
        @include('partials.product-catalog', ['clearUrl' => route('products.brand', $brand['slug'])])
    </div>

    {{-- ============ MARKA BİLGİ KARTLARI ============ --}}
    @if(! empty($brandSeo['sections']))
        <section class="my-12 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach(collect($brandSeo['sections'])->take(2) as $sec)
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
            <h2 class="text-2xl font-bold">{{ $brand['name'] }} cihazları için teklif alın</h2>
            <p class="mt-1 text-sm text-slate-300">Model ve adet bilgisiyle talebinizi iletin, teknik ekibimiz aynı gün dönüş yapsın.</p>
        </div>
        <a href="{{ $genericQuoteCta['quote_url'] }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            Teklif Talebi Oluştur
        </a>
    </section>

</div>
</div>
@endsection
