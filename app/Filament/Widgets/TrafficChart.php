<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrafficChart extends ChartWidget
{
    protected static ?string $heading = 'Traffic Pengunjung';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 4;

    protected static ?string $maxHeight = '260px';

    public ?string $filter = 'daily';

    protected function getFilters(): ?array
    {
        return [
            'hourly' => '24 Jam Terakhir',
            'weekly' => '7 Hari Terakhir',
            'biweekly' => '14 Hari Terakhir',
            'daily' => 'Harian (30 hari terakhir)',
            'monthly' => 'Bulanan (12 bulan terakhir)',
            'yearly' => 'Tahunan (5 tahun terakhir)',
        ];
    }

    protected function getData(): array
    {
        [$labels, $counts] = match ($this->filter) {
            'hourly' => $this->hourly(),
            'weekly' => $this->daysRange(6),
            'biweekly' => $this->daysRange(13),
            'monthly' => $this->monthly(),
            'yearly' => $this->yearly(),
            default => $this->daysRange(29),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Kunjungan Halaman',
                    'data' => $counts,
                    'borderColor' => '#5a49f8',
                    'backgroundColor' => 'rgba(90, 73, 248, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function hourly(): array
    {
        $start = Carbon::now()->subHours(23)->startOfHour();
        $end = Carbon::now();

        $rows = PageView::query()
            ->selectRaw("DATE_FORMAT(viewed_at, '%Y-%m-%d %H:00:00') as period, COUNT(*) as total")
            ->where('viewed_at', '>=', $start)
            ->groupBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $counts = [];
        for ($hour = $start->copy(); $hour <= $end; $hour->addHour()) {
            $key = $hour->format('Y-m-d H:00:00');
            $labels[] = $hour->format('H:00');
            $counts[] = (int) ($rows[$key] ?? 0);
        }

        return [$labels, $counts];
    }

    protected function daysRange(int $daysBack): array
    {
        $start = Carbon::now()->subDays($daysBack)->startOfDay();

        $rows = PageView::query()
            ->selectRaw('DATE(viewed_at) as period, COUNT(*) as total')
            ->where('viewed_at', '>=', $start)
            ->groupBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $counts = [];
        for ($date = $start->copy(); $date <= Carbon::now(); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $counts[] = (int) ($rows[$key] ?? 0);
        }

        return [$labels, $counts];
    }

    protected function monthly(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();

        $rows = PageView::query()
            ->selectRaw("DATE_FORMAT(viewed_at, '%Y-%m') as period, COUNT(*) as total")
            ->where('viewed_at', '>=', $start)
            ->groupBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $counts = [];
        for ($date = $start->copy(); $date <= Carbon::now(); $date->addMonth()) {
            $key = $date->format('Y-m');
            $labels[] = $date->translatedFormat('M Y');
            $counts[] = (int) ($rows[$key] ?? 0);
        }

        return [$labels, $counts];
    }

    protected function yearly(): array
    {
        $start = Carbon::now()->subYears(4)->startOfYear();

        $rows = PageView::query()
            ->selectRaw('YEAR(viewed_at) as period, COUNT(*) as total')
            ->where('viewed_at', '>=', $start)
            ->groupBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $counts = [];
        for ($year = (int) $start->format('Y'); $year <= (int) Carbon::now()->format('Y'); $year++) {
            $labels[] = (string) $year;
            $counts[] = (int) ($rows[$year] ?? 0);
        }

        return [$labels, $counts];
    }
}
