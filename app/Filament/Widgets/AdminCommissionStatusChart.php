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

        return [
            'datasets' => [
                [
                    'data' => collect($labels)->keys()->map(fn (string $status) => $sums->get($status, 0))->all(),
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => array_values($labels),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
