<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_partner_update_permission_can_login_as_partner(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->callTableAction('impersonate', $partner)
            ->assertRedirect('/partner');

        $this->assertTrue(Auth::guard('partner')->check());
        $this->assertSame($partner->id, Auth::guard('partner')->id());
    }

    public function test_impersonation_is_recorded_in_the_audit_log(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->callTableAction('impersonate', $partner);

        $log = AuditLog::where('auditable_type', Partner::class)
            ->where('auditable_id', $partner->id)
            ->where('action', 'impersonated')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame(User::class, $log->user_type);
        $this->assertSame($admin->id, $log->user_id);
    }

    public function test_staff_without_partner_update_permission_cannot_see_the_action(): void
    {
        $staff = User::factory()->create();
        $staff->syncRoles([]);
        $staff->syncPermissions(['partner.view']);

        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($staff)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->assertTableActionHidden('impersonate', $partner);
    }

    public function test_pending_and_suspended_partners_cannot_be_impersonated(): void
    {
        $admin = User::factory()->create();
        $pending = Partner::factory()->create(['status' => 'pending_review']);
        $suspended = Partner::factory()->create(['status' => 'suspended']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->assertTableActionHidden('impersonate', $pending)
            ->assertTableActionHidden('impersonate', $suspended);
    }

    public function test_stop_impersonating_logs_out_of_partner_guard_and_keeps_admin_session(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($admin, 'web');
        session(['impersonating_admin_id' => $admin->id]);
        Auth::guard('partner')->login($partner);

        $this->assertTrue(Auth::guard('partner')->check());

        $response = $this->get(route('partner.stop-impersonating'));

        $response->assertRedirect('/admin/partners');
        $this->assertFalse(Auth::guard('partner')->check());
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($admin->id, Auth::guard('web')->id());
    }

    public function test_banner_shows_while_impersonating_and_hides_otherwise(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($admin, 'web');
        session(['impersonating_admin_id' => $admin->id]);
        Auth::guard('partner')->login($partner);

        $this->get(route('filament.partner.pages.dashboard'))
            ->assertOk()
            ->assertSee('Kembali ke Admin');

        // Without the session flag, the same page shouldn't show the banner.
        session()->forget('impersonating_admin_id');

        $this->get(route('filament.partner.pages.dashboard'))
            ->assertOk()
            ->assertDontSee('Kembali ke Admin');
    }
}
