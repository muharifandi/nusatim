<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionStatusHistory extends Model
{
    protected $fillable = [
        'commission_id',
        'from_status',
        'to_status',
        'note',
    ];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }
}
