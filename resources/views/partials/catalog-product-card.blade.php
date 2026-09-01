{{-- CRO-focused catalog product card. Expects $product (array). --}}
@php
    use Illuminate\Support\Str;

    $productUrl = route('products.show', $product['slug']);
    $brandUrl = ! empty($product['brand_slug']) ? route('products.brand', $product['brand_slug']) : null;
    $categoryUrl = ! empty($product['category_slug']) ? route('products.category', $product['category_slug']) : null;

    $ignoredSpecKeys = ['marka', 'model', 'urun-grubu', 'kategori', 'sku', 'seri'];
    $specBits = collect($product['specs'] ?? [])
        ->reject(fn ($value, $key) => in_array(Str::slug((string) $key), $ignoredSpecKeys, true) || trim((string) $value) === '')
        ->take(2);
@endphp

<article class="cui-card" data-card data-name="{{ Str::lower($product['name']) }}">
    <div class="cui-card-body">
        <a class="cui-card-media" href="{{ $productUrl }}" tabindex="-1" aria-hidden="true">
            @if(! empty($product['image']))
                <img src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] ?? $product['name'] }}" loading="lazy">
            @else
                <span class="cui-card-ph">{{ $product['name'] }}</span>
            @endif
        </a>

        <p class="cui-card-meta">
            @if($brandUrl)<a href="{{ $brandUrl }}">{{ $product['brand'] }}</a>@else<span>{{ $product['brand'] }}</span>@endif
            <span class="cui-card-meta-sep" aria-hidden="true">·</span>
            @if($categoryUrl)<a href="{{ $categoryUrl }}">{{ $product['category'] }}</a>@else<span>{{ $product['category'] }}</span>@endif
        </p>

        <h3 class="cui-card-title"><a href="{{ $productUrl }}">{{ $product['name'] }}</a></h3>

        @if($specBits->isNotEmpty())
            <ul class="cui-card-specs">
                @foreach($specBits as $value)
                    <li>{{ Str::limit(trim((string) $value), 34) }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="cui-card-foot">
        <a class="cui-btn" href="{{ $productUrl }}">İncele</a>
    </div>
</article>
