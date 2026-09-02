@php
    use Illuminate\Support\Str;

    $isCalibration = ($kind ?? 'calibration') === 'calibration';
    $scopeGroups = collect($scopeGroups ?? []);
    $standards = array_values(array_filter((array) ($standards ?? [])));
    $servicedDevices = array_values(array_filter((array) ($servicedDevices ?? [])));
    $relatedProducts = collect($relatedProducts ?? []);
    $faqs = collect($faqs ?? [])->take(6);
    $authorizedService = $authorizedService ?? null;
    $authorizedLogo = $authorizedService && ! empty($authorizedService['logo']) && file_exists(public_path($authorizedService['logo']))
        ? asset($authorizedService['logo'])
        : null;

    $tableRows = $scopeGroups->flatMap(function ($group) use ($standards) {
        return collect($group['items'] ?? [])->map(fn ($item) => [
            'name' => $item['name'] ?? '',
            'range' => $item['range'] ?? 'Talep üzerine netleştirilir',
            'standard' => $item['standard'] ?? ($standards[0] ?? 'Talep üzerine'),
            'group' => $group['title'] ?? '',
        ]);
    });
    if ($tableRows->isEmpty() && $servicedDevices) {
        $tableRows = collect($servicedDevices)->map(fn ($d) => [
            'name' => is_array($d) ? ($d['name'] ?? '') : $d,
            'range' => 'Kapsam netleştirilir',
            'standard' => $standards[0] ?? 'Talep üzerine',
            'group' => '',
        ]);
    }

    $processSteps = [
        ['no' => '01', 'title' => 'Cihaz Kabul & Müşteri Talebi', 'text' => 'Cihaz tipi, marka/model, adet ve talep bilgisi alınır; kayıt açılır.'],
        ['no' => '02', 'title' => 'Ön İnceleme & Arıza Tespiti', 'text' => 'Fiziksel kontrol, ön test ve sapma/arıza değerlendirmesi yapılır.'],
        ['no' => '03', 'title' => $isCalibration ? 'Ölçüm & Analiz' : 'Bakım & Onarım', 'text' => $isCalibration ? 'Referans ekipmanlarla belirlenen noktalarda ölçüm alınır.' : 'Orijinal yedek parça ile onarım, elektrot/prob ve modül işlemleri yapılır.'],
        ['no' => '04', 'title' => $isCalibration ? 'Kalibrasyon & Sertifikalandırma' : 'Kontrol & Kalibrasyon', 'text' => 'Sonuçlar değerlendirilir, uygunluk kararı verilir ve sertifika hazırlanır.'],
        ['no' => '05', 'title' => 'Güvenli Teslimat', 'text' => 'Cihaz ve rapor teslim edilir; sonraki periyot takibi planlanır.'],
    ];
@endphp

<div class="bg-slate-50 text-slate-900">
<div class="mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">

    {{-- ============ SECTION 1 · HERO SPLIT + FORM ============ --}}
    <section class="relative my-6 overflow-hidden rounded-3xl bg-slate-900 p-8 text-white lg:p-12">
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-teal-500/10 blur-3xl"></div>
        <div class="relative grid grid-cols-1 items-center gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <nav class="mb-4 flex flex-wrap items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
                    @foreach($breadcrumb as $crumb)
                        @if(! empty($crumb['url']))<a href="{{ $crumb['url'] }}" class="text-teal-400 hover:underline">{{ $crumb['label'] }}</a><span>/</span>
                        @else<span>{{ $crumb['label'] }}</span>@endif
                    @endforeach
                </nav>
                <span class="mb-3 inline-block rounded-full border border-teal-500/30 bg-teal-500/20 px-3 py-1 font-mono text-xs font-bold text-teal-300">
                    TÜRKAK AKREDİTE / ISO 17025 İZLENEBİLİR
                </span>
                <h1 class="text-3xl font-extrabold leading-tight lg:text-[2.6rem]">{{ $title }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300">
                    {{ $leadText ?: 'Hassas ölçüm cihazlarınız için akredite kalibrasyon, periyodik bakım ve orijinal yedek parça garantili teknik destek.' }}
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach(['TÜRKAK Akredite', 'Orijinal Parça Garantisi', 'Hızlı Sertifikalandırma'] as $badge)
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800/70 px-3 py-1.5 text-xs font-medium text-slate-200">
                            <svg class="h-3.5 w-3.5 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            {{ $badge }}
                        </span>
                    @endforeach
                </div>

                @if($authorizedService)
                    <div class="mt-5 inline-flex items-center gap-3 rounded-xl border border-amber-400/30 bg-white/95 px-4 py-2.5 shadow-lg">
                        @if($authorizedLogo)
                            <img src="{{ $authorizedLogo }}" alt="{{ $authorizedService['brand'] }} logosu" class="h-7 w-auto object-contain">
                        @else
                            <span class="text-sm font-extrabold text-slate-900">{{ $authorizedService['brand'] }}</span>
                        @endif
                        <span class="h-6 w-px bg-slate-200"></span>
                        <span class="text-xs font-bold uppercase tracking-wide text-amber-700">{{ $authorizedService['role'] }}</span>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-5">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 text-slate-900 shadow-2xl">
                    @if(session('lead_success'))
                        <div class="rounded-xl bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                            {{ session('lead_success') }}
                        </div>
                    @else
                        <p class="text-sm font-bold">Hızlı Servis &amp; Kalibrasyon Talebi</p>
                        <p class="mt-1 text-xs text-slate-500">Bilgileri iletin, 24 saat içinde dönüş yapalım.</p>
                        <form method="post" action="{{ $formAction }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="source_type" value="{{ $sourceType }}">
                            <input type="hidden" name="{{ $sourceType === 'technical_service' ? 'technical_service' : 'service' }}" value="{{ $sourceSlug }}">
                            <input type="hidden" name="source_name" value="{{ $title }}">
                            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                            <textarea name="message" rows="2" required placeholder="Cihaz tipi ve adedi (ör. 3 adet dijital manometre)"
                                      class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-teal-500">{{ old('message') }}</textarea>
                            <input type="text" name="name" required placeholder="Firma / Yetkili kişi" value="{{ old('name') }}"
                                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-teal-500">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="tel" name="phone" required placeholder="Telefon" value="{{ old('phone') }}"
                                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-teal-500">
                                <input type="email" name="email" placeholder="E-posta" value="{{ old('email') }}"
                                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-teal-500">
                            </div>
                            @error('name')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            @error('phone')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            <button type="submit" class="h-11 w-full rounded-lg bg-amber-600 text-sm font-bold text-white shadow-md transition-all hover:bg-amber-500">
                                Hızlı Fiyat Teklifi Al
                            </button>
                            <p class="text-center text-[11px] text-slate-400">
                                veya <a href="{{ $quoteCta['whatsapp_url'] }}" target="_blank" rel="noopener" class="font-semibold text-teal-600">WhatsApp ile yazın</a>
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ============ YETKİLİ / MERKEZ SERVİS ============ --}}
    @if($authorizedService)
        <section class="my-8 overflow-hidden rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white">
            <div class="grid grid-cols-1 gap-6 p-8 lg:grid-cols-12 lg:items-center lg:p-10">
                <div class="lg:col-span-4">
                    <div class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                        @if($authorizedLogo)
                            <img src="{{ $authorizedLogo }}" alt="{{ $authorizedService['brand'] }} logosu" class="h-10 w-auto object-contain">
                        @else
                            <span class="text-lg font-extrabold text-slate-900">{{ $authorizedService['brand'] }}</span>
                        @endif
                    </div>
                    <p class="mt-3 text-xs font-bold uppercase tracking-wide text-amber-700">{{ $authorizedService['role'] }}</p>
                </div>
                <div class="lg:col-span-8">
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $authorizedService['brand'] }} {{ $authorizedService['role'] }}</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $authorizedService['blurb'] }}</p>
                    @if(! empty($authorizedService['points']))
                        <ul class="mt-4 grid grid-cols-1 gap-2 text-sm text-slate-700 sm:grid-cols-2">
                            @foreach($authorizedService['points'] as $point)
                                <li class="flex items-start gap-2">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ============ SECTION 2 · NE ZAMAN & HAZIRLIK ============ --}}
    <section class="my-10 grid grid-cols-1 gap-6 md:grid-cols-2">
        @php
            $whenList = $isCalibration
                ? ['Periyodik kalibrasyon zamanı geldiğinde', 'Ölçüm sapması (drift) tespit edildiğinde', 'Tamir veya parça değişimi sonrasında', 'Kalite denetimleri ve akreditasyon öncesinde']
                : ['Cihaz stabil ölçüm yapmadığında / hata verdiğinde', 'Prob, sensör veya elektrot sorunu oluştuğunda', 'Ekran / elektronik kart arızası görüldüğünde', 'Periyodik bakım ve kalite denetimi öncesinde'];
            $prepList = ['Cihazın temizlenmesi ve dekontaminasyonu', 'Aksesuar ve bağlantı parçalarının eksiksiz hazırlanması', 'Arıza / sapma geçmişinin yazılı bildirilmesi', 'Marka, model ve seri numarası bilgisinin iletilmesi'];
        @endphp
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-base font-bold text-slate-900">Hangi Durumlarda {{ $isCalibration ? 'Kalibrasyon' : 'Servis' }} Gereklidir?</h2>
            <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                @foreach($whenList as $li)
                    <li class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        {{ $li }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-base font-bold text-slate-900">{{ $isCalibration ? 'Kalibrasyon' : 'Bakım' }} Öncesi Hazırlık Süreci</h2>
            <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                @foreach($prepList as $li)
                    <li class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        {{ $li }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ============ SECTION 3 · KAPSAM TABLOSU / CİHAZ GRID ============ --}}
    @if($tableRows->isNotEmpty())
        <section class="my-12">
            <h2 class="text-xl font-extrabold text-slate-900 lg:text-2xl">
                {{ $isCalibration ? 'Hizmet Kapsamındaki Cihazlar ve Ölçüm Toleransları' : 'Servis Verilen Cihaz Grupları' }}
            </h2>
            <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3 font-bold">Cihaz Tipi</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">Ölçüm Aralığı</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">Standart / Tolerans</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">Hizmet Türü</th>
                            <th class="px-4 py-3 font-bold">Aksiyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableRows as $row)
                            <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row['range'] }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $row['standard'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $isCalibration ? 'TÜRKAK Kapsamında' : 'Yetkili Servis' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('quote', ['source_type' => $sourceType, 'source_name' => $row['name'] . ' — ' . $title]) }}"
                                       class="whitespace-nowrap text-xs font-bold text-teal-600 hover:underline">Bu Cihaz İçin Teklif Al →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($standards)
                <p class="mt-3 text-xs text-slate-500">Uygulanan standartlar: {{ implode(' · ', $standards) }}</p>
            @endif
        </section>
    @endif

    {{-- ============ SECTION 4 · 5 ADIMLI YATAY SÜREÇ ============ --}}
    <section class="my-12">
        <h2 class="text-xl font-extrabold text-slate-900 lg:text-2xl">Adım Adım Hizmet Akışı</h2>
        <div class="relative mt-6 grid grid-cols-1 gap-4 md:grid-cols-5">
            <div class="pointer-events-none absolute left-0 right-0 top-8 hidden h-px bg-slate-200 md:block"></div>
            @foreach($processSteps as $step)
                <div class="relative rounded-xl border border-slate-200 bg-white p-4 text-center transition-all hover:border-teal-500">
                    <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full bg-teal-600 text-xs font-extrabold text-white">{{ $step['no'] }}</span>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $step['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ SECTION 5 · İLİŞKİLİ CİHAZLAR ============ --}}
    @if($relatedProducts->isNotEmpty())
        <section class="my-12">
            <h2 class="text-xl font-extrabold text-slate-900 lg:text-2xl">Bu Hizmetle İlgili Cihazlar ve Yedek Parçalar</h2>
            <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($relatedProducts as $p)
                    <a href="{{ route('products.show', $p['slug']) }}"
                       class="group flex flex-col rounded-xl border border-slate-200 bg-white p-3 transition-all hover:border-teal-500 hover:shadow-lg">
                        <div class="flex h-28 items-center justify-center overflow-hidden rounded-lg border border-slate-100 bg-white p-3">
                            @if(! empty($p['image']))
                                <img src="{{ img_url($p['image']) }}" alt="{{ $p['image_alt'] ?? $p['name'] }}" class="h-auto max-h-full w-auto max-w-full object-contain" loading="lazy">
                            @else
                                <svg class="h-9 w-9 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="13" r="3"/></svg>
                            @endif
                        </div>
                        <p class="mt-2 text-[11px] font-bold uppercase tracking-wider text-teal-700">{{ $p['brand'] ?? '' }}</p>
                        <h3 class="line-clamp-2 text-xs font-semibold text-slate-900 group-hover:text-teal-600">{{ $p['name'] }}</h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ SECTION 6 · SSS ============ --}}
    @if($faqs->isNotEmpty())
        <section class="mx-auto my-10 max-w-3xl">
            <h2 class="text-center text-xl font-extrabold text-slate-900 lg:text-2xl">Sıkça Sorulan Sorular</h2>
            <div class="mt-6 space-y-3">
                @foreach($faqs as $faq)
                    <details class="group rounded-xl border border-slate-200 bg-white p-4">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-sm font-semibold text-slate-900">
                            {{ $faq['question'] }}
                            <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <p class="mt-3 text-sm text-slate-600">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ SECTION 7 · ALT CTA ============ --}}
    <section class="my-10 flex flex-col items-center justify-between gap-6 rounded-3xl bg-slate-900 p-8 text-white shadow-2xl md:flex-row lg:p-12">
        <div>
            <h2 class="text-2xl font-bold">{{ $cta['title'] ?? 'Cihazlarınızın Bakım veya Kalibrasyon Zamanı Geldi mi?' }}</h2>
            <p class="mt-1 text-sm text-slate-300">{{ $cta['note'] ?? $cta['text'] ?? 'Cihaz listenizi iletin, uzman teknik ekibimiz 24 saat içinde teklifinizi hazırlasın.' }}</p>
        </div>
        <a href="{{ $quoteCta['quote_url'] }}"
           class="inline-flex h-12 shrink-0 items-center justify-center rounded-lg bg-teal-600 px-8 text-sm font-bold text-white shadow-lg transition hover:bg-teal-500">
            {{ $isCalibration ? 'Toplu Kalibrasyon Teklifi Al' : 'Toplu Servis Teklifi Al' }}
        </a>
    </section>

</div>
</div>
