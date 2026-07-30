<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_from_partner_project_shows_project_columns(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create([
            'name' => 'Website Company Profile',
            'status' => 'assigned',
            'partner_id' => $partner->id,
            'progress' => 40,
        ]);
        $customer = Customer::create([
            'partner_id' => $partner->id,
            'partner_project_id' => $project->id,
            'name' => 'PT Maju Jaya',
        ]);

        $this->assertSame('Website Company Profile', $customer->partnerProject->name);
        $this->assertSame('assigned', $customer->partnerProject->status);
        $this->assertSame(40, (int) $customer->partnerProject->progress);
    }

    public function test_customer_from_lead_won_only_has_no_partner_project(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create([
            'partner_id' => $partner->id,
            'name' => 'PT Tanpa Project',
        ]);

        $this->assertNull($customer->partnerProject);
    }

    public function test_update_progress_action_updates_partner_project(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create([
            'name' => 'Aplikasi Mobile',
            'status' => 'assigned',
            'partner_id' => $partner->id,
            'progress' => 10,
        ]);
        $customer = Customer::create([
            'partner_id' => $partner->id,
            'partner_project_id' => $project->id,
            'name' => 'PT Progress Test',
        ]);

        $project->update(['progress' => 75]);
        $customer->refresh();

        $this->assertSame(75, (int) $customer->partnerProject->progress);
    }

    public function test_admin_can_update_progress_via_partner_project_resource_form(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create([
            'name' => 'Sistem ERP',
            'status' => 'assigned',
            'partner_id' => $partner->id,
            'progress' => 0,
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerProjectResource\Pages\ManagePartnerProjects::class)
            ->mountTableAction('edit', $project)
            ->setTableActionData(['progress' => 60])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertSame(60, (int) $project->fresh()->progress);
    }

    public function test_follow_up_meeting_proposal_activity_note_and_commission_accessors_are_filtered_correctly(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Workspace', 'phone' => '0899']);
        $lead->reminders()->create(['type' => 'follow_up', 'remind_at' => now(), 'note' => 'Telpon lagi besok']);
        $lead->reminders()->create(['type' => 'meeting', 'remind_at' => now()->addDay(), 'note' => 'Demo produk']);
        $lead->documents()->create(['path' => 'proposal.pdf', 'original_name' => 'Proposal Akhir.pdf']);
        $lead->activities()->create(['type' => 'note', 'body' => 'Catatan manual dari sales']);
        $lead->activities()->create(['type' => 'status_change', 'body' => 'Status berubah ke won']);

        $customer = $lead->markWon();
        Commission::create([
            'customer_id' => $customer->id,
            'partner_id' => $partner->id,
            'amount' => 500000,
            'type' => 'flat',
            'status' => 'pending',
        ]);

        $this->assertCount(1, $customer->follow_ups);
        $this->assertSame('Telpon lagi besok', $customer->follow_ups[0]['note']);

        $this->assertCount(1, $customer->meetings);
        $this->assertSame('Demo produk', $customer->meetings[0]['note']);

        $this->assertCount(1, $customer->proposal_documents);
        $this->assertSame('Proposal Akhir.pdf', $customer->proposal_documents[0]['name']);

        $noteBodies = collect($customer->notes)->pluck('body')->all();
        $this->assertContains('Catatan manual dari sales', $noteBodies);
        $this->assertNotContains('Status berubah ke won', $noteBodies);

        $systemActivityBodies = collect($customer->system_activities)->pluck('body')->all();
        $this->assertContains('Status berubah ke won', $systemActivityBodies);
        $this->assertNotContains('Catatan manual dari sales', $systemActivityBodies);

        $this->assertSame('pending', $customer->commission->status);
    }

    public function test_customer_without_lead_has_empty_follow_up_meeting_and_proposal_accessors(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'PT Tanpa Lead']);

        $this->assertSame([], $customer->follow_ups);
        $this->assertSame([], $customer->meetings);
        $this->assertSame([], $customer->proposal_documents);
    }

    public function test_customer_view_and_index_pages_render_with_and_without_partner_project(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $project = PartnerProject::create([
            'name' => 'Project Render Test',
            'status' => 'assigned',
            'partner_id' => $partner->id,
            'progress' => 20,
        ]);
        $withProject = Customer::create([
            'partner_id' => $partner->id,
            'partner_project_id' => $project->id,
            'name' => 'PT Dengan Project',
        ]);
        $withoutProject = Customer::create([
            'partner_id' => $partner->id,
            'name' => 'PT Tanpa Project Render',
        ]);

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.customers.index'))
            ->assertOk();

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.customers.view', $withProject))
            ->assertOk();

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.customers.view', $withoutProject))
            ->assertOk();
    }
}
