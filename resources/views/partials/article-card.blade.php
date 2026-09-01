<article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-lg">
    <a href="{{ route('knowledge.show', $article['slug']) }}" class="relative flex h-32 items-end bg-gradient-to-br from-slate-800 to-slate-900 p-4">
        <span class="rounded-md bg-white/10 px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-teal-300">{{ $article['category'] }}</span>
    </a>
    <div class="flex flex-1 flex-col p-5">
        <h2 class="text-base font-bold leading-snug text-slate-900 line-clamp-2 group-hover:text-teal-700">
            <a href="{{ route('knowledge.show', $article['slug']) }}">{{ $article['title'] }}</a>
        </h2>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $article['excerpt'] }}</p>
        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
            <span>{{ $article['author'] }}</span>
            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
            <span>{{ $article['reading_time'] }}</span>
        </div>
        <a href="{{ route('knowledge.show', $article['slug']) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-teal-700 hover:gap-2">
            İçeriği oku
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.3 4.3a1 1 0 011.4 0l5 5a1 1 0 010 1.4l-5 5a1 1 0 01-1.4-1.4L11.6 10 7.3 5.7a1 1 0 010-1.4z" clip-rule="evenodd"/></svg>
        </a>
    </div>
</article>
