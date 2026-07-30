<?php

namespace Tests\Feature;

use App\Filament\Pages\SendAnnouncement;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadReminder;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    protected function approvedCommission(Partner $partner, float $amount): Commission
    {
        $customer = Customer::create([
            'partner_id' => $partner->id,
            'name' => 'Customer '.uniqid(),
            'project_value' => $amount,
        ]);

        return Commission::create([
            'customer_id' => $customer->id,
            'partner_id' => $partner->id,
            'amount' => $amount,
            'type' => 'flat',
            'status' => 'approved',
        ]);
    }

    public function test_lead_status_change_notifies_the_owning_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Notif', 'phone' => '0811']);

        $lead->update(['status' => 'contacted']);

        $this->assertSame(1, $partner->notifications()->count());
    }

    public function test_publishing_a_project_notifies_every_approved_partner(): void
    {
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);
        $pending = Partner::factory()->create(['status' => 'pending_review']);

        $project = PartnerProject::create(['name' => 'Proyek Publish', 'status' => 'draft']);
        $project->publish();

        $this->assertSame(1, $partnerA->notifications()->count());
        $this->assertSame(1, $partnerB->notifications()->count());
        $this->assertSame(0, $pending->notifications()->count());
    }

    public function test_approving_a_claim_sends_both_mail_and_database_notification(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Proyek Klaim', 'status' => 'available']);
        $project->claim($partner);

        $project->approveClaim();

        $this->assertSame(1, $partner->notifications()->count());
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PartnerProjectClaimApproved::class);
    }

    public function test_generating_a_commission_notifies_the_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Komisi', 'project_value' => 1000000]);

        Commission::generateForCustomer($customer);

        $this->assertSame(1, $partner->notifications()->count());
    }

    public function test_approving_a_withdrawal_notifies_the_partner(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $this->approvedCommission($partner, 500000);
        $withdrawal = Withdrawal::submit($partner, ['amount' => 300000, 'ktp_path' => 'withdrawals/ktp.jpg']);

        $withdrawal->approve();

        $this->assertSame(1, $partner->notifications()->count());
    }

    public function test_due_reminders_are_notified_once_and_not_repeated(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Reminder', 'phone' => '0822']);
        $reminder = $lead->reminders()->create([
            'type' => 'follow_up',
            'remind_at' => now()->subMinute(),
        ]);

        Artisan::call('reminders:notify-due');

        $this->assertSame(1, $partner->notifications()->count());
        $this->assertNotNull($reminder->fresh()->notified_at);

        Artisan::call('reminders:notify-due');

        $this->assertSame(1, $partner->notifications()->count());
    }

    public function test_reminder_not_yet_due_is_not_notified(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $lead = Lead::create(['partner_id' => $partner->id, 'name' => 'Lead Masa Depan', 'phone' => '0833']);
        $lead->reminders()->create([
            'type' => 'meeting',
            'remind_at' => now()->addDay(),
        ]);

        Artisan::call('reminders:notify-due');

        $this->assertSame(0, $partner->notifications()->count());
    }

    public function test_admin_can_broadcast_an_announcement_to_all_approved_partners(): void
    {
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        $admin = User::factory()->create();
        $partnerA = Partner::factory()->create(['status' => 'approved']);
        $partnerB = Partner::factory()->create(['status' => 'approved']);
        $pending = Partner::factory()->create(['status' => 'pending_review']);

        Livewire::actingAs($admin, 'web')
            ->test(SendAnnouncement::class)
            ->fillForm([
                'title' => 'Libur Nasional',
                'body' => 'Kantor tutup besok.',
                'recipients' => 'all',
            ])
            ->call('send');

        $this->assertSame(1, $partnerA->notifications()->count());
        $this->assertSame(1, $partnerB->notifications()->count());
        $this->assertSame(0, $pending->notifications()->count());
    }

    public function test_admin_can_broadcast_an_announcement_to_specific_partners(): void
    {
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        $admin = User::factory()->create();
        $target = Partner::factory()->create(['status' => 'approved']);
        $other = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($admin, 'web')
            ->test(SendAnnouncement::class)
            ->fillForm([
                'title' => 'Info Khusus',
                'body' => 'Untuk kamu saja.',
                'recipients' => 'specific',
                'partner_ids' => [$target->id],
            ])
            ->call('send');

        $this->assertSame(1, $target->notifications()->count());
        $this->assertSame(0, $other->notifications()->count());
    }

    public function test_send_announcement_page_renders(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'web')
            ->get(route('filament.admin.pages.send-announcement'))
            ->assertOk();
    }
}
