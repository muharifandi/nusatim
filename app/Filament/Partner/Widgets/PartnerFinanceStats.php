<?php

namespace App\Filament\Partner\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PartnerFinanceStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $partner = Auth::guard('partner')->user();

        $totalProjectValue = (float) $partner->customers()->sum('project_value');
        $totalCommission = (float) $partner->commissions()->sum('amount');
        $pendingCommission = (float) $partner->commissions()->where('status', 'pending')->sum('amount');
        $readyWithdrawal = $partner->availableBalance();
        $totalWithdrawn = (float) $partner->withdrawals()->where('status', 'paid')->sum('amount');

        $target = $partner->currentSalesTarget();
        $targetDescription = 'Belum ada target diset untuk bulan ini';

        if ($target) {
            $percentage = $target->target_amount > 0
                ? round(($totalProjectValue / (float) $target->target_amount) * 100, 1)
                : 0;

            $targetDescription = 'Rp'.number_format($totalProjectValue, 0, ',', '.')
                .' dari target Rp'.number_format((float) $target->target_amount, 0, ',', '.')
                ." ({$percentage}%)";
        }

        return [
            Stat::make('Total Nilai Project', 'Rp'.number_format($totalProjectValue, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color('info'),
            Stat::make('Total Komisi', 'Rp'.number_format($totalCommission, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('gray'),
            Stat::make('Komisi Pending', 'Rp'.number_format($pendingCommission, 0, ',', '.'))
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Komisi Ready Withdrawal', 'Rp'.number_format($readyWithdrawal, 0, ',', '.'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Total Withdrawal', 'Rp'.number_format($totalWithdrawn, 0, ',', '.'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray'),
            Stat::make('Target Penjualan Bulan Ini', $target ? "{$percentage}%" : '-')
                ->description($targetDescription)
                ->icon('heroicon-o-flag')
                ->color($target ? 'success' : 'gray'),
        ];
    }
}
