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

            AuditLog::create([
                'auditable_type' => $model->getMorphClass(),
                'auditable_id' => $model->getKey(),
                ...self::currentActorMorph(),
                'action' => 'updated',
                'changes' => [
                    'before' => collect($changes)->keys()->mapWithKeys(
                        fn ($key) => [$key => $model->getOriginal($key)]
                    )->all(),
                    'after' => $changes,
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
