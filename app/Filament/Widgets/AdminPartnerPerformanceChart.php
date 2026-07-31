<?php

namespace App\Filament\Widgets;

use App\Models\Partner;
use Filament\Widgets\ChartWidget;

/**
 * Reports page (Fase 22) ringkasan visual - all-time, TIDAK ikut filter
 * tanggal (lihat catatan di AdminClosingTrendChart).
 */
class AdminPartnerPerformanceChart extends ChartWidget
{
    protected static ?string $heading = 'Top 8 Partner (Komisi Approved + Paid, All-time)';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $top = Partner::query()
            ->get()
            ->map(fn (Partner $partner) => [
                'name' => $partner->name,
                'commission' => (float) $partner->commissions()->whereIn('status', ['approved', 'paid'])->sum('amount'),
            ])
            ->sortByDesc('commission')
            ->take(8)
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Komisi',
                    'data' => $top->pluck('commission')->all(),
                    'backgroundColor' => '#0f6674',
                ],
            ],
            'labels' => $top->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        // indexAxis 'y' membuat bar horizontal - lebih mudah dibaca untuk
        // label nama partner yang panjang dibanding bar vertikal.
        return [
            'indexAxis' => 'y',
        ];
    }
}
