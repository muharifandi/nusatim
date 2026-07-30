<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\Role;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WorkflowAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_migration_creates_all_6_workflow_rows_unassigned(): void
    {
        $this->assertSame(6, WorkflowAssignment::count());
        $this->assertTrue(WorkflowAssignment::where('workflow', 'partner_registration')->whereNull('role_id')->exists());
        $this->assertTrue(WorkflowAssignment::where('workflow', 'support_ticket')->whereNull('role_id')->exists());
    }

    public function test_unassigned_workflow_lets_anyone_with_the_permission_approve(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions(['withdrawal.view', 'withdrawal.approve', 'withdrawal.reject']);

        $partner = Partner::factory()->create(['status' => 'approved']);
        $withdrawal = Withdrawal::create([
            'partner_id' => $partner->id,
            'amount' => 100000,
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_holder' => $partner->name,
            'ktp_path' => 'x.jpg',
            'status' => 'pending',
        ]);

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\WithdrawalResource\Pages\ManageWithdrawals::class)
            ->assertTableActionVisible('approve', $withdrawal);
    }

    public function test_assigned_workflow_blocks_users_without_the_assigned_role(): void
    {
        $approverRole = Role::create(['name' => 'Withdrawal Approver', 'guard_name' => 'web']);
        WorkflowAssignment::where('workflow', WorkflowAssignment::WITHDRAWAL_APPROVAL)
            ->update(['role_id' => $approverRole->id]);

        $staffWithoutRole = User::factory()->create();
        $staffWithoutRole->syncRoles([]);
        $staffWithoutRole->syncPermissions(['withdrawal.view', 'withdrawal.approve']);

        $staffWithRole = User::factory()->create();
        $staffWithRole->syncRoles([]);
        $staffWithRole->syncPermissions(['withdrawal.view', 'withdrawal.approve']);
        $staffWithRole->assignRole($approverRole);

        $partner = Partner::factory()->create(['status' => 'approved']);
        $withdrawal = Withdrawal::create([
            'partner_id' => $partner->id,
            'amount' => 100000,
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_holder' => $partner->name,
            'ktp_path' => 'x.jpg',
            'status' => 'pending',
        ]);

        \Livewire\Livewire::actingAs($staffWithoutRole)
            ->test(\App\Filament\Resources\WithdrawalResource\Pages\ManageWithdrawals::class)
            ->assertTableActionHidden('approve', $withdrawal);

        \Livewire\Livewire::actingAs($staffWithRole)
            ->test(\App\Filament\Resources\WithdrawalResource\Pages\ManageWithdrawals::class)
            ->assertTableActionVisible('approve', $withdrawal);
    }

    public function test_admin_can_assign_a_role_to_a_workflow_via_workflow_assignment_resource(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Finance Approver', 'guard_name' => 'web']);
        $assignment = WorkflowAssignment::where('workflow', WorkflowAssignment::WITHDRAWAL_APPROVAL)->firstOrFail();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\WorkflowAssignmentResource\Pages\ManageWorkflowAssignments::class)
            ->mountTableAction('edit', $assignment)
            ->setTableActionData(['role_id' => $role->id])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertSame($role->id, $assignment->fresh()->role_id);
    }

    public function test_partner_settings_page_no_longer_has_the_free_text_workflow_notes_field(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('partner_settings', 'approval_workflow_notes'));
    }
}
