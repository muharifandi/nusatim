<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Fase 27 - writes an AuditLog row automatically on created/updated/deleted,
 * no manual instrumentation needed per action. Because approve/reject/
 * publish/etc across this codebase are all implemented as plain ->update()
 * calls, the generic 'updated' hook already captures those as an ordinary
 * attribute diff (e.g. status: pending -> approved) - there's nothing
 * approval-specific to log separately.
 */
trait LogsAudit
{
    public static function bootLogsAudit(): void
    {
        static::created(function ($model) {
            AuditLog::create([
                'auditable_type' => $model->getMorphClass(),
                'auditable_id' => $model->getKey(),
                ...self::currentActorMorph(),
                'action' => 'created',
            ]);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            // getChanges()/getOriginal() read raw attributes, bypassing the
            // $hidden filtering that normally keeps a password hash out of
            // array/JSON output - without this, a User's password change
            // would otherwise land the old and new bcrypt hash directly in
            // the audit trail.
            $hidden = $model->getHidden();
            $redact = fn ($key, $value) => in_array($key, $hidden, true) ? '[hidden]' : $value;

            AuditLog::create([
                'auditable_type' => $model->getMorphClass(),
                'auditable_id' => $model->getKey(),
                ...self::currentActorMorph(),
                'action' => 'updated',
                'changes' => [
                    'before' => collect($changes)->keys()->mapWithKeys(
                        fn ($key) => [$key => $redact($key, $model->getOriginal($key))]
                    )->all(),
                    'after' => collect($changes)->mapWithKeys(
                        fn ($value, $key) => [$key => $redact($key, $value)]
                    )->all(),
                ],
            ]);
        });

        static::deleted(function ($model) {
            AuditLog::create([
                'auditable_type' => $model->getMorphClass(),
                'auditable_id' => $model->getKey(),
                ...self::currentActorMorph(),
                'action' => 'deleted',
            ]);
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected static function currentActorMorph(): array
    {
        $actor = Auth::guard('web')->user() ?? Auth::guard('partner')->user();

        if (! $actor) {
            return ['user_type' => null, 'user_id' => null];
        }

        return [
            'user_type' => $actor instanceof User ? User::class : Partner::class,
            'user_id' => $actor->id,
        ];
    }
}
