<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'currency',
        'period',
        'features',
        'cta_text',
        'cta_url',
        'is_highlighted',
        'highlight_color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_highlighted' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
