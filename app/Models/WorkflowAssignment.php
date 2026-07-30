<?php

namespace App\Models;

use App\Models\Concerns\LogsAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fase 25 - completes RBAC (Fase 24) by deciding WHO (which Role) is the
 * approver for each workflow, not just WHAT a permission allows. One row
 * per workflow, seeded once by seed_workflow_assignments (all starting
 * with role_id null = "anyone holding the module's approve permission").
 */
class WorkflowAssignment extends Model
{
    use LogsAudit;

    public const PARTNER_REGISTRATION = 'partner_registration';

    public const PROJECT_CLAIM = 'project_claim';

    public const PROJECT_APPROVAL = 'project_approval';

    public const COMMISSION_APPROVAL = 'commission_approval';

    public const WITHDRAWAL_APPROVAL = 'withdrawal_approval';

    public const SUPPORT_TICKET = 'support_ticket';

    public const WORKFLOWS = [
        self::PARTNER_REGISTRATION => 'Registrasi Partner',
        self::PROJECT_CLAIM => 'Project Claim',
        self::PROJECT_APPROVAL => 'Project Approval',
        self::COMMISSION_APPROVAL => 'Commission Approval',
        self::WITHDRAWAL_APPROVAL => 'Withdrawal Approval',
        self::SUPPORT_TICKET => 'Support Ticket',
    ];

    protected $fillable = [
        'workflow',
        'role_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * True if $user is allowed to act on this workflow's approve/reject
     * action. Callers are expected to already have checked the module's
     * plain `approve`/`reject` permission separately - this only adds the
     * EXTRA "must also hold the assigned Role" restriction when one has
     * been set. No row / no role_id set = unrestricted (permission alone
     * is enough).
     */
    public static function userIsAuthorizedFor(string $workflow, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $assignment = static::where('workflow', $workflow)->first();

        if (! $assignment || ! $assignment->role_id) {
            return true;
        }

        return $user->hasRole($assignment->role->name);
    }
}
