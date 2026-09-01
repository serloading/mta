@php
    $type = $type ?? 'category';
    $title = $title ?? $name;
    $action = $action ?? 'İncele';
    $classes = trim('content-card category-card taxonomy-card taxonomy-card--' . $type . ' ' . ($class ?? ''));
@endphp

<a class="{{ $classes }}" href="{{ $href }}" aria-label="{{ $title }}">
    @if(! empty($image))
        <img class="category-card-media object-image" src="{{ asset($image) }}" alt="{{ $alt ?? $name }}">
    @else
        <div class="visual-placeholder category-card-media">
            <span>{{ $name }}</span>
        </div>
    @endif
    <h2>{{ $title }}</h2>
    @if(! empty($summary))
        <p>{{ $summary }}</p>
    @endif
    <span class="category-card-action taxonomy-card-action">{{ $action }}</span>
</a>
