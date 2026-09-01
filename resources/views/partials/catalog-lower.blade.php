{{--
    Lower content block for brand & category catalog pages.
    Expects:
      $seo        : $brandSeo | $categorySeo  (sections[], support_links[], faq[], cta{title,text,button})
      $entityName : brand or category name
      $quoteUrl   : quote route url
      $chips      : Collection of ['label' => , 'url' => ]           (related groups / brands)
      $brandCards : Collection|null of ['name','slug','logo','url']  (category pages only)
      $brandCardsTitle : string|null
--}}
@php
    $steps = [
        ['no' => '01', 'title' => 'Kullanım Alanı & Seçim Kriterleri', 'fallback' => 'Cihaz seçiminde ölçüm aralığı, hassasiyet, numune tipi, kapasite ve laboratuvar koşulları birlikte değerlendirilir.'],
        ['no' => '02', 'title' => 'Kalibrasyon & Servis Gereksinimi', 'fallback' => 'Periyodik kalibrasyon, akreditasyon kapsamı, yedek parça erişimi ve teknik servis desteği satın alma kararının parçasıdır.'],
        ['no' => '03', 'title' => 'Resmi Tedarik & Teklif Süreci', 'fallback' => 'Talebiniz teknik ekip tarafından değerlendirilir; marka, model, adet ve kullanım alanı bilgisiyle en uygun çözüm sunulur.'],
    ];
    $seoSections = collect($seo['sections'] ?? [])->values();
@endphp

<div class="catalog-lower">
    <div class="clower-shell">

        {{-- ===== A. Teknik Alım & Seçim Rehberi ===== --}}
        <section class="clower-guide">
            <div class="clower-guide-intro">
                <span class="clower-eyebrow">Rehber</span>
                <h2>Teknik Alım &amp; Seçim Rehberi</h2>
                <p>{{ $entityName }} ürünlerini değerlendirirken teknik uygunluk, servis ilişkisi ve tedarik akışını birlikte ele alın.</p>
            </div>
            <ol class="clower-steps">
                @foreach($steps as $i => $step)
                    <li class="clower-step">
                        <span class="clower-step-no">{{ $step['no'] }}</span>
                        <div>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $seoSections[$i]['text'] ?? $step['fallback'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>

        {{-- ===== B. İlgili Ürün Grupları & Ekipmanlar ===== --}}
        @if($chips->isNotEmpty())
            <section class="clower-tags">
                <h2>İlgili Ürün Grupları &amp; Ekipmanlar</h2>
                <div class="clower-chiprow">
                    @foreach($chips as $chip)
                        <a class="clower-chip" href="{{ $chip['url'] }}">{{ $chip['label'] }}</a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ===== C. İlgili Markalar ===== --}}
        @if(! empty($brandCards) && $brandCards->isNotEmpty())
            <section class="clower-brands">
                <h2 class="clower-center">{{ $brandCardsTitle ?? $entityName . ' İçin Öne Çıkan Markalar' }}</h2>
                <div class="clower-brand-grid">
                    @foreach($brandCards as $bc)
                        <a class="clower-brand-card" href="{{ $bc['url'] }}">
                            @if(! empty($bc['logo']))
                                <img src="{{ asset($bc['logo']) }}" alt="{{ $bc['name'] }} logosu" loading="lazy">
                            @else
                                <span class="clower-brand-fallback">{{ $bc['name'] }}</span>
                            @endif
                            <span class="clower-brand-name">{{ $bc['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ===== D. SSS ===== --}}
        @if(! empty($seo['faq']))
            <section class="clower-faq">
                <div class="clower-faq-head">
                    <span class="clower-eyebrow">SSS</span>
                    <h2>Sıkça Sorulan Sorular</h2>
                </div>
                <div class="clower-faq-list">
                    @foreach($seo['faq'] as $faq)
                        <details>
                            <summary>
                                <span>{{ $faq['question'] }}</span>
                                <svg class="clower-faq-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ===== E. Bottom CTA Banner ===== --}}
        <section class="clower-cta">
            <div>
                <span class="clower-cta-tag">TEKLİF VE TEKNİK BİLGİ</span>
                <h2>{{ $entityName }} ürünleri için hızlı teklif alın</h2>
                <p>{{ $seo['cta']['text'] ?? 'İhtiyacınız olan cihazı veya model kodunu paylaşın; teknik ekibimiz en kısa sürede teklifinizi hazırlasın.' }}</p>
            </div>
            <a class="clower-cta-btn" href="{{ $quoteUrl }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Teklif Talebi Oluştur
            </a>
        </section>

    </div>
</div>
