<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Resolves an IP address to a country using a free, no-API-key lookup
 * (ip-api.com). Deliberately NOT called from the request path (see
 * TrackPageView) - only from a batch job/command so a slow or unreachable
 * lookup never affects a real visitor's page load. Results are cached
 * per-IP forever, so the same visitor never triggers a second HTTP call.
 */
class GeoLookupService
{
    /**
     * @return array{code: string, name: string}|null
     */
    public function resolve(?string $ip): ?array
    {
        if (! $ip || $this->isPrivateOrLocal($ip)) {
            return null;
        }

        return Cache::rememberForever("geo-ip:{$ip}", function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,countryCode',
                ]);

                if (! $response->ok() || $response->json('status') !== 'success') {
                    return null;
                }

                $code = $response->json('countryCode');
                $name = $response->json('country');

                return $code && $name ? ['code' => $code, 'name' => $name] : null;
            } catch (\Throwable) {
                return null;
            }
        });
    }

    protected function isPrivateOrLocal(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            return true;
        }

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
