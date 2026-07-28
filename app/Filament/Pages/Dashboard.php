<?php

namespace App\Filament\Pages;

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
}
