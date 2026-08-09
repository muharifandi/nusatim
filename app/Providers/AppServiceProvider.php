<?php

namespace App\Providers;

use App\Models\LegalPage;
use App\Models\Menu;
use App\Models\Promotion;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    private bool $globalsResolved = false;

    private ?bool $siteSettingsTableExists = null;

    private ?SiteSetting $siteSettings = null;

    private ?Menu $headerMenu = null;

    private ?Menu $footerMenu = null;

    private ?Promotion $activePromotion = null;

    private ?Collection $footerLegalPages = null;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // We already vendor-published and committed our own
        // personal_access_tokens migration (database/migrations/
        // 2026_08_01_194208_create_personal_access_tokens_table.php) -
        // Sanctum's own loadMigrationsFrom() would otherwise also try to
        // create the same table and collide with it.
        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->applyDynamicMailConfig();

        View::composer('*', function ($view) {
            // Guard against running before migrations exist (e.g. fresh install).
            // Memoized too - Schema::hasTable() hits information_schema, and
            // this composer fires once per view/partial (see note below).
            $this->siteSettingsTableExists ??= Schema::hasTable('site_settings');
            if (! $this->siteSettingsTableExists) {
                return;
            }

            // View::composer('*', ...) runs its callback once per Blade
            // view/partial rendered, not once per request - a single page
            // load renders dozens of nested partials (menu items recurse
            // per node, plus nav/footer/promo-popup/page-banner...), so
            // without memoizing here these 4 queries were firing 170+ times
            // on a single /blog request instead of just once.
            if (! $this->globalsResolved) {
                $this->siteSettings = SiteSetting::current();
                // partials.menu-item checks ->children at every recursion
                // depth (e.g. the "Pages" dropdown's own children: Pricing,
                // Team, FAQ), so eager load one level deeper than footerMenu
                // needs to avoid a lazy-loaded query per leaf item.
                $this->headerMenu = Menu::query()->where('slug', 'header')->with('items.children.children')->first();
                $this->footerMenu = Menu::query()->where('slug', 'footer')->with('items.children')->first();
                $this->activePromotion = Promotion::current();
                // legal_pages is a newer table than site_settings - guard
                // separately so a deploy that hasn't migrated yet doesn't
                // break the footer (and therefore every page) site-wide.
                $this->footerLegalPages = Schema::hasTable('legal_pages')
                    ? LegalPage::active()->get()
                    : collect();
                $this->globalsResolved = true;
            }

            $view->with('siteSettings', $this->siteSettings);
            $view->with('headerMenu', $this->headerMenu);
            $view->with('footerMenu', $this->footerMenu);
            $view->with('activePromotion', $this->activePromotion);
            $view->with('footerLegalPages', $this->footerLegalPages);
        });
    }

    /**
     * Lets an admin manage SMTP credentials from the Site Settings panel
     * instead of needing file/SSH access to .env - runs unconditionally
     * (not deferred like the View::composer globals above) so it also
     * applies in console/queue contexts, where mail is often actually sent
     * from (queued notifications, scheduled reminders).
     */
    private function applyDynamicMailConfig(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        SiteSetting::current()->applyMailConfig();
    }
}
