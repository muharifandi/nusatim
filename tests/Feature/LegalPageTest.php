<?php

namespace Tests\Feature;

use App\Filament\Resources\LegalPageResource\Pages\CreateLegalPage;
use App\Filament\Resources\LegalPageResource\Pages\EditLegalPage;
use App\Models\LegalPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LegalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_legal_page_renders(): void
    {
        LegalPage::create([
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'content' => '<p>Isi kebijakan privasi.</p>',
            'is_active' => true,
        ]);

        $response = $this->get('/legal/kebijakan-privasi');

        $response->assertOk();
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('Isi kebijakan privasi.', false);
    }

    public function test_inactive_legal_page_returns_404(): void
    {
        LegalPage::create([
            'title' => 'Draft Belum Rilis',
            'slug' => 'draft-belum-rilis',
            'is_active' => false,
        ]);

        $this->get('/legal/draft-belum-rilis')->assertNotFound();
    }

    public function test_index_lists_only_active_pages_ordered(): void
    {
        LegalPage::create(['title' => 'B Page', 'slug' => 'b-page', 'is_active' => true, 'order' => 2]);
        LegalPage::create(['title' => 'A Page', 'slug' => 'a-page', 'is_active' => true, 'order' => 1]);
        LegalPage::create(['title' => 'Hidden Page', 'slug' => 'hidden-page', 'is_active' => false, 'order' => 0]);

        $response = $this->get('/legal');

        $response->assertOk();
        $response->assertSeeInOrder(['A Page', 'B Page']);
        $response->assertDontSee('Hidden Page');
    }

    public function test_show_page_lists_other_active_legal_pages_in_sidebar(): void
    {
        $privacy = LegalPage::create(['title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi', 'is_active' => true]);
        LegalPage::create(['title' => 'Syarat & Ketentuan', 'slug' => 'syarat-ketentuan', 'is_active' => true]);
        LegalPage::create(['title' => 'Hidden Doc', 'slug' => 'hidden-doc', 'is_active' => false]);

        $response = $this->get("/legal/{$privacy->slug}");

        $response->assertOk();
        $response->assertSee('Syarat &amp; Ketentuan', false);
        $response->assertDontSee('Hidden Doc');
    }

    public function test_meta_fields_are_used_when_present(): void
    {
        LegalPage::create([
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'is_active' => true,
            'meta_title' => 'Meta Title Kustom',
            'meta_description' => 'Meta description kustom.',
        ]);

        $response = $this->get('/legal/kebijakan-privasi');

        $response->assertSee('<title>Meta Title Kustom</title>', false);
        $response->assertSee('Meta description kustom.', false);
    }

    public function test_admin_can_create_a_legal_page_via_panel(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        $this->get('/admin/legal-pages')->assertOk();

        Livewire::test(CreateLegalPage::class)
            ->fillForm([
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'content' => '<p>Isi kebijakan.</p>',
                'is_active' => true,
                'meta_title' => 'Meta Kebijakan Privasi',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('legal_pages', [
            'slug' => 'kebijakan-privasi',
            'meta_title' => 'Meta Kebijakan Privasi',
        ]);
    }

    public function test_admin_can_edit_and_deactivate_a_legal_page_via_panel(): void
    {
        $admin = User::factory()->create();
        $legalPage = LegalPage::create([
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'is_active' => true,
        ]);
        $this->actingAs($admin, 'web');

        Livewire::test(EditLegalPage::class, ['record' => $legalPage->getRouteKey()])
            ->assertFormSet(['title' => 'Kebijakan Privasi'])
            ->fillForm(['is_active' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($legalPage->fresh()->is_active);
        $this->get("/legal/{$legalPage->slug}")->assertNotFound();
    }

    public function test_policy_type_page_does_not_show_letterhead(): void
    {
        LegalPage::create([
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'type' => 'policy',
            'is_active' => true,
        ]);

        $response = $this->get('/legal/kebijakan-privasi');

        $response->assertOk();
        $response->assertDontSee('legal-letterhead', false);
        $response->assertDontSee('No:', false);
    }

    public function test_document_type_page_shows_letterhead_and_document_number(): void
    {
        LegalPage::create([
            'title' => 'Kontrak Kerja Sama Partner',
            'slug' => 'kontrak-kerja-sama-partner',
            'type' => 'document',
            'document_number' => '001/NSTM-KKS/VIII/2026',
            'is_active' => true,
        ]);

        $response = $this->get('/legal/kontrak-kerja-sama-partner');

        $response->assertOk();
        $response->assertSee('legal-letterhead', false);
        $response->assertSee('No: 001/NSTM-KKS/VIII/2026', false);
    }

    public function test_document_number_is_required_when_type_is_document(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        Livewire::test(CreateLegalPage::class)
            ->fillForm([
                'title' => 'Kontrak Kerja Sama',
                'slug' => 'kontrak-kerja-sama',
                'type' => 'document',
                'document_number' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['document_number']);
    }

    public function test_footer_shows_links_to_active_legal_pages(): void
    {
        LegalPage::create(['title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi', 'is_active' => true, 'order' => 1]);
        LegalPage::create(['title' => 'Hidden Doc', 'slug' => 'hidden-doc', 'is_active' => false, 'order' => 2]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Kebijakan Privasi');
        $response->assertDontSee('Hidden Doc');
    }

    /**
     * Requires barryvdh/laravel-dompdf (composer require barryvdh/laravel-dompdf)
     * - not installable in this environment (no network access to
     * packagist.org). Skips itself instead of failing until it's present.
     */
    public function test_pdf_download_returns_a_pdf_for_active_page(): void
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $this->markTestSkipped('barryvdh/laravel-dompdf not installed yet - run composer require barryvdh/laravel-dompdf.');
        }

        $legalPage = LegalPage::create([
            'title' => 'Kontrak Kerja Sama Partner',
            'slug' => 'kontrak-kerja-sama-partner',
            'type' => 'document',
            'document_number' => '001/NSTM-KKS/VIII/2026',
            'is_active' => true,
        ]);

        $response = $this->get("/legal/{$legalPage->slug}/pdf");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_download_404s_for_inactive_page(): void
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $this->markTestSkipped('barryvdh/laravel-dompdf not installed yet - run composer require barryvdh/laravel-dompdf.');
        }

        $legalPage = LegalPage::create([
            'title' => 'Draft',
            'slug' => 'draft',
            'is_active' => false,
        ]);

        $this->get("/legal/{$legalPage->slug}/pdf")->assertNotFound();
    }
}
