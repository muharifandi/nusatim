<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * 5 columns lets the traffic chart take 4 (80%) and the blog-category
     * donut take 1 (20%) on the same row; other widgets just span 'full'.
     */
    public function getColumns(): int | string | array
    {
        return 5;
    }

    /**
     * The base Dashboard::getWidgets() returns EVERY widget discovered
     * panel-wide (->discoverWidgets() in AdminPanelProvider registers them
     * globally, it doesn't scope them to a specific page) - the 4 Reports
     * chart widgets (AdminClosingTrendChart etc, Fase 28) were meant only
     * for the Reports page and ended up duplicated onto this Dashboard too
     * as an unintended side effect. Excluded here explicitly rather than
     * rewriting this into an allow-list, so any other auto-discovered
     * widget keeps landing on the dashboard exactly as before.
     */
    public function getWidgets(): array
    {
        $reportsOnlyWidgets = [
            \App\Filament\Widgets\AdminClosingTrendChart::class,
            \App\Filament\Widgets\AdminCommissionStatusChart::class,
            \App\Filament\Widgets\AdminProjectStatusChart::class,
            \App\Filament\Widgets\AdminPartnerPerformanceChart::class,
        ];

        return array_values(array_diff(Filament::getWidgets(), $reportsOnlyWidgets));
    }
}
