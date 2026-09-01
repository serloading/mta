{{--
    Catalog results area: mobile filter trigger + toolbar + product grid + drawer.
    Expects:
      $products      : Collection of product arrays
      $resultsLabel  : string, e.g. "VELP markasına ait"
      $primaryGroup  : array (see partials.catalog-filters)
      $specFilters   : Collection
      $clearUrl      : string
      $activeCount   : int  (number of active filters, for the mobile badge)
      $emptyTitle    : string
      $emptyText     : string
      $emptyCtaUrl   : string
      $emptyCtaLabel : string
--}}
@php($hasActive = ($activeCount ?? 0) > 0)

<button type="button" class="cui-mfilter" data-drawer-open aria-controls="cui-filter-drawer">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
    Filtreler
    @if($hasActive)<span class="cui-mfilter-n">{{ $activeCount }}</span>@endif
</button>

<div class="cui-toolbar">
    <p class="cui-toolbar-count">
        {{ $resultsLabel }} <strong>{{ $products->count() }}</strong> ürün listeleniyor
    </p>
    <div class="cui-toolbar-actions">
        <label class="sr-only" for="cui-sort">Sıralama</label>
        <select id="cui-sort" class="cui-select" data-sort>
            <option value="recommended">Önerilen Sıralama</option>
            <option value="az">İsim: A → Z</option>
            <option value="za">İsim: Z → A</option>
        </select>
        <div class="cui-viewtoggle" role="group" aria-label="Görünüm">
            <button type="button" data-view="grid" aria-pressed="true" aria-label="Izgara görünümü" title="Izgara görünümü">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>
            <button type="button" data-view="list" aria-pressed="false" aria-label="Liste görünümü" title="Liste görünümü">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
        </div>
    </div>
</div>

<div class="cui-grid" data-grid>
    @forelse($products as $product)
        @include('partials.catalog-product-card', ['product' => $product])
    @empty
        <div class="cui-empty">
            <h2>{{ $emptyTitle }}</h2>
            <p>{{ $emptyText }}</p>
            <a class="cui-btn" href="{{ $emptyCtaUrl }}">{{ $emptyCtaLabel }}</a>
        </div>
    @endforelse
</div>

{{-- Mobile off-canvas filter drawer --}}
<div class="cui-backdrop" data-drawer-backdrop></div>
<div class="cui-drawer" id="cui-filter-drawer" role="dialog" aria-modal="true" aria-label="Filtreler" inert>
    <div class="cui-drawer-head">
        <h2>Filtreler</h2>
        <button type="button" class="cui-iconbtn" data-drawer-close aria-label="Kapat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <div class="cui-drawer-body">
        @include('partials.catalog-filters', [
            'primaryGroup' => $primaryGroup,
            'specFilters' => $specFilters,
            'clearUrl' => $clearUrl,
            'hasActive' => $hasActive,
        ])
    </div>
    <div class="cui-drawer-foot">
        @if($hasActive)
            <a class="cui-btn cui-btn--ghost" href="{{ $clearUrl }}">Temizle</a>
        @endif
        <button type="button" class="cui-btn" data-drawer-close>Sonuçları gör ({{ $products->count() }})</button>
    </div>
</div>
