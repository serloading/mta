@extends('layouts.site')

@php
    use Illuminate\Support\Str;

    /* Bir grup için kısa "aralık" ve "belirsizlik" özeti üret */
    $groupSummary = function (array $group): array {
        $cols = $group['columns'] ?? [];
        $rows = $group['rows'] ?? [];
        $find = function (array $needles) use ($cols) {
            foreach ($cols as $i => $c) {
                foreach ($needles as $n) {
                    if (Str::contains(Str::lower($c), $n)) return $i;
                }
            }
            return null;
        };
        $rangeIdx = $find(['aralık', 'ölçüm', 'hacim', 'adım', 'kapasite']);
        $uIdx = $find(['belirsiz']) ?? $find(['u']);
        $range = null;
        if ($rangeIdx !== null && $rows) {
            $first = $rows[0][$rangeIdx] ?? null;
            $last = $rows[count($rows) - 1][$rangeIdx] ?? null;
            $range = ($first && $last && $first !== $last) ? ($first . '  …  ' . $last) : ($first ?: $last);
        }
        $u = ($uIdx !== null && $rows) ? ($rows[0][$uIdx] ?? null) : null;
        return ['range' => $range, 'u' => $u];
    };
@endphp

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============== SECTION 1 · HERO + INSTANT SEARCH ============== --}}
    <section class="relative my-6 overflow-hidden rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-teal-500/10 blur-3xl"></div>
        <nav class="mb-5 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span><span>Kapsam</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">TÜRKAK ISO/IEC 17025 AKREDİTE KAPSAM</p>
        <h1 class="mt-2 max-w-3xl text-3xl font-extrabold leading-tight lg:text-4xl">Kalibrasyon Kapsamımız ve Ölçüm Aralıkları</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">
            Laboratuvarımız bünyesinde veya sahanızda kalibre edilen tüm cihazları ve ölçüm belirsizliklerini anında arayın.
        </p>

        <div class="relative mt-7 max-w-3xl">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" data-scope-search
                   class="h-14 w-full rounded-xl bg-white pl-12 pr-28 text-sm text-slate-900 shadow-2xl shadow-slate-950/40 outline-none placeholder:text-slate-400"
                   placeholder="Cihaz adı, parametre veya ölçüm aralığı ara... (ör. Manometre, 0-100 bar, Kumpas)">
            <span data-scope-search-count
                  class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                {{ $scopeStats['groups'] }} grup
            </span>
        </div>

        <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-400">
            <span><strong class="text-white">{{ $scopeStats['categories'] }}</strong> ölçüm alanı</span>
            <span><strong class="text-white">{{ $scopeStats['groups'] }}</strong> cihaz grubu</span>
            <span><strong class="text-white">{{ $scopeStats['rows'] }}+</strong> aralık satırı</span>
        </div>
    </section>

    {{-- ============== SECTION 2 · STICKY CATEGORY TABS ============== --}}
    <div data-scope-toolbar class="sticky top-[68px] z-30 -mx-4 mb-8 border-b border-slate-200 bg-slate-50/90 px-4 py-4 backdrop-blur-md sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <button type="button" data-scope-filter="all" class="scope-tab is-active">Tümü</button>
            @foreach($scopeCategories as $cat)
                <button type="button" data-scope-filter="{{ $cat['slug'] }}" class="scope-tab">
                    <span aria-hidden="true">{{ $cat['icon'] }}</span> {{ $cat['title'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ============== SECTION 3 · SCOPE DATA CARDS ============== --}}
    <section class="pb-6">
        @foreach($scopeCategories as $cat)
            <div data-scope-block data-cat="{{ $cat['slug'] }}" id="{{ $cat['slug'] }}" class="mb-12 scroll-mt-40">
                <div class="mb-5 flex flex-wrap items-center gap-3 border-b-2 border-slate-200 pb-3">
                    <span class="text-2xl leading-none" aria-hidden="true">{{ $cat['icon'] }}</span>
                    <h2 class="text-xl font-extrabold text-slate-900 lg:text-2xl">{{ $cat['title'] }} Kalibrasyonları</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ count($cat['groups']) }} cihaz grubu kapsamda
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach($cat['groups'] as $group)
                        @php($sum = $groupSummary($group))
                        <details data-scope-card id="{{ $group['id'] }}"
                                 class="group flex flex-col rounded-xl border border-slate-200 bg-white p-4 transition-all hover:border-teal-500 open:md:col-span-2">
                            <summary class="flex cursor-pointer list-none flex-col gap-3">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-base font-semibold text-slate-900">{{ $group['title'] }}</span>
                                    <span class="flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                        TÜRKAK Akredite
                                    </span>
                                </div>
                                <div class="space-y-1 text-xs text-slate-500">
                                    @if($sum['range'])
                                        <div><span class="text-slate-400">Ölçüm Aralığı:</span> <span class="font-medium text-slate-700">{{ $sum['range'] }}</span></div>
                                    @endif
                                    @if($sum['u'])
                                        <div><span class="text-slate-400">Genişletilmiş Belirsizlik (k=2):</span> <span class="font-medium text-slate-700">{{ $sum['u'] }}</span></div>
                                    @endif
                                    <div class="text-slate-400">{{ count($group['rows']) }} ölçüm satırı · detay için aç</div>
                                </div>
                                <span class="flex items-center gap-1 self-end text-xs font-bold text-teal-600">
                                    Tüm aralıkları gör
                                    <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </span>
                            </summary>

                            <div class="mt-4 border-t border-slate-100 pt-4">
                                <div class="overflow-x-auto rounded-lg border border-slate-200">
                                    <table class="w-full border-collapse text-sm">
                                        <thead>
                                            <tr class="bg-slate-50 text-left">
                                                @foreach($group['columns'] as $col)
                                                    <th class="whitespace-nowrap border-b border-slate-200 px-3 py-2 font-bold text-slate-900">{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group['rows'] as $row)
                                                <tr class="odd:bg-white even:bg-slate-50/60">
                                                    @foreach($row as $cell)
                                                        <td class="whitespace-nowrap border-b border-slate-100 px-3 py-2 text-slate-700">
                                                            @if($cell === '—')<span class="text-slate-400">—</span>@else{{ $cell }}@endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <a href="{{ route('quote', ['source_type' => 'service', 'source_name' => 'Kapsam: ' . $group['title']]) }}"
                                   class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:underline">
                                    Kapsam İçin Teklif Al
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </a>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach

        <p data-scope-empty hidden class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
            Aramanızla eşleşen cihaz grubu bulunamadı. Farklı bir terim deneyin.
        </p>

        <p class="mt-6 border-t border-slate-200 pt-5 text-xs leading-relaxed text-slate-500">
            <span aria-hidden="true">*</span> {!! $scopeNote !!}
        </p>
    </section>

    {{-- ============== SECTION 4 · OUT-OF-SCOPE SUPPORT ============== --}}
    <section class="my-6 rounded-2xl border border-slate-200 bg-white p-6 lg:p-8">
        <div class="flex flex-col items-start justify-between gap-5 md:flex-row md:items-center">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Aradığınız Cihazı Listede Bulamadınız mı?</h2>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">
                    Akreditasyon kapsamımız dışındaki özel ölçüm cihazlarınız ve özel projeleriniz için teknik ekibimizle iletişime geçebilirsiniz.
                </p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-3">
                <a href="{{ $genericQuoteCta['whatsapp_url'] }}" target="_blank" rel="noopener"
                   class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-[#25D366] px-5 text-sm font-bold text-white transition hover:bg-[#1fb85a]">
                    <svg class="h-4 w-4" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M16 3C9.4 3 4 8.3 4 15c0 2.3.7 4.4 1.8 6.3L4 29l7.9-1.8A12 12 0 0 0 16 27c6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.6c-1.9 0-3.7-.5-5.3-1.4l-.4-.2-4.6 1 .9-4.5-.3-.4A9.5 9.5 0 0 1 6.5 15c0-5.3 4.3-9.5 9.5-9.5s9.5 4.3 9.5 9.5-4.2 9.6-9.5 9.6z"/></svg>
                    WhatsApp ile Sorun
                </a>
                <a href="{{ $genericQuoteCta['quote_url'] }}"
                   class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-bold text-slate-700 transition hover:border-teal-500 hover:text-teal-700">
                    Özel Cihaz Listesi İletin
                </a>
            </div>
        </div>
    </section>

    {{-- ============== SECTION 5 · BOTTOM CTA ============== --}}
    <section class="my-10 flex flex-col items-center justify-between gap-6 rounded-2xl bg-slate-900 p-8 text-white shadow-xl md:flex-row">
        <div>
            <h2 class="text-2xl font-bold">Kalibrasyon İhtiyaçlarınız İçin Hızlı Fiyat Alın</h2>
            <p class="mt-1 text-sm text-slate-300">Toplu cihaz listenizi iletin, uzman ekibimiz kapsam ve teklif çalışmasını aynı gün iletsin.</p>
        </div>
        <a href="{{ $genericQuoteCta['quote_url'] }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-md transition hover:bg-teal-500">
            Cihaz Listesi İle Teklif İsteyin
        </a>
    </section>

</div>
</div>
@endsection
