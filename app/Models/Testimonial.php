<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'name',
        'position',
        'photo',
        'quote',
        'rating',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    protected function fileFields(): array
    {
        return ['photo'];
    }
}
