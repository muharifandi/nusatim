<?php

namespace Tests\Feature\Api;

use App\Models\Lead;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadPipelineApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_board_groups_own_leads_by_status(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Lead::create(['partner_id' => $partner->id, 'name' => 'A', 'phone' => '1', 'status' => 'new']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'B', 'phone' => '2', 'status' => 'new']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'C', 'phone' => '3', 'status' => 'won']);
        Lead::create(['partner_id' => $other->id, 'name' => 'D', 'phone' => '4', 'status' => 'new']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/pipeline');

        $response->assertOk();
        $response->assertJsonCount(2, 'new');
        $response->assertJsonCount(1, 'won');
        $response->assertJsonCount(0, 'lost');
    }

    public function test_board_filters_by_service_and_date(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        // created_at isn't in Lead::$fillable, so create() silently ignores
        // it (mass-assignment protection) - forceFill() bypasses that.
        Lead::create(['partner_id' => $partner->id, 'name' => 'Old Lead', 'phone' => '1', 'status' => 'new'])
            ->forceFill(['created_at' => now()->subDays(10)])->save();
        Lead::create(['partner_id' => $partner->id, 'name' => 'New Lead', 'phone' => '2', 'status' => 'new']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/pipeline?date_from='.now()->subDay()->toDateString());

        $response->assertOk();
        $response->assertJsonCount(1, 'new');
        $response->assertJsonPath('new.0.name', 'New Lead');
    }

    public function test_moving_a_lead_status_via_the_lead_status_endpoint(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Movable', 'phone' => '1', 'status' => 'new']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/leads/{$lead->id}/status", ['status' => 'contacted'])
            ->assertOk()
            ->assertJsonPath('data.status', 'contacted');

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/pipeline');
        $response->assertJsonCount(1, 'contacted');
        $response->assertJsonCount(0, 'new');
    }
}
