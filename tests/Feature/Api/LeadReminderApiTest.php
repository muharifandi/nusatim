<?php

namespace Tests\Feature\Api;

use App\Models\Lead;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadReminderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_partner_can_crud_reminders_for_their_own_lead(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead A', 'phone' => '0811']);

        $create = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/leads/{$lead->id}/reminders", [
                'type' => 'follow_up',
                'remind_at' => now()->addDay()->toDateTimeString(),
                'note' => 'Telepon lagi besok.',
            ]);
        $create->assertCreated();
        $reminderId = $create->json('data.id');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/leads/{$lead->id}/reminders")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/v1/leads/{$lead->id}/reminders/{$reminderId}", ['note' => 'Sudah diupdate.'])
            ->assertOk()
            ->assertJsonPath('data.note', 'Sudah diupdate.');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/leads/{$lead->id}/reminders/{$reminderId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('lead_reminders', ['id' => $reminderId]);
    }

    public function test_complete_endpoint_sets_completed_at(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead B', 'phone' => '0811']);
        $reminder = $lead->reminders()->create(['type' => 'meeting', 'remind_at' => now()]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/leads/{$lead->id}/reminders/{$reminder->id}/complete");

        $response->assertOk();
        $this->assertNotNull($response->json('data.completed_at'));
    }

    public function test_reminders_are_scoped_through_lead_ownership(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $otherLead = Lead::create(['partner_id' => $other->id, 'name' => 'Bukan Punya Saya', 'phone' => '0811']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/leads/{$otherLead->id}/reminders")
            ->assertNotFound();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/leads/{$otherLead->id}/reminders", [
                'type' => 'follow_up',
                'remind_at' => now()->toDateTimeString(),
            ])
            ->assertNotFound();
    }
}
