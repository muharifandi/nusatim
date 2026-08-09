<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsurePartnerApproved;
use App\Models\SiteSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PartnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('partner')
            ->path('partner')
            ->authGuard('partner')
            // Without this, Filament's password-reset flow falls back to the
            // default 'users' broker (config('auth.defaults.passwords')) even
            // though this panel authenticates against the 'partner' guard -
            // it would silently look up the reset token/email against the
            // `users` table instead of `partners`, so a partner's "forgot
            // password" would never find their account.
            ->authPasswordBroker('partners')
            ->login()
            ->registration(\App\Filament\Partner\Pages\Auth\Register::class)
            ->passwordReset(\App\Filament\Partner\Pages\Auth\RequestPasswordReset::class)
            ->profile(\App\Filament\Partner\Pages\Auth\EditProfile::class)
            ->databaseNotifications()
            ->favicon(fn () => static::siteAsset('favicon'))
            ->brandLogo(fn () => static::siteAsset('logo_dark'))
            ->darkModeBrandLogo(fn () => static::siteAsset('logo_light'))
            ->brandLogoHeight('2.25rem')
            ->colors([
                // Warm burnt-orange - deliberately distinct hue from the
                // admin panel's teal (still functions as an at-a-glance
                // "which panel am I in" cue) and reads as sales/growth
                // energy rather than a generic Tailwind swatch name.
                'primary' => Color::hex('#a6541a'),
            ])
            ->font('Instrument Sans')
            // Fase 2's real Dashboard (app/Filament/Partner/Pages/Dashboard.php)
            // is picked up by discoverPages() below, same as the admin
            // panel's own custom Dashboard - no explicit ->pages() needed
            // anymore now that a real one exists to discover.
            ->discoverResources(in: app_path('Filament/Partner/Resources'), for: 'App\\Filament\\Partner\\Resources')
            ->discoverPages(in: app_path('Filament/Partner/Pages'), for: 'App\\Filament\\Partner\\Pages')
            ->discoverWidgets(in: app_path('Filament/Partner/Widgets'), for: 'App\\Filament\\Partner\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsurePartnerApproved::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.hooks.pipeline-loader'),
                scopes: [\App\Filament\Partner\Pages\Pipeline::class],
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.hooks.panel-polish'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => view('filament.hooks.impersonation-banner'),
            );
    }

    /**
     * Resolves a SiteSetting file field (favicon, logo_dark, logo_light) to
     * a public URL, or null to fall back to Filament's own default - guarded
     * against running before the site_settings table/migration exists.
     */
    private static function siteAsset(string $field): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        $path = SiteSetting::current()->{$field};

        return $path ? asset($path) : null;
    }
}
