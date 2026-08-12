<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * slug columns on these 6 tables already have a DB-level unique
 * constraint (see their create_*_table migrations) - without a matching
 * ->unique(ignoreRecord: true) on the form field, submitting a duplicate
 * slug used to bubble up as a raw QueryException (500) instead of a
 * clean "sudah dipakai" validation message next to the field.
 */
class SlugUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_slug_must_be_unique(): void
    {
        $admin = User::factory()->create();
        Post::create(['title' => 'Pertama', 'slug' => 'artikel-sama', 'status' => 'draft']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PostResource\Pages\CreatePost::class)
            ->fillForm(['title' => 'Kedua', 'slug' => 'artikel-sama'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertSame(1, Post::where('slug', 'artikel-sama')->count());
    }

    public function test_service_slug_must_be_unique(): void
    {
        $admin = User::factory()->create();
        Service::create(['title' => 'Pertama', 'slug' => 'layanan-sama']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ServiceResource\Pages\CreateService::class)
            ->fillForm(['title' => 'Kedua', 'slug' => 'layanan-sama'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertSame(1, Service::where('slug', 'layanan-sama')->count());
    }

    public function test_project_slug_must_be_unique(): void
    {
        $admin = User::factory()->create();
        Project::create(['title' => 'Pertama', 'slug' => 'proyek-sama', 'image' => 'media/uploads/placeholder.jpg']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\ProjectResource\Pages\CreateProject::class)
            ->fillForm(['title' => 'Kedua', 'slug' => 'proyek-sama'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertSame(1, Project::where('slug', 'proyek-sama')->count());
    }

    public function test_page_slug_must_be_unique(): void
    {
        $admin = User::factory()->create();
        Page::create(['name' => 'Pertama', 'slug' => 'halaman-sama']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\PageResource\Pages\CreatePage::class)
            ->fillForm(['name' => 'Kedua', 'slug' => 'halaman-sama'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertSame(1, Page::where('slug', 'halaman-sama')->count());
    }

    public function test_menu_slug_must_be_unique(): void
    {
        $admin = User::factory()->create();
        Menu::create(['name' => 'Pertama', 'slug' => 'menu-sama']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\MenuResource\Pages\CreateMenu::class)
            ->fillForm(['name' => 'Kedua', 'slug' => 'menu-sama'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertSame(1, Menu::where('slug', 'menu-sama')->count());
    }

    public function test_legal_page_slug_must_be_unique(): void
    {
        $admin = User::factory()->create();
        LegalPage::create(['title' => 'Pertama', 'slug' => 'legal-sama', 'type' => 'policy']);

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\LegalPageResource\Pages\CreateLegalPage::class)
            ->fillForm(['title' => 'Kedua', 'slug' => 'legal-sama', 'type' => 'policy'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertSame(1, LegalPage::where('slug', 'legal-sama')->count());
    }
}
