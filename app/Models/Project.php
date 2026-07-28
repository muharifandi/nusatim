<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'image',
        'description',
        'order',
        'is_active',
        'meta_title',
        'meta_description',
        'og_image',
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
        return ['image', 'og_image'];
    }
}
