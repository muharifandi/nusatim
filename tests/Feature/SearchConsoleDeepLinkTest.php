<?php

namespace Tests\Feature;

use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SearchConsoleDeepLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_search_console_action_visible_only_for_live_posts(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        $draft = Post::create(['title' => 'Draft', 'slug' => 'draft', 'content' => 'x', 'is_published' => false]);
        $live = Post::create(['title' => 'Live', 'slug' => 'live', 'content' => 'x', 'is_published' => true]);

        Livewire::test(EditPost::class, ['record' => $draft->getRouteKey()])
            ->assertActionHidden('checkSearchConsole');

        Livewire::test(EditPost::class, ['record' => $live->getRouteKey()])
            ->assertActionVisible('checkSearchConsole');
    }

    public function test_check_search_console_action_warns_when_resource_id_not_configured(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        SiteSetting::current()->update(['search_console_resource_id' => null]);

        $post = Post::create(['title' => 'Live', 'slug' => 'live', 'content' => 'x', 'is_published' => true]);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction('checkSearchConsole')
            ->assertNotified('Google Search Console Property belum diatur');
    }

    public function test_check_search_console_action_does_not_warn_when_configured(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin, 'web');

        SiteSetting::current()->update(['search_console_resource_id' => 'sc-domain:nusatim.com']);

        $post = Post::create(['title' => 'Live', 'slug' => 'live', 'content' => 'x', 'is_published' => true]);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction('checkSearchConsole')
            ->assertNotNotified();
    }
}
