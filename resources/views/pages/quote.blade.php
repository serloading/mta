@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO + FORM ============ --}}
    <section class="my-6 overflow-hidden rounded-3xl bg-slate-900 text-white">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            {{-- Sol: anlatım --}}
            <div class="p-8 lg:col-span-6 lg:p-12">
                <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
                    <span>Teklif Al</span>
                </nav>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">Hızlı Fiyatlandırma</p>
                <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $pageSeo['h1'] ?? 'Teklif Al' }}</h1>
                <p class="mt-3 max-w-md text-sm text-slate-300">
                    Ürün, kalibrasyon hizmeti veya teknik servis ihtiyacınızı iletin; teknik ekibimiz kapsamı netleştirip
                    24 saat içinde fiyat ve termin bilgisiyle dönüş yapsın.
                </p>

                <ul class="mt-8 space-y-3 text-sm">
                    @foreach([
                        'TÜRKAK akredite kalibrasyon (ISO/IEC 17025)',
                        'Yetkili distribütör ürün tedariki ve garanti',
                        'Yerinde / laboratuvarda servis seçenekleri',
                        'Kurumsal fatura ve sözleşmeli hizmet',
                    ] as $item)
                        <li class="flex items-start gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                            <span class="text-slate-300">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-8 flex flex-wrap gap-3 text-xs text-slate-400">
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-white/15 px-3 py-2 font-medium text-white transition hover:bg-white/5">
                        İletişim bilgileri
                    </a>
                    <a href="{{ route('scope') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-white/15 px-3 py-2 font-medium text-white transition hover:bg-white/5">
                        Kalibrasyon kapsamı
                    </a>
                </div>
            </div>

            {{-- Sağ: form --}}
            <div class="bg-slate-50 p-6 text-slate-900 lg:col-span-6 lg:p-10">
                @include('partials.lead-form', [
                    'leadContext' => $leadContext,
                    'formAction' => $formAction,
                    'formTitle' => 'Teklif Formu',
                    'formNote' => 'Zorunlu alanları doldurmanız yeterli; kapsamı birlikte netleştiririz.',
                ])
            </div>
        </div>
    </section>

    {{-- ============ GÜVEN ŞERİDİ ============ --}}
    <section class="my-10 grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach([
            ['24 saat', 'içinde ilk dönüş'],
            ['17025', 'akredite kalibrasyon'],
            ['500+', 'kurumsal referans'],
            ['Türkiye geneli', 'yerinde servis'],
        ] as $stat)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center">
                <p class="text-xl font-extrabold text-teal-700">{{ $stat[0] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $stat[1] }}</p>
            </div>
        @endforeach
    </section>

</div>
</div>
@endsection
