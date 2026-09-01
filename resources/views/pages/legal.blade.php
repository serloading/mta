@extends('layouts.site')

@php use Illuminate\Support\Carbon; @endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-10">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>{{ $legal['title'] }}</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Yasal Bilgilendirme</p>
        <h1 class="mt-1 text-2xl font-extrabold lg:text-4xl">{{ $legal['title'] }}</h1>
        @if(! empty($legal['updated']))
            <p class="mt-3 text-sm text-slate-400">Son güncelleme: {{ Carbon::parse($legal['updated'])->format('d.m.Y') }}</p>
        @endif
    </section>

    {{-- ============ İÇERİK ============ --}}
    <div class="my-8 grid grid-cols-1 gap-8 lg:grid-cols-12">

        {{-- Yan menü --}}
        <aside class="lg:col-span-3">
            <nav class="sticky top-24 space-y-1 rounded-2xl border border-slate-200 bg-white p-3 text-sm">
                <p class="px-2 pb-2 pt-1 text-xs font-bold uppercase tracking-wide text-slate-400">Yasal Metinler</p>
                @foreach($legalPages as $lp)
                    <a href="{{ url($lp['slug']) }}"
                       class="block rounded-lg px-3 py-2 font-medium transition {{ $lp['slug'] === $legalSlug ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        {{ $lp['title'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Metin --}}
        <div class="lg:col-span-9">
            <article class="legal-prose rounded-2xl border border-slate-200 bg-white p-6 lg:p-10">
                @if(! empty($legal['lead']))
                    <p class="legal-lead">{!! $legal['lead'] !!}</p>
                @endif
                @foreach($legal['sections'] as $sec)
                    <h2>{{ $sec['h'] }}</h2>
                    {!! $sec['body'] !!}
                @endforeach
            </article>
        </div>
    </div>

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-center justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">Sorularınız mı var?</h2>
            <p class="mt-1 text-sm text-slate-300">Kişisel verileriniz ve gizlilik uygulamalarımızla ilgili her konuda bize ulaşabilirsiniz.</p>
        </div>
        <a href="{{ route('contact') }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            İletişime Geçin
        </a>
    </section>

</div>
</div>
@endsection
