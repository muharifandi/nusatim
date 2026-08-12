<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PostController::index() only ever reads the first is_featured=true row
 * (ordered newest-first) - letting more than one post hold that flag
 * simultaneously was harmless data-wise but confusing: whoever featured
 * the second post would see nothing change on the blog page.
 */
class PostFeaturedTest extends TestCase
{
    use RefreshDatabase;

    public function test_featuring_a_post_unfeatures_the_previously_featured_one(): void
    {
        $postA = Post::create(['title' => 'Post A', 'slug' => 'post-a', 'is_featured' => true]);
        $postB = Post::create(['title' => 'Post B', 'slug' => 'post-b', 'is_featured' => false]);

        $postB->update(['is_featured' => true]);

        $this->assertFalse($postA->fresh()->is_featured);
        $this->assertTrue($postB->fresh()->is_featured);
    }

    public function test_only_one_post_can_be_featured_at_creation_time(): void
    {
        $postA = Post::create(['title' => 'Post A', 'slug' => 'post-a', 'is_featured' => true]);
        $postB = Post::create(['title' => 'Post B', 'slug' => 'post-b', 'is_featured' => true]);

        $this->assertFalse($postA->fresh()->is_featured);
        $this->assertTrue($postB->fresh()->is_featured);
        $this->assertSame(1, Post::where('is_featured', true)->count());
    }

    public function test_unfeaturing_a_post_does_not_affect_others(): void
    {
        $postA = Post::create(['title' => 'Post A', 'slug' => 'post-a', 'is_featured' => true]);

        $postA->update(['is_featured' => false]);

        $this->assertSame(0, Post::where('is_featured', true)->count());
    }
}
