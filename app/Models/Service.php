<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use App\Services\IndexNowService;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use DeletesOldFiles;

    protected static function booted(): void
    {
        // Same "ping on every live save" pattern as Post::booted() - a
        // content edit is as much a re-crawl signal as the first publish.
        static::saved(function (Service $service) {
            if ($service->is_active && ! app()->runningUnitTests()) {
                app(IndexNowService::class)->submit(route('services.show', $service->slug));
            }
        });
    }

    protected $fillable = [
        'title',
        'slug',
        'icon',
        'image',
        'short_description',
        'cta_url',
        'cta_text',
        'cta_visible',
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
        'cta_visible' => 'boolean',
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
            ['color' => 'dodger-blue', 'icon' => 'flaticon-responsive', 'title' => 'Desain Responsif'],
            ['color' => 'sunset-orange', 'icon' => 'flaticon-hand', 'title' => 'Teruji di Berbagai Perangkat'],
            ['color' => 'royal-blue', 'icon' => 'flaticon-canvas', 'title' => 'Tampilan Modern'],
            ['color' => 'california', 'icon' => 'flaticon-goal', 'title' => 'Pengalaman Terbaik'],
        ];
    }

    protected function fileFields(): array
    {
        return ['image', 'og_image'];
    }
}
