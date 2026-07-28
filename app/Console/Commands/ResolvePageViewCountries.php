<?php

namespace App\Console\Commands;

use App\Models\PageView;
use App\Services\GeoLookupService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('pageviews:resolve-countries {--limit=50}')]
#[Description('Resolve country for recently logged page views that don\'t have one yet.')]
class ResolvePageViewCountries extends Command
{
    public function handle(GeoLookupService $geo): int
    {
        $limit = (int) $this->option('limit');

        $rows = PageView::unresolved()
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            $this->info('Nothing to resolve.');

            return self::SUCCESS;
        }

        $resolved = 0;

        foreach ($rows as $row) {
            $result = $geo->resolve($row->ip_address);

            // Mark private/local/unresolvable IPs as "checked" (XX) so we
            // don't keep retrying the same unresolvable address forever.
            $row->update([
                'country_code' => $result['code'] ?? 'XX',
                'country_name' => $result['name'] ?? null,
            ]);

            if ($result) {
                $resolved++;
            }
        }

        $this->info("Resolved {$resolved} of {$rows->count()} page view(s).");

        return self::SUCCESS;
    }
}
