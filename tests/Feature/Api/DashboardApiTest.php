<?php

namespace Tests\Feature\Api;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\PartnerSalesTarget;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(Partner $partner): string
    {
        return $partner->createToken('mobile')->plainTextToken;
    }

    public function test_pending_partner_is_blocked_from_the_dashboard(): void
    {
        $partner = Partner::factory()->create(['status' => 'pending_review']);
        $token = $this->tokenFor($partner);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/dashboard')
            ->assertForbidden()
            ->assertJsonPath('status', 'pending_review');
    }

    public function test_activity_and_finance_numbers_are_scoped_to_the_logged_in_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Milik Saya', 'phone' => '0811', 'status' => 'opportunity']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Baru', 'phone' => '0812']);
        Lead::create(['partner_id' => $other->id, 'name' => 'Lead Orang Lain', 'phone' => '0813']);

        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Saya', 'project_value' => 5000000]);

        PartnerProject::create(['name' => 'Available Board', 'status' => 'available']);
        PartnerProject::create(['name' => 'Project Saya', 'status' => 'assigned', 'partner_id' => $partner->id]);

        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 300000, 'type' => 'flat', 'status' => 'pending']);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 200000, 'type' => 'flat', 'status' => 'approved']);

        Withdrawal::create([
            'partner_id' => $partner->id,
            'amount' => 150000,
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_holder' => $partner->name,
            'ktp_path' => 'x.jpg',
            'status' => 'paid',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/dashboard');

        $response->assertOk();
        $response->assertJsonPath('activity.total_leads', 2);
        $response->assertJsonPath('activity.total_opportunities', 1);
        $response->assertJsonPath('activity.total_customers', 1);
        $response->assertJsonPath('activity.total_projects', 1);
        $response->assertJsonPath('activity.available_projects', 1);

        $response->assertJsonPath('finance.total_project_value', 5000000);
        $response->assertJsonPath('finance.total_commission', 500000);
        $response->assertJsonPath('finance.pending_commission', 300000);
        $response->assertJsonPath('finance.available_balance', 200000);
        $response->assertJsonPath('finance.total_withdrawn', 150000);
    }

    public function test_todays_reminders_are_split_by_type(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Reminder', 'phone' => '0821']);

        $lead->reminders()->create(['type' => 'follow_up', 'remind_at' => now()]);
        $lead->reminders()->create(['type' => 'meeting', 'remind_at' => now()]);
        $lead->reminders()->create(['type' => 'meeting', 'remind_at' => now()->addDay()]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/dashboard');

        $response->assertJsonPath('activity.follow_ups_today', 1);
        $response->assertJsonPath('activity.meetings_today', 1);
    }

    public function test_sales_target_percentage_is_computed_against_project_value(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Target', 'project_value' => 2500000]);
        PartnerSalesTarget::create([
            'partner_id' => $partner->id,
            'period' => now()->startOfMonth()->toDateString(),
            'target_amount' => 10000000,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/dashboard');

        $response->assertJsonPath('finance.sales_target.target_amount', 10000000);
        $response->assertJsonPath('finance.sales_target.achieved_amount', 2500000);
        $response->assertJsonPath('finance.sales_target.achieved_percentage', 25);
    }

    public function test_pipeline_counts_leads_by_status(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        Lead::create(['partner_id' => $partner->id, 'name' => 'A', 'phone' => '1', 'status' => 'new']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'B', 'phone' => '2', 'status' => 'new']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'C', 'phone' => '3', 'status' => 'won']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/dashboard');

        $response->assertJsonPath('pipeline.new', 2);
        $response->assertJsonPath('pipeline.won', 1);
        $response->assertJsonPath('pipeline.lost', 0);
    }

    public function test_closing_and_commission_trends_cover_trailing_12_months(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $token = $this->tokenFor($partner);

        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Trend Customer', 'project_value' => 1000000]);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 400000, 'type' => 'flat', 'status' => 'approved']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/dashboard');

        $response->assertOk();
        $response->assertJsonCount(12, 'closing_trend.labels');
        $response->assertJsonCount(12, 'closing_trend.data');
        $response->assertJsonCount(12, 'commission_trend.labels');
        $response->assertJsonCount(12, 'commission_trend.data');

        $this->assertSame(1, array_sum($response->json('closing_trend.data')));
        $this->assertEquals(400000, array_sum($response->json('commission_trend.data')));
    }
}
