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
                <h1 class="text-3xl font-extrabold lg:text-4xl">MTA İletişim</h1>
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

    {{-- ============ ADRES / HARİTA ============ --}}
    @php($mapsUrl = 'https://www.google.com/maps/search/?api=1&query=40.88065695847028,29.23721823498121')
    <section class="my-10 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="flex flex-col items-start justify-between gap-4 p-8 md:flex-row md:items-center">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Adres</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">{{ config('mta.site.address') }}</h2>
            </div>
            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
               class="inline-flex h-11 shrink-0 items-center justify-center rounded-lg border border-slate-300 px-6 text-sm font-bold text-slate-700 transition hover:border-teal-600 hover:text-teal-700">
                Haritada Aç
            </a>
        </div>
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d188.53748485729912!2d29.23721823498121!3d40.88065695847028!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cadcb2c6343aef%3A0x8c18e811c5b87e40!2zTVRBIEVuZMO8c3RyaSDDnHLDvG5sZXJp!5e0!3m2!1str!2str!4v1788313364297!5m2!1str!2str"
            width="100%" height="380" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
            title="MTA Endüstri konum haritası" class="block w-full"></iframe>
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
