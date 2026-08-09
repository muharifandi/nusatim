<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class IndexNowController extends Controller
{
    /**
     * Ownership-verification file IndexNow requires at /{key}.txt, body is
     * just the raw key. Route constrains {key} to the exact configured
     * value (see routes/web.php) so this doesn't swallow unrelated .txt
     * requests.
     */
    public function key(): Response
    {
        return response(config('services.indexnow.key'), 200)
            ->header('Content-Type', 'text/plain');
    }
}
