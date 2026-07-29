<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\CommissionScheme;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_a_project_claim_creates_a_customer_idempotently(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Proyek Komisi', 'status' => 'available', 'budget' => 20000000]);
        $project->claim($partner);

        $project->approveClaim();
        $project->approveClaim();

        $this->assertSame(1, Customer::where('partner_project_id', $project->id)->count());

        $customer = Customer::where('partner_project_id', $project->id)->firstOrFail();
        $this->assertSame($partner->id, $customer->partner_id);
        $this->assertEquals(20000000, (float) $customer->project_value);
    }

    public function test_scheme_resolution_prioritizes_project_over_partner_over_service_over_global(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $service = Service::create(['title' => 'Web Development', 'slug' => 'web-dev', 'is_active' => true, 'order' => 1]);
        $project = PartnerProject::create(['name' => 'Proyek Prioritas', 'status' => 'available', 'service_id' => $service->id]);

        $customer = Customer::create([
            'partner_id' => $partner->id,
            'partner_project_id' => $project->id,
            'name' => 'Customer Prioritas',
            'service_id' => $service->id,
            'project_value' => 1000000,
        ]);

        $global = CommissionScheme::create(['name' => 'Global', 'type' => 'percentage', 'percentage' => 5]);
        $this->assertSame($global->id, CommissionScheme::resolveFor($customer)->id);

        $byService = CommissionScheme::create(['name' => 'Per Produk', 'type' => 'percentage', 'percentage' => 8, 'service_id' => $service->id]);
        $this->assertSame($byService->id, CommissionScheme::resolveFor($customer)->id);

        $byPartner = CommissionScheme::create(['name' => 'Per Partner', 'type' => 'percentage', 'percentage' => 10, 'partner_id' => $partner->id]);
        $this->assertSame($byPartner->id, CommissionScheme::resolveFor($customer)->id);

        $byProject = CommissionScheme::create(['name' => 'Per Project', 'type' => 'flat', 'flat_amount' => 500000, 'partner_project_id' => $project->id]);
        $this->assertSame($byProject->id, CommissionScheme::resolveFor($customer)->id);
    }

    public function test_generating_a_commission_computes_percentage_and_flat_amounts_and_is_idempotent(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $percentageCustomer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Persen', 'project_value' => 10000000]);
        CommissionScheme::create(['name' => 'Skema Persen', 'type' => 'percentage', 'percentage' => 10]);

        $commission = Commission::generateForCustomer($percentageCustomer);
        $this->assertEquals(1000000, (float) $commission->amount);

        $again = Commission::generateForCustomer($percentageCustomer);
        $this->assertSame($commission->id, $again->id);
        $this->assertSame(1, Commission::where('customer_id', $percentageCustomer->id)->count());

        $flatCustomer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Flat', 'project_value' => 10000000]);
        CommissionScheme::create(['name' => 'Skema Flat Khusus', 'type' => 'flat', 'flat_amount' => 750000, 'partner_id' => $partner->id]);

        $flatCommission = Commission::generateForCustomer($flatCustomer);
        $this->assertEquals(750000, (float) $flatCommission->amount);
    }

    public function test_status_changes_are_recorded_in_the_audit_trail(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Audit', 'project_value' => 5000000]);
        $commission = Commission::generateForCustomer($customer);

        $commission->approve();
        $commission->markPaid();

        $this->assertSame(2, $commission->histories()->count());
        $this->assertSame('pending', $commission->histories()->first()->from_status);
        $this->assertSame('approved', $commission->histories()->first()->to_status);
        $this->assertSame('paid', $commission->histories()->latest('id')->first()->to_status);
    }

    public function test_rejecting_a_commission_records_the_reason(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Ditolak', 'project_value' => 2000000]);
        $commission = Commission::generateForCustomer($customer);

        $commission->reject('Data project tidak valid');

        $this->assertSame('rejected', $commission->fresh()->status);
        $this->assertSame('Data project tidak valid', $commission->fresh()->rejection_reason);
        $this->assertSame('Data project tidak valid', $commission->histories()->latest('id')->first()->note);
    }

    public function test_partner_can_only_see_their_own_commissions(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $customerA = Customer::create(['partner_id' => $partnerA->id, 'name' => 'Customer A', 'project_value' => 1000000]);
        Commission::generateForCustomer($customerA);

        $response = $this->actingAs($partnerB, 'partner')
            ->get(route('filament.partner.resources.commissions.index'));

        $response->assertOk();
        $response->assertDontSee('Customer A');
    }

    public function test_admin_can_add_a_bonus_commission_without_a_scheme(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);

        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\CommissionResource\Pages\ManageCommissions::class)
            ->callTableAction('addBonus', data: [
                'partner_id' => $partner->id,
                'amount' => 300000,
                'note' => 'Bonus pencapaian target',
            ]);

        $bonus = Commission::where('partner_id', $partner->id)->where('is_bonus', true)->first();
        $this->assertNotNull($bonus);
        $this->assertEquals(300000, (float) $bonus->amount);
        $this->assertSame('Bonus pencapaian target', $bonus->note);
        $this->assertNull($bonus->customer_id);
    }

    public function test_commission_pages_render_for_partner_and_admin(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.commissions.index'))
            ->assertOk();

        // Guard must be explicit here: actingAs($partner, 'partner') above
        // mutates the global auth.defaults.guard config (see Laravel's
        // AuthManager::shouldUse()) - a later actingAs($admin) with no
        // guard argument would silently authenticate admin on the
        // 'partner' guard too, leaving 'web' unauthenticated.
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.resources.commissions.index'))
            ->assertOk();
        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.resources.commission-schemes.index'))
            ->assertOk();
    }
}
