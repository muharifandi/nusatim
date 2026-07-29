<?php

namespace Tests\Feature;

use App\Filament\Partner\Resources\MarketingMaterialResource\Pages\ListMarketingMaterials;
use App\Filament\Resources\MarketingMaterialResource\Pages\ManageMarketingMaterials;
use App\Models\MarketingMaterial;
use App\Models\Partner;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarketingCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_only_sees_active_materials(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('partner'));

        $partner = Partner::factory()->create(['status' => 'approved']);
        $active = MarketingMaterial::create([
            'title' => 'Brosur Aktif',
            'category' => 'brosur',
            'file_path' => 'media/uploads/brosur.pdf',
            'is_active' => true,
        ]);
        $inactive = MarketingMaterial::create([
            'title' => 'Brosur Nonaktif',
            'category' => 'brosur',
            'file_path' => 'media/uploads/brosur-lama.pdf',
            'is_active' => false,
        ]);

        Livewire::actingAs($partner, 'partner')
            ->test(ListMarketingMaterials::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$inactive]);
    }

    public function test_file_based_material_shows_a_working_download_link(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $material = MarketingMaterial::create([
            'title' => 'Banner Promo',
            'category' => 'banner',
            'file_path' => 'media/uploads/banner-promo.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.marketing-materials.view', $material));

        $response->assertOk();
        $response->assertSee(asset('media/uploads/banner-promo.jpg'), false);
    }

    public function test_text_based_material_shows_its_content(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $material = MarketingMaterial::create([
            'title' => 'Template Follow Up',
            'category' => 'template_whatsapp',
            'content' => 'Halo kak, jadi lanjut order-nya?',
            'is_active' => true,
        ]);

        $response = $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.marketing-materials.view', $material));

        $response->assertOk();
        $response->assertSee('jadi lanjut order-nya', false);
    }

    public function test_admin_can_create_a_marketing_material(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->create();

        // Text-based category (no FileUpload involved) keeps this test
        // focused on the CRUD wiring itself, not Livewire's file-upload
        // test plumbing.
        Livewire::actingAs($admin, 'web')
            ->test(ManageMarketingMaterials::class)
            ->callAction('create', data: [
                'title' => 'Template Follow Up Standar',
                'category' => 'template_whatsapp',
                'content' => 'Halo kak, gimana kabarnya?',
                'order' => 1,
                'is_active' => true,
            ]);

        $this->assertDatabaseHas('marketing_materials', [
            'title' => 'Template Follow Up Standar',
            'category' => 'template_whatsapp',
        ]);
    }

    public function test_marketing_center_pages_render_for_partner_and_admin(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.marketing-materials.index'))
            ->assertOk();

        $admin = User::factory()->create();
        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.resources.marketing-materials.index'))
            ->assertOk();
    }
}
