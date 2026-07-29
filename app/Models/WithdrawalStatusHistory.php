<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalStatusHistory extends Model
{
    protected $fillable = [
        'withdrawal_id',
        'from_status',
        'to_status',
        'note',
    ];

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }
}
