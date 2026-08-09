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
        'mail_use_custom_smtp',
        'mail_host',
        'mail_port',
        'mail_encryption',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
        'search_console_resource_id',
        'show_language_switcher',
    ];

    protected $casts = [
        'enable_image_skeleton' => 'boolean',
        'coming_soon_enabled' => 'boolean',
        'show_language_switcher' => 'boolean',
        'mail_use_custom_smtp' => 'boolean',
        // Laravel's built-in encrypt/decrypt-on-access cast (keyed off APP_KEY) -
        // this SMTP password sits in the database rather than a gitignored .env,
        // so it must never be stored in plain text.
        'mail_password' => 'encrypted',
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

    /**
     * Overrides the mail.* config at runtime with these DB-stored settings,
     * when enabled - lets an admin change SMTP credentials from the panel
     * instead of needing file/SSH access to .env. No-op (leaves .env's mail
     * config untouched) when the custom-SMTP toggle is off or no host is set.
     */
    public function applyMailConfig(): void
    {
        if (! $this->mail_use_custom_smtp || blank($this->mail_host)) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.scheme' => $this->mail_encryption ?: null,
            'mail.mailers.smtp.host' => $this->mail_host,
            'mail.mailers.smtp.port' => $this->mail_port,
            'mail.mailers.smtp.username' => $this->mail_username,
            'mail.mailers.smtp.password' => $this->mail_password,
            'mail.from.address' => $this->mail_from_address ?: $this->mail_username,
            'mail.from.name' => $this->mail_from_name ?: $this->company_name,
        ]);
    }
}
