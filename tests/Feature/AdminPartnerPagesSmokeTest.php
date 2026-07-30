<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\CommissionScheme;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\MarketingMaterial;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\PartnerSalesTarget;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Broad HTTP-level smoke test across every admin + partner panel page
 * (index/list pages, plus a handful of view/edit pages where a record is
 * needed anyway) - not a substitute for actually looking at rendered
 * pages in a browser, but catches PHP errors, missing views, and broken
 * routes across the whole surface in one pass.
 */
class AdminPartnerPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_index_page_renders(): void
    {
        $admin = User::factory()->create();

        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Smoke', 'phone' => '0811']);
        $project = PartnerProject::create(['name' => 'Project Smoke', 'status' => 'available']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Smoke', 'project_value' => 1000000]);
        CommissionScheme::create(['name' => 'Skema Smoke', 'type' => 'flat', 'flat_amount' => 100000]);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 100000, 'type' => 'flat', 'status' => 'pending']);
        Withdrawal::create(['partner_id' => $partner->id, 'amount' => 100000, 'bank_name' => 'BCA', 'bank_account_number' => '123', 'bank_account_holder' => $partner->name, 'ktp_path' => 'x.jpg', 'status' => 'pending']);
        MarketingMaterial::create(['title' => 'Materi Smoke', 'category' => 'faq', 'content' => 'x', 'is_active' => true, 'order' => 1]);
        PartnerSalesTarget::create(['partner_id' => $partner->id, 'period' => now()->startOfMonth(), 'target_amount' => 5000000]);
        SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'Tiket Smoke', 'description' => 'x']);

        $routes = [
            'filament.admin.pages.dashboard',
            'filament.admin.resources.partners.index',
            'filament.admin.resources.leads.index',
            'filament.admin.resources.partner-projects.index',
            'filament.admin.resources.commission-schemes.index',
            'filament.admin.resources.commissions.index',
            'filament.admin.resources.withdrawals.index',
            'filament.admin.resources.marketing-materials.index',
            'filament.admin.resources.partner-sales-targets.index',
            'filament.admin.resources.support-tickets.index',
            'filament.admin.resources.roles.index',
            'filament.admin.resources.users.index',
            'filament.admin.resources.workflow-assignments.index',
            'filament.admin.resources.audit-logs.index',
            'filament.admin.pages.reports',
            'filament.admin.pages.manage-partner-settings',
            'filament.admin.pages.send-announcement',
        ];

        foreach ($routes as $routeName) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk();
        }

        // Every admin resource here is a single-page ManageRecords pattern
        // (view/edit happen via modal table actions on the index page, not
        // separate routes) - exercise those modals too, since that's where
        // infolist()/form() with real related data (relations, badges,
        // computed accessors) actually gets touched.
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PartnerResource\Pages\ManagePartners::class)
            ->mountTableAction('view', $partner)
            ->assertTableActionMounted('view');

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\SupportTicketResource\Pages\ManageSupportTickets::class)
            ->mountTableAction('view', SupportTicket::first())
            ->assertTableActionMounted('view');
    }

    public function test_every_partner_index_page_renders(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Smoke', 'phone' => '0811']);
        $project = PartnerProject::create(['name' => 'Project Smoke', 'status' => 'assigned', 'partner_id' => $partner->id]);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Smoke', 'project_value' => 1000000]);
        MarketingMaterial::create(['title' => 'Materi Smoke', 'category' => 'faq', 'content' => 'x', 'is_active' => true, 'order' => 1]);
        SupportTicket::create(['partner_id' => $partner->id, 'subject' => 'Tiket Smoke', 'description' => 'x']);

        $routes = [
            'filament.partner.pages.dashboard',
            'filament.partner.pages.pipeline',
            'filament.partner.resources.leads.index',
            'filament.partner.resources.customers.index',
            'filament.partner.resources.partner-projects.index',
            'filament.partner.resources.marketing-materials.index',
            'filament.partner.resources.support-tickets.index',
        ];

        foreach ($routes as $routeName) {
            $this->actingAs($partner, 'partner')
                ->get(route($routeName))
                ->assertOk();
        }

        $this->actingAs($partner, 'partner')->get(route('filament.partner.resources.leads.view', $lead))->assertOk();
        $this->actingAs($partner, 'partner')->get(route('filament.partner.resources.customers.view', $customer))->assertOk();
        $this->actingAs($partner, 'partner')->get(route('filament.partner.resources.partner-projects.view', $project))->assertOk();
        $this->actingAs($partner, 'partner')->get(route('filament.partner.resources.support-tickets.view', SupportTicket::first()))->assertOk();
    }
}
