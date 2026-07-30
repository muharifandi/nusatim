<?php

namespace App\Console\Commands;

use App\Models\PartnerProject;
use App\Models\PartnerSetting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('projects:expire-stale-claims')]
#[Description('Reject any pending_approval project claim older than the configured Project Claim Rule processing window.')]
class ExpireStaleClaims extends Command
{
    public function handle(): int
    {
        $hours = PartnerSetting::current()->claim_processing_hours;

        if (! $hours) {
            $this->info('No claim_processing_hours configured - nothing to expire.');

            return self::SUCCESS;
        }

        $stale = PartnerProject::query()
            ->where('status', 'pending_approval')
            ->where('claimed_at', '<=', now()->subHours($hours))
            ->get();

        foreach ($stale as $project) {
            $project->rejectClaim();
        }

        $this->info("Expired {$stale->count()} stale claim(s).");

        return self::SUCCESS;
    }
}
