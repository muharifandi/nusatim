<?php

namespace Tests\Feature;

use App\Filament\Partner\Pages\Auth\EditProfile;
use App\Mail\PartnerProjectClaimApproved;
use App\Models\Partner;
use App\Models\PartnerProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('partner'));
    }

    public function test_partner_can_update_biodata_and_bank_account(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($partner, 'partner')
            ->test(EditProfile::class)
            ->fillForm([
                'name' => 'Nama Baru',
                'bank_name' => 'Mandiri',
                'bank_account_number' => '999888',
                'bank_account_holder' => 'Nama Baru',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $partner->refresh();
        $this->assertSame('Nama Baru', $partner->name);
        $this->assertSame('Mandiri', $partner->bank_name);
        $this->assertSame('999888', $partner->bank_account_number);
    }

    public function test_replacing_the_ktp_path_deletes_the_old_file(): void
    {
        // Exercises Partner's DeletesOldFiles wiring directly (fileDisk() =
        // 'partner_documents', fileFields() includes ktp_path) rather than
        // fighting Livewire's fake-upload simulation for an EDIT form with
        // a pre-existing value - the Filament field itself is the same
        // FileUpload component already proven working on the registration
        // wizard (Fase 1) and other admin resources.
        Storage::disk('partner_documents')->put('registrations/old-ktp.jpg', 'fake-old');

        $partner = Partner::factory()->create([
            'status' => 'approved',
            'ktp_path' => 'registrations/old-ktp.jpg',
        ]);

        $partner->update(['ktp_path' => 'profile/new-ktp.jpg']);

        $this->assertTrue(Storage::disk('partner_documents')->missing('registrations/old-ktp.jpg'));
        $this->assertSame('profile/new-ktp.jpg', $partner->fresh()->ktp_path);
    }

    public function test_partner_can_change_their_password(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved', 'password' => Hash::make('old-password')]);

        Livewire::actingAs($partner, 'partner')
            ->test(EditProfile::class)
            ->fillForm([
                'password' => 'new-password-123',
                'passwordConfirmation' => 'new-password-123',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('new-password-123', $partner->fresh()->password));
        $this->assertFalse(Hash::check('old-password', $partner->fresh()->password));
    }

    public function test_disabling_email_notifications_stops_the_email_but_not_the_in_app_notification(): void
    {
        Mail::fake();

        $partner = Partner::factory()->create(['status' => 'approved']);

        Livewire::actingAs($partner, 'partner')
            ->test(EditProfile::class)
            ->fillForm(['email_notifications_enabled' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($partner->fresh()->email_notifications_enabled);

        $project = PartnerProject::create(['name' => 'Proyek Preferensi', 'status' => 'available']);
        $project->claim($partner);
        $project->approveClaim();

        Mail::assertNotSent(PartnerProjectClaimApproved::class);
        $this->assertSame(1, $partner->notifications()->count());
    }

    public function test_profile_page_renders(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($partner, 'partner')
            ->get(route('filament.partner.auth.profile'))
            ->assertOk();
    }
}
