<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $navigationGroup = 'Reports & Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        return (bool) auth('web')->user()?->can('report.view');
    }

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    /**
     * Widgets don't automatically receive this page's $dateFrom/$dateTo
     * Livewire properties, so they show a fixed window (12 bulan
     * terakhir/all-time) as a visual summary - the detailed tables below
     * remain the precisely date-filtered source of truth. See the notice
     * in reports.blade.php.
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AdminClosingTrendChart::class,
            \App\Filament\Widgets\AdminCommissionStatusChart::class,
            \App\Filament\Widgets\AdminProjectStatusChart::class,
            \App\Filament\Widgets\AdminPartnerPerformanceChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function service(): ReportService
    {
        return new ReportService($this->dateFrom, $this->dateTo);
    }

    public function partnerReport(): array
    {
        return $this->service()->partnerReport();
    }

    public function leadReport()
    {
        return $this->service()->leadReport();
    }

    public function projectReport()
    {
        return $this->service()->projectReport();
    }

    public function closingReport()
    {
        return $this->service()->closingReport();
    }

    public function commissionReport(): array
    {
        return $this->service()->commissionReport();
    }

    public function withdrawalReport(): array
    {
        return $this->service()->withdrawalReport();
    }

    public function partnerPerformanceReport()
    {
        return $this->service()->partnerPerformanceReport();
    }

    public function totalSalesReport(): float
    {
        return $this->service()->totalSalesReport();
    }

    public function exportUrl(string $report): string
    {
        return route('admin.reports.export', [
            'report' => $report,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);
    }
}
