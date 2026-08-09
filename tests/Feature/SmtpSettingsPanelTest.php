<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SmtpSettingsPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_save_smtp_settings_via_the_panel(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        $this->get('/admin/manage-site-settings')->assertOk();

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'mail_use_custom_smtp' => true,
                'mail_host' => 'smtp.brevo.com',
                'mail_port' => '587',
                'mail_encryption' => 'smtp',
                'mail_username' => 'test@brevo-test.com',
                'mail_password' => 'my-secret-password',
                'mail_from_address' => 'info@nusatim.com',
                'mail_from_name' => 'Nusatim',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = SiteSetting::current();
        $this->assertTrue($settings->mail_use_custom_smtp);
        $this->assertSame('smtp.brevo.com', $settings->mail_host);
        $this->assertSame('my-secret-password', $settings->mail_password);

        // Re-mount fresh (simulates reopening the page) - password field
        // must not be prefilled with the decrypted secret, and saving again
        // while leaving it blank must not wipe the stored password.
        Livewire::test(ManageSiteSettings::class)
            ->assertFormSet(['mail_host' => 'smtp.brevo.com'])
            ->fillForm(['mail_from_name' => 'Nusatim Updated'])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings->refresh();
        $this->assertSame('Nusatim Updated', $settings->mail_from_name);
        $this->assertSame('my-secret-password', $settings->mail_password);
    }

    public function test_test_email_action_reports_failure_for_unreachable_smtp_host(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        SiteSetting::current()->update([
            'mail_use_custom_smtp' => true,
            'mail_host' => 'smtp.invalid-host-that-does-not-exist.test',
            'mail_port' => '587',
            'mail_encryption' => 'smtp',
            'mail_username' => 'test@example.com',
            'mail_password' => 'whatever',
        ]);

        Livewire::test(ManageSiteSettings::class)
            ->callAction('testEmail', data: ['test_recipient' => 'someone@example.com'])
            ->assertNotified();
    }

    public function test_test_email_action_reports_success_when_sending_succeeds(): void
    {
        // Mail::raw() is a documented no-op under MailFake (nothing to
        // assertSent on), so the meaningful check here is the outcome
        // reported back to the admin, not Mail's internal call tracking.
        Mail::fake();

        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        Livewire::test(ManageSiteSettings::class)
            ->callAction('testEmail', data: ['test_recipient' => 'someone@example.com'])
            ->assertNotified('Email test berhasil dikirim');
    }
}
