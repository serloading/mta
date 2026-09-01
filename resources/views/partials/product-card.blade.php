@php($productUrl = route('products.show', $product['slug']))

<article class="product-card">
    @if(! empty($product['image']))
        <img class="product-card-image object-image" src="{{ asset($product['image']) }}" alt="{{ $product['image_alt'] ?? $product['name'] }}">
    @else
        <div class="visual-placeholder product-card-image">
            <span>{{ $product['image_label'] ?? $product['name'] }}</span>
            <small>Tek ürün görseli</small>
        </div>
    @endif
    <div class="product-card-body">
        <span>{{ $product['brand'] }} / {{ $product['category'] }}</span>
        <h3>{{ $product['name'] }}</h3>
        <p>{{ $product['summary'] }}</p>
        <a class="product-card-action product-card-link" href="{{ $productUrl }}">Ürünü İncele</a>
    </div>
</article>
