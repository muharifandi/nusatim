<?php

namespace App\Filament\Partner\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 1;

    /**
     * 2 columns lets PartnerClosingChart and PartnerCommissionChart sit
     * side by side; the stats widgets and pipeline chart span 'full'
     * (both columns) regardless.
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }
}
