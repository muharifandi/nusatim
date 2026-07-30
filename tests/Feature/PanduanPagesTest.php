<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanduanPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_panduan_page_renders_with_admin_only_content(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('filament.admin.pages.panduan'))
            ->assertOk();

        $response->assertSee('Workflow Assignment');
        $response->assertSee('Role &amp; Permission', false);
        $response->assertDontSee('Sales Pipeline');
    }

    public function test_partner_panduan_page_renders_with_partner_only_content(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.pages.panduan'))
            ->assertOk();

        $response->assertSee('Sales Pipeline');
        $response->assertSee('Marketing Center');
        $response->assertDontSee('Workflow Assignment');
        $response->assertDontSee('Audit Log');
    }
}
