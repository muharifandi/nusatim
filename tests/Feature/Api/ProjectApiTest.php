<?php

namespace Tests\Feature\Api;

use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\PartnerSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_board_shows_available_projects_and_ones_this_partner_claimed(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        PartnerProject::create(['name' => 'Available Project', 'status' => 'available']);
        PartnerProject::create(['name' => 'My Claimed Project', 'status' => 'pending_approval', 'partner_id' => $partner->id]);
        PartnerProject::create(['name' => 'Someone Elses Project', 'status' => 'assigned', 'partner_id' => $other->id]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/projects');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Available Project', $names);
        $this->assertContains('My Claimed Project', $names);
        $this->assertNotContains('Someone Elses Project', $names);
    }

    public function test_partner_can_claim_an_available_project(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $project = PartnerProject::create(['name' => 'Klaim Saya', 'status' => 'available']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/projects/{$project->id}/claim");

        $response->assertOk();
        $response->assertJsonPath('data.status', 'pending_approval');
        $this->assertSame($partner->id, $project->fresh()->partner_id);
    }

    /**
     * The true atomic race (both claim() calls racing on the same
     * already-fetched row) is already covered at the model layer by
     * tests/Feature/PartnerProjectTest.php. At the API layer, once partner
     * A's claim has committed, the project's status is no longer
     * 'available' and its partner_id isn't B's - B's own scoped query
     * (available OR own, same rule as PartnerProjectResource::
     * getEloquentQuery()) genuinely can't see the row anymore, so a
     * synchronous second request correctly 404s rather than 409s.
     */
    public function test_a_project_already_claimed_by_someone_else_is_no_longer_visible(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);
        $tokenB = $this->tokenFor($partnerB);

        $project = PartnerProject::create(['name' => 'Rebutan', 'status' => 'available']);
        $project->claim($partnerA);

        $response = $this->withHeader('Authorization', "Bearer {$tokenB}")
            ->postJson("/api/v1/projects/{$project->id}/claim");

        $response->assertNotFound();
        $this->assertSame($partnerA->id, $project->fresh()->partner_id);
    }

    public function test_claim_is_rejected_past_the_concurrent_claim_limit(): void
    {
        PartnerSetting::current()->update(['max_concurrent_claimed_projects' => 1]);
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $already = PartnerProject::create(['name' => 'Sudah Diklaim', 'status' => 'available']);
        $already->claim($partner);

        $project = PartnerProject::create(['name' => 'Melebihi Limit', 'status' => 'available']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/projects/{$project->id}/claim")
            ->assertUnprocessable();
    }

    public function test_partner_can_cancel_their_own_pending_claim(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $project = PartnerProject::create(['name' => 'Batal Klaim', 'status' => 'available']);
        $project->claim($partner);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/projects/{$project->id}/cancel-claim");

        $response->assertOk();
        $response->assertJsonPath('data.status', 'available');
        $this->assertNull($project->fresh()->partner_id);
    }

    public function test_cannot_cancel_a_claim_that_isnt_pending_or_isnt_own(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $othersProject = PartnerProject::create(['name' => 'Bukan Punya Saya', 'status' => 'pending_approval', 'partner_id' => $other->id]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/projects/{$othersProject->id}/cancel-claim")
            ->assertNotFound();
    }
}
