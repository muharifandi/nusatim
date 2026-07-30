<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_report_counts_by_status_and_per_partner_totals(): void
    {
        $approved = Partner::factory()->create(['status' => 'approved']);
        Partner::factory()->create(['status' => 'pending_review']);

        Lead::create(['partner_id' => $approved->id, 'name' => 'Lead A', 'phone' => '0811']);
        $customer = Customer::create(['partner_id' => $approved->id, 'name' => 'Customer A', 'project_value' => 1000000]);
        Commission::create([
            'customer_id' => $customer->id,
            'partner_id' => $approved->id,
            'amount' => 100000,
            'type' => 'flat',
            'status' => 'approved',
        ]);

        $report = (new ReportService)->partnerReport();

        $this->assertSame(1, $report['by_status']['approved']);
        $this->assertSame(1, $report['by_status']['pending_review']);

        $row = collect($report['rows'])->firstWhere('name', $approved->name);
        $this->assertSame(1, $row['leads']);
        $this->assertSame(1, $row['customers']);
        $this->assertEquals(100000, $row['commission']);
    }

    public function test_date_filter_excludes_records_outside_the_range(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $inRange = Customer::create(['partner_id' => $partner->id, 'name' => 'In Range', 'project_value' => 500000]);
        $inRange->created_at = '2026-06-15';
        $inRange->save();

        $outOfRange = Customer::create(['partner_id' => $partner->id, 'name' => 'Out of Range', 'project_value' => 999999]);
        $outOfRange->created_at = '2026-01-01';
        $outOfRange->save();

        $service = new ReportService('2026-06-01', '2026-06-30');

        $this->assertEquals(500000, $service->totalSalesReport());
    }

    public function test_commission_and_withdrawal_reports_break_down_by_status_and_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer B', 'project_value' => 2000000]);

        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 200000, 'type' => 'flat', 'status' => 'approved']);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 50000, 'type' => 'bonus', 'status' => 'paid', 'is_bonus' => true]);

        $report = (new ReportService)->commissionReport();

        $this->assertEquals(250000, $report['total']);
        $this->assertEquals(200000, $report['by_status']['approved']);
        $this->assertEquals(50000, $report['by_status']['paid']);
        $this->assertEquals(250000, $report['by_partner'][$partner->name]);
    }

    public function test_partner_performance_report_is_sorted_descending_by_commission(): void
    {
        $low = Partner::factory()->create(['status' => 'approved']);
        $high = Partner::factory()->create(['status' => 'approved']);

        $customerLow = Customer::create(['partner_id' => $low->id, 'name' => 'C Low', 'project_value' => 100000]);
        Commission::create(['customer_id' => $customerLow->id, 'partner_id' => $low->id, 'amount' => 10000, 'type' => 'flat', 'status' => 'approved']);

        $customerHigh = Customer::create(['partner_id' => $high->id, 'name' => 'C High', 'project_value' => 900000]);
        Commission::create(['customer_id' => $customerHigh->id, 'partner_id' => $high->id, 'amount' => 90000, 'type' => 'flat', 'status' => 'approved']);

        $ranking = (new ReportService)->partnerPerformanceReport();

        $this->assertSame($high->name, $ranking->first()['name']);
        $this->assertSame($low->name, $ranking->last()['name']);
    }

    public function test_export_csv_returns_a_downloadable_file_with_header_row(): void
    {
        $admin = User::factory()->create();
        $partner = Partner::factory()->create(['status' => 'approved']);
        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Export', 'phone' => '0822']);

        $response = $this->actingAs($admin, 'web')->get(route('admin.reports.export', ['report' => 'lead']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Partner,Status,Jumlah', $content);
        $this->assertStringContainsString($partner->name, $content);
    }

    public function test_export_rejects_an_unknown_report_name(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'web')
            ->get(route('admin.reports.export', ['report' => 'not-a-real-report']))
            ->assertNotFound();
    }

    public function test_reports_page_renders(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.pages.reports'))
            ->assertOk();
    }
}
