<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerSalesTarget extends Model
{
    protected $fillable = [
        'partner_id',
        'period',
        'target_amount',
    ];

    protected $casts = [
        'period' => 'date',
        'target_amount' => 'decimal:2',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
