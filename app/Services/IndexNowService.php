<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Pings the shared IndexNow endpoint (relayed to Bing, Yandex, Naver, Seznam
 * - Google does not participate in IndexNow) whenever content is published
 * or updated, so those engines can re-crawl near-instantly instead of
 * waiting for their next scheduled crawl. See routes/web.php for the
 * matching {key}.txt verification route IndexNow requires.
 */
class IndexNowService
{
    public function submit(string $url): void
    {
        $key = config('services.indexnow.key');

        if (blank($key)) {
            return;
        }

        try {
            Http::timeout(3)->get('https://api.indexnow.org/indexnow', [
                'url' => $url,
                'key' => $key,
                'keyLocation' => url("/{$key}.txt"),
            ]);
        } catch (Throwable $e) {
            // Never let a slow/unreachable IndexNow endpoint block or fail
            // the actual content save - this is a best-effort ping.
            Log::warning('IndexNow submission failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
