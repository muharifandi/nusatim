<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Lead;
use App\Models\Partner;

/**
 * Same scoping rule as every partner-facing Lead query in the Filament
 * panel (LeadResource::getEloquentQuery(), Pipeline::moveLead()) - a
 * partner can only ever reach their own leads, an out-of-scope ID 404s
 * via findOrFail() rather than leaking existence with a 403.
 */
trait ScopesToOwnLead
{
    protected function resolveLead(Partner $partner, int $leadId): Lead
    {
        return Lead::where('partner_id', $partner->id)->findOrFail($leadId);
    }
}
