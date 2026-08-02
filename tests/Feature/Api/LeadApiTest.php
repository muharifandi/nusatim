<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_pending_partner_cannot_access_leads(): void
    {
        $partner = Partner::factory()->create(['status' => 'pending_review']);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/leads')
            ->assertForbidden();
    }

    public function test_partner_can_only_list_their_own_leads(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Saya', 'phone' => '0811']);
        Lead::create(['partner_id' => $other->id, 'name' => 'Lead Orang Lain', 'phone' => '0812']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/leads');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Lead Saya');
    }

    public function test_index_filters_by_status_and_search(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Lead::create(['partner_id' => $partner->id, 'name' => 'Budi Santoso', 'phone' => '0811', 'status' => 'new']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'Ani Wijaya', 'phone' => '0812', 'status' => 'won']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/leads?status=won')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Ani Wijaya');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/leads?search=Budi')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Budi Santoso');
    }

    public function test_partner_can_create_a_lead(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/leads', [
                'name' => 'Lead Baru',
                'phone' => '081234567890',
                'email' => 'lead@example.com',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Lead Baru');
        $response->assertJsonPath('data.status', 'new');
        $this->assertDatabaseHas('leads', ['partner_id' => $partner->id, 'name' => 'Lead Baru']);
    }

    public function test_viewing_another_partners_lead_returns_404(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $lead = Lead::create(['partner_id' => $other->id, 'name' => 'Bukan Punya Saya', 'phone' => '0811']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/leads/{$lead->id}")
            ->assertNotFound();
    }

    public function test_partner_can_update_a_lead(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Lama', 'phone' => '0811']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/v1/leads/{$lead->id}", ['name' => 'Lead Sudah Diupdate']);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Lead Sudah Diupdate');
    }

    public function test_updating_status_to_won_creates_a_customer(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Menang', 'phone' => '0811', 'estimated_value' => 1000000]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/leads/{$lead->id}/status", ['status' => 'won']);

        $response->assertOk();
        $response->assertJsonPath('data.status', 'won');
        $this->assertDatabaseHas('customers', ['lead_id' => $lead->id, 'partner_id' => $partner->id]);
    }

    public function test_update_status_rejects_an_invalid_status(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead X', 'phone' => '0811']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/leads/{$lead->id}/status", ['status' => 'not-a-real-status'])
            ->assertUnprocessable();
    }

    public function test_partner_can_delete_a_lead(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Dihapus', 'phone' => '0811']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/leads/{$lead->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_lead_creation_auto_logs_an_activity(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Timeline', 'phone' => '0811']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/leads/{$lead->id}/activities");

        $response->assertOk();
        $response->assertJsonFragment(['type' => 'created']);
    }

    public function test_partner_can_add_a_note_to_a_lead(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Note', 'phone' => '0811']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/leads/{$lead->id}/activities", ['body' => 'Sudah dihubungi via WA.']);

        $response->assertCreated();
        $response->assertJsonPath('data.type', 'note');
        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'type' => 'note', 'body' => 'Sudah dihubungi via WA.']);
    }
}
