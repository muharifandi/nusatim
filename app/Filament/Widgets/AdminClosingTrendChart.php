<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Reports page (Fase 22) ringkasan visual - jendela waktu tetap (12 bulan
 * terakhir, semua partner), TIDAK ikut filter tanggal di atasnya (tabel
 * detail di bawah tetap yang presisi sesuai filter). Widget Filament tidak
 * otomatis menerima property Livewire dari Page induknya, jadi ini
 * sengaja berdiri sendiri - pola sama seperti chart dashboard partner.
 */
class AdminClosingTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Closing (12 Bulan Terakhir)';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // startOfMonth() BEFORE subMonths(), not after - Carbon's month
        // arithmetic overflows when the current day doesn't exist in the
        // target month (e.g. subtracting from the 31st lands on the 1st-3rd
        // of the month after a 30-day month), which silently duplicates/
        // skips months in the rolling window if done in the other order.
        $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->startOfMonth()->subMonths($i));

        $counts = Customer::query()
            ->where('created_at', '>=', $months->first())
            ->get()
            ->countBy(fn (Customer $c) => $c->created_at->format('Y-m'));

        return [
            'datasets' => [
                [
                    'label' => 'Customer Closing',
                    'data' => $months->map(fn (Carbon $m) => $counts->get($m->format('Y-m'), 0))->all(),
                    'borderColor' => '#0f6674',
                    'backgroundColor' => 'rgba(15, 102, 116, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->all(),
        ];
    }
}
