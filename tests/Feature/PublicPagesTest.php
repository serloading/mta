<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_pages_render_with_new_design(): void
    {
        foreach ([
            '/kvkk' => 'KVKK Aydınlatma Metni',
            '/gizlilik-politikasi' => 'Gizlilik Politikası',
            '/cerez-politikasi' => 'Çerez (Cookie) Politikası',
        ] as $path => $title) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee($title, false);
            $response->assertSee('legal-prose', false);
            $response->assertSee('Yasal Metinler', false);
            $response->assertSee('Yasal Bilgilendirme', false);
        }
    }

    public function test_unknown_legal_slug_is_not_registered(): void
    {
        // Only the three explicit legal routes exist; anything else falls through
        // to the redirect fallback (404 when no redirect matches).
        $this->get('/kullanim-kosullari')->assertNotFound();
    }

    public function test_quote_page_uses_new_design_identity(): void
    {
        $response = $this->get('/teklif-al');
        $response->assertOk();
        $response->assertSee('Teklif Formu', false);
        $response->assertSee('rounded-3xl', false);
        $response->assertSee('name="website"', false); // honeypot preserved
    }

    public function test_blog_index_uses_new_design_identity(): void
    {
        $response = $this->get('/blog');
        $response->assertOk();
        $response->assertSee('Yayın Akışı', false);
        $response->assertDontSee('visual-placeholder', false);
    }

    public function test_contact_page_uses_new_design_identity(): void
    {
        $response = $this->get('/iletisim');
        $response->assertOk();
        $response->assertSee('Bize Yazın', false);
        $response->assertDontSee('class="lead-form"', false);
    }
}
