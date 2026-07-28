<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'title',
        'slug',
        'icon',
        'image',
        'short_description',
        'content',
        'features',
        'order',
        'is_active',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Generic fallback so services created before this field existed (or
     * left blank) still show something instead of an empty row of badges.
     */
    public static function defaultFeatures(): array
    {
        return [
            ['color' => 'dodger-blue', 'icon' => 'flaticon-responsive', 'title' => 'Fully Responsive'],
            ['color' => 'sunset-orange', 'icon' => 'flaticon-hand', 'title' => 'Device Testing'],
            ['color' => 'royal-blue', 'icon' => 'flaticon-canvas', 'title' => 'Clean & Modern'],
            ['color' => 'california', 'icon' => 'flaticon-goal', 'title' => 'Great Experience'],
        ];
    }

    protected function fileFields(): array
    {
        return ['image', 'og_image'];
    }
}
