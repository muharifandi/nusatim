<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Post.is_published / Service.is_active / LegalPage.is_active had no
 * relationship to their `content` field - an admin could publish an
 * empty article, service page, or legal document, and the public page
 * would render with nothing in the body.
 */
class RequireContentWhenPublishedTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishing_a_post_without_content_is_rejected(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PostResource\Pages\CreatePost::class)
            ->fillForm(['title' => 'Artikel Kosong', 'slug' => 'artikel-kosong', 'is_published' => true])
            ->call('create')
            ->assertHasFormErrors(['content']);

        $this->assertFalse(Post::where('slug', 'artikel-kosong')->exists());
    }

    public function test_a_draft_post_without_content_is_allowed(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PostResource\Pages\CreatePost::class)
            ->fillForm(['title' => 'Draft Kosong', 'slug' => 'draft-kosong', 'is_published' => false])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(Post::where('slug', 'draft-kosong')->exists());
    }

    public function test_activating_a_service_without_content_is_rejected(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ServiceResource\Pages\CreateService::class)
            ->fillForm(['title' => 'Layanan Kosong', 'slug' => 'layanan-kosong', 'is_active' => true])
            ->call('create')
            ->assertHasFormErrors(['content']);

        $this->assertFalse(Service::where('slug', 'layanan-kosong')->exists());
    }

    public function test_activating_a_legal_page_without_content_is_rejected(): void
    {
        $admin = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\LegalPageResource\Pages\CreateLegalPage::class)
            ->fillForm(['title' => 'Dokumen Kosong', 'slug' => 'dokumen-kosong', 'type' => 'policy', 'is_active' => true])
            ->call('create')
            ->assertHasFormErrors(['content']);

        $this->assertFalse(LegalPage::where('slug', 'dokumen-kosong')->exists());
    }
}
