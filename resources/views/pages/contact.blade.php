@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO + FORM ============ --}}
    <section class="my-6 overflow-hidden rounded-3xl bg-slate-900 text-white">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="p-8 lg:col-span-6 lg:p-12">
                <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
                    <span>İletişim</span>
                </nav>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">{{ $pageSeo['eyebrow'] ?? 'İletişim' }}</p>
                <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $pageSeo['h1'] }}</h1>
                <p class="mt-3 max-w-md text-sm text-slate-300">{{ $pageSeo['hero_text'] }}</p>

                <dl class="mt-8 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Telefon</dt>
                        <dd class="mt-1"><a href="tel:{{ preg_replace('/\D+/', '', config('mta.site.phone')) }}" class="font-semibold text-white hover:text-teal-300">{{ config('mta.site.phone') }}</a></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">E-posta</dt>
                        <dd class="mt-1"><a href="mailto:{{ config('mta.site.email') }}" class="font-semibold text-white hover:text-teal-300">{{ config('mta.site.email') }}</a></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Adres</dt>
                        <dd class="mt-1 text-slate-300">{{ config('mta.site.address') }}</dd>
                    </div>
                    @if(config('mta.site.fax'))
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Faks</dt>
                            <dd class="mt-1 text-slate-300">{{ config('mta.site.fax') }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 flex flex-wrap gap-2 text-xs">
                    @foreach(config('mta.site.social_links') as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                           class="inline-flex items-center rounded-lg border border-white/15 px-3 py-1.5 font-medium text-white transition hover:bg-white/5">
                            {{ $social['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-slate-50 p-6 text-slate-900 lg:col-span-6 lg:p-10">
                @include('partials.lead-form', [
                    'leadContext' => $leadContext ?? null,
                    'formAction' => $formAction ?? route('leads.store'),
                    'formTitle' => 'Bize Yazın',
                    'formNote' => 'Talebinizi iletin, en kısa sürede dönüş yapalım.',
                ])
            </div>
        </div>
    </section>

    {{-- ============ SEO İÇERİK KARTLARI ============ --}}
    @if(! empty($pageSeo['sections']))
        <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($pageSeo['sections'] as $section)
                <article class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-base font-bold text-slate-900">{{ $section['title'] }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $section['text'] }}</p>
                    @if($loop->first && ! empty($pageSeo['request_info']))
                        <ul class="mt-4 space-y-2 text-sm text-slate-600">
                            @foreach($pageSeo['request_info'] as $item)
                                <li class="flex items-start gap-2">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @elseif(! $loop->first && ! empty($pageSeo['support_links']))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($pageSeo['support_links'] as $link)
                                <a href="{{ $link['url'] }}" class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-teal-50 hover:text-teal-700">{{ $link['anchor'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ ADRES / HARİTA ============ --}}
    <section class="my-10 flex flex-col items-start justify-between gap-6 rounded-3xl border border-slate-200 bg-white p-8 md:flex-row md:items-center">
        <div>
            <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Adres</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">{{ config('mta.site.address') }}</h2>
        </div>
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(config('mta.site.address')) }}" target="_blank" rel="noopener"
           class="inline-flex h-11 shrink-0 items-center justify-center rounded-lg border border-slate-300 px-6 text-sm font-bold text-slate-700 transition hover:border-teal-600 hover:text-teal-700">
            Haritada Aç
        </a>
    </section>

    {{-- ============ SSS ============ --}}
    @if(! empty($pageSeo['faq']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">SSS</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Sık sorulan sorular</h2>
            </div>
            <div class="mx-auto max-w-3xl space-y-3">
                @foreach($pageSeo['faq'] as $faq)
                    <details class="group rounded-2xl border border-slate-200 bg-white p-5">
                        <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-slate-900">
                            {{ $faq['question'] }}
                            <svg class="h-4 w-4 shrink-0 text-slate-400 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 011.4 0L10 10.6l3.3-3.3a1 1 0 111.4 1.4l-4 4a1 1 0 01-1.4 0l-4-4a1 1 0 010-1.4z" clip-rule="evenodd"/></svg>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

</div>
</div>
@endsection
