<?php

namespace Tests\Feature;

use App\Filament\Partner\Pages\Pipeline;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\Service;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // See LeadManagementTest - Livewire::test() needs the panel context
        // set explicitly, since there's no HTTP request to derive it from.
        Filament::setCurrentPanel(Filament::getPanel('partner'));
    }

    public function test_moving_a_lead_to_won_updates_status_logs_activity_and_creates_customer(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create([
            'partner_id' => $partner->id,
            'name' => 'Lead Drag Drop',
            'phone' => '0877777777',
        ]);

        Livewire::actingAs($partner, 'partner')
            ->test(Pipeline::class)
            ->call('moveLead', $lead->id, 'won');

        $lead->refresh();

        $this->assertSame('won', $lead->status);
        $this->assertSame(1, $lead->activities()->where('type', 'status_change')->count());
        $this->assertSame(1, Customer::where('lead_id', $lead->id)->count());
    }

    public function test_moving_a_lead_rejects_an_invalid_status(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create([
            'partner_id' => $partner->id,
            'name' => 'Lead Status Invalid',
            'phone' => '0888888888',
        ]);

        Livewire::actingAs($partner, 'partner')
            ->test(Pipeline::class)
            ->call('moveLead', $lead->id, 'not-a-real-status')
            ->assertStatus(422);

        $this->assertSame('new', $lead->fresh()->status);
    }

    public function test_partner_cannot_move_another_partners_lead(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $leadA = Lead::create([
            'partner_id' => $partnerA->id,
            'name' => 'Lead Milik A',
            'phone' => '0899999999',
        ]);

        // findOrFail() throwing here (rather than silently updating) is the
        // actual proof the scoping query works - Livewire::test() doesn't
        // convert this into an assertable HTTP response the way a real
        // request would, so assert on the exception directly.
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($partnerB, 'partner')
            ->test(Pipeline::class)
            ->call('moveLead', $leadA->id, 'won');
    }

    public function test_filters_narrow_down_the_board(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $serviceA = Service::create(['title' => 'Web Development', 'slug' => 'web-development', 'is_active' => true, 'order' => 1]);
        $serviceB = Service::create(['title' => 'SEO', 'slug' => 'seo', 'is_active' => true, 'order' => 2]);

        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead A', 'phone' => '0810000001', 'service_id' => $serviceA->id]);
        Lead::create(['partner_id' => $partner->id, 'name' => 'Lead B', 'phone' => '0810000002', 'service_id' => $serviceB->id]);

        $component = Livewire::actingAs($partner, 'partner')->test(Pipeline::class);

        $this->assertSame(2, $component->instance()->getLeadsByStatus()['new']->count());

        $component->set('serviceFilter', $serviceA->id);

        $this->assertSame(1, $component->instance()->getLeadsByStatus()['new']->count());
    }

    public function test_pipeline_page_renders_all_status_columns(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.pages.pipeline'))
            ->assertOk()
            ->assertSee('New')
            ->assertSee('Won')
            ->assertSee('Lost');
    }
}
