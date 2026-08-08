<?php

namespace App\Console\Commands;

use App\Models\PageView;
use App\Services\GeoLookupService;
use Illuminate\Console\Command;

class ResolvePageViewCountries extends Command
{
    protected $signature = 'pageviews:resolve-countries {--limit=50}';

    protected $description = 'Resolve country for recently logged page views that don\'t have one yet.';

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
