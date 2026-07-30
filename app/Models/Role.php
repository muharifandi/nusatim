<?php

namespace App\Models;

use App\Models\Concerns\LogsAudit;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Thin subclass of Spatie's Role model, purely so Role changes get picked
 * up by Fase 27's Audit Log (LogsAudit hooks into created/updated/deleted
 * model events, which only fire for the concrete class actually used -
 * config('permission.models.role') is pointed at this class instead of
 * Spatie's directly).
 */
class Role extends SpatieRole
{
    use LogsAudit;
}
