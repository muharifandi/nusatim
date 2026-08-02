<?php

namespace Tests\Feature\Api;

use App\Models\MarketingMaterial;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingMaterialApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_index_only_shows_active_materials_grouped_by_category(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        MarketingMaterial::create(['title' => 'Brosur A', 'category' => 'brosur', 'file_path' => 'media/brosur-a.pdf', 'is_active' => true, 'order' => 1]);
        MarketingMaterial::create(['title' => 'Brosur Nonaktif', 'category' => 'brosur', 'is_active' => false, 'order' => 2]);
        MarketingMaterial::create(['title' => 'FAQ Umum', 'category' => 'faq', 'content' => 'Isi FAQ', 'is_active' => true, 'order' => 1]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/marketing-materials');

        $response->assertOk();
        $response->assertJsonCount(1, 'brosur');
        $response->assertJsonPath('brosur.0.title', 'Brosur A');
        $response->assertJsonPath('brosur.0.download_url', asset('media/brosur-a.pdf'));
        $response->assertJsonCount(1, 'faq');
        $response->assertJsonPath('faq.0.content', 'Isi FAQ');
    }

    public function test_index_filters_by_category(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        MarketingMaterial::create(['title' => 'Brosur A', 'category' => 'brosur', 'is_active' => true, 'order' => 1]);
        MarketingMaterial::create(['title' => 'FAQ Umum', 'category' => 'faq', 'content' => 'x', 'is_active' => true, 'order' => 1]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/marketing-materials?category=faq');

        $response->assertOk();
        $response->assertJsonMissing(['brosur']);
        $response->assertJsonCount(1, 'faq');
    }

    public function test_inactive_material_is_not_viewable(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $material = MarketingMaterial::create(['title' => 'Nonaktif', 'category' => 'brosur', 'is_active' => false, 'order' => 1]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/marketing-materials/{$material->id}")
            ->assertNotFound();
    }

    public function test_pending_partner_cannot_access_marketing_materials(): void
    {
        $partner = Partner::factory()->create(['status' => 'pending_review']);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/marketing-materials')
            ->assertForbidden();
    }
}
