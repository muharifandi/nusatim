<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Commission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PartnerCommissionChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Komisi';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $partnerId = Auth::guard('partner')->id();
        // startOfMonth() BEFORE subMonths(), not after - Carbon's month
        // arithmetic overflows when the current day doesn't exist in the
        // target month (e.g. subtracting from the 31st lands on the 1st-3rd
        // of the month after a 30-day month), which silently duplicates/
        // skips months in the rolling window if done in the other order.
        $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->startOfMonth()->subMonths($i));

        $sums = Commission::query()
            ->where('partner_id', $partnerId)
            ->where('created_at', '>=', $months->first())
            ->get()
            ->groupBy(fn (Commission $c) => $c->created_at->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount'));

        return [
            'datasets' => [
                [
                    'label' => 'Komisi',
                    'data' => $months->map(fn (Carbon $m) => $sums->get($m->format('Y-m'), 0))->all(),
                    'borderColor' => '#818cf8',
                    'backgroundColor' => 'rgba(129, 140, 248, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->all(),
        ];
    }
}
