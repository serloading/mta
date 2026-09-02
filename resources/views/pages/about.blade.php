@extends('layouts.site')

@section('content')
<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ HERO ============ --}}
    <section class="my-6 rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-teal-400 hover:underline">Ana Sayfa</a><span>/</span>
            <span>Hakkımızda</span>
        </nav>
        <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-400">{{ $pageSeo['eyebrow'] }}</p>
        <h1 class="mt-1 text-3xl font-extrabold lg:text-4xl">{{ $pageSeo['h1'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-300">{{ $pageSeo['hero_text'] }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('contact') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">{{ $pageSeo['primary_cta'] }}</a>
            <a href="{{ route('services.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-white/15 px-6 text-sm font-bold text-white transition hover:bg-white/5">{{ $pageSeo['secondary_cta'] }}</a>
        </div>
    </section>

    {{-- ============ EDİTORYAL + YAN PANEL ============ --}}
    <section class="my-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <article class="legal-prose rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-8 lg:p-10">
            <h2>2010'dan Bugüne Teknik Tedarik ve Destek</h2>
            <p>2010 yılında kurulan MTA Endüstri Ürünleri; laboratuvar, cihaz, ekipman ve sarf malzeme tedariğinde sektörün aranılan kuruluşlarından biri olma yönünde ilerlemektedir.</p>
            <p>Firmamız; kimya, gıda, ilaç, akademik, plastik, petrokimya ve medikal sektörleri başta olmak üzere kalite kontrol ve AR-GE laboratuvarlarında faaliyet gösteren iş ortaklarına sürdürülebilir destek sağlamayı ilke edinmiştir.</p>

            <h2>Markalarla Güçlü İş Birlikleri</h2>
            <p>İşbirliği içerisinde olduğu tedarikçilerine her geçen gün yenilerini ekleyen MTA Endüstri, iş ortaklarına daha iyi ve kaliteli hizmet sunmayı amaçlar.</p>
            <p>Müşteri memnuniyetini en üst noktaya taşımak, dünya markalarıyla kurumsal işbirlikleri yapmak ve sektördeki son gelişmeleri ülkemize taşımak en önemli hedeflerimiz arasındadır.</p>

            <h2>Satış Sonrası Destek Yaklaşımı</h2>
            <p>Ar-Ge ve kalite kontrol laboratuvarları bünyesinde bulunan test cihazları ve bu cihazlara ait sarf malzemeleri temini alanında faaliyet gösteren firmamız; güvenilirliği, satış sonrası desteği ve müşteri eğitim faaliyetlerine verdiği önemle alanında bir marka haline gelmeyi hedeflemektedir.</p>

            @foreach($pageSeo['sections'] as $section)
                <h2>{{ $section['title'] }}</h2>
                <p>{{ $section['text'] }}</p>
            @endforeach

            @if(! empty($pageSeo['support_links']))
                <div class="not-prose mt-6 flex flex-wrap gap-2">
                    @foreach($pageSeo['support_links'] as $link)
                        <a href="{{ $link['url'] }}" class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 no-underline transition hover:bg-teal-50 hover:text-teal-700">{{ $link['anchor'] }}</a>
                    @endforeach
                </div>
            @endif
        </article>

        <aside class="lg:col-span-4">
            <div class="sticky top-24 space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Uzmanlık — Çalışma Alanları</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-700">
                        @foreach($pageSeo['expertise'] as $item)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl bg-slate-900 p-6 text-white">
                    <p class="text-xs font-bold uppercase tracking-wide text-teal-300">İletişim</p>
                    <dl class="mt-3 space-y-3 text-sm">
                        <div><dt class="text-slate-400">Adres</dt><dd class="text-slate-200">{{ config('mta.site.address') }}</dd></div>
                        <div><dt class="text-slate-400">E-posta</dt><dd><a href="mailto:{{ config('mta.site.email') }}" class="text-white hover:text-teal-300">{{ config('mta.site.email') }}</a></dd></div>
                        <div><dt class="text-slate-400">Telefon</dt><dd><a href="tel:{{ preg_replace('/\D+/', '', config('mta.site.phone')) }}" class="text-white hover:text-teal-300">{{ config('mta.site.phone') }}</a></dd></div>
                    </dl>
                    <a href="{{ route('quote') }}" class="mt-5 inline-flex h-11 w-full items-center justify-center rounded-lg bg-teal-600 px-6 text-sm font-bold text-white transition hover:bg-teal-500">Teklif ve bilgi talebi</a>
                </div>
            </div>
        </aside>
    </section>

    {{-- ============ SEKTÖRLER ============ --}}
    @if(! empty($pageSeo['sectors']))
        <section class="my-12">
            <div class="mb-6 text-center">
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-teal-700">Çalıştığımız Alanlar</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">Kalite kontrol ve AR-GE laboratuvarlarına odaklanan yapı</h2>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($pageSeo['sectors'] as $sector)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <span class="font-mono text-sm font-bold text-teal-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $sector }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ ALT CTA ============ --}}
    <section class="my-12 flex flex-col items-start justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row md:items-center lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">{{ $pageSeo['cta']['title'] }}</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-300">{{ $pageSeo['cta']['text'] }}</p>
        </div>
        <a href="{{ route('contact') }}" class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">{{ $pageSeo['cta']['button'] }}</a>
    </section>

</div>
</div>
@endsection
