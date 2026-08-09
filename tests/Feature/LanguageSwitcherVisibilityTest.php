<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitcherVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_switcher_hidden_by_default(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('langSwitcher', false);
    }

    public function test_language_switcher_shown_when_enabled_in_site_settings(): void
    {
        SiteSetting::current()->update(['show_language_switcher' => true]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('langSwitcher', false);
    }
}
