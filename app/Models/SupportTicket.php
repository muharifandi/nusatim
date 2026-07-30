<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'partner_id',
        'subject',
        'description',
        'status',
        'assigned_to',
        'resolution_note',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assign(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);
    }

    public function resolve(string $note): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution_note' => $note,
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function reopen(): void
    {
        $this->update(['status' => 'open']);
    }
}
