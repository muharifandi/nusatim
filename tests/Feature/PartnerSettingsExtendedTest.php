<?php

namespace Tests\Feature;

use App\Filament\Partner\Pages\Auth\Register;
use App\Models\Commission;
use App\Models\CommissionScheme;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\PartnerProject;
use App\Models\PartnerSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerSettingsExtendedTest extends TestCase
{
    use RefreshDatabase;

    public function test_explicit_default_scheme_wins_over_the_scope_less_fallback(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer X', 'project_value' => 1000000]);

        $scopeless = CommissionScheme::create(['name' => 'Fallback Lama', 'type' => 'percentage', 'percentage' => 5]);
        $explicitDefault = CommissionScheme::create(['name' => 'Default Baru', 'type' => 'percentage', 'percentage' => 8]);

        PartnerSetting::current()->update(['default_commission_scheme_id' => $explicitDefault->id]);

        $this->assertSame($explicitDefault->id, CommissionScheme::resolveFor($customer)->id);
    }

    public function test_a_more_specific_scheme_still_wins_over_the_explicit_default(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $customer = Customer::create(['partner_id' => $partner->id, 'name' => 'Customer Y', 'project_value' => 1000000]);

        $explicitDefault = CommissionScheme::create(['name' => 'Default Baru', 'type' => 'percentage', 'percentage' => 8]);
        $byPartner = CommissionScheme::create(['name' => 'Khusus Partner', 'type' => 'flat', 'flat_amount' => 500000, 'partner_id' => $partner->id]);

        PartnerSetting::current()->update(['default_commission_scheme_id' => $explicitDefault->id]);

        $this->assertSame($byPartner->id, CommissionScheme::resolveFor($customer)->id);
    }

    public function test_claim_is_rejected_once_the_concurrent_limit_is_reached(): void
    {
        PartnerSetting::current()->update(['max_concurrent_claimed_projects' => 1]);

        $partner = Partner::factory()->create(['status' => 'approved']);
        $projectA = PartnerProject::create(['name' => 'Project A', 'status' => 'available']);
        $projectB = PartnerProject::create(['name' => 'Project B', 'status' => 'available']);

        $this->assertTrue($projectA->claim($partner));

        $this->expectException(ValidationException::class);
        $projectB->claim($partner);
    }

    public function test_claim_limit_is_not_enforced_when_the_setting_is_empty(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $projectA = PartnerProject::create(['name' => 'Project A', 'status' => 'available']);
        $projectB = PartnerProject::create(['name' => 'Project B', 'status' => 'available']);

        $this->assertTrue($projectA->claim($partner));
        $this->assertTrue($projectB->claim($partner));
    }

    public function test_expire_stale_claims_command_rejects_overdue_pending_claims_only(): void
    {
        PartnerSetting::current()->update(['claim_processing_hours' => 24]);

        $partner = Partner::factory()->create(['status' => 'approved']);

        $stale = PartnerProject::create(['name' => 'Stale', 'status' => 'available']);
        $stale->claim($partner);
        $stale->update(['claimed_at' => now()->subHours(48)]);

        $fresh = PartnerProject::create(['name' => 'Fresh', 'status' => 'available']);
        $fresh->claim($partner);
        $fresh->update(['claimed_at' => now()->subHours(2)]);

        Artisan::call('projects:expire-stale-claims');

        $this->assertSame('available', $stale->fresh()->status);
        $this->assertNull($stale->fresh()->partner_id);
        $this->assertSame('pending_approval', $fresh->fresh()->status);
    }

    public function test_expire_stale_claims_does_nothing_when_the_setting_is_empty(): void
    {
        $partner = Partner::factory()->create(['status' => 'approved']);
        $project = PartnerProject::create(['name' => 'Project', 'status' => 'available']);
        $project->claim($partner);
        $project->update(['claimed_at' => now()->subDays(30)]);

        Artisan::call('projects:expire-stale-claims');

        $this->assertSame('pending_approval', $project->fresh()->status);
    }

    public function test_new_registrations_use_the_configured_default_email_notification_channel(): void
    {
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('partner'));

        PartnerSetting::current()->update(['default_email_notifications_enabled' => false]);

        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Partner Baru',
                'email' => 'partnerbaru@example.com',
                'password' => 'password123',
                'passwordConfirmation' => 'password123',
                'profile_photo_path' => [UploadedFile::fake()->image('photo.jpg')],
                'ktp_path' => [UploadedFile::fake()->image('ktp.jpg')],
                'bank_name' => 'BCA',
                'bank_account_number' => '123',
                'bank_account_holder' => 'Partner Baru',
                'agreement' => true,
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $partner = Partner::where('email', 'partnerbaru@example.com')->firstOrFail();
        $this->assertFalse($partner->email_notifications_enabled);
    }
}
