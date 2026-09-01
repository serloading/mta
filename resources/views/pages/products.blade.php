@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-10">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span><span>Ürünler</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Endüstriyel &amp; Laboratuvar Cihazları</p>
        <h1 class="mt-1 text-2xl font-extrabold lg:text-4xl">Tüm Ürün Kataloğu</h1>
        <p class="mt-2 max-w-2xl text-sm text-slate-300">Laboratuvar cihazları, analiz sistemleri ve ölçüm ekipmanlarını marka, kategori ve teknik özelliğe göre filtreleyerek inceleyin.</p>
    </section>

    {{-- ============ KATALOG ============ --}}
    <div class="my-8">
        @include('partials.product-catalog', ['clearUrl' => route('products.index')])
    </div>

    {{-- ============ MARKA GRID ============ --}}
    <section class="my-12">
        <h2 class="text-center text-lg font-bold text-slate-900">Distribütörü ve Tedarikçisi Olduğumuz Markalar</h2>
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6">
            @foreach($brands->filter(fn ($b) => ! empty($b['logo'])) as $b)
                <a href="{{ route('products.brand', $b['slug']) }}"
                   class="flex items-center justify-center rounded-xl border border-slate-200/80 bg-white p-4 grayscale transition-all hover:border-teal-500 hover:shadow-sm hover:grayscale-0">
                    <img src="{{ asset($b['logo']) }}" alt="{{ $b['name'] }}" class="h-8 w-auto object-contain lg:h-10" loading="lazy">
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-center justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">Projeniz İçin Toplu Cihaz Teklifi İsteyin</h2>
            <p class="mt-1 text-sm text-slate-300">Laboratuvarınız için ihtiyaç duyduğunuz cihaz listesini iletin, 24 saat içinde özel fiyatlandıralım.</p>
        </div>
        <a href="{{ $genericQuoteCta['quote_url'] }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            Toplu Teklif Formunu Doldur
        </a>
    </section>

</div>
</div>
@endsection
