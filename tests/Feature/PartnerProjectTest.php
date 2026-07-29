<?php

namespace Tests\Feature;

use App\Filament\Partner\Resources\PartnerProjectResource\Pages\ListPartnerProjects;
use App\Filament\Resources\PartnerProjectResource\Pages\ManagePartnerProjects;
use App\Mail\PartnerProjectClaimApproved;
use App\Mail\PartnerProjectClaimRejected;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_board_only_shows_available_projects_and_their_own(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('partner'));

        $partner = Partner::factory()->create(['status' => 'approved']);
        $otherPartner = Partner::factory()->create(['status' => 'approved']);

        $draft = PartnerProject::create(['name' => 'Masih Draft', 'status' => 'draft']);
        $available = PartnerProject::create(['name' => 'Bisa Diklaim', 'status' => 'available']);
        $ownAssigned = PartnerProject::create(['name' => 'Punya Saya', 'status' => 'assigned', 'partner_id' => $partner->id]);
        $othersAssigned = PartnerProject::create(['name' => 'Punya Orang Lain', 'status' => 'assigned', 'partner_id' => $otherPartner->id]);

        Livewire::actingAs($partner, 'partner')
            ->test(ListPartnerProjects::class)
            ->assertCanSeeTableRecords([$available, $ownAssigned])
            ->assertCanNotSeeTableRecords([$draft, $othersAssigned]);
    }

    public function test_claiming_a_project_is_atomic_only_one_partner_can_win_the_race(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $project = PartnerProject::create(['name' => 'Rebutan', 'status' => 'available']);

        $resultA = $project->claim($partnerA);
        $resultB = $project->fresh()->claim($partnerB);

        $this->assertTrue($resultA);
        $this->assertFalse($resultB);
        $this->assertSame($partnerA->id, $project->fresh()->partner_id);
        $this->assertSame('pending_approval', $project->fresh()->status);
    }

    public function test_partner_can_cancel_their_own_pending_claim(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Batal Klaim', 'status' => 'available']);
        $project->claim($partner);

        $project->cancelClaim();

        $this->assertSame('available', $project->fresh()->status);
        $this->assertNull($project->fresh()->partner_id);
    }

    public function test_approving_a_claim_assigns_the_project_and_sends_an_email(): void
    {
        Mail::fake();

        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Disetujui', 'status' => 'available']);
        $project->claim($partner);

        $project->approveClaim();

        $this->assertSame('assigned', $project->fresh()->status);
        Mail::assertSent(PartnerProjectClaimApproved::class, fn ($mail) => $mail->hasTo($partner->email));
    }

    public function test_rejecting_a_claim_reopens_the_project_and_sends_an_email(): void
    {
        Mail::fake();

        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Ditolak', 'status' => 'available']);
        $project->claim($partner);

        $project->rejectClaim();

        $project->refresh();
        $this->assertSame('available', $project->status);
        $this->assertNull($project->partner_id);
        Mail::assertSent(PartnerProjectClaimRejected::class, fn ($mail) => $mail->hasTo($partner->email));
    }

    public function test_admin_can_assign_a_partner_directly_without_a_claim(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Assign Langsung', 'status' => 'available']);

        Livewire::actingAs($admin)
            ->test(ManagePartnerProjects::class)
            ->callTableAction('assignPartner', $project, data: ['partner_id' => $partner->id]);

        $project->refresh();
        $this->assertSame('assigned', $project->status);
        $this->assertSame($partner->id, $project->partner_id);
    }

    public function test_partner_project_board_page_renders(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.partner-projects.index'))
            ->assertOk();
    }

    public function test_admin_project_board_page_renders(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.partner-projects.index'))
            ->assertOk();
    }
}
