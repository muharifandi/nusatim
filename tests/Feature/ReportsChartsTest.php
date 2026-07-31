<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsChartsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_renders_and_registers_the_4_chart_widgets(): void
    {
        $admin = User::factory()->create();

        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Chart', 'project_value' => 1000000]);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 100000, 'type' => 'flat', 'status' => 'paid']);
        PartnerProject::create(['name' => 'Project Chart', 'status' => 'available']);

        // Widgets are lazy-loaded by Livewire (a plain HTTP GET only
        // renders the placeholder shell, not widget content) - assert the
        // page itself loads fine, and check widget registration + each
        // widget's own render separately below.
        $this->actingAs($admin)
            ->get(route('filament.admin.pages.reports'))
            ->assertOk();

        $reports = new \App\Filament\Pages\Reports;
        $widgets = (new \ReflectionMethod($reports, 'getHeaderWidgets'))->invoke($reports);

        $this->assertSame([
            \App\Filament\Widgets\AdminClosingTrendChart::class,
            \App\Filament\Widgets\AdminCommissionStatusChart::class,
            \App\Filament\Widgets\AdminProjectStatusChart::class,
            \App\Filament\Widgets\AdminPartnerPerformanceChart::class,
        ], $widgets);

        foreach ($widgets as $widgetClass) {
            // Rendering without throwing is the signal here - a broken
            // getData()/getOptions() would raise during mount/render.
            $html = Livewire::actingAs($admin)->test($widgetClass)->html();
            $this->assertNotEmpty($html);
        }
    }

    protected function invokeGetData(object $widget): array
    {
        $method = new \ReflectionMethod($widget, 'getData');
        $method->setAccessible(true);

        return $method->invoke($widget);
    }

    public function test_closing_trend_chart_counts_customers_per_month(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        Customer::create(['partner_id' => $partner->id, 'name' => 'C1', 'project_value' => 1000000]);
        Customer::create(['partner_id' => $partner->id, 'name' => 'C2', 'project_value' => 1000000]);

        $data = $this->invokeGetData(new \App\Filament\Widgets\AdminClosingTrendChart);

        $this->assertSame(2, array_sum($data['datasets'][0]['data']));
    }

    public function test_commission_status_chart_sums_by_status(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'C1', 'project_value' => 1000000]);
        Commission::create(['customer_id' => $customer->id, 'partner_id' => $partner->id, 'amount' => 500000, 'type' => 'flat', 'status' => 'paid']);

        $data = $this->invokeGetData(new \App\Filament\Widgets\AdminCommissionStatusChart);

        $paidIndex = array_search('Paid', $data['labels']);
        $this->assertSame(500000.0, $data['datasets'][0]['data'][$paidIndex]);
    }
}
