<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * seo_entries tablosundaki zayıf (tek kelime / marka eki olmayan) title'ları
 * anahtar kelime + marka içeren sürümlerle günceller. Sadece hâlâ zayıf olanlara dokunur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('seo_entries')) {
            return;
        }

        $fix = [
            '/hizmetler' => [
                'title' => 'Akredite Kalibrasyon Hizmetleri | MTA Endüstri',
                'description' => 'TÜRKAK akredite (ISO/IEC 17025) basınç, sıcaklık, tork, devir, kütle-terazi ve hacim kalibrasyonu hizmetleri. Kapsam ve teklif için MTA Endüstri.',
            ],
            '/teknik-servis' => [
                'title' => 'Laboratuvar Cihazları Teknik Servis | MTA Endüstri',
                'description' => 'Laboratuvar ve analiz cihazlarında arıza tespiti, bakım, onarım ve kalibrasyon öncesi teknik servis. Çok markalı; Bahco tork ürünlerinde yetkili servis.',
            ],
            '/urunler' => [
                'title' => 'Laboratuvar Cihazları ve Ölçüm Ekipmanları Kataloğu | MTA Endüstri',
                'description' => 'Hassas terazi, pH metre, refraktometre, viskozimetre, nem tayin, titratör, etüv, inkübatör ve karıştırıcıları marka ve teknik özelliğe göre inceleyin; teklif alın.',
            ],
            '/markalar' => [
                'title' => 'Laboratuvar Cihazları Markaları | MTA Endüstri',
                'description' => 'MTA Endüstri kataloğundaki laboratuvar cihazı markalarını kategori, ürün grubu ve teknik teklif akışıyla inceleyin.',
            ],
            '/blog' => [
                'title' => 'Blog | Laboratuvar Cihazları ve Kalibrasyon Rehberleri | MTA Endüstri',
                'description' => 'Kalibrasyon, cihaz seçimi, teknik servis ve ölçüm güvenilirliği üzerine MTA Endüstri teknik editör yazıları.',
            ],
            '/bilgi-merkezi' => [
                'title' => 'Bilgi Merkezi | Kalibrasyon ve Cihaz Rehberleri | MTA Endüstri',
                'description' => 'Kalibrasyon nedir, cihaz nasıl seçilir, teknik servis ne zaman gerekir? Kullanıcı sorusundan başlayan teknik rehber içerikleri.',
            ],
            '/hakkimizda' => [
                'title' => 'MTA Endüstri Hakkında | Laboratuvar Cihazları ve Kalibrasyon',
                'description' => '2010\'dan bu yana laboratuvar cihazları tedariği, akredite kalibrasyon ve teknik servis alanında kalite kontrol ve AR-GE laboratuvarlarına çözüm.',
            ],
            '/sertifikalar' => [
                'title' => 'Sertifikalar ve Kurumsal Belgeler | MTA Endüstri',
                'description' => 'MTA Endüstri kurumsal belgeleri, kalite süreç dokümanları ve akreditasyon bilgileri.',
            ],
            '/referanslar' => [
                'title' => 'Referanslar ve Uygulama Alanları | MTA Endüstri',
                'description' => 'MTA Endüstri laboratuvar cihazları, kalibrasyon ve teknik servis hizmetlerinin kullanıldığı sektör ve uygulama alanları.',
            ],
            '/iletisim' => [
                'title' => 'İletişim ve Teklif Talebi | MTA Endüstri',
                'description' => 'Kalibrasyon, laboratuvar cihazı ve teknik servis talepleriniz için MTA Endüstri iletişim bilgileri, form ve konum.',
            ],
        ];

        foreach ($fix as $path => $data) {
            $row = DB::table('seo_entries')->where('path', $path)->first();
            if (! $row) {
                continue;
            }
            // Zaten " | MTA Endüstri" içeriyor ve 30+ karakterse dokunma (elle iyileştirilmiş olabilir)
            if (str_contains((string) $row->title, '| MTA Endüstri') && mb_strlen((string) $row->title) > 30) {
                continue;
            }
            DB::table('seo_entries')->where('path', $path)->update([
                'title' => $data['title'],
                'description' => blank($row->description) ? $data['description'] : $row->description,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Geri alınmaz (içerik iyileştirmesi).
    }
};
