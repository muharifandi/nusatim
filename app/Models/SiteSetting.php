<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use DeletesOldFiles;

    protected $fillable = [
        'company_name',
        'legal_name',
        'tagline',
        'email',
        'phone',
        'address',
        'logo_light',
        'logo_dark',
        'logo_mobile',
        'logo_footer',
        'favicon',
        'preloader_logo',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'google_maps_embed_url',
        'default_meta_title',
        'default_meta_description',
        'default_meta_keywords',
        'default_og_image',
        'enable_image_skeleton',
        'coming_soon_enabled',
        'google_analytics_id',
        'nav_cta_text',
        'services_explore_heading',
        'services_explore_image',
    ];

    protected $casts = [
        'enable_image_skeleton' => 'boolean',
        'coming_soon_enabled' => 'boolean',
    ];

    /**
     * Site settings is a singleton - always return (and lazily create) row #1.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    protected function fileFields(): array
    {
        return ['logo_light', 'logo_dark', 'logo_mobile', 'logo_footer', 'favicon', 'default_og_image', 'preloader_logo', 'services_explore_image'];
    }
}
