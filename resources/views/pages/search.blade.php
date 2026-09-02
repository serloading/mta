@extends('layouts.site')

@section('content')
@php
    $total = count($results['products']) + count($results['categories']) + count($results['brands']);
@endphp

<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO + ARAMA ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-10">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Arama</span>
        </nav>
        <h1 class="text-2xl font-extrabold lg:text-3xl">
            @if($query !== '')
                &ldquo;{{ $query }}&rdquo; için {{ $total }} sonuç
            @else
                Ürün ve hizmet arama
            @endif
        </h1>
        <form action="{{ route('search') }}" method="get" role="search" class="mt-5 flex max-w-xl gap-2">
            <input type="search" name="q" value="{{ $query }}" placeholder="Cihaz, model kodu veya kategori ara…" autofocus
                   class="h-11 flex-1 rounded-lg border border-slate-600 bg-slate-800 px-4 text-sm text-white outline-none placeholder:text-slate-400 focus:border-teal-500">
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">Ara</button>
        </form>
    </section>

    @if($query === '')
        <p class="my-10 text-sm text-slate-500">Aramak istediğiniz cihaz adını, model kodunu, kategoriyi veya markayı yazın.</p>
    @elseif($total === 0)
        <div class="my-12 rounded-3xl border border-slate-200 bg-white p-10 text-center">
            <h2 class="text-lg font-bold text-slate-900">Sonuç bulunamadı</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-600">&ldquo;{{ $query }}&rdquo; için eşleşme yok. Farklı bir anahtar kelime deneyin ya da tüm kataloğa göz atın.</p>
            <a href="{{ route('products.index') }}" class="mt-5 inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">Ürün Kataloğu</a>
        </div>
    @else
        @if(! empty($results['categories']) || ! empty($results['brands']))
            <section class="my-8">
                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400">Kategori &amp; Marka</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(array_merge($results['categories'], $results['brands']) as $item)
                        <a href="{{ $item['url'] }}"
                           class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:border-teal-600 hover:text-teal-700">
                            {{ $item['label'] }}
                            <span class="text-xs text-slate-400">· {{ $item['sub'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(! empty($results['products']))
            <section class="my-8">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-500">Ürünler</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($results['products'] as $p)
                        <a href="{{ $p['url'] }}"
                           class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="flex h-40 items-center justify-center overflow-hidden border-b border-slate-100 bg-white p-5">
                                @if($p['image'])
                                    <img src="{{ $p['image'] }}" alt="{{ $p['label'] }}" class="max-h-full w-auto max-w-full object-contain" loading="lazy">
                                @else
                                    <svg class="h-9 w-9 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="13" r="3"/></svg>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-teal-700">{{ $p['sub'] }}</p>
                                <h3 class="mt-1 flex-1 text-sm font-semibold leading-snug text-slate-900 line-clamp-2 group-hover:text-teal-700">{{ $p['label'] }}</h3>
                                <span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-teal-700">İncele
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.3 4.3a1 1 0 011.4 0l5 5a1 1 0 010 1.4l-5 5a1 1 0 01-1.4-1.4L11.6 10 7.3 5.7a1 1 0 010-1.4z" clip-rule="evenodd"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endif

</div>
</div>
@endsection
