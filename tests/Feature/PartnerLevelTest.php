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

    public function test_scheme_resolution_prioritizes_partner_level_between_partner_and_service(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved', 'level' => 'platinum']);

        $customer = Customer::create([
            'partner_id' => $partner->id,
            'name' => 'Customer Level Test',
            'project_value' => 1000000,
        ]);

        $global = CommissionScheme::create(['name' => 'Global', 'type' => 'percentage', 'percentage' => 5]);
        $this->assertSame($global->id, CommissionScheme::resolveFor($customer)->id);

        $byLevel = CommissionScheme::create(['name' => 'Per Level Platinum', 'type' => 'percentage', 'percentage' => 12, 'level' => 'platinum']);
        $this->assertSame($byLevel->id, CommissionScheme::resolveFor($customer)->id);

        $byPartner = CommissionScheme::create(['name' => 'Per Partner', 'type' => 'flat', 'flat_amount' => 500000, 'partner_id' => $partner->id]);
        $this->assertSame($byPartner->id, CommissionScheme::resolveFor($customer)->id);
    }

    public function test_level_scheme_only_matches_partners_with_that_exact_level(): void
    {
        $goldPartner = Partner::factory()->create(['status' => 'approved', 'level' => 'gold']);
        $silverPartner = Partner::factory()->create(['status' => 'approved', 'level' => 'silver']);
        $noLevelPartner = Partner::factory()->create(['status' => 'approved', 'level' => null]);

        CommissionScheme::create(['name' => 'Per Level Gold', 'type' => 'percentage', 'percentage' => 10, 'level' => 'gold']);

        $goldCustomer = Customer::create(['partner_id' => $goldPartner->id, 'name' => 'Gold Customer', 'project_value' => 1000000]);
        $silverCustomer = Customer::create(['partner_id' => $silverPartner->id, 'name' => 'Silver Customer', 'project_value' => 1000000]);
        $noLevelCustomer = Customer::create(['partner_id' => $noLevelPartner->id, 'name' => 'No Level Customer', 'project_value' => 1000000]);

        $this->assertSame('Per Level Gold', CommissionScheme::resolveFor($goldCustomer)?->name);
        $this->assertNull(CommissionScheme::resolveFor($silverCustomer));
        $this->assertNull(CommissionScheme::resolveFor($noLevelCustomer));
    }

    public function test_generating_commission_uses_the_level_scheme_rate(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved', 'level' => 'bronze']);
        CommissionScheme::create(['name' => 'Per Level Bronze', 'type' => 'percentage', 'percentage' => 3, 'level' => 'bronze']);

        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Bronze Customer', 'project_value' => 2000000]);

        $commission = Commission::generateForCustomer($customer);

        $this->assertEquals(60000, (float) $commission->amount);
    }

    public function test_admin_can_create_a_level_scoped_commission_scheme(): void
    {
        $admin = User::factory()->create();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\CommissionSchemeResource\Pages\ManageCommissionSchemes::class)
            ->callAction('create', data: [
                'name' => 'Skema Silver',
                'type' => 'percentage',
                'percentage' => 7,
                'level' => 'silver',
                'is_active' => true,
            ]);

        $this->assertDatabaseHas('commission_schemes', [
            'name' => 'Skema Silver',
            'level' => 'silver',
        ]);
    }
}
