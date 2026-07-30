<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PartnerPipelineChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pipeline';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $statuses = [
            'new' => 'New',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'opportunity' => 'Opportunity',
            'proposal' => 'Proposal',
            'negotiation' => 'Negotiation',
            'won' => 'Won',
            'lost' => 'Lost',
        ];

        $counts = Lead::query()
            ->where('partner_id', Auth::guard('partner')->id())
            ->get()
            ->countBy('status');

        return [
            'datasets' => [
                [
                    'data' => collect($statuses)->keys()->map(fn ($status) => $counts->get($status, 0))->all(),
                    'backgroundColor' => ['#94a3b8', '#60a5fa', '#818cf8', '#a78bfa', '#f472b6', '#fb923c', '#34d399', '#f87171'],
                ],
            ],
            'labels' => array_values($statuses),
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
