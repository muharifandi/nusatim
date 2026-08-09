<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceResourceCtaTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_card_button_falls_back_to_its_own_detail_page_and_default_label(): void
    {
        $service = Service::create([
            'title' => 'Digital Marketing & SEO',
            'slug' => 'digital-marketing-seo',
            'short_description' => 'Desc',
            'is_active' => true,
            'order' => 0,
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertSee(route('services.show', $service->slug), false);
        $response->assertSee('Selengkapnya', false);
    }

    public function test_service_card_button_uses_its_own_custom_text_and_url_when_set(): void
    {
        Service::create([
            'title' => 'Digital Marketing & SEO',
            'slug' => 'digital-marketing-seo',
            'short_description' => 'Desc',
            'is_active' => true,
            'order' => 0,
            'cta_text' => 'Hubungi via WhatsApp',
            'cta_url' => 'https://wa.me/6281234567890',
        ]);

        Service::create([
            'title' => 'UI/UX Design',
            'slug' => 'ui-ux-design',
            'short_description' => 'Desc',
            'is_active' => true,
            'order' => 1,
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertSee('https://wa.me/6281234567890', false);
        $response->assertSee('Hubungi via WhatsApp', false);
        // The other service, with no override, still uses the default.
        $response->assertSee(route('services.show', 'ui-ux-design'), false);
    }

    public function test_cta_button_is_hidden_when_cta_visible_is_false(): void
    {
        Service::create([
            'title' => 'Digital Marketing & SEO',
            'slug' => 'digital-marketing-seo',
            'short_description' => 'Desc',
            'is_active' => true,
            'order' => 0,
            'cta_text' => 'Hubungi via WhatsApp',
            'cta_visible' => false,
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertDontSee('Hubungi via WhatsApp', false);
    }

    public function test_cta_button_shows_by_default(): void
    {
        Service::create([
            'title' => 'Digital Marketing & SEO',
            'slug' => 'digital-marketing-seo',
            'short_description' => 'Desc',
            'is_active' => true,
            'order' => 0,
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertSee('Selengkapnya', false);
    }
}
