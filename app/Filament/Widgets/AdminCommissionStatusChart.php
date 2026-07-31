<?php

namespace App\Filament\Widgets;

use App\Models\Commission;
use Filament\Widgets\ChartWidget;

/**
 * Reports page (Fase 22) ringkasan visual - all-time, TIDAK ikut filter
 * tanggal (lihat catatan di AdminClosingTrendChart).
 */
class AdminCommissionStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Komisi per Status (All-time)';

    // Matches the existing TrafficChart/BlogCategoryChart widgets - without
    // an explicit max height the canvas has no height constraint to size
    // against and renders squashed/oversized depending on its column width.
    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        // Warna disamakan dengan badge status yang sudah dipakai di
        // CommissionResource, supaya konsisten lintas halaman.
        $labels = [
            'pending' => 'Pending',
            'waiting_client_payment' => 'Waiting Client Payment',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
        ];

        $colors = [
            'pending' => '#94a3b8',
            'waiting_client_payment' => '#fbbf24',
            'approved' => '#60a5fa',
            'paid' => '#34d399',
            'rejected' => '#f87171',
        ];

        $sums = Commission::query()->get()->groupBy('status')->map(fn ($rows) => (float) $rows->sum('amount'));

        // A doughnut with every slice at 0 doesn't render as "nothing" -
        // Chart.js still tries to divide a full circle among zero-sized
        // slices, producing a blank/broken-looking shape. An explicit
        // single gray "Belum ada data" slice is an honest, intentional
        // empty state instead.
        if ((float) $sums->sum() === 0.0) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e4e4e7'],
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => ['Belum ada data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => collect($labels)->keys()->map(fn (string $status) => $sums->get($status, 0))->all(),
                    'backgroundColor' => array_values($colors),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => array_values($labels),
        ];
    }

    protected function getOptions(): array
    {
        return [
            // Filament's chart.js wrapper force-adds x/y scale config for
            // every chart type - a doughnut has no axes, so without
            // explicitly turning them off Chart.js renders stray cartesian
            // tick marks behind it (same fix already applied to
            // BlogCategoryChart/PartnerPipelineChart).
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 10,
                        'padding' => 10,
                        'font' => ['size' => 11],
                    ],
                ],
            ],
        ];
    }
}
