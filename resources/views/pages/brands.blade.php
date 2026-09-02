@extends('layouts.site')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Markalar</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Marka Bazlı Katalog</p>
        <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $brandsSeo['h1'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $brandsSeo['hero_text'] }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="#marka-listesi" class="inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">{{ $brandsSeo['primary_cta'] }}</a>
            <a href="{{ route('products.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">{{ $brandsSeo['secondary_cta'] }}</a>
        </div>
        <div class="mt-8 flex gap-8 border-t border-white/10 pt-6">
            <div><p class="text-2xl font-extrabold text-white">{{ $brands->count() }}</p><p class="text-xs text-slate-400">marka</p></div>
            <div><p class="text-2xl font-extrabold text-white">{{ $categories->count() }}</p><p class="text-xs text-slate-400">kategori</p></div>
        </div>
    </section>

    {{-- ============ SEO GİRİŞ ============ --}}
    @if(! empty($brandsSeo['sections']))
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach(array_slice($brandsSeo['sections'], 0, 2) as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ MARKA KARTLARI ============ --}}
    <section class="my-12" id="marka-listesi">
        <div class="mb-6 text-center">
            <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Markalar</p>
            <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Katalogda Yer Alan Markalar</h2>
            <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600">Marka kartlarından ilgili ürün listesine, kategori ilişkilerine ve teklif talebine geçiş yapabilirsiniz.</p>
        </div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($brands as $brand)
                @php($card = $brandsSeo['brand_cards'][$brand['slug']] ?? null)
                @php($logo = $brand['image'] ?? $brand['logo'] ?? null)
                <a href="{{ route('products.brand', $brand['slug']) }}"
                   class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex h-28 items-center justify-center border-b border-slate-100 bg-white p-6">
                        @if($logo)
                            <img src="{{ img_url($logo) }}" alt="{{ $card['alt'] ?? $brand['name'] . ' logosu' }}" class="max-h-full w-auto max-w-full object-contain grayscale transition group-hover:grayscale-0" loading="lazy">
                        @else
                            <span class="text-lg font-extrabold text-slate-800">{{ $brand['name'] }}</span>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-teal-700">{{ $card['anchor'] ?? $brand['name'] . ' ürünleri' }}</h3>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $card['summary'] ?? $brand['summary'] }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-teal-700">Ürünleri Gör
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.3 4.3a1 1 0 011.4 0l5 5a1 1 0 010 1.4l-5 5a1 1 0 01-1.4-1.4L11.6 10 7.3 5.7a1 1 0 010-1.4z" clip-rule="evenodd"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============ KATEGORİ İLİŞKİLERİ ============ --}}
    @if(isset($brandsSeo['sections'][2]))
        <section class="my-12 rounded-3xl border border-slate-200 bg-white p-8 lg:p-10">
            <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Kategori İlişkileri</p>
            <h2 class="mt-1 text-xl font-extrabold text-slate-900">{{ $brandsSeo['sections'][2]['title'] }}</h2>
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-600">{{ $brandsSeo['sections'][2]['text'] }}</p>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($categories as $category)
                    @php($card = $brandsSeo['category_cards'][$category['slug']] ?? null)
                    <a href="{{ route('products.category', $category['slug']) }}"
                       class="group rounded-xl border border-slate-200 p-4 transition hover:border-teal-500 hover:bg-slate-50">
                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-teal-700">{{ $card['title'] ?? $category['name'] }}</h3>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500 line-clamp-2">{{ $card['summary'] ?? $category['summary'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ EK SEO METİNLERİ ============ --}}
    @if(count($brandsSeo['sections']) > 3)
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach(array_slice($brandsSeo['sections'], 3) as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                    @if($loop->last && ! empty($brandsSeo['support_links']))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($brandsSeo['support_links'] as $link)
                                <a href="{{ $link['url'] }}" class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-teal-50 hover:text-teal-700">{{ $link['anchor'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ SEÇİM KRİTERLERİ ============ --}}
    @if(! empty($brandsSeo['selection_items']))
        <section class="my-12 grid grid-cols-1 gap-8 rounded-3xl border border-slate-200 bg-white p-8 lg:grid-cols-2 lg:p-10">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Seçim Kriterleri</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-900">Marka seçerken nelere dikkat edilmeli?</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">Marka adı tek başına yeterli değildir; teknik ihtiyaç, kullanım ortamı ve servis bağlantısı birlikte değerlendirilmelidir.</p>
            </div>
            <ul class="grid grid-cols-1 gap-2.5 text-sm text-slate-700 sm:content-start">
                @foreach($brandsSeo['selection_items'] as $item)
                    <li class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ============ SSS ============ --}}
    @if(! empty($brandsSeo['faq']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">SSS</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Sık sorulan sorular</h2>
            </div>
            <div class="mx-auto max-w-3xl space-y-3">
                @foreach($brandsSeo['faq'] as $faq)
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
            <h2 class="text-2xl font-bold">{{ $brandsSeo['cta']['title'] }}</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $brandsSeo['cta']['text'] }}</p>
        </div>
        <div class="flex shrink-0 flex-wrap gap-3">
            <a href="{{ route('quote', ['source_type' => 'product', 'source_name' => 'Marka bazlı ürün teklifi']) }}" class="inline-flex h-12 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">{{ $brandsSeo['cta']['button'] }}</a>
            <a href="{{ route('products.index') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">{{ $brandsSeo['cta']['secondary_button'] }}</a>
        </div>
    </section>

</div>
</div>
@endsection
