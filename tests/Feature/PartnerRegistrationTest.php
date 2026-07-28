<?php

namespace Tests\Feature;

use App\Filament\Partner\Pages\Auth\Register;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_wizard_creates_a_pending_partner(): void
    {
        Mail::fake();

        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Budi Tester',
                'email' => 'budi@example.com',
                'password' => 'password123',
                'passwordConfirmation' => 'password123',
                'profile_photo_path' => [UploadedFile::fake()->image('photo.jpg')],
                'ktp_path' => [UploadedFile::fake()->image('ktp.jpg')],
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'Budi Tester',
                'agreement' => true,
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $partner = Partner::where('email', 'budi@example.com')->firstOrFail();

        $this->assertSame('pending_review', $partner->status);
        $this->assertNotNull($partner->agreement_accepted_at);
        $this->assertNotNull($partner->ktp_path);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('partner_documents')->exists($partner->ktp_path));
    }

    public function test_unapproved_partner_is_redirected_to_status_page(): void
    {
        $partner = Partner::factory()->create(['status' => 'pending_review']);

        $response = $this->actingAs($partner, 'partner')->get('/partner');

        $response->assertRedirect(route('filament.partner.pages.status'));
    }

    public function test_approved_partner_reaches_the_dashboard(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        // With Filament\Pages\Dashboard registered at the panel root (see
        // PartnerPanelProvider), '/partner' resolves directly to it instead
        // of bouncing through the home-redirect fallback.
        $this->actingAs($partner, 'partner')->get('/partner')->assertOk();
    }

    public function test_rejected_partner_sees_the_rejection_reason(): void
    {
        $partner = Partner::factory()->create([
            'status' => 'rejected',
            'rejection_reason' => 'Dokumen KTP tidak terbaca.',
        ]);

        $response = $this->actingAs($partner, 'partner')->get(route('filament.partner.pages.status'));

        $response->assertOk();
        $response->assertSee('Dokumen KTP tidak terbaca.');
    }

    public function test_partner_guard_is_fully_isolated_from_admin_panel(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);

        $this->actingAs($partner, 'partner')
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_user_cannot_authenticate_on_partner_guard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web')
            ->get('/partner')
            ->assertRedirect('/partner/login');
    }

    public function test_partner_can_only_view_their_own_private_documents(): void
    {
        $owner = Partner::factory()->create(['ktp_path' => 'registrations/owner-ktp.jpg']);
        $other = Partner::factory()->create(['status' => 'approved']);

        \Illuminate\Support\Facades\Storage::disk('partner_documents')->put('registrations/owner-ktp.jpg', 'fake');

        $this->actingAs($other, 'partner')
            ->get(route('partner.documents.show', [$owner, 'ktp']))
            ->assertForbidden();

        $this->actingAs($owner, 'partner')
            ->get(route('partner.documents.show', [$owner, 'ktp']))
            ->assertOk();
    }
}
