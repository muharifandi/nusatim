<?php

namespace App\Filament\Widgets;

use App\Models\PartnerProject;
use Filament\Widgets\ChartWidget;

/**
 * Reports page (Fase 22) ringkasan visual - all-time, TIDAK ikut filter
 * tanggal (lihat catatan di AdminClosingTrendChart).
 */
class AdminProjectStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Project Board per Status (All-time)';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        // Warna disamakan dengan badge status yang sudah dipakai di
        // PartnerProjectResource, supaya konsisten lintas halaman.
        $labels = [
            'draft' => 'Draft',
            'available' => 'Available',
            'pending_approval' => 'Pending Approval',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];

        $colors = [
            'draft' => '#94a3b8',
            'available' => '#60a5fa',
            'pending_approval' => '#fbbf24',
            'assigned' => '#0f6674',
            'in_progress' => '#5eead4',
            'closed' => '#34d399',
            'cancelled' => '#f87171',
        ];

        $counts = PartnerProject::query()->get()->countBy('status');

        return [
            'datasets' => [
                [
                    'data' => collect($labels)->keys()->map(fn (string $status) => $counts->get($status, 0))->all(),
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
