<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;

class BlogCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Kategori Terpopuler';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $rows = Post::query()
            ->whereNotNull('category')
            ->selectRaw('category, SUM(views_count) as total_views')
            ->groupBy('category')
            ->orderByDesc('total_views')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $rows->pluck('total_views'),
                    'backgroundColor' => [
                        '#5a49f8',
                        '#06b6d4',
                        '#f59e0b',
                        '#10b981',
                        '#ec4899',
                        '#f97316',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $rows->pluck('category'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            // Filament's chart.js wrapper force-adds x/y scale config for
            // every chart type (`options.scales.x ??= {}` etc, regardless of
            // type) - a doughnut has no axes, so without explicitly turning
            // them off Chart.js renders stray cartesian tick marks behind it.
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
