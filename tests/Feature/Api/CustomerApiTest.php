<?php

namespace Tests\Feature\Api;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadDocument;
use App\Models\Partner;
use App\Models\PartnerProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_partner_can_only_list_their_own_customers(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Saya', 'project_value' => 1000000]);
        Customer::create(['partner_id' => $other->id, 'name' => 'Customer Orang Lain', 'project_value' => 500000]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/customers');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Customer Saya');
    }

    public function test_viewing_another_partners_customer_returns_404(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $customer = Customer::create(['partner_id' => $other->id, 'name' => 'Bukan Punya Saya']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/customers/{$customer->id}")
            ->assertNotFound();
    }

    public function test_show_includes_project_commission_and_lead_derived_data(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Asal', 'phone' => '0811']);
        $customer = $lead->markWon();
        $customer->update(['pic_name' => 'Budi']);

        $lead->reminders()->create(['type' => 'follow_up', 'remind_at' => now(), 'note' => 'Follow up 1']);
        $document = LeadDocument::create(['lead_id' => $lead->id, 'path' => 'leads/proposal.pdf', 'original_name' => 'Proposal.pdf']);

        $project = PartnerProject::create(['name' => 'Project A', 'status' => 'assigned', 'partner_id' => $partner->id, 'progress' => 10]);
        $customer->update(['partner_project_id' => $project->id]);

        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 200000, 'type' => 'flat', 'status' => 'approved']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson("/api/v1/customers/{$customer->id}");

        $response->assertOk();
        $response->assertJsonPath('data.pic_name', 'Budi');
        $response->assertJsonPath('data.project.name', 'Project A');
        $response->assertJsonPath('data.project.progress', 10);
        $response->assertJsonPath('data.commission.status', 'approved');
        $response->assertJsonPath('data.commission.amount', 200000);
        $response->assertJsonCount(1, 'data.follow_ups');
        $response->assertJsonCount(1, 'data.proposal_documents');
        $response->assertJsonPath(
            'data.proposal_documents.0.url',
            route('api.v1.leads.documents.download', ['lead' => $lead->id, 'document' => $document->id])
        );
    }

    public function test_partner_can_update_customer_fields(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Nama Lama']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/v1/customers/{$customer->id}", ['name' => 'Nama Baru', 'payment_status' => 'paid']);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Nama Baru');
        $response->assertJsonPath('data.payment_status', 'paid');
    }

    public function test_update_progress_requires_a_linked_project(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Tanpa Project']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/customers/{$customer->id}/progress", ['progress' => 50])
            ->assertUnprocessable();
    }

    public function test_update_progress_updates_the_linked_project(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $project = PartnerProject::create(['name' => 'Project B', 'status' => 'assigned', 'partner_id' => $partner->id, 'progress' => 20]);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Dengan Project', 'partner_project_id' => $project->id]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/customers/{$customer->id}/progress", ['progress' => 75]);

        $response->assertOk();
        $response->assertJsonPath('data.project.progress', 75);
        $this->assertSame(75, $project->fresh()->progress);
    }
}
