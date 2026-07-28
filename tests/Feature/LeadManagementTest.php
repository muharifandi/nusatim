<?php

namespace Tests\Feature;

use App\Filament\Partner\Resources\LeadResource\Pages\CreateLead;
use App\Filament\Partner\Resources\LeadResource\Pages\EditLead;
use App\Filament\Partner\Resources\LeadResource\Pages\ListLeads;
use App\Filament\Resources\LeadResource\Pages\ManageLeads;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadDocument;
use App\Models\Partner;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Livewire::test() boots a component in isolation, without the HTTP
        // middleware that normally sets "current panel" from the request
        // URL - without this, Filament resolves resource/action routes
        // against the default 'admin' panel even for partner-panel pages,
        // which share resource slugs (both LeadResource classes -> 'leads')
        // and produces "route not defined" errors that have nothing to do
        // with the actual feature being tested.
        Filament::setCurrentPanel(Filament::getPanel('partner'));
    }

    public function test_partner_can_create_a_lead_and_it_is_logged_to_the_timeline(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($partner, 'partner')
            ->test(CreateLead::class)
            ->fillForm([
                'name' => 'PT Contoh Jaya',
                'phone' => '08123456789',
                'email' => 'contoh@example.com',
                'estimated_value' => 5000000,
                'status' => 'new',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $lead = Lead::where('name', 'PT Contoh Jaya')->firstOrFail();

        $this->assertSame($partner->id, $lead->partner_id);
        $this->assertSame(1, $lead->activities()->where('type', 'created')->count());
    }

    public function test_marking_a_lead_won_creates_a_customer_exactly_once(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create([
            'partner_id' => $partner->id,
            'name' => 'PT Menang Terus',
            'phone' => '0811111111',
            'estimated_value' => 10000000,
        ]);

        $lead->markWon();
        $lead->markWon();

        $this->assertSame(1, Customer::where('lead_id', $lead->id)->count());

        $customer = Customer::where('lead_id', $lead->id)->firstOrFail();
        $this->assertSame($partner->id, $customer->partner_id);
        $this->assertEquals(10000000, (float) $customer->project_value);
        $this->assertSame(1, $lead->activities()->where('type', 'status_change')->count());
    }

    public function test_marking_a_lead_lost_does_not_create_a_customer(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create([
            'partner_id' => $partner->id,
            'name' => 'PT Batal Deal',
            'phone' => '0822222222',
        ]);

        $lead->markLost();

        $this->assertSame('lost', $lead->fresh()->status);
        $this->assertSame(0, Customer::where('lead_id', $lead->id)->count());
    }

    public function test_partner_cannot_see_or_edit_another_partners_lead(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $leadA = Lead::create([
            'partner_id' => $partnerA->id,
            'name' => 'Lead Milik A',
            'phone' => '0833333333',
        ]);

        Livewire::actingAs($partnerB, 'partner')
            ->test(ListLeads::class)
            ->assertCanNotSeeTableRecords([$leadA]);

        $this->actingAs($partnerB, 'partner')
            ->get(route('filament.partner.resources.leads.edit', $leadA))
            ->assertNotFound();
    }

    public function test_partner_can_only_view_their_own_lead_documents(): void
    {
        $owner = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create([
            'partner_id' => $owner->id,
            'name' => 'Lead Dengan Dokumen',
            'phone' => '0844444444',
        ]);

        Storage::disk('lead_documents')->put('leads/contoh.pdf', 'fake-pdf-content');
        $document = LeadDocument::create([
            'lead_id' => $lead->id,
            'path' => 'leads/contoh.pdf',
            'original_name' => 'contoh.pdf',
        ]);

        $this->actingAs($other, 'partner')
            ->get(route('lead.documents.show', $document))
            ->assertForbidden();

        $this->actingAs($owner, 'partner')
            ->get(route('lead.documents.show', $document))
            ->assertOk();
    }

    public function test_lead_view_and_customer_view_pages_render_without_error(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create([
            'partner_id' => $partner->id,
            'name' => 'Lead Untuk Dilihat',
            'phone' => '0866666666',
        ]);
        $lead->reminders()->create([
            'type' => 'follow_up',
            'remind_at' => now()->addDay(),
        ]);
        $customer = $lead->markWon();

        // Real HTTP requests (not Livewire::test()) so the panel-detection
        // middleware runs exactly as it does in production - this is what
        // actually exercises the RelationManagers (Documents/Reminders/
        // Activities) and the Customer Infolist rendering end-to-end.
        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.leads.view', $lead))
            ->assertOk()
            ->assertSee('Reminder Follow Up & Meeting')
            ->assertSee('Timeline & Catatan Internal');

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.resources.customers.view', $customer))
            ->assertOk()
            ->assertSee('Riwayat Aktivitas');
    }

    public function test_admin_can_validate_and_transfer_lead_ownership(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->create();
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);

        $lead = Lead::create([
            'partner_id' => $partnerA->id,
            'name' => 'Lead Untuk Dipindah',
            'phone' => '0855555555',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageLeads::class)
            ->callTableAction('validate', $lead)
            ->callTableAction('transferOwnership', $lead, data: ['partner_id' => $partnerB->id]);

        $lead->refresh();

        $this->assertTrue($lead->is_validated);
        $this->assertSame($partnerB->id, $lead->partner_id);
    }
}
