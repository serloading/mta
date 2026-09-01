@php
    use Illuminate\Support\Str;

    $lock = $catalogLock ?? ['category' => null, 'brand' => null];
    $action = $catalogAction ?? url()->current();
    $clearUrl = $clearUrl ?? $action;

    $deviceIcon = '<svg class="h-14 w-14 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 3v4h8V3"/><circle cx="12" cy="13" r="3"/><path d="M9 21v-2h6v2"/></svg>';
@endphp

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12" data-catalog>

    {{-- SOL · filtre sidebar --}}
    <form method="get" action="{{ $action }}" data-catalog-filter class="lg:col-span-3">
        <input type="hidden" name="sirala" value="{{ $filters['sirala'] }}">
        <div class="sticky top-24 max-h-[85vh] space-y-5 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mega-scroll">

            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" name="q" value="{{ $filters['q'] }}" data-catalog-search autocomplete="off"
                       class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs outline-none focus:border-teal-500"
                       placeholder="Model, marka veya cihaz ara...">
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                <span class="text-sm font-bold text-slate-900">Filtreler</span>
                @if($hasActiveFilters)
                    <a href="{{ $clearUrl }}" class="text-xs font-medium text-teal-600 hover:underline">Temizle</a>
                @endif
            </div>

            @if(! $lock['category'] && $facetCategories->isNotEmpty())
                <details open class="group border-t border-slate-100 pt-4">
                    <summary class="flex cursor-pointer items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                        Kategoriler
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="mt-3 space-y-1.5">
                        @foreach($facetCategories as $c)
                            <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 hover:text-slate-900">
                                <input type="checkbox" name="kategori[]" value="{{ $c['slug'] }}" @checked(in_array($c['slug'], $filters['kategori'])) class="h-3.5 w-3.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500 focus:ring-offset-0">
                                <span class="flex-1">{{ $c['name'] }}</span>
                                <span class="text-slate-400">({{ $c['count'] }})</span>
                            </label>
                        @endforeach
                    </div>
                </details>
            @endif

            @if(! $lock['brand'] && $facetBrands->isNotEmpty())
                <details open class="group border-t border-slate-100 pt-4">
                    <summary class="flex cursor-pointer items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                        Markalar
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="mt-3 space-y-1.5">
                        @foreach($facetBrands as $b)
                            <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 hover:text-slate-900">
                                <input type="checkbox" name="marka[]" value="{{ $b['slug'] }}" @checked(in_array($b['slug'], $filters['marka'])) class="h-3.5 w-3.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500 focus:ring-offset-0">
                                <span class="flex-1">{{ $b['name'] }}</span>
                                <span class="text-slate-400">({{ $b['count'] }})</span>
                            </label>
                        @endforeach
                    </div>
                </details>
            @endif

            <details open class="group border-t border-slate-100 pt-4">
                <summary class="flex cursor-pointer items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                    Durum
                    <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </summary>
                <div class="mt-3 space-y-1.5">
                    <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 hover:text-slate-900">
                        <input type="checkbox" name="durum[]" value="turkak" @checked(in_array('turkak', $filters['durum'])) class="h-3.5 w-3.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500 focus:ring-offset-0">
                        TÜRKAK Akredite Kalibrasyonlu
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-600 hover:text-slate-900">
                        <input type="checkbox" name="durum[]" value="gorselli" @checked(in_array('gorselli', $filters['durum'])) class="h-3.5 w-3.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500 focus:ring-offset-0">
                        Görselli ürünler
                    </label>
                </div>
            </details>

            <button type="submit" class="w-full rounded-lg bg-teal-600 py-2 text-xs font-semibold text-white transition hover:bg-teal-700 lg:hidden">Filtreleri Uygula</button>
        </div>
    </form>

    {{-- SAĞ · ürün grid --}}
    <div class="lg:col-span-9">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs font-medium text-slate-500">Toplam <strong class="text-slate-900">{{ $total }}</strong> ürün listeleniyor</p>
            <div class="flex items-center gap-2">
                <form method="get" action="{{ $action }}" data-catalog-sort>
                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                    @foreach($filters['kategori'] as $c)<input type="hidden" name="kategori[]" value="{{ $c }}">@endforeach
                    @foreach($filters['marka'] as $b)<input type="hidden" name="marka[]" value="{{ $b }}">@endforeach
                    @foreach($filters['durum'] as $d)<input type="hidden" name="durum[]" value="{{ $d }}">@endforeach
                    <select name="sirala" class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 outline-none focus:border-teal-500">
                        <option value="onerilen" @selected($filters['sirala'] === 'onerilen')>Önerilen Sıralama</option>
                        <option value="az" @selected($filters['sirala'] === 'az')>Ada Göre (A-Z)</option>
                        <option value="za" @selected($filters['sirala'] === 'za')>Ada Göre (Z-A)</option>
                        <option value="marka" @selected($filters['sirala'] === 'marka')>Markaya Göre</option>
                    </select>
                </form>
                <div class="flex overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <button type="button" data-view="grid" aria-pressed="true" aria-label="Izgara görünüm" class="flex h-9 w-9 items-center justify-center text-slate-500 aria-pressed:bg-teal-600 aria-pressed:text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </button>
                    <button type="button" data-view="list" aria-pressed="false" aria-label="Liste görünüm" class="flex h-9 w-9 items-center justify-center text-slate-500 aria-pressed:bg-teal-600 aria-pressed:text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>

        @if($products->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 p-12 text-center text-sm text-slate-500">
                Filtrelerinize uygun ürün bulunamadı.
                <a href="{{ $clearUrl }}" class="font-semibold text-teal-600 hover:underline">Filtreleri temizleyin</a>.
            </div>
        @else
            <div data-grid class="catalog-grid grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($products as $p)
                    <a href="{{ route('products.show', $p['slug']) }}" data-card data-name="{{ $p['name'] }}"
                       class="catalog-card group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4 transition-all hover:border-teal-500 hover:shadow-lg">
                        <div class="catalog-card-body">
                            <div class="catalog-card-media relative mb-3 flex h-48 w-full items-center justify-center overflow-hidden rounded-lg border border-slate-100 bg-white p-5">
                                @if(! empty($p['image']))
                                    <img src="{{ asset($p['image']) }}" alt="{{ $p['image_alt'] ?? $p['name'] }}" class="h-auto max-h-full w-auto max-w-full object-contain" loading="lazy">
                                @else
                                    {!! $deviceIcon !!}
                                @endif
                                @if(! empty($p['related_services']))
                                    <span class="absolute right-2 top-2 rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 shadow-sm">Kalibrasyonlu</span>
                                @endif
                            </div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-teal-700">{{ trim(($p['brand'] ?? '') . (($p['brand'] ?? '') && ($p['category'] ?? '') ? ' | ' : '') . ($p['category'] ?? ''), ' |') }}</p>
                            <h3 class="mt-1 line-clamp-2 text-sm font-semibold text-slate-900 transition-colors group-hover:text-teal-600">{{ $p['name'] }}</h3>
                            @php($specPreview = collect($p['specs'] ?? [])->take(2))
                            @if($specPreview->isNotEmpty())
                                <ul class="mt-2 space-y-0.5 text-xs text-slate-500">
                                    @foreach($specPreview as $k => $v)
                                        <li class="line-clamp-1">{{ is_string($k) ? $k . ': ' : '' }}{{ Str::limit((string) $v, 44) }}</li>
                                    @endforeach
                                </ul>
                            @elseif(! empty($p['features']))
                                <ul class="mt-2 space-y-0.5 text-xs text-slate-500">
                                    @foreach(collect($p['features'])->take(2) as $f)
                                        <li class="line-clamp-1">{{ Str::limit((string) $f, 44) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-2">
                            <span class="rounded-md bg-amber-50 px-2 py-1 text-[11px] font-bold text-amber-700">Teklif Alın</span>
                            <span class="rounded-lg bg-teal-600 px-3.5 py-2 text-xs font-semibold text-white transition-all group-hover:bg-teal-700">İncele / Teklif İste</span>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($products->hasPages())
                <nav class="mt-12 flex items-center justify-center gap-1.5 border-t border-slate-200 py-4" aria-label="Sayfalandırma">
                    @if($products->onFirstPage())
                        <span class="flex h-9 items-center rounded-lg border border-slate-200 px-3 text-xs text-slate-300">Önceki</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-600 hover:border-teal-500 hover:text-teal-700">Önceki</a>
                    @endif

                    @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if($page == $products->currentPage())
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-600 text-xs font-bold text-white">{{ $page }}</span>
                        @elseif($page == 1 || $page == $products->lastPage() || abs($page - $products->currentPage()) <= 1)
                            <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:border-teal-500 hover:text-teal-700">{{ $page }}</a>
                        @elseif(abs($page - $products->currentPage()) === 2)
                            <span class="px-1 text-xs text-slate-400">…</span>
                        @endif
                    @endforeach

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-600 hover:border-teal-500 hover:text-teal-700">Sonraki</a>
                    @else
                        <span class="flex h-9 items-center rounded-lg border border-slate-200 px-3 text-xs text-slate-300">Sonraki</span>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</div>
