<?php

/*
|--------------------------------------------------------------------------
| Yasal metinler — /gizlilik-politikasi, /cerez-politikasi, /kvkk
|--------------------------------------------------------------------------
| Her kayıt: title, updated, lead, sections[] ({ h, body }).
| body ham HTML'dir (<p>, <ul><li>, <table> vb.).
*/

$company = 'MTA Endüstri Ürünleri Pazarlama Sanayi ve Ticaret Limited Şirketi';

return [

    'gizlilik-politikasi' => [
        'title' => 'Gizlilik Politikası',
        'updated' => '2026-09-02',
        'lead' => $company . ' ("Şirket", "biz") olarak, mtaend.com web sitesini ("Site") ziyaret etmeniz ve kullanmanız sırasında elde edilen kişisel verilerinizin güvenliğine önem veriyoruz. Bu Gizlilik Politikası, Site üzerinden hangi verilerin, hangi amaçlarla toplandığını ve nasıl korunduğunu açıklamak amacıyla hazırlanmıştır.',
        'sections' => [
            ['h' => '1. Toplanan Veriler', 'body' => '<p>Siteyi kullanımınız sırasında aşağıdaki türde veriler toplanabilir:</p><ul><li><strong>Sizin doğrudan sağladığınız veriler:</strong> İletişim formu, teklif talebi veya e-posta yoluyla paylaştığınız ad-soyad, telefon numarası, e-posta adresi, şirket bilgisi ve mesaj içeriği.</li><li><strong>Otomatik olarak toplanan veriler:</strong> IP adresi, tarayıcı türü, ziyaret edilen sayfalar, ziyaret süresi ve tarihi gibi teknik veriler (çerezler ve benzer teknolojiler aracılığıyla — ayrıntılar için Çerez Politikamıza bakınız).</li></ul>'],
            ['h' => '2. Verilerin Kullanım Amacı', 'body' => '<p>Toplanan veriler; taleplerinizin yanıtlanması, teklif ve sipariş süreçlerinin yürütülmesi, müşteri ilişkilerinin sürdürülmesi, Sitenin işlevselliğinin ve kullanıcı deneyiminin geliştirilmesi, güvenliğin sağlanması ve yasal yükümlülüklerin yerine getirilmesi amacıyla kullanılır.</p>'],
            ['h' => '3. Verilerin Paylaşılması', 'body' => '<p>Kişisel verileriniz, yasal zorunluluklar dışında ve açık rızanız olmaksızın üçüncü kişilerle paylaşılmaz. Site altyapısı, e-posta veya analiz hizmeti aldığımız tedarikçilerle (barındırma/hosting sağlayıcıları, e-posta servisleri, analiz araçları gibi) yalnızca hizmetin gerektirdiği ölçüde ve gizlilik yükümlülükleri çerçevesinde veri paylaşımı yapılabilir.</p>'],
            ['h' => '4. Veri Güvenliği', 'body' => '<p>Kişisel verilerinizin yetkisiz erişime, kayba, kötüye kullanıma veya ifşaya karşı korunması için makul teknik ve idari tedbirler alınmaktadır.</p>'],
            ['h' => '5. Haklarınız', 'body' => '<p>6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamındaki haklarınız (bilgi talep etme, düzeltme, silme, itiraz vb.) hakkında detaylı bilgi için <a href="/kvkk">KVKK Aydınlatma Metni</a>&rsquo;ni inceleyebilirsiniz.</p>'],
            ['h' => '6. Üçüncü Taraf Bağlantıları', 'body' => '<p>Site, üçüncü taraflara ait web sitelerine bağlantılar içerebilir. Bu sitelerin gizlilik uygulamalarından Şirketimiz sorumlu değildir; ilgili sitelerin kendi gizlilik politikalarını incelemenizi öneririz.</p>'],
            ['h' => '7. Politikada Değişiklik', 'body' => '<p>Bu Gizlilik Politikası, yasal düzenlemeler veya iş süreçlerimizdeki değişiklikler doğrultusunda güncellenebilir. Güncel metin her zaman bu sayfada yayınlanır.</p>'],
            ['h' => '8. İletişim', 'body' => '<p>Gizlilik uygulamalarımıza ilişkin sorularınız için bizimle <a href="/iletisim">iletişim</a> sayfası üzerinden iletişime geçebilirsiniz.</p>'],
        ],
    ],

    'cerez-politikasi' => [
        'title' => 'Çerez (Cookie) Politikası',
        'updated' => '2026-09-02',
        'lead' => 'Bu Çerez Politikası, ' . $company . ' tarafından işletilen mtaend.com web sitesini ("Site") ziyaretiniz sırasında kullanılan çerezler (cookie) ve benzer teknolojiler hakkında sizi bilgilendirmek amacıyla hazırlanmıştır.',
        'sections' => [
            ['h' => '1. Çerez Nedir?', 'body' => '<p>Çerezler, ziyaret ettiğiniz web siteleri tarafından tarayıcınız aracılığıyla cihazınıza (bilgisayar, telefon, tablet) kaydedilen küçük metin dosyalarıdır. Çerezler; Sitenin düzgün çalışmasını sağlamak, tercihlerinizi hatırlamak ve site kullanımını analiz etmek gibi amaçlarla kullanılır.</p>'],
            ['h' => '2. Kullanılan Çerez Türleri', 'body' => '<div class="legal-table-wrap"><table><thead><tr><th>Çerez Türü</th><th>Amaç</th><th>Zorunluluk</th></tr></thead><tbody><tr><td>Zorunlu (Teknik) Çerezler</td><td>Sitenin temel işlevlerinin (sayfa gezinme, form gönderimi vb.) çalışması için gereklidir.</td><td>Devre dışı bırakılamaz.</td></tr><tr><td>Performans / Analitik Çerezler</td><td>Ziyaretçi sayısı, hangi sayfaların ziyaret edildiği gibi istatistiksel bilgilerin toplanmasını sağlar (örn. Google Analytics).</td><td>Onayınıza tabidir.</td></tr><tr><td>İşlevsellik Çerezleri</td><td>Dil tercihi gibi seçimlerinizin hatırlanmasını sağlar.</td><td>Onayınıza tabidir.</td></tr><tr><td>Pazarlama / Hedefleme Çerezleri</td><td>İlgi alanlarınıza yönelik içerik veya reklam gösterimi amacıyla kullanılabilir.</td><td>Onayınıza tabidir.</td></tr></tbody></table></div><p><em>Sitede fiilen hangi çerezlerin/araçların kullanıldığına göre bu tablo güncellenir.</em></p>'],
            ['h' => '3. Çerezlerin Yönetimi', 'body' => '<p>Tarayıcı ayarlarınız üzerinden çerezleri kabul edebilir, reddedebilir veya silebilirsiniz. Zorunlu çerezler dışındaki çerezleri devre dışı bırakmanız Sitenin bazı işlevlerini etkileyebilir. Çoğu tarayıcı, çerez tercihlerini yönetmenize imkan tanıyan ayarlar sunar; bu ayarlara tarayıcınızın "Ayarlar" veya "Tercihler" menüsünden ulaşabilirsiniz.</p>'],
            ['h' => '4. Onay', 'body' => '<p>Siteyi ilk ziyaretinizde ekranda beliren çerez bildirimi üzerinden zorunlu olmayan çerezlere onay verip vermeyeceğinizi seçebilirsiniz. Onayınızı dilediğiniz zaman geri alabilir veya tercihlerinizi güncelleyebilirsiniz.</p>'],
            ['h' => '5. Kişisel Verilerin Korunması', 'body' => '<p>Çerezler aracılığıyla elde edilen veriler, 6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında işlenmektedir. Detaylı bilgi için <a href="/kvkk">KVKK Aydınlatma Metni</a>&rsquo;ni inceleyebilirsiniz.</p>'],
            ['h' => '6. İletişim', 'body' => '<p>Çerez uygulamalarımıza ilişkin sorularınız için <a href="/iletisim">iletişim</a> sayfası üzerinden bizimle iletişime geçebilirsiniz.</p>'],
        ],
    ],

    'kvkk' => [
        'title' => 'KVKK Aydınlatma Metni',
        'updated' => '2026-09-02',
        'lead' => strtoupper($company) . ' ("Şirket") olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK" veya "Kanun") uyarınca veri sorumlusu sıfatıyla, kişisel verilerinizin güvenliğine önem veriyoruz. Bu Aydınlatma Metni, Kanun&rsquo;un 10. maddesi ile Aydınlatma Yükümlülüğünün Yerine Getirilmesinde Uyulacak Usul ve Esaslar Hakkında Tebliğ kapsamında hazırlanmıştır.',
        'sections' => [
            ['h' => '1. Veri Sorumlusunun Kimliği', 'body' => '<p><strong>Unvan:</strong> ' . $company . '<br><strong>Web sitesi:</strong> mtaend.com</p>'],
            ['h' => '2. Kişisel Verilerinizin İşlenme Amacı', 'body' => '<p>Şirketimizle telefon, e-posta, web sitesi, sosyal medya veya diğer iletişim kanalları aracılığıyla kurduğunuz iletişim kapsamında elde edilen kişisel verileriniz (ad-soyad, telefon numarası, e-posta adresi, çağrı ses kaydı, yazışma içeriği ve varsa şirket/unvan bilgileri gibi) aşağıdaki amaçlarla işlenmektedir:</p><ul><li>Talep, öneri ve şikâyetlerinizin alınması, değerlendirilmesi ve sonuçlandırılması,</li><li>Müşteri ilişkileri ve iletişim süreçlerinin yürütülmesi,</li><li>Sipariş, teklif, satış ve satış sonrası hizmet süreçlerinin yürütülmesi,</li><li>Hizmet kalitesinin ölçülmesi, geliştirilmesi ve iç denetim/eğitim faaliyetlerinin yürütülmesi,</li><li>Çağrı kayıtlarının, olası uyuşmazlıklarda delil teşkil etmesi amacıyla saklanması,</li><li>Şirketimizin yasal yükümlülüklerinin yerine getirilmesi ve yetkili kamu kurum/kuruluşlarının taleplerinin karşılanması,</li><li>Bilgi güvenliği süreçlerinin yürütülmesi.</li></ul>'],
            ['h' => '3. Toplanma Yöntemi ve Hukuki Sebebi', 'body' => '<p>Kişisel verileriniz; telefon görüşmeleri (ses kaydı dahil), e-posta, web sitesi iletişim formları, sosyal medya kanalları ve fiziki/dijital yazışmalar aracılığıyla otomatik veya kısmen otomatik yollarla toplanmaktadır.</p><p>Kişisel verileriniz, KVKK&rsquo;nın 5. maddesinde yer alan; bir sözleşmenin kurulması veya ifasıyla doğrudan ilgili olması, Şirketimizin hukuki yükümlülüğünü yerine getirebilmesi için zorunlu olması ve ilgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla Şirketimizin meşru menfaatleri için veri işlenmesinin zorunlu olması hukuki sebeplerine dayanılarak işlenmektedir. Çağrı kayıtları bakımından ayrıca görüşmenin başında sözlü bilgilendirme yapılmaktadır.</p>'],
            ['h' => '4. Kişisel Verilerin Aktarılması', 'body' => '<p>Toplanan kişisel verileriniz; yasal yükümlülüklerimiz kapsamında yetkili kamu kurum ve kuruluşlarına, hizmet aldığımız tedarikçilere (örneğin çağrı merkezi altyapı sağlayıcıları, bilgi işlem/bulut hizmeti sağlayıcıları), hukuki uyuşmazlık halinde ilgili yargı mercilerine ve avukatlarımıza, KVKK&rsquo;nın 8. ve 9. maddelerinde belirtilen şartlar çerçevesinde aktarılabilecektir.</p>'],
            ['h' => '5. Kişisel Verilerin Saklanma Süresi', 'body' => '<p>Kişisel verileriniz, işlenme amacının gerektirdiği süre boyunca ve ilgili mevzuatta öngörülen zamanaşımı süreleri (özellikle Türk Ticaret Kanunu ve Türk Borçlar Kanunu kapsamındaki genel zamanaşımı süreleri) dikkate alınarak saklanmaktadır. Çağrı kayıtları, olası uyuşmazlıklarda delil teşkil edebileceğinden makul bir süre boyunca saklanır ve bu sürenin sonunda silinir, yok edilir veya anonim hale getirilir.</p>'],
            ['h' => '6. İlgili Kişinin (Veri Sahibinin) Hakları', 'body' => '<p>KVKK&rsquo;nın 11. maddesi uyarınca Şirketimize başvurarak aşağıdaki haklara sahipsiniz:</p><ul><li>Kişisel verilerinizin işlenip işlenmediğini öğrenme,</li><li>İşlenmişse buna ilişkin bilgi talep etme,</li><li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme,</li><li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme,</li><li>Eksik veya yanlış işlenmişse düzeltilmesini isteme,</li><li>KVKK&rsquo;da öngörülen şartlarla silinmesini veya yok edilmesini isteme,</li><li>Düzeltme, silme ve yok etme işlemlerinin aktarıldığı üçüncü kişilere bildirilmesini isteme,</li><li>Münhasıran otomatik sistemlerle analiz edilmesi suretiyle aleyhinize bir sonuç çıkmasına itiraz etme,</li><li>Kanuna aykırı işlenme sebebiyle zarara uğramanız hâlinde zararın giderilmesini talep etme.</li></ul>'],
            ['h' => '7. Başvuru Yöntemi', 'body' => '<p>Yukarıda sayılan haklarınıza ilişkin taleplerinizi, kimliğinizi tevsik edici belgeler ile birlikte yazılı olarak veya Kişisel Verileri Koruma Kurulu tarafından belirlenen diğer yöntemlerle Şirketimize iletebilirsiniz. Başvurunuz, niteliğine göre en kısa sürede ve en geç 30 (otuz) gün içinde ücretsiz olarak sonuçlandırılacaktır; ancak işlemin ayrıca bir maliyet gerektirmesi hâlinde Kurul tarafından belirlenen tarifedeki ücret alınabilir.</p>'],
        ],
    ],

];
