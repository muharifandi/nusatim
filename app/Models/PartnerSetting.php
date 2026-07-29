<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerSetting extends Model
{
    protected $fillable = [
        'partnership_agreement',
        'minimum_withdrawal',
    ];

    protected $casts = [
        'minimum_withdrawal' => 'decimal:2',
    ];

    /**
     * Partner settings is a singleton - always return (and lazily create) row #1.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
