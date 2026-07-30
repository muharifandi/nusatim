<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PartnerClosingChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Closing';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $partnerId = Auth::guard('partner')->id();
        $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->startOfMonth());

        $counts = Customer::query()
            ->where('partner_id', $partnerId)
            ->where('created_at', '>=', $months->first())
            ->get()
            ->countBy(fn (Customer $c) => $c->created_at->format('Y-m'));

        return [
            'datasets' => [
                [
                    'label' => 'Closing',
                    'data' => $months->map(fn (Carbon $m) => $counts->get($m->format('Y-m'), 0))->all(),
                    'borderColor' => '#34d399',
                    'backgroundColor' => 'rgba(52, 211, 153, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->all(),
        ];
    }
}
