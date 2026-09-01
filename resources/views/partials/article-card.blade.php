<article class="article-card">
    <div class="article-card-media visual-placeholder">
        <span>Makale görseli alanı</span>
        <small>Gerçek kapak görseli eklenecek</small>
    </div>
    <div class="article-card-body">
        <a class="card-kicker" href="{{ route('knowledge.category', $article['category_slug']) }}">{{ $article['category'] }}</a>
        <h2>{{ $article['title'] }}</h2>
        <p>{{ $article['excerpt'] }}</p>
        <div class="article-meta">
            <span>{{ $article['author'] }}</span>
            <span>{{ $article['reading_time'] }}</span>
            <span>Güncelleme: {{ $article['updated_at'] }}</span>
        </div>
        <a class="read-link" href="{{ route('knowledge.show', $article['slug']) }}">İçeriği oku</a>
    </div>
</article>
