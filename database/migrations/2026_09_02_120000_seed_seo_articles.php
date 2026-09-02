<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Yüksek hacimli SEO anahtar kelimeleri için bilgi merkezi yazıları.
 * Gövde bölümleri SiteController::articleSeoContent() tarafından sağlanır;
 * burada yalnızca yayın kaydı (meta) oluşturulur. Slug bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('articles')) {
            return;
        }

        $now = now();
        $rows = [
            [
                'title' => 'Kalibrasyon Nedir?',
                'slug' => 'kalibrasyon-nedir',
                'category' => 'Kalibrasyon Rehberleri',
                'category_slug' => 'kalibrasyon-rehberleri',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '6 dk',
                'excerpt' => 'Kalibrasyon nedir, neden yapılır, hangi cihazlarda uygulanır, kalibrasyon ile ayar ve doğrulama arasındaki fark nedir ve kalibrasyon periyodu nasıl belirlenir?',
                'body' => 'Kalibrasyon, bir ölçüm cihazının gösterdiği değerin izlenebilir bir referans ile karşılaştırılması, sapmanın belirlenmesi ve sonuçların belirsizlik bilgisiyle birlikte bir sertifikada raporlanması işlemidir.',
                'seo_title' => 'Kalibrasyon Nedir? Tanımı, Süreci ve Periyodu | MTA Endüstri',
                'meta_description' => 'Kalibrasyon nedir, neden yapılır, hangi cihazlarda uygulanır? Kalibrasyon ile ayar/doğrulama farkı, izlenebilirlik, ölçüm belirsizliği ve kalibrasyon periyodu bu rehberde.',
            ],
            [
                'title' => 'Kalibrasyon Sertifikası Nedir ve Neleri İçerir?',
                'slug' => 'kalibrasyon-sertifikasi',
                'category' => 'Kalibrasyon Rehberleri',
                'category_slug' => 'kalibrasyon-rehberleri',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '5 dk',
                'excerpt' => 'Kalibrasyon sertifikası (kalibrasyon raporu) nedir, hangi bilgileri içerir, TÜRKAK akredite sertifika ile akreditasyonsuz rapor arasındaki fark nedir ve geçerlilik süresi nasıl belirlenir?',
                'body' => 'Kalibrasyon sertifikası; kalibre edilen cihazı, uygulanan yöntemi, ölçüm noktalarını, bulunan sapmaları ve genişletilmiş ölçüm belirsizliğini (k=2) izlenebilirlik bilgisiyle birlikte belgeleyen resmi rapordur.',
                'seo_title' => 'Kalibrasyon Sertifikası Nedir? İçeriği ve Okunması | MTA Endüstri',
                'meta_description' => 'Kalibrasyon sertifikası (kalibrasyon raporu) hangi bilgileri içerir, nasıl okunur, TÜRKAK akredite sertifika ile akreditasyonsuz rapor farkı ve geçerlilik süresi.',
            ],
            [
                'title' => 'Analitik Terazi Nedir?',
                'slug' => 'analitik-terazi-nedir',
                'category' => 'Satın Alma Rehberleri',
                'category_slug' => 'satin-alma-rehberleri',
                'author' => 'MTA Teknik Editör',
                'reading_time' => '5 dk',
                'excerpt' => 'Analitik terazi nedir, hassas teraziden farkı nedir, okunabilirlik ve kapasite değerleri ne anlama gelir, analitik terazi seçerken ve kurarken nelere dikkat edilir?',
                'body' => 'Analitik terazi, tipik olarak 0,1 mg (0,0001 g) okunabilirliğe ve rüzgarlık kabinine sahip, düşük miktarlı numunelerin yüksek doğrulukla tartılması için kullanılan laboratuvar terazisidir.',
                'seo_title' => 'Analitik Terazi Nedir? Hassas Teraziden Farkı | MTA Endüstri',
                'meta_description' => 'Analitik terazi nedir, hassas teraziden farkı nedir, 0,1 mg okunabilirlik ne demek, analitik terazi seçerken ve kurarken nelere dikkat edilir?',
            ],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('articles')->where('slug', $row['slug'])->exists();
            if ($exists) {
                continue;
            }
            DB::table('articles')->insert(array_merge($row, [
                'status' => 'published',
                'robots' => 'index,follow',
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('articles')) {
            return;
        }

        DB::table('articles')
            ->whereIn('slug', ['kalibrasyon-nedir', 'kalibrasyon-sertifikasi', 'analitik-terazi-nedir'])
            ->delete();
    }
};
