<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Fase 27 - generic, cross-model log of WHO changed WHAT and WHEN.
 * Different from the existing Status History tables (commission_status_
 * histories/withdrawal_status_histories, Fase 9/10), which only track
 * status transitions on those two specific models. Immutable (no
 * updated_at) - rows are never edited after being written.
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'user_type',
        'user_id',
        'action',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
