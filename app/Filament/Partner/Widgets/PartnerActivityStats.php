<?php

namespace App\Filament\Partner\Widgets;

use App\Models\LeadReminder;
use App\Models\PartnerProject;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PartnerActivityStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $partner = Auth::guard('partner')->user();

        // "Opportunity" = a lead that has progressed past initial contact
        // but hasn't closed either way yet - not an explicit spec
        // definition, this is the most reasonable reading of the term.
        $opportunityCount = $partner->leads()
            ->whereIn('status', ['opportunity', 'proposal', 'negotiation'])
            ->count();

        $todayReminders = LeadReminder::query()
            ->whereHas('lead', fn ($q) => $q->where('partner_id', $partner->id))
            ->whereDate('remind_at', today())
            ->whereNull('completed_at');

        return [
            Stat::make('Total Lead', $partner->leads()->count())
                ->icon('heroicon-o-funnel')
                ->color('gray'),
            Stat::make('Total Opportunity', $opportunityCount)
                ->icon('heroicon-o-arrow-trending-up')
                ->color('warning'),
            Stat::make('Total Customer', $partner->customers()->count())
                ->icon('heroicon-o-user-group')
                ->color('success'),
            Stat::make('Total Project', PartnerProject::where('partner_id', $partner->id)->count())
                ->icon('heroicon-o-briefcase')
                ->color('info'),
            Stat::make('Project Available', PartnerProject::where('status', 'available')->count())
                ->description('Bisa diklaim siapa saja')
                ->icon('heroicon-o-globe-alt')
                ->color('info'),
            Stat::make('Follow Up Hari Ini', (clone $todayReminders)->where('type', 'follow_up')->count())
                ->icon('heroicon-o-phone')
                ->color('warning'),
            Stat::make('Meeting Hari Ini', (clone $todayReminders)->where('type', 'meeting')->count())
                ->icon('heroicon-o-calendar-days')
                ->color('warning'),
        ];
    }
}
