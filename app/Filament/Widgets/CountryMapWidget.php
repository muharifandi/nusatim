<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\Widget;

class CountryMapWidget extends Widget
{
    protected static string $view = 'filament.widgets.country-map-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    /**
     * Distinct, professional per-country palette (not a single-hue intensity
     * scale) - Indonesia keeps the site's brand purple since it's the
     * primary market, the rest cycle through complementary hues.
     */
    private const PALETTE = [
        '#5a49f8', '#06b6d4', '#f59e0b', '#10b981', '#ef4444',
        '#8b5cf6', '#ec4899', '#3b82f6', '#f97316', '#14b8a6',
    ];

    protected function getViewData(): array
    {
        $rows = PageView::query()
            ->whereNotNull('country_code')
            ->where('country_code', '!=', 'XX')
            ->selectRaw('country_code, country_name, COUNT(*) as total')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('total')
            ->get();

        $countryColors = [];
        foreach ($rows as $i => $row) {
            $countryColors[$row->country_code] = self::PALETTE[$i % count(self::PALETTE)];
        }

        return [
            'mapValues' => $rows->pluck('total', 'country_code'),
            'countryColors' => $countryColors,
            'topCountries' => $rows->take(8),
            'totalCountries' => $rows->count(),
            'totalVisits' => $rows->sum('total'),
        ];
    }

    public static function flagEmoji(string $countryCode): string
    {
        $code = strtoupper($countryCode);

        if (strlen($code) !== 2) {
            return '🏳️';
        }

        return mb_chr(127397 + ord($code[0])).mb_chr(127397 + ord($code[1]));
    }
}
