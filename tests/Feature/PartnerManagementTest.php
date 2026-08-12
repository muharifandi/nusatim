<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PartnerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_suspend_an_approved_partner_with_a_reason(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->mountTableAction('suspend', $partner)
            ->setTableActionData(['rejection_reason' => 'Melanggar kode etik partner.'])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $partner->refresh();
        $this->assertSame('suspended', $partner->status);
        $this->assertSame('Melanggar kode etik partner.', $partner->rejection_reason);
    }

    public function test_suspended_partner_is_blocked_from_the_portal_and_sees_suspension_message(): void
    {
        $partner = Partner::factory()->create([
            'status' => 'suspended',
            'rejection_reason' => 'Alasan suspend test.',
        ]);

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.pages.dashboard'))
            ->assertRedirect(route('filament.partner.pages.status'));

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.pages.status'))
            ->assertOk()
            ->assertSee('Akun Disuspend')
            ->assertSee('Alasan suspend test.');
    }

    public function test_admin_can_reactivate_a_suspended_partner(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create([
            'status' => 'suspended',
            'rejection_reason' => 'Sedang ditinjau.',
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->callTableAction('reactivate', $partner);

        $partner->refresh();
        $this->assertSame('approved', $partner->status);
        $this->assertNull($partner->rejection_reason);
    }

    public function test_suspend_action_only_visible_for_approved_partners(): void
    {
        $admin = User::factory()->create();
        $pending = Partner::factory()->create(['status' => 'pending_review']);
        $approved = Partner::factory()->create(['status' => 'approved']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->assertTableActionHidden('suspend', $pending)
            ->assertTableActionVisible('suspend', $approved)
            ->assertTableActionHidden('reactivate', $approved);
    }

    public function test_admin_reset_password_action_sends_reset_link(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->callTableAction('resetPassword', $partner);

        Notification::assertSentTo($partner, \Illuminate\Auth\Notifications\ResetPassword::class);

        $this->assertDatabaseHas('partner_password_reset_tokens', [
            'email' => $partner->email,
        ]);
    }

    public function test_reset_password_action_not_visible_for_pending_partner(): void
    {
        $admin = User::factory()->create();
        $pending = Partner::factory()->create(['status' => 'pending_review']);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->assertTableActionHidden('resetPassword', $pending);
    }

    /**
     * Filament's mountTableAction()/callMountedTableAction() never re-checks
     * ->visible() before running an action's closure - it only checks
     * ->isDisabled(). A staff user who can merely view this page
     * (partner.view) but lacks partner.update could, before this fix,
     * still suspend/reactivate/change-level/reset-password on any partner
     * via a direct Livewire call even though the buttons were hidden.
     */
    public function test_a_staff_user_without_partner_update_permission_cannot_invoke_these_actions_via_a_direct_call(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions(['partner.view']);

        $approved = Partner::factory()->create(['status' => 'approved', 'level' => 'bronze']);
        $suspended = Partner::factory()->create(['status' => 'suspended']);

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->call('mountTableAction', 'suspend', $approved->getKey())
            ->call('callMountedTableAction');
        $this->assertSame('approved', $approved->fresh()->status);

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->call('mountTableAction', 'reactivate', $suspended->getKey())
            ->call('callMountedTableAction');
        $this->assertSame('suspended', $suspended->fresh()->status);

        \Livewire\Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->call('mountTableAction', 'updateLevel', $approved->getKey())
            ->set('mountedTableActionsData.0.level', 'platinum')
            ->call('callMountedTableAction');
        $this->assertSame('bronze', $approved->fresh()->level);
    }
}
