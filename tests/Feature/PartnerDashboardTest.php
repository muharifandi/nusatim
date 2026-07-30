<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\PartnerSalesTarget;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_stats_are_scoped_to_the_logged_in_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);

        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Milik Saya', 'phone' => '0811', 'status' => 'opportunity']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Baru', 'phone' => '0812']);
        Lead::create(['partner_id' => $other->id, 'name' => 'Lead Orang Lain', 'phone' => '0813']);

        Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Saya', 'project_value' => 1000000]);

        PartnerProject::create(['name' => 'Available Board', 'status' => 'available']);
        $ownProject = PartnerProject::create(['name' => 'Project Saya', 'status' => 'assigned', 'partner_id' => $partner->id]);

        $widget = new \App\Filament\Partner\Widgets\PartnerActivityStats;
        $this->actingAs($partner, 'partner');

        $stats = $this->invokeGetStats($widget);

        $this->assertSame(2, $stats['Total Lead']);
        $this->assertSame(1, $stats['Total Opportunity']);
        $this->assertSame(1, $stats['Total Customer']);
        $this->assertSame(1, $stats['Total Project']);
        $this->assertSame(1, $stats['Project Available']);
    }

    public function test_todays_reminders_are_split_by_type(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Reminder', 'phone' => '0821']);

        $lead->reminders()->create(['type' => 'follow_up', 'remind_at' => now()]);
        $lead->reminders()->create(['type' => 'meeting', 'remind_at' => now()]);
        $lead->reminders()->create(['type' => 'meeting', 'remind_at' => now()->addDay()]);

        $this->actingAs($partner, 'partner');
        $stats = $this->invokeGetStats(new \App\Filament\Partner\Widgets\PartnerActivityStats);

        $this->assertSame(1, $stats['Follow Up Hari Ini']);
        $this->assertSame(1, $stats['Meeting Hari Ini']);
    }

    public function test_finance_stats_compute_correct_totals(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Finance', 'project_value' => 5000000]);

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

        $this->actingAs($partner, 'partner');
        $stats = $this->invokeGetStats(new \App\Filament\Partner\Widgets\PartnerFinanceStats);

        $this->assertSame('Rp5.000.000', $stats['Total Nilai Project']);
        $this->assertSame('Rp500.000', $stats['Total Komisi']);
        $this->assertSame('Rp300.000', $stats['Komisi Pending']);
        $this->assertSame('Rp200.000', $stats['Komisi Ready Withdrawal']);
        $this->assertSame('Rp150.000', $stats['Total Withdrawal']);
    }

    public function test_sales_target_percentage_and_missing_target_message(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Target', 'project_value' => 2500000]);

        $this->actingAs($partner, 'partner');
        $withoutTarget = $this->invokeGetStats(new \App\Filament\Partner\Widgets\PartnerFinanceStats);
        $this->assertStringContainsString('Belum ada target', $withoutTarget['__descriptions']['Target Penjualan Bulan Ini']);

        PartnerSalesTarget::create([
            'partner_id' => $partner->id,
            'period' => now()->startOfMonth(),
            'target_amount' => 5000000,
        ]);

        $withTarget = $this->invokeGetStats(new \App\Filament\Partner\Widgets\PartnerFinanceStats);
        $this->assertSame('50%', $withTarget['Target Penjualan Bulan Ini']);
    }

    public function test_dashboard_and_sales_target_pages_render(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.pages.dashboard'))
            ->assertOk();

        $admin = User::factory()->create();
        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.resources.partner-sales-targets.index'))
            ->assertOk();
    }

    /**
     * @return array<string, mixed>
     */
    protected function invokeGetStats(object $widget): array
    {
        $method = new \ReflectionMethod($widget, 'getStats');
        $method->setAccessible(true);

        $stats = $method->invoke($widget);
        $result = [];

        foreach ($stats as $stat) {
            $label = (new \ReflectionProperty($stat, 'label'))->getValue($stat);
            $value = (new \ReflectionProperty($stat, 'value'))->getValue($stat);
            $description = (new \ReflectionProperty($stat, 'description'))->getValue($stat);

            $result[$label] = $value;
            $result['__descriptions'][$label] = $description;
        }

        return $result;
    }
}
