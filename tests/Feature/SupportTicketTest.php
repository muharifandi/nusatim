<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WorkflowAssignment;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_create_a_support_ticket(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('partner'));

        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($partner, 'partner')
            ->test(\App\Filament\Partner\Resources\SupportTicketResource\Pages\CreateSupportTicket::class)
            ->fillForm([
                'subject' => 'Tidak bisa upload dokumen',
                'description' => 'Setiap saya upload KTP selalu gagal.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $ticket = SupportTicket::where('subject', 'Tidak bisa upload dokumen')->firstOrFail();
        $this->assertSame($partner->id, $ticket->partner_id);
        $this->assertSame('open', $ticket->status);
    }

    public function test_partner_only_sees_their_own_tickets(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        SupportTicket::create(['partner_id' => $partnerA->id, 'subject' => 'Tiket A', 'description' => 'x']);
        SupportTicket::create(['partner_id' => $partnerB->id, 'subject' => 'Tiket B', 'description' => 'x']);

        Filament::setCurrentPanel(Filament::getPanel('partner'));

        Livewire::actingAs($partnerA, 'partner')
            ->test(\App\Filament\Partner\Resources\SupportTicketResource\Pages\ListSupportTickets::class)
            ->assertCanSeeTableRecords(SupportTicket::where('partner_id', $partnerA->id)->get())
            ->assertCanNotSeeTableRecords(SupportTicket::where('partner_id', $partnerB->id)->get());
    }

    public function test_admin_sees_tickets_from_all_partners(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $ticketA = SupportTicket::create(['partner_id' => $partnerA->id, 'subject' => 'Tiket A', 'description' => 'x']);
        $ticketB = SupportTicket::create(['partner_id' => $partnerB->id, 'subject' => 'Tiket B', 'description' => 'x']);

        $admin = User::factory()->create();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->assertCanSeeTableRecords([$ticketA, $ticketB]);
    }

    public function test_admin_can_assign_resolve_and_close_a_ticket(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $ticket = SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'Perlu bantuan', 'description' => 'x']);

        $admin = User::factory()->create();
        $staff = User::factory()->create();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->mountTableAction('assign', $ticket)
            ->setTableActionData(['assigned_to' => $staff->id])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $ticket->refresh();
        $this->assertSame($staff->id, $ticket->assigned_to);
        $this->assertSame('in_progress', $ticket->status);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->mountTableAction('resolve', $ticket)
            ->setTableActionData(['resolution_note' => 'Sudah diperbaiki.'])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $ticket->refresh();
        $this->assertSame('resolved', $ticket->status);
        $this->assertSame('Sudah diperbaiki.', $ticket->resolution_note);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->callTableAction('close', $ticket);

        $this->assertSame('closed', $ticket->fresh()->status);
    }

    public function test_resolve_action_is_gated_by_permission_and_workflow_assignment(): void
    {
        $approverRole = Role::create(['name' => 'Support Approver', 'guard_name' => 'web']);
        WorkflowAssignment::where('workflow', WorkflowAssignment::SUPPORT_TICKET)
            ->update(['role_id' => $approverRole->id]);

        $partner = Partner::factory()->create(['status' => 'approved']);
        $ticket = SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'Perlu bantuan', 'description' => 'x']);

        $staffWithoutRole = User::factory()->create();
        $staffWithoutRole->syncRoles([]);
        $staffWithoutRole->syncPermissions(['support_ticket.view', 'support_ticket.approve']);

        $staffWithRole = User::factory()->create();
        $staffWithRole->syncRoles([]);
        $staffWithRole->syncPermissions(['support_ticket.view', 'support_ticket.approve']);
        $staffWithRole->assignRole($approverRole);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs($staffWithoutRole)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->assertTableActionHidden('resolve', $ticket);

        Livewire::actingAs($staffWithRole)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->assertTableActionVisible('resolve', $ticket);
    }
}
