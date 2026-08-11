<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use App\Services\IndexNowService;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use DeletesOldFiles;

    protected static function booted(): void
    {
        // Same "ping on every live save" pattern as Post::booted() - a
        // content edit is as much a re-crawl signal as the first publish.
        static::saved(function (Project $project) {
            if ($project->is_active && ! app()->runningUnitTests()) {
                app(IndexNowService::class)->submit(route('portfolio.show', $project->slug));
            }
        });
    }

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
