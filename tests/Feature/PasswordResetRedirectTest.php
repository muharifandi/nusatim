<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RequestPasswordReset as AdminRequestPasswordReset;
use App\Filament\Partner\Pages\Auth\RequestPasswordReset as PartnerRequestPasswordReset;
use App\Models\Partner;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_request_password_reset_redirects_to_login_after_sending(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'admin@example.com']);

        Livewire::test(AdminRequestPasswordReset::class)
            ->fillForm(['email' => 'admin@example.com'])
            ->call('request')
            ->assertRedirect('/admin/login');
    }

    public function test_partner_request_password_reset_redirects_to_login_after_sending(): void
    {
        Mail::fake();
        Partner::factory()->create(['email' => 'partner@example.com']);

        // Livewire::test() doesn't go through the panel-scoped HTTP
        // middleware that normally binds "which panel is this" - without
        // this, Filament::getCurrentPanel() falls back to the default
        // panel ('admin'), so the password broker resolves to 'users'
        // instead of 'partners' and the partner is never found.
        Filament::setCurrentPanel(Filament::getPanel('partner'));

        Livewire::test(PartnerRequestPasswordReset::class)
            ->fillForm(['email' => 'partner@example.com'])
            ->call('request')
            ->assertRedirect('/partner/login');
    }

    public function test_admin_request_password_reset_does_not_redirect_on_validation_failure(): void
    {
        Livewire::test(AdminRequestPasswordReset::class)
            ->fillForm(['email' => 'not-an-email'])
            ->call('request')
            ->assertHasFormErrors(['email'])
            ->assertNoRedirect();
    }
}
