<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Services\IndexNowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IndexNowTest extends TestCase
{
    use RefreshDatabase;

    public function test_key_verification_file_serves_the_configured_key(): void
    {
        $response = $this->get('/'.config('services.indexnow.key').'.txt');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/plain; charset=UTF-8');
        $this->assertSame(config('services.indexnow.key'), $response->getContent());
    }

    public function test_service_pings_the_indexnow_endpoint_with_the_correct_payload(): void
    {
        Http::fake();

        app(IndexNowService::class)->submit('https://nusatim.com/blog/example-post');

        Http::assertSent(function ($request) {
            $query = [];
            parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

            return str_starts_with($request->url(), 'https://api.indexnow.org/indexnow')
                && $query['url'] === 'https://nusatim.com/blog/example-post'
                && $query['key'] === config('services.indexnow.key')
                && $query['keyLocation'] === url('/'.config('services.indexnow.key').'.txt');
        });
    }

    public function test_service_does_nothing_when_no_key_is_configured(): void
    {
        Http::fake();
        config(['services.indexnow.key' => null]);

        app(IndexNowService::class)->submit('https://nusatim.com/blog/example-post');

        Http::assertNothingSent();
    }

    public function test_service_swallows_http_failures_without_throwing(): void
    {
        Http::fake(fn () => throw new ConnectionException('offline'));

        // Should not throw.
        app(IndexNowService::class)->submit('https://nusatim.com/blog/example-post');

        $this->assertTrue(true);
    }

    public function test_saving_a_live_post_does_not_fire_a_real_request_during_tests(): void
    {
        Http::fake();

        Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'content' => 'Content',
            'is_published' => true,
        ]);

        // Post::booted() explicitly skips while app()->runningUnitTests(),
        // so nothing should have been sent even though the post is live.
        Http::assertNothingSent();
    }
}
