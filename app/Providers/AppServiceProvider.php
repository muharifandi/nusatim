<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Promotion;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private bool $globalsResolved = false;

    private ?bool $siteSettingsTableExists = null;

    private ?SiteSetting $siteSettings = null;

    private ?Menu $headerMenu = null;

    private ?Menu $footerMenu = null;

    private ?Promotion $activePromotion = null;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
                $this->globalsResolved = true;
            }

            $view->with('siteSettings', $this->siteSettings);
            $view->with('headerMenu', $this->headerMenu);
            $view->with('footerMenu', $this->footerMenu);
            $view->with('activePromotion', $this->activePromotion);
        });
    }
}
