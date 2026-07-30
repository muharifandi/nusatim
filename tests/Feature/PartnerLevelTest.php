<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\CommissionScheme;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerLevelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_partners_level(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved', 'level' => null]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->mountTableAction('updateLevel', $partner)
            ->setTableActionData(['level' => 'gold'])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $this->assertSame('gold', $partner->fresh()->level);
    }

    /**
     * Final business decision (2026-07-30): Partner Level is NOT a
     * Commission Scheme scope - it's purely informational (badge/loyalty/
     * dashboard). A partner's level having any value at all must not
     * change which scheme resolves for their customers.
     */
    public function test_partner_level_does_not_affect_commission_scheme_resolution(): void
    {
        $platinumPartner = Partner::factory()->create(['status' => 'approved', 'level' => 'platinum']);
        $noLevelPartner = Partner::factory()->create(['status' => 'approved', 'level' => null]);

        $global = CommissionScheme::create(['name' => 'Global', 'type' => 'percentage', 'percentage' => 5]);

        $platinumCustomer = Customer::create(['partner_id' => $platinumPartner->id, 'name' => 'Platinum Customer', 'project_value' => 1000000]);
        $noLevelCustomer = Customer::create(['partner_id' => $noLevelPartner->id, 'name' => 'No Level Customer', 'project_value' => 1000000]);

        $this->assertSame($global->id, CommissionScheme::resolveFor($platinumCustomer)->id);
        $this->assertSame($global->id, CommissionScheme::resolveFor($noLevelCustomer)->id);

        $byPartner = CommissionScheme::create(['name' => 'Per Partner', 'type' => 'flat', 'flat_amount' => 500000, 'partner_id' => $platinumPartner->id]);
        $this->assertSame($byPartner->id, CommissionScheme::resolveFor($platinumCustomer)->id);
    }

    public function test_commission_schemes_table_no_longer_has_a_level_column(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('commission_schemes', 'level'));
    }

    public function test_generating_commission_ignores_partner_level_and_uses_global_scheme_rate(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved', 'level' => 'bronze']);
        CommissionScheme::create(['name' => 'Global', 'type' => 'percentage', 'percentage' => 5]);

        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Bronze Customer', 'project_value' => 2000000]);

        $commission = Commission::generateForCustomer($customer);

        $this->assertEquals(100000, (float) $commission->amount);
    }
}
