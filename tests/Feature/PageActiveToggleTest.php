<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageActiveToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_page_renders_normally(): void
    {
        Page::create(['slug' => 'about', 'name' => 'About', 'is_active' => true]);

        $this->get('/about')->assertOk();
    }

    public function test_inactive_page_returns_404(): void
    {
        Page::create(['slug' => 'about', 'name' => 'About', 'is_active' => false]);

        $this->get('/about')->assertNotFound();
    }

    public function test_missing_page_still_renders_without_404(): void
    {
        // No Page row at all for 'about' - controllers already handle this
        // gracefully via ?-> throughout, must stay that way (distinct from
        // "explicitly deactivated").
        $this->get('/about')->assertOk();
    }

    public function test_bySlug_returns_null_for_missing_page(): void
    {
        $this->assertNull(Page::bySlug('does-not-exist'));
    }

    public function test_bySlug_returns_the_page_when_active(): void
    {
        $page = Page::create(['slug' => 'faq', 'name' => 'FAQ', 'is_active' => true]);

        $this->assertTrue(Page::bySlug('faq')->is($page));
    }
}
